<?php
include 'config/koneksi.php';

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
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $b['judul_buku']; ?> - Detail Buku</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,600;0,700;1,400&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #fdfbf7; color: #333; }
        .serif-font { font-family: 'Lora', serif; color: #1a252f; }
        .navbar-custom { background-color: #1a252f; border-bottom: 3px solid #b8975a; }
        .navbar-brand { font-family: 'Lora', serif; font-weight: 700; }
        .detail-card { background: white; border: 1px solid #e9e5db; padding: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .cover-wrapper { background-color: #f1ede1; padding: 30px; text-align: center; border: 1px solid #e9e5db; height: 100%; display:flex; align-items:center; justify-content:center;}
        .cover-wrapper img { max-width: 100%; max-height: 500px; box-shadow: 10px 10px 25px rgba(0,0,0,0.3); }
        .table-detail th { width: 30%; color: #6c757d; font-weight: 500; }
        .badge-genre { background-color: #f8f9fa; color: #b8975a; border: 1px solid #b8975a; font-weight: 500; text-transform: uppercase; letter-spacing: 1px; font-size: 0.8rem; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom py-3">
        <div class="container">
            <a class="navbar-brand fs-4" href="index.php"><i class="bi bi-bank me-2" style="color: #b8975a;"></i>Perpus Akademik</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="index.php">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="katalog.php">Katalog Utama</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <a href="katalog.php" class="btn btn-outline-dark rounded-0 mb-4"><i class="bi bi-arrow-left"></i> Kembali ke Katalog</a>
        
        <div class="detail-card">
            <div class="row g-5">
                <div class="col-md-4">
                    <div class="cover-wrapper">
                        <?php if($has_cover): ?>
                            <img src="<?php echo $cover_path; ?>" alt="<?php echo $b['judul_buku']; ?>">
                        <?php else: ?>
                            <div class="text-center text-muted"><i class="bi bi-book" style="font-size: 8rem; color: #d5d0c4;"></i></div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h1 class="serif-font fw-bold" style="font-size: 2.5rem; line-height: 1.2;"><?php echo $b['judul_buku']; ?></h1>
                    </div>
                    <h4 class="text-muted mb-4" style="font-style: italic;"><i class="bi bi-pen-fill" style="color: #b8975a;"></i> Karya: <?php echo $b['pengarang']; ?></h4>
                    
                    <div class="mb-4">
                        <?php 
                        $genres = explode(", ", $b['daftar_genre']);
                        foreach($genres as $g) {
                            if($g != "") echo "<span class='badge badge-genre me-2 mb-2'>$g</span>";
                        }
                        if(empty($b['daftar_genre'])) echo "<span class='badge badge-genre'>UMUM</span>";
                        ?>
                    </div>

                    <div class="p-4 bg-light border mb-4">
                        <h5 class="serif-font border-bottom pb-2 mb-3">Informasi Bibliografi</h5>
                        <table class="table table-borderless table-sm table-detail mb-0">
                            <tr><th>Kode Buku</th><td>: <strong><?php echo $b['kode_buku']; ?></strong></td></tr>
                            <tr><th>Penerbit</th><td>: <?php echo $b['penerbit']; ?></td></tr>
                            <tr><th>Tahun Terbit</th><td>: <?php echo $b['tahun_terbit']; ?></td></tr>
                            <tr>
                                <th>Ketersediaan</th>
                                <td>: 
                                    <?php if($b['stok'] > 0): ?>
                                        <span class="badge bg-success">Tersedia (<?php echo $b['stok']; ?> Eksemplar)</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Sedang Habis Dipinjam</span>
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

    <footer class="py-4 text-center mt-5" style="background-color: #1a252f; color: #adb5bd; border-top: 4px solid #b8975a;">
        <div class="container">
            <p class="mb-0" style="font-size: 0.9rem;">&copy; <?php echo date('Y'); ?> Sistem Informasi Perpustakaan Akademik.</p>
        </div>
    </footer>
</body>
</html>
