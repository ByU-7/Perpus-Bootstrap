<?php 
include 'header.php'; 
include '../config/koneksi.php';

// Mengambil data genre untuk dropdown
$data_genre = mysqli_query($koneksi, "SELECT * FROM genre ORDER BY nama_genre ASC");

// Logika Simpan
if(isset($_POST['simpan'])){
    $kode = mysqli_real_escape_string($koneksi, $_POST['kode_buku']);
    $judul = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $pengarang = mysqli_real_escape_string($koneksi, $_POST['pengarang']);
    $penerbit = mysqli_real_escape_string($koneksi, $_POST['penerbit']);
    $tahun = mysqli_real_escape_string($koneksi, $_POST['tahun']);
    $stok = mysqli_real_escape_string($koneksi, $_POST['stok']);
    $sinopsis = mysqli_real_escape_string($koneksi, $_POST['sinopsis']);
    $genres = isset($_POST['genre']) ? $_POST['genre'] : [];

    // Proses Upload Cover
    $nama_file = "";
    if(isset($_FILES['cover']) && $_FILES['cover']['name'] != ''){
        $ekstensi_diperbolehkan = array('png','jpg','jpeg');
        $nama_file_asli = $_FILES['cover']['name'];
        $x = explode('.', $nama_file_asli);
        $ekstensi = strtolower(end($x));
        $ukuran = $_FILES['cover']['size'];
        $file_tmp = $_FILES['cover']['tmp_name'];

        if(in_array($ekstensi, $ekstensi_diperbolehkan) === true){
            if($ukuran < 2048000){ // Max 2MB
                $nama_file = time() . '_' . $nama_file_asli;
                move_uploaded_file($file_tmp, '../assets/img/covers/'.$nama_file);
            } else {
                echo "<script>alert('Ukuran file cover terlalu besar! Max 2MB'); window.history.back();</script>";
                exit();
            }
        } else {
            echo "<script>alert('Ekstensi file cover tidak diperbolehkan! Harus JPG/PNG.'); window.history.back();</script>";
            exit();
        }
    }

    // Insert ke tabel buku
    mysqli_query($koneksi, "INSERT INTO buku (kode_buku, judul_buku, pengarang, penerbit, tahun_terbit, stok, cover, sinopsis) 
                            VALUES ('$kode', '$judul', '$pengarang', '$penerbit', '$tahun', '$stok', '$nama_file', '$sinopsis')");
    
    // Ambil ID buku yang baru saja di-insert
    $id_buku = mysqli_insert_id($koneksi);

    // Insert ke tabel pivot buku_genre
    if(!empty($genres)){
        foreach($genres as $id_genre){
            mysqli_query($koneksi, "INSERT INTO buku_genre (id_buku, id_genre) VALUES ('$id_buku', '$id_genre')");
        }
    }
    
    echo "<script>window.location.href='buku.php?pesan=simpan';</script>";
    exit();
}
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Tambah Data Buku</h2>
    <a href="buku.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Kode Buku</label>
                    <input type="text" class="form-control" name="kode_buku" placeholder="Contoh: BK-001" required>
                </div>
                <div class="col-md-8 mb-3">
                    <label class="form-label">Judul Buku</label>
                    <input type="text" class="form-control" name="judul" required>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Pengarang</label>
                    <input type="text" class="form-control" name="pengarang" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Penerbit</label>
                    <input type="text" class="form-control" name="penerbit" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tahun Terbit</label>
                    <input type="number" class="form-control" name="tahun" min="1900" max="2099" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Jumlah Stok</label>
                    <input type="number" class="form-control" name="stok" min="0" required>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label fw-bold">Sinopsis / Deskripsi Buku</label>
                    <textarea class="form-control" name="sinopsis" rows="4" placeholder="Tuliskan sinopsis singkat buku ini..."></textarea>
                </div>
            </div>

            <div class="row border-top pt-4 mt-3">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Daftar Genre Buku</label>
                    <div class="text-muted small mb-2"><i class="bi bi-info-circle"></i> Anda bisa memilih lebih dari satu genre. Ketik untuk mencari.</div>
                    <select class="form-select select2" name="genre[]" multiple="multiple" required>
                        <?php while($g = mysqli_fetch_array($data_genre)): ?>
                            <option value="<?php echo $g['id_genre']; ?>"><?php echo $g['nama_genre']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Cover Buku (JPG/PNG, Max 2MB)</label>
                    <div class="mb-2">
                        <img id="preview" src="#" alt="Preview Cover" class="img-thumbnail shadow-sm d-none" style="max-height: 250px;">
                    </div>
                    <input type="file" class="form-control" name="cover" accept=".jpg,.jpeg,.png" onchange="previewImage(this);">
                    <small class="text-muted">Biarkan kosong jika tidak ada cover.</small>
                </div>
            </div>

            <button type="submit" name="simpan" class="btn btn-primary"><i class="bi bi-save"></i> Simpan Buku</button>
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
