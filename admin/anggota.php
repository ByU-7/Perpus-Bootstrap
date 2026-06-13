<?php include 'header.php'; ?>
<?php include '../config/koneksi.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Data Anggota</h2>
    <a href="anggota_tambah.php" class="btn btn-primary"><i class="bi bi-person-plus"></i> Tambah Anggota</a>
</div>

<?php 
if(isset($_GET['pesan'])){
    if($_GET['pesan'] == "simpan"){
        echo "<div class='alert alert-success'>Data berhasil disimpan!</div>";
    } else if($_GET['pesan'] == "hapus"){
        echo "<div class='alert alert-success'>Data berhasil dihapus!</div>";
    }
}
?>

<div class="card shadow-sm">
    <div class="card-body">
        
        <!-- Form Pencarian & Filter -->
        <form method="GET" action="" class="mb-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex flex-column flex-md-row gap-2 w-100" style="max-width: 800px;">
                    <div class="flex-grow-1">
                        <input type="text" class="form-control" name="cari" placeholder="Cari Nama Anggota atau NIM/NIK..." value="<?php echo isset($_GET['cari']) ? $_GET['cari'] : ''; ?>">
                    </div>
                    <div class="flex-grow-1" style="min-width: 200px;">
                        <select class="form-select" name="filter_status">
                            <option value="">-- Semua Status --</option>
                            <option value="Regular" <?php echo (isset($_GET['filter_status']) && $_GET['filter_status'] == 'Regular') ? 'selected' : ''; ?>>Regular</option>
                            <option value="Khusus" <?php echo (isset($_GET['filter_status']) && $_GET['filter_status'] == 'Khusus') ? 'selected' : ''; ?>>Khusus</option>
                        </select>
                    </div>
                    <div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i> Cari</button>
                            <?php if((isset($_GET['cari']) && $_GET['cari'] != '') || (isset($_GET['filter_status']) && $_GET['filter_status'] != '')): ?>
                                <a href="anggota.php" class="btn btn-danger" title="Reset"><i class="bi bi-x-lg"></i></a>
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

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th width="5%">No</th>
                        <th>NIM/NIK</th>
                        <th>Nama Anggota</th>
                        <th>L/P</th>
                        <th>No. Telp</th>
                        <th>Status</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    $kondisi = [];
                    
                    // Cek jika ada input pencarian
                    if(isset($_GET['cari']) && $_GET['cari'] != ''){
                        $cari = mysqli_real_escape_string($koneksi, $_GET['cari']);
                        $kondisi[] = "(nama_anggota LIKE '%$cari%' OR nim_nik LIKE '%$cari%')";
                    }
                    
                    // Cek jika ada filter status
                    if(isset($_GET['filter_status']) && $_GET['filter_status'] != ''){
                        $status = mysqli_real_escape_string($koneksi, $_GET['filter_status']);
                        $kondisi[] = "status_keanggotaan = '$status'";
                    }
                    
                    // Cek urutan
                    $order_by = "id_anggota DESC";
                    if(isset($_GET['sort_waktu']) && $_GET['sort_waktu'] == 'terlama') {
                        $order_by = "id_anggota ASC";
                    }

                    // Susun Query SQL
                    $sql = "SELECT * FROM anggota";
                    if(count($kondisi) > 0){
                        $sql .= " WHERE " . implode(" AND ", $kondisi);
                    }
                    $sql .= " ORDER BY $order_by";

                    $data = mysqli_query($koneksi, $sql);
                    
                    // Cek jika data kosong
                    if(mysqli_num_rows($data) == 0){
                        echo "<tr><td colspan='7' class='text-center'>Data tidak ditemukan</td></tr>";
                    }

                    while($d = mysqli_fetch_array($data)){
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo $d['nim_nik']; ?></td>
                        <td>
                            <a href="#" onclick="viewAnggota(<?php echo $d['id_anggota']; ?>); return false;" class="text-decoration-none fw-bold text-primary">
                                <?php echo $d['nama_anggota']; ?>
                            </a>
                        </td>
                        <td><?php echo $d['jk'] == 'L' ? 'Laki-laki' : 'Perempuan'; ?></td>
                        <td><?php echo $d['no_telp']; ?></td>
                        <td>
                            <?php if($d['status_keanggotaan'] == 'Khusus'): ?>
                                <span class="badge bg-warning text-dark">Khusus</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Regular</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="anggota_edit.php?id=<?php echo $d['id_anggota']; ?>" class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-square"></i></a>
                            <a href="anggota_hapus.php?id=<?php echo $d['id_anggota']; ?>" class="btn btn-sm btn-danger" title="Hapus" onclick="confirmAction(event, this.href, 'Hapus Data?', 'Yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.')"><i class="bi bi-trash"></i></a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

