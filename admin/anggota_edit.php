<?php 
include 'header.php'; 
include '../config/koneksi.php';

// Menangkap ID dari URL
$id = $_GET['id'];

// Mengambil data anggota berdasarkan ID
$data = mysqli_query($koneksi, "SELECT * FROM anggota WHERE id_anggota='$id'");
$d = mysqli_fetch_array($data);

// Jika data tidak ditemukan, kembali ke halaman anggota
if(!$d){
    echo "<script>window.location.href='anggota.php';</script>";
    exit();
}

// Logika Update
if(isset($_POST['update'])){
    $nim_nik = mysqli_real_escape_string($koneksi, $_POST['nim_nik']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $jk = mysqli_real_escape_string($koneksi, $_POST['jk']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $no_telp = mysqli_real_escape_string($koneksi, $_POST['no_telp']);
    $status = mysqli_real_escape_string($koneksi, $_POST['status']);

    mysqli_query($koneksi, "UPDATE anggota SET 
                            nim_nik='$nim_nik', 
                            nama_anggota='$nama', 
                            jk='$jk', 
                            alamat='$alamat', 
                            no_telp='$no_telp', 
                            status_keanggotaan='$status' 
                            WHERE id_anggota='$id'");
    
    echo "<script>window.location.href='anggota.php?pesan=simpan';</script>";
    exit();
}
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Edit Data Anggota</h2>
    <a href="anggota.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">NIM / NIK</label>
                    <input type="text" class="form-control" name="nim_nik" value="<?php echo $d['nim_nik']; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" name="nama" value="<?php echo $d['nama_anggota']; ?>" required>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Jenis Kelamin</label>
                    <select class="form-select" name="jk" required>
                        <option value="L" <?php if($d['jk'] == 'L'){ echo 'selected'; } ?>>Laki-laki</option>
                        <option value="P" <?php if($d['jk'] == 'P'){ echo 'selected'; } ?>>Perempuan</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">No. Telepon / WA</label>
                    <input type="text" class="form-control" name="no_telp" value="<?php echo $d['no_telp']; ?>" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Alamat Lengkap</label>
                <textarea class="form-control" name="alamat" rows="3" required><?php echo $d['alamat']; ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Status Keanggotaan</label>
                <select class="form-select" name="status" required>
                    <option value="Regular" <?php if($d['status_keanggotaan'] == 'Regular'){ echo 'selected'; } ?>>Regular</option>
                    <option value="Khusus" <?php if($d['status_keanggotaan'] == 'Khusus'){ echo 'selected'; } ?>>Khusus</option>
                </select>
            </div>

            <button type="submit" name="update" class="btn btn-warning"><i class="bi bi-save"></i> Update Data</button>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
