<?php include 'header.php'; ?>
<?php include '../config/koneksi.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-speedometer2 text-primary"></i> Dashboard Overview</h2>
    <div>
        <span class="text-muted"><i class="bi bi-calendar3"></i> <?php echo date('l, d F Y'); ?></span>
    </div>
</div>

<?php
// 1. Kartu Ringkasan
$jml_anggota = mysqli_num_rows(mysqli_query($koneksi, "SELECT id_anggota FROM anggota"));
$jml_buku = mysqli_num_rows(mysqli_query($koneksi, "SELECT id_buku FROM buku"));
$jml_pinjam = mysqli_num_rows(mysqli_query($koneksi, "SELECT id_pinjam FROM peminjaman WHERE status='Dipinjam'"));

$q_terlambat = mysqli_query($koneksi, "SELECT COUNT(id_pinjam) as terlambat FROM peminjaman WHERE status='Dipinjam' AND tgl_kembali_seharusnya < CURDATE()");
$d_terlambat = mysqli_fetch_assoc($q_terlambat);
$jml_terlambat = $d_terlambat['terlambat'];

// 2. Data Grafik (8 Minggu Terakhir)
$bulan_labels = [];
$data_peminjaman = [];
for($i = 7; $i >= 0; $i--) {
    $days_start = ($i * 7) + 6;
    $days_end = ($i * 7);
    $start_date = date('Y-m-d', strtotime("-$days_start days"));
    $end_date = date('Y-m-d', strtotime("-$days_end days"));
    
    $label = date('d/m', strtotime($start_date)) . '-' . date('d/m', strtotime($end_date));
    $bulan_labels[] = $label;
    
    $q_chart = mysqli_query($koneksi, "SELECT COUNT(id_pinjam) as total FROM peminjaman WHERE DATE(tgl_pinjam) >= '$start_date' AND DATE(tgl_pinjam) <= '$end_date'");
    $d_chart = mysqli_fetch_assoc($q_chart);
    $data_peminjaman[] = $d_chart['total'];
}
?>


<!-- Baris 1: Kartu Ringkasan -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4 mb-xl-0">
        <div class="card dash-card bg-primary text-white shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase fw-bold mb-1 opacity-75">Total Anggota</h6>
                        <h2 class="mb-0 fw-bold"><?php echo number_format($jml_anggota); ?></h2>
                    </div>
                    <div class="fs-1 opacity-75"><i class="bi bi-people"></i></div>
                </div>
                <i class="bi bi-people-fill dash-icon-bg"></i>
            </div>
            <div class="card-footer bg-white bg-opacity-10 border-0 text-white text-center small">
                <a href="anggota.php" class="text-white text-decoration-none">Lihat Detail <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4 mb-xl-0">
        <div class="card dash-card bg-success text-white shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase fw-bold mb-1 opacity-75">Total Buku</h6>
                        <h2 class="mb-0 fw-bold"><?php echo number_format($jml_buku); ?></h2>
                    </div>
                    <div class="fs-1 opacity-75"><i class="bi bi-book"></i></div>
                </div>
                <i class="bi bi-book-half dash-icon-bg"></i>
            </div>
            <div class="card-footer bg-white bg-opacity-10 border-0 text-white text-center small">
                <a href="buku.php" class="text-white text-decoration-none">Lihat Detail <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4 mb-xl-0">
        <div class="card dash-card bg-warning text-dark shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase fw-bold mb-1 opacity-75">Sedang Dipinjam</h6>
                        <h2 class="mb-0 fw-bold"><?php echo number_format($jml_pinjam); ?></h2>
                    </div>
                    <div class="fs-1 opacity-75"><i class="bi bi-arrow-left-right"></i></div>
                </div>
                <i class="bi bi-cart-check dash-icon-bg text-dark"></i>
            </div>
            <div class="card-footer border-0 text-center small" style="background-color: rgba(0,0,0,0.05);">
                <a href="peminjaman.php?filter_status=Dipinjam" class="text-dark text-decoration-none">Lihat Detail <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4 mb-xl-0">
        <div class="card dash-card bg-danger text-white shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase fw-bold mb-1 opacity-75">Terlambat Kembali</h6>
                        <h2 class="mb-0 fw-bold"><?php echo number_format($jml_terlambat); ?></h2>
                    </div>
                    <div class="fs-1 opacity-75"><i class="bi bi-exclamation-triangle"></i></div>
                </div>
                <i class="bi bi-alarm dash-icon-bg"></i>
            </div>
            <div class="card-footer bg-white bg-opacity-10 border-0 text-white text-center small">
                <a href="peminjaman.php" class="text-white text-decoration-none">Tindak Lanjuti <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>
</div>

<!-- Baris 2: Grafik & Transaksi -->
<div class="row">
    <!-- Grafik -->
    <div class="col-xl-8 col-lg-7 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                <h5 class="fw-bold m-0"><i class="bi bi-bar-chart-line text-primary me-2"></i> Tren Peminjaman (8 Minggu Terakhir)</h5>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="peminjamanChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Buku Terpopuler -->
    <div class="col-xl-4 col-lg-5 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                <h5 class="fw-bold m-0"><i class="bi bi-star-fill text-warning me-2"></i> Buku Terpopuler</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <?php
                    $q_populer = mysqli_query($koneksi, "SELECT b.judul_buku, b.cover, b.stok, COUNT(p.id_pinjam) as total 
                                                        FROM peminjaman p 
                                                        JOIN buku b ON p.id_buku = b.id_buku 
                                                        GROUP BY p.id_buku 
                                                        ORDER BY total DESC LIMIT 5");
                    
                    if(mysqli_num_rows($q_populer) > 0) {
                        while($pop = mysqli_fetch_assoc($q_populer)) {
                            $cover_src = ($pop['cover'] != '' && file_exists('../uploads/covers/'.$pop['cover'])) ? '../uploads/covers/'.$pop['cover'] : '../assets/img/book-placeholder.jpg';
                    ?>
                        <li class="list-group-item px-0 border-bottom-0 mb-2">
                            <div class="d-flex align-items-center">
                                <img src="<?php echo $cover_src; ?>" alt="Cover" class="rounded me-3 shadow-sm" style="width: 45px; height: 60px; object-fit: cover;">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 text-truncate" style="max-width: 180px;" title="<?php echo htmlspecialchars($pop['judul_buku']); ?>">
                                        <?php echo htmlspecialchars($pop['judul_buku']); ?>
                                    </h6>
                                    <small class="text-muted"><i class="bi bi-arrow-up-right-circle text-success"></i> Dipinjam <?php echo $pop['total']; ?> kali</small>
                                </div>
                            </div>
                        </li>
                    <?php 
                        }
                    } else {
                        echo "<li class='list-group-item px-0 text-center text-muted py-4'>Belum ada data peminjaman.</li>";
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Baris 3: Tabel Transaksi Terbaru -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-bottom-0 pt-4 pb-2 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold m-0"><i class="bi bi-clock-history text-info me-2"></i> 5 Transaksi Peminjaman Terakhir</h5>
        <a href="peminjaman.php" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Tgl Pinjam</th>
                        <th>Anggota</th>
                        <th>Buku</th>
                        <th>Batas Kembali</th>
                        <th class="pe-4">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $q_recent = mysqli_query($koneksi, "SELECT p.*, a.nama_anggota, b.judul_buku 
                                                        FROM peminjaman p
                                                        JOIN anggota a ON p.id_anggota = a.id_anggota
                                                        JOIN buku b ON p.id_buku = b.id_buku
                                                        ORDER BY p.id_pinjam DESC LIMIT 5");
                                                        
                    if(mysqli_num_rows($q_recent) > 0) {
                        while($rec = mysqli_fetch_assoc($q_recent)) {
                    ?>
                        <tr>
                            <td class="ps-4"><?php echo date('d M Y', strtotime($rec['tgl_pinjam'])); ?></td>
                            <td>
                                <a href="#" onclick="viewAnggota(<?php echo $rec['id_anggota']; ?>); return false;" class="text-decoration-none text-primary fw-bold">
                                    <?php echo $rec['nama_anggota']; ?>
                                </a>
                            </td>
                            <td><span class="text-truncate d-inline-block" style="max-width: 250px;"><?php echo $rec['judul_buku']; ?></span></td>
                            <td><?php echo date('d M Y', strtotime($rec['tgl_kembali_seharusnya'])); ?></td>
                            <td class="pe-4">
                                <?php if($rec['status'] == 'Dipinjam'): ?>
                                    <span class="badge bg-primary rounded-pill">Dipinjam</span>
                                <?php else: ?>
                                    <span class="badge bg-success rounded-pill">Kembali</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='5' class='text-center py-4 text-muted'>Belum ada transaksi.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('peminjamanChart').getContext('2d');
    
    // Convert PHP arrays to JS
    const labels = <?php echo json_encode($bulan_labels); ?>;
    const dataPoints = <?php echo json_encode($data_peminjaman); ?>;
    
    const primaryColor = getComputedStyle(document.documentElement).getPropertyValue('--bs-primary').trim() || '#a8452c';
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Jumlah Peminjaman',
                data: dataPoints,
                backgroundColor: 'rgba(194, 89, 59, 0.1)',
                borderColor: primaryColor,
                borderWidth: 3,
                pointBackgroundColor: primaryColor,
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: primaryColor,
                pointRadius: 5,
                pointHoverRadius: 7,
                fill: true,
                tension: 0.4 // Smooth curve
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    padding: 12,
                    titleFont: { size: 14 },
                    bodyFont: { size: 14, weight: 'bold' },
                    displayColors: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        precision: 0
                    },
                    grid: {
                        color: 'rgba(0,0,0,0.05)',
                        drawBorder: false
                    }
                },
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    }
                }
            }
        }
    });
});
</script>

<?php include 'footer.php'; ?>

