<?php
include 'header_public.php';

// Menghitung Statistik
$jml_buku = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(stok) as total FROM buku"))['total'] ?? 0;
$jml_anggota = mysqli_num_rows(mysqli_query($koneksi, "SELECT id_anggota FROM anggota"));
$jml_pinjam = mysqli_num_rows(mysqli_query($koneksi, "SELECT id_pinjam FROM peminjaman"));

// Mengambil 4 Buku Terbaru
$query_terbaru = "SELECT b.*, GROUP_CONCAT(g.nama_genre SEPARATOR ', ') as daftar_genre 
                  FROM buku b
                  LEFT JOIN buku_genre bg ON b.id_buku = bg.id_buku
                  LEFT JOIN genre g ON bg.id_genre = g.id_genre
                  GROUP BY b.id_buku 
                  ORDER BY b.id_buku DESC LIMIT 4";
$buku_terbaru = mysqli_query($koneksi, $query_terbaru);

// Mengambil 4 Buku Terpopuler (Berdasarkan jumlah peminjaman)
$query_populer = "SELECT b.*, GROUP_CONCAT(g.nama_genre SEPARATOR ', ') as daftar_genre, 
                  (SELECT COUNT(*) FROM peminjaman p WHERE p.id_buku = b.id_buku) as pinjam_count
                  FROM buku b
                  LEFT JOIN buku_genre bg ON b.id_buku = bg.id_buku
                  LEFT JOIN genre g ON bg.id_genre = g.id_genre
                  GROUP BY b.id_buku 
                  ORDER BY pinjam_count DESC LIMIT 4";
$buku_populer = mysqli_query($koneksi, $query_populer);
?>

    <!-- Hero Section -->
    <section id="beranda" class="hero" data-aos="fade-in" data-aos-duration="1500">
        <div class="container">
            <h1 class="serif-font" data-aos="fade-down" data-aos-delay="300">Katalog Online Perpustakaan</h1>
            <p class="lead mb-5" style="color: #e9ecef; max-width: 800px; margin: 0 auto;" data-aos="fade-up" data-aos-delay="500">
                Cari koleksi buku kami dan pastikan status ketersediaannya secara <i>real-time</i> sebelum Anda berkunjung ke perpustakaan fisik kami.
            </p>
            <div class="text-center" data-aos="zoom-in" data-aos-delay="700">
                <button class="btn btn-outline-light rounded-pill px-5 py-3 fs-5" style="border-width: 2px;" data-bs-toggle="modal" data-bs-target="#searchModal">
                    <i class="bi bi-search me-2"></i> Cek Ketersediaan Buku
                </button>
            </div>
        </div>
    </section>

    <!-- Statistik Section -->
    <section class="stats-section py-5" style="background-color: white; border-bottom: 1px solid #e9e5db;">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="text-center p-3">
                        <div class="stat-number" data-target="<?php echo $jml_buku; ?>" style="font-size: 3rem; font-weight: 700; color: #b8975a; font-family: 'Lora', serif; line-height: 1;">0</div>
                        <div style="font-size: 1.1rem; color: #6c757d; font-weight: 500; margin-top: 10px; text-transform: uppercase; letter-spacing: 1px;">Total Buku Tersedia</div>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="text-center p-3 border-start border-end">
                        <div class="stat-number" data-target="<?php echo $jml_anggota; ?>" style="font-size: 3rem; font-weight: 700; color: #b8975a; font-family: 'Lora', serif; line-height: 1;">0</div>
                        <div style="font-size: 1.1rem; color: #6c757d; font-weight: 500; margin-top: 10px; text-transform: uppercase; letter-spacing: 1px;">Anggota Terdaftar</div>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="text-center p-3">
                        <div class="stat-number" data-target="<?php echo $jml_pinjam; ?>" style="font-size: 3rem; font-weight: 700; color: #b8975a; font-family: 'Lora', serif; line-height: 1;">0</div>
                        <div style="font-size: 1.1rem; color: #6c757d; font-weight: 500; margin-top: 10px; text-transform: uppercase; letter-spacing: 1px;">Transaksi Peminjaman</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Buku Terbaru Section -->
    <section id="terbaru" class="py-5 my-4">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="serif-font fw-bold text-uppercase" style="letter-spacing: 2px;">Koleksi Terbaru</h2>
                <div style="width: 80px; height: 3px; background-color: #b8975a; margin: 20px auto;"></div>
                <p class="text-muted">Literatur yang baru saja ditambahkan ke rak perpustakaan kami.</p>
            </div>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4">
                <?php 
                $delay = 100;
                while($b = mysqli_fetch_array($buku_terbaru)): 
                    $cover_path = "assets/img/covers/" . $b['cover'];
                    $has_cover = ($b['cover'] != "" && file_exists($cover_path));
                ?>
                <div class="col" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
                    <a href="detail.php?id=<?php echo $b['id_buku']; ?>" class="book-card">
                        <div class="book-cover-container">
                            <?php if($has_cover): ?>
                                <img src="<?php echo $cover_path; ?>" alt="<?php echo $b['judul_buku']; ?>">
                            <?php else: ?>
                                <div class="text-center text-muted"><i class="bi bi-book" style="font-size: 4rem; color: #d5d0c4;"></i></div>
                            <?php endif; ?>
                        </div>
                        <div class="book-info">
                            <h3 class="book-title serif-font"><?php echo $b['judul_buku']; ?></h3>
                            <div class="book-author"><i class="bi bi-pen-fill" style="color:#b8975a;"></i> <?php echo $b['pengarang']; ?></div>
                            <div class="book-genre"><i class="bi bi-bookmark-fill me-1"></i><?php echo $b['daftar_genre'] ?: 'Umum'; ?></div>
                        </div>
                    </a>
                </div>
                <?php $delay += 100; endwhile; ?>
            </div>
            <div class="text-center mt-5" data-aos="zoom-in">
                <a href="katalog.php" class="btn btn-outline-dark rounded-0 px-4 py-2" style="border-color: #b8975a; color: #b8975a;">Lihat Semua Koleksi <i class="bi bi-arrow-right ms-2"></i></a>
            </div>
        </div>
    </section>

    <!-- Buku Populer Section -->
    <section id="populer" class="py-5" style="background-color: #f1ede1;">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="serif-font fw-bold text-uppercase" style="letter-spacing: 2px;">Paling Sering Dipinjam</h2>
                <div style="width: 80px; height: 3px; background-color: #b8975a; margin: 20px auto;"></div>
                <p class="text-muted">Buku-buku yang paling banyak diminati oleh anggota perpustakaan.</p>
            </div>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4">
                <?php 
                $delay = 100;
                while($b = mysqli_fetch_array($buku_populer)): 
                    $cover_path = "assets/img/covers/" . $b['cover'];
                    $has_cover = ($b['cover'] != "" && file_exists($cover_path));
                ?>
                <div class="col" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
                    <a href="detail.php?id=<?php echo $b['id_buku']; ?>" class="book-card">
                        <div class="book-cover-container">
                            <?php if($has_cover): ?>
                                <img src="<?php echo $cover_path; ?>" alt="<?php echo $b['judul_buku']; ?>">
                            <?php else: ?>
                                <div class="text-center text-muted"><i class="bi bi-book" style="font-size: 4rem; color: #d5d0c4;"></i></div>
                            <?php endif; ?>
                            <div style="position: absolute; top: 15px; right: -5px; background: #b8975a; color: white; padding: 5px 15px; font-weight: bold; font-size: 0.8rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                <i class="bi bi-star-fill text-warning me-1"></i> Populer
                            </div>
                        </div>
                        <div class="book-info">
                            <h3 class="book-title serif-font"><?php echo $b['judul_buku']; ?></h3>
                            <div class="book-author"><i class="bi bi-pen-fill" style="color:#b8975a;"></i> <?php echo $b['pengarang']; ?></div>
                            <div class="book-genre"><i class="bi bi-bookmark-fill me-1"></i><?php echo $b['daftar_genre'] ?: 'Umum'; ?></div>
                        </div>
                    </a>
                </div>
                <?php $delay += 100; endwhile; ?>
            </div>
        </div>
    </section>

    <!-- Lokasi & Kontak Section -->
    <section id="kontak" class="py-5" style="background-color: white;">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="serif-font fw-bold text-uppercase" style="letter-spacing: 2px;">Kunjungi Kami</h2>
                <div style="width: 80px; height: 3px; background-color: #b8975a; margin: 20px auto;"></div>
                <p class="text-muted">Temukan kami di pusat keajaiban arsitektur dunia.</p>
            </div>
            <div class="row g-5 align-items-center">
                <div class="col-lg-6" data-aos="fade-right">
                    <div class="p-4 bg-light rounded" style="border: 1px solid #e9e5db;">
                        <h4 class="serif-font mb-4">Informasi Perpustakaan</h4>
                        <ul class="list-unstyled mb-0" style="font-size: 1.1rem; line-height: 2;">
                            <li><i class="bi bi-geo-alt-fill text-warning me-3 fs-5"></i> <strong>Alamat:</strong> Downtown Dubai, Uni Emirat Arab</li>
                            <li><i class="bi bi-telephone-fill text-warning me-3 fs-5"></i> <strong>Telepon:</strong> +971 4 366 1688</li>
                            <li><i class="bi bi-envelope-fill text-warning me-3 fs-5"></i> <strong>Email:</strong> hello@perpusbayu.ae</li>
                            <li><i class="bi bi-clock-fill text-warning me-3 fs-5"></i> <strong>Jam Buka:</strong> Senin - Jumat (08:00 - 18:00)</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <div class="rounded overflow-hidden shadow-sm" style="border: 1px solid #e9e5db;">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d115545.91890332822!2d55.20573932822432!3d25.176465403247065!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e5f43496ad9c645%3A0xbde66e5084295162!2sDubai%20-%20United%20Arab%20Emirates!5e0!3m2!1sen!2sid!4v1700000000000!5m2!1sen!2sid" width="100%" height="350" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const counters = document.querySelectorAll('.stat-number');
    const speed = 100; // Semakin kecil semakin cepat

    const animateCounters = () => {
        counters.forEach(counter => {
            const updateCount = () => {
                const target = +counter.getAttribute('data-target');
                const count = +counter.innerText.replace(/\./g, '');
                const inc = target / speed;

                if (count < target) {
                    counter.innerText = Math.ceil(count + inc).toLocaleString('id-ID');
                    setTimeout(updateCount, 20);
                } else {
                    counter.innerText = target.toLocaleString('id-ID');
                }
            };
            updateCount();
        });
    };

    // Use Intersection Observer to trigger when visible
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if(entry.isIntersecting) {
                animateCounters();
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });
    
    const statsSection = document.querySelector('.stats-section');
    if(statsSection) observer.observe(statsSection);
});
</script>

<?php include 'footer_public.php'; ?>
