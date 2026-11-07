# Product Requirement Document (PRD)

| Metadata     | Detail                                   |
| :----------- | :--------------------------------------- |
| **Project**  | Event Management System (Kafeinarts)     |
| **Developer**| Arif Permana Putrasuryana                |
| **Date**     | 6 November 2026                          |
| **Status**   | Draft / Ready for Prompting              |

---

## 1. Project Overview
Aplikasi **Event Management & Ticketing System** yang fleksibel dan modular untuk berbagai jenis *event organizer* (Konser, Event Cosplay, Marathon, Workshop, dsb.). Sistem mencakup alur pendaftaran peserta berbasis *wizard*, pengelolaan event & tiket dari sisi Admin, serta sistem generate tiket/surat konfirmasi dalam format PDF.

---

## 2. Tech Stack & Architecture Rules

### 2.1 Tech Stack
- **Frontend Framework / UI**: Bootstrap 5 (Clean, Responsive, Standard UI)
- **State Management**: Form Wizard State Management (persistensi data antar-step pendaftaran)
- **PDF Generator**: Engine penilai PDF untuk e-ticket & konfirmasi (misal: Dompdf / Snappy / TCPDF)

### 2.2 Architectural Rules
1. **Modular & Clean Architecture**: Pemisahan struktur kode yang terorganisasi dan scalable.
2. **Separation of Concerns**:
   - **Controller**: Hanya menangani request/response processing dan routing.
   - **Service Layer**: Menampung seluruh *business logic* dan manipulasi data.
3. **State Management**: Form pendaftaran wajib mempertahankan state data jika pengguna berpindah step (*prev/next*) atau me-refresh halaman sebelum submit akhir.

---

## 3. Key Features & Functional Requirements

### 3.1 Public / Participant Module
* **Multi-step Registration Wizard**:
  * Step-by-step form pendaftaran peserta yang intuitif.
  * Form kustom dinamis (input teks, radio, checkbox addon/merchandise).
  * Pemilihan tier tiket / pricelist.
  * Input kode voucher diskon.
* **Payment & Checkout**:
  * Kalkulasi otomatis total harga (Tiket + Addons - Diskon Voucher) dalam bentuk simulasi dan siap di integrasikan ke payment gateway.
  * Konfirmasi pendaftaran & penyimpanan ke database.
* **E-Ticket & Document Generation**:
  * Auto-generate PDF E-Ticket / Surat Konfirmasi dengan QR Code/Barcode unik untuk check-in panitia.

### 3.2 Admin Dashboard & Management Module
* **Interactive Dashboard Analytics**:
  * Widget statistik jumlah pendaftar total & per-event.
  * Ringkasan total pendapatan (*revenue analytics*).
* **Event & Ticket Form Builder**:
  * CRUD Event (Nama, Tanggal, Lokasi, Deskripsi).
  * Pengaturan Pricelist & Tier Tiket (misal: Early Bird, VIP, Regular).
  * Custom Form Fields & Addons (misal: ukuran baju, merchandise, opsi konsumsi) beserta manajemen kuota/stok item.
* **Voucher & Discount Management**:
  * Pembuatan kode voucher, besaran diskon (persentase / nominal), kuota penggunaan, dan tanggal kedaluwarsa.
* **Participant & Inventory Tracking**:
  * Daftar pendaftar real-time beserta status pembayaran dan pilihan addon.
  * Pengurangan stok otomatis untuk item berkategori terbatas.

---

## 4. Goals & Expected Deliverables
1. **User Experience**: Alur pendaftaran peserta yang mulus (*seamless*) tanpa kehilangan data input saat pengisian wizard.
2. **Admin Control**: Kemudahan admin dalam membuat event baru beserta variasi tiket, form kustom, dan item merchandise secara terpusat.
3. **Automated Ticketing**: Hasil cetak PDF e-ticket yang valid dan siap diverifikasi oleh panitia di lokasi acara.
4. **Clean Code**: Struktur repositori modular berbasis Service-Controller pattern yang mudah dirawat dan dikembangkan lebih lanjut.
4. **Multiple Language**: Aplikasi harus mampu menggunakan dua atau lebih bahasa karena nantinya klien tidak hanya berasal dari Indonesia, melainkan ada klien dari luar negeri juga