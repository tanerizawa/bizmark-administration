# Pembersihan Histori Git (Path Admin Lama & String Sensitif)

Dokumen ini untuk **tim DevOps / maintainer repo** setelah path admin diganti ke `ADMIN_SECRET_PATH` dan kode tree sudah bersih. Tujuannya: mengurangi jejak string sensitif di **histori commit**, bukan hanya di HEAD.

> **Peringatan:** menulis ulang histori mengubah semua hash commit, memerlukan **force-push** ke semua remote, dan memaksa semua kontributor **re-sync** (rebase ulang atau clone baru). Jangan dijalankan tanpa jendela maintenance dan komunikasi tim.

---

## 1) Prasyarat

1. **Backup bare clone** (wajib):

   ```bash
   git clone --mirror https://github.com/ORG/REPO.git repo-backup-$(date +%Y%m%d).git
   ```

2. Pasang **git-filter-repo** (disarankan daripada filter-branch):

   ```bash
   # Debian/Ubuntu
   sudo apt-get update && sudo apt-get install -y git-filter-repo

   # atau pip (user)
   pip install git-filter-repo
   ```

   Verifikasi: `git filter-repo --version`

3. Pastikan **tidak ada PR/pipeline** yang bergantung pada SHA lama selama operasi.

---

## 2) Audit sebelum scrub

Dari root repo:

```bash
./scripts/git-audit-sensitive-paths.sh
```

Atau manual:

```bash
git log --all --oneline -S'LEGACY_SUBSTRING' | head -50
```

Ganti `LEGACY_SUBSTRING` dengan substring yang ingin dihapus dari blob histori (misalnya path admin lama yang sudah tidak dipakai). Hindari string terlalu pendek agar tidak merusak commit tidak terkait.

---

## 3) Eksekusi terstandar di repo ini

1. Pastikan working tree **bersih** (semua perubahan sudah di-commit).
2. Backup mirror (lihat §1).
3. Jalankan:

   ```bash
   export CONFIRM_HISTORY_REWRITE=I_ACCEPT_REWRITE_AND_FORCE_PUSH
   ./scripts/git-history-scrub-execute.sh
   ```

Skrip membangun ekspresi pengganti di file sementara (bukan file ter-track) agar aturan `git filter-repo` tidak ikut merusak dirinya sendiri, lalu memproses **blob** dan **pesan commit** (`--replace-text` + `--replace-message`). Remote lokal dipulihkan dari cadangan sementara setelah rewrite.

Dokumentasi resmi: [git-filter-repo manual](https://github.com/newren/git-filter-repo).

### Contoh manual (tanpa skrip)

```bash
git filter-repo --force \
  --replace-text replacements.txt \
  --replace-message replacements.txt
```

File `replacements.txt` memakai sintaks yang sama (satu pasangan `literal==>pengganti` per baris).

---

## 4) Setelah filter-repo

1. `bash scripts/git-audit-sensitive-paths.sh` — pastikan substring path admin yang sudah tidak dipakai tidak lagi muncul berurutan di **blob** ter-track. (Skrip audit membangun pola sensitif lewat `\x2f` agar file sumber tidak menyimpan path utuh.)
2. `git log -S'<substring_lama>' --oneline --all` — pastikan substring yang baru saja di-scrub tidak lagi muncul di diff histori (ganti placeholder dengan pola audit tim).
3. Regresi ringan pada tree hasil rewrite — jalankan `./scripts/verify-post-scrub-local.sh` (`config:clear`, audit, **PHPUnit `tests/Unit`** dengan SQLite in-memory, `route:list`). Di Debian/Ubuntu pastikan paket `php8.4-sqlite3` (atau versi PHP Anda) terpasang agar driver `pdo_sqlite` ada.
4. **Force-push** ke remote kanonikal (tanpa ini, rewrite hanya ada di mesin lokal):

   ```bash
   git push origin --force --all
   git push origin --force --tags
   ```

   Jika repo memakai remote `upstream` sebagai sumber kebenaran kedua, ulangi perintah yang sama dengan `upstream` setelah koordinasi pemilik organisasi.

   **Autentikasi:** URL `https://github.com/...` membutuhkan kredensial (PAT + credential helper, atau `gh auth login`). Alternatif: `git remote set-url origin git@github.com:ORG/REPO.git` lalu push lewat SSH key.

5. Instruksikan tim: `git fetch origin && git reset --hard origin/main` (ganti `main` jika default branch lain), atau **clone baru** — semua SHA lama tidak lagi valid.

---

## 5) Alternatif tanpa rewrite histori

- Putar ulang **secret** (URL admin, password DB, API key) sehingga nilai di histori tidak lagi berguna.
- Pertahankan tree saat ini bersih (sudah menjadi kontrol utama).

Rewrite histori adalah **lapisan tambahan** kebersihan, bukan pengganti rotasi kredensial.
