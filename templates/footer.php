        </div>
        <!-- /.container-fluid -->

        </div>
        <!-- End of Main Content -->

        <!-- Footer -->
        <footer class="sticky-footer bg-white py-4">
            <div class="container my-auto">
                <div class="copyright text-center my-auto d-flex flex-column flex-md-row justify-content-between align-items-center">
                    <div class="mb-2 mb-md-0">
                        <span class="text-gray-600">Copyright &copy; Smart-Health Blood Donation 2026</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Developer husi: <b class="text-primary">Abilio & Lukas & Pedro</b></span>
                    </div>
                </div>
            </div>
        </footer>
        <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

        </div>
        <!-- End of Page Wrapper -->

        <!-- Scroll to Top Button-->
        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fas fa-angle-up"></i>
        </a>

        <!-- Logout Modal-->
        <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-gradient-primary text-white">
                        <h5 class="modal-title" id="exampleModalLabel">Prontu atu Sai?</h5>
                        <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">Hili "Logout" iha okos karik ita boot prontu ona atu taka sesaun ida ne'e.</div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Kansela</button>
                        <a class="btn btn-primary" href="<?php echo $base; ?>logout">Logout</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bootstrap core JavaScript-->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.0/js/bootstrap.bundle.min.js"></script>

        <!-- Core plugin JavaScript-->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>

        <!-- Custom scripts for all pages-->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.4/js/sb-admin-2.min.js"></script>

        <!-- DataTables JS -->
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>

        <!-- DataTables Buttons JS & Dependencies -->
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

        <script>
            // ===== DataTables Auto-Init =====
            $(document).ready(function() {
                if ($.fn.DataTable) {
                    $('table#dataTable, table.dt-table').DataTable({
                        responsive: true,
                        language: {
                            search: '<i class="fas fa-search"></i>',
                            searchPlaceholder: 'Buka...',
                            lengthMenu: 'Hatudu _MENU_ linha',
                            info: 'Hatudu _START_ to _END_ husi _TOTAL_ rekord',
                            infoEmpty: 'Laiha rekord disponivel',
                            infoFiltered: '(filtrado husi _MAX_ rekord total)',
                            paginate: {
                                first: 'Uluk',
                                last: 'Ikus',
                                next: '<i class="fas fa-chevron-right"></i>',
                                previous: '<i class="fas fa-chevron-left"></i>'
                            },
                            zeroRecords: 'Laiha rekord ne\'ebe tuir\'ur',
                            emptyTable: 'Laiha dadus disponivel iha tabela'
                        },
                        pageLength: 10,
                        lengthMenu: [
                            [10, 25, 50, -1],
                            [10, 25, 50, 'Hotu']
                        ],
                        columnDefs: [{
                            orderable: false,
                            targets: -1
                        }]
                    });
                }
            });

            // SweetAlert for Success Messages
            const urlParams = new URLSearchParams(window.location.search);
            const msg = urlParams.get('msg');

            if (msg) {
                Swal.fire({
                    icon: 'success',
                    title: 'Susesu!',
                    text: msg,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
                window.history.replaceState({}, document.title, window.location.pathname);
            }

            // Export Table to Excel - proper HTML table export
            function exportTableToExcel(contentID, filename) {
                var el = document.getElementById(contentID);
                if (!el) { alert('Dadus labele hetan!'); return; }

                // Extract only real tables from the content, strip images to avoid bloat
                var clone = el.cloneNode(true);

                // Remove all images (logo) so Excel doesn't break
                var imgs = clone.querySelectorAll('img');
                imgs.forEach(function(img) { img.parentNode.removeChild(img); });

                // Remove buttons, badges with icons that don't render well
                var icons = clone.querySelectorAll('i.fas, i.fab, i.far, .d-print-none');
                icons.forEach(function(ic) { ic.parentNode.removeChild(ic); });

                // Remove progress bars (keep only text)
                var bars = clone.querySelectorAll('.progress');
                bars.forEach(function(bar) { bar.parentNode.removeChild(bar); });

                var style = '<style>' +
                    'body { font-family: Arial, sans-serif; font-size: 12px; }' +
                    'table { border-collapse: collapse; width: 100%; margin-bottom: 10px; }' +
                    'th { background-color: #4f46e5; color: white; border: 1px solid #333; padding: 6px 8px; text-align: center; font-weight: bold; }' +
                    'td { border: 1px solid #999; padding: 5px 8px; }' +
                    'h4, h5 { text-align: center; margin: 4px 0; }' +
                    '.text-center { text-align: center; }' +
                    '.font-weight-bold { font-weight: bold; }' +
                    '.badge { padding: 2px 6px; border-radius: 3px; font-size: 11px; }' +
                    '.badge-primary { background-color: #4f46e5; color: white; }' +
                    '.badge-danger { background-color: #dc3545; color: white; }' +
                    '.text-success { color: #198754; }' +
                    '.text-primary { color: #4f46e5; }' +
                    '</style>';

                var html = '<html><head><meta charset="utf-8">' + style + '</head><body>' + clone.innerHTML + '</body></html>';
                filename = (filename || 'export') + '.xls';

                var blob = new Blob(['\ufeff', html], { type: 'application/vnd.ms-excel;charset=utf-8' });
                var url = URL.createObjectURL(blob);
                var a = document.createElement('a');
                a.href = url;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            }


            // Export Table to Word (Improved with Styles and Layout)
            function exportTableToWord(element, filename = '') {
                var preHtml = "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:office:word' xmlns='http://www.w3.org/TR/REC-html40'><head><meta charset='utf-8'><title>Export HTML To Doc</title><style>body { font-family: 'Times New Roman', serif; } table { width: 100%; border-collapse: collapse; margin-top: 20px; } th, td { border: 1px solid #333; padding: 8px; text-align: left; } .text-center { text-align: center; } .font-weight-bold { font-weight: bold; } h4, h5 { text-align: center; margin: 5px 0; text-transform: uppercase; } .text-primary { color: #4f46e5; } .mb-5 { margin-bottom: 25px; } img { width: 80px; display: block; margin: 0 auto 10px; }</style></head><body>";
                var postHtml = "</body></html>";
                var content = document.getElementById(element).innerHTML;
                
                // Clean up some web-only classes if needed
                var html = preHtml + content + postHtml;

                var blob = new Blob(['\ufeff', html], {
                    type: 'application/msword'
                });

                filename = filename ? filename + '.doc' : 'document.doc';

                if (navigator.msSaveOrOpenBlob) {
                    navigator.msSaveOrOpenBlob(blob, filename);
                } else {
                    var downloadLink = document.createElement("a");
                    document.body.appendChild(downloadLink);
                    var url = 'data:application/vnd.ms-word;charset=utf-8,' + encodeURIComponent(html);
                    downloadLink.href = url;
                    downloadLink.download = filename;
                    downloadLink.click();
                    document.body.removeChild(downloadLink);
                }
            }

            function confirmDelete(url, title = 'Ita boot fiar atu hamos?') {
                Swal.fire({
                    title: title,
                    text: "Dadus ne'ebé hamos ona labele foti fali!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#4f46e5',
                    cancelButtonColor: '#858796',
                    confirmButtonText: 'Sim, Hamos!',
                    cancelButtonText: 'Kansela',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            }

            // Mobile Sidebar Backdrop Overlay
            $(document).ready(function() {
                // Add backdrop element with z-index 9998 (sidebar is 9999)
                $('body').append('<div id="sidebar-backdrop" class="d-none" style="position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.5);z-index:9998;transition:all 0.3s ease;"></div>');
                
                // Unbind previous events and bind our own custom offcanvas toggle
                $('#sidebarToggleTop').off('click').on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $('body').toggleClass('sidebar-toggled');
                    $('.sidebar').toggleClass('toggled');
                    
                    if ($('.sidebar').hasClass('toggled')) {
                        $('#sidebar-backdrop').removeClass('d-none');
                    } else {
                        $('#sidebar-backdrop').addClass('d-none');
                    }
                });
                
                $('#sidebar-backdrop').on('click', function() {
                    $('body').removeClass('sidebar-toggled');
                    $('.sidebar').removeClass('toggled');
                    $(this).addClass('d-none');
                });
            });
        </script>

        </body>


        </html>