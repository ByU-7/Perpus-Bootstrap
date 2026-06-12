<?php 
// Mengaktifkan session php
session_start();

// Menghapus semua session
session_destroy();

// Mengalihkan halaman ke halaman utama (pengunjung)
header("location:../index.php");
?>
