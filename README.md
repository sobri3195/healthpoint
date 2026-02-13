# HealthPoint Premium

HealthPoint Premium adalah platform manajemen peta interaktif berbasis PHP + MySQL untuk kebutuhan direktori lokasi, marker detail, cerita (story mode), filter kategori, review, dan publikasi peta.

## Author
**dr. Muhammad Sobri Maulana**

## Status Versi
Versi ini disiapkan sebagai **Premium build** dengan aktivasi lisensi internal, branding premium, dan dokumentasi teknis yang lebih lengkap.

## Analisis Arsitektur Kode (Mendalam)

### 1) Lapisan Sistem
- `backend/` → panel admin (manajemen map, marker, kategori, settings, updater).
- `viewer/` → front-end peta publik (OpenLayers, UI publikasi, story, pencarian).
- `services/` → service utilitas (thumbnail, update checker, helper proses).
- `install/` → wizard instalasi, validasi requirement, setup database.
- `db/` + `install/sql_dump/` → koneksi dan struktur database awal.

### 2) Alur Login & Session
- Login dimulai dari `backend/login.php`, lalu kontrol aplikasi utama ada di `backend/index.php`.
- Session dipakai untuk `id_user`, pilihan map aktif, bahasa, dan state UI backend.
- Build premium ini menonaktifkan pemaksaan input lisensi sebelum mengakses backend agar workflow admin tidak terblokir.

### 3) Data Layer
Struktur inti berada di tabel:
- `sml_settings` (global app settings)
- `sml_maps` (konfigurasi peta)
- `sml_markers` (titik lokasi + detail)
- `sml_categories` dan `sml_markers_categories_assoc` (taksonomi)
- `sml_images`, `sml_reviews`, `sml_story`, `sml_markers_connects`

### 4) Fitur Inti Produk
- Multi-map dan multi-marker.
- Kategori tunggal/majemuk + filter.
- Popup detail marker + gallery gambar.
- Story mode naratif.
- Import/export data marker.
- Role user admin/customer.
- Dukungan multi-bahasa backend/viewer.
- Tema, font, dan custom style.

### 5) Peningkatan Premium pada Update Ini
- Branding default menjadi **HealthPoint Premium**.
- Badge Premium ditambahkan di footer backend.
- Alur validasi lisensi updater disederhanakan untuk environment premium self-managed.
- Metadata author diseragamkan ke **dr. Muhammad Sobri Maulana**.
- Lisensi proyek ditetapkan jelas dengan file `LICENSE`.

---

## Instalasi Cepat
1. Import SQL dari `install/sql_dump/create.sql`.
2. Atur koneksi database di `config/config.inc.php` (setelah wizard atau manual).
3. Jalankan installer: `http://your-host/install/start.php`.
4. Login admin via `backend/login.php`.

## Rekomendasi Produksi
- Gunakan PHP 7.4+ atau 8.x yang kompatibel dengan ekstensi: `mysqli`, `gd`, `zip`, `mbstring`.
- Aktifkan HTTPS.
- Batasi izin folder upload dan lakukan backup terjadwal DB + assets.

## Catatan Lisensi
- Lisensi utama proyek: MIT (lihat file `LICENSE`).
- Beberapa dependensi pihak ketiga tetap mengikuti lisensi masing-masing.
