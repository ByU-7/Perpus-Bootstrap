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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Perpus Bayu</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,700;1,400&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/book-transition.css">
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #fdfbf7; color: #333; overflow-x: hidden; }
        .serif-font { font-family: 'Lora', serif; }
        
        /* Split Layout */
        .login-split { min-height: 100vh; display: flex; flex-wrap: wrap; }
        .login-left {
            flex: 1; min-width: 300px;
            background: linear-gradient(rgba(26, 37, 47, 0.8), rgba(26, 37, 47, 0.9)), url('https://images.unsplash.com/photo-1541963463532-d68292c34b19?auto=format&fit=crop&w=1000&q=80') center/cover;
            display: flex; flex-direction: column; justify-content: center; align-items: center; color: white; text-align: center; padding: 40px;
        }
        .login-right {
            flex: 1; min-width: 300px; display: flex; align-items: center; justify-content: center; padding: 40px; background-color: #fdfbf7;
        }
        
        .login-form-container { width: 100%; max-width: 400px; margin: 0 auto; }
        @keyframes floatIcon { 0% { transform: translateY(0); } 50% { transform: translateY(-15px); } 100% { transform: translateY(0); } }
        .float-animation { animation: floatIcon 3s ease-in-out infinite; }
        
        .form-control { border: 1px solid #ced4da; padding: 12px 15px; border-radius: 8px; }
        .form-control:focus { border-color: #b8975a; box-shadow: 0 0 0 0.25rem rgba(184, 151, 90, 0.25); }
        .btn-gold { background-color: #b8975a; color: white; border: none; padding: 12px; border-radius: 8px; font-weight: 600; transition: all 0.3s; }
        .btn-gold:hover { background-color: #a0814a; color: white; transform: translateY(-2px); }
    </style>
</head>
<body>

<div class="login-split">
    <!-- Kolom Kiri: Gambar & Slogan -->
    <div class="login-left d-none d-lg-flex">
        <i class="bi bi-book-half float-animation" style="font-size: 5rem; color: #b8975a; margin-bottom: 20px;"></i>
        <h1 class="serif-font fw-bold mb-3">Perpus Bayu</h1>
        <p class="lead" style="max-width: 400px; color: #e9ecef;">"Mengelola Pengetahuan, Membangun Masa Depan"</p>
        <div style="margin-top: 50px; width: 60px; height: 3px; background-color: #b8975a;"></div>
    </div>

    <!-- Kolom Kanan: Form Login -->
    <div class="login-right">
        <div class="login-form-container">
            <div class="text-center mb-5 d-lg-none">
                <i class="bi bi-book-half text-warning" style="font-size: 4rem;"></i>
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
                <button type="submit" class="btn btn-gold w-100 shadow-sm"><i class="bi bi-box-arrow-in-right me-2"></i> MASUK KE DASHBOARD</button>
            </form>
            
            <div class="text-center mt-4">
                <a href="../index.php" class="btn btn-outline-secondary w-100 rounded-pill"><i class="bi bi-arrow-left me-2"></i> Kembali ke Beranda Publik</a>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/book-transition.js"></script>
</body>
</html>
