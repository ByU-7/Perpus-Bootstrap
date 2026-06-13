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

<!-- Form Pencarian & Filter -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="" class="mb-0">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex flex-column flex-md-row gap-2 w-100" style="max-width: 800px;">
                    <div class="flex-grow-1">
                        <input type="text" class="form-control" name="cari" placeholder="Cari Nama Anggota atau Judul Buku..." value="<?php echo isset($_GET['cari']) ? htmlspecialchars($_GET['cari']) : ''; ?>">
                    </div>
                    <div class="flex-grow-1" style="min-width: 200px;">
                        <select class="form-select select2" name="filter_status" data-placeholder="Filter status...">
                            <option value="">-- Semua Status --</option>
                            <option value="Dipinjam" <?php echo (isset($_GET['filter_status']) && $_GET['filter_status'] == 'Dipinjam') ? 'selected' : ''; ?>>Dipinjam</option>
                            <option value="Kembali" <?php echo (isset($_GET['filter_status']) && $_GET['filter_status'] == 'Kembali') ? 'selected' : ''; ?>>Sudah Kembali</option>
                        </select>
                    </div>
                    <div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i> Cari</button>
                            <?php if(!empty($_GET['cari']) || !empty($_GET['filter_status'])): ?>
                                <a href="peminjaman.php" class="btn btn-outline-secondary px-2"><i class="bi bi-x-lg"></i></a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex align-items-center gap-2 border-start ps-3">
                    <label class="form-label mb-0 text-muted small fw-bold">Urutkan:</label>
                    <select class="form-select form-select-sm border-secondary" name="sort_waktu" onchange="this.form.submit()" style="width: 130px; cursor: pointer;">
                        <option value="terbaru" <?php echo (isset($_GET['sort_waktu']) && $_GET['sort_waktu'] == 'terlama') ? '' : 'selected'; ?>>Waktu Terbaru</option>
                        <option value="terlama" <?php echo (isset($_GET['sort_waktu']) && $_GET['sort_waktu'] == 'terlama') ? 'selected' : ''; ?>>Waktu Terlama</option>
                    </select>
                </div>
            </div>
        </form>
    </div>
</div>

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
                    $cari = isset($_GET['cari']) ? mysqli_real_escape_string($koneksi, $_GET['cari']) : '';
                    $filter_status = isset($_GET['filter_status']) ? mysqli_real_escape_string($koneksi, $_GET['filter_status']) : '';
                    
                    $where = [];
                    if($cari != ''){
                        $where[] = "(a.nama_anggota LIKE '%$cari%' OR b.judul_buku LIKE '%$cari%')";
                    }
                    if($filter_status != ''){
                        $where[] = "p.status = '$filter_status'";
                    }
                    
                    $where_clause = "";
                    if(count($where) > 0){
                        $where_clause = " WHERE " . implode(" AND ", $where);
                    }

                    // Cek urutan
                    $order_by = "p.id_pinjam DESC";
                    if(isset($_GET['sort_waktu']) && $_GET['sort_waktu'] == 'terlama') {
                        $order_by = "p.id_pinjam ASC";
                    }

                    $sql = "SELECT p.*, a.nama_anggota, a.nim_nik, b.judul_buku, b.kode_buku 
                            FROM peminjaman p
                            INNER JOIN anggota a ON p.id_anggota = a.id_anggota
                            INNER JOIN buku b ON p.id_buku = b.id_buku
                            $where_clause
                            ORDER BY $order_by";
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
                            <a href="#" onclick="viewAnggota(<?php echo $d['id_anggota']; ?>); return false;" class="text-decoration-none text-primary fw-bold">
                                <?php echo $d['nama_anggota']; ?>
                            </a><br>
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
                                <a href="peminjaman_kembali.php?id=<?php echo $d['id_pinjam']; ?>" class="btn btn-sm btn-success" onclick="confirmAction(event, this.href, 'Proses Pengembalian?', 'Anda yakin ingin memproses pengembalian buku ini sekarang?')"><i class="bi bi-box-arrow-in-down-left"></i> Kembalikan</a>
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

