<style>
.hover-gold { transition: all 0.3s ease; display: inline-block; color: #ced4da !important; }
.hover-gold:hover { color: #b8975a !important; transform: translateX(8px); }
.form-control::placeholder { color: rgba(255, 255, 255, 0.6) !important; font-style: italic; }
</style>
    <!-- Footer -->
    <footer id="tentang" style="background: linear-gradient(rgba(17, 26, 34, 0.95), rgba(17, 26, 34, 0.98)), url('https://images.unsplash.com/photo-1507842217343-583bb7270b66?auto=format&fit=crop&w=2000&q=80') center/cover; padding: 60px 0 20px 0; border-top: 5px solid #b8975a; color: #f8f9fa;">
        <div class="container" data-aos="fade-up">
            <div class="row g-5 mb-4">
                <div class="col-lg-4">
                    <h4 class="text-white serif-font mb-4"><i class="bi bi-book-half text-warning me-2"></i>Perpus Bayu</h4>
                    <p style="line-height: 1.8; color: #ced4da;">Sistem Katalog Online (OPAC) Perpustakaan Akademik. Gunakan platform ini untuk mengeksplorasi koleksi rak kami dan memastikan status ketersediaan buku secara real-time.</p>
                    <div class="d-flex gap-3 mt-4">
                        <a href="#" class="btn btn-outline-light rounded-circle"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="btn btn-outline-light rounded-circle"><i class="bi bi-twitter-x"></i></a>
                        <a href="#" class="btn btn-outline-light rounded-circle"><i class="bi bi-youtube"></i></a>
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
                    <form>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <input type="text" class="form-control bg-transparent text-white border-secondary" placeholder="Nama Anda" style="border-radius: 8px; padding: 12px 15px;">
                            </div>
                            <div class="col-md-6">
                                <input type="email" class="form-control bg-transparent text-white border-secondary" placeholder="Alamat Email" style="border-radius: 8px; padding: 12px 15px;">
                            </div>
                            <div class="col-12">
                                <textarea class="form-control bg-transparent text-white border-secondary" rows="3" placeholder="Tuliskan saran atau pertanyaan Anda..." style="border-radius: 8px; padding: 12px 15px;"></textarea>
                            </div>
                            <div class="col-12">
                                <button type="button" class="btn btn-warning fw-bold px-4 py-2" style="background-color: #b8975a; border: none; color: white; border-radius: 8px;">Kirim Pesan</button>
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
    <script>
        // Inline Expanding Search Logic
        const searchToggle = document.getElementById('navSearchToggle');
        const searchWrapper = document.getElementById('navSearchWrapper');
        const searchInput = document.getElementById('navSearchInput');
        const searchIcon = document.getElementById('navSearchIcon');
        const navMenu = document.getElementById('nav-links-menu');

        if(searchToggle) {
            searchToggle.addEventListener('click', function(e) {
                const isKatalog = this.getAttribute('data-is-katalog') === 'true';
                
                if(isKatalog) {
                    // Jika di katalog.php, gulir ke form pencarian utama
                    const catalogSearch = document.getElementById('catalogMainSearch');
                    if(catalogSearch) {
                        catalogSearch.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        setTimeout(() => {
                            catalogSearch.querySelector('input').focus();
                        }, 500);
                    }
                } else {
                    // Jika bukan di katalog, expand search inline
                    e.preventDefault();
                    if(searchWrapper.classList.contains('active')) {
                        // Kalau sedang aktif tapi input kosong, tutup. Kalau ada isi, submit form.
                        if(searchInput.value.trim() !== "") {
                            document.getElementById('navSearchForm').submit();
                        } else {
                            closeSearch();
                        }
                    } else {
                        // Buka pencarian
                        searchWrapper.classList.add('active');
                        navMenu.classList.add('fade-out');
                        searchIcon.classList.remove('bi-search');
                        searchIcon.classList.add('bi-x-lg');
                        setTimeout(() => searchInput.focus(), 300);
                    }
                }
            });
        }

        function closeSearch() {
            if(searchWrapper) {
                searchWrapper.classList.remove('active');
                navMenu.classList.remove('fade-out');
                searchIcon.classList.remove('bi-x-lg');
                searchIcon.classList.add('bi-search');
                searchInput.value = '';
            }
        }

        // Tutup pencarian jika klik di luar
        document.addEventListener('click', function(e) {
            if(searchWrapper && searchWrapper.classList.contains('active')) {
                if(!searchWrapper.contains(e.target)) {
                    closeSearch();
                }
            }
        });

        // AOS initialization is now handled by book-transition.js 
        // to ensure it only runs after page transitions are complete.
    </script>
</body>
</html>
