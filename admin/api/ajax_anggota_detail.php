<?php
include '../../config/koneksi.php';

$id = isset($_GET['id']) ? mysqli_real_escape_string($koneksi, $_GET['id']) : 0;

$query = mysqli_query($koneksi, "SELECT * FROM anggota WHERE id_anggota='$id'");
if(mysqli_num_rows($query) == 0){
    echo "<div class='alert alert-danger'>Data Anggota tidak ditemukan!</div>";
    exit;
}
$d = mysqli_fetch_assoc($query);

// Tentukan foto profil
$fallback_img = "https://ui-avatars.com/api/?name=".urlencode($d['nama_anggota'])."&background=c2593b&color=fff&size=150";
$foto_src = ($d['foto'] != '' && file_exists('../uploads/anggota/'.$d['foto'])) ? '../uploads/anggota/'.$d['foto'] : $fallback_img;
?>

<div class="row">
    <!-- Kolom Kiri: Foto & Status -->
    <div class="col-md-5 mb-3 text-center border-end">
        <img src="<?php echo $foto_src; ?>" alt="Foto Profil" class="rounded mb-3 shadow-sm" style="width: 120px; height: 120px; object-fit: cover; border: 4px solid #f1f5f9;">
        <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($d['nama_anggota']); ?></h5>
        <p class="text-muted mb-2 small">NIM/NIK: <?php echo htmlspecialchars($d['nim_nik']); ?></p>
        
        <?php if($d['status_keanggotaan'] == 'Khusus'): ?>
            <span class="badge bg-warning text-dark px-3 py-1 rounded-pill"><i class="bi bi-star-fill"></i> Khusus</span>
        <?php else: ?>
            <span class="badge bg-secondary px-3 py-1 rounded-pill"><i class="bi bi-person-fill"></i> Regular</span>
        <?php endif; ?>
        
        <div class="mt-4 border-top pt-3 text-start px-2">
            <p class="mb-1 text-muted" style="font-size: 0.8rem;"><i class="bi bi-telephone-fill text-primary"></i> Kontak</p>
            <p class="fw-bold mb-3"><?php echo htmlspecialchars($d['no_telp']); ?></p>
            
            <p class="mb-1 text-muted" style="font-size: 0.8rem;"><i class="bi bi-calendar-check-fill text-primary"></i> Bergabung</p>
            <p class="fw-bold mb-0"><?php echo date('d M Y', strtotime($d['tgl_daftar'])); ?></p>
        </div>
    </div>
    
    <!-- Kolom Kanan: Biodata & Riwayat Singkat -->
    <div class="col-md-7">
        <h6 class="fw-bold text-primary border-bottom pb-2"><i class="bi bi-info-circle-fill"></i> Detail Biodata</h6>
        <table class="table table-sm table-borderless mb-4">
            <tr>
                <td width="35%" class="text-muted">Jenis Kelamin</td>
                <td width="5%">:</td>
                <td class="fw-bold"><?php echo $d['jk'] == 'L' ? 'Laki-laki' : 'Perempuan'; ?></td>
            </tr>
            <tr>
                <td class="text-muted align-top">Alamat</td>
                <td class="align-top">:</td>
                <td class="fw-bold"><?php echo nl2br(htmlspecialchars($d['alamat'])); ?></td>
            </tr>
        </table>
        
        <h6 class="fw-bold text-primary border-bottom pb-2"><i class="bi bi-clock-history"></i> Riwayat Peminjaman (5 Terakhir)</h6>
        <div class="table-responsive" style="max-height: 200px; overflow-y: auto;">
            <table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.85rem;">
                <thead class="table-light sticky-top">
                    <tr>
                        <th>Buku</th>
                        <th>Tgl</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $q_riwayat = mysqli_query($koneksi, "SELECT p.*, b.judul_buku FROM peminjaman p JOIN buku b ON p.id_buku = b.id_buku WHERE p.id_anggota = '$id' ORDER BY p.id_pinjam DESC LIMIT 5");
                    
                    if(mysqli_num_rows($q_riwayat) > 0){
                        while($r = mysqli_fetch_assoc($q_riwayat)){
                    ?>
                        <tr>
                            <td><span class="d-inline-block text-truncate" style="max-width: 150px;" title="<?php echo htmlspecialchars($r['judul_buku']); ?>"><?php echo htmlspecialchars($r['judul_buku']); ?></span></td>
                            <td><?php echo date('d/m/Y', strtotime($r['tgl_pinjam'])); ?></td>
                            <td>
                                <?php if($r['status'] == 'Dipinjam'): ?>
                                    <span class="badge bg-primary">Dipinjam</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Kembali</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='3' class='text-center py-3 text-muted'>Belum ada riwayat.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <?php if(mysqli_num_rows($q_riwayat) > 0): ?>
            <div class="text-end mt-2">
                <a href="anggota_detail.php?id=<?php echo $id; ?>" class="btn btn-sm btn-link text-decoration-none">Lihat Selengkapnya <i class="bi bi-arrow-right"></i></a>
            </div>
        <?php endif; ?>
    </div>
</div>

