<?php
include 'config/koneksi.php';
$query = "CREATE TABLE IF NOT EXISTS saran (
    id_saran INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    pesan TEXT NOT NULL,
    tgl_kirim DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('belum_dibaca', 'sudah_dibaca') DEFAULT 'belum_dibaca'
)";
if(mysqli_query($koneksi, $query)) {
    echo "Tabel saran berhasil dibuat!\n";
} else {
    echo "Error: " . mysqli_error($koneksi) . "\n";
}
?>
