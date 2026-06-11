<?php
include 'config/koneksi.php';

// Menangani Pencarian
$keyword = "";
$where_clause = "";
if(isset($_GET['q']) && $_GET['q'] != "") {
    $keyword = mysqli_real_escape_string($koneksi, $_GET['q']);
    $where_clause = "WHERE b.judul_buku LIKE '%$keyword%' OR b.pengarang LIKE '%$keyword%' OR b.penerbit LIKE '%$keyword%'";
}

$query = "SELECT b.*, GROUP_CONCAT(g.nama_genre SEPARATOR ', ') as daftar_genre 
          FROM buku b
          LEFT JOIN buku_genre bg ON b.id_buku = bg.id_buku
          LEFT JOIN genre g ON bg.id_genre = g.id_genre
          $where_clause
          GROUP BY b.id_buku 
          ORDER BY b.judul_buku ASC";

$data_buku = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Buku - Perpustakaan Akademik</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,600;0,700;1,400&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #fdfbf7; color: #333; }
        .serif-font { font-family: 'Lora', serif; color: #1a252f; }
        .navbar-custom { background-color: #1a252f; border-bottom: 3px solid #b8975a; }
        .navbar-brand { font-family: 'Lora', serif; font-weight: 700; }
        .book-card {
            background: white; border: 1px solid #e9e5db; overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease; height: 100%; display: flex; flex-direction: column; cursor: pointer; text-decoration: none; color: inherit;
        }
        .book-card:hover { transform: translateY(-8px); box-shadow: 0 15px 30px rgba(26, 37, 47, 0.15); border-color: #b8975a; color: inherit; }
        .book-cover-container { width: 100%; height: 280px; background-color: #f1ede1; display: flex; align-items: center; justify-content: center; border-bottom: 1px solid #e9e5db; padding: 20px; }
        .book-cover-container img { max-height: 100%; max-width: 100%; object-fit: contain; box-shadow: 5px 5px 15px rgba(0,0,0,0.2); }
        .book-info { padding: 20px; flex-grow: 1; display: flex; flex-direction: column; }
        .book-title { font-size: 1.1rem; font-weight: 600; margin-bottom: 8px; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .book-author { color: #555; font-size: 0.9rem; margin-bottom: 15px; font-style: italic; }
        .book-genre { font-size: 0.75rem; color: #b8975a; text-transform: uppercase; letter-spacing: 1px; font-weight: 600; margin-bottom: auto; }
        footer { background-color: #1a252f; color: #adb5bd; border-top: 4px solid #b8975a; }
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
                    <li class="nav-item"><a class="nav-link active" href="katalog.php">Katalog Utama</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="py-5 bg-light border-bottom">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2 class="serif-font mb-0">Eksplorasi Katalog</h2>
                    <p class="text-muted mt-2 mb-md-0">Telusuri ribuan judul buku di perpustakaan kami.</p>
                </div>
                <div class="col-md-6">
                    <form action="katalog.php" method="GET" class="d-flex">
                        <input type="text" name="q" class="form-control rounded-0 border-dark" placeholder="Cari judul, pengarang..." value="<?php echo htmlspecialchars($keyword); ?>">
                        <button type="submit" class="btn btn-dark rounded-0 px-4"><i class="bi bi-search"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <?php if($keyword != ""): ?>
                <div class="mb-4 d-flex justify-content-between align-items-center border-bottom pb-3">
                    <h5 class="serif-font mb-0">Hasil untuk: <span class="text-warning">"<?php echo $keyword; ?>"</span> (<?php echo mysqli_num_rows($data_buku); ?> ditemukan)</h5>
                    <a href="katalog.php" class="btn btn-outline-secondary btn-sm rounded-0">Reset Filter</a>
                </div>
            <?php endif; ?>

            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
                <?php 
                if(mysqli_num_rows($data_buku) == 0){
                    echo "<div class='col-12 text-center py-5'><i class='bi bi-journal-x fs-1 text-muted mb-3 d-block'></i><h5 class='text-muted serif-font'>Buku tidak ditemukan.</h5></div>";
                }
                while($b = mysqli_fetch_array($data_buku)): 
                    $cover_path = "assets/img/covers/" . $b['cover'];
                    $has_cover = ($b['cover'] != "" && file_exists($cover_path));
                ?>
                <div class="col">
                    <a href="detail.php?id=<?php echo $b['id_buku']; ?>" class="book-card">
                        <div class="book-cover-container">
                            <?php if($has_cover): ?>
                                <img src="<?php echo $cover_path; ?>" alt="<?php echo $b['judul_buku']; ?>">
                            <?php else: ?>
                                <div class="text-center text-muted"><i class="bi bi-book" style="font-size: 4rem; color: #d5d0c4;"></i></div>
                            <?php endif; ?>
                        </div>
                        <div class="book-info">
                            <h3 class="book-title serif-font"><?php echo $b['judul_buku']; ?></h3>
                            <div class="book-author"><i class="bi bi-pen-fill" style="color:#b8975a;"></i> <?php echo $b['pengarang']; ?></div>
                            <div class="book-genre"><i class="bi bi-bookmark-fill me-1"></i><?php echo $b['daftar_genre'] ?: 'Umum'; ?></div>
                        </div>
                    </a>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <footer class="py-4 text-center">
        <div class="container">
            <p class="mb-0" style="font-size: 0.9rem;">&copy; <?php echo date('Y'); ?> Sistem Informasi Perpustakaan Akademik.</p>
        </div>
    </footer>
</body>
</html>
