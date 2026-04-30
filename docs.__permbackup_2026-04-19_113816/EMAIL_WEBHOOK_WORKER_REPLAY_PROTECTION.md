# Email Webhook Replay Protection — Cloudflare Worker Changes

Repo ini tidak menyertakan source Cloudflare Worker inbound email. Untuk mengaktifkan replay protection (timestamp + nonce) end-to-end, Worker perlu mengirim header tambahan pada setiap request ke endpoint:

- `POST /webhook/email/receive`

## Header yang wajib dikirim

- `X-Timestamp`: epoch seconds (UTC), contoh `1713412345`
- `X-Nonce`: string acak unik per request (min 16 char, max 128 char)

Header ini diverifikasi oleh middleware:
- [EnsureEmailWebhookReplayProtection.php](file:///home/bizmark/bizmark.id/app/Http/Middleware/EnsureEmailWebhookReplayProtection.php)

## Rekomendasi implementasi Worker (pseudocode)

```js
const timestamp = Math.floor(Date.now() / 1000).toString()
const nonce = crypto.randomUUID().replaceAll('-', '') + crypto.randomUUID().replaceAll('-', '')

const body = JSON.stringify(payload)
const signature = await hmacSha256Hex(body, WEBHOOK_SECRET)

await fetch(WEBHOOK_URL, {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'X-Webhook-Signature': signature,
    'X-Timestamp': timestamp,
    'X-Nonce': nonce,
  },
  body,
})
```

## Konfigurasi aplikasi

Aktifkan replay protection di environment production:
- `EMAIL_WEBHOOK_REPLAY_PROTECTION_ENABLED=true`
- `EMAIL_WEBHOOK_CACHE_STORE=redis`

Untuk window:
- `EMAIL_WEBHOOK_MAX_AGE_SECONDS=300` (maksimal 5 menit ke belakang)
- `EMAIL_WEBHOOK_MAX_FUTURE_SKEW_SECONDS=30` (maksimal 30 detik ke depan)

Nonce retention:
- `EMAIL_WEBHOOK_NONCE_TTL_SECONDS=86400` (24 jam)
- `EMAIL_WEBHOOK_RESPONSE_CACHE_TTL_SECONDS=86400`

