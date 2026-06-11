<?php 
// Mengaktifkan session php
session_start();

// Menghubungkan dengan koneksi database
include '../config/koneksi.php';

// Menangkap data yang dikirim dari form login
$username = mysqli_real_escape_string($koneksi, $_POST['username']);
$password = md5($_POST['password']); // Menggunakan MD5 sesuai konfigurasi SQL kita

// Menyeleksi data admin dengan username dan password yang sesuai
$data = mysqli_query($koneksi, "SELECT * FROM admin WHERE username='$username' AND password='$password'");

// Menghitung jumlah data yang ditemukan
$cek = mysqli_num_rows($data);

if($cek > 0){
    $admin = mysqli_fetch_assoc($data);
    
    // Menyimpan data ke session
    $_SESSION['admin_id'] = $admin['id_admin'];
    $_SESSION['admin_nama'] = $admin['nama_lengkap'];
    $_SESSION['status'] = "login";
    
    // Alihkan ke halaman dashboard admin
    header("location:index.php");
}else{
    // Alihkan kembali ke halaman login dengan pesan gagal
    header("location:login.php?pesan=gagal");
}
?>
