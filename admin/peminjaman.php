<?php include 'header.php'; ?>
<?php include '../config/koneksi.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-arrow-left-right text-primary"></i> Data Transaksi Peminjaman</h2>
    <a href="peminjaman_tambah.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Pinjam Baru</a>
</div>

<?php 
if(isset($_GET['pesan'])){
    if($_GET['pesan'] == "simpan"){
        echo "<div class='alert alert-success alert-dismissible fade show'><i class='bi bi-check-circle'></i> Peminjaman berhasil diproses!<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    } else if($_GET['pesan'] == "kembali"){
        echo "<div class='alert alert-success alert-dismissible fade show'><i class='bi bi-check-circle'></i> Buku berhasil dikembalikan!<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    }
}
?>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="ps-3">No</th>
                        <th>Anggota</th>
                        <th>Buku</th>
                        <th>Tgl Pinjam</th>
                        <th>Batas Kembali</th>
                        <th>Status / Denda</th>
                        <th class="pe-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    $sql = "SELECT p.*, a.nama_anggota, a.nim_nik, b.judul_buku, b.kode_buku 
                            FROM peminjaman p
                            INNER JOIN anggota a ON p.id_anggota = a.id_anggota
                            INNER JOIN buku b ON p.id_buku = b.id_buku
                            ORDER BY p.id_pinjam DESC";
                    $data = mysqli_query($koneksi, $sql);
                    
                    if(mysqli_num_rows($data) == 0){
                        echo "<tr><td colspan='7' class='text-center py-5 text-muted'>
                                <i class='bi bi-inbox fs-1 d-block mb-2'></i>
                                Belum ada transaksi peminjaman.
                              </td></tr>";
                    }

                    while($d = mysqli_fetch_array($data)){
                        // Kalkulasi Denda Real-time
                        $denda_hitung = 0;
                        $tgl_sekarang = date('Y-m-d');
                        $tgl_tenggat = $d['tgl_kembali_seharusnya'];
                        
                        if($d['status'] == 'Dipinjam') {
                            if(strtotime($tgl_sekarang) > strtotime($tgl_tenggat)){
                                $selisih_hari = round((strtotime($tgl_sekarang) - strtotime($tgl_tenggat)) / (60 * 60 * 24));
                                $denda_hitung = $selisih_hari * 1000;
                            }
                        } else {
                            $denda_hitung = $d['denda']; // ambil dari database jika sudah kembali
                        }
                    ?>
                    <tr>
                        <td class="ps-3"><?php echo $no++; ?></td>
                        <td>
                            <strong><?php echo $d['nama_anggota']; ?></strong><br>
                            <small class="text-muted"><?php echo $d['nim_nik']; ?></small>
                        </td>
                        <td>
                            <strong><?php echo $d['judul_buku']; ?></strong><br>
                            <small class="text-muted">Kode: <?php echo $d['kode_buku']; ?></small>
                        </td>
                        <td><?php echo date('d M Y', strtotime($d['tgl_pinjam'])); ?></td>
                        <td><?php echo date('d M Y', strtotime($d['tgl_kembali_seharusnya'])); ?></td>
                        <td>
                            <?php if($d['status'] == 'Dipinjam'): ?>
                                <?php if($denda_hitung > 0): ?>
                                    <span class="badge bg-danger mb-1"><i class="bi bi-exclamation-triangle"></i> Terlambat</span><br>
                                    <small class="text-danger fw-bold">Denda: Rp <?php echo number_format($denda_hitung,0,',','.'); ?></small>
                                <?php else: ?>
                                    <span class="badge bg-primary px-3 py-2">Dipinjam</span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="badge bg-success mb-1 px-3 py-2"><i class="bi bi-check-circle"></i> Kembali</span>
                                <?php if($denda_hitung > 0): ?>
                                    <br><small class="text-danger fw-bold">Denda lunas: Rp <?php echo number_format($denda_hitung,0,',','.'); ?></small>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                        <td class="pe-3 text-center">
                            <?php if($d['status'] == 'Dipinjam'): ?>
                                <a href="peminjaman_kembali.php?id=<?php echo $d['id_pinjam']; ?>" class="btn btn-sm btn-success" onclick="return confirm('Proses pengembalian buku ini?')"><i class="bi bi-box-arrow-in-down-left"></i> Kembalikan</a>
                            <?php else: ?>
                                <span class="text-muted small"><i class="bi bi-check2-all"></i> Selesai</span><br>
                                <small class="text-muted" style="font-size: 0.7rem;">Tgl: <?php echo date('d/m/y', strtotime($d['tgl_dikembalikan'])); ?></small>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
