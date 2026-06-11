<?php
include 'header_public.php';

if(!isset($_GET['id']) || empty($_GET['id'])) {
    header("location:katalog.php");
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
    header("location:katalog.php");
    exit();
}

$b = mysqli_fetch_array($result);
$cover_path = "assets/img/covers/" . $b['cover'];
$has_cover = ($b['cover'] != "" && file_exists($cover_path));
?>

    <div class="container py-5" data-aos="fade-up">
        <a href="katalog.php" class="btn btn-outline-dark rounded-0 mb-4 mt-3"><i class="bi bi-arrow-left"></i> Kembali ke Katalog</a>
        
        <div class="card border-0 shadow-sm rounded-0" style="background: white; border: 1px solid #e9e5db !important;">
            <div class="card-body p-lg-5 p-4">
                <div class="row g-5">
                    <div class="col-md-4">
                        <div class="text-center" style="background-color: #ffffff; padding: 20px; border: 1px solid #e9e5db; border-radius: 4px;">
                            <?php if($has_cover): ?>
                                <img src="<?php echo $cover_path; ?>" alt="<?php echo $b['judul_buku']; ?>" class="img-fluid" style="max-height: 500px; box-shadow: 5px 5px 20px rgba(0,0,0,0.15);">
                            <?php else: ?>
                                <div class="text-muted py-5"><i class="bi bi-book" style="font-size: 8rem; color: #d5d0c4;"></i></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <h1 class="serif-font fw-bold mb-3" style="font-size: 2.5rem; line-height: 1.2;"><?php echo $b['judul_buku']; ?></h1>
                        <h4 class="text-muted mb-4" style="font-style: italic;"><i class="bi bi-pen-fill me-2" style="color: #b8975a;"></i><?php echo $b['pengarang']; ?></h4>
                        
                        <div class="mb-4">
                            <?php 
                            $genres = explode(", ", $b['daftar_genre']);
                            foreach($genres as $g) {
                                if($g != "") echo "<span class='badge bg-light text-dark border me-2 mb-2 p-2 px-3' style='font-weight: 500; letter-spacing: 1px;'>".strtoupper($g)."</span>";
                            }
                            if(empty($b['daftar_genre'])) echo "<span class='badge bg-light text-dark border p-2 px-3'>UMUM</span>";
                            ?>
                        </div>

                        <!-- Sinopsis Section -->
                        <div class="mb-5">
                            <h5 class="serif-font fw-bold mb-3">Sinopsis</h5>
                            <p class="text-muted" style="line-height: 1.8; font-size: 1.05rem; text-align: justify;">
                                <?php 
                                    if(!empty($b['sinopsis'])) {
                                        echo nl2br(htmlspecialchars($b['sinopsis']));
                                    } else {
                                        echo "<span class='font-italic'>Sinopsis belum tersedia untuk buku ini.</span>";
                                    }
                                ?>
                            </p>
                        </div>

                        <div class="p-4 bg-light border mb-4">
                            <h5 class="serif-font border-bottom pb-2 mb-3">Informasi Bibliografi</h5>
                            <table class="table table-borderless table-sm mb-0">
                                <tr><th style="width: 30%; color: #6c757d; font-weight: 500;">Kode Buku</th><td>: <strong><?php echo $b['kode_buku']; ?></strong></td></tr>
                                <tr><th style="width: 30%; color: #6c757d; font-weight: 500;">Penerbit</th><td>: <?php echo $b['penerbit']; ?></td></tr>
                                <tr><th style="width: 30%; color: #6c757d; font-weight: 500;">Tahun Terbit</th><td>: <?php echo $b['tahun_terbit']; ?></td></tr>
                                <tr>
                                    <th style="width: 30%; color: #6c757d; font-weight: 500;">Ketersediaan</th>
                                    <td>: 
                                        <?php if($b['stok'] > 0): ?>
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success p-2">Tersedia (<?php echo $b['stok']; ?> Eksemplar)</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger p-2">Sedang Habis Dipinjam</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <?php if($b['stok'] > 0): ?>
                            <div class="alert alert-success border-success border-opacity-25 bg-success bg-opacity-10 d-flex align-items-center">
                                <i class="bi bi-info-circle-fill fs-4 me-3 text-success"></i>
                                <div>
                                    <strong>Buku Tersedia!</strong> Silakan datang ke perpustakaan dan tunjukkan Kode Buku <strong><?php echo $b['kode_buku']; ?></strong> kepada petugas untuk meminjam.
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-danger border-danger border-opacity-25 bg-danger bg-opacity-10 d-flex align-items-center">
                                <i class="bi bi-exclamation-triangle-fill fs-4 me-3 text-danger"></i>
                                <div>
                                    <strong>Stok Kosong!</strong> Buku ini sedang dipinjam oleh anggota lain. Silakan periksa kembali beberapa hari ke depan.
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php include 'footer_public.php'; ?>
