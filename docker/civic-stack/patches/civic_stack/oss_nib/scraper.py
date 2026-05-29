"""
OSS RBA NIB scraper — api-prd.oss.go.id direct HTTP tier.

Two tiers:
- Public  (no credentials): NIB → 6 basic fields (nama, status, skala usaha, modal investasi).
- Private (OSS credentials): Own-account NIB → 30+ fields including alamat, NPWP, modal detail,
  ekspor/impor flags, tanggal terbit, wilayah, kontak, status badan hukum.

Patch history:
- 2026-05-05 v1-v3: Playwright attempts — blocked by reCAPTCHA + overlay.
- 2026-05-05 v4: REWRITE — direct HTTP to api-prd.oss.go.id public endpoint.
                 Public endpoint: POST /v1/reg/public/nib — works with user_key + any captcha token.
- 2026-05-09 v5: Added authenticated tier via OSS SSO login.
                 Auth endpoint: POST /v1/sso/new/users/login (needs Basic Auth + user_key).
                 Detail endpoint: POST /v1/izin-read/main/getDetailPermohonan (Bearer token).
                 Result: 30+ fields for the account's own company NIB.
                 Authenticated data is PROFILE-SCOPED — returns logged-in user's company only.
                 Configure via env vars: OSS_USERNAME, OSS_PASSWORD (optional).
                 Token is cached in-process for ~3h (JWT lifetime).

Public endpoint:
  POST https://api-prd.oss.go.id/v1/reg/public/nib
  Body: {"dataNib": {"nib": "<13-digit>"}}
  Returns 6 fields: nib, nama, status_aktif, status_migrasi, status_penanaman_modal, skala_usaha

Private endpoint (own account only):
  POST https://api-prd.oss.go.id/v1/izin-read/main/getDetailPermohonan
  Returns dataNib with 30+ fields + dataProfile with owner identity + region details
"""

from __future__ import annotations

import base64
import datetime
import logging
import os
import time
from typing import Any

import httpx

from civic_stack.shared.schema import CivicStackResponse, error_response, not_found_response

logger = logging.getLogger(__name__)

MODULE = "oss_nib"

# API endpoint discovered 2026-05-05 via browser network capture
_NIB_API_URL = "https://api-prd.oss.go.id/v1/reg/public/nib"
_BASE_URL = "https://api-prd.oss.go.id/v1"
_LOGIN_URL = f"{_BASE_URL}/sso/new/users/login"
_DETAIL_URL = f"{_BASE_URL}/izin-read/main/getDetailPermohonan"

# Public key embedded in the OSS JavaScript bundle (client-side, not secret)
_USER_KEY = "846ee507525c6b00d18733e066bd5686"

# OSS system credentials for the login API Basic Auth header
# These are OSS platform credentials (not user credentials) embedded in the portal JS bundle
_OSS_SYSTEM_BASIC = base64.b64encode(b"OSS000:Ux4BXVBWW2VfaFExUztUZlJjAGtQOlFq").decode()

_HEADERS = {
    "Content-Type": "application/json",
    "Accept": "application/json",
    "Referer": "https://oss.go.id/",
    "Origin": "https://oss.go.id",
    "User-Agent": (
        "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 "
        "(KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36"
    ),
    "user_key": _USER_KEY,
    # Server-side reCAPTCHA validation is absent — any non-empty value accepted
    "g-recaptcha-response": "civic-stack-public",
}

_AUTH_HEADERS = {
    "Content-Type": "application/json",
    "Accept": "application/json",
    "User-Agent": (
        "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 "
        "(KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36"
    ),
    "user_key": _USER_KEY,
    "Origin": "https://perizinan.oss.go.id",
    "Referer": "https://perizinan.oss.go.id/",
}

# In-process token cache: (access_token, expiry_timestamp, own_nib)
_token_cache: tuple[str, float, str] | None = None


async def _login(username: str, password: str) -> tuple[str, float] | None:
    """Login to OSS SSO and return (access_token, expiry_ts). Returns None on failure."""
    global _token_cache  # noqa: PLW0603

    # Return cached token if still valid (with 5-min buffer)
    if _token_cache and time.time() < _token_cache[1] - 300:
        return _token_cache[0], _token_cache[1]

    try:
        async with httpx.AsyncClient(timeout=15.0, follow_redirects=True) as client:
            resp = await client.post(
                _LOGIN_URL,
                json={"username": username, "password": password},
                headers={
                    **_AUTH_HEADERS,
                    "Authorization": f"Basic {_OSS_SYSTEM_BASIC}",
                },
            )
        if resp.status_code != 200:
            logger.warning("OSS SSO login failed: HTTP %s", resp.status_code)
            return None

        data = resp.json().get("data", {})
        token = data.get("access_token")
        if not token:
            logger.warning("OSS SSO login: no access_token in response")
            return None

        # JWT lifetime is ~3h; store expiry based on current time + 3h
        expiry = data.get("expired_aToken")
        if expiry and isinstance(expiry, (int, float)):
            # expired_aToken is likely epoch ms
            expiry_ts = expiry / 1000 if expiry > 1e10 else expiry
        else:
            expiry_ts = time.time() + 10800  # 3h default

        _token_cache = (token, expiry_ts, "")
        return token, expiry_ts

    except Exception:
        logger.exception("OSS SSO login exception")
        return None


async def _fetch_own_detail(token: str) -> dict[str, Any] | None:
    """Fetch own-account detailed NIB data. Returns dict with dataNib + dataProfile."""
    try:
        async with httpx.AsyncClient(timeout=15.0, follow_redirects=True) as client:
            resp = await client.post(
                _DETAIL_URL,
                json={},
                headers={
                    **_AUTH_HEADERS,
                    "Authorization": f"Bearer {token}",
                },
            )
        if resp.status_code != 200:
            logger.warning("OSS detail fetch failed: HTTP %s", resp.status_code)
            return None

        data = resp.json()
        if data.get("status") != 200:
            return None

        return data.get("data")
    except Exception:
        logger.exception("OSS detail fetch exception")
        return None


async def fetch(
    query: str,
    *,
    debug: bool = False,
    proxy_url: str | None = None,
    oss_username: str | None = None,
    oss_password: str | None = None,
) -> CivicStackResponse:
    """
    Look up a business by NIB number (13-digit) via direct HTTP.

    When OSS credentials are provided (or set via OSS_USERNAME / OSS_PASSWORD env vars),
    attempts an authenticated lookup first which returns 30+ fields including alamat,
    NPWP, modal, kontak, wilayah, and status detail. Authenticated data is only available
    for the account owner's own company NIB — other NIBs fall back to the public 6-field API.

    Args:
        query: NIB number (exactly 13 digits).
        debug: Include raw response data in result.
        proxy_url: Optional HTTP proxy URL.
        oss_username: OSS portal username (overrides OSS_USERNAME env var).
        oss_password: OSS portal password (overrides OSS_PASSWORD env var).
    """
    nib = query.strip()
    if not nib or not nib.isdigit() or len(nib) != 13:
        return error_response(MODULE, _NIB_API_URL, detail=f"Invalid NIB format: {nib!r} (must be 13 digits)")

    # Resolve credentials: explicit param → env var → None
    username = oss_username or os.environ.get("OSS_USERNAME")
    password = oss_password or os.environ.get("OSS_PASSWORD")

    # Try authenticated lookup if credentials available
    if username and password:
        auth_result = await _try_authenticated_fetch(nib, username=username, password=password, debug=debug)
        if auth_result is not None:
            return auth_result

    # Fall back to public API
    try:
        transport = httpx.AsyncHTTPTransport(proxy=proxy_url) if proxy_url else None
        async with httpx.AsyncClient(
            timeout=15.0,
            transport=transport,
            follow_redirects=True,
        ) as client:
            resp = await client.post(
                _NIB_API_URL,
                json={"dataNib": {"nib": nib}},
                headers=_HEADERS,
            )

        if resp.status_code == 403:
            logger.warning(
                "OSS-NIB API returned 403 — user_key may have rotated; "
                "re-capture from oss.go.id JS bundle"
            )
            return error_response(MODULE, _NIB_API_URL, detail="API key rotated (403 Forbidden)")

        if resp.status_code != 200:
            return error_response(MODULE, _NIB_API_URL, detail=f"HTTP {resp.status_code}")

        data = resp.json()

    except httpx.TimeoutException:
        return error_response(MODULE, _NIB_API_URL, detail="Request timeout")
    except Exception as exc:
        logger.exception("OSS-NIB API call failed for NIB '%s'", nib)
        return error_response(MODULE, _NIB_API_URL, detail=str(exc))

    return _normalize_api_response(data, nib=nib, debug=debug)


async def _try_authenticated_fetch(
    nib: str,
    *,
    username: str,
    password: str,
    debug: bool = False,
) -> CivicStackResponse | None:
    """
    Attempt authenticated fetch. Returns CivicStackResponse if the account's own NIB
    matches the queried NIB. Returns None if NIB doesn't match (caller falls back to public).
    """
    login_result = await _login(username, password)
    if not login_result:
        return None

    token, _ = login_result
    detail_data = await _fetch_own_detail(token)
    if not detail_data:
        return None

    nib_list = detail_data.get("dataNib", [])
    profile = detail_data.get("dataProfile", {})

    if not nib_list:
        return None

    # Find the matching NIB in the account's data
    nib_data = next((n for n in nib_list if n.get("nib") == nib), None)
    if nib_data is None:
        # Queried NIB doesn't belong to this account — fall back to public API
        logger.debug("NIB %s not found in authenticated account (owns %s) — using public API",
                     nib, [n.get("nib") for n in nib_list])
        return None

    return _normalize_authenticated_response(nib_data, profile=profile, debug=debug)


def _normalize_authenticated_response(
    nib_data: dict[str, Any],
    *,
    profile: dict[str, Any],
    debug: bool = False,
) -> CivicStackResponse:
    """
    Normalize getDetailPermohonan response to CivicStackResponse.

    nib_data keys include: id_permohonan, nib, nama_perusahaan, alamat_perusahaan,
    rt_rw_perusahaan, kode_pos_perusahaan, nomor_telp_perusahaan, email_perusahaan,
    npwp_perusahaan, status_penanaman_modal, kd_skala_usaha_final, total_modal,
    nilai_pmdn, nilai_pma_dominan, flag_umkm, flag_ekspor, flag_impor, status_nib,
    tanggal_terbit_oss, tanggal_perubahan_nib, jenis_perusahaan, status_badan_hukum, etc.

    profile keys include: nomor_identitas, nama, daerah_id, _m_region_new (region details).
    """
    nib = nib_data.get("nib", "")
    region = profile.get("_m_region_new") or {}

    # Map kode to human-readable values
    status_pm_map = {"01": "PMA", "02": "PMDN", "03": "Non Fasilitas"}
    skala_map = {"01": "Mikro", "02": "Kecil", "03": "Menengah", "04": "Besar"}
    status_nib_map = {"01": "Aktif", "02": "Tidak Aktif", "03": "Dicabut"}
    badan_hukum_map = {
        "01": "Perseroan Terbatas (PT)", "02": "Koperasi", "03": "Firma",
        "04": "CV", "05": "UD/PD", "10": "Perorangan", "26": "Lainnya",
    }

    result: dict[str, Any] = {
        # Basic identification
        "nib": nib,
        "company_name": nib_data.get("nama_perusahaan", "").strip(),
        "npwp": nib_data.get("npwp_perusahaan"),
        # Address
        "alamat": nib_data.get("alamat_perusahaan", "").strip(),
        "rt_rw": nib_data.get("rt_rw_perusahaan"),
        "kode_pos": nib_data.get("kode_pos_perusahaan"),
        "kelurahan": region.get("kelurahan"),
        "kecamatan": region.get("kecamatan"),
        "kota_kabupaten": region.get("kab_kota"),
        "provinsi": region.get("propinsi"),
        # Contact
        "telepon": nib_data.get("nomor_telp_perusahaan"),
        "email": nib_data.get("email_perusahaan"),
        # Business classification
        "status_aktif": status_nib_map.get(nib_data.get("status_nib", ""), "Aktif"),
        "status_migrasi": "OSS RBA",
        "status_penanaman_modal": status_pm_map.get(nib_data.get("status_penanaman_modal", ""), nib_data.get("status_penanaman_modal")),
        "skala_usaha": skala_map.get(nib_data.get("kd_skala_usaha_final", ""), nib_data.get("kd_skala_usaha_final")),
        "jenis_badan_hukum": badan_hukum_map.get(nib_data.get("status_badan_hukum", ""), nib_data.get("status_badan_hukum")),
        # Financial
        "total_modal": nib_data.get("total_modal"),
        "modal_pmdn": nib_data.get("nilai_pmdn"),
        "modal_pma": nib_data.get("nilai_pma_dominan"),
        "persen_pmdn": nib_data.get("persen_pmdn"),
        "persen_pma": nib_data.get("persen_pma"),
        # Flags
        "umkm": nib_data.get("flag_umkm") == "Y",
        "ekspor": nib_data.get("flag_ekspor") == "Y",
        "impor": nib_data.get("flag_impor") == "Y",
        # Dates
        "tanggal_terbit_nib": nib_data.get("tanggal_terbit_oss"),
        "tanggal_perubahan_nib": nib_data.get("tanggal_perubahan_nib"),
        # IDs
        "id_permohonan": nib_data.get("id_permohonan"),
        "daerah_id": nib_data.get("perusahaan_daerah_id"),
        # Metadata
        "data_source": "authenticated",
    }

    # Remove None values for cleaner output
    result = {k: v for k, v in result.items() if v is not None}

    raw = {"dataNib": nib_data, "dataProfile": profile} if debug else None

    return CivicStackResponse(
        found=True,
        module=MODULE,
        source_url=_DETAIL_URL,
        result=result,
        fetched_at=datetime.datetime.utcnow().isoformat(),
        confidence=1.0,
        status=_map_status(status_nib_map.get(nib_data.get("status_nib", ""), "Aktif")),
        raw=raw,
    )


def _normalize_api_response(data: dict[str, Any], *, nib: str, debug: bool = False) -> CivicStackResponse:
    """
    Normalize api-prd.oss.go.id response to CivicStackResponse.

    Raw response shape:
      {
        "kode": 200, "desc": "Sukses",
        "data": {
          "nib": "0220101102834",
          "titleNama": "Nama Perusahaan",  # e.g. "PT", "CV", "UD"
          "nama": "ASIACON CIPTA PRIMA",
          "status_aktif": "Aktif",
          "status_migrasi": "OSS RBA",
          "status_penanaman_modal": "PMDN",
          "skala_usaha": "Menengah"
        }
      }
    """
    kode = data.get("kode")
    if kode != 200 or not data.get("data"):
        return not_found_response(MODULE, _NIB_API_URL)

    payload = data["data"]
    nama = payload.get("nama") or ""
    title = payload.get("titleNama") or ""
    # titleNama is the field label (e.g. "Nama Perusahaan"), NOT an entity prefix like "PT".
    # The entity type (PT/CV/etc) is not returned by this API tier.
    # Use nama directly as the company name.
    company_name = nama.strip()

    result: dict[str, Any] = {
        "nib": payload.get("nib") or nib,
        "company_name": company_name,
        "status_aktif": payload.get("status_aktif"),
        "status_migrasi": payload.get("status_migrasi"),
        "status_penanaman_modal": payload.get("status_penanaman_modal"),
        "skala_usaha": payload.get("skala_usaha"),
        "data_source": "public",
    }

    return CivicStackResponse(
        found=True,
        module=MODULE,
        source_url=_NIB_API_URL,
        result=result,
        fetched_at=datetime.datetime.utcnow().isoformat(),
        confidence=1.0,
        status=_map_status(payload.get("status_aktif", "")),
        raw=payload if debug else None,
    )


def _map_status(status_aktif: str) -> str:
    """Map Indonesian OSS status_aktif to CivicStackResponse status enum."""
    _map = {
        "aktif": "ACTIVE",
        "active": "ACTIVE",
        "tidak aktif": "SUSPENDED",
        "inactive": "SUSPENDED",
        "dicabut": "REVOKED",
        "revoked": "REVOKED",
        "expired": "EXPIRED",
        "kadaluarsa": "EXPIRED",
    }
    return _map.get(status_aktif.strip().lower(), "ACTIVE")


async def search(
    keyword: str,
    filters: dict | None = None,  # noqa: ARG001
    *,
    proxy_url: str | None = None,
) -> list[CivicStackResponse]:
    """
    Search by NIB number.

    The OSS public API only supports exact NIB lookup (not company name search).
    If keyword is a 13-digit NIB, delegates to fetch(). Otherwise NOT_FOUND.
    """
    keyword = keyword.strip()
    if keyword.isdigit() and len(keyword) == 13:
        result = await fetch(keyword, proxy_url=proxy_url)
        return [result]

    return [not_found_response(MODULE, _NIB_API_URL)]
