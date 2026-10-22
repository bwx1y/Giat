# GIAT (Graha Informasi Aktivitas Terintegrasi) 🚀

Sistem Informasi Keaktifan Organisasi Mahasiswa Berbasis Web untuk memantau, mengelola, dan meningkatkan partisipasi anggota secara transparan, akuntabel, dan terintegrasi.

---

## 📌 Latar Belakang

Banyak program kerja di organisasi kemahasiswaan tidak berjalan secara optimal akibat minimnya partisipasi anggota dan sulitnya pemantauan keaktifan secara konsisten oleh pengurus. GIAT hadir sebagai solusi digital berbasis web yang mengubah proses pemantauan manual menjadi sistem otomatis berbasis data (*data-driven decision making*).

---

## ✨ Fitur Utama

- 📱 **Presensi QR Code Otomatis**: Pencatatan kehadiran rapat dan kegiatan secara cepat, presisi, dan terverifikasi.
- 🛠️ **Presensi Manual Admin**: Mekanisme cadangan (*fallback*) bagi pengurus untuk menangani kendala teknis perangkat di lapangan.
- 🎮 **Gamifikasi & Sistem Poin**: Mendorong budaya aktif melalui kalkulasi poin otomatis:
  - `+3` poin untuk setiap kehadiran rapat/kegiatan.
  - `-1` poin untuk setiap ketidakhadiran (absensi).
- 🏆 **Papan Leaderboard**: Visualisasi peringkat keaktifan anggota secara *real-time* untuk membangun kompetisi yang sehat.
- 📜 **Integrasi Syarat Sertifikat**: Akumulasi poin dijadikan syarat utama klaim sertifikat kepengurusan di akhir periode.
- 📊 **Dashboard Manajerial**: Panel kontrol terpusat bagi pengurus untuk menganalisis keaktifan anggota dan mengambil keputusan berbasis data valid.

---

## 🛠️ Stack Teknologi

- **Frontend**: HTML5, CSS3, JavaScript (Bootstrap)
- **Backend**: PHP (Native)
- **Database**: PostgreSQL (via PDO `pdo_pgsql`)
- **Tools & Libraries**: HTML5-QRCode Scanner, Chart.js

---

## 🚀 Panduan Menjalankan Proyek

### 1. Prasyarat
- PHP 8.x dengan ekstensi `pdo_pgsql` aktif
- PostgreSQL Server

### 2. Konfigurasi Environment
Salin file konfigurasi `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```
Sesuaikan parameter database di dalam file `.env`:
```env
DB_HOST=127.0.0.1
DB_PORT=5432
DB_NAME=pbl_db
DB_USER=postgres
DB_PASS=password_anda
```

### 3. Migrasi Database
Jalankan migrasi untuk membuat tabel dan struktur skema:
```bash
php migration up
# atau jika file memiliki izin eksekusi
./migration up
```

Perintah migrasi lainnya:
```bash
php migration down            # Rollback 1 migrasi terakhir
php migration create <nama>   # Buat file template migrasi baru
php migration seed            # Isi dummy data (20 data per tabel)
```

### 4. Menjalankan Server Lokal
Jalankan built-in web server PHP:
```bash
php -S localhost:8080 -t public/
```
Akses aplikasi melalui browser di `http://localhost:8080`.

### 5. Pengecekan Sintaks PHP
```bash
find . -name "*.php" -not -path "*/.*" -exec php -l {} +
```

---

## 📝 Format Commit & Alur Kerja Git

Repository ini terhubung dengan Notion Task Status Sync melalui GitHub Actions. Setiap pesan commit **wajib** menyertakan Task ID di bagian depan:

```bash
git commit -m "<task_id> - <pesan>"
# Contoh: git commit -m "16 - Update data"
```
Setiap *push* ke remote branch akan otomatis mengubah status task di Notion menjadi **In progress**.
