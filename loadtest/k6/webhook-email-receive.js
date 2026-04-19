import http from 'k6/http'
import { check, sleep } from 'k6'

export const options = {
  scenarios: {
    ramp: {
      executor: 'ramping-arrival-rate',
      startRate: 100,
      timeUnit: '1s',
      preAllocatedVUs: 200,
      maxVUs: 5000,
      stages: [
        { target: 10000, duration: '5m' },
        { target: 10000, duration: '30m' },
        { target: 0, duration: '5m' },
      ],
    },
  },
  thresholds: {
    http_req_failed: ['rate<0.01'],
    http_req_duration: ['p(95)<500', 'p(99)<2000'],
  },
}

function randomNonce(len = 32) {
  const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'
  let out = ''
  for (let i = 0; i < len; i++) out += chars[Math.floor(Math.random() * chars.length)]
  return out
}

export default function () {
  const baseUrl = __ENV.BASE_URL ?? 'http://127.0.0.1:8000'
  const url = `${baseUrl}/webhook/email/receive`

  const payload = JSON.stringify({
    from: 'Sender <sender@example.com>',
    to: 'info@bizmark.id',
    subject: 'Load test',
    message_id: `k6-${__VU}-${__ITER}`,
    text: 'Hello',
  })

  const res = http.post(url, payload, {
    headers: {
      'Content-Type': 'application/json',
      'X-Timestamp': `${Math.floor(Date.now() / 1000)}`,
      'X-Nonce': randomNonce(32),
    },
  })

  check(res, {
    'status is 200/401/403': (r) => [200, 401, 403].includes(r.status),
  })

  sleep(0.01)
}

