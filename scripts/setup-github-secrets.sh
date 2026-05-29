#!/usr/bin/env bash
# Setup GitHub Secrets untuk auto-deploy bizmark.id
# 
# Cara pakai:
#   export GITHUB_TOKEN=ghp_xxxxxxxxxxxx
#   bash scripts/setup-github-secrets.sh
#
# Atau jalankan langsung (akan prompt token):
#   bash scripts/setup-github-secrets.sh
set -euo pipefail

REPO="tanerizawa/bizmark-administration"
VPS_HOST="72.61.143.92"
VPS_USER="bizmark"
VPS_PORT="2222"
SSH_KEY_FILE="/root/.ssh/bizmark_deploy"

# Login gh jika belum
if ! gh auth status &>/dev/null; then
    if [ -n "${GITHUB_TOKEN:-}" ]; then
        echo "$GITHUB_TOKEN" | gh auth login --with-token
    else
        echo "Masukkan GitHub Personal Access Token (Settings > Developer settings > PAT > Classic)"
        echo "Scope yang dibutuhkan: repo, workflow, admin:repo_hook"
        echo ""
        read -rsp "Token: " TOKEN
        echo ""
        echo "$TOKEN" | gh auth login --with-token
    fi
fi

echo "==> Logged in as: $(gh api user --jq .login)"
echo "==> Setting secrets untuk repo: $REPO"
echo ""

gh secret set VPS_HOST    --body "$VPS_HOST"                  --repo "$REPO" && echo "  VPS_HOST = $VPS_HOST"
gh secret set VPS_USER    --body "$VPS_USER"                  --repo "$REPO" && echo "  VPS_USER = $VPS_USER"
gh secret set VPS_PORT    --body "$VPS_PORT"                  --repo "$REPO" && echo "  VPS_PORT = $VPS_PORT"
gh secret set VPS_SSH_KEY --body "$(cat "$SSH_KEY_FILE")"     --repo "$REPO" && echo "  VPS_SSH_KEY = [set from $SSH_KEY_FILE]"

echo ""
echo "==> Buat environment 'production'"
gh api "repos/$REPO/environments/production" --method PUT \
    --field wait_timer=0 \
    --silent && echo "  environment 'production' OK"

echo ""
echo "==> Secrets aktif:"
gh secret list --repo "$REPO"

echo ""
echo "Pipeline siap! Setiap push ke main akan auto-deploy."
echo "Monitor: https://github.com/$REPO/actions"
