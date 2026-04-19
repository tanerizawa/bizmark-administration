# Audit Ekosistem VPS Debian (2026-04-19)

## Ringkasan Eksekutif
Audit ini dilakukan pada mesin Debian yang menjalankan Docker dan beberapa layanan jaringan publik. Fokus audit: konfigurasi OS, inventory service/port, keamanan dasar (SSH, user, update policy), utilisasi resource, dan dependensi yang terpasang.

**Temuan risiko tertinggi (perlu prioritas segera):**
- SSH mengizinkan **root login** dan **password authentication** (kombinasi ini sangat berisiko terhadap brute-force).
- Banyak port terbuka ke publik (termasuk **3389** dan beberapa port aplikasi non-standar). Firewall rules tidak bisa diverifikasi dari konteks eksekusi ini (kemungkinan keterbatasan capability), sehingga status proteksi jaringan tidak bisa dipastikan.

## 1) Konteks & Batasan Audit
- Sistem terlihat menggunakan `systemd` sebagai PID 1, tetapi akses ke kontrol service via `systemctl`/DBus dan kontrol netfilter (`ufw/nft/iptables`) dari sesi ini terbatas (kemungkinan karena sandbox/capability).
- Karena batasan tersebut, inventory service dilakukan melalui **proses yang berjalan** dan **port listening** (ss/ps), bukan status unit systemd sepenuhnya.

## 2) Informasi Sistem Operasi
- CPU: 4 vCPU (AMD EPYC, virtualized KVM).
- RAM: 15GiB (used ~6.8GiB, available ~8.8GiB saat audit).
- Swap: 8GiB (used ~1.7GiB).
- Disk: ~200GiB (root ext4), penggunaan ~70% (sisa ~57GiB).
- systemd-resolved aktif (stub resolv.conf).

## 3) Inventory Port & Layanan yang Terlihat
### 3.1 Port listening (ringkas)
**Publik (0.0.0.0 / :::)**
- 22/tcp: SSH
- 80/tcp: HTTP
- 443/tcp: HTTPS
- 3389/tcp: RDP (risiko tinggi bila tidak dibatasi)
- 8443/tcp: HTTPS alternatif / panel
- 3001, 3002, 3003, 3005/tcp: aplikasi (perlu identifikasi & pembatasan)
- 4000/tcp, 5000/tcp, 18789/tcp, 5433/tcp: aplikasi/DB/proxy (perlu validasi kebutuhan & pembatasan)

**Loopback saja (127.0.0.1 / ::1)**
- 5432/tcp: PostgreSQL
- 6379/tcp: Redis
- 25/tcp: local mail submission (loopback)
- beberapa port internal aplikasi (mis. 8888, 3000, dst)

### 3.2 Proses dominan
Terlihat proses terkait:
- Docker daemon (`dockerd`) dan `containerd`.
- Redis (`redis-server`).
- PostgreSQL processes (`postgres: ...`).
- Nginx & Apache terinstal (lihat paket), dan port 80/443 aktif (perlu dipastikan siapa yang benar-benar bind).
- Ada beberapa proses Node/Next/Chromium terkait tooling/automation.

## 4) Audit Keamanan

### 4.1 SSH configuration (temuan kritikal)
Nilai penting yang terdeteksi dari `/etc/ssh/sshd_config`:
- `PermitRootLogin yes`
- `PasswordAuthentication yes`
- `X11Forwarding yes`
- `PubkeyAuthentication yes`

**Risiko:**
- Root login + password auth adalah target utama brute-force dan credential stuffing.
- X11 forwarding jarang dibutuhkan di server produksi; memperluas attack surface.

**Rekomendasi (Prioritas P0):**
1) Set:
   - `PermitRootLogin no`
   - `PasswordAuthentication no` (pastikan key-based access sudah valid)
   - `X11Forwarding no`
2) Tambahkan hardening:
   - `MaxAuthTries 3`
   - `LoginGraceTime 30`
   - `ClientAliveInterval 300`
   - `ClientAliveCountMax 2`
3) (Opsional) ubah port SSH dari 22 dan batasi `AllowUsers`/`AllowGroups`.
4) Restart SSH: `systemctl restart ssh` (wajib dilakukan hati-hati agar tidak mengunci akses).

### 4.2 User & privilege
User login-capable terdeteksi: `root`, `debian`, `bizmark`, `postgres`, `kasm`, dll.
- Group `sudo`: `debian` (punya sudo).
- Fail2ban aktif dengan jail `sshd`.

**Rekomendasi (P0/P1):**
- Pastikan hanya user yang diperlukan berada di grup `sudo`.
- Terapkan kebijakan password kuat + rotasi akses key.
- Pastikan fail2ban memonitor port SSH yang sebenarnya.

### 4.3 Firewall rules
Paket `ufw`, `nftables`, `iptables` terinstal, namun dari sesi audit ini perintah `ufw/nft/iptables` tidak dapat menampilkan ruleset (indikasi keterbatasan permission/capability).

**Risiko:**
- Tidak bisa memastikan apakah port publik benar-benar dibatasi di layer firewall.

**Rekomendasi (P0):**
Verifikasi dari shell host (non-sandbox):
- `ufw status verbose` atau `nft list ruleset`
- Pastikan hanya port yang dibutuhkan yang dibuka.
- Minimal: batasi 3389/8443/300x/4000/5000/18789/5433 dengan allowlist IP (VPN/office IP).

### 4.4 Update policy
Konfigurasi unattended-upgrades terdeteksi (`/etc/apt/apt.conf.d/50unattended-upgrades`).
**Rekomendasi (P1):**
- Pastikan service unattended-upgrades aktif dan ada monitoring patching window.

## 5) Audit Utilisasi Resource & Kapasitas
- RAM masih cukup (available ~8.8GiB) namun swap sudah terpakai (~1.7GiB) → indikasi pressure periodik.
- Disk root 70% → masih aman, tetapi docker overlay banyak mount; perlu housekeeping docker images/volumes untuk mencegah disk penuh.

**Rekomendasi (P1):**
- Monitoring: node_exporter/telegraf + alerting untuk disk >85% dan swap >0 berkelanjutan.
- Docker cleanup berkala: prune image/volume yang tidak dipakai (dengan SOP yang aman).

## 6) Aplikasi & Dependensi Terinstal (indikasi)
Paket kunci yang terpasang:
- Web server: `nginx`, `apache2`
- Runtime: `php 8.4`, `php-fpm`, `php8.4-fpm`
- DB/cache: `postgresql 17`, `redis-server`
- Container: `docker-ce`
- Security: `openssh-server`, `fail2ban`, `ufw`, `nftables`, `iptables`
- Tooling: `node` (v22), `npm` (10), `composer` (2.9)

**Catatan penting:**
Nginx dan Apache sama-sama terpasang. Pastikan arsitektur jelas:
- salah satu jadi reverse proxy dan yang lain tidak aktif, atau
- hanya satu yang dipakai untuk :80/:443.

## 7) Prioritas Perbaikan (Action Plan)
### P0 (hari ini)
- Hardening SSH: disable root login + password auth + X11 forwarding, pastikan key-based login berfungsi.
- Audit port publik: pastikan hanya port yang benar-benar diperlukan dibuka.
- Jika 3389/8443 harus ada, wajib allowlist IP / VPN.

### P1 (minggu ini)
- Pastikan unattended-upgrades aktif + log/alert patching.
- Rapikan stack web (nginx vs apache) agar tidak tumpang tindih.
- Pasang monitoring disk/mem/swap + housekeeping docker.

### P2 (berkala)
- Review user & grup sudo secara rutin.
- Review log auth + fail2ban metrics.
- Dokumentasi SOP deploy/backup/rollback.

## 8) Output Dokumen Pendukung
Audit ini disusun berdasarkan observasi runtime dari `ps`, `ss`, `lscpu`, `free`, `df`, `dpkg -l`, dan pembacaan konfigurasi sshd. IP telah disamarkan untuk keamanan.
