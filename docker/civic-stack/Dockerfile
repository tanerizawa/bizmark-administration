# indonesia-civic-stack — BizMark deployment image
#
# Build strategy:
#   1. Clone upstream indonesia-civic-stack from GitHub
#   2. COPY patches/ over the cloned source (adds JDIH module, fixes OSS-NIB selectors)
#   3. pip install in editable mode + uvicorn
#   4. Install Playwright Chromium for Playwright-dependent modules (BPJPH, OSS-NIB, JDIH)
#   5. Serve the patched unified app via uvicorn

FROM python:3.12-slim

WORKDIR /app

# System deps: git for cloning, curl for healthcheck, playwright browser deps
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Clone upstream source
RUN git clone --depth 1 https://github.com/suryast/indonesia-civic-stack.git /app

# Apply BizMark patches over the cloned source:
#   - patches/civic_stack/jdih/         → new JDIH module (router + __init__)
#   - patches/civic_stack/oss_nib/      → fixed _find_search_input selectors
#   - patches/civic_stack/app.py        → registers jdih router in unified app
COPY patches/ /app/

# Install package with all required extras:
#   mcp     = fastmcp (MCP server)
#   api     = fastapi + uvicorn (HTTP server)
#   browser = playwright + camoufox (for BPJPH, OSS-NIB, JDIH scrapers)
RUN pip install --no-cache-dir -e ".[mcp,api,browser]"

# Install Playwright browser (Chromium) — needed by BPJPH, OSS-NIB, JDIH
RUN python -m playwright install chromium --with-deps

ENV PYTHONUNBUFFERED=1
ENV PYTHONDONTWRITEBYTECODE=1

EXPOSE 8000

# Use patched app.py as the entry point
CMD ["uvicorn", "civic_stack.app:app", "--host", "0.0.0.0", "--port", "8000"]
