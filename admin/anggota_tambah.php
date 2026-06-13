<?php 
include 'header.php'; 
include '../config/koneksi.php';

// Logika Simpan
if(isset($_POST['simpan'])){
    $nim_nik = mysqli_real_escape_string($koneksi, $_POST['nim_nik']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $jk = mysqli_real_escape_string($koneksi, $_POST['jk']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $no_telp = mysqli_real_escape_string($koneksi, $_POST['no_telp']);
    $status = mysqli_real_escape_string($koneksi, $_POST['status']);
    $tgl_daftar = date('Y-m-d'); // Tanggal hari ini
    
    // Upload Foto
    $nama_file = "";
    if($_FILES['foto']['name'] != ''){
        $ekstensi_diperbolehkan = array('png','jpg','jpeg');
        $nama_file_asli = $_FILES['foto']['name'];
        $x = explode('.', $nama_file_asli);
        $ekstensi = strtolower(end($x));
        $ukuran = $_FILES['foto']['size'];
        $file_tmp = $_FILES['foto']['tmp_name'];    
        
        if(in_array($ekstensi, $ekstensi_diperbolehkan) === true){
            if($ukuran < 2048000){ // Max 2MB
                $nama_file = time() . '_' . $nama_file_asli;
                move_uploaded_file($file_tmp, '../uploads/anggota/'.$nama_file);
            } else {
                echo "<script>alert('Ukuran foto terlalu besar! Max 2MB'); window.history.back();</script>";
                exit();
            }
        } else {
            echo "<script>alert('Ekstensi foto tidak diperbolehkan! Harus JPG/PNG.'); window.history.back();</script>";
            exit();
        }
    }

    mysqli_query($koneksi, "INSERT INTO anggota (nim_nik, nama_anggota, jk, alamat, no_telp, tgl_daftar, status_keanggotaan, foto) 
                            VALUES ('$nim_nik', '$nama', '$jk', '$alamat', '$no_telp', '$tgl_daftar', '$status', '$nama_file')");
    
    // Redirect menggunakan script JavaScript karena header() terkadang bentrok kalau ada output HTML sebelumnya
    echo "<script>window.location.href='anggota.php?pesan=simpan';</script>";
    exit();
}
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Tambah Data Anggota</h2>
    <a href="anggota.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">NIM / NIK</label>
                    <input type="text" class="form-control" name="nim_nik" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" name="nama" required>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Jenis Kelamin</label>
                    <select class="form-select" name="jk" required>
                        <option value="">-- Pilih Jenis Kelamin --</option>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">No. Telepon / WA</label>
                    <input type="text" class="form-control" name="no_telp" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Alamat Lengkap</label>
                <textarea class="form-control" name="alamat" rows="3" required></textarea>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Status Keanggotaan</label>
                    <select class="form-select" name="status" required>
                        <option value="Regular">Regular</option>
                        <option value="Khusus">Khusus</option>
                    </select>
                </div>
                <div class="col-md-6 mb-4">
                    <label class="form-label">Foto Profil (Opsional, JPG/PNG Max 2MB)</label>
                    <div class="mb-2">
                        <img id="preview" src="#" alt="Preview" class="rounded shadow-sm d-none" style="max-height: 150px; width: 150px; object-fit: cover;">
                    </div>
                    <input type="file" class="form-control" name="foto" accept="image/png, image/jpeg" onchange="previewImage(this);">
                </div>
            </div>

            <button type="submit" name="simpan" class="btn btn-primary"><i class="bi bi-save"></i> Simpan Data</button>
        </form>
    </div>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview').src = e.target.result;
            document.getElementById('preview').classList.remove('d-none');
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php include 'footer.php'; ?>
