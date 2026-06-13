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

    // Upload Foto
    $query_update = "UPDATE anggota SET 
                            nim_nik='$nim_nik', 
                            nama_anggota='$nama', 
                            jk='$jk', 
                            alamat='$alamat', 
                            no_telp='$no_telp', 
                            status_keanggotaan='$status'";

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
                $query_update .= ", foto='$nama_file'";
            } else {
                echo "<script>alert('Ukuran foto terlalu besar! Max 2MB'); window.history.back();</script>";
                exit();
            }
        } else {
            echo "<script>alert('Ekstensi foto tidak diperbolehkan! Harus JPG/PNG.'); window.history.back();</script>";
            exit();
        }
    }

    $query_update .= " WHERE id_anggota='$id'";
    mysqli_query($koneksi, $query_update);
    
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
        <form method="POST" action="" enctype="multipart/form-data">
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

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Status Keanggotaan</label>
                    <select class="form-select" name="status" required>
                        <option value="Regular" <?php if($d['status_keanggotaan'] == 'Regular'){ echo 'selected'; } ?>>Regular</option>
                        <option value="Khusus" <?php if($d['status_keanggotaan'] == 'Khusus'){ echo 'selected'; } ?>>Khusus</option>
                    </select>
                </div>
                <div class="col-md-6 mb-4">
                    <label class="form-label">Ganti Foto Profil (Opsional, Max 2MB)</label>
                    <div class="d-flex align-items-center mb-2">
                        <?php 
                        $fallback_img = "https://ui-avatars.com/api/?name=".urlencode($d['nama_anggota'])."&background=c2593b&color=fff&size=150";
                        $foto_src = ($d['foto'] != '' && file_exists('../uploads/anggota/'.$d['foto'])) ? '../uploads/anggota/'.$d['foto'] : $fallback_img;
                        ?>
                        <img id="preview" src="<?php echo $foto_src; ?>" alt="Preview" class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover; border: 1px solid #ddd;">
                        <input type="file" class="form-control" name="foto" accept="image/png, image/jpeg" onchange="previewImage(this);">
                    </div>
                </div>
            </div>

            <button type="submit" name="update" class="btn btn-warning"><i class="bi bi-save"></i> Update Data</button>
        </form>
    </div>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php include 'footer.php'; ?>

