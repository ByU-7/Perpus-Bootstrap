<?php
include 'config/koneksi.php';

// Menangani Pencarian
$keyword = "";
if(isset($_GET['q'])) {
    $keyword = mysqli_real_escape_string($koneksi, $_GET['q']);
    $query = "SELECT b.*, GROUP_CONCAT(g.nama_genre SEPARATOR ', ') as daftar_genre 
              FROM buku b
              LEFT JOIN buku_genre bg ON b.id_buku = bg.id_buku
              LEFT JOIN genre g ON bg.id_genre = g.id_genre
              WHERE b.judul_buku LIKE '%$keyword%' OR b.pengarang LIKE '%$keyword%'
              GROUP BY b.id_buku 
              ORDER BY b.id_buku DESC";
} else {
    $query = "SELECT b.*, GROUP_CONCAT(g.nama_genre SEPARATOR ', ') as daftar_genre 
              FROM buku b
              LEFT JOIN buku_genre bg ON b.id_buku = bg.id_buku
              LEFT JOIN genre g ON bg.id_genre = g.id_genre
              GROUP BY b.id_buku 
              ORDER BY b.id_buku DESC";
}

$data_buku = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Publik - Perpustakaan Akademik</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts untuk nuansa Klasik/Akademis -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,600;0,700;1,400&family=Outfit:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #fdfbf7; /* Warna kertas tua/krem sangat muda */
            color: #333;
        }
        h1, h2, h3, h4, h5, .serif-font {
            font-family: 'Lora', serif;
            color: #1a252f;
        }
        
        /* Navbar */
        .navbar-custom {
            background-color: #1a252f; /* Navy Blue gelap */
            border-bottom: 3px solid #b8975a; /* Aksen emas klasik */
        }
        .navbar-brand {
            font-family: 'Lora', serif;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(rgba(26, 37, 47, 0.8), rgba(26, 37, 47, 0.95)), url('https://images.unsplash.com/photo-1507842217343-583bb7270b66?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80') center/cover;
            padding: 90px 0;
            color: white;
            text-align: center;
            border-bottom: 5px solid #b8975a;
        }
        .hero h1 {
            color: #fdfbf7;
            font-size: 3.5rem;
            margin-bottom: 20px;
        }
        
        /* Search Box */
        .search-box {
            max-width: 600px;
            margin: 0 auto;
            position: relative;
        }
        .search-box input {
            height: 60px;
            border-radius: 30px;
            padding-left: 25px;
            padding-right: 120px;
            font-size: 1.1rem;
            border: 2px solid #b8975a;
            background-color: rgba(255, 255, 255, 0.98);
        }
        .search-box input:focus {
            box-shadow: 0 0 20px rgba(184, 151, 90, 0.4);
            border-color: #b8975a;
            outline: none;
        }
        .search-box button {
            position: absolute;
            right: 6px;
            top: 6px;
            height: 48px;
            border-radius: 25px;
            background-color: #b8975a;
            color: white;
            border: none;
            padding: 0 30px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .search-box button:hover {
            background-color: #a08149;
        }

        /* Book Card */
        .book-card {
            background: white;
            border: 1px solid #e9e5db;
            border-radius: 0; /* Ujung kotak tegas ala klasik */
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .book-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(26, 37, 47, 0.15);
            border-color: #b8975a;
        }
        .book-cover-container {
            width: 100%;
            height: 320px;
            background-color: #f1ede1;
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 1px solid #e9e5db;
            position: relative;
            padding: 20px;
        }
        .book-cover-container img {
            max-height: 100%;
            max-width: 100%;
            object-fit: contain;
            box-shadow: 5px 5px 15px rgba(0,0,0,0.2);
        }
        .book-info {
            padding: 25px 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        .book-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 8px;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .book-author {
            color: #555;
            font-size: 0.95rem;
            margin-bottom: 20px;
            font-style: italic;
        }
        .book-genre {
            font-size: 0.75rem;
            color: #b8975a;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            margin-bottom: auto; /* Mendorong stok ke bagian bawah */
        }
        .book-stok {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #e9e5db;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        /* Footer */
        footer {
            background-color: #1a252f;
            color: #adb5bd;
            border-top: 4px solid #b8975a;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom py-3">
        <div class="container">
            <a class="navbar-brand fs-4" href="index.php">
                <i class="bi bi-bank me-2" style="color: #b8975a;"></i>Perpustakaan Akademik
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Katalog Utama</a>
                    </li>
                    <li class="nav-item ms-lg-4 mt-3 mt-lg-0">
                        <a class="btn btn-outline-light rounded-0 px-4 py-2" style="border-width: 2px;" href="admin/login.php">
                            <i class="bi bi-person-lock me-1"></i> Portal Admin
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1 class="serif-font">Eksplorasi Jendela Dunia</h1>
            <p class="lead mb-5" style="color: #e9ecef; font-family: 'Outfit', sans-serif;">
                Temukan ribuan koleksi literatur klasik maupun keilmuan modern di ruang baca kami.
            </p>
            
            <div class="search-box">
                <form action="index.php" method="GET">
                    <input type="text" name="q" class="form-control" placeholder="Cari judul buku atau nama pengarang..." value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
                    <button type="submit"><i class="bi bi-search"></i> Cari</button>
                </form>
            </div>
        </div>
    </section>

    <!-- Main Content (Katalog) -->
    <section class="py-5 mt-3">
        <div class="container">
            
            <?php if($keyword != ""): ?>
                <div class="d-flex justify-content-between align-items-center mb-5">
                    <h4 class="serif-font mb-0">Hasil pencarian untuk: "<span style="color: #b8975a;"><?php echo $keyword; ?></span>"</h4>
                    <a href="index.php" class="btn btn-outline-secondary btn-sm rounded-0"><i class="bi bi-x"></i> Reset Pencarian</a>
                </div>
            <?php else: ?>
                <div class="text-center mb-5">
                    <h2 class="serif-font fw-bold text-uppercase" style="letter-spacing: 2px;">Koleksi Terbaru</h2>
                    <div style="width: 80px; height: 3px; background-color: #b8975a; margin: 20px auto;"></div>
                </div>
            <?php endif; ?>

            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-5">
                
                <?php 
                if(mysqli_num_rows($data_buku) == 0){
                    echo "<div class='col-12 text-center py-5'>
                            <i class='bi bi-journal-x fs-1 text-muted mb-3 d-block'></i>
                            <h5 class='text-muted serif-font'>Tidak ada literatur yang ditemukan.</h5>
                          </div>";
                }

                while($b = mysqli_fetch_array($data_buku)): 
                    $cover_path = "assets/img/covers/" . $b['cover'];
                    $has_cover = ($b['cover'] != "" && file_exists($cover_path));
                ?>
                <div class="col">
                    <div class="book-card">
                        <div class="book-cover-container">
                            <?php if($has_cover): ?>
                                <img src="<?php echo $cover_path; ?>" alt="<?php echo $b['judul_buku']; ?>">
                            <?php else: ?>
                                <div class="text-center text-muted">
                                    <i class="bi bi-book" style="font-size: 5rem; color: #d5d0c4;"></i>
                                    <div class="mt-3 serif-font" style="font-size: 0.9rem; color: #9c978b;">Tanpa Sampul</div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="book-info">
                            <h3 class="book-title serif-font" title="<?php echo $b['judul_buku']; ?>"><?php echo $b['judul_buku']; ?></h3>
                            <div class="book-author"><i class="bi bi-pen-fill" style="color:#b8975a; font-size: 0.8rem; margin-right:5px;"></i> <?php echo $b['pengarang']; ?></div>
                            
                            <div class="book-genre">
                                <?php 
                                    if($b['daftar_genre']) {
                                        echo "<i class='bi bi-bookmark-fill me-1'></i>" . str_replace(", ", " &bull; ", $b['daftar_genre']);
                                    } else {
                                        echo "<i class='bi bi-bookmark'></i> Literatur Umum";
                                    }
                                ?>
                            </div>

                            <div class="book-stok">
                                <span class="text-muted" style="font-size: 0.85rem; font-family: 'Lora', serif; font-style: italic;">Terbit: <?php echo $b['tahun_terbit']; ?></span>
                                <?php if($b['stok'] > 0): ?>
                                    <span class="badge" style="background-color: #eef5ed; color: #2e7d32; border: 1px solid #c8e6c9;">Tersedia (<?php echo $b['stok']; ?>)</span>
                                <?php else: ?>
                                    <span class="badge" style="background-color: #fdeded; color: #c62828; border: 1px solid #ffcdd2;">Habis Dipinjam</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>

            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-5 mt-5 text-center">
        <div class="container">
            <h4 class="serif-font mb-3"><i class="bi bi-bank me-2" style="color: #b8975a;"></i>Perpustakaan Akademik</h4>
            <p class="mb-0 mt-3" style="font-size: 0.95rem; opacity: 0.7;">&copy; <?php echo date('Y'); ?> Sistem Informasi Perpustakaan.</p>
            <p style="font-size: 0.85rem; opacity: 0.5; margin-top: 5px;">Dikembangkan sebagai Portofolio & Studi Kasus.</p>
        </div>
    </footer>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
