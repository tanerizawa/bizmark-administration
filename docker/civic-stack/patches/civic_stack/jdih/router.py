"""FastAPI router for the JDIH module (Jaringan Dokumentasi dan Informasi Hukum)."""

from __future__ import annotations

from fastapi import APIRouter, HTTPException, Query

from civic_stack.shared.schema import CivicStackResponse

from .scraper import fetch, list_recent, search

router = APIRouter(prefix="/jdih", tags=["JDIH"])

VALID_TYPES = {"uu", "pp", "perpres", "permen", "perda", "kepmen"}


@router.get(
    "/regulation/{regulation_id:path}",
    response_model=CivicStackResponse,
    summary="Ambil detail peraturan berdasarkan ID atau nomor peraturan",
)
async def get_regulation(regulation_id: str) -> CivicStackResponse:
    resp = await fetch(regulation_id)
    if not resp.found:
        raise HTTPException(
            status_code=404,
            detail=f"Regulasi '{regulation_id}' tidak ditemukan di JDIH",
        )
    return resp


@router.get(
    "/search",
    response_model=list[CivicStackResponse],
    summary="Cari peraturan hukum berdasarkan kata kunci",
)
async def search_regulations(
    q: str = Query(..., min_length=2, description="Kata kunci pencarian regulasi"),
    type: str = Query("uu", description=f"Jenis regulasi: {', '.join(sorted(VALID_TYPES))}"),
) -> list[CivicStackResponse]:
    if type not in VALID_TYPES:
        raise HTTPException(
            status_code=422,
            detail=f"Tipe tidak valid. Pilih salah satu: {', '.join(sorted(VALID_TYPES))}",
        )
    return await search(q, regulation_type=type)


@router.get(
    "/recent",
    response_model=list[CivicStackResponse],
    summary="Daftar peraturan terbaru",
)
async def get_recent(
    type: str = Query("uu", description=f"Jenis regulasi: {', '.join(sorted(VALID_TYPES))}"),
    limit: int = Query(10, ge=1, le=50, description="Jumlah hasil (maks 50)"),
) -> list[CivicStackResponse]:
    if type not in VALID_TYPES:
        raise HTTPException(
            status_code=422,
            detail=f"Tipe tidak valid. Pilih salah satu: {', '.join(sorted(VALID_TYPES))}",
        )
    return await list_recent(regulation_type=type, limit=limit)
