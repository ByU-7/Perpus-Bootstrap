<?php
session_start();
include '../../config/koneksi.php';

header('Content-Type: application/json');

// Cek autentikasi sederhana (harus sudah login)
if(!isset($_SESSION['status']) || $_SESSION['status'] != "login"){
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$action = isset($_GET['action']) ? $_GET['action'] : '';

if($action == 'get') {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    // Ambil data buku
    $query = mysqli_query($koneksi, "SELECT * FROM buku WHERE id_buku = '$id'");
    $buku = mysqli_fetch_assoc($query);
    
    if(!$buku) {
        echo json_encode(['status' => 'error', 'message' => 'Buku tidak ditemukan']);
        exit();
    }
    
    // Ambil genre yang terkait
    $genres = [];
    $q_genre = mysqli_query($koneksi, "SELECT id_genre FROM buku_genre WHERE id_buku = '$id'");
    while($g = mysqli_fetch_assoc($q_genre)) {
        $genres[] = $g['id_genre'];
    }
    
    $buku['genres'] = $genres;
    
    echo json_encode(['status' => 'success', 'data' => $buku]);
    exit();
}

if($action == 'save') {
    $id_buku = isset($_POST['id_buku']) ? mysqli_real_escape_string($koneksi, $_POST['id_buku']) : '';
    
    $kode = mysqli_real_escape_string($koneksi, $_POST['kode_buku']);
    $judul = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $pengarang = mysqli_real_escape_string($koneksi, $_POST['pengarang']);
    $penerbit = mysqli_real_escape_string($koneksi, $_POST['penerbit']);
    $tahun = mysqli_real_escape_string($koneksi, $_POST['tahun']);
    $stok = mysqli_real_escape_string($koneksi, $_POST['stok']);
    $sinopsis = mysqli_real_escape_string($koneksi, $_POST['sinopsis']);
    $genres = isset($_POST['genre']) ? $_POST['genre'] : []; // Array
    
    // Jika array genre dikirim sebagai string comma separated dari Select2, kita pecah
    if(!is_array($genres) && !empty($genres)) {
        $genres = explode(',', $genres);
    }
    
    $nama_file = "";
    $upload_sukses = true;
    $pesan_error = "";
    
    // Cek jika ada upload cover
    if(isset($_FILES['cover']) && $_FILES['cover']['name'] != ''){
        $ekstensi_diperbolehkan = array('png','jpg','jpeg');
        $nama_file_asli = $_FILES['cover']['name'];
        $x = explode('.', $nama_file_asli);
        $ekstensi = strtolower(end($x));
        $ukuran = $_FILES['cover']['size'];
        $file_tmp = $_FILES['cover']['tmp_name'];

        if(in_array($ekstensi, $ekstensi_diperbolehkan)){
            if($ukuran < 2048000){ // Max 2MB
                $nama_file = time() . '_' . $nama_file_asli;
                move_uploaded_file($file_tmp, '../uploads/covers/'.$nama_file);
            } else {
                $upload_sukses = false;
                $pesan_error = "Ukuran cover max 2MB.";
            }
        } else {
            $upload_sukses = false;
            $pesan_error = "Ekstensi cover harus JPG/PNG.";
        }
    }
    
    if(!$upload_sukses) {
        echo json_encode(['status' => 'error', 'message' => $pesan_error]);
        exit();
    }
    
    if($id_buku == '') {
        // INSERT
        $q = mysqli_query($koneksi, "INSERT INTO buku (kode_buku, judul_buku, pengarang, penerbit, tahun_terbit, stok, cover, sinopsis) 
                                     VALUES ('$kode', '$judul', '$pengarang', '$penerbit', '$tahun', '$stok', '$nama_file', '$sinopsis')");
        if(!$q) {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan buku: ' . mysqli_error($koneksi)]);
            exit();
        }
        $new_id = mysqli_insert_id($koneksi);
        
        // Insert Genre
        if(!empty($genres) && is_array($genres)){
            foreach($genres as $id_genre){
                mysqli_query($koneksi, "INSERT INTO buku_genre (id_buku, id_genre) VALUES ('$new_id', '$id_genre')");
            }
        }
        echo json_encode(['status' => 'success', 'message' => 'Buku berhasil ditambahkan']);
    } else {
        // UPDATE
        // Ambil data buku lama untuk cek cover
        $q_lama = mysqli_query($koneksi, "SELECT cover FROM buku WHERE id_buku='$id_buku'");
        $lama = mysqli_fetch_assoc($q_lama);
        
        $sql_update = "UPDATE buku SET 
                       kode_buku='$kode', 
                       judul_buku='$judul', 
                       pengarang='$pengarang', 
                       penerbit='$penerbit', 
                       tahun_terbit='$tahun', 
                       stok='$stok',
                       sinopsis='$sinopsis'";
                       
        if($nama_file != "") {
            $sql_update .= ", cover='$nama_file'";
            // Hapus file lama
            if(!empty($lama['cover']) && file_exists('../uploads/covers/'.$lama['cover'])) {
                unlink('../uploads/covers/'.$lama['cover']);
            }
        }
        $sql_update .= " WHERE id_buku='$id_buku'";
        
        $q = mysqli_query($koneksi, $sql_update);
        if(!$q) {
            echo json_encode(['status' => 'error', 'message' => 'Gagal mengupdate buku: ' . mysqli_error($koneksi)]);
            exit();
        }
        
        // Update Genre
        mysqli_query($koneksi, "DELETE FROM buku_genre WHERE id_buku='$id_buku'");
        if(!empty($genres) && is_array($genres)){
            foreach($genres as $id_genre){
                mysqli_query($koneksi, "INSERT INTO buku_genre (id_buku, id_genre) VALUES ('$id_buku', '$id_genre')");
            }
        }
        echo json_encode(['status' => 'success', 'message' => 'Buku berhasil diupdate']);
    }
    exit();
}

if($action == 'delete') {
    $id_buku = isset($_POST['id']) ? intval($_POST['id']) : 0;
    
    // Ambil cover lama
    $q_lama = mysqli_query($koneksi, "SELECT cover FROM buku WHERE id_buku='$id_buku'");
    $lama = mysqli_fetch_assoc($q_lama);
    
    // Hapus dari buku_genre
    mysqli_query($koneksi, "DELETE FROM buku_genre WHERE id_buku='$id_buku'");
    
    // Hapus buku
    $q = mysqli_query($koneksi, "DELETE FROM buku WHERE id_buku='$id_buku'");
    
    if($q) {
        // Hapus file fisik cover
        if(!empty($lama['cover']) && file_exists('../uploads/covers/'.$lama['cover'])) {
            unlink('../uploads/covers/'.$lama['cover']);
        }
        echo json_encode(['status' => 'success', 'message' => 'Buku berhasil dihapus']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus buku: ' . mysqli_error($koneksi)]);
    }
    exit();
}

echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
