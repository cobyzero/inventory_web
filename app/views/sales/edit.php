<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?= $title ?></h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/sales" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

<?php if (!empty($errors['general'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= $errors['general'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="container-fluid px-0">
    <form method="POST" action="/sales/<?= htmlspecialchars($sale['id']) ?>/update" class="needs-validation" novalidate>
        <div class="row g-4">
            <!-- Cliente -->
            <div class="col-md-4">
                <label for="customer_id" class="form-label">Cliente</label>
                <select name="customer_id" id="customer_id" class="form-select" required>
                    <option value="">Seleccione un cliente</option>
                    <?php foreach ($customers as $customer): ?>
                        <option value="<?= $customer['id'] ?>" <?= $sale['customer_id'] == $customer['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($customer['full_name'] ?? $customer['username']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (!empty($errors['customer_id'])): ?>
                    <div class="invalid-feedback d-block">
                        <?= $errors['customer_id'] ?>
                    </div>
                <?php endif; ?>
            </div>
            <!-- Estado -->
            <div class="col-md-4">
                <label for="status" class="form-label">Estado</label>
                <select name="status" id="status" class="form-select">
                    <?php foreach ($statuses as $key => $label): ?>
                        <option value="<?= $key ?>" <?= $sale['status'] == $key ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <hr>
        <!-- Productos -->
        <div class="row g-4">
            <div class="col-12">
                <h5>Productos en la venta</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-primary">
                            <tr>
                                <th>Producto</th>
                                <th>Código</th>
                                <th>Precio</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="saleItems">
                            <?php foreach ($sale['items'] as $index => $item): ?>
                                <tr>
                                    <td class="align-middle">
                                        <select name="items[<?= $index ?>][product_id]" class="form-select" required>
                                            <option value="">Seleccione producto</option>
                                            <?php foreach ($products as $product): ?>
                                                <option value="<?= $product['id'] ?>" <?= $item['product_id'] == $product['id'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($product['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td class="align-middle">
                                        <?= htmlspecialchars($item['product_code'] ?? '') ?>
                                    </td>
                                    <td class="align-middle">
                                        <input type="number" name="items[<?= $index ?>][price]" class="form-control" min="0.01" step="0.01" value="<?= htmlspecialchars($item['price']) ?>" required>
                                    </td>
                                    <td class="align-middle">
                                        <input type="number" name="items[<?= $index ?>][quantity]" class="form-control" min="1" value="<?= htmlspecialchars($item['quantity']) ?>" required>
                                    </td>
                                    <td class="align-middle">
                                        $<?= number_format($item['price'] * $item['quantity'], 2) ?>
                                    </td>
                                    <td class="align-middle">
                                        <!-- Aquí se podría agregar botón para eliminar producto de la venta -->
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-4 offset-md-8">
                <table class="table">
                    <tr>
                        <th>Subtotal:</th>
                        <td>$<?= number_format($sale['subtotal'], 2) ?></td>
                    </tr>
                    <tr>
                        <th>Total:</th>
                        <td>$<?= number_format($sale['total'], 2) ?></td>
                    </tr>
                </table>
                <input type="hidden" name="subtotal" value="<?= $sale['subtotal'] ?>">
                <input type="hidden" name="total" value="<?= $sale['total'] ?>">
            </div>
        </div>
        <div class="d-flex flex-row-reverse gap-2 mt-4">
            <button type="submit" class="btn btn-success">
                <i class="bi bi-check-circle"></i> Guardar Cambios
            </button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
