<?php
session_start();
include '../config/koneksi.php';

if(isset($_SESSION['status']) && $_SESSION['status'] == "login"){
    header("location:index.php");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <script>
        if (sessionStorage.getItem('incomingTransition')) {
            document.write('<div id="anti-flicker-overlay" style="position:fixed; top:0; left:0; width:100vw; height:100vh; background:#1a252f; z-index:9999999;"></div>');
        }
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Perpus Bayu</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,700;1,400&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/book-transition.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="login-body">

<div class="login-split">
    <!-- Kolom Kiri: Gambar & Slogan -->
    <div class="login-left d-none d-lg-flex">
        <i class="bi bi-book-half float-animation" style="font-size: 5rem; color: #a8452c; margin-bottom: 20px;"></i>
        <h1 class="serif-font fw-bold mb-3">Perpus Bayu</h1>
        <p class="lead" style="max-width: 400px; color: #e9ecef;">"Mengelola Pengetahuan, Membangun Masa Depan"</p>
        <div style="margin-top: 50px; width: 60px; height: 3px; background-color: #a8452c;"></div>
    </div>

    <!-- Kolom Kanan: Form Login -->
    <div class="login-right">
        <div class="login-form-container">
            <div class="text-center mb-5 d-lg-none">
                <i class="bi bi-book-half" style="font-size: 4rem; color: #a8452c;"></i>
                <h2 class="serif-font fw-bold mt-2">Perpus Bayu</h2>
            </div>

            <div class="mb-5">
                <h3 class="serif-font fw-bold" style="color: #1a252f;">Selamat Datang Kembali</h3>
                <p class="text-muted">Silakan masukkan kredensial administrator Anda.</p>
            </div>

            <?php 
            if(isset($_GET['pesan'])){
                if($_GET['pesan'] == "gagal"){
                    echo "<div class='alert alert-danger border-0 shadow-sm'><i class='bi bi-exclamation-circle me-2'></i>Username atau Password salah!</div>";
                } else if($_GET['pesan'] == "logout"){
                    echo "<div class='alert alert-success border-0 shadow-sm'><i class='bi bi-check-circle me-2'></i>Anda telah berhasil logout.</div>";
                } else if($_GET['pesan'] == "belum_login"){
                    echo "<div class='alert alert-warning border-0 shadow-sm'><i class='bi bi-exclamation-triangle me-2'></i>Anda harus login terlebih dahulu!</div>";
                }
            }
            ?>

            <form action="proses_login.php" method="POST">
                <div class="mb-4">
                    <label class="form-label fw-bold text-muted small text-uppercase">Username</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-person text-muted"></i></span>
                        <input type="text" name="username" class="form-control border-start-0" placeholder="Masukkan username" required autofocus>
                    </div>
                </div>
                <div class="mb-5">
                    <label class="form-label fw-bold text-muted small text-uppercase">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-lock text-muted"></i></span>
                        <input type="password" name="password" class="form-control border-start-0" placeholder="Masukkan password" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-terracotta w-100 shadow-sm"><i class="bi bi-box-arrow-in-right me-2"></i> MASUK KE DASHBOARD</button>
            </form>
            
            <div class="text-center mt-4">
                <a href="../index.php" class="btn btn-outline-secondary w-100 rounded-pill" data-out="admin-book-close" data-in="public-book-open"><i class="bi bi-arrow-left me-2"></i> Kembali ke Beranda Publik</a>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/book-transition.js"></script>
</body>
</html>

