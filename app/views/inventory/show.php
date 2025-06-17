<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="h4 mb-0">Detalles del Movimiento #<?= $movement['id'] ?></h2>
                    <div>
                        <a href="/inventory" class="btn btn-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="card-subtitle mb-1 text-muted">Producto</h5>
                            <p class="card-text">
                                <?= htmlspecialchars($movement['product_name']) ?>
                                <small class="d-block text-muted">Código: <?= htmlspecialchars($movement['product_code']) ?></small>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="card-subtitle mb-1 text-muted">Tipo de Movimiento</h5>
                            <?php 
                            $badge_class = [
                                'entry' => 'bg-success',
                                'exit' => 'bg-danger',
                                'adjustment' => 'bg-warning'
                            ][$movement['type']] ?? 'bg-secondary';
                            ?>
                            <p class="card-text">
                                <span class="badge <?= $badge_class ?>">
                                    <?= $movement_types[$movement['type']] ?? ucfirst($movement['type']) ?>
                                </span>
                            </p>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <h5 class="card-subtitle mb-1 text-muted">Cantidad</h5>
                            <p class="card-text fs-4 fw-bold <?= $movement['quantity'] > 0 ? 'text-success' : 'text-danger' ?>">
                                <?= $movement['quantity'] > 0 ? '+' . $movement['quantity'] : $movement['quantity'] ?>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <h5 class="card-subtitle mb-1 text-muted">Fecha</h5>
                            <p class="card-text">
                                <?= date('d/m/Y H:i', strtotime($movement['created_at'])) ?>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <h5 class="card-subtitle mb-1 text-muted">Registrado por</h5>
                            <p class="card-text">
                                <?= htmlspecialchars($movement['user_name'] ?? 'Sistema') ?>
                            </p>
                        </div>
                    </div>
                    
                    <?php if (!empty($movement['reference_type']) || !empty($movement['reference_id'])): ?>
                        <div class="mb-4">
                            <h5 class="card-subtitle mb-1 text-muted">Referencia</h5>
                            <p class="card-text">
                                <?php if ($movement['reference_type'] === 'sale'): ?>
                                    Venta #<?= htmlspecialchars($movement['reference_id']) ?>
                                <?php elseif ($movement['reference_type'] === 'purchase'): ?>
                                    Compra #<?= htmlspecialchars($movement['reference_id']) ?>
                                <?php else: ?>
                                    <?= !empty($movement['reference_id']) ? htmlspecialchars($movement['reference_id']) : 'N/A' ?>
                                    <?php if (!empty($movement['notes'])): ?>
                                        - <?= htmlspecialchars($movement['notes']) ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($movement['notes'])): ?>
                        <div class="mb-4">
                            <h5 class="card-subtitle mb-1 text-muted">Notas</h5>
                            <p class="card-text"><?= nl2br(htmlspecialchars($movement['notes'])) ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <div class="mt-4 pt-3 border-top">
                        <a href="/inventory" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Volver al listado
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
