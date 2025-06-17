<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Detalles del Producto</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/products" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-8">
                <h3><?= htmlspecialchars($product['name']) ?></h3>
                <p class="text-muted"><?= htmlspecialchars($product['code']) ?></p>
                
                <?php if (!empty($product['description'])): ?>
                    <div class="mb-3">
                        <h5>Descripción</h5>
                        <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                    </div>
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <h5>Información del Producto</h5>
                        <table class="table table-sm">
                            <tr>
                                <th>Categoría:</th>
                                <td><?= htmlspecialchars($product['category_name'] ?? 'Sin categoría') ?></td>
                            </tr>
                            <tr>
                                <th>Precio:</th>
                                <td>$<?= number_format($product['price'], 2) ?></td>
                            </tr>
                            <tr>
                                <th>Costo:</th>
                                <td>$<?= number_format($product['cost'], 2) ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5>Inventario</h5>
                        <table class="table table-sm">
                            <tr>
                                <th>Stock actual:</th>
                                <td>
                                    <span class="badge bg-<?= $product['stock'] <= $product['min_stock'] ? 'danger' : 'success' ?>">
                                        <?= $product['stock'] ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Stock mínimo:</th>
                                <td><?= $product['min_stock'] ?></td>
                            </tr>
                            <tr>
                                <th>Estado:</th>
                                <td>
                                    <?php if ($product['stock'] == 0): ?>
                                        <span class="badge bg-danger">Agotado</span>
                                    <?php elseif ($product['stock'] <= $product['min_stock']): ?>
                                        <span class="badge bg-warning">Bajo inventario</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Disponible</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="border rounded p-3 mb-3">
                    <h5>Acciones</h5>
                    <div class="d-grid gap-2">
                        <a href="/products/<?= $product['id'] ?>/edit" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Editar Producto
                        </a>
                        <form action="/products/<?= $product['id'] ?>" method="POST" class="d-grid" 
                              onsubmit="return confirm('¿Estás seguro de eliminar este producto?')">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash"></i> Eliminar Producto
                            </button>
                        </form>
                    </div>
                </div>
                
                <div class="border rounded p-3">
                    <h5>Información Adicional</h5>
                    <table class="table table-sm">
                        <tr>
                            <th>Creado:</th>
                            <td><?= date('d/m/Y H:i', strtotime($product['created_at'])) ?></td>
                        </tr>
                        <tr>
                            <th>Actualizado:</th>
                            <td><?= date('d/m/Y H:i', strtotime($product['updated_at'])) ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
