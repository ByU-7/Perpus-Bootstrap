<?php 
// Mengaktifkan session php
session_start();

// Menghapus semua session
session_destroy();

// Mengalihkan halaman ke halaman login dengan pesan logout
header("location:login.php?pesan=logout");
?>
