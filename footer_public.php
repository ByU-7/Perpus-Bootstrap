    <!-- Footer -->
    <footer style="background: linear-gradient(rgba(17, 26, 34, 0.92), rgba(17, 26, 34, 0.98)), url('https://images.unsplash.com/photo-1507842217343-583bb7270b66?auto=format&fit=crop&w=2000&q=80') center/cover; padding: 60px 0 20px 0; border-top: 5px solid #b8975a; color: #adb5bd;">
        <div class="container" data-aos="fade-up">
            <div class="row g-4 mb-4">
                <div class="col-lg-5">
                    <h4 class="text-white serif-font mb-3"><i class="bi bi-book-half text-warning me-2"></i>Perpus Bayu</h4>
                    <p style="line-height: 1.8; max-width: 400px;">Sistem Katalog Online (OPAC) Perpustakaan Akademik. Gunakan platform ini untuk mengeksplorasi koleksi rak kami dan memastikan status ketersediaan buku secara real-time.</p>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h5 class="text-white serif-font mb-3">Tautan Cepat</h5>
                    <a href="index.php" class="text-decoration-none text-secondary d-block mb-2">Beranda Utama</a>
                    <a href="katalog.php" class="text-decoration-none text-secondary d-block mb-2">Semua Katalog</a>
                    <a href="admin/login.php" class="text-decoration-none text-secondary d-block">Masuk Pustakawan</a>
                </div>
                <div class="col-lg-4 col-md-6">
                    <h5 class="text-white serif-font mb-3">Ikuti Kami</h5>
                    <p>Dapatkan pembaruan koleksi terbaru melalui media sosial kami.</p>
                    <div class="d-flex gap-3 mt-3">
                        <a href="#" class="btn btn-outline-secondary rounded-circle"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="btn btn-outline-secondary rounded-circle"><i class="bi bi-twitter-x"></i></a>
                        <a href="#" class="btn btn-outline-secondary rounded-circle"><i class="bi bi-youtube"></i></a>
                    </div>
                </div>
            </div>
            <div class="border-top border-secondary pt-4 text-center mt-4">
                <p class="mb-0" style="font-size: 0.9rem; opacity: 0.7;">&copy; <?php echo date('Y'); ?> Perpus Bayu. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Splash Screen Logic
        window.addEventListener('load', function() {
            setTimeout(function() {
                var splash = document.getElementById('splash-screen');
                if(splash) {
                    splash.style.opacity = '0';
                    splash.style.visibility = 'hidden';
                }
            }, 1000); // Tampil 1 detik
        });

        // Inisialisasi AOS (Animasi Scroll)
        AOS.init({
            duration: 800,
            once: true,
            offset: 100
        });

        // Memfokuskan input form saat modal pencarian terbuka
        var searchModal = document.getElementById('searchModal')
        if(searchModal) {
            searchModal.addEventListener('shown.bs.modal', function () {
                var input = searchModal.querySelector('input');
                if(input) input.focus()
            })
        }
    </script>
</body>
</html>
