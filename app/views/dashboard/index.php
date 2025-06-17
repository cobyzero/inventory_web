<?php include __DIR__ . '/../layouts/header.php'; ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Panel de Control</h1> 
</div>

<div class="container-fluid">
    <div class="row">
        <!-- Tarjeta de Productos -->
        <div class="col-md-4 mb-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Productos</h5>
                            <h2 class="mb-0"><?= number_format($productCount) ?></h2>
                            <p class="mb-0">Productos registrados</p>
                        </div>
                        <i class="bi bi-box-seam display-4 opacity-50"></i>
                    </div>
                </div>
                <div class="card-footer bg-primary bg-opacity-25 d-flex align-items-center justify-content-between">
                    <a href="/products" class="text-white text-decoration-none small">Ver productos</a>
                    <i class="bi bi-arrow-right-circle"></i>
                </div>
            </div>
        </div>
        
        <!-- Tarjeta de Ventas -->
        <div class="col-md-4 mb-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Ventas</h5>
                            <h2 class="mb-0"><?= number_format($salesThisMonth) ?></h2>
                            <p class="mb-0">Ventas este mes</p>
                        </div>
                        <i class="bi bi-cart-check display-4 opacity-50"></i>
                    </div>
                </div>
                <div class="card-footer bg-success bg-opacity-25 d-flex align-items-center justify-content-between">
                    <a href="/sales" class="text-white text-decoration-none small">Ver ventas</a>
                    <i class="bi bi-arrow-right-circle"></i>
                </div>
            </div>
        </div>
        
        <!-- Tarjeta de Inventario -->
        <div class="col-md-4 mb-4">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Inventario</h5>
                            <h2 class="mb-0"><?= number_format($inStockCount) ?></h2>
                            <p class="mb-0">Productos en stock</p>
                        </div>
                        <i class="bi bi-box-seam display-4 opacity-50"></i>
                    </div>
                </div>
                <div class="card-footer bg-warning bg-opacity-25 d-flex align-items-center justify-content-between">
                    <a href="/inventory" class="text-white text-decoration-none small">Ver inventario</a>
                    <i class="bi bi-arrow-right-circle"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección de Productos con Bajo Stock -->
    <?php if (!empty($lowStockProducts)): ?>
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header bg-danger bg-opacity-10 text-danger">
                    <h5 class="mb-0"><i class="bi bi-exclamation-triangle-fill me-2"></i>Productos con bajo stock</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Código</th>
                                    <th>Producto</th>
                                    <th class="text-center">Stock Actual</th>
                                    <th class="text-center">Stock Mínimo</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lowStockProducts as $product): ?>
                                <tr>
                                    <td><?= htmlspecialchars($product['code']) ?></td>
                                    <td><?= htmlspecialchars($product['name']) ?></td>
                                    <td class="text-center text-danger fw-bold"><?= number_format($product['current_stock']) ?></td>
                                    <td class="text-center"><?= number_format($product['min_stock']) ?></td>
                                    <td class="text-center">
                                        <a href="/products/<?= $product['id'] ?>/edit" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i> Reabastecer
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Sección de Resumen -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Resumen Reciente</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Bienvenido, <?= htmlspecialchars($user['name']) ?></h5>
                            <p class="text-muted">Bienvenido al sistema de gestión de inventario. Selecciona una opción del menú lateral para comenzar.</p>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-info">
                                <h6><i class="bi bi-info-circle-fill me-2"></i>Información del sistema</h6>
                                <p class="mb-0 small">
                                    <i class="bi bi-calendar-check me-1"></i> 
                                    <?= date('d/m/Y H:i:s') ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
                </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
