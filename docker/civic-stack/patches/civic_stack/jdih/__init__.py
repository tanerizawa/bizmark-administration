"""
modules/jdih — Indonesian legal regulations aggregator.

Source: peraturan.go.id (Jaringan Dokumentasi dan Informasi Hukum Nasional)
Method: Playwright — JS-rendered portal
License: MIT

Public API:
    fetch(regulation_id) -> CivicStackResponse
    search(keyword, regulation_type='uu') -> list[CivicStackResponse]
    list_recent(regulation_type='uu', limit=10) -> list[CivicStackResponse]
"""

from __future__ import annotations

from civic_stack.jdih.scraper import fetch, list_recent, search

__all__ = ["fetch", "search", "list_recent"]
