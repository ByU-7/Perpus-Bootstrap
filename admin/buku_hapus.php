<?php 
session_start();
if($_SESSION['status'] != "login"){
    header("location:login.php?pesan=belum_login");
    exit();
}

include '../config/koneksi.php';

$id = $_GET['id'];

if($id != ""){
    // Cek dan hapus file fisik gambar cover jika ada
    $data = mysqli_query($koneksi, "SELECT cover FROM buku WHERE id_buku='$id'");
    $d = mysqli_fetch_array($data);
    if($d && $d['cover'] != "" && file_exists('../uploads/covers/'.$d['cover'])){
        unlink('../uploads/covers/'.$d['cover']);
    }

    // Hapus dari tabel buku (Tabel buku_genre otomatis terhapus karena relasi ON DELETE CASCADE)
    mysqli_query($koneksi, "DELETE FROM buku WHERE id_buku='$id'");
}

header("location:buku.php?pesan=hapus");
?>
