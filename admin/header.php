<?php
if(session_status() == PHP_SESSION_NONE) {
    session_start();
}
if($_SESSION['status'] != "login"){
    header("location:login.php?pesan=belum_login");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script>
        if (sessionStorage.getItem('incomingTransition')) {
            document.write('<style id="anti-flicker-style">body > *:not(#book-transition):not(script) { opacity: 0 !important; visibility: hidden !important; }</style>');
            document.write('<div id="anti-flicker-overlay" style="position:fixed; top:0; left:0; width:100vw; height:100vh; background:linear-gradient(135deg, #2c3e50 0%, #1a252f 100%); z-index:-1;"></div>');
        }
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Perpustakaan</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <link rel="stylesheet" href="../assets/css/book-transition.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<!-- Mobile Header (Visible only on small screens) -->
<header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow d-md-none" style="height: 56px; border-bottom: 1px solid rgba(255,255,255,0.1);">
  <button class="navbar-toggler d-md-none collapsed border-0 ms-2" type="button" onclick="document.getElementById('sidebarMenu').classList.toggle('show');">
    <span class="navbar-toggler-icon"></span>
  </button>
  <a class="navbar-brand col-md-3 col-lg-2 me-auto px-3 fs-5 fw-bold" href="index.php">
      <i class="bi bi-book-half text-primary"></i> Admin Perpus Bayu
  </a>
</header>

<div class="container-fluid p-0">
    <!-- Sidebar -->
    <nav id="sidebarMenu" class="sidebar">
        <div class="position-sticky text-white h-100 overflow-auto">
            <div class="px-3 mb-4 d-none d-md-block text-center border-bottom border-secondary pb-4">
                <span class="fs-4 fw-bold"><i class="bi bi-book-half text-primary"></i>Admin Perpus Bayu</span>
            </div>
            
            <ul class="nav flex-column mb-auto mt-3 mt-md-0">
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="index.php">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo strpos(basename($_SERVER['PHP_SELF']), 'anggota') !== false ? 'active' : ''; ?>" href="anggota.php">
                        <i class="bi bi-people me-2"></i> Data Anggota
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo strpos(basename($_SERVER['PHP_SELF']), 'buku') !== false ? 'active' : ''; ?>" href="buku.php">
                        <i class="bi bi-journal-text me-2"></i> Data Buku
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo strpos(basename($_SERVER['PHP_SELF']), 'peminjaman') !== false ? 'active' : ''; ?>" href="peminjaman.php">
                        <i class="bi bi-arrow-left-right me-2"></i> Peminjaman
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo strpos(basename($_SERVER['PHP_SELF']), 'saran') !== false ? 'active' : ''; ?>" href="saran.php">
                        <i class="bi bi-inbox-fill me-2"></i> Kotak Saran
                    </a>
                </li>
            </ul>

            <hr class="border-secondary mx-3 mt-4">
            <div class="px-3 pb-4">
                <div class="d-flex align-items-center mb-3 text-white">
                    <img src="https://github.com/mdo.png" alt="Admin" width="32" height="32" class="rounded me-2">
                    <span class="text-truncate"><strong><?php echo $_SESSION['admin_nama']; ?></strong></span>
                </div>
                <div class="px-3 pb-3 mt-auto">
                    <a href="logout.php" class="btn btn-danger btn-sm w-100" data-out="admin-book-close" data-in="public-book-open"><i class="bi bi-box-arrow-right"></i> Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Area -->
    <main class="py-4 px-3 px-md-4">


