        </div> <!-- /.content-wrapper -->

        <!-- Main Footer -->
        <footer class="main-footer">
            <div class="container">
                <div class="float-right d-none d-sm-inline">
                    v1.3
                </div>
                <strong>Copyright &copy; 2025 <a href="https://www.instagram.com/spikuningan">SPI 2K Kuningan</a>.</strong> All rights reserved.
            </div>
        </footer>
        </div>
        <!-- ./wrapper -->

        <!-- jQuery -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <!-- Bootstrap 4 -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.bundle.min.js"></script>
        <!-- AdminLTE App -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.1.0/js/adminlte.min.js"></script>

        <script>
            // Update current date untuk halaman kasir
            document.addEventListener('DOMContentLoaded', function() {
                const currentDateElement = document.getElementById('currentDate');
                if (currentDateElement) {
                    currentDateElement.textContent = new Date().toLocaleDateString('id-ID', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                }
            });
        </script>
        </body>

        </html>