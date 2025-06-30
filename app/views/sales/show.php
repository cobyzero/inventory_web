<?php include __DIR__ . '/../layouts/header.php'; ?>
<!-- Main Content -->
    <div
        class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><?= $title ?></h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="/sales" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
            <?php if ($sale['status'] !== 'cancelled' && $_SESSION['role'] === 'admin'): ?>
                <a href="/sales/<?= $sale['id'] ?>/edit" class="btn btn-primary me-2">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cancelSaleModal">
                    <i class="fas fa-times"></i> Anular Venta
                </button>
            <?php endif; ?>
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
                    <i class="fas fa-boxes me-1"></i>
                    Productos
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-end">Precio Unit.</th>
                                    <th class="text-center">Cantidad</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sale['items'] as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold"><?= htmlspecialchars($item['product_name']) ?></div>
                                            <small class="text-muted"><?= htmlspecialchars($item['product_code']) ?></small>
                                        </td>
                                        <td class="text-end">$<?= number_format($item['price'], 2) ?></td>
                                        <td class="text-center"><?= $item['quantity'] ?></td>
                                        <td class="text-end">$<?= number_format($item['subtotal'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Subtotal:</td>
                                    <td class="text-end fw-bold">$<?= number_format($sale['subtotal'], 2) ?></td>
                                </tr>

                                <tr class="table-active">
                                    <td colspan="3" class="text-end fw-bold">Total:</td>
                                    <td class="text-end fw-bold fs-5">$<?= number_format($sale['total'], 2) ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Movimientos de inventario relacionados -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-exchange-alt me-1"></i>
                    Movimientos de Inventario
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Producto</th>
                                    <th class="text-center">Tipo</th>
                                    <th class="text-end">Cantidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sale['items'] as $item): ?>
                                    <?php
                                    // Simulación de movimientos de inventario
                                    // En una implementación real, esto vendría de la base de datos
                                    $movement = [
                                        'created_at' => $sale['created_at'],
                                        'product_name' => $item['product_name'],
                                        'type' => 'sale',
                                        'quantity' => -$item['quantity'] // Negativo porque es una salida
                                    ];
                                    ?>
                                    <tr>
                                        <td><?= date('d/m/Y H:i', strtotime($movement['created_at'])) ?></td>
                                        <td><?= htmlspecialchars($movement['product_name']) ?></td>
                                        <td class="text-center">
                                            <span class="badge bg-danger">
                                                <?= $movement['type'] === 'sale' ? 'Venta' : ucfirst($movement['type']) ?>
                                            </span>
                                        </td>
                                        <td
                                            class="text-end fw-bold <?= $movement['quantity'] > 0 ? 'text-success' : 'text-danger' ?>">
                                            <?= $movement['quantity'] > 0 ? '+' : '' ?>    <?= $movement['quantity'] ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Información de la Venta -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-info-circle me-1"></i>
                    Información de la Venta
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="card-subtitle mb-1 text-muted">Número de Factura</h6>
                        <p class="card-text"><?= htmlspecialchars($sale['invoice_number_formatted']) ?></p>
                    </div>

                    <div class="mb-3">
                        <h6 class="card-subtitle mb-1 text-muted">Fecha y Hora</h6>
                        <p class="card-text"><?= date('d/m/Y H:i', strtotime($sale['created_at'])) ?></p>
                    </div>

                    <div class="mb-3">
                        <h6 class="card-subtitle mb-1 text-muted">Estado</h6>
                        <p class="card-text">
                            <?php
                            $status_class = [
                                'completed' => 'success',
                                'pending' => 'warning',
                                'cancelled' => 'danger'
                            ][$sale['status']] ?? 'secondary';
                            ?>
                            <span class="badge bg-<?= $status_class ?>">
                                <?= $statuses[$sale['status']] ?? ucfirst($sale['status']) ?>
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Información del Cliente -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-user me-1"></i>
                        Cliente
                    </div>
                </div>
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($sale['customer_name']) ?></h5>

                    <?php if (!empty($sale['customer_email'])): ?>
                        <div class="mb-2">
                            <i class="fas fa-envelope me-2 text-muted"></i>
                            <a href="mailto:<?= htmlspecialchars($sale['customer_email']) ?>" class="text-decoration-none">
                                <?= htmlspecialchars($sale['customer_email']) ?>
                            </a>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($sale['customer_phone'])): ?>
                        <div class="mb-2">
                            <i class="fas fa-phone me-2 text-muted"></i>
                            <a href="tel:<?= htmlspecialchars($sale['customer_phone']) ?>" class="text-decoration-none">
                                <?= htmlspecialchars($sale['customer_phone']) ?>
                            </a>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($sale['customer_address'])): ?>
                        <div class="mb-2">
                            <i class="fas fa-map-marker-alt me-2 text-muted"></i>
                            <span class="text-muted"><?= nl2br(htmlspecialchars($sale['customer_address'])) ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

<!-- Modal para anular venta -->
<div class="modal fade" id="cancelSaleModal" tabindex="-1" aria-labelledby="cancelSaleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="cancelSaleModalLabel">Confirmar Anulación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form action="/sales/<?= $sale['id'] ?>/cancel" method="post">
                <div class="modal-body">
                    <p>¿Está seguro de que desea anular esta venta? Esta acción no se puede deshacer.</p>
                    <div class="mb-3">
                        <label for="cancellation_reason" class="form-label">Motivo de la anulación</label>
                        <textarea class="form-control" id="cancellation_reason" name="reason" rows="3"
                            required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Confirmar Anulación</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>

<style>
    .avatar {
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        margin-bottom: 1.5rem;
        border: none;
    }

    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        font-weight: 500;
    }

    .table th {
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-top: none;
        border-bottom: 1px solid #e9ecef;
    }

    .table td {
        vertical-align: middle;
    }

    .badge {
        font-weight: 500;
        padding: 0.4em 0.8em;
    }

    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
</style>