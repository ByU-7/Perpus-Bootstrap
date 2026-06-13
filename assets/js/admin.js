// Global Admin Scripts

$(document).ready(function() {
    // Inisialisasi Select2 secara global (jika ada class .select2)
    $('.select2').each(function() {
        $(this).select2({
            theme: 'bootstrap-5',
            placeholder: $(this).data('placeholder') || "Ketik atau pilih...",
            allowClear: true
        });
    });
});

// Modal Detail Anggota
function viewAnggota(id) {
    var modalEl = document.getElementById('modalDetailAnggota');
    if (!modalEl) return;
    var modal = new bootstrap.Modal(modalEl);
    
    // Skeleton Loader HTML
    var skeletonHtml = `
        <div class="row">
            <div class="col-md-4 text-center border-end border-light">
                <div class="skeleton skeleton-avatar mx-auto mb-3"></div>
                <div class="skeleton skeleton-text mx-auto mb-2" style="height:2rem; width:80%;"></div>
                <div class="skeleton skeleton-badge mx-auto mb-3"></div>
            </div>
            <div class="col-md-8 px-4">
                <div class="skeleton skeleton-text mb-4" style="height:1.5rem; width:40%;"></div>
                <div class="skeleton skeleton-text mb-2"></div>
                <div class="skeleton skeleton-text mb-2"></div>
                <div class="skeleton skeleton-text short mb-2"></div>
                <div class="skeleton skeleton-text mb-4"></div>
                
                <div class="skeleton skeleton-text mb-2 mt-4" style="height:1.5rem; width:50%;"></div>
                <div class="skeleton skeleton-img" style="height:120px;"></div>
            </div>
        </div>
    `;
    
    document.getElementById('modalDetailAnggotaContent').innerHTML = skeletonHtml;
    modal.show();

    // Panggil endpoint AJAX
    $.ajax({
        url: 'api/ajax_anggota_detail.php',
        type: 'GET',
        data: { id: id },
        success: function(response) {
            $('#modalDetailAnggotaContent').html(response);
        },
        error: function() {
            $('#modalDetailAnggotaContent').html('<div class="alert alert-danger text-center">Terjadi kesalahan saat memuat data.</div>');
        }
    });
}

// SweetAlert2 Global Confirmation
function confirmAction(e, url, title, text) {
    e.preventDefault();
    if(typeof Swal === 'undefined') {
        if(confirm(title + '\n' + text)) {
            window.location.href = url;
        }
        return;
    }
    Swal.fire({
        title: title || 'Apakah Anda Yakin?',
        text: text || "Tindakan ini tidak dapat dibatalkan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#a8452c',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Lanjutkan!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state while navigating
            Swal.fire({
                title: 'Memproses...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            window.location.href = url;
        }
    });
}

// Global Image Preview
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            const previewId = input.dataset.previewTarget || 'previewCover';
            const previewEl = document.getElementById(previewId);
            if(previewEl) {
                previewEl.src = e.target.result;
                previewEl.classList.remove('d-none');
            }
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        const previewId = input.dataset.previewTarget || 'previewCover';
        const previewEl = document.getElementById(previewId);
        if(previewEl) previewEl.classList.add('d-none');
    }
}
