    </div> <!-- Cierre de main-content -->
    
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Scripts -->
    <script>
        // Activar tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            // Activar popovers
            var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl);
            });
            
            // Cerrar alertas automáticamente después de 5 segundos
            var alertList = [].slice.call(document.querySelectorAll('.alert'));
            alertList.forEach(function (alert) {
                setTimeout(function() {
                    var bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
            
            // Inicializar selects con búsqueda
            if (typeof $ !== 'undefined' && $.fn.select2) {
                $('.select2').select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    placeholder: 'Seleccione una opción',
                    allowClear: true
                });
            }
            
            // Inicializar datatables
            if ($.fn.DataTable) {
                $('.datatable').DataTable({
                    responsive: true,
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
                    },
                    dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                          "<'row'<'col-sm-12'tr>>" +
                          "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                    pageLength: 25
                });
            }
        });
        
        // Confirmación antes de eliminar
        function confirmDelete(event) {
            if (!confirm('¿Estás seguro de que deseas eliminar este registro? Esta acción no se puede deshacer.')) {
                event.preventDefault();
                return false;
            }
            return true;
        }
        
        // Formatear números como moneda
        function formatCurrency(amount) {
            return new Intl.NumberFormat('es-MX', {
                style: 'currency',
                currency: 'MXN',
                minimumFractionDigits: 2
            }).format(amount);
        }
    </script>
    
    <!-- Scripts adicionales -->
    <?php if (isset($scripts) && is_array($scripts)): ?>
        <?php foreach ($scripts as $script): ?>
            <script src="<?= $script ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Scripts en línea -->
    <?php if (isset($inline_script)): ?>
        <script>
            <?= $inline_script ?>
        </script>
    <?php endif; ?>
</body>
</html>
