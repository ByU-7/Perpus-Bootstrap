    <!-- Mega Footer -->
    <footer id="tentang">
        <div class="container">
            <div class="row g-5 mb-5">
                <!-- Kolom 1: Tentang -->
                <div class="col-lg-4">
                    <h4 class="footer-heading"><i class="bi bi-bank text-warning me-2"></i>Perpus Akademik</h4>
                    <p style="line-height: 1.8;">Sistem Katalog Online (OPAC) Perpustakaan Akademik. Gunakan platform ini untuk mengeksplorasi koleksi rak kami dan memastikan status ketersediaan buku secara <i>real-time</i> sebelum Anda berkunjung.</p>
                </div>
                
                <!-- Kolom 2: Tautan Cepat -->
                <div class="col-lg-2 offset-lg-1 col-md-4">
                    <h5 class="footer-heading">Navigasi</h5>
                    <a href="index.php" class="footer-link">Beranda</a>
                    <a href="katalog.php" class="footer-link">Katalog Utama</a>
                    <a href="index.php#terbaru" class="footer-link">Buku Terbaru</a>
                    <a href="index.php#populer" class="footer-link">Paling Populer</a>
                </div>

                <!-- Kolom 3: Kontak & Alamat -->
                <div class="col-lg-3 col-md-4">
                    <h5 class="footer-heading">Hubungi Kami</h5>
                    <div class="contact-info mb-2"><i class="bi bi-geo-alt-fill"></i> Jl. Pendidikan No. 123, Kampus Merdeka, Jakarta 12345</div>
                    <div class="contact-info mb-2"><i class="bi bi-envelope-fill"></i> info@perpusakademik.ac.id</div>
                    <div class="contact-info mb-2"><i class="bi bi-telephone-fill"></i> +62 21 555 7890</div>
                    <div class="contact-info"><i class="bi bi-clock-fill"></i> Senin - Jumat: 08:00 - 16:00</div>
                </div>

                <!-- Kolom 4: Kirim Pesan -->
                <div class="col-lg-2 col-md-4">
                    <h5 class="footer-heading">Saran & Masukan</h5>
                    <form onsubmit="alert('Fitur pesan sedang dalam pengembangan!'); return false;">
                        <input type="email" class="form-control bg-dark border-secondary text-white mb-2 rounded-0" placeholder="Email Anda" required>
                        <textarea class="form-control bg-dark border-secondary text-white mb-2 rounded-0" rows="2" placeholder="Pesan singkat..." required></textarea>
                        <button type="submit" class="btn btn-outline-light btn-sm w-100 rounded-0">Kirim Pesan</button>
                    </form>
                </div>
            </div>
            
            <div class="border-top border-secondary pt-4 text-center mt-4">
                <p class="mb-0" style="font-size: 0.9rem; opacity: 0.7;">&copy; <?php echo date('Y'); ?> Sistem Informasi Perpustakaan Akademik. All rights reserved.</p>
                <div class="mt-2">
                    <a href="#" class="text-secondary me-3"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="text-secondary me-3"><i class="bi bi-twitter-x"></i></a>
                    <a href="#" class="text-secondary me-3"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="text-secondary"><i class="bi bi-youtube"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script>
        // Memfokuskan input form saat modal pencarian terbuka
        var searchModal = document.getElementById('searchModal')
        searchModal.addEventListener('shown.bs.modal', function () {
            searchModal.querySelector('input').focus()
        })
    </script>
</body>
</html>
