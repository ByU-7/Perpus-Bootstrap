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
    <section id="beranda" class="hero" style="padding: 160px 0 160px 0; position: relative;" data-aos="fade-in" data-aos-duration="1500">
        <div class="container">
            <h1 class="serif-font" data-aos="fade-down" data-aos-delay="300">Sistem Perpustakaan & Sirkulasi</h1>
            <p class="lead mb-5" style="color: #e9ecef; max-width: 800px; margin: 0 auto;" data-aos="fade-up" data-aos-delay="500">
                Pusat pengelolaan literatur cetak dan referensi fisik. Telusuri koleksi kami, temukan raknya, dan pastikan ketersediaannya sebelum Anda berkunjung.
            </p>
            <div class="text-center" data-aos="zoom-in" data-aos-delay="700">
                <a href="#terbaru" class="btn btn-hero rounded-pill px-4 py-2 fw-bold">
                    Eksplorasi Koleksi <i class="bi bi-arrow-down ms-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Statistik Section (Floating Over Hero) -->
    <section id="statistik" class="stats-section" style="margin-top: -60px; position: relative; z-index: 10;">
        <div class="container">
            <div class="row g-0 shadow-lg rounded-4 overflow-hidden" style="background: linear-gradient(rgba(255,255,255,0.97), rgba(255,255,255,0.97)), url('https://www.transparenttextures.com/patterns/cream-paper.png'); border: 1px solid #e9e5db;">
                <div class="col-md-4">
                    <div class="text-center p-4">
                        <div class="stat-number" data-target="<?php echo $jml_buku; ?>" style="font-size: 3.5rem; font-weight: 700; color: #e6a756; font-family: 'Lora', serif; line-height: 1;">0</div>
                        <div style="font-size: 1.1rem; color: #6c757d; font-weight: 500; margin-top: 10px; text-transform: uppercase; letter-spacing: 1px;">Total Buku Tersedia</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center p-4 border-start border-end">
                        <div class="stat-number" data-target="<?php echo $jml_anggota; ?>" style="font-size: 3.5rem; font-weight: 700; color: #e6a756; font-family: 'Lora', serif; line-height: 1;">0</div>
                        <div style="font-size: 1.1rem; color: #6c757d; font-weight: 500; margin-top: 10px; text-transform: uppercase; letter-spacing: 1px;">Anggota Terdaftar</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center p-4">
                        <div class="stat-number" data-target="<?php echo $jml_pinjam; ?>" style="font-size: 3.5rem; font-weight: 700; color: #e6a756; font-family: 'Lora', serif; line-height: 1;">0</div>
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
                <div style="width: 80px; height: 3px; background-color: #e6a756; margin: 20px auto;"></div>
                <p class="text-muted">Literatur yang baru saja ditambahkan ke rak perpustakaan kami.</p>
            </div>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4">
                <?php 
                $delay = 100;
                while($b = mysqli_fetch_array($buku_terbaru)): 
                    $cover_path = "uploads/covers/" . $b['cover'];
                    $has_cover = ($b['cover'] != "" && file_exists($cover_path));
                ?>
                <div class="col" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
                    <a href="javascript:void(0)" onclick="openBookModal(<?php echo $b['id_buku']; ?>)" class="book-card">
                        <div class="book-cover-container">
                            <?php if($has_cover): ?>
                                <img src="<?php echo $cover_path; ?>" alt="<?php echo $b['judul_buku']; ?>">
                            <?php else: ?>
                                <div class="text-center text-muted" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;"><i class="bi bi-book" style="font-size: 4rem; color: #d5d0c4;"></i></div>
                            <?php endif; ?>
                        </div>
                        <div class="book-info">
                            <h3 class="book-title serif-font"><?php echo $b['judul_buku']; ?></h3>
                            <div class="book-author"><i class="bi bi-pen-fill" style="color:#e6a756;"></i> <?php echo $b['pengarang']; ?></div>
                            <div class="book-genre"><i class="bi bi-bookmark-fill me-1"></i><?php echo $b['daftar_genre'] ?: 'Umum'; ?></div>
                        </div>
                    </a>
                </div>
                <?php $delay += 100; endwhile; ?>
            </div>
            <div class="text-center mt-5" data-aos="zoom-in">
                <a href="katalog.php" class="btn btn-outline-dark rounded-0 px-4 py-2" style="border-color: #e6a756; color: #e6a756;">Lihat Semua Koleksi <i class="bi bi-arrow-right ms-2"></i></a>
            </div>
        </div>
    </section>

    <!-- Buku Populer Section -->
    <section id="populer" class="py-5" style="background-color: #f1ede1;">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="serif-font fw-bold text-uppercase" style="letter-spacing: 2px;">Paling Sering Dipinjam</h2>
                <div style="width: 80px; height: 3px; background-color: #e6a756; margin: 20px auto;"></div>
                <p class="text-muted">Buku-buku yang paling banyak diminati oleh anggota perpustakaan.</p>
            </div>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4">
                <?php 
                $delay = 100;
                while($b = mysqli_fetch_array($buku_populer)): 
                    $cover_path = "uploads/covers/" . $b['cover'];
                    $has_cover = ($b['cover'] != "" && file_exists($cover_path));
                ?>
                <div class="col" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
                    <a href="javascript:void(0)" onclick="openBookModal(<?php echo $b['id_buku']; ?>)" class="book-card">
                        <div class="book-cover-container">
                            <?php if($has_cover): ?>
                                <img src="<?php echo $cover_path; ?>" alt="<?php echo $b['judul_buku']; ?>">
                            <?php else: ?>
                                <div class="text-center text-muted" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;"><i class="bi bi-book" style="font-size: 4rem; color: #d5d0c4;"></i></div>
                            <?php endif; ?>
                            <div style="position: absolute; top: 15px; right: -5px; background: #e6a756; color: white; padding: 5px 15px; font-weight: bold; font-size: 0.8rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                <i class="bi bi-star-fill text-warning me-1"></i> Populer
                            </div>
                        </div>
                        <div class="book-info">
                            <h3 class="book-title serif-font"><?php echo $b['judul_buku']; ?></h3>
                            <div class="book-author"><i class="bi bi-pen-fill" style="color:#e6a756;"></i> <?php echo $b['pengarang']; ?></div>
                            <div class="book-genre"><i class="bi bi-bookmark-fill me-1"></i><?php echo $b['daftar_genre'] ?: 'Umum'; ?></div>
                        </div>
                    </a>
                </div>
                <?php $delay += 100; endwhile; ?>
            </div>
        </div>
    </section>

    <!-- Lokasi & Kontak Section -->
    <section id="kontak" class="py-5" style="background-color: #fdfbf7;">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="serif-font fw-bold text-uppercase" style="letter-spacing: 2px;">Kunjungi Kami</h2>
                <div style="width: 80px; height: 3px; background-color: #e6a756; margin: 20px auto;"></div>
                <p class="text-muted">Pusat literatur dan ruang tenang untuk menemukan inspirasi Anda.</p>
            </div>
            
            <!-- Placeholder Foto Perpustakaan Asli -->
            <div class="row mb-5" data-aos="fade-up" data-aos-delay="100">
                <div class="col-12">
                    <div class="rounded-3 overflow-hidden shadow-sm border" style="height: 350px; background-color: #333; display: flex; align-items: center; justify-content: center; position: relative; border-color: #e9e5db !important;">
                        <img src="https://images.unsplash.com/photo-1568667256549-094345857637?auto=format&fit=crop&w=1920&q=80" alt="Foto Perpustakaan" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.6;">
                        <div style="position: absolute; text-align: center; color: white; text-shadow: 0 4px 6px rgba(0,0,0,0.8);">
                            <i class="bi bi-camera-fill fs-1 mb-2 d-block text-warning"></i>
                            <h3 class="serif-font fw-bold">Area Placeholder Foto Asli</h3>
                            <p class="mb-0">Ganti URL gambar ini dengan foto bangunan / ruangan perpustakaan Anda</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 align-items-stretch">
                <!-- Info Cards -->
                <div class="col-lg-5">
                    <div class="row g-4 h-100">
                        <div class="col-sm-6 col-lg-12" data-aos="fade-right" data-aos-delay="100">
                            <div class="info-card h-100 d-flex align-items-center p-4 bg-white rounded-3 shadow-sm border" style="border-color: #e9e5db !important; transition: transform 0.3s, box-shadow 0.3s;">
                                <div class="icon-box me-4 d-flex align-items-center justify-content-center rounded-circle" style="width: 60px; height: 60px; background-color: rgba(230, 167, 86, 0.1); color: #e6a756; flex-shrink: 0; transition: all 0.3s ease;">
                                    <i class="bi bi-geo-alt-fill fs-3"></i>
                                </div>
                                <div>
                                    <h5 class="serif-font fw-bold mb-1" style="color: #333;">Alamat Utama</h5>
                                    <p class="text-muted mb-0 small">Pusat Kota Jakarta, Kompleks Literatur Nasional, 10110</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-12" data-aos="fade-right" data-aos-delay="200">
                            <div class="info-card h-100 d-flex align-items-center p-4 bg-white rounded-3 shadow-sm border" style="border-color: #e9e5db !important; transition: transform 0.3s, box-shadow 0.3s;">
                                <div class="icon-box me-4 d-flex align-items-center justify-content-center rounded-circle" style="width: 60px; height: 60px; background-color: rgba(230, 167, 86, 0.1); color: #e6a756; flex-shrink: 0; transition: all 0.3s ease;">
                                    <i class="bi bi-clock-fill fs-3"></i>
                                </div>
                                <div>
                                    <h5 class="serif-font fw-bold mb-1" style="color: #333;">Jam Operasional</h5>
                                    <p class="text-muted mb-0 small">Senin - Jumat: 08:00 - 20:00<br>Akhir Pekan: 09:00 - 16:00</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-lg-12" data-aos="fade-right" data-aos-delay="300">
                            <div class="info-card h-100 d-flex align-items-center p-4 bg-white rounded-3 shadow-sm border" style="border-color: #e9e5db !important; transition: transform 0.3s, box-shadow 0.3s;">
                                <div class="icon-box me-4 d-flex align-items-center justify-content-center rounded-circle" style="width: 60px; height: 60px; background-color: rgba(230, 167, 86, 0.1); color: #e6a756; flex-shrink: 0; transition: all 0.3s ease;">
                                    <i class="bi bi-envelope-paper-fill fs-3"></i>
                                </div>
                                <div>
                                    <h5 class="serif-font fw-bold mb-1" style="color: #333;">Kontak & Layanan</h5>
                                    <p class="text-muted mb-0 small">Email: hello@perpusbayu.id<br>Telepon: (021) 555-0198</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Map -->
                <div class="col-lg-7" data-aos="fade-left" data-aos-delay="200">
                    <div class="h-100 rounded-3 overflow-hidden shadow-sm border" style="border-color: #e9e5db !important; min-height: 400px; position: relative;">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126920.2403698053!2d106.75881457812501!3d-6.229746499999998!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f3e945e34b9d%3A0x5371bf0fdad786a2!2sJakarta%2C%20Indonesia!5e0!3m2!1sen!2sid!4v1700000000000!5m2!1sen!2sid" width="100%" height="100%" style="border:0; position: absolute; top:0; left:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            </div>

        </div>
    </section>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const initAnimations = () => {
        document.body.classList.add('loaded');

        const counters = document.querySelectorAll('.stat-number');
        const duration = 2000; // 2 detik

        const animateCounters = () => {
            counters.forEach(counter => {
                const target = +counter.getAttribute('data-target');
                let startTime = null;

                const updateCount = (currentTime) => {
                    if (!startTime) startTime = currentTime;
                    const progress = currentTime - startTime;
                    const percentage = Math.min(progress / duration, 1);
                    
                    const easeOut = 1 - Math.pow(1 - percentage, 3);
                    const currentCount = Math.ceil(target * easeOut);
                    
                    counter.innerText = currentCount.toLocaleString('id-ID');

                    if (progress < duration) {
                        requestAnimationFrame(updateCount);
                    } else {
                        counter.innerText = target.toLocaleString('id-ID');
                    }
                };
                requestAnimationFrame(updateCount);
            });
        };

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
    };

    if (window.bookTransitionFinished) {
        initAnimations();
    } else {
        window.triggerInitAnimations = () => {
            initAnimations();
        };
    }
});
</script>

<?php include 'footer_public.php'; ?>

