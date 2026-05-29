# Setup Auto-Deploy ke VPS Production

## GitHub Secrets yang Dibutuhkan

Buka: `https://github.com/tanerizawa/bizmark-administration/settings/secrets/actions`

Tambahkan secrets berikut:

| Secret | Nilai | Keterangan |
|--------|-------|-----------|
| `VPS_HOST` | IP atau domain VPS | Contoh: `103.x.x.x` atau `vps.bizmark.id` |
| `VPS_USER` | `bizmark` | User SSH di VPS |
| `VPS_SSH_KEY` | Private key SSH | Lihat cara generate di bawah |
| `VPS_PORT` | `22` | Port SSH (opsional, default 22) |

## Cara Generate SSH Key untuk Deploy

Jalankan di lokal (bukan di VPS):

```bash
ssh-keygen -t ed25519 -C "github-actions-deploy" -f ~/.ssh/bizmark_deploy -N ""
```

Ini menghasilkan dua file:
- `~/.ssh/bizmark_deploy` — **private key** → masukkan ke GitHub Secret `VPS_SSH_KEY`
- `~/.ssh/bizmark_deploy.pub` — **public key** → tambahkan ke VPS

## Tambahkan Public Key ke VPS

```bash
# Di VPS, jalankan:
echo "ISI_PUBLIC_KEY_DISINI" >> /home/bizmark/.ssh/authorized_keys
chmod 600 /home/bizmark/.ssh/authorized_keys
```

## Tambahkan GitHub Environment "production"

Buka: `https://github.com/tanerizawa/bizmark-administration/settings/environments`

1. Klik **New environment** → nama: `production`
2. Tambahkan **Required reviewers** jika ingin approval manual sebelum deploy
3. Tambahkan **Environment secrets** jika ada secret khusus production

## Cara Kerja Pipeline

```
Push ke main
    │
    ├── test (PHPUnit) ──┐
    │                    ├── PASS → deploy ke VPS via SSH
    └── pint (style) ───┘         └── git pull + deploy-vps.sh
                         FAIL → stop, tidak deploy
```

## Test Manual Deploy

Setelah secrets terpasang, bisa trigger manual dari:
`https://github.com/tanerizawa/bizmark-administration/actions`
→ pilih workflow **CI** → **Run workflow**

## Verifikasi Deploy Berhasil

```bash
# SSH ke VPS
ssh bizmark@VPS_HOST

# Cek versi yang berjalan
cd /home/bizmark/bizmark.id
git log --oneline -3
php artisan about --only=environment
```
