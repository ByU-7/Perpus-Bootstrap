            <footer class="mt-5 pt-3 border-top text-center text-muted">
                <small>&copy; 2026 Sistem Informasi Perpustakaan. All rights reserved.</small>
            </footer>
        </main> <!-- End of Main Content Area (main) -->
</div> <!-- End of Container-Fluid -->

<!-- Modal Global Detail Anggota -->
<div class="modal fade" id="modalDetailAnggota" tabindex="-1" aria-labelledby="modalDetailAnggotaLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header border-bottom-0 pb-0">
        <h5 class="modal-title" id="modalDetailAnggotaLabel"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body pt-0" id="modalDetailAnggotaContent">
          <div class="text-center py-5">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
              <p class="mt-2 text-muted">Memuat data anggota...</p>
          </div>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="../assets/js/bootstrap.bundle.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="../assets/js/admin.js"></script>
<script src="../assets/js/book-transition.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>
</html>
