<?php
// config/koneksi.php

$server   = "localhost";
$user     = "root";
$pass     = "";
// Nama database yang akan kita buat
$database = "db_perpus_bootstrap";

$koneksi  = mysqli_connect($server, $user, $pass, $database);

// Cek koneksi
if (mysqli_connect_errno()) {
    echo "Koneksi database gagal: " . mysqli_connect_error();
    exit();
}
?>
