"""
Unified FastAPI application — mounts all module routers.

Patched version for BizMark deployment: adds JDIH router.

Original: https://github.com/suryast/indonesia-civic-stack
Patch date: 2026-05-05
Changes: Added civic_stack.jdih.router, "jdih" added to _ALL_MODULES
"""

from __future__ import annotations

from fastapi import FastAPI
from fastapi.responses import JSONResponse

from civic_stack.ahu.router import router as ahu_router
from civic_stack.bmkg.router import router as bmkg_router
from civic_stack.bpjph.router import router as bpjph_router
from civic_stack.bpom.router import router as bpom_router
from civic_stack.bps.router import router as bps_router
from civic_stack.jdih.router import router as jdih_router
from civic_stack.kpu.router import router as kpu_router
from civic_stack.lhkpn.router import router as lhkpn_router
from civic_stack.lpse.router import router as lpse_router
from civic_stack.ojk.router import router as ojk_router
from civic_stack.oss_nib.router import router as oss_nib_router
from civic_stack.simbg.router import router as simbg_router

app = FastAPI(
    title="indonesia-civic-stack",
    description=(
        "Production-ready scrapers and API wrappers for Indonesian government data sources. "
        "Phase 1: BPOM, BPJPH, AHU. Phase 2: KPU, OJK, OSS-NIB, LPSE. "
        "Phase 3: LHKPN, BPS, BMKG, SIMBG. Phase 4 (BizMark patch): JDIH."
    ),
    version="0.4.0-bizmark",
    contact={
        "name": "indonesia-civic-stack contributors",
        "url": "https://github.com/suryast/indonesia-civic-stack",
    },
    license_info={
        "name": "MIT / Apache-2.0 (per module — see LICENSES.md)",
        "url": "https://github.com/suryast/indonesia-civic-stack/blob/main/LICENSES.md",
    },
)

# Phase 1 routers
app.include_router(bpom_router)
app.include_router(bpjph_router)
app.include_router(ahu_router)

# Phase 2 routers
app.include_router(kpu_router)
app.include_router(ojk_router)
app.include_router(oss_nib_router)
app.include_router(lpse_router)

# Phase 3 routers
app.include_router(lhkpn_router)
app.include_router(bps_router)
app.include_router(bmkg_router)
app.include_router(simbg_router)

# Phase 4 (BizMark patch) routers
app.include_router(jdih_router)

_ALL_MODULES = [
    # Phase 1
    "bpom",
    "bpjph",
    "ahu",
    # Phase 2
    "kpu",
    "ojk",
    "oss_nib",
    "lpse",
    # Phase 3
    "lhkpn",
    "bps",
    "bmkg",
    "simbg",
    # Phase 4 (BizMark patch)
    "jdih",
]


@app.get("/", include_in_schema=False)
async def root() -> JSONResponse:
    return JSONResponse(
        {
            "name": "indonesia-civic-stack",
            "version": "0.4.0-bizmark",
            "phase": 4,
            "modules": _ALL_MODULES,
            "docs": "/docs",
            "openapi": "/openapi.json",
        }
    )


@app.get("/health")
async def health() -> dict:
    return {
        "status": "ok",
        "modules": _ALL_MODULES,
    }
