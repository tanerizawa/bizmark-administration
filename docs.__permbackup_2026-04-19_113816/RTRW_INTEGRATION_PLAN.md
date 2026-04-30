# RENCANA INTEGRASI PETA ZONA RTRW KE POLYGON SHP MAKER

## Latar Belakang

Polygon SHP Maker saat ini hanya membantu klien membuat file SHP untuk OSS. Namun, klien seringkali **tidak mengetahui zona/peruntukan tata ruang** dari lokasi yang mereka gambar poligonnya. Dengan mengintegrasikan data **RTRW (Rencana Tata Ruang Wilayah)**, tool ini dapat memberikan informasi zona kawasan secara otomatis — sehingga klien langsung tahu apakah lokasi mereka masuk ke zona permukiman, industri, pertanian, hutan lindung, dll.

## Sumber Data Terbuka yang Terverifikasi

### Sumber Utama: GISTARU RTR Online (Kementerian ATR/BPN)

| Item | Detail |
|------|--------|
| **Platform** | ArcGIS Server 10.91 |
| **URL Base** | `https://gistaru.atrbpn.go.id/arcgis/rest/services/` |
| **Proxy** | `https://gistaru.atrbpn.go.id/proxy_rtronline/run.ashx?` (wajib, direct access butuh token) |
| **Proyeksi** | WGS84 (EPSG:4326) — sama dengan SHP Maker |
| **Tipe Data** | RTRW Provinsi/Kab/Kota (Perda) |
| **Cakupan** | 34 Provinsi (mayoritas tersedia, beberapa kabupaten masih kosong/gray) |
| **Format** | ArcGIS MapServer — support Export Map (PNG), Query, Identify |

#### Struktur Service RTRW
```
Pattern: {kode}_RTR_KABUPATEN_KOTA_PROVINSI_{NAMA}/_{kode_provinsi}_{NAMA}_PR_PERDA/MapServer
Contoh: 021_RTR_KABUPATEN_KOTA_PROVINSI_JAWA_BARAT/_3200_JAWA_BARAT_PR_PERDA/MapServer
```

Setiap MapServer memiliki **layer per kabupaten/kota**:
- Layer 0: _3201_KABUPATEN BOGOR
- Layer 1: _3202_KABUPATEN SUKABUMI
- Layer 20: _3273_KOTA BANDUNG
- dsb.

#### Atribut Data Zona (Field)
| Field | Deskripsi | Contoh |
|-------|-----------|--------|
| **NAMOBJ** | Nama Zona/Peruntukan | "Kawasan Permukiman Perkotaan" |
| **WADMPR** | Provinsi | "Provinsi Jawa Barat" |
| **WADMKK** | Kabupaten/Kota | "Kabupaten Bogor" |
| **WADMKC** | Kecamatan | "Kecamatan Caringin" |
| **NOTHPR** | Dasar Hukum (Perda) | "Perda Kabupaten Bogor No 1 Tahun 2024..." |
| **REMARK** | Catatan | "KEK Lido" |

#### Jenis Zona RTRW yang Tersedia
| Kategori | Zona |
|----------|------|
| **Lindung** | Hutan Lindung, Cagar Alam, Taman Nasional, Taman Wisata Alam |
| **Perlindungan** | Perlindungan Setempat, Badan Air, Keunikan Bentang Alam |
| **Budidaya - Pertanian** | Tanaman Pangan, Hortikultura, Perkebunan, Peternakan |
| **Budidaya - Non-Pertanian** | Permukiman Perkotaan, Permukiman Perdesaan, Industri, Pariwisata |
| **Lainnya** | Pertambangan, Perikanan Budi Daya, Pertahanan & Keamanan, Hutan Produksi |

### Sumber Tambahan (Opsional/Masa Depan)

| Sumber | URL | Data | Status |
|--------|-----|------|--------|
| GISTARU RDTR SEAMLESS | `SEAMLESS/MapServer` | RDTR seluruh Indonesia | Cakupan terbatas |
| BIG GeoServices | `geoservices.big.go.id` | Penutup Lahan, Batas Wilayah | Pelengkap |
| Peta Peruntukan ATR/BPN | `petaperuntukan.atrbpn.go.id` | Peta Peruntukan | Belum ada API publik |

---

## Rencana Implementasi

### FASE 1: Server-Side Proxy & Mapping Data (Backend)
> **Tujuan**: Membangun proxy API di Laravel untuk menjembatani browser ke GISTARU ArcGIS

#### 1.1 Buat Mapping Provinsi → Service Path
```
File: config/rtrw.php
```
Mapping kode provinsi (dari dropdown Polygon SHP Maker) ke service path GISTARU:
```php
// Contoh:
'provinces' => [
    '11' => '001_RTR_KABUPATEN_KOTA_PROVINSI_ACEH/_1100_ACEH_PR_PERDA',
    '12' => '002_RTR_KABUPATEN_KOTA_PROVINSI_SUMATERA_UTARA/_1200_SUMATERA_UTARA_PR_PERDA',
    '32' => '021_RTR_KABUPATEN_KOTA_PROVINSI_JAWA_BARAT/_3200_JAWA_BARAT_PR_PERDA',
    '33' => '022_RTR_KABUPATEN_KOTA_PROVINSI_JAWA_TENGAH/_3300_JAWA_TENGAH_PR_PERDA',
    // ... 34 provinsi
]
```

#### 1.2 Buat Mapping Kabupaten/Kota → Layer ID
```
File: config/rtrw.php (atau database migration)
```
Mapping kode kabupaten/kota ke layer ID di masing-masing MapServer.
Data ini bisa di-fetch sekali dari API dan di-cache.

#### 1.3 Buat Proxy Controller
```
File: app/Http/Controllers/Api/RtrwProxyController.php
```

Endpoint yang diperlukan:

| Method | Endpoint | Fungsi |
|--------|----------|--------|
| GET | `/api/rtrw/zona?lat={lat}&lng={lng}&kab_kode={kode}` | Query zona di titik tertentu |
| GET | `/api/rtrw/overlay?bbox={bbox}&provinsi_kode={kode}` | Ambil tile PNG overlay RTRW |
| GET | `/api/rtrw/layers/{provinsi_kode}` | List layer kab/kota di provinsi |

**Mengapa proxy server-side?**
- GISTARU membutuhkan proxy (`proxy_rtronline`) untuk akses tanpa token
- CORS blocking jika diakses langsung dari browser
- Bisa menambahkan caching (Redis/file) untuk mengurangi beban ke GISTARU
- Bisa menambahkan rate limiting untuk mencegah abuse
- Kontrol error handling yang lebih baik

#### 1.4 Caching Strategy
```
File: Cache layer menggunakan Laravel Cache (Redis/File)
```
- **Layer list per provinsi**: Cache 24 jam (jarang berubah)
- **Zona query per koordinat**: Cache 1 jam (data RTRW jarang update)
- **Overlay tiles**: Cache 6 jam (gambar PNG statis)
- Cache key pattern: `rtrw:zona:{provinsi}:{layer}:{lat_rounded}:{lng_rounded}`

---

### FASE 2: Overlay Peta RTRW (Frontend - Leaflet)
> **Tujuan**: Menampilkan lapisan peta RTRW sebagai overlay transparan di atas peta dasar

#### 2.1 Toggle Layer RTRW
Tambahkan toggle button di toolbar peta untuk mengaktifkan/menonaktifkan overlay RTRW.

```
Lokasi: resources/views/tools/polygon-shp.blade.php
Alpine state: showRtrwOverlay: false
```

#### 2.2 Dynamic Tile Layer
Ketika user memilih provinsi/kabupaten, otomatis load overlay RTRW dari proxy endpoint.

```javascript
// Pseudo-code
if (showRtrwOverlay && selectedProvinsi) {
    rtrwLayer = L.tileLayer.wms('/api/rtrw/overlay', {
        layers: layerId,
        provinsi_kode: selectedProvinsi,
        format: 'image/png',
        transparent: true,
        opacity: 0.5
    }).addTo(map);
}
```

Alternatif: Karena GISTARU menggunakan ArcGIS (bukan standard WMS), gunakan **esri-leaflet** plugin atau custom `L.TileLayer` yang memanggil endpoint `export` per tile.

#### 2.3 Opacity Slider
Slider untuk mengatur transparansi overlay RTRW (default 50%).

#### 2.4 Legend (Legenda Zona)
Tampilkan legenda warna zona RTRW otomatis berdasarkan data `uniqueValueInfos` dari MapServer.

---

### FASE 3: Identifikasi Zona Otomatis (Core Feature)
> **Tujuan**: Setelah user selesai menggambar poligon, otomatis tunjukkan zona apa yang termasuk

#### 3.1 Centroid Query
Setelah poligon selesai digambar/diedit:
1. Hitung centroid poligon
2. Kirim request ke `/api/rtrw/zona?lat=...&lng=...&kab_kode=...`
3. Tampilkan hasil zona di panel informasi

#### 3.2 Multi-Point Query (untuk poligon besar)
Untuk poligon yang melintasi beberapa zona:
1. Sample beberapa titik di dalam poligon (centroid + corners + random interior)
2. Query semua titik
3. Aggregate: "Poligon Anda mencakup: 60% Permukiman Perkotaan, 30% Kawasan Pariwisata, 10% Perlindungan Setempat"

#### 3.3 UI: Info Panel Zona
Tambahkan card/panel baru di sidebar yang menampilkan:

```
┌──────────────────────────────────────┐
│ 📍 Informasi Zona Tata Ruang (RTRW) │
├──────────────────────────────────────┤
│ Zona Utama: Kawasan Permukiman       │
│             Perkotaan                │
│ Provinsi:   Jawa Barat              │
│ Kabupaten:  Kabupaten Bogor          │
│ Kecamatan:  Kecamatan Caringin      │
│ Dasar Hukum: Perda Kab. Bogor       │
│              No.1 Tahun 2024         │
│ Catatan:    KEK Lido                 │
│                                      │
│ ⚠️ Data merujuk pada GISTARU        │
│    ATR/BPN - bersifat informatif     │
└──────────────────────────────────────┘
```

#### 3.4 Disclaimer
Teks disclaimer wajib:
> "Data zona tata ruang bersumber dari GISTARU (gistaru.atrbpn.go.id) Direktorat Jenderal Tata Ruang, Kementerian ATR/BPN. Data bersifat informatif dan 'sebagaimana adanya' (as is). Untuk kepastian hukum, silakan merujuk pada produk hukum (Perda) yang berlaku."

---

### FASE 4: Penyimpanan & Ekspor Zona (Enrichment)
> **Tujuan**: Menyimpan informasi zona ke dalam file SHP dan database

#### 4.1 Tambah Atribut Zona ke SHP
Saat generate SHP, tambahkan field zona ke file .dbf:
- `ZONA_RTRW`: Nama zona utama
- `ZONA_PROV`: Provinsi
- `ZONA_KAB`: Kabupaten/Kota
- `ZONA_KEC`: Kecamatan
- `ZONA_HUKM`: Dasar hukum (Perda)

#### 4.2 Simpan ke Database
Tambah kolom di `shapefile_projects`:
- `rtrw_zona`: Nama zona RTRW
- `rtrw_perda`: Dasar hukum
- `rtrw_remark`: Catatan tambahan

#### 4.3 Lead Enrichment
Data zona bisa digunakan untuk:
- Auto-categorize leads berdasarkan jenis zona
- Prioritize leads di zona yang relevan dengan layanan Bizmark

---

## Urutan Implementasi (Prioritas)

| Prioritas | Fase | Deliverable | Estimasi Kompleksitas |
|-----------|------|-------------|----------------------|
| **P0** | 1.1-1.3 | Proxy API + Config mapping | Medium |
| **P0** | 3.1, 3.3 | Zona query centroid + Info panel | Medium |
| **P1** | 2.1-2.2 | Overlay peta RTRW | Medium-High |
| **P1** | 1.4 | Caching layer | Low-Medium |
| **P1** | 3.4 | Disclaimer | Low |
| **P2** | 2.3-2.4 | Opacity slider + Legenda | Low |
| **P2** | 3.2 | Multi-point query | Medium |
| **P3** | 4.1-4.3 | SHP enrichment + Database | Medium |

## Risiko & Mitigasi

| Risiko | Dampak | Mitigasi |
|--------|--------|----------|
| GISTARU proxy down/berubah | Fitur overlay & zona tidak berfungsi | Graceful degradation, cache fallback, monitoring |
| Data RTRW tidak lengkap (beberapa daerah kosong) | User tidak mendapat info zona | Tampilkan pesan "Data belum tersedia untuk wilayah ini" |
| CORS blocking dari proxy GISTARU | Request gagal dari browser | Server-side proxy di Laravel (sudah direncanakan) |
| Rate limiting dari GISTARU | Akses dibatasi | Aggressive caching + rate limiting di sisi kita |
| Data RTRW outdated | Info zona tidak akurat | Disclaimer + tampilkan tahun/nomor Perda |
| Mapping kode wilayah tidak lengkap | Query gagal untuk daerah tertentu | Auto-discovery layer via MapServer API |

## Catatan Teknis

### CORS & Proxy Flow
```
Browser → Laravel Proxy → GISTARU Proxy → GISTARU ArcGIS
         /api/rtrw/*     proxy_rtronline/    arcgis/rest/services/
```

### Kode Provinsi yang Sudah Terverifikasi
| Kode | Provinsi | Service Path |
|------|----------|-------------|
| 11 | Aceh | 001_..._ACEH/_1100_ACEH_PR_PERDA |
| 32 | Jawa Barat | 021_..._JAWA_BARAT/_3200_JAWA_BARAT_PR_PERDA |
| 33 | Jawa Tengah | 022_..._JAWA_TENGAH/_3300_JAWA_TENGAH_PR_PERDA |
| 35 | Jawa Timur | 024_..._JAWA_TIMUR/_3500_JAWA_TIMUR_PR_PERDA |
| 36 | Banten | 025_..._BANTEN/_3600_BANTEN_PR_PERDA |

*Kode service lain perlu di-discover melalui GISTARU API*

### Library yang Dibutuhkan
- **esri-leaflet** (CDN): Plugin Leaflet untuk ArcGIS services — atau custom TileLayer
- Tidak ada dependency backend tambahan (Laravel HTTP client sudah cukup)
