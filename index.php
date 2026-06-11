<?php
include 'config/koneksi.php';

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
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - Perpustakaan Akademik</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,600;0,700;1,400&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #fdfbf7;
            color: #333;
            scroll-behavior: smooth;
        }
        .serif-font {
            font-family: 'Lora', serif;
            color: #1a252f;
        }
        
        /* Sticky Navbar */
        .navbar-custom {
            background-color: rgba(26, 37, 47, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 3px solid #b8975a;
            transition: all 0.3s ease;
        }
        .navbar-brand { font-family: 'Lora', serif; font-weight: 700; letter-spacing: 0.5px; }
        .nav-link { color: #fdfbf7 !important; font-weight: 500; transition: color 0.3s ease; }
        .nav-link:hover { color: #b8975a !important; }

        /* Hero Section */
        .hero {
            background: linear-gradient(rgba(26, 37, 47, 0.85), rgba(26, 37, 47, 0.95)), url('https://images.unsplash.com/photo-1481627834876-b7833e8f5570?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80') center/cover fixed;
            padding: 120px 0;
            color: white;
            text-align: center;
            border-bottom: 5px solid #b8975a;
        }
        .hero h1 { color: #fdfbf7; font-size: 3.5rem; margin-bottom: 20px; }
        
        /* Search Box */
        .search-box { max-width: 700px; margin: 0 auto; position: relative; }
        .search-box input {
            height: 65px; border-radius: 35px; padding: 0 30px; font-size: 1.15rem;
            border: 2px solid #b8975a; box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        .search-box input:focus { box-shadow: 0 0 20px rgba(184, 151, 90, 0.5); outline: none; border-color: #b8975a; }
        .search-box button {
            position: absolute; right: 8px; top: 8px; height: 49px; border-radius: 25px;
            background-color: #b8975a; color: white; border: none; padding: 0 35px;
            font-weight: 600; font-size: 1.1rem; transition: background 0.3s ease;
        }
        .search-box button:hover { background-color: #a08149; }

        /* Stats Section */
        .stats-section { padding: 60px 0; background-color: white; border-bottom: 1px solid #e9e5db; }
        .stat-box { text-align: center; padding: 20px; }
        .stat-number { font-size: 3rem; font-weight: 700; color: #b8975a; font-family: 'Lora', serif; line-height: 1; }
        .stat-label { font-size: 1.1rem; color: #6c757d; font-weight: 500; margin-top: 10px; text-transform: uppercase; letter-spacing: 1px; }

        /* Section Titles */
        .section-title { text-align: center; margin-bottom: 50px; }
        .section-title h2 { font-weight: 700; text-transform: uppercase; letter-spacing: 2px; }
        .section-title .divider { width: 80px; height: 3px; background-color: #b8975a; margin: 20px auto; }

        /* Book Card (Consistent with previous design) */
        .book-card {
            background: white; border: 1px solid #e9e5db; overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease; height: 100%; display: flex; flex-direction: column; cursor: pointer; text-decoration: none; color: inherit;
        }
        .book-card:hover { transform: translateY(-8px); box-shadow: 0 15px 30px rgba(26, 37, 47, 0.15); border-color: #b8975a; color: inherit; }
        .book-cover-container { width: 100%; height: 280px; background-color: #f1ede1; display: flex; align-items: center; justify-content: center; border-bottom: 1px solid #e9e5db; padding: 20px; }
        .book-cover-container img { max-height: 100%; max-width: 100%; object-fit: contain; box-shadow: 5px 5px 15px rgba(0,0,0,0.2); }
        .book-info { padding: 20px; flex-grow: 1; display: flex; flex-direction: column; }
        .book-title { font-size: 1.1rem; font-weight: 600; margin-bottom: 8px; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .book-author { color: #555; font-size: 0.9rem; margin-bottom: 15px; font-style: italic; }
        .book-genre { font-size: 0.75rem; color: #b8975a; text-transform: uppercase; letter-spacing: 1px; font-weight: 600; margin-bottom: auto; }

        /* About Section */
        .about-section { background-color: #1a252f; color: white; padding: 80px 0; border-top: 5px solid #b8975a; }
        
        footer { background-color: #111a22; color: #adb5bd; padding: 30px 0; text-align: center; }
    </style>
</head>
<body>

    <!-- Sticky Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom py-3 fixed-top">
        <div class="container">
            <a class="navbar-brand fs-4" href="index.php">
                <i class="bi bi-bank me-2" style="color: #b8975a;"></i>Perpus Akademik
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="#beranda">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="#terbaru">Koleksi Terbaru</a></li>
                    <li class="nav-item"><a class="nav-link" href="#populer">Terpopuler</a></li>
                    <li class="nav-item"><a class="nav-link" href="katalog.php">Semua Katalog</a></li>
                    <li class="nav-item"><a class="nav-link" href="#tentang">Tentang</a></li>
                    <li class="nav-item ms-lg-4 mt-3 mt-lg-0">
                        <a class="btn btn-outline-light rounded-0 px-4 py-2" style="border-width: 2px;" href="admin/login.php">
                            <i class="bi bi-person-lock me-1"></i> Admin
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="beranda" class="hero mt-5">
        <div class="container">
            <h1 class="serif-font">Jendela Menuju Keabadian Ilmu</h1>
            <p class="lead mb-5" style="color: #e9ecef; max-width: 800px; margin: 0 auto;">
                Sistem Informasi Perpustakaan Akademik menyediakan akses cepat ke ribuan literatur klasik dan modern untuk menunjang riset dan pengetahuan Anda.
            </p>
            <div class="search-box">
                <form action="katalog.php" method="GET">
                    <input type="text" name="q" class="form-control" placeholder="Cari buku berdasarkan judul, pengarang, atau penerbit..." required>
                    <button type="submit"><i class="bi bi-search me-1"></i> Cari</button>
                </form>
            </div>
        </div>
    </section>

    <!-- Statistik Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="stat-box">
                        <div class="stat-number"><?php echo number_format($jml_buku, 0, ',', '.'); ?></div>
                        <div class="stat-label">Total Buku Tersedia</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-box border-start border-end">
                        <div class="stat-number"><?php echo number_format($jml_anggota, 0, ',', '.'); ?></div>
                        <div class="stat-label">Anggota Terdaftar</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-box">
                        <div class="stat-number"><?php echo number_format($jml_pinjam, 0, ',', '.'); ?></div>
                        <div class="stat-label">Transaksi Peminjaman</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Buku Terbaru Section -->
    <section id="terbaru" class="py-5 my-4">
        <div class="container">
            <div class="section-title">
                <h2 class="serif-font">Koleksi Terbaru</h2>
                <div class="divider"></div>
                <p class="text-muted">Literatur yang baru saja ditambahkan ke rak perpustakaan kami.</p>
            </div>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4">
                <?php while($b = mysqli_fetch_array($buku_terbaru)): 
                    $cover_path = "assets/img/covers/" . $b['cover'];
                    $has_cover = ($b['cover'] != "" && file_exists($cover_path));
                ?>
                <div class="col">
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
                <?php endwhile; ?>
            </div>
            <div class="text-center mt-5">
                <a href="katalog.php" class="btn btn-outline-dark rounded-0 px-4 py-2" style="border-color: #b8975a; color: #b8975a;">Lihat Semua Koleksi <i class="bi bi-arrow-right ms-2"></i></a>
            </div>
        </div>
    </section>

    <!-- Buku Populer Section -->
    <section id="populer" class="py-5" style="background-color: #f1ede1;">
        <div class="container">
            <div class="section-title">
                <h2 class="serif-font">Paling Sering Dipinjam</h2>
                <div class="divider"></div>
                <p class="text-muted">Buku-buku yang paling banyak diminati oleh anggota perpustakaan.</p>
            </div>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4">
                <?php while($b = mysqli_fetch_array($buku_populer)): 
                    $cover_path = "assets/img/covers/" . $b['cover'];
                    $has_cover = ($b['cover'] != "" && file_exists($cover_path));
                ?>
                <div class="col">
                    <a href="detail.php?id=<?php echo $b['id_buku']; ?>" class="book-card">
                        <div class="book-cover-container">
                            <?php if($has_cover): ?>
                                <img src="<?php echo $cover_path; ?>" alt="<?php echo $b['judul_buku']; ?>">
                            <?php else: ?>
                                <div class="text-center text-muted"><i class="bi bi-book" style="font-size: 4rem; color: #d5d0c4;"></i></div>
                            <?php endif; ?>
                            <div style="position: absolute; top: 10px; right: 10px; background: #b8975a; color: white; padding: 5px 10px; font-weight: bold; font-size: 0.8rem; border-radius: 3px;">
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
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="tentang" class="about-section">
        <div class="container text-center">
            <h2 class="serif-font mb-4 text-warning"><i class="bi bi-bank"></i> Tentang Perpustakaan Akademik</h2>
            <p class="lead" style="max-width: 800px; margin: 0 auto; opacity: 0.9;">
                Sistem Informasi Perpustakaan ini dibangun dengan dedikasi untuk menjembatani mahasiswa, dosen, dan peneliti dengan literatur berkualitas. Kami berkomitmen untuk terus menghadirkan akses pengetahuan yang tak terbatas, mengadopsi teknologi modern (seperti Arsitektur Single Page App feel), dan memberikan kemudahan eksplorasi bagi setiap insan akademis.
            </p>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p class="mb-0" style="font-size: 0.9rem;">&copy; <?php echo date('Y'); ?> Sistem Informasi Perpustakaan Akademik. All rights reserved.</p>
        </div>
    </footer>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
