<?php include 'header.php'; ?>
<?php include '../config/koneksi.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Data Buku</h2>
    <a href="buku_tambah.php" class="btn btn-primary"><i class="bi bi-journal-plus"></i> Tambah Buku</a>
</div>

<?php 
if(isset($_GET['pesan'])){
    if($_GET['pesan'] == "simpan"){
        echo "<div class='alert alert-success'>Data buku berhasil disimpan!</div>";
    } else if($_GET['pesan'] == "hapus"){
        echo "<div class='alert alert-success'>Data buku berhasil dihapus!</div>";
    }
}
?>

<div class="card shadow-sm">
    <div class="card-body">
        
        <!-- Form Pencarian & Filter -->
        <form method="GET" action="" class="mb-3">
            <div class="row g-2">
                <div class="col-md-5">
                    <input type="text" class="form-control" name="cari" placeholder="Cari Judul Buku atau Pengarang..." value="<?php echo isset($_GET['cari']) ? $_GET['cari'] : ''; ?>">
                </div>
                <div class="col-md-5">
                    <select class="form-select select2" name="filter_genre">
                        <option value="">-- Semua Genre --</option>
                        <?php 
                        $q_genre = mysqli_query($koneksi, "SELECT * FROM genre ORDER BY nama_genre ASC");
                        while($g = mysqli_fetch_array($q_genre)):
                        ?>
                            <option value="<?php echo $g['id_genre']; ?>" <?php echo (isset($_GET['filter_genre']) && $_GET['filter_genre'] == $g['id_genre']) ? 'selected' : ''; ?>>
                                <?php echo $g['nama_genre']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary w-100" type="submit"><i class="bi bi-search"></i> Cari</button>
                        <?php if((isset($_GET['cari']) && $_GET['cari'] != '') || (isset($_GET['filter_genre']) && $_GET['filter_genre'] != '')): ?>
                            <a href="buku.php" class="btn btn-danger" title="Reset"><i class="bi bi-x-lg"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th width="5%">No</th>
                        <th width="8%">Cover</th>
                        <th>Info Buku</th>
                        <th>Tahun</th>
                        <th>Stok</th>
                        <th width="12%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    $kondisi = [];
                    $join_genre = "";

                    // Cek jika ada input pencarian
                    if(isset($_GET['cari']) && $_GET['cari'] != ''){
                        $cari = mysqli_real_escape_string($koneksi, $_GET['cari']);
                        $kondisi[] = "(b.judul_buku LIKE '%$cari%' OR b.pengarang LIKE '%$cari%')";
                    }
                    
                    // Cek jika ada filter genre
                    if(isset($_GET['filter_genre']) && $_GET['filter_genre'] != ''){
                        $id_genre = mysqli_real_escape_string($koneksi, $_GET['filter_genre']);
                        // Tambahkan JOIN khusus untuk filter
                        $join_genre = "INNER JOIN buku_genre bg_filter ON b.id_buku = bg_filter.id_buku AND bg_filter.id_genre = '$id_genre'";
                    }

                    // Susun Query Utama dengan GROUP_CONCAT untuk menggabungkan genre
                    $sql = "SELECT b.*, GROUP_CONCAT(g.nama_genre SEPARATOR ', ') as daftar_genre 
                            FROM buku b 
                            $join_genre
                            LEFT JOIN buku_genre bg ON b.id_buku = bg.id_buku 
                            LEFT JOIN genre g ON bg.id_genre = g.id_genre";
                            
                    if(count($kondisi) > 0){
                        $sql .= " WHERE " . implode(" AND ", $kondisi);
                    }
                    $sql .= " GROUP BY b.id_buku ORDER BY b.id_buku DESC";

                    $data = mysqli_query($koneksi, $sql);
                    
                    if(mysqli_num_rows($data) == 0){
                        echo "<tr><td colspan='6' class='text-center'>Data tidak ditemukan</td></tr>";
                    }

                    while($d = mysqli_fetch_array($data)){
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td class="text-center">
                            <?php if($d['cover'] != '' && file_exists('../assets/img/covers/'.$d['cover'])): ?>
                                <img src="../assets/img/covers/<?php echo $d['cover']; ?>" alt="Cover" class="img-thumbnail" style="max-height: 80px;">
                            <?php else: ?>
                                <div class="bg-secondary text-white rounded d-flex justify-content-center align-items-center mx-auto" style="width:50px; height:70px;">
                                    <i class="bi bi-book fs-4"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="fw-bold fs-6 text-primary"><?php echo $d['judul_buku']; ?></div>
                            <div class="text-muted small mb-1">Oleh: <?php echo $d['pengarang']; ?> | Penerbit: <?php echo $d['penerbit']; ?></div>
                            <span class="badge bg-dark">Kode: <?php echo $d['kode_buku']; ?></span>
                            <?php if($d['daftar_genre']): ?>
                                <?php 
                                    $arr_genre = explode(', ', $d['daftar_genre']);
                                    foreach($arr_genre as $gn): 
                                ?>
                                    <span class="badge bg-info text-dark"><?php echo $gn; ?></span>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $d['tahun_terbit']; ?></td>
                        <td>
                            <?php if($d['stok'] > 0): ?>
                                <span class="badge bg-success"><?php echo $d['stok']; ?> Tersedia</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Habis</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="buku_edit.php?id=<?php echo $d['id_buku']; ?>" class="btn btn-sm btn-warning mb-1"><i class="bi bi-pencil-square"></i> Edit</a>
                            <a href="buku_hapus.php?id=<?php echo $d['id_buku']; ?>" class="btn btn-sm btn-danger mb-1" onclick="return confirm('Yakin ingin menghapus data buku ini?')"><i class="bi bi-trash"></i> Hapus</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
