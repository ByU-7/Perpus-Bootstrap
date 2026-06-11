<?php 
session_start();
if($_SESSION['status'] != "login"){
    header("location:login.php?pesan=belum_login");
    exit();
}

include '../config/koneksi.php';

// Menangkap ID dari URL
$id = $_GET['id'];

// Cek apakah ID ada
if($id != ""){
    // Menghapus data dari tabel anggota
    mysqli_query($koneksi, "DELETE FROM anggota WHERE id_anggota='$id'");
}

// Mengalihkan halaman kembali ke anggota.php
header("location:anggota.php?pesan=hapus");
?>
