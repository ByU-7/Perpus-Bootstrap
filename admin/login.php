<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Perpustakaan</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            background-color: white;
        }
    </style>
</head>
<body>

<div class="login-card">
    <h3 class="text-center mb-4">Login Admin</h3>
    
    <?php 
    if(isset($_GET['pesan'])){
        if($_GET['pesan'] == "gagal"){
            echo "<div class='alert alert-danger'>Login gagal! Username atau Password salah.</div>";
        } else if($_GET['pesan'] == "logout"){
            echo "<div class='alert alert-success'>Anda telah berhasil logout.</div>";
        } else if($_GET['pesan'] == "belum_login"){
            echo "<div class='alert alert-warning'>Anda harus login untuk mengakses halaman admin.</div>";
        }
    }
    ?>

    <form action="proses_login.php" method="POST">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" required placeholder="Masukkan username">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required placeholder="Masukkan password">
        </div>
        <button type="submit" class="btn btn-primary w-100 mt-3">Login</button>
    </form>
    <div class="text-center mt-3">
        <a href="../index.php" class="text-decoration-none">Kembali ke Halaman Pengunjung</a>
    </div>
</div>

<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
