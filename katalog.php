<?php
include 'header_public.php';

// Menangani Pencarian & Filter
$keyword = "";
$genre_filter = "";
$status_filter = "";
$where_clauses = [];

if(isset($_GET['q']) && $_GET['q'] != "") {
    $keyword = mysqli_real_escape_string($koneksi, $_GET['q']);
    $where_clauses[] = "(b.judul_buku LIKE '%$keyword%' OR b.pengarang LIKE '%$keyword%' OR b.kode_buku LIKE '%$keyword%')";
}

if(isset($_GET['genre']) && $_GET['genre'] != "") {
    $genre_filter = mysqli_real_escape_string($koneksi, $_GET['genre']);
    $where_clauses[] = "b.id_buku IN (SELECT id_buku FROM buku_genre WHERE id_genre = '$genre_filter')";
}

if(isset($_GET['status']) && $_GET['status'] != "") {
    $status_filter = mysqli_real_escape_string($koneksi, $_GET['status']);
    if($status_filter == 'tersedia') {
        $where_clauses[] = "b.stok > 0";
    } elseif($status_filter == 'habis') {
        $where_clauses[] = "b.stok = 0";
    }
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

// Helper untuk membuat URL filter
function build_url($q, $genre, $status) {
    $params = [];
    if($q != "") $params[] = "q=" . urlencode($q);
    if($genre != "") $params[] = "genre=" . urlencode($genre);
    if($status != "") $params[] = "status=" . urlencode($status);
    return "katalog.php" . (count($params) > 0 ? "?" . implode("&", $params) : "");
}
?>

    <section class="py-5 bg-light border-bottom" data-aos="fade-down">
        <div class="container pt-3">
            <div class="row align-items-center mb-4">
                <div class="col-md-6">
                    <h2 class="serif-font mb-0">Eksplorasi Katalog Utama</h2>
                    <p class="text-muted mt-2 mb-md-0">Temukan buku yang Anda butuhkan dan catat kode bukunya untuk dipinjam di meja sirkulasi.</p>
                </div>
                <div class="col-md-6 mt-4 mt-md-0">
                    <form action="katalog.php" method="GET">
                        <div class="input-group shadow-sm">
                            <input type="text" name="q" class="form-control py-3" placeholder="Pencarian spesifik..." value="<?php echo htmlspecialchars($keyword); ?>">
                            <?php if($genre_filter != "") echo '<input type="hidden" name="genre" value="'.$genre_filter.'">'; ?>
                            <?php if($status_filter != "") echo '<input type="hidden" name="status" value="'.$status_filter.'">'; ?>
                            <button type="submit" class="btn text-white px-4" style="background-color: #1a252f;"><i class="bi bi-search"></i> Cari</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Filter Controls -->
            <div class="row border-top pt-4">
                <div class="col-md-8">
                    <h6 class="text-muted mb-3 text-uppercase" style="letter-spacing: 1px; font-size: 0.8rem;">Filter Berdasarkan Genre:</h6>
                    <a href="<?php echo build_url($keyword, "", $status_filter); ?>" class="filter-pill <?php echo $genre_filter == '' ? 'active' : ''; ?>">Semua Genre</a>
                    <?php while($g = mysqli_fetch_array($data_genre_filter)): ?>
                        <a href="<?php echo build_url($keyword, $g['id_genre'], $status_filter); ?>" class="filter-pill <?php echo $genre_filter == $g['id_genre'] ? 'active' : ''; ?>">
                            <?php echo $g['nama_genre']; ?>
                        </a>
                    <?php endwhile; ?>
                </div>
                <div class="col-md-4 mt-3 mt-md-0">
                    <h6 class="text-muted mb-3 text-uppercase" style="letter-spacing: 1px; font-size: 0.8rem;">Status Ketersediaan:</h6>
                    <div class="btn-group w-100 shadow-sm">
                        <a href="<?php echo build_url($keyword, $genre_filter, ""); ?>" class="btn btn-outline-secondary <?php echo $status_filter == '' ? 'active' : ''; ?>">Semua</a>
                        <a href="<?php echo build_url($keyword, $genre_filter, "tersedia"); ?>" class="btn btn-outline-secondary <?php echo $status_filter == 'tersedia' ? 'active' : ''; ?>">Tersedia</a>
                        <a href="<?php echo build_url($keyword, $genre_filter, "habis"); ?>" class="btn btn-outline-secondary <?php echo $status_filter == 'habis' ? 'active' : ''; ?>">Habis</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 mb-5" style="min-height: 50vh;">
        <div class="container">
            <?php if($keyword != "" || $genre_filter != "" || $status_filter != ""): ?>
                <div class="mb-4 d-flex justify-content-between align-items-center pb-3" data-aos="fade-right">
                    <h5 class="serif-font mb-0">Menampilkan hasil penyaringan katalog</h5>
                    <a href="katalog.php" class="btn btn-outline-secondary btn-sm rounded-0"><i class="bi bi-arrow-counterclockwise"></i> Reset Filter</a>
                </div>
            <?php endif; ?>

            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
                <?php 
                if(mysqli_num_rows($data_buku) == 0){
                    echo "<div class='col-12 text-center py-5 my-5' data-aos='zoom-in'>
                            <i class='bi bi-journal-x text-muted mb-3 d-block' style='font-size: 5rem; opacity: 0.5;'></i>
                            <h4 class='text-muted serif-font'>Buku tidak ditemukan.</h4>
                            <p class='text-muted'>Cobalah menggunakan kata kunci atau filter yang berbeda.</p>
                          </div>";
                }
                
                $delay = 100;
                while($b = mysqli_fetch_array($data_buku)): 
                    $cover_path = "assets/img/covers/" . $b['cover'];
                    $has_cover = ($b['cover'] != "" && file_exists($cover_path));
                ?>
                <div class="col" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
                    <a href="detail.php?id=<?php echo $b['id_buku']; ?>" class="book-card">
                        <div class="book-cover-container">
                            <?php if($has_cover): ?>
                                <img src="<?php echo $cover_path; ?>" alt="<?php echo $b['judul_buku']; ?>">
                            <?php else: ?>
                                <div class="text-center text-muted"><i class="bi bi-book" style="font-size: 4rem; color: #d5d0c4;"></i></div>
                            <?php endif; ?>
                            
                            <?php if($b['stok'] == 0): ?>
                                <div style="position: absolute; top: 15px; right: -5px; background: #dc3545; color: white; padding: 5px 15px; font-weight: bold; font-size: 0.75rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                    Habis Dipinjam
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="book-info">
                            <h3 class="book-title serif-font"><?php echo $b['judul_buku']; ?></h3>
                            <div class="book-author"><i class="bi bi-pen-fill" style="color:#b8975a;"></i> <?php echo $b['pengarang']; ?></div>
                            <div class="book-genre"><i class="bi bi-bookmark-fill me-1"></i><?php echo $b['daftar_genre'] ?: 'Umum'; ?></div>
                        </div>
                    </a>
                </div>
                <?php 
                    $delay += 50; 
                    if($delay > 300) $delay = 100; // Reset delay untuk row berikutnya
                endwhile; 
                ?>
            </div>
        </div>
    </section>

<?php include 'footer_public.php'; ?>
