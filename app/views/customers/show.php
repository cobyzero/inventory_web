<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?= $title ?></h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/customers" class="btn btn-secondary me-2">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
        <a href="/customers/<?= $customer['id'] ?>/edit" class="btn btn-primary me-2">
            <i class="fas fa-edit"></i> Editar
        </a>
        <form action="/customers/<?= $customer['id'] ?>" method="POST" class="d-inline" 
              onsubmit="return confirm('¿Está seguro de que desea eliminar este cliente?')">
            <input type="hidden" name="_method" value="DELETE">
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash"></i> Eliminar
            </button>
        </form>
    </div>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $_SESSION['success'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-info-circle me-1"></i>
                Información del Cliente
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Nombre:</div>
                    <div class="col-md-8"><?= htmlspecialchars($customer['name']) ?></div>
                </div>
                
                <?php if (!empty($customer['email'])): ?>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Correo Electrónico:</div>
                        <div class="col-md-8">
                            <a href="mailto:<?= htmlspecialchars($customer['email']) ?>">
                                <?= htmlspecialchars($customer['email']) ?>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($customer['phone'])): ?>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Teléfono:</div>
                        <div class="col-md-8">
                            <a href="tel:<?= htmlspecialchars($customer['phone']) ?>">
                                <?= htmlspecialchars($customer['phone']) ?>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($customer['address'])): ?>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Dirección:</div>
                        <div class="col-md-8">
                            <?= nl2br(htmlspecialchars($customer['address'])) ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-md-4 fw-bold">Fecha de Registro:</div>
                    <div class="col-md-8">
                        <?= date('d/m/Y H:i', strtotime($customer['created_at'])) ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Estadísticas del cliente -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-chart-bar me-1"></i>
                Estadísticas
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4 mb-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Ventas Totales</h5>
                                <p class="card-text display-6">0</p>
                                <small>Desde el registro</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Monto Total</h5>
                                <p class="card-text display-6">$0.00</p>
                                <small>En compras</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title">Última Compra</h5>
                                <p class="card-text">Nunca</p>
                                <small>Sin compras registradas</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Historial de ventas recientes -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-history me-1"></i>
                    Ventas Recientes
                </div>
                <a href="#" class="btn btn-sm btn-outline-primary">Ver Todas</a>
            </div>
            <div class="card-body">
                <div class="text-center py-4">
                    <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No hay ventas recientes</p>
                </div>
            </div>
        </div>
        
        <!-- Notas del cliente -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-sticky-note me-1"></i>
                Notas
            </div>
            <div class="card-body">
                <form>
                    <div class="mb-3">
                        <textarea class="form-control" rows="5" placeholder="Agregar una nota sobre este cliente..."></textarea>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Guardar Nota</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
