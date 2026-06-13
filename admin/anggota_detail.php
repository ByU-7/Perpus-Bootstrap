<?php 
include 'header.php'; 
include '../config/koneksi.php';

$id = isset($_GET['id']) ? mysqli_real_escape_string($koneksi, $_GET['id']) : 0;

$query = mysqli_query($koneksi, "SELECT * FROM anggota WHERE id_anggota='$id'");
if(mysqli_num_rows($query) == 0){
    echo "<script>alert('Data Anggota tidak ditemukan!'); window.location.href='anggota.php';</script>";
    exit;
}
$d = mysqli_fetch_assoc($query);

// Tentukan foto profil
$fallback_img = "https://ui-avatars.com/api/?name=".urlencode($d['nama_anggota'])."&background=c2593b&color=fff&size=150";
$foto_src = ($d['foto'] != '' && file_exists('../uploads/anggota/'.$d['foto'])) ? '../uploads/anggota/'.$d['foto'] : $fallback_img;
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-person-badge text-primary"></i> Detail Profil Anggota</h2>
    <a href="anggota.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

<div class="row">
    <!-- Kolom Kiri: Foto & Status -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0 text-center h-100">
            <div class="card-body py-5">
                <img src="<?php echo $foto_src; ?>" alt="Foto Profil" class="rounded mb-3 shadow" style="width: 150px; height: 150px; object-fit: cover; border: 5px solid #f1f5f9;">
                <h4 class="fw-bold mb-1"><?php echo htmlspecialchars($d['nama_anggota']); ?></h4>
                <p class="text-muted mb-3">NIM/NIK: <?php echo htmlspecialchars($d['nim_nik']); ?></p>
                
                <?php if($d['status_keanggotaan'] == 'Khusus'): ?>
                    <span class="badge bg-warning text-dark px-4 py-2 rounded-pill fs-6"><i class="bi bi-star-fill"></i> Anggota Khusus</span>
                <?php else: ?>
                    <span class="badge bg-secondary px-4 py-2 rounded-pill fs-6"><i class="bi bi-person-fill"></i> Anggota Regular</span>
                <?php endif; ?>
                
                <div class="mt-4 pt-4 border-top text-start px-3">
                    <p class="mb-1 text-muted small text-uppercase fw-bold">Kontak</p>
                    <p class="mb-3"><i class="bi bi-telephone-fill text-primary me-2"></i> <?php echo htmlspecialchars($d['no_telp']); ?></p>
                    <p class="mb-1 text-muted small text-uppercase fw-bold">Bergabung Sejak</p>
                    <p class="mb-0"><i class="bi bi-calendar-check-fill text-primary me-2"></i> <?php echo date('d M Y', strtotime($d['tgl_daftar'])); ?></p>
                </div>
            </div>
            <div class="card-footer bg-white border-0 pb-4 px-4">
                <a href="anggota_edit.php?id=<?php echo $d['id_anggota']; ?>" class="btn btn-outline-primary w-100"><i class="bi bi-pencil-square"></i> Edit Profil</a>
            </div>
        </div>
    </div>
    
    <!-- Kolom Kanan: Biodata & Riwayat -->
    <div class="col-md-8">
        <!-- Panel Biodata -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-2">
                <h5 class="fw-bold m-0"><i class="bi bi-info-circle-fill text-primary me-2"></i> Informasi Pribadi</h5>
            </div>
            <div class="card-body px-4 pb-4">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td width="30%" class="text-muted">Nama Lengkap</td>
                        <td width="5%">:</td>
                        <td class="fw-bold"><?php echo htmlspecialchars($d['nama_anggota']); ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">NIM / NIK</td>
                        <td>:</td>
                        <td class="fw-bold"><?php echo htmlspecialchars($d['nim_nik']); ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Jenis Kelamin</td>
                        <td>:</td>
                        <td class="fw-bold"><?php echo $d['jk'] == 'L' ? 'Laki-laki' : 'Perempuan'; ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Alamat Lengkap</td>
                        <td>:</td>
                        <td class="fw-bold"><?php echo nl2br(htmlspecialchars($d['alamat'])); ?></td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Panel Riwayat Peminjaman -->
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-2 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold m-0"><i class="bi bi-clock-history text-primary me-2"></i> Riwayat Peminjaman Buku</h5>
                <a href="peminjaman_tambah.php" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg"></i> Pinjamkan Buku</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">No</th>
                                <th>Buku</th>
                                <th>Tanggal Pinjam</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $q_riwayat = mysqli_query($koneksi, "SELECT p.*, b.judul_buku FROM peminjaman p JOIN buku b ON p.id_buku = b.id_buku WHERE p.id_anggota = '$id' ORDER BY p.id_pinjam DESC");
                            $no = 1;
                            
                            if(mysqli_num_rows($q_riwayat) > 0){
                                while($r = mysqli_fetch_assoc($q_riwayat)){
                            ?>
                                <tr>
                                    <td class="ps-4"><?php echo $no++; ?></td>
                                    <td><strong><?php echo htmlspecialchars($r['judul_buku']); ?></strong></td>
                                    <td><?php echo date('d M Y', strtotime($r['tgl_pinjam'])); ?></td>
                                    <td>
                                        <?php if($r['status'] == 'Dipinjam'): ?>
                                            <span class="badge bg-primary">Sedang Dipinjam</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Dikembalikan</span>
                                            <br><small class="text-muted" style="font-size: 0.7rem;">Tgl: <?php echo date('d M Y', strtotime($r['tgl_dikembalikan'])); ?></small>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php 
                                }
                            } else {
                                echo "<tr><td colspan='4' class='text-center py-5 text-muted'>Belum ada riwayat peminjaman untuk anggota ini.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

