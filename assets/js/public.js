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
                if(navMenu) navMenu.classList.add('fade-out');
                if(searchIcon) {
                    searchIcon.classList.remove('bi-search');
                    searchIcon.classList.add('bi-x-lg');
                }
                setTimeout(() => searchInput.focus(), 300);
            }
        }
    });
}

function closeSearch() {
    if(searchWrapper) {
        searchWrapper.classList.remove('active');
        if(navMenu) navMenu.classList.remove('fade-out');
        if(searchIcon) {
            searchIcon.classList.remove('bi-x-lg');
            searchIcon.classList.add('bi-search');
        }
        if(searchInput) searchInput.value = '';
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

// Modal Detail Buku
function openBookModal(id_buku) {
    var modalEl = document.getElementById('detailBukuModal');
    if(!modalEl) return;
    var modal = bootstrap.Modal.getInstance(modalEl);
    if (!modal) {
        modal = new bootstrap.Modal(modalEl);
    }
    var contentDiv = document.getElementById('detailBukuContent');
    // Tampilkan loading state menggunakan Skeleton Loader
    contentDiv.innerHTML = `
        <div class="row">
            <div class="col-md-5 mb-4 mb-md-0">
                <div class="skeleton skeleton-img" style="height:400px; border-radius: 8px;"></div>
            </div>
            <div class="col-md-7">
                <div class="skeleton skeleton-title mb-2" style="width:100%;"></div>
                <div class="skeleton skeleton-text mb-4" style="width:50%;"></div>
                
                <div class="skeleton skeleton-text mb-2"></div>
                <div class="skeleton skeleton-text mb-2"></div>
                <div class="skeleton skeleton-text mb-2" style="width:70%;"></div>
                
                <div class="skeleton skeleton-text mt-4 mb-2"></div>
                <div class="skeleton skeleton-text mb-2"></div>
                <div class="skeleton skeleton-text mb-2"></div>
                <div class="skeleton skeleton-text mb-2" style="width:40%;"></div>
            </div>
        </div>
    `;
    
    modal.show();
    
    // Fetch data
    fetch('api/api_detail_buku.php?id=' + id_buku)
        .then(response => response.text())
        .then(html => {
            contentDiv.innerHTML = html;
        })
        .catch(error => {
            contentDiv.innerHTML = '<div class="alert alert-danger">Terjadi kesalahan saat memuat data.</div>';
        });
}

// AJAX Saran Form
document.addEventListener('DOMContentLoaded', function() {
    const formSaran = document.getElementById('formSaran');
    if(formSaran) {
        formSaran.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('btnKirimSaran');
            const btnText = btn.querySelector('.btn-text');
            const spinner = btn.querySelector('.spinner-border');
            
            btn.disabled = true;
            btnText.classList.add('d-none');
            spinner.classList.remove('d-none');

            const formData = new FormData(this);

            fetch('api/ajax_submit_saran.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.status === 'success') {
                    if(typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Terkirim!',
                            text: data.message,
                            confirmButtonColor: '#e6a756'
                        });
                    } else {
                        alert(data.message);
                    }
                    formSaran.reset();
                } else {
                    if(typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: data.message,
                            confirmButtonColor: '#e6a756'
                        });
                    } else {
                        alert(data.message);
                    }
                }
            })
            .catch(error => {
                if(typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Kesalahan',
                        text: 'Gagal menghubungi server.',
                        confirmButtonColor: '#e6a756'
                    });
                } else {
                    alert('Gagal menghubungi server.');
                }
            })
            .finally(() => {
                btn.disabled = false;
                btnText.classList.remove('d-none');
                spinner.classList.add('d-none');
            });
        });
    }

    // Index.php Animations
    const initAnimations = () => {
        document.body.classList.add('loaded');

        const counters = document.querySelectorAll('.stat-number');
        if(counters.length > 0) {
            const duration = 2000; // 2 detik

            const animateCounters = () => {
                counters.forEach(counter => {
                    const target = +counter.getAttribute('data-target');
                    let startTime = null;

                    const updateCount = (currentTime) => {
                        if (!startTime) startTime = currentTime;
                        const progress = currentTime - startTime;
                        const percentage = Math.min(progress / duration, 1);
                        
                        const easeOut = 1 - Math.pow(1 - percentage, 3);
                        const currentCount = Math.ceil(target * easeOut);
                        
                        counter.innerText = currentCount.toLocaleString('id-ID');

                        if (progress < duration) {
                            requestAnimationFrame(updateCount);
                        } else {
                            counter.innerText = target.toLocaleString('id-ID');
                        }
                    };
                    requestAnimationFrame(updateCount);
                });
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if(entry.isIntersecting) {
                        animateCounters();
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.5 });
            
            const statsSection = document.querySelector('.stats-section');
            if(statsSection) observer.observe(statsSection);
        }
    };

    if (window.bookTransitionFinished) {
        initAnimations();
    } else {
        window.triggerInitAnimations = () => {
            initAnimations();
        };
    }
});
