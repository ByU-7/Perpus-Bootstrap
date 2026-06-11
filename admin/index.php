<?php include 'header.php'; ?>

    <h2>Dashboard</h2>
    <p>Selamat datang di halaman panel kontrol Administrator Perpustakaan.</p>
    
    <?php
    include '../config/koneksi.php';
    // Menghitung jumlah data
    $jml_anggota = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM anggota"));
    $jml_buku = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM buku"));
    $jml_pinjam = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM peminjaman WHERE status='Dipinjam'"));
    ?>

    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white mb-3">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-people"></i> Total Anggota</h5>
                    <p class="card-text fs-3"><?php echo $jml_anggota; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white mb-3">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-book"></i> Total Buku</h5>
                    <p class="card-text fs-3"><?php echo $jml_buku; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-dark mb-3">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-arrow-left-right"></i> Peminjaman Aktif</h5>
                    <p class="card-text fs-3"><?php echo $jml_pinjam; ?></p>
                </div>
            </div>
        </div>
    </div>

<?php include 'footer.php'; ?>
