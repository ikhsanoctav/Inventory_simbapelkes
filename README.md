<p align="center">
  <img src="public/assets/img/icons/simba.png" width="200" alt="Logo Simba">
</p>

# Inventory Simba Pelkes

Sistem Informasi Manajemen Barang (Inventory Simba) adalah sebuah platform berbasis web yang dirancang untuk mempermudah pemantauan, pelacakan, dan pengelolaan stok barang. Sistem ini mengintegrasikan seluruh alur pengelolaan inventaris mulai dari pencatatan barang masuk, barang keluar, barang reject (rusak), hingga melayani sistem permintaan (request) dari internal maupun eksternal.

## 🚀 Teknologi (Framework)

Sistem ini dibangun menggunakan ekosistem pengembangan modern untuk memastikan kecepatan, keamanan, dan User Experience (UX) yang optimal:

- **Backend:** [Laravel 12.0](https://laravel.com/) (PHP Framework)
- **Frontend / UI:** [Tailwind CSS](https://tailwindcss.com/) & [Alpine.js](https://alpinejs.dev/)
- **Asset Bundler:** [Vite](https://vitejs.dev/)
- **Database:** MySQL
- **Other Packages:**
  - `barryvdh/laravel-dompdf` (Pembuatan & Export PDF)
  - `maatwebsite/excel` (Import & Export Excel)
  - `milon/barcode` (Pencetakan Barcode)

## 👥 Hak Akses (Role) dan Perannya

Sistem ini menggunakan *Multi-role Authentication* untuk menjaga alur kerja (workflow) agar lebih terstruktur. Terdapat empat peran utama di dalam aplikasi:

1. **Super Admin**
   Memiliki hak akses tertinggi dalam sistem. Berperan dalam mengelola seluruh **Master Data**, yang meliputi:
   - Manajemen User (Pegawai, Admin, Tamu).
   - Mengelola Data Barang, Kategori, Satuan (Unit), dan Supplier.
   - Manajemen Kop Surat untuk keperluan cetak laporan.

2. **Admin**
   Berperan sebagai operator yang mengelola operasional / transaksi harian sistem, antara lain:
   - Pencatatan **Barang Masuk** dan **Barang Keluar**.
   - Manajemen **Barang Rusak** (Reject).
   - Memvalidasi dan menyetujui (Approve/Reject) Permintaan Barang dari Pegawai dan Tamu.
   - Export Data dan Laporan ke format Excel maupun PDF (termasuk scan/cetak barcode).

3. **Pegawai (Employee)**
   Berperan sebagai user internal yang dapat melakukan:
   - Melihat katalog produk / stok barang yang tersedia.
   - Membuat pengajuan permintaan barang (Request Item).
   - Memonitor status pengajuan (Pending, Approved, Rejected) dan histori permintaan.

4. **Tamu (Guest / Visitor)**
   Berperan sebagai entitas pihak eksternal yang diizinkan untuk mengajukan *request* barang tertentu dengan alur persetujuan (approval) tersendiri dari pihak Admin.

## 📦 Fitur Utama

- **Master Data Management:** Pengelolaan produk, kategori, unit, supplier, dan data user.
- **Transaction & Inventory Tracking:** Lacak riwayat masuk, keluar, serta inventaris cacat secara akurat (dilengkapi dengan *history* dan *stok real-time*).
- **Request System (Keranjang Permintaan):** Pegawai dan tamu dapat memilih barang menggunakan sistem layaknya _cart_ untuk diajukan ke admin.
- **Reporting & Export:** Cetak dokumen laporan secara berkala atau spesifik per transaksi ke format PDF atau Excel. Termasuk pembuatan struk barang keluar.

## 📸 Tampilan Aplikasi

### Halaman Login
![Login Page](public/assets/img/backgrounds/Login.png)

## 🛠 Panduan Instalasi (Development)

Untuk menjalankan proyek ini di mesin lokal, ikuti langkah-langkah berikut:

1. **Clone Repositori**
   ```bash
   git clone https://github.com/ikhsanoctav/Inventory_simbapelkes.git
   cd Inventory_simbapelkes
   ```

2. **Install Dependensi Backend & Frontend**
   ```bash
   composer install
   npm install
   ```

3. **Setup Environment**
   Salin `.env.example` ke `.env` lalu konfigurasikan akses ke Database Anda.
   ```bash
   cp .env.example .env
   ```
   Lalu *generate* *Application Key*:
   ```bash
   php artisan key:generate
   ```

4. **Migrate & Seed Database**
   Menyiapkan skema tabel beserta akun _dummy_ bawaan (termasuk role).
   ```bash
   php artisan migrate --seed
   ```

5. **Jalankan Development Server**
   Buka dua jendela terminal untuk menjalankan Vite dan server PHP secara bersamaan:
   ```bash
   # Terminal 1: Vite (Build Asset Frontend)
   npm run dev
   
   # Terminal 2: Laravel Server
   php artisan serve
   ```
   Akses aplikasi pada `http://localhost:8000`.
