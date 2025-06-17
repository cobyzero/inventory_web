<div class="d-flex flex-column flex-shrink-0 p-3 text-white">
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="/dashboard" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], 'dashboard') ? 'active' : '' ?>">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="/products" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], 'products') ? 'active' : '' ?>">
                <i class="bi bi-box-seam"></i> Productos
            </a>
        </li>
        <li>
            <a href="/categories" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], 'categories') ? 'active' : '' ?>">
                <i class="bi bi-tags"></i> Categorías
            </a>
        </li>
        <li>
            <a href="/sales" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], 'sales') ? 'active' : '' ?>">
                <i class="bi bi-cart-check"></i> Ventas
            </a>
        </li>
        <li>
            <a href="/inventory" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], 'inventory') ? 'active' : '' ?>">
                <i class="bi bi-boxes"></i> Inventario
            </a>
        </li>
        <li>
            <a href="/customers" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], 'customers') ? 'active' : '' ?>">
                <i class="bi bi-people"></i> Clientes
            </a>
        </li>
        <li>
            <a href="/reports/index" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], 'reports') ? 'active' : '' ?>">
                <i class="bi bi-graph-up"></i> Reportes
            </a>
        </li>
    </ul>
    
    <hr>
    <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown">
            <i class="bi bi-person-circle me-2"></i>
            <strong><?= htmlspecialchars($_SESSION['user_name'] ?? 'Usuario') ?></strong>
        </a>
        <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
            <li><a class="dropdown-item" href="/profile">Perfil</a></li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <form action="/auth/logout" method="POST" class="d-inline">
                    <button type="submit" class="dropdown-item">Cerrar sesión</button>
                </form>
            </li>
        </ul>
    </div>
</div>

<!-- Script para el menú desplegable en móviles -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const sidebarToggle = document.getElementById('sidebarToggle');
        
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('show');
            });
        }
        
        // Cerrar el menú al hacer clic en un enlace en móviles
        const navLinks = document.querySelectorAll('.sidebar .nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth < 992) {
                    sidebar.classList.remove('show');
                }
            });
        });
        
        // Cerrar el menú al hacer clic fuera de él
        document.addEventListener('click', function(event) {
            const isClickInsideSidebar = sidebar.contains(event.target);
            const isClickOnToggle = sidebarToggle.contains(event.target);
            
            if (!isClickInsideSidebar && !isClickOnToggle && window.innerWidth < 992) {
                sidebar.classList.remove('show');
            }
        });
    });
</script>
