# SmartFinder v3.0 — Tema Light
Sistem Manajemen Barang Temuan Kampus | PHP + MySQL

## Fitur Lengkap
- 🔍 Pencarian & filter barang (publik)
- 🛡️ Verifikasi kepemilikan OOP (Elektronik/Dokumen/Umum)
- 📊 Dashboard statistik real-time
- 📦 Manajemen barang (CRUD + upload foto)
- 📋 Kelola klaim (approve/reject)
- 👥 Manajemen user & role (admin/petugas)
- 📈 Laporan bulanan + Ekspor CSV & PDF (print)
- 🕐 Log aktivitas petugas
- 👤 Profil & ganti password

## Instalasi
1. Import `smartfinder.sql` ke phpMyAdmin
2. Copy folder `sf2/` ke `htdocs/smartfinder/` (XAMPP)
3. Sesuaikan `koneksi.php` jika perlu
4. Buka `http://localhost/smartfinder/`

## Login Default
| Username | Password | Role  |
|----------|----------|-------|
| admin    | admin123 | Admin |

> Jika login gagal, jalankan di phpMyAdmin:
> ```sql
> UPDATE users SET password='$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' WHERE username='admin';
> ```

## Struktur File
| File | Keterangan |
|------|-----------|
| `index.php` | Beranda publik (hero + kartu barang) |
| `klaim.php` | Form verifikasi publik |
| `proses.php` | Backend validasi OOP |
| `hasil.php` | Halaman hasil verifikasi |
| `login.php` | Login petugas |
| `logout.php` | Logout |
| `dashboard.php` | Dashboard + statistik |
| `tambah_barang.php` | Input barang + upload foto |
| `kelola_barang.php` | CRUD barang |
| `edit_barang.php` | Edit barang |
| `kelola_klaim.php` | Manajemen klaim |
| `kelola_user.php` | Manajemen user (admin only) |
| `laporan.php` | Laporan bulanan |
| `export.php` | Ekspor CSV & PDF |
| `activity_log.php` | Log aktivitas (admin only) |
| `profil.php` | Profil & ganti password |
| `barang.php` | OOP: abstract + polymorphism |
| `koneksi.php` | Koneksi DB + helper |
| `layout.php` | Template UI light theme |
| `smartfinder.sql` | Database (8 tabel) |
