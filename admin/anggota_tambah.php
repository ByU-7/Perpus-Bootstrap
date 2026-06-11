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

    mysqli_query($koneksi, "INSERT INTO anggota (nim_nik, nama_anggota, jk, alamat, no_telp, tgl_daftar, status_keanggotaan) 
                            VALUES ('$nim_nik', '$nama', '$jk', '$alamat', '$no_telp', '$tgl_daftar', '$status')");
    
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
        <form method="POST" action="">
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

            <div class="mb-3">
                <label class="form-label">Status Keanggotaan</label>
                <select class="form-select" name="status" required>
                    <option value="Regular">Regular</option>
                    <option value="Khusus">Khusus</option>
                </select>
            </div>

            <button type="submit" name="simpan" class="btn btn-primary"><i class="bi bi-save"></i> Simpan Data</button>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
