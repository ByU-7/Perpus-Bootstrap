<style>
.hover-gold { transition: all 0.3s ease; display: inline-block; color: #ced4da !important; }
.hover-gold:hover { color: #e6a756 !important; transform: translateX(8px); }
.form-control::placeholder { color: rgba(255, 255, 255, 0.6) !important; font-style: italic; }
.btn-social { transition: all 0.3s ease; color: #f8f9fa; border-color: #f8f9fa; background: transparent; }
.btn-social:hover { background-color: #e6a756; border-color: #e6a756; color: #111a22; transform: translateY(-3px); box-shadow: 0 4px 10px rgba(230, 167, 86, 0.3); }
#formSaran .form-control:focus { border-color: #e6a756; box-shadow: 0 0 0 0.25rem rgba(230, 167, 86, 0.25); background-color: rgba(255,255,255,0.05) !important; color: white; }
</style>
    <!-- Footer -->
    <footer id="tentang" style="background: linear-gradient(rgba(17, 26, 34, 0.75), rgba(17, 26, 34, 0.85)), url('https://images.unsplash.com/photo-1507842217343-583bb7270b66?auto=format&fit=crop&w=2000&q=80') center/cover; padding: 60px 0 20px 0; border-top: 5px solid #e6a756; color: #f8f9fa;">
        <div class="container" data-aos="fade-up">
            <div class="row g-5 mb-4">
                <div class="col-lg-4">
                    <h4 class="text-white serif-font mb-4"><i class="bi bi-book-half me-2" style="color: #d5d0c4;"></i>Perpus Bayu</h4>
                    <p style="line-height: 1.8; color: #ced4da;">Sistem Katalog Online (OPAC) Perpustakaan Akademik. Gunakan platform ini untuk mengeksplorasi koleksi rak kami dan memastikan status ketersediaan buku secara real-time.</p>
                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <a href="https://instagram.com/byudstr777" target="_blank" class="btn btn-social rounded-pill px-3"><i class="bi bi-instagram me-2"></i>@byudstr777</a>
                        <a href="https://tiktok.com/@byu_777" target="_blank" class="btn btn-social rounded-pill px-3"><i class="bi bi-tiktok me-2"></i>@byu_777</a>
                        <a href="https://youtube.com/" target="_blank" class="btn btn-social rounded-pill px-3"><i class="bi bi-youtube me-2"></i>Perpus Bayu Official</a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <h5 class="text-white serif-font mb-4">Tautan Cepat</h5>
                    <ul class="list-unstyled" style="line-height: 2.2;">
                        <li><a href="index.php#beranda" class="text-decoration-none hover-gold">Beranda Utama</a></li>
                        <li><a href="katalog.php" class="text-decoration-none hover-gold">Semua Katalog</a></li>
                        <li><a href="admin/login.php" class="text-decoration-none hover-gold">Masuk Pustakawan</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-5 col-md-6">
                    <h5 class="text-white serif-font mb-4">Saran & Masukan</h5>
                    <form id="formSaran">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <input type="text" name="nama" class="form-control bg-transparent text-white border-secondary" placeholder="Nama Anda" style="border-radius: 8px; padding: 12px 15px;" required>
                            </div>
                            <div class="col-md-6">
                                <input type="email" name="email" class="form-control bg-transparent text-white border-secondary" placeholder="Alamat Email" style="border-radius: 8px; padding: 12px 15px;" required>
                            </div>
                            <div class="col-12">
                                <textarea name="pesan" class="form-control bg-transparent text-white border-secondary" rows="3" placeholder="Tuliskan saran atau pertanyaan Anda..." style="border-radius: 8px; padding: 12px 15px;" required></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn fw-bold px-4 py-2" id="btnKirimSaran" style="background-color: #e6a756; border: none; color: #111a22; border-radius: 8px;">
                                    <span class="btn-text">Kirim Pesan</span>
                                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="border-top border-secondary pt-4 text-center mt-5">
                <p class="mb-0" style="font-size: 0.9rem; opacity: 0.7;">&copy; <?php echo date('Y'); ?> Perpus Bayu. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="assets/js/book-transition.js"></script>
    <script src="assets/js/public.js"></script>

    <!-- Modal Detail Buku -->
    <div class="modal fade" id="detailBukuModal" tabindex="-1" aria-labelledby="detailBukuModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 8px; background-color: #fdfbf7;">
          <div class="modal-header border-0 pb-0">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-4 p-md-5 pt-0" id="detailBukuContent">
            <!-- Konten akan dimuat via AJAX -->
            <div class="text-center py-5">
                <div class="spinner-border text-warning" role="status" style="color: #654321 !important;">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 text-muted">Memuat detail buku...</p>
            </div>
          </div>
        </div>
      </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>

