<?php
include 'header.php';
include '../config/koneksi.php';

// Handle Hapus
if(isset($_GET['hapus'])) {
    $id_hapus = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    mysqli_query($koneksi, "DELETE FROM saran WHERE id_saran='$id_hapus'");
    echo "<script>window.location='saran.php';</script>";
}

$kondisi = [];
$order_by = "tgl_kirim DESC";
if(isset($_GET['sort_waktu']) && $_GET['sort_waktu'] == 'terlama') {
    $order_by = "tgl_kirim ASC";
}

$sql = "SELECT * FROM saran";
if(count($kondisi) > 0){
    $sql .= " WHERE " . implode(" AND ", $kondisi);
}
$sql .= " ORDER BY $order_by";

$query_saran = mysqli_query($koneksi, $sql);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1 fw-bold text-dark"><i class="bi bi-inbox-fill text-primary me-2"></i>Kotak Saran & Masukan</h2>
        <p class="text-muted mb-0">Baca dan kelola pesan dari pengunjung perpustakaan.</p>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; overflow: hidden;">
    <div class="card-body p-3 border-bottom bg-light">
        <form method="GET" action="" class="mb-0">
            <div class="d-flex justify-content-end align-items-center gap-2">
                <label class="form-label mb-0 text-muted small fw-bold">Urutkan:</label>
                <select class="form-select form-select-sm border-secondary bg-white" name="sort_waktu" onchange="this.form.submit()" style="width: 140px; cursor: pointer;">
                    <option value="terbaru" <?php echo (isset($_GET['sort_waktu']) && $_GET['sort_waktu'] == 'terlama') ? '' : 'selected'; ?>>Waktu Terbaru</option>
                    <option value="terlama" <?php echo (isset($_GET['sort_waktu']) && $_GET['sort_waktu'] == 'terlama') ? 'selected' : ''; ?>>Waktu Terlama</option>
                </select>
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th width="20%">Pengirim</th>
                        <th width="45%">Pesan</th>
                        <th width="15%">Tanggal</th>
                        <th width="15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    if(mysqli_num_rows($query_saran) > 0) {
                        while($s = mysqli_fetch_array($query_saran)): 
                    ?>
                    <tr>
                        <td class="text-center"><?php echo $no++; ?></td>
                        <td>
                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($s['nama']); ?></div>
                            <div class="small text-muted"><?php echo htmlspecialchars($s['email']); ?></div>
                        </td>
                        <td>
                            <p class="mb-0 text-break" style="max-width: 450px;"><?php echo htmlspecialchars($s['pesan']); ?></p>
                        </td>
                        <td class="text-muted small">
                            <?php echo date('d M Y, H:i', strtotime($s['tgl_kirim'])); ?>
                        </td>
                        <td class="text-center">
                            <a href="mailto:<?php echo htmlspecialchars($s['email']); ?>" class="btn btn-sm btn-primary" title="Balas"><i class="bi bi-reply-fill"></i></a>
                            <a href="javascript:void(0)" onclick="confirmAction(event, 'saran.php?hapus=<?php echo $s['id_saran']; ?>', 'Hapus Pesan?', 'Pesan ini akan dihapus permanen.')" class="btn btn-sm btn-danger" title="Hapus"><i class="bi bi-trash"></i></a>
                        </td>
                    </tr>
                    <?php 
                        endwhile; 
                    } else {
                        echo '<tr><td colspan="5" class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>Belum ada saran/masukan.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
