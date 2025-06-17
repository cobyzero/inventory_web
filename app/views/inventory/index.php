<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Movimientos de Inventario</h1>
        <div>
            <a href="/inventory/create" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Nuevo Movimiento
            </a>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['success'] ?>
            <?php unset($_SESSION['success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['error'] ?>
            <?php unset($_SESSION['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <?php if (empty($movements)): ?>
                <div class="text-center py-4">
                    <p class="text-muted">No hay movimientos registrados</p>
                    <a href="/inventory/create" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Registrar Primer Movimiento
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Producto</th>
                                <th class="text-center">Tipo</th>
                                <th class="text-end">Cantidad</th>
                                <th>Referencia</th>
                                <th>Usuario</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($movements as $movement): ?>
                                <tr>
                                    <td><?= date('d/m/Y H:i', strtotime($movement['created_at'])) ?></td>
                                    <td>
                                        <div><?= htmlspecialchars($movement['product_name']) ?></div>
                                        <small class="text-muted"><?= htmlspecialchars($movement['product_code']) ?></small>
                                    </td>
                                    <td class="text-center">
                                        <?php 
                                        $badge_class = [
                                            'entry' => 'bg-success',
                                            'exit' => 'bg-danger',
                                            'adjustment' => 'bg-warning'
                                        ][$movement['type']] ?? 'bg-secondary';
                                        ?>
                                        <span class="badge <?= $badge_class ?>">
                                            <?= $movement_types[$movement['type']] ?? ucfirst($movement['type']) ?>
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold <?= $movement['quantity'] > 0 ? 'text-success' : 'text-danger' ?>">
                                        <?= $movement['quantity'] > 0 ? '+' . $movement['quantity'] : $movement['quantity'] ?>
                                    </td>
                                    <td><?= !empty($movement['reference']) ? htmlspecialchars($movement['reference']) : '<span class="text-muted">N/A</span>' ?></td>
                                    <td><?= htmlspecialchars($movement['user_name'] ?? 'Sistema') ?></td>
                                    <td class="text-center">
                                        <a href="/inventory/<?= $movement['id'] ?>" class="btn btn-sm btn-info" title="Ver">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <?php if ($total_pages > 1): ?>
                    <nav aria-label="Navegación de páginas" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php if ($current_page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $current_page - 1 ?>">Anterior</a>
                                </li>
                            <?php else: ?>
                                <li class="page-item disabled">
                                    <span class="page-link">Anterior</span>
                                </li>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($current_page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $current_page + 1 ?>">Siguiente</a>
                                </li>
                            <?php else: ?>
                                <li class="page-item disabled">
                                    <span class="page-link">Siguiente</span>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>

            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
