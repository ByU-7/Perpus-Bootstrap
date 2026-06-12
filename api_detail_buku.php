<?php
include 'config/koneksi.php';

if(!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<div class='alert alert-danger'>ID Buku tidak ditemukan.</div>";
    exit();
}

$id_buku = mysqli_real_escape_string($koneksi, $_GET['id']);
$query = "SELECT b.*, GROUP_CONCAT(g.nama_genre SEPARATOR ', ') as daftar_genre 
          FROM buku b
          LEFT JOIN buku_genre bg ON b.id_buku = bg.id_buku
          LEFT JOIN genre g ON bg.id_genre = g.id_genre
          WHERE b.id_buku = '$id_buku'
          GROUP BY b.id_buku";
$result = mysqli_query($koneksi, $query);

if(mysqli_num_rows($result) == 0) {
    echo "<div class='alert alert-danger'>Data buku tidak ditemukan.</div>";
    exit();
}

$b = mysqli_fetch_array($result);
$cover_path = "assets/img/covers/" . $b['cover'];
$has_cover = ($b['cover'] != "" && file_exists($cover_path));
?>

<div class="row g-4">
    <div class="col-md-5">
        <div class="text-center" style="background-color: #ffffff; padding: 15px; border: 1px solid #e9e5db; border-radius: 4px;">
            <?php if($has_cover): ?>
                <img src="<?php echo $cover_path; ?>" alt="<?php echo htmlspecialchars($b['judul_buku']); ?>" class="img-fluid" style="max-height: 400px; box-shadow: 5px 5px 15px rgba(0,0,0,0.1);">
            <?php else: ?>
                <div class="text-muted py-5"><i class="bi bi-book" style="font-size: 6rem; color: #d5d0c4;"></i></div>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-md-7">
        <h2 class="serif-font fw-bold mb-2" style="color: #1a252f;"><?php echo htmlspecialchars($b['judul_buku']); ?></h2>
        <h5 class="text-muted mb-3" style="font-style: italic;"><i class="bi bi-pen-fill me-2" style="color: #b8975a;"></i><?php echo htmlspecialchars($b['pengarang']); ?></h5>
        
        <div class="mb-4">
            <?php 
            $genres = explode(", ", $b['daftar_genre']);
            foreach($genres as $g) {
                if($g != "") echo "<span class='badge bg-light text-dark border me-2 mb-2 p-1 px-2' style='font-weight: 500; letter-spacing: 0.5px;'>".strtoupper(htmlspecialchars($g))."</span>";
            }
            if(empty($b['daftar_genre'])) echo "<span class='badge bg-light text-dark border p-1 px-2'>UMUM</span>";
            ?>
        </div>

        <div class="mb-4">
            <h6 class="serif-font fw-bold border-bottom pb-2 mb-2">Sinopsis</h6>
            <p class="text-muted small" style="line-height: 1.6; text-align: justify; max-height: 150px; overflow-y: auto; padding-right: 10px;">
                <?php 
                    if(!empty($b['sinopsis'])) {
                        echo nl2br(htmlspecialchars($b['sinopsis']));
                    } else {
                        echo "<span class='font-italic'>Sinopsis belum tersedia untuk buku ini.</span>";
                    }
                ?>
            </p>
        </div>

        <div class="p-3 bg-light border mb-4 rounded">
            <h6 class="serif-font border-bottom pb-2 mb-2">Informasi Bibliografi</h6>
            <table class="table table-borderless table-sm mb-0 small">
                <tr><th style="width: 35%; color: #6c757d; font-weight: 500;">Kode Buku</th><td>: <strong><?php echo htmlspecialchars($b['kode_buku']); ?></strong></td></tr>
                <tr><th style="width: 35%; color: #6c757d; font-weight: 500;">Penerbit</th><td>: <?php echo htmlspecialchars($b['penerbit']); ?></td></tr>
                <tr><th style="width: 35%; color: #6c757d; font-weight: 500;">Tahun Terbit</th><td>: <?php echo htmlspecialchars($b['tahun_terbit']); ?></td></tr>
                <tr>
                    <th style="width: 35%; color: #6c757d; font-weight: 500;">Ketersediaan</th>
                    <td>: 
                        <?php if($b['stok'] > 0): ?>
                            <span class="badge bg-success bg-opacity-10 text-success border border-success">Tersedia (<?php echo $b['stok']; ?>)</span>
                        <?php else: ?>
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">Habis</span>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>

        <?php if($b['stok'] > 0): ?>
            <div class="alert alert-success border-success border-opacity-25 bg-success bg-opacity-10 py-2 small d-flex align-items-center mb-0">
                <i class="bi bi-info-circle-fill fs-5 me-2 text-success"></i>
                <div>
                    Silakan tunjukkan Kode Buku <strong><?php echo htmlspecialchars($b['kode_buku']); ?></strong> kepada petugas untuk meminjam.
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
