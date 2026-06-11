            <footer class="mt-5 pt-3 border-top text-center text-muted">
                <small>&copy; 2026 Sistem Informasi Perpustakaan. All rights reserved.</small>
            </footer>
        </main> <!-- End of Main Content Area (main) -->
</div> <!-- End of Container-Fluid -->

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="../assets/js/bootstrap.bundle.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap-5',
            placeholder: "Ketik atau pilih genre...",
            allowClear: true
        });
    });
</script>
</body>
</html>
