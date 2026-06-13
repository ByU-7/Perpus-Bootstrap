<?php 
include 'header.php'; 
include '../config/koneksi.php';

$id = $_GET['id'];
$data = mysqli_query($koneksi, "SELECT * FROM buku WHERE id_buku='$id'");
$d = mysqli_fetch_array($data);

if(!$d){
    echo "<script>window.location.href='buku.php';</script>";
    exit();
}

// Mengambil data genre untuk dropdown
$data_genre = mysqli_query($koneksi, "SELECT * FROM genre ORDER BY nama_genre ASC");

// Mengambil genre yang sudah dipilih sebelumnya
$selected_genres = [];
$sg_query = mysqli_query($koneksi, "SELECT id_genre FROM buku_genre WHERE id_buku='$id'");
while($sg = mysqli_fetch_assoc($sg_query)){
    $selected_genres[] = $sg['id_genre'];
}

// Logika Update
if(isset($_POST['update'])){
    $kode = mysqli_real_escape_string($koneksi, $_POST['kode_buku']);
    $judul = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $pengarang = mysqli_real_escape_string($koneksi, $_POST['pengarang']);
    $penerbit = mysqli_real_escape_string($koneksi, $_POST['penerbit']);
    $tahun = mysqli_real_escape_string($koneksi, $_POST['tahun']);
    $stok = mysqli_real_escape_string($koneksi, $_POST['stok']);
    $sinopsis = mysqli_real_escape_string($koneksi, $_POST['sinopsis']);
    $genres = isset($_POST['genre']) ? $_POST['genre'] : [];
    
    $nama_file = $d['cover']; // Default pakai cover lama

    // Jika ada file cover baru yang diupload
    if(isset($_FILES['cover']) && $_FILES['cover']['name'] != ''){
        $ekstensi_diperbolehkan = array('png','jpg','jpeg');
        $nama_file_asli = $_FILES['cover']['name'];
        $x = explode('.', $nama_file_asli);
        $ekstensi = strtolower(end($x));
        $ukuran = $_FILES['cover']['size'];
        $file_tmp = $_FILES['cover']['tmp_name'];

        if(in_array($ekstensi, $ekstensi_diperbolehkan) === true){
            if($ukuran < 2048000){ // Max 2MB
                // Hapus cover lama jika ada file fisiknya
                if($d['cover'] != "" && file_exists('../uploads/covers/'.$d['cover'])){
                    unlink('../uploads/covers/'.$d['cover']);
                }

                $nama_file = time() . '_' . $nama_file_asli;
                move_uploaded_file($file_tmp, '../uploads/covers/'.$nama_file);
            } else {
                echo "<script>alert('Ukuran file cover terlalu besar! Max 2MB'); window.history.back();</script>";
                exit();
            }
        } else {
            echo "<script>alert('Ekstensi file cover tidak diperbolehkan! Harus JPG/PNG.'); window.history.back();</script>";
            exit();
        }
    }

    mysqli_query($koneksi, "UPDATE buku SET 
                            kode_buku='$kode', 
                            judul_buku='$judul', 
                            pengarang='$pengarang', 
                            penerbit='$penerbit', 
                            tahun_terbit='$tahun', 
                            stok='$stok',
                            sinopsis='$sinopsis',
                            cover='$nama_file'
                            WHERE id_buku='$id'");
                            
    // Update Pivot Tabel Genre (Hapus yang lama, insert yang baru)
    mysqli_query($koneksi, "DELETE FROM buku_genre WHERE id_buku='$id'");
    if(!empty($genres)){
        foreach($genres as $id_genre){
            mysqli_query($koneksi, "INSERT INTO buku_genre (id_buku, id_genre) VALUES ('$id', '$id_genre')");
        }
    }
    
    echo "<script>window.location.href='buku.php?pesan=simpan';</script>";
    exit();
}
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Edit Data Buku</h2>
    <a href="buku.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Kode Buku</label>
                    <input type="text" class="form-control" name="kode_buku" value="<?php echo $d['kode_buku']; ?>" required>
                </div>
                <div class="col-md-8 mb-3">
                    <label class="form-label">Judul Buku</label>
                    <input type="text" class="form-control" name="judul" value="<?php echo $d['judul_buku']; ?>" required>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Pengarang</label>
                    <input type="text" class="form-control" name="pengarang" value="<?php echo $d['pengarang']; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Penerbit</label>
                    <input type="text" class="form-control" name="penerbit" value="<?php echo $d['penerbit']; ?>" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tahun Terbit</label>
                    <input type="number" class="form-control" name="tahun" value="<?php echo $d['tahun_terbit']; ?>" min="1900" max="2099" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Jumlah Stok</label>
                    <input type="number" class="form-control" name="stok" value="<?php echo $d['stok']; ?>" min="0" required>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label fw-bold">Sinopsis / Deskripsi Buku</label>
                    <textarea class="form-control" name="sinopsis" rows="4" placeholder="Tuliskan sinopsis singkat buku ini..."><?php echo htmlspecialchars($d['sinopsis']); ?></textarea>
                </div>
            </div>

            <div class="row border-top pt-4 mt-3">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Daftar Genre Buku</label>
                    <div class="text-muted small mb-2"><i class="bi bi-info-circle"></i> Anda bisa memilih lebih dari satu genre. Ketik untuk mencari.</div>
                    <select class="form-select select2" name="genre[]" multiple="multiple" data-placeholder="Ketik atau pilih genre..." required>
                        <?php while($g = mysqli_fetch_array($data_genre)): ?>
                            <option value="<?php echo $g['id_genre']; ?>" <?php if(in_array($g['id_genre'], $selected_genres)) echo 'selected'; ?>><?php echo $g['nama_genre']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Ganti Cover Buku (Opsional)</label>
                    <div class="mb-2" id="cover-container">
                        <?php if($d['cover'] != '' && file_exists('../uploads/covers/'.$d['cover'])): ?>
                            <img id="preview" src="../uploads/covers/<?php echo $d['cover']; ?>" class="img-thumbnail shadow-sm rounded" style="max-height: 250px;">
                        <?php else: ?>
                            <img id="preview" src="#" alt="Preview" class="img-thumbnail shadow-sm rounded d-none" style="max-height: 250px;">
                            <div id="no-cover" class="bg-light border text-muted rounded d-flex justify-content-center align-items-center" style="width:150px; height:200px;"><i class="bi bi-image fs-1"></i></div>
                        <?php endif; ?>
                    </div>
                    <input type="file" class="form-control" name="cover" accept=".jpg,.jpeg,.png" onchange="previewImage(this);">
                    <small class="text-muted">Biarkan kosong jika tidak ingin mengganti cover.</small>
                </div>
            </div>

            <button type="submit" name="update" class="btn btn-warning"><i class="bi bi-save"></i> Update Buku</button>
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
            var noCover = document.getElementById('no-cover');
            if(noCover) noCover.classList.add('d-none');
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php include 'footer.php'; ?>
