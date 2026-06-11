<?php 
include 'header.php'; 
include '../config/koneksi.php';

// Ambil data anggota
$data_anggota = mysqli_query($koneksi, "SELECT * FROM anggota ORDER BY nama_anggota ASC");
// Ambil data buku yang stoknya lebih dari 0
$data_buku = mysqli_query($koneksi, "SELECT * FROM buku WHERE stok > 0 ORDER BY judul_buku ASC");

if(isset($_POST['simpan'])){
    $id_anggota = mysqli_real_escape_string($koneksi, $_POST['id_anggota']);
    $id_buku = mysqli_real_escape_string($koneksi, $_POST['id_buku']);
    $id_admin = $_SESSION['admin_id'];
    
    $tgl_pinjam = date('Y-m-d');
    // Hitung tanggal kembali seharusnya (7 hari dari sekarang)
    $tgl_kembali_seharusnya = date('Y-m-d', strtotime('+7 days', strtotime($tgl_pinjam)));

    // Insert ke peminjaman
    $query = mysqli_query($koneksi, "INSERT INTO peminjaman (id_buku, id_anggota, id_admin, tgl_pinjam, tgl_kembali_seharusnya, status) 
                            VALUES ('$id_buku', '$id_anggota', '$id_admin', '$tgl_pinjam', '$tgl_kembali_seharusnya', 'Dipinjam')");
    
    if($query){
        // Kurangi stok buku
        mysqli_query($koneksi, "UPDATE buku SET stok = stok - 1 WHERE id_buku='$id_buku'");
        echo "<script>window.location.href='peminjaman.php?pesan=simpan';</script>";
        exit();
    } else {
        echo "<script>alert('Gagal meminjam buku!');</script>";
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-cart-plus text-primary"></i> Peminjaman Baru</h2>
    <a href="peminjaman.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <label class="form-label fw-bold">Pilih Anggota</label>
                    <select class="form-select select2" name="id_anggota" required>
                        <option value=""></option>
                        <?php while($a = mysqli_fetch_array($data_anggota)): ?>
                            <option value="<?php echo $a['id_anggota']; ?>">
                                <?php echo $a['nama_anggota']; ?> (NIM/NIK: <?php echo $a['nim_nik']; ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="col-md-6 mb-4">
                    <label class="form-label fw-bold">Pilih Buku</label>
                    <select class="form-select select2" name="id_buku" required>
                        <option value=""></option>
                        <?php while($b = mysqli_fetch_array($data_buku)): ?>
                            <option value="<?php echo $b['id_buku']; ?>">
                                <?php echo $b['judul_buku']; ?> (Stok: <?php echo $b['stok']; ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <small class="text-muted"><i class="bi bi-info-circle"></i> Hanya buku yang stoknya tersedia yang muncul di sini.</small>
                </div>
            </div>

            <div class="alert alert-info border-info border-start border-4">
                <h5 class="alert-heading mb-3"><i class="bi bi-calendar-event"></i> Aturan Peminjaman:</h5>
                <ul class="mb-0 fs-6">
                    <li>Tanggal Pinjam: <strong><?php echo date('d M Y'); ?></strong></li>
                    <li>Batas Waktu Pengembalian: <strong><?php echo date('d M Y', strtotime('+7 days')); ?></strong> (7 Hari)</li>
                    <li>Denda Keterlambatan: <strong class="text-danger">Rp 1.000 / Hari</strong></li>
                </ul>
            </div>

            <button type="submit" name="simpan" class="btn btn-primary btn-lg mt-3 w-100"><i class="bi bi-check-circle"></i> Proses Peminjaman</button>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
