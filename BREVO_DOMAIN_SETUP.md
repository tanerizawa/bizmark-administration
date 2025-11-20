# Setup Domain Email di Brevo (Sendinblue)

## Masalah
Email dikirim dari: `noreply@10192393.brevosend.com`  
Seharusnya dari: `noreply@bizmark.id`

## Solusi: Verifikasi Domain di Brevo

### Langkah 1: Tambah Domain di Brevo Dashboard
1. Login ke https://app.brevo.com
2. Buka menu **Senders & IP** → **Domains**
3. Klik **Add a Domain**
4. Masukkan: `bizmark.id`

### Langkah 2: Verifikasi Domain dengan DNS Records
Brevo akan memberikan DNS records yang perlu ditambahkan:

#### SPF Record (TXT)
```
Type: TXT
Name: @
Value: v=spf1 include:spf.brevo.com ~all
```

#### DKIM Record (CNAME atau TXT)
```
Type: CNAME
Name: mail._domainkey
Value: mail._domainkey.bizmark.id.brevo.com
```

#### DMARC Record (TXT) - Optional tapi recommended
```
Type: TXT
Name: _dmarc
Value: v=DMARC1; p=none; rua=mailto:dmarc@bizmark.id
```

### Langkah 3: Tambahkan Records ke DNS Provider
1. Login ke DNS provider (Cloudflare/namecheap/dll)
2. Tambahkan ketiga DNS records di atas
3. Tunggu propagasi DNS (bisa 5 menit - 48 jam)

### Langkah 4: Verifikasi di Brevo
1. Kembali ke Brevo Dashboard → **Domains**
2. Klik **Verify** pada domain `bizmark.id`
3. Brevo akan cek DNS records
4. Status akan berubah menjadi **Verified** ✓

### Langkah 5: Set sebagai Default Sender
1. Di Brevo Dashboard → **Senders & IP** → **Senders**
2. Tambah sender baru: `noreply@bizmark.id`
3. Set sebagai **Default Sender**

## Setelah Verifikasi Selesai
Email akan otomatis dikirim dari `noreply@bizmark.id` tanpa perlu ubah kode lagi.

## Status Saat Ini
✅ Kode Laravel sudah dikonfigurasi dengan benar
✅ `.env` sudah set `MAIL_FROM_ADDRESS="noreply@bizmark.id"`
✅ Mailable classes sudah enforce from address
⏳ **Menunggu verifikasi domain di Brevo**

## Troubleshooting

### Email masih dari brevosend.com?
- Pastikan domain sudah terverifikasi (status: Verified)
- Pastikan `noreply@bizmark.id` sudah ditambah sebagai sender
- Clear cache: `php artisan config:clear`

### DNS tidak terdeteksi?
- Tunggu 24-48 jam untuk propagasi DNS
- Cek DNS dengan tool: https://mxtoolbox.com/SuperTool.aspx
- Pastikan tidak ada typo di DNS records

### Email masuk spam?
- Pastikan SPF, DKIM, dan DMARC sudah terkonfigurasi
- Warming up: kirim email bertahap (jangan langsung banyak)
- Avoid spam trigger words di subject/content
