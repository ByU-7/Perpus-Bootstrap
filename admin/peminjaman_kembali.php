<?php 
session_start();
if($_SESSION['status'] != "login"){
    header("location:login.php?pesan=belum_login");
    exit();
}
include '../config/koneksi.php';

$id = $_GET['id'];

if($id != ""){
    // Ambil data peminjaman
    $query = mysqli_query($koneksi, "SELECT * FROM peminjaman WHERE id_pinjam='$id' AND status='Dipinjam'");
    $d = mysqli_fetch_array($query);
    
    if($d){
        $id_buku = $d['id_buku'];
        $tgl_tenggat = $d['tgl_kembali_seharusnya'];
        $tgl_kembali_skrg = date('Y-m-d');
        
        // Hitung denda: 1000 per hari keterlambatan
        $denda = 0;
        if(strtotime($tgl_kembali_skrg) > strtotime($tgl_tenggat)){
            $selisih_hari = round((strtotime($tgl_kembali_skrg) - strtotime($tgl_tenggat)) / (60 * 60 * 24));
            $denda = $selisih_hari * 1000;
        }

        // Update status peminjaman menjadi Kembali, catat tgl kembali dan denda
        mysqli_query($koneksi, "UPDATE peminjaman SET 
                                status='Kembali', 
                                tgl_dikembalikan='$tgl_kembali_skrg', 
                                denda='$denda' 
                                WHERE id_pinjam='$id'");
                                
        // Kembalikan (tambah) stok buku
        mysqli_query($koneksi, "UPDATE buku SET stok = stok + 1 WHERE id_buku='$id_buku'");
    }
}

header("location:peminjaman.php?pesan=kembali");
?>
