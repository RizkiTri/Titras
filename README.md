# Fania — CLI Historical Dialog Game

Ringkasan
--------
Proyek ini adalah engine CLI sederhana untuk memainkan dialog interaktif berbasis file JSON. UI CLI terintegrasi ke dalam perintah `game:start` dan juga ada skrip runner `play_dialog.php` untuk menjalankan dialog langsung.

Persyaratan
---------
- PHP 8.0+ (disarankan 8.1 atau 8.2)
- Composer (untuk dependencies pada `vendor/`)

Instalasi
--------
1. Buka terminal (PowerShell di Windows) dan install melalui source code:

```powershell
git clone https://github.com/RizkiTri/Titras.git
```

2. Install dependency (jika belum ada `vendor/`):

```powershell
composer install
```

3. Pastikan file game JSON sudah ada di `Database/Game/diponegoro_s.json` (contoh file sudah disertakan di repo).

Menjalankan game 
---------------------------

Proyek memiliki `StartCommand` yang mengintegrasikan engine dialog. Cara memanggilnya bergantung pada CLI/runner internal proyek. Jika ada binary/entrypoint, gunakan (misal `php index.php game:start`), atau jalankan mekanisme CLI yang sudah tersedia dalam proyek.

```powershell
php index.php game:start
```

Lalu ketika tampilan awal muncul ketik `loaddata` untuk melanjutkan.

Cara bermain singkat
--------------------
- Sistem menampilkan teks narator/speaker dan daftar pilihan bernomor.
- Ketik nomor pilihan lalu Enter untuk memilih.
- Pada node tanpa pilihan, tekan Enter untuk melanjutkan.
- Perintah khusus selama permainan:
  - `status` — lihat statistik/efek pemain
  - `help` — bantuan singkat
  - `quit` — keluar permainan dan menyimpan state

Penyimpanan / Lanjutkan
----------------------
- Saat Anda memilih profile melalui `game:start`, state permainan (statistik sederhana) akan disimpan di object profile (`gameState`) saat Anda keluar.
- Memuat profile yang sama akan memulihkan state terakhir.

