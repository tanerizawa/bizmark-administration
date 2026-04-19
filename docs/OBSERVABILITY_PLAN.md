# Observability Plan (Next Phase)

Dokumen ini merangkum desain observability lanjutan untuk Bizmark.id, dengan pendekatan bertahap agar implementasi aman dan tidak mengganggu produksi.

## 1) Fondasi yang sudah ada

- Request correlation via middleware Request ID: [app.php](file:///home/bizmark/bizmark.id/bootstrap/app.php)
- Webhook replay protection metrics berbasis cache + dashboard endpoint:
  - [EnsureEmailWebhookReplayProtection.php](file:///home/bizmark/bizmark.id/app/Http/Middleware/EnsureEmailWebhookReplayProtection.php)
  - [SecurityDashboardController.php](file:///home/bizmark/bizmark.id/app/Http/Controllers/Admin/SecurityDashboardController.php)
  - [EmailWebhookMetricsReport.php](file:///home/bizmark/bizmark.id/app/Console/Commands/EmailWebhookMetricsReport.php)

## 2) Distributed tracing (Jaeger/Zipkin)

Rekomendasi:
- Gunakan OpenTelemetry SDK untuk PHP dan export ke Jaeger atau Zipkin.
- Scope minimum:
  - HTTP inbound spans per request (route name, status code, duration).
  - DB query spans untuk query yang lambat.
  - External HTTP spans untuk call ke Midtrans/OpenRouter/Search Console dsb.

Tahapan rollout:
1. Enable trace context propagation + sampling rendah (mis. 1-5%).
2. Naikkan sampling khusus endpoint kritikal (payments/webhook).
3. Tambahkan attribute sanitization (PII) dan redaction.

## 3) Metrics & alerting

Metrics minimum per modul:
- Throughput (requests/minute)
- Error rate (4xx/5xx)
- Latency p95/p99

Alert baseline (contoh):
- p95 > 500ms (5 menit)
- error rate > 1% (5 menit)
- Redis healthcheck gagal > 3x

## 4) Log aggregation

Rekomendasi:
- Standarisasi struktur log: request_id, route, user_id (jika ada), module, duration_ms.
- Kirim ke ELK/Loki dengan retention policy.

## 5) RUM (frontend)

Target:
- Core Web Vitals (LCP, CLS, INP)
- Error JS, network failures, route performance

## 6) Deliverables

- Tracing:
  - Dokumen install & konfigurasi collector (Jaeger/Zipkin).
  - Instrumentasi minimal HTTP + DB.
- Metrics:
  - Dashboard per modul (Grafana).
  - Alerting rules.
- Logging:
  - Pipeline log + redaction policy.

