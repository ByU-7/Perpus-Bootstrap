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
    <script>
        if (sessionStorage.getItem('incomingTransition') || (!sessionStorage.getItem('splashShown') && (window.location.pathname.endsWith('/') || window.location.pathname.includes('index.php')))) {
            document.write('<style id="anti-flicker-style">body > *:not(#book-transition):not(script) { opacity: 0 !important; visibility: hidden !important; }</style>');
            document.write('<div id="anti-flicker-overlay" style="position:fixed; top:0; left:0; width:100vw; height:100vh; background:linear-gradient(135deg, #2c3e50 0%, #1a252f 100%); z-index:-1;"></div>');
        }
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perpustakaan Bayu</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,600;0,700;1,400&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/book-transition.css">
    <link rel="stylesheet" href="assets/css/public.css">
</head>
<body>

    <!-- Sticky Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom py-3 fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php" data-out="public-book-close" data-in="" onclick="sessionStorage.removeItem('splashShown');"><i class="bi bi-book-half me-2"></i>Perpus Bayu</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="bi bi-list fs-2"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center" id="nav-links-menu">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'index.php' ? 'active' : ''; ?>" href="<?php echo $current_page == 'index.php' ? '#' : 'index.php'; ?>" <?php echo $current_page == 'index.php' ? '' : 'data-out="page-backward-out" data-in="page-backward-in"'; ?>>Beranda<br>Utama</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#terbaru" <?php echo $current_page == 'index.php' ? '' : 'data-out="page-backward-out" data-in="page-backward-in"'; ?>>Koleksi<br>Terbaru</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#populer" <?php echo $current_page == 'index.php' ? '' : 'data-out="page-backward-out" data-in="page-backward-in"'; ?>>Buku<br>Populer</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#kontak" <?php echo $current_page == 'index.php' ? '' : 'data-out="page-backward-out" data-in="page-backward-in"'; ?>>Tentang<br>Kami</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'katalog.php' ? 'active' : ''; ?>" href="katalog.php" <?php echo $current_page == 'katalog.php' ? '' : 'data-out="page-forward-out" data-in="page-forward-in"'; ?>>Katalog<br>Lengkap</a>
                    </li>
                </ul>
                
                <!-- Inline Expanding Search -->
                <div class="nav-search-wrapper d-none d-lg-flex ms-3 me-3" id="navSearchWrapper">
                    <form action="katalog.php" method="GET" class="nav-search-form" id="navSearchForm">
                        <div class="nav-search-group" style="background: #1a252f;">
                            <input type="text" name="q" class="nav-search-input form-control border-0 ps-3" id="navSearchInput" placeholder="Ketik judul buku...">
                            
                            <button type="button" class="btn border-0 text-white-50 px-2" title="Filter Pencarian Lanjutan" data-bs-toggle="collapse" data-bs-target="#navFilterCollapse"><i class="bi bi-sliders"></i></button>
                            
                            <button type="submit" class="btn border-0 pe-3" style="color: #e6a756;"><i class="bi bi-search"></i></button>
                        </div>
                        
                        <!-- Advanced Filter Collapse -->
                        <div class="collapse w-100 mt-2" id="navFilterCollapse" style="position: absolute; top: 100%;">
                            <div class="card card-body border-0 shadow-lg" style="background-color: #fdfbf7; border-top: 3px solid #e6a756 !important; border-radius: 8px;">
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

                <a class="btn btn-outline-warning btn-sm px-4 rounded-pill fw-bold mt-3 mt-lg-0" href="admin/login.php" data-out="public-book-close" data-in="admin-book-open" style="border-color: #e6a756; color: #e6a756; position: relative; z-index: 10;">Admin</a>
            </div>
        </div>
    </nav>

