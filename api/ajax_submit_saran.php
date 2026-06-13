<?php
include '../config/koneksi.php';

header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $pesan = mysqli_real_escape_string($koneksi, $_POST['pesan']);

    if(empty($nama) || empty($email) || empty($pesan)) {
        echo json_encode(['status' => 'error', 'message' => 'Semua kolom harus diisi.']);
        exit;
    }

    $query = "INSERT INTO saran (nama, email, pesan) VALUES ('$nama', '$email', '$pesan')";
    
    if(mysqli_query($koneksi, $query)) {
        echo json_encode(['status' => 'success', 'message' => 'Terima kasih atas saran dan masukan Anda!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan sistem.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Metode tidak diizinkan.']);
}
?>
