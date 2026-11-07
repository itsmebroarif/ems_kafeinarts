<p align="center"><a href="https://github.com/itsmebroarif/ems_kafeinarts" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
    <a href="https://github.com/itsmebroarif/ems_kafeinarts/actions"><img src="https://github.com/itsmebroarif/ems_kafeinarts/workflows/tests/badge.svg" alt="Build Status"></a>
    <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

<h1 align="center">Kafeinarts EMS — Event Management System</h1>

<p align="center"><strong>Platform manajemen event digital yang lengkap, modern, dan mudah digunakan.</strong></p>
<p align="center">Dibuat dengan Laravel 13, Bootstrap 5, dan MySQL (development) / SQLite (testing).</p>

---

## 📖 Tentang Aplikasi

Kafeinarts EMS adalah sistem manajemen event yang membantu **event organizer, komunitas, dan institusi pendidikan** untuk mengelola seluruh alur event, mulai dari pembuatan event, pendaftaran peserta, pembayaran, hingga pencetakan tiket dan surat checkout.

Aplikasi ini dirancang agar:
- **Cepat** — data event langsung tampil tanpa menunggu refresh penuh.
- **Intuitif** — panel admin dengan sidebar, wizard 4 langkah, dan tab navigasi.
- **Skalable** — mendukung banyak kategori, tier tiket, add-ons, dan voucher.
- **Responsif** — tampilan optimal di desktop dan mobile (offcanvas slide-in).

---

## ✨ Fitur yang Sudah Ada

### 🌐 Publik & Pendaftaran
- **Daftar Event** dengan filter bertingkat (Kategori, Jenis Event, Lokasi, Harga, Tanggal).
- **Pencarian** nama/lokasi event secara real-time.
- **Filter Kategori** — sidebar dengan jumlah event per kategori.
- **Filter Jenis Event** — Kompetisi / Event Hiburan.
- **Filter Lokasi** — daftar kota yang sedang memiliki event.
- **Filter Harga** — gratis / berbayar.
- **Filter Tanggal** — hari ini, minggu ini, bulan ini, mendatang.
- **Detail Event** — nama, avatar & banner otomatis (dengan fallback SVG), lokasi, provinsi, kota, tanggal, deskripsi.
- **Avatar & Banner Upload** — upload JPG/PNG/WEBP atau gunakan fallback SVG deterministik berdasarkan nama event (tanpa gambar default yang sama).
- **Pendaftaran Event** — wizard 3 langkah (Data Diri → Tiket & Add-on → Konfirmasi) dengan preview banner event.
- **Tier Tiket** — Regular, VIP, Early Bird, Student, Premium.
- **Add-ons** — merchandise dengan pilihan switcher (maks. 1 unit per pembeli).
- **Voucher Diskon** — kode voucher (persen/nominal) dengan batas penggunaan.
- **Ringkasan Pembayaran** — simulasi checkout dengan perhitungan subtotal, add-ons, diskon, dan fee.
- **Tiket Cetak (A4)** — halaman print siap cetak dengan QR Code.
- **Surat Checkout Event** — dokumen resmi untuk diunduh sebagai HTML atau PDF (Dompdf).
- **Dark Mode / Light Mode** — toggle tema yang tersimpan di `localStorage`.

### 🛠 Admin & Manajemen Event
- **Sidebar Admin** — navigasi mudah antar dashboard, event, papan, tutorial, dan manajemen master data.
- **Dashboard Admin** — statistik event, pendaftar, dan kategori.
- **Papan Kanban** — 4 kolom: Event Terdaftar, Event Akan Dimulai, Event Ditutup, dan **Result** (khusus sport/esport).
- **Wizard Buat Event** — 4 langkah:
  1. Info Dasar (nama, slug, kategori dengan indikator ⚡ Sport, jenis event)
  2. Lokasi & Waktu (Provinsi + Kota auto-complete, event 1–2 hari, time picker)
  3. Biaya & Kuota (harga, fee, max quota)
  4. Media & Publikasi (avatar, banner, is_published)
- **Edit Event** — mode tab dengan free navigation (tidak harus urut langkah).
- **Kategori Event** — Sport/Esport vs Non-Sport, inline creation, warna badge kustom.
- **Tier Tiket** — kelola tier per event.
- **Add-ons** — produk tambahan dengan stok dan harga.
- **Voucher** — manajemen kode diskon.
- **Pendaftar** — daftar seluruh peserta per event.
- **Export/Import Master Data** — CSV/Excel (Tier, Add-ons, Voucher, Pendaftar, dan Result event).
- **Tutorial Admin** — halaman panduan lengkap untuk setiap menu.
- **Add Result** — form khusus sport/esport untuk mengisi skor, pemenang, MVP.
- **Cetak Tiket & Surat Checkout** — dari halaman manajemen peserta.
- **Dummy Events Seeder** — generate 30 event + peserta untuk demo.

### 🎨 UI/UX
- Bootstrap 5 + custom CSS dengan shadow dan border radius.
- Hover transition, gradient, dan focus states yang konsisten.
- Dark mode support (`data-bs-theme`).
- Mobile-first dengan offcanvas slide-in.
- Pagination Vertikal (Showing text di atas, nomor di bawah).
- Category dot & badge colors.

### 🔧 Engineering & Quality
- **54 tests / 242 assertions** — feature tests untuk registrasi, board, PDF, import/export.
- **Pint** — format konsisten.
- **View cache** — blade templates di-cache untuk performa.
- **SQLite** untuk testing, **MySQL** untuk development.
- **Vite/Laravel Boost** — full-stack dengan AI-assisted development.

---

## 🚀 Teknologi

| Kategori | Teknologi |
|----------|-----------|
| Framework | Laravel 13 |
| Bahasa | PHP 8.5 |
| Frontend | Bootstrap 5.3 + Blade |
| Database | MySQL 8 (dev) / SQLite (test) |
| Icon | Bootstrap Icons |
| PDF | Dompdf v3 |
| Testing | PHPUnit + Laravel Testing Kit |
| Dev Tools | Laravel Boost, Pint, Vite |

---

## 🛠 Instalasi & Menjalankan

### Prasyarat
- PHP >= 8.2
- Composer
- MySQL / MariaDB (atau SQLite untuk testing)
- Node.js & npm (jika ingin build asset)

### Langkah-langkah
```bash
# Clone repository
git clone https://github.com/itsmebroarif/ems_kafeinarts.git
cd ems_kafeinarts

# Install dependensi PHP
composer install

# Install dependensi JavaScript (opsional)
npm install

# Salin .env dan generate key
copy .env.example .env   # Windows
# atau
cp .env.example .env     # Linux/Mac
php artisan key:generate

# Konfigurasi database di .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=db_ems_kafeinarts
# DB_USERNAME=root
# DB_PASSWORD=

# Migrasi & seeder
php artisan migrate --force
php artisan db:seed --force

# Jika ingin dummy data untuk demo
php artisan db:seed --class=DummyEventsSeeder --force

# Jalankan aplikasi
php artisan serve
```

Buka 👉 `http://127.0.0.1:8000`

### Seed Categories (Starter)
```bash
php artisan db:seed --class=EventCategorySeeder --force
```

---

## 🧩 Struktur Proyek

```
app/
├── Enums/              # EventBoardStatus, EventType
├── Http/Controllers/
│   ├── Admin/          # Dashboard, Event, Tier, Addon, Voucher, Registrant, MasterData
│   └── Public/         # EventController, ParticipantController
├── Models/             # Event, TicketTier, Addon, Voucher, Registrant, Category, dll.
├── Repositories/       # EventRepository, VoucherRepository, dll.
└── Services/           # CalculationService, TicketGenerationService, MasterDataImportExportService

resources/views/
├── admin/              # Dashboard, events (wizard/tabs/board/result), tutorial
├── public/events/      # Index, Show, Stage (registration wizard), dll.
├── public/participants/# Ticket, Checkout Letter, Print
└── vendor/pagination/  # Custom Bootstrap 5 pagination
```

---

## 📝 Cara Penggunaan

### Admin
1. Login ke `/admin/dashboard`
2. Buat event melalui **Wizard 4 Langkah** (atau tab di halaman edit)
3. Kelola **Tier Tiket**, **Add-ons**, **Voucher** di setiap event
4. Pantau peserta di **Papan Kanban** (Event Terdaftar → Akan Dimulai → Ditutup → Result)
5. Export/Import data master jika perlu

### Publik
1. Buka `/events`
2. Gunakan filter kiri untuk jenis, lokasi, harga, atau tanggal
3. Klik event → pilih tier → isi data diri → pilih add-ons (switcher) → konfirmasi
4. Buka tiket atau cetak **Surat Checkout Event**

---

## 🗺 Future Plans (Apa yang akan datang?)

Berikut rencana fitur yang ingin dikembangkan agar aplikasi ini jauh lebih lengkap:

### 💳 Pembayaran & Monetisasi
- **Payment Gateway Real**: Integrasi Midtrans / Xendit / DOKU untuk pembayaran otomatis.
- **QRIS & Virtual Account**: Dukungan metode pembayaran bank dan e-wallet Indonesia.
- **Invoice & Receipt PDF**: Generate invoice otomatis dan kirim via email.
- **Commission & Fee Management**: Pengaturan fee platform dan pembayar service secara dinamis.

### 📱 Scan & Check-in
- **QR Scanner / Barcode Check-in**: Halaman scan untuk verifikasi tiket peserta di pintu masuk.
- **Offline Mode**: Izinkan scan tanpa koneksi dengan sync data saat online.
- **Real-time Counter**: Dashboard check-in yang update langsung saat tiket di-scan.

### 📊 Reporting & Analytics
- **Laporan Penjualan**: Total revenue per event, tier, dan periode.
- **Demographic Report**: Jenis kelamin, usia, kota peserta.
- **Export PDF/Excel**: Laporan lengkap dengan grafik dan visualisasi.
- **Attendance Report**: Daftar peserta yang hadir vs belum hadir.

### 🎟 Penjualan & Promosi
- **Early Bird / Flash Sale**: Tier harga spesial dengan countdown timer.
- **Group Booking**: Diskon otomatis untuk pembelian lebih dari N tiket.
- **Affiliate / Referral System**: Kode referral untuk peserta yang mengajak teman.
- **Wishlist & Bookmark**: Pengguna bisa menyimpan event favorit.
- **Event Reminder**: Notifikasi email/SMS 7 hari, 3 hari, dan 1 hari sebelum event.

### 🏆 Result & Kompetisi
- **Leaderboard / Klasemen**: Tampilan peringkat untuk kompetisi sport/esport.
- **Score Input Grid**: Input skor cepat dengan validasi otomatis.
- **Result Export**: Generate sertifikat pemenang otomatis.
- **Judge Panel**: Panel juri untuk penilaian estetika/seni.

### 🌍 Multi-Bahasa & Aksesibilitas
- **i18n**: Dukungan bahasa selain Indonesia dan Inggris.
- **Accessibility (A11y)**: Aria labels, keyboard navigation, high contrast mode.
- **WCAG 2.1 Compliance**: Aksesibilitas sesuai standar internasional.

### 🎨 Branding & Customization
- **Custom Domain**: Support white-label / custom domain untuk event organizer besar.
- **Logo & Branding per Event**: Warna tema dan logo khusus per event.
- **Email Template Builder**: Template email dengan variabel {{event_name}}, {{participant_name}}, dll.
- **WhatsApp Integration**: Broadcast pengingat via WhatsApp API.

### 🛠 Engineering & DX
- **API & Mobile App**: REST API + React Native / Flutter companion app.
- **Webhook**: Integrasi dengan sistem CRM/ERP pihak ketiga.
- **Unit Test Coverage**: Target minimal 80% coverage.
- **Docker / Laravel Sail**: Containerization untuk environment yang konsisten.
- **CI/CD Pipeline**: GitHub Actions untuk automated testing dan deploy.

### 🔒 Keamanan & Compliance
- **Role & Permission**: Admin, Event Manager, Staff, Viewer.
- **Audit Log**: Catatan semua perubahan data (siapa, kapan, apa).
- **Rate Limiting**: Proteksi terhadap brute force login / spam pendaftaran.
- **GDPR Compliance**: Export & hapus data pribadi peserta.

---

## 🤝 Kontribusi

Kami sangat terbuka terhadap kontribusi dari komunitas. Jika Anda ingin:
- Menambahkan fitur baru
- Memperbaiki bug
- Menulis dokumentasi
- Meningkatkan UI/UX

Silakan buka **Issue** atau kirim **Pull Request**. Pastikan menjalankan `php artisan test --compact` dan `vendor/bin/pint` sebelum submit.

---

## 📝 Lisensi

MIT License.

---

<p align="center">Dibuat dengan ❤️ oleh <strong>Arif</strong> dan komunitas <a href="https://github.com/itsmebroarif/ems_kafeinarts">Kafeinarts EMS</a></p>
