# 📚 Sistem Informasi Perpustakaan (Portofolio Project)

Sebuah studi kasus pengembangan aplikasi *Library Management System* berbasis Web menggunakan PHP Native dan Bootstrap 5. Proyek ini dibangun dari nol sebagai kerangka dasar (*template*) yang *scalable* dan responsif.

## 🚀 Latar Belakang Proyek
Proyek ini bertujuan untuk menyelesaikan masalah administrasi perpustakaan tradisional dengan mendigitalisasi proses pendataan anggota, manajemen katalog buku (dengan relasi *Multi-Genre*), hingga otomatisasi transaksi peminjaman dan kalkulasi denda. 

## 🛠️ Tech Stack yang Digunakan
- **Backend:** PHP 8 Native (Prosedural & Konsep Relasional)
- **Database:** MySQL / MariaDB
- **Frontend:** HTML5, CSS3, Vanilla JavaScript
- **Framework UI:** Bootstrap 5.3
- **Library Tambahan:** Select2 (dengan Tema Bootstrap 5) untuk *Multiple Select*

## 🏗️ Arsitektur & Proses Pembuatan

Proyek ini dibangun melalui beberapa fase pengembangan yang terstruktur:

### Fase 1: Desain Database Relasional
Alih-alih menggunakan satu tabel besar yang rentan redundansi, database dirancang dengan normalisasi tingkat lanjut:
- Tabel `admin`: Mengamankan akses *Back-End*.
- Tabel `anggota`: Sentralisasi data peminjam.
- Tabel `buku`: Katalog utama perpustakaan.
- Tabel `genre` & `buku_genre` (Pivot Table): Mengimplementasikan relasi *Many-to-Many* agar satu buku dapat memiliki lebih dari satu genre tanpa memberatkan database.
- Tabel `peminjaman`: Berfungsi sebagai tabel transaksional yang merekam riwayat, status, dan denda.

### Fase 2: Autentikasi & Keamanan Sistem
Membangun portal login dengan menggunakan enkripsi `MD5` untuk *password* (sebagai fondasi dasar pembelajaran) dan manajemen `Session` PHP untuk mencegah akses ilegal via URL (*Direct URL Access*).

### Fase 3: Pembangunan UI/UX & Master Data (CRUD)
Fokus pada sisi administratif (*Back-End*):
1. **Responsive Dashboard Template:** Membangun *layout* dengan navigasi *Sidebar* yang tetap (fixed) di versi Desktop, dan otomatis berubah menjadi *Offcanvas* (menu geser dengan ikon hamburger) di versi Mobile.
2. **Manajemen Buku Lanjutan:** 
   - Memodifikasi form input HTML standar dengan **Library Select2** agar admin dapat memilih atau menambahkan beberapa genre sekaligus dengan desain *Pill-tags*.
   - **File Management System:** Membuat logika unggah gambar sampul (Cover). Sistem menyimpan file fisik ke direktori `/assets/img/covers` dan hanya menyimpan "nama file" di dalam database untuk mencegah *bloating* pada memori database MySQL. Ditambah dengan fitur *Live Preview* gambar menggunakan JavaScript `FileReader` API.

### Fase 4: Engine Transaksi Peminjaman (Core Business Logic)
Modul paling kompleks yang mengotomatisasi beberapa logika bisnis:
1. **Auto-Deduction Stock:** Ketika buku berhasil dipinjam, algoritma langsung mengurangi ketersediaan stok buku secara *real-time*. Jika stok = 0, buku disembunyikan dari pilihan peminjaman.
2. **Due Date Automation:** Sistem otomatis menghitung batas waktu pengembalian (Masa Pinjam: 7 Hari) dengan fungsi `strtotime()`.
3. **Dynamic Penalty Calculation (Denda):** Tabel peminjaman dapat menghitung denda keterlambatan secara dinamis. Jika status="Dipinjam" dan tanggal hari ini melewati batas waktu, sistem otomatis mengalikan jumlah hari keterlambatan dengan tarif denda (Rp 1.000/hari) tanpa perlu menyimpan perhitungan sementara ke database hingga buku benar-benar dikembalikan.

## 💡 Kesimpulan Pembelajaran (*Key Takeaways*)
Pembuatan aplikasi perpustakaan ini tidak sekadar membuat form CRUD, melainkan mengasah kemampuan logika transaksional (pengurangan stok, hitung mundur denda), manajemen *file upload*, dan desain *user interface* yang benar-benar responsif menyesuaikan perangkat pengguna.
