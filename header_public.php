<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'config/koneksi.php';

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perpustakaan Bayu</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,600;0,700;1,400&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #fdfbf7; color: #333; overflow-x: hidden; }
        .serif-font { font-family: 'Lora', serif; }
        
        /* Navbar Kustom */
        .navbar-custom { background-color: rgba(26, 37, 47, 0.95) !important; backdrop-filter: blur(10px); transition: all 0.3s ease; border-bottom: 2px solid #b8975a; }
        .navbar-custom .navbar-brand { font-family: 'Lora', serif; font-weight: 700; color: #b8975a !important; font-size: 1.5rem; letter-spacing: 1px; }
        .navbar-custom .nav-link { color: #e9ecef !important; font-weight: 500; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px; padding: 10px 15px; transition: color 0.3s; }
        .navbar-custom .nav-link:hover, .navbar-custom .nav-link.active { color: #b8975a !important; }
        .navbar-toggler { border: none; color: #b8975a; }
        .navbar-toggler:focus { box-shadow: none; }

        /* Inline Search Nav */
        .nav-search-wrapper { position: relative; display: flex; align-items: center; justify-content: flex-end; margin-left: 15px; }
        .nav-search-btn { background: transparent; border: none; color: white; font-size: 1.2rem; cursor: pointer; transition: color 0.3s; z-index: 2; }
        .nav-search-btn:hover { color: #b8975a; }
        
        .nav-search-form { 
            position: absolute; right: 0; display: flex; align-items: center; 
            width: 0; opacity: 0; overflow: hidden; transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            background: #1a252f; z-index: 1; padding-right: 35px; /* space for icon */
        }
        .nav-search-wrapper.active .nav-search-form { width: 280px; opacity: 1; }
        .nav-search-wrapper.active .nav-search-btn { color: #b8975a; }
        
        .nav-search-input {
            background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);
            color: white; border-radius: 20px; padding: 6px 15px; width: 100%;
            outline: none; font-size: 0.9rem; transition: border-color 0.3s;
        }
        .nav-search-input::placeholder { color: rgba(255,255,255,0.5); font-style: italic; }
        .nav-search-input:focus { border-color: #b8975a; background: rgba(255,255,255,0.15); }
        
        /* Nav Menu Transition (to fade when search is active) */
        #nav-links-menu { transition: opacity 0.3s ease; }
        #nav-links-menu.fade-out { opacity: 0; pointer-events: none; }

        /* Hero Section */
        .hero {
            padding: 120px 0 80px 0;
            background: linear-gradient(rgba(17, 26, 34, 0.85), rgba(17, 26, 34, 0.95)), url('https://images.unsplash.com/photo-1541963463532-d68292c34b19?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') center/cover;
            color: white; text-align: center; border-bottom: 5px solid #b8975a;
        }
        .hero h1 { font-size: 3.5rem; font-weight: 700; margin-bottom: 20px; color: #fdfbf7; }
        
        /* Card Buku */
        .book-card {
            border: 1px solid transparent; border-radius: 8px; overflow: hidden;
            transition: all 0.3s ease; text-decoration: none; color: inherit;
            display: flex; flex-direction: column; height: 100%; background: transparent;
        }
        .book-card:hover { transform: translateY(-10px); }
        .book-cover-container { width: 100%; padding-top: 150%; position: relative; background-color: #f1ede1; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); transition: box-shadow 0.3s ease; }
        .book-card:hover .book-cover-container { box-shadow: 0 10px 25px rgba(184, 151, 90, 0.2); }
        .book-cover-container img { position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
        .book-card:hover .book-cover-container img { transform: scale(1.05); }
        
        .book-info { padding: 15px 5px 0 5px; flex-grow: 1; display: flex; flex-direction: column; }
        .book-title { font-size: 1.1rem; font-weight: 700; color: #1a252f; margin-bottom: 5px; line-height: 1.3; }
        .book-card:hover .book-title { color: #b8975a; }
        .book-author { font-size: 0.85rem; color: #6c757d; margin-bottom: 10px; font-weight: 500; }
        .book-genre { font-size: 0.75rem; color: white; background-color: #1a252f; padding: 3px 10px; border-radius: 12px; display: inline-block; align-self: flex-start; margin-top: auto; }

        body { padding-top: 76px; }
    </style>
</head>
<body>

    <!-- Sticky Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom py-3 fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php"><i class="bi bi-book-half me-2"></i>Perpus Bayu</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="bi bi-list fs-2"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center" id="nav-links-menu">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'index.php' ? 'active' : ''; ?>" href="index.php">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'katalog.php' ? 'active' : ''; ?>" href="katalog.php">Katalog</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#tentang">Tentang</a>
                    </li>
                    <li class="nav-item ms-lg-3 mt-3 mt-lg-0">
                        <a class="btn btn-outline-warning btn-sm px-4 rounded-pill fw-bold" href="admin/login.php" style="border-color: #b8975a; color: #b8975a;">Admin</a>
                    </li>
                </ul>
                
                <!-- Inline Expanding Search -->
                <div class="nav-search-wrapper d-none d-lg-flex" id="navSearchWrapper">
                    <form action="katalog.php" method="GET" class="nav-search-form" id="navSearchForm">
                        <input type="text" name="q" class="nav-search-input" id="navSearchInput" placeholder="Ketik judul buku...">
                    </form>
                    <button class="nav-search-btn" id="navSearchToggle" title="Cari Buku" data-is-katalog="<?php echo $current_page == 'katalog.php' ? 'true' : 'false'; ?>">
                        <i class="bi bi-search" id="navSearchIcon"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>
