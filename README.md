# GIAT (Graha Informasi Aktivitas Terintegrasi)

Sistem Informasi Keaktifan Organisasi Mahasiswa Berbasis Web untuk memantau, mengelola, dan meningkatkan partisipasi anggota secara transparan, akuntabel, dan terintegrasi.

## Latar Belakang

Banyak program kerja di organisasi kemahasiswaan tidak berjalan optimal akibat minimnya partisipasi anggota dan sulitnya pemantauan keaktifan secara konsisten. GIAT hadir sebagai solusi digital berbasis web untuk mentransformasi presensi manual menjadi sistem otomatis berbasis data (*data-driven decision making*).

## Fitur Utama

- **Presensi QR Code Otomatis**: Pencatatan kehadiran rapat dan kegiatan secara cepat, presisi, dan terverifikasi.
- **Presensi Manual Admin**: Mekanisme cadangan (*fallback*) penanganan kendala teknis perangkat di lapangan.
- **Gamifikasi & Sistem Poin**:
  - `+3 poin` per kehadiran rapat/kegiatan.
  - `-1 poin` per ketidakhadiran (*absensi*).
- **Papan Leaderboard**: Peringkat keaktifan anggota secara *real-time*.
- **Integrasi Syarat Sertifikat**: Akumulasi poin sebagai prasyarat klaim sertifikat kepengurusan.
- **Dashboard Manajerial**: Panel analitik pengurus untuk pengambilan keputusan.

## Stack Teknologi

- **Backend**: Native PHP 8.5+ (arsitektur micro-framework tanpa dependency eksternal)
- **Database**: PostgreSQL (koneksi via PDO `pdo_pgsql`)
- **Frontend**: HTML5, CSS3, JavaScript (Bootstrap, Chart.js, HTML5-QRCode Scanner)
- **Autentikasi**: JSON Web Token (JWT) HS256

## Prasyarat Sistem

- **PHP 8.5+** (wajib mendukung operator pipa `|>`)
- Ekstensi PHP: `pdo`, `pdo_pgsql`
- Server PostgreSQL aktif

## Panduan Instalasi & Menjalankan Server

1. **Clone repository**:
   ```bash
   git clone <repo_url>
   cd Giat
   ```

2. **Konfigurasi Environment**:
   Salin file konfigurasi contoh:
   ```bash
   cp .env.example .env
   ```
   Sesuaikan parameter pada `.env`:
   ```env
   JWT_SECRET=rahasia_jwt_anda
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_NAME=pbl_db
   DB_USER=postgres
   DB_PASS=password_anda
   ```

3. **Jalankan Migrasi Database**:
   ```bash
   php migrate up
   ```
   *Perintah migrasi lain:*
   - `php migrate down` : Rollback migrasi terakhir.
   - `php migrate create <nama>` : Buat file migrasi baru.

4. **Jalankan Server**:
   - **PHP CLI Server (Development)**:
     ```bash
     php -S localhost:8080 index.php
     ```
   - **FrankenPHP (Worker Mode / Production)**:
     ```bash
     docker build -t giat .
     docker run -e FRANKENPHP_CONFIG="worker /app/worker.php" -p 8080:8080 giat
     ```

5. **Verifikasi API**:
   Akses root endpoint:
   ```bash
   curl http://localhost:8080/api
   ```
   Output: `{"status":200,"message":"API server is online"}`

## Struktur Endpoint API

Semua rute wajib diawali prefix `/api/`.

| Method | Endpoint | Auth | Deskripsi |
|---|---|---|---|
| `GET` | `/api` | Publik | Cek status server API |
| `POST` | `/api/auth/login` | Publik | Autentikasi user & generate JWT |
| `GET` | `/api/profile` | Bearer Token (`*`) | Data profil user yang sedang login |
| `POST` | `/api/profile` | Bearer Token (`*`) | Perbarui profil user yang sedang login |

## Konvensi Commit & CI/CD

Repository ini terintegrasi dengan GitHub Actions untuk sinkronisasi otomatis status task di Notion. Setiap commit pada branch kerja wajib diawali dengan ID Task:
```bash
git commit -m "<task_id> - <pesan_commit>"
# Contoh:
git commit -m "8 - add profile controller"
```
