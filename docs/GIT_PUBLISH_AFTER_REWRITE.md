# Menerbitkan Repo Setelah Histori Lokal Berubah

Gunakan dokumen ini jika `git push origin main` gagal (**non-fast-forward** / *fetch first*) dan `git merge-base main origin/main` **gagal** (tidak ada nenek moyang bersama) — biasanya setelah `git filter-repo` atau rewrite histori lain di lokal, sementara GitHub masih memegang pohon lama.

---

## 1) Prasyarat

1. Working tree **bersih**: `git status` tidak boleh menampilkan perubahan yang belum di-commit (commit, stash, atau `git restore`).
2. Verifikasi kode: `make verify` (atau `bash scripts/verify-post-scrub-local.sh`) **hijau**.
3. Koordinasi tim: semua orang sadar SHA akan berubah; PR lama berbasis commit lama tidak lagi valid.

---

## 2) Terbitkan `main` saja (disarankan pertama)

### Opsi A — HTTPS + PAT (sekali jalan)

```bash
export GITHUB_TOKEN='...'   # PAT: Contents Read/Write + SSO org jika perlu
git fetch origin
git push --force-with-lease "https://x-access-token:${GITHUB_TOKEN}@github.com/tanerizawa-dev/bizmark-administration.git" main
```

Atau skrip terbatas `main`:

```bash
export GITHUB_TOKEN='...'
CONFIRM_FORCE_PUSH_MAIN=YES ./scripts/git-push-main-force-with-lease.sh
```

*(Skrip `git-push-main-force-with-lease.sh` memakai remote `origin` dari `git config`; pastikan credential helper / env sudah benar jika URL origin tanpa token.)*

### Opsi B — SSH

```bash
git remote set-url origin git@github.com:tanerizawa-dev/bizmark-administration.git
git fetch origin
git push --force-with-lease origin main
```

---

## 3) Terbitkan semua branch + tag (opsional)

Hanya jika Anda **yakin** semua branch lokal harus menimpa remote:

```bash
export GITHUB_TOKEN='...'
./scripts/git-force-push-with-github-token.sh
```

Perhatikan branch AI/experimen di remote — tim harus setuju sebelum menimpa.

---

## 4) Jika push ditolak (`permission denied`)

- Fine-grained PAT: **Contents: Read and write** untuk repo ini.
- Organisasi: **Authorize SSO** pada token.
- **Branch protection / ruleset** pada `main`: izinkan force-push sementara atau gunakan akun admin dengan bypass.

---

## 5) Setelah push berhasil

1. Kabari tim: `git fetch origin && git reset --hard origin/main` atau **clone baru**.
2. Cabut PAT yang dipakai di shell jika sekali pakai; jangan commit token.

---

## 6) Jika yang benar adalah **remote** (buang rewrite lokal)

**Hati-hati:** ini membuang commit yang hanya ada di lokal.

```bash
git fetch origin
git reset --hard origin/main
```

Lihat juga: `docs/SECURITY_GIT_HISTORY_SCRUB.md` (audit & scrub histori).
