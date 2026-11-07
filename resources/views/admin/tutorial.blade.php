@extends('layouts.app')

@section('title', 'Tutorial')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">
            <div class="card border-0 shadow">
                <div class="card-body p-4">
                    <h1 class="h3 fw-bold mb-4">
                        <i class="bi bi-book me-2 text-primary"></i>Tutorial Sistem Manajemen Event
                    </h1>
                    <p class="text-muted">
                        Panduan lengkap penggunaan panel admin. Ikuti langkah-langkah di bawah untuk mengelola event Anda.
                    </p>

                    <hr class="my-4">

                    {{-- Dashboard --}}
                    <h2 class="h5 fw-bold mb-3">
                        <i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard
                    </h2>
                    <p>Halaman utama yang menampilkan ringkasan data:</p>
                    <ul>
                        <li>Jumlah total event yang terdaftar</li>
                        <li>Jumlah pendaftar pada masing-masing event</li>
                        <li>Event yang akan datang (starting soon)</li>
                        <li>Statistik singkat lainnya</li>
                    </ul>
                    <p class="text-muted small">Gunakan dashboard untuk memantau aktivitas event secara real-time.</p>

                    <hr class="my-4">

                    {{-- Event --}}
                    <h2 class="h5 fw-bold mb-3">
                        <i class="bi bi-calendar-event me-2 text-success"></i>Event
                    </h2>
                    <p>Halaman untuk mengelola semua event Anda:</p>
                    <ul>
                        <li><strong>Daftar Event</strong> — melihat semua event yang pernah dibuat</li>
                        <li><strong>Tambah Event</strong> — membuat event baru dengan wizard 4 langkah</li>
                        <li><strong>Edit Event</strong> — memperbarui detail event yang sudah ada</li>
                        <li><strong>Detail Event</strong> — melihat informasi lengkap event</li>
                        <li><strong>Hapus Event</strong> — menghapus event (hanya jika belum ada pendaftar)</li>
                    </ul>
                    <p class="text-muted small">
                        Saat membuat atau mengedit event, Anda akan mengikuti wizard 4 langkah:
                    </p>
                    <ol class="small">
                        <li><strong>Info Dasar</strong> — nama event, slug, kategori (sport/non-sport), jenis event (kompetisi/hiburan)</li>
                        <li><strong>Lokasi & Waktu</strong> — provinsi, kota/kabupaten, detail venue, tanggal mulai/selesai, waktu</li>
                        <li><strong>Biaya & Kuota</strong> — harga dasar, fee admin, submit fee, kuota maksimal</li>
                        <li><strong>Media & Publikasi</strong> — avatar event, banner, apakah tersedia untuk publik</li>
                    </ol>

                    <hr class="my-4">

                    {{-- Papan Event --}}
                    <h2 class="h5 fw-bold mb-3">
                        <i class="bi bi-kanban me-2 text-warning"></i>Papan Event (Kanban)
                    </h2>
                    <p>Tampilan papan gaya kanban untuk mengorganisir status event:</p>
                    <ul>
                        <li><strong>Event Terdaftar</strong> — event yang baru dibuat dan menunggu pelaksanaan</li>
                        <li><strong>Event Akan Dimulai</strong> — event yang mendekati atau sedang berlangsung</li>
                        <li><strong>Event Ditutup</strong> — event yang sudah selesai</li>
                        <li><strong>Result</strong> <span class="text-success fw-bold">(Khusus Sport)</span> — event sport/esport yang sudah memiliki hasil</li>
                    </ul>
                    <p class="text-muted small">
                        Seret dan lepas kartu event untuk memindahkan antar kolom.
                        Kolom <strong>Result</strong> hanya bisa diisi oleh event dengan kategori sport/esport.
                    </p>

                    <hr class="my-4">

                    {{-- Tier Tiket --}}
                    <h2 class="h5 fw-bold mb-3">
                        <i class="bi bi-ticket-detailed me-2 text-info"></i>Tier Tiket
                    </h2>
                    <p>Kelola tier tiket untuk setiap event:</p>
                    <ul>
                        <li>Buat tier seperti Regular, VIP, Early Bird, dll.</li>
                        <li>Atur harga dan fee untuk setiap tier</li>
                        <li>Tentukan kuota (stock_qty) untuk setiap tier</li>
                        <li>Atur apakah tier tersedia untuk dibeli online</li>
                    </ul>
                    <p class="text-muted small">
                        Setiap event bisa memiliki banyak tier. Contoh: Regular (Rp 100.000), VIP (Rp 250.000).
                    </p>

                    <hr class="my-4">

                    {{-- Add-ons --}}
                    <h2 class="h5 fw-bold mb-3">
                        <i class="bi bi-bag-plus me-2 text-danger"></i>Add-on
                    </h2>
                    <p>Kelola add-on (produk tambahan) yang bisa dibeli pengunjung:</p>
                    <ul>
                        <li>Contoh: Kaos, Lanyard, Backpack, Buku, dll.</li>
                        <li>Setiap add-on memiliki batas pembelian (max 1 unit per pembeli)</li>
                        <li>Kelola stok (qty) add-on</li>
                        <li>Import/export data add-on dari/ke Excel</li>
                    </ul>
                    <p class="text-muted small">
                        Add-on ditampilkan sebagai switcher (aktif/nonaktif) pada halaman pendaftaran.
                    </p>

                    <hr class="my-4">

                    {{-- Voucher --}}
                    <h2 class="h5 fw-bold mb-3">
                        <i class="bi bi-tag me-2 text-warning"></i>Voucher
                    </h2>
                    <p>Kelola kode voucher diskon:</p>
                    <ul>
                        <li>Buat voucher dengan kode unik (contoh: HEMAT10, EARLY50)</li>
                        <li>Tentukan jenis diskon: persen (is_percent) atau nominal</li>
                        <li>Atur batas penggunaan (total_uses)</li>
                        <li>Atur tanggal kadaluarsa (expired_at)</li>
                        <li>Import/export voucher dari/ke Excel</li>
                    </ul>
                    <p class="text-muted small">
                        Voucher bisa otomatis diterapkan atau diketik manual oleh pembeli.
                    </p>

                    <hr class="my-4">

                    {{-- Pendaftar --}}
                    <h2 class="h5 fw-bold mb-3">
                        <i class="bi bi-people me-2 text-success"></i>Pendaftar
                    </h2>
                    <p>Lihat dan kelola semua pendaftar event:</p>
                    <ul>
                        <li>Daftar semua peserta yang sudah mendaftar</li>
                        <li>Detail pendaftar: nama, email, no HP, tier, status pembayaran</li>
                        <li>Cetak tiket atau surat checkout event</li>
                        <li>Export data pendaftar ke Excel/CSV</li>
                    </ul>
                    <p class="text-muted small">
                        Status pendaftar: pending, confirmed, rejected, cancelled, paid.
                    </p>

                    <hr class="my-4">

                    {{-- Export/Import --}}
                    <h2 class="h5 fw-bold mb-3">
                        <i class="bi bi-file-earmark-excel me-2 text-primary"></i>Export & Import Master Data
                    </h2>
                    <p>Anda bisa export/import data master ke/dari Excel:</p>
                    <ul>
                        <li><strong>Export</strong> — mengunduh semua data event ke file Excel/CSV</li>
                        <li><strong>Template</strong> — mengunduh contoh format file Excel untuk diisi</li>
                        <li><strong>Import</strong> — mengunggah file Excel untuk membuat/update data otomatis</li>
                    </ul>
                    <p class="text-muted small">
                        Saat ini tersedia untuk: Tier Tiket, Add-on, Voucher, Pendaftar, dan Result Event.
                    </p>

                    <hr class="my-4">

                    {{-- Tips --}}
                    <h2 class="h5 fw-bold mb-3">
                        <i class="bi bi-lightbulb me-2 text-warning"></i>Tips Mengoptimalkan Event
                    </h2>
                    <ul>
                        <li><strong>Kategori Sport</strong>: Pastikan event sport/esport memilih kategori yang ditandai ⚡ Sport agar bisa masuk ke kolom Result di papan event</li>
                        <li><strong>Kuota Tepat</strong>: Set kuota tier sesuai dengan kapasitas venue</li>
                        <li><strong>Voucher</strong>: Gunakan voucher persen untuk diskon besar, nominal untuk diskon tetap</li>
                        <li><strong>Add-ons</strong>: Jual merchandise eksklusif sebagai add-on untuk meningkatkan revenue</li>
                        <li><strong>Banner</strong>: Gunakan banner 16:5 dengan kualitas tinggi untuk profesional look</li>
                    </ul>

                    <hr class="my-4">

                    <div class="alert alert-info">
                        <div class="d-flex align-items-start gap-2">
                            <i class="bi bi-info-circle fs-5 mt-1"></i>
                            <div>
                                <strong>Butuh bantuan?</strong><br>
                                Coba gunakan fitur ekspor/import untuk memindahkan data antar event.
                                Pastikan kategori sport (⚡ Sport) dipilih untuk event yang akan dipindahkan ke kolom Result.
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection