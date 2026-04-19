# Load Testing (k6) — 10.000 RPS Plan & Deliverables

Dokumen ini menyediakan scaffolding load test menggunakan k6 untuk target 10.000 RPS, serta template laporan untuk analisis bottleneck dan rekomendasi optimasi.

## Prasyarat staging (mirror production)

- Minimal 3 instance aplikasi di belakang load balancer.
- Redis untuk cache + session (disarankan cluster di production).
- Queue system sesuai production (worker terpisah dari web).
- Logging terpusat dan monitoring sistem (CPU/mem/IO) per node.
- Email webhook replay protection aktif:
  - `EMAIL_WEBHOOK_REPLAY_PROTECTION_ENABLED=true`
  - `EMAIL_WEBHOOK_CACHE_STORE=redis`

## Script k6

- Webhook inbound email: [webhook-email-receive.js](file:///home/bizmark/bizmark.id/loadtest/k6/webhook-email-receive.js)

Gunakan variable environment:
- `BASE_URL` (contoh: `https://staging.bizmark.id`)

## Skenario

- Ramp-up 5 menit ke 10k RPS
- Sustain 30 menit
- Ramp-down 5 menit

Threshold:
- p95 < 500ms
- p99 < 2s
- error rate < 1%

## Checklist pengukuran

- Aplikasi: p95/p99 latency, error rate, throughput per endpoint.
- Infrastruktur: CPU, memory, IO, network.
- Redis: latency read/write, connection health, command stats.
- DB: slow query log, lock contention, connections.

## Template laporan hasil load testing

### 1) Ringkasan eksekusi
- Target RPS:
- Durasi:
- Commit/branch:
- Topology staging:

### 2) Hasil utama
- p95:
- p99:
- Error rate:
- Throughput:

### 3) Bottleneck yang ditemukan
- Aplikasi:
- DB:
- Redis:
- Network / LB:

### 4) Rekomendasi optimasi
- Quick wins:
- Medium term:
- Long term:

### 5) Risiko & keputusan
- Risiko yang diterima:
- Aksi mitigasi:

