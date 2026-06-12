<?php include 'header.php'; ?>
<?php include '../config/koneksi.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Data Buku</h2>
    <button onclick="openModalTambah()" class="btn btn-primary"><i class="bi bi-journal-plus"></i> Tambah Buku</button>
</div>

<div id="alertContainer"></div>

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
                <tbody id="bukuTableBody">
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
                            <button onclick="openModalEdit(<?php echo $d['id_buku']; ?>)" class="btn btn-sm btn-warning mb-1"><i class="bi bi-pencil-square"></i> Edit</button>
                            <button onclick="hapusBuku(<?php echo $d['id_buku']; ?>)" class="btn btn-sm btn-danger mb-1"><i class="bi bi-trash"></i> Hapus</button>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form Buku -->
<div class="modal fade" id="modalFormBuku" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalFormBukuTitle">Tambah Data Buku</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formBuku" onsubmit="simpanBuku(event)" enctype="multipart/form-data">
          <div class="modal-body">
                <input type="hidden" name="id_buku" id="id_buku">
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Kode Buku</label>
                        <input type="text" class="form-control" name="kode_buku" id="kode_buku" placeholder="Contoh: BK-001" required>
                    </div>
                    <div class="col-md-8 mb-3">
                        <label class="form-label">Judul Buku</label>
                        <input type="text" class="form-control" name="judul" id="judul" required>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Pengarang</label>
                        <input type="text" class="form-control" name="pengarang" id="pengarang" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Penerbit</label>
                        <input type="text" class="form-control" name="penerbit" id="penerbit" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tahun Terbit</label>
                        <input type="number" class="form-control" name="tahun" id="tahun" min="1900" max="2099" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Jumlah Stok</label>
                        <input type="number" class="form-control" name="stok" id="stok" min="0" required>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-bold">Sinopsis / Deskripsi Buku</label>
                        <textarea class="form-control" name="sinopsis" id="sinopsis" rows="4" placeholder="Tuliskan sinopsis singkat buku ini..."></textarea>
                    </div>
                </div>

                <div class="row border-top pt-4 mt-3">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Daftar Genre Buku</label>
                        <select class="form-select select2" name="genre[]" id="genre_select" multiple="multiple" required style="width: 100%;">
                            <?php 
                            $q_genre2 = mysqli_query($koneksi, "SELECT * FROM genre ORDER BY nama_genre ASC");
                            while($g = mysqli_fetch_array($q_genre2)): 
                            ?>
                                <option value="<?php echo $g['id_genre']; ?>"><?php echo $g['nama_genre']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Cover Buku (Max 2MB)</label>
                        <div class="mb-2">
                            <img id="previewCover" src="#" alt="Preview" class="img-thumbnail shadow-sm d-none" style="max-height: 150px;">
                        </div>
                        <input type="file" class="form-control" name="cover" id="cover" accept=".jpg,.jpeg,.png" onchange="previewImage(this);">
                        <small class="text-muted" id="coverHelp">Biarkan kosong jika tidak ada cover.</small>
                    </div>
                </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary" id="btnSimpan"><i class="bi bi-save"></i> Simpan Buku</button>
          </div>
      </form>
    </div>
  </div>
</div>

<script>
let modalBuku;

document.addEventListener('DOMContentLoaded', function() {
    modalBuku = new bootstrap.Modal(document.getElementById('modalFormBuku'));
    // Initialize Select2 in modal
    $('#genre_select').select2({
        dropdownParent: $('#modalFormBuku')
    });
});

function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewCover').src = e.target.result;
            document.getElementById('previewCover').classList.remove('d-none');
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        document.getElementById('previewCover').classList.add('d-none');
    }
}

function showAlert(type, message) {
    const alertHtml = \`<div class="alert alert-\${type} alert-dismissible fade show">
        \${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>\`;
    document.getElementById('alertContainer').innerHTML = alertHtml;
}

function reloadTableData() {
    fetch(window.location.href)
        .then(res => res.text())
        .then(html => {
            const doc = new DOMParser().parseFromString(html, 'text/html');
            const newTbody = doc.getElementById('bukuTableBody');
            if(newTbody) {
                document.getElementById('bukuTableBody').innerHTML = newTbody.innerHTML;
            }
        });
}

function openModalTambah() {
    document.getElementById('formBuku').reset();
    document.getElementById('id_buku').value = '';
    document.getElementById('modalFormBukuTitle').innerText = 'Tambah Data Buku';
    document.getElementById('btnSimpan').innerText = 'Simpan Buku';
    document.getElementById('btnSimpan').className = 'btn btn-primary';
    document.getElementById('previewCover').classList.add('d-none');
    document.getElementById('coverHelp').innerText = 'Biarkan kosong jika tidak ada cover.';
    $('#genre_select').val(null).trigger('change');
    modalBuku.show();
}

function openModalEdit(id) {
    fetch('api_buku.php?action=get&id=' + id)
        .then(res => res.json())
        .then(res => {
            if(res.status === 'success') {
                const data = res.data;
                document.getElementById('id_buku').value = data.id_buku;
                document.getElementById('kode_buku').value = data.kode_buku;
                document.getElementById('judul').value = data.judul_buku;
                document.getElementById('pengarang').value = data.pengarang;
                document.getElementById('penerbit').value = data.penerbit;
                document.getElementById('tahun').value = data.tahun_terbit;
                document.getElementById('stok').value = data.stok;
                document.getElementById('sinopsis').value = data.sinopsis;
                
                $('#genre_select').val(data.genres).trigger('change');
                
                if(data.cover) {
                    document.getElementById('previewCover').src = '../assets/img/covers/' + data.cover;
                    document.getElementById('previewCover').classList.remove('d-none');
                } else {
                    document.getElementById('previewCover').classList.add('d-none');
                }
                
                document.getElementById('modalFormBukuTitle').innerText = 'Edit Data Buku';
                document.getElementById('btnSimpan').innerHTML = '<i class="bi bi-save"></i> Update Buku';
                document.getElementById('btnSimpan').className = 'btn btn-warning';
                document.getElementById('coverHelp').innerText = 'Biarkan kosong jika tidak ingin mengganti cover.';
                
                modalBuku.show();
            } else {
                showAlert('danger', res.message);
            }
        });
}

function simpanBuku(e) {
    e.preventDefault();
    const form = document.getElementById('formBuku');
    const formData = new FormData(form);
    
    // Select2 multiple values
    const genres = $('#genre_select').val();
    formData.set('genre', genres);

    fetch('api_buku.php?action=save', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(res => {
        if(res.status === 'success') {
            modalBuku.hide();
            showAlert('success', res.message);
            reloadTableData();
        } else {
            showAlert('danger', res.message);
        }
    })
    .catch(err => {
        showAlert('danger', 'Terjadi kesalahan sistem.');
    });
}

function hapusBuku(id) {
    if(confirm('Yakin ingin menghapus data buku ini?')) {
        const formData = new FormData();
        formData.append('id', id);
        
        fetch('api_buku.php?action=delete', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(res => {
            if(res.status === 'success') {
                showAlert('success', res.message);
                reloadTableData();
            } else {
                showAlert('danger', res.message);
            }
        });
    }
}
</script>

<?php include 'footer.php'; ?>
