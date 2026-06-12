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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Perpustakaan</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <link rel="stylesheet" href="../assets/css/book-transition.css">
    <style>
        body {
            font-size: .875rem;
            background-color: #f8f9fa;
            overflow-x: hidden;
        }
        
        /* Rock-Solid Responsive Sidebar styling */
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
            background-color: #212529; /* bg-dark */
        }
        
        /* Main content offset */
        main {
            margin-left: 250px;
            /* removed width calc that caused overflow */
        }
        
        /* Desktop View */
        @media (min-width: 768px) {
            .sidebar {
                width: 250px;
                padding-top: 20px;
            }
        }

        /* Mobile View */
        @media (max-width: 767.98px) {
            .sidebar {
                top: 56px; /* Height of mobile navbar */
                padding: 0;
                width: 250px;
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
            }
            .sidebar.show {
                transform: translateX(0);
                box-shadow: 5px 0 15px rgba(0,0,0,0.5); /* Added shadow when open */
            }
            main {
                margin-left: 0;
            }
        }

        .sidebar .nav-link {
            font-weight: 500;
            color: #adb5bd;
            padding: 0.75rem 1rem;
            border-radius: 0.25rem;
            margin: 0 0.8rem 0.2rem 0.8rem;
        }

        .sidebar .nav-link:hover {
            color: #fff;
            background-color: rgba(255,255,255,0.1);
        }

        .sidebar .nav-link.active {
            color: #fff;
            background-color: #0d6efd;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>

<!-- Mobile Header (Visible only on small screens) -->
<header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow d-md-none" style="height: 56px;">
  <button class="navbar-toggler d-md-none collapsed border-0 ms-2" type="button" onclick="document.getElementById('sidebarMenu').classList.toggle('show');">
    <span class="navbar-toggler-icon"></span>
  </button>
  <a class="navbar-brand col-md-3 col-lg-2 me-auto px-3 fs-5 fw-bold" href="index.php">
      <i class="bi bi-book-half text-primary"></i> Perpus Admin
  </a>
</header>

<div class="container-fluid p-0">
    <!-- Sidebar -->
    <nav id="sidebarMenu" class="sidebar">
        <div class="position-sticky text-white h-100 overflow-auto">
            <div class="px-3 mb-4 d-none d-md-block text-center border-bottom border-secondary pb-4">
                <span class="fs-4 fw-bold"><i class="bi bi-book-half text-primary"></i> Perpus Admin</span>
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
            </ul>

            <hr class="border-secondary mx-3 mt-4">
            <div class="px-3 pb-4">
                <div class="d-flex align-items-center mb-3 text-white">
                    <img src="https://github.com/mdo.png" alt="Admin" width="32" height="32" class="rounded-circle me-2">
                    <span class="text-truncate"><strong><?php echo $_SESSION['admin_nama']; ?></strong></span>
                </div>
                <a href="logout.php" class="btn btn-danger btn-sm w-100"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </div>
        </div>
    </nav>

    <!-- Main Content Area -->
    <main class="py-4 px-3 px-md-4">
