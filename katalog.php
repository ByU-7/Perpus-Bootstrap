<?php
include 'header_public.php';

// Menangani Pencarian & Filter
$keyword = "";
$genre_filter = "";
$where_clauses = [];

if(isset($_GET['q']) && $_GET['q'] != "") {
    $keyword = mysqli_real_escape_string($koneksi, $_GET['q']);
    $where_clauses[] = "(b.judul_buku LIKE '%$keyword%' OR b.pengarang LIKE '%$keyword%' OR b.penerbit LIKE '%$keyword%')";
}

if(isset($_GET['genre']) && $_GET['genre'] != "") {
    $genre_filter = mysqli_real_escape_string($koneksi, $_GET['genre']);
    // Memastikan buku memiliki genre yang dipilih
    $where_clauses[] = "b.id_buku IN (SELECT id_buku FROM buku_genre WHERE id_genre = '$genre_filter')";
}

$where_sql = "";
if(count($where_clauses) > 0) {
    $where_sql = "WHERE " . implode(" AND ", $where_clauses);
}

$query = "SELECT b.*, GROUP_CONCAT(g.nama_genre SEPARATOR ', ') as daftar_genre 
          FROM buku b
          LEFT JOIN buku_genre bg ON b.id_buku = bg.id_buku
          LEFT JOIN genre g ON bg.id_genre = g.id_genre
          $where_sql
          GROUP BY b.id_buku 
          ORDER BY b.judul_buku ASC";

$data_buku = mysqli_query($koneksi, $query);

// Mengambil semua genre untuk Filter Pills
$data_genre_filter = mysqli_query($koneksi, "SELECT * FROM genre ORDER BY nama_genre ASC");
?>

    <section class="py-5 mt-5 bg-light border-bottom">
        <div class="container pt-3">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2 class="serif-font mb-0">Eksplorasi Katalog Utama</h2>
                    <p class="text-muted mt-2 mb-md-0">Telusuri ribuan judul buku di perpustakaan kami.</p>
                </div>
            </div>

            <!-- Filter Genre Pills -->
            <div class="mt-4 pt-4 border-top">
                <h6 class="text-muted mb-3 text-uppercase" style="letter-spacing: 1px; font-size: 0.8rem;">Filter Berdasarkan Genre:</h6>
                <a href="katalog.php<?php echo $keyword ? '?q='.$keyword : ''; ?>" class="filter-pill <?php echo $genre_filter == '' ? 'active' : ''; ?>">Semua Genre</a>
                <?php while($g = mysqli_fetch_array($data_genre_filter)): 
                    // Mempertahankan query pencarian jika ada
                    $url = "katalog.php?genre=".$g['id_genre'];
                    if($keyword != "") $url .= "&q=".$keyword;
                ?>
                    <a href="<?php echo $url; ?>" class="filter-pill <?php echo $genre_filter == $g['id_genre'] ? 'active' : ''; ?>">
                        <?php echo $g['nama_genre']; ?>
                    </a>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <section class="py-5 mb-5">
        <div class="container">
            <?php if($keyword != ""): ?>
                <div class="mb-4 d-flex justify-content-between align-items-center pb-3">
                    <h5 class="serif-font mb-0">Hasil pencarian untuk: <span class="text-warning">"<?php echo $keyword; ?>"</span></h5>
                    <a href="katalog.php" class="btn btn-outline-secondary btn-sm rounded-0">Reset Semua Filter</a>
                </div>
            <?php endif; ?>

            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
                <?php 
                if(mysqli_num_rows($data_buku) == 0){
                    echo "<div class='col-12 text-center py-5 my-5'>
                            <i class='bi bi-journal-x text-muted mb-3 d-block' style='font-size: 5rem; opacity: 0.5;'></i>
                            <h4 class='text-muted serif-font'>Buku tidak ditemukan.</h4>
                            <p class='text-muted'>Cobalah menggunakan kata kunci atau filter genre yang berbeda.</p>
                          </div>";
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

<?php include 'footer_public.php'; ?>
