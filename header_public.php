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
        .navbar-custom .nav-item { display: flex; align-items: center; }
        .navbar-custom .nav-link { position: relative; color: #e9ecef !important; font-weight: 500; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px; padding: 10px 15px; transition: color 0.3s; text-align: center; line-height: 1.4; }
        .navbar-custom .nav-link:hover, .navbar-custom .nav-link.active { color: #b8975a !important; }
        .navbar-custom .nav-link::after {
            content: ''; position: absolute; bottom: 0; left: 15px; right: 15px; height: 2px;
            background-color: #b8975a; transform: scaleX(0); transition: transform 0.3s ease; transform-origin: left;
        }
        .navbar-custom .nav-link:hover::after, .navbar-custom .nav-link.active::after { transform: scaleX(1); }
        .navbar-toggler { border: none; color: #b8975a; }
        .navbar-toggler:focus { box-shadow: none; }

        /* Inline Search Nav */
        .nav-search-wrapper { position: relative; display: flex; align-items: center; justify-content: flex-end; margin-left: 15px; }
        .nav-search-btn { background: transparent; border: none; color: white; font-size: 1.2rem; cursor: pointer; transition: color 0.3s; z-index: 2; }
        .nav-search-btn:hover { color: #b8975a; }
        
        .nav-search-form { 
            position: absolute; right: 0; top: 50%; transform: translateY(-50%); display: flex; flex-direction: column; 
            width: 0; opacity: 0; transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            background: transparent; z-index: 1; padding-right: 35px; /* space for icon */
        }
        .nav-search-wrapper.active .nav-search-form { width: 340px; opacity: 1; }
        .nav-search-wrapper.active .nav-search-btn { color: #b8975a; }
        
        .nav-search-input {
            background: transparent; border: none;
            color: white; width: 100%;
            outline: none; font-size: 0.9rem;
        }
        .nav-search-input::placeholder { color: rgba(255,255,255,0.5); font-style: italic; }
        .nav-search-input:focus { outline: none !important; box-shadow: none !important; background: transparent !important; color: white !important; }
        
        .nav-search-group {
            display: flex; flex-wrap: nowrap; align-items: center;
            border: 1px solid rgba(255,255,255,0.2); border-radius: 20px; 
            background: rgba(255,255,255,0.1); width: 100%; overflow: hidden;
            transition: all 0.3s ease;
        }
        .nav-search-group:focus-within {
            border-color: #b8975a; background: rgba(255,255,255,0.15);
            box-shadow: 0 0 0 0.2rem rgba(184, 151, 90, 0.25);
        }
        
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
            border: 1px solid #e9e5db; border-radius: 12px; overflow: hidden;
            transition: all 0.3s ease; text-decoration: none; color: inherit;
            display: flex; flex-direction: column; height: 100%; background: #ffffff;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        }
        .book-card:hover { transform: translateY(-10px); box-shadow: 0 15px 30px rgba(184, 151, 90, 0.15); }
        .book-cover-container { width: 100%; padding-top: 140%; position: relative; background-color: #fdfbf7; overflow: hidden; border-bottom: 1px solid #f1ede1; }
        .book-cover-container img { position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: contain; transition: transform 0.5s ease; padding: 15px; }
        .book-card:hover .book-cover-container img { transform: scale(1.05); }
        
        .book-info { padding: 20px 15px; flex-grow: 1; display: flex; flex-direction: column; }
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
                        <a class="nav-link <?php echo $current_page == 'index.php' ? 'active' : ''; ?>" href="index.php">Beranda<br>Utama</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#terbaru">Koleksi<br>Terbaru</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#populer">Buku<br>Populer</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#tentang">Tentang<br>Kami</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'katalog.php' ? 'active' : ''; ?>" href="katalog.php">Katalog<br>Lengkap</a>
                    </li>
                </ul>
                
                <!-- Inline Expanding Search -->
                <div class="nav-search-wrapper d-none d-lg-flex ms-3 me-3" id="navSearchWrapper">
                    <form action="katalog.php" method="GET" class="nav-search-form" id="navSearchForm">
                        <div class="nav-search-group" style="background: #1a252f;">
                            <input type="text" name="q" class="nav-search-input form-control border-0 ps-3" id="navSearchInput" placeholder="Ketik judul buku...">
                            
                            <button type="button" class="btn border-0 text-white-50 px-2" title="Filter Pencarian Lanjutan" data-bs-toggle="collapse" data-bs-target="#navFilterCollapse"><i class="bi bi-sliders"></i></button>
                            
                            <button type="submit" class="btn border-0 pe-3" style="color: #b8975a;"><i class="bi bi-search"></i></button>
                        </div>
                        
                        <!-- Advanced Filter Collapse -->
                        <div class="collapse w-100 mt-2" id="navFilterCollapse" style="position: absolute; top: 100%;">
                            <div class="card card-body border-0 shadow-lg" style="background-color: #fdfbf7; border-top: 3px solid #b8975a !important; border-radius: 8px;">
                                <div class="mb-3">
                                    <label class="form-label text-muted small fw-bold text-uppercase"><i class="bi bi-bookmark-fill text-warning me-1"></i> Pilih Genre</label>
                                    <select name="genre" class="form-select border-secondary">
                                        <option value="">Semua Genre</option>
                                        <?php 
                                        $nav_genre_filter = mysqli_query($koneksi, "SELECT * FROM genre ORDER BY nama_genre ASC");
                                        while($ng = mysqli_fetch_array($nav_genre_filter)): 
                                        ?>
                                            <option value="<?php echo $ng['id_genre']; ?>"><?php echo $ng['nama_genre']; ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div>
                                    <label class="form-label text-muted small fw-bold text-uppercase"><i class="bi bi-check-circle-fill text-success me-1"></i> Ketersediaan</label>
                                    <div class="d-flex flex-wrap gap-2 mt-1">
                                        <input type="radio" class="btn-check" name="status" id="navStatusSemua" value="" checked>
                                        <label class="btn btn-outline-secondary rounded-pill btn-sm px-3" for="navStatusSemua">Semua</label>

                                        <input type="radio" class="btn-check" name="status" id="navStatusTersedia" value="tersedia">
                                        <label class="btn btn-outline-success rounded-pill btn-sm px-3" for="navStatusTersedia">Tersedia</label>

                                        <input type="radio" class="btn-check" name="status" id="navStatusHabis" value="habis">
                                        <label class="btn btn-outline-danger rounded-pill btn-sm px-3" for="navStatusHabis">Habis</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <button class="nav-search-btn" id="navSearchToggle" title="Cari Buku" data-is-katalog="<?php echo $current_page == 'katalog.php' ? 'true' : 'false'; ?>">
                        <i class="bi bi-search" id="navSearchIcon"></i>
                    </button>
                </div>

                <a class="btn btn-outline-warning btn-sm px-4 rounded-pill fw-bold mt-3 mt-lg-0" href="admin/login.php" style="border-color: #b8975a; color: #b8975a; position: relative; z-index: 10;">Admin</a>
            </div>
        </div>
    </nav>
