<?php
if(session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'config/koneksi.php';

// Menentukan menu aktif
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perpustakaan Akademik</title>
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
            padding-top: 76px; /* Offset for fixed navbar */
        }
        .serif-font {
            font-family: 'Lora', serif;
            color: #1a252f;
        }
        
        /* Sticky Navbar */
        .navbar-custom {
            background-color: rgba(26, 37, 47, 0.98);
            backdrop-filter: blur(10px);
            border-bottom: 3px solid #b8975a;
            transition: all 0.3s ease;
        }
        .navbar-brand { font-family: 'Lora', serif; font-weight: 700; letter-spacing: 0.5px; }
        
        /* Navigasi berjarak & Hover Underline */
        .navbar-nav .nav-item { margin-left: 15px; }
        .nav-link { 
            color: #fdfbf7 !important; 
            font-weight: 500; 
            transition: all 0.3s ease;
            position: relative;
            padding-bottom: 5px;
        }
        .nav-link::after {
            content: ''; position: absolute; width: 0; height: 2px;
            bottom: 0; left: 50%; background-color: #b8975a;
            transition: all 0.3s ease; transform: translateX(-50%);
        }
        .nav-link:hover::after, .nav-link.active::after { width: 80%; }
        .nav-link:hover, .nav-link.active { color: #b8975a !important; }

        /* Icon Search di Navigasi */
        .nav-search-btn {
            background: transparent; border: none; color: white;
            font-size: 1.2rem; cursor: pointer; transition: color 0.3s;
        }
        .nav-search-btn:hover { color: #b8975a; }

        /* Modal Search Full Screen */
        .modal-search { background-color: rgba(26, 37, 47, 0.98); }
        .modal-search .modal-content { background: transparent; border: none; }
        .modal-search input { 
            background: transparent; border: none; border-bottom: 3px solid #b8975a; 
            color: white; font-size: 2.5rem; border-radius: 0; padding-left: 0;
            box-shadow: none !important; font-family: 'Lora', serif;
        }
        .modal-search input::placeholder { color: rgba(255,255,255,0.3); }
        .modal-search input:focus { border-color: white; }
        .btn-close-search { position: absolute; top: 30px; right: 30px; font-size: 2rem; color: white; cursor: pointer; z-index: 1060; }

        /* Hero Section */
        .hero {
            background-color: #1a252f;
            background-image: linear-gradient(rgba(26, 37, 47, 0.85), rgba(26, 37, 47, 0.95)), url('https://images.unsplash.com/photo-1481627834876-b7833e8f5570?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80');
            background-size: cover;
            background-position: center;
            padding: 100px 0;
            color: white;
            text-align: center;
            border-bottom: 5px solid #b8975a;
        }
        .hero h1 { color: #fdfbf7; font-size: 3.5rem; margin-bottom: 20px; }
        .book-card {
            background: #ffffff; border: 1px solid #eaeaea; overflow: hidden; border-radius: 6px;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); 
            height: 100%; display: flex; flex-direction: column; cursor: pointer; text-decoration: none; color: inherit;
        }
        /* Hover Animation Ekstra */
        .book-card:hover { 
            transform: translateY(-12px); 
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1); 
            border-color: #b8975a; 
            color: inherit; 
        }
        /* Container cover disamakan warnanya dengan card (Putih) & Hilangkan border patah */
        .book-cover-container { 
            width: 100%; height: 280px; background-color: #ffffff; 
            display: flex; align-items: center; justify-content: center; 
            padding: 20px; border-bottom: none; 
            transition: all 0.3s ease;
        }
        .book-card:hover .book-cover-container { padding: 15px; } /* Efek gambar membesar */
        .book-cover-container img { max-height: 100%; max-width: 100%; object-fit: contain; box-shadow: 5px 5px 15px rgba(0,0,0,0.15); transition: all 0.3s ease; }
        .book-card:hover .book-cover-container img { box-shadow: 8px 8px 25px rgba(0,0,0,0.25); }
        
        .book-info { padding: 0 20px 25px 20px; flex-grow: 1; display: flex; flex-direction: column; }
        .book-title { font-size: 1.15rem; font-weight: 700; margin-bottom: 8px; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .book-author { color: #6c757d; font-size: 0.9rem; margin-bottom: 15px; font-style: italic; }
        .book-genre { font-size: 0.75rem; color: #b8975a; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; margin-bottom: auto; }

        /* Filter Genre (Pills) */
        .filter-pill {
            display: inline-block; padding: 8px 20px; margin: 0 5px 10px 0;
            background-color: white; border: 1px solid #dee2e6; color: #495057;
            border-radius: 50px; text-decoration: none; font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        .filter-pill:hover, .filter-pill.active { background-color: #1a252f; color: white; border-color: #1a252f; }

        /* Mega Footer */
        footer { background-color: #111a22; color: #adb5bd; padding: 60px 0 20px 0; border-top: 5px solid #b8975a; }
        .footer-heading { color: white; font-family: 'Lora', serif; margin-bottom: 25px; font-weight: 600; }
        .footer-link { color: #adb5bd; text-decoration: none; transition: color 0.3s; display: block; margin-bottom: 10px; }
        .footer-link:hover { color: #b8975a; padding-left: 5px; }
        .contact-info i { color: #b8975a; width: 25px; }
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
                    <li class="nav-item"><a class="nav-link <?php echo $current_page == 'index.php' ? 'active' : ''; ?>" href="index.php#beranda">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php#terbaru">Koleksi Terbaru</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php#populer">Terpopuler</a></li>
                    <li class="nav-item"><a class="nav-link <?php echo $current_page == 'katalog.php' ? 'active' : ''; ?>" href="katalog.php">Semua Katalog</a></li>
                    <li class="nav-item"><a class="nav-link" href="#tentang">Tentang</a></li>
                    
                    <!-- Search Icon Button -->
                    <li class="nav-item ms-lg-3">
                        <button class="nav-search-btn" data-bs-toggle="modal" data-bs-target="#searchModal">
                            <i class="bi bi-search"></i>
                        </button>
                    </li>
                    
                    <li class="nav-item ms-lg-4 mt-3 mt-lg-0">
                        <a class="btn btn-outline-light rounded-0 px-4 py-2" style="border-width: 2px;" href="admin/login.php">
                            <i class="bi bi-person-lock me-1"></i> Admin
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Modal Search Full Screen -->
    <div class="modal fade modal-search" id="searchModal" tabindex="-1" aria-hidden="true">
        <i class="bi bi-x-lg btn-close-search" data-bs-dismiss="modal"></i>
        <div class="modal-dialog modal-fullscreen d-flex align-items-center justify-content-center">
            <div class="modal-content">
                <div class="modal-body d-flex align-items-center justify-content-center p-5">
                    <form action="katalog.php" method="GET" style="width: 100%; max-width: 800px;">
                        <h4 class="text-white opacity-75 mb-4 serif-font text-center">Cari ketersediaan buku di rak kami</h4>
                        <div class="input-group">
                            <input type="text" name="q" class="form-control text-center" placeholder="Ketik judul buku, nama pengarang, atau kode buku..." autofocus required>
                        </div>
                        <div class="text-center mt-5">
                            <button type="submit" class="btn btn-outline-light rounded-pill px-5 py-2 fs-5">Cari di Katalog</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
