<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="h4 mb-0">Nuevo Movimiento de Inventario</h2>
                </div>
                <div class="card-body">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <?php if (is_array($error)): ?>
                                        <?php foreach ($error as $err): ?>
                                            <li><?= $err ?></li>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <li><?= $error ?></li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="/inventory" method="POST">
                        <div class="mb-3">
                            <label for="movement_type" class="form-label">Tipo de Movimiento <span class="text-danger">*</span></label>
                            <select class="form-select <?= isset($errors['movement_type']) ? 'is-invalid' : '' ?>" 
                                    id="movement_type" 
                                    name="movement_type" 
                                    required>
                                <?php foreach ($types as $value => $label): ?>
                                    <option value="<?= $value ?>" <?= (isset($_POST['movement_type']) && $_POST['movement_type'] === $value) ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['type'])): ?>
                                <div class="invalid-feedback">
                                    <?= $errors['type'] ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="product_id" class="form-label">Producto <span class="text-danger">*</span></label>
                            <select class="form-select <?= isset($errors['product_id']) ? 'is-invalid' : '' ?>" 
                                    id="product_id" 
                                    name="product_id" 
                                    required>
                                <option value="">Seleccione un producto</option>
                                <?php foreach ($products as $product): ?>
                                    <option value="<?= $product['id'] ?>" 
                                            data-stock="<?= $product['stock'] ?>"
                                            <?= $movement['product_id'] == $product['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($product['name']) ?> (<?= htmlspecialchars($product['code']) ?>) - Stock: <?= $product['stock'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div id="stockHelp" class="form-text">
                                Stock actual: <span id="currentStock">0</span>
                            </div>
                            <?php if (isset($errors['product_id'])): ?>
                                <div class="invalid-feedback">
                                    <?= $errors['product_id'] ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="quantity" class="form-label">Cantidad <span class="text-danger">*</span></label>
                            <input type="number" 
                                   class="form-control <?= isset($errors['quantity']) ? 'is-invalid' : '' ?>" 
                                   id="quantity" 
                                   name="quantity" 
                                   value="<?= htmlspecialchars($movement['quantity'] ?? '1') ?>" 
                                   min="0.01" 
                                   step="0.01" 
                                   required>
                            <?php if (isset($errors['quantity'])): ?>
                                <div class="invalid-feedback">
                                    <?= $errors['quantity'] ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="reference_type" class="form-label">Tipo de Referencia</label>
                                    <select class="form-select <?= isset($errors['reference_type']) ? 'is-invalid' : '' ?>" 
                                            id="reference_type" 
                                            name="reference_type">
                                        <option value="">Seleccione un tipo</option>
                                        <option value="sale" <?= ($movement['reference_type'] ?? '') === 'sale' ? 'selected' : '' ?>>Venta</option>
                                        <option value="purchase" <?= ($movement['reference_type'] ?? '') === 'purchase' ? 'selected' : '' ?>>Compra</option>
                                        <option value="other" <?= ($movement['reference_type'] ?? '') === 'other' ? 'selected' : '' ?>>Otro</option>
                                    </select>
                                    <?php if (isset($errors['reference_type'])): ?>
                                        <div class="invalid-feedback">
                                            <?= $errors['reference_type'] ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="reference_id" class="form-label">Número de Referencia</label>
                                    <input type="text" 
                                           class="form-control <?= isset($errors['reference_id']) ? 'is-invalid' : '' ?>" 
                                           id="reference_id" 
                                           name="reference_id" 
                                           value="<?= htmlspecialchars($movement['reference_id'] ?? '') ?>">
                                    <div class="form-text">Ej: Número de factura, orden de compra, etc.</div>
                                    <?php if (isset($errors['reference_id'])): ?>
                                        <div class="invalid-feedback">
                                            <?= $errors['reference_id'] ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>



                        <div class="mb-3">
                            <label for="notes" class="form-label">Notas</label>
                            <textarea class="form-control <?= isset($errors['notes']) ? 'is-invalid' : '' ?>" 
                                      id="notes" 
                                      name="notes" 
                                      rows="3"><?= htmlspecialchars($movement['notes'] ?? '') ?></textarea>
                            <?php if (isset($errors['notes'])): ?>
                                <div class="invalid-feedback">
                                    <?= $errors['notes'] ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="/inventory" class="btn btn-secondary me-md-2">
                                <i class="bi bi-arrow-left"></i> Cancelar
                            </a>
                            <button type="submit" name="save_and_new" class="btn btn-outline-primary me-md-2">
                                <i class="bi bi-save"></i> Guardar y Nuevo
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Guardar Movimiento
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Actualizar stock al cambiar el producto
    const productSelect = document.getElementById('product_id');
    const currentStockSpan = document.getElementById('currentStock');
    
    function updateStock() {
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        if (selectedOption && selectedOption.dataset.stock !== undefined) {
            currentStockSpan.textContent = selectedOption.dataset.stock;
        } else {
            currentStockSpan.textContent = '0';
        }
    }
    
    productSelect.addEventListener('change', updateStock);
    
    // Actualizar stock al cargar la página
    updateStock();
    
    // Validar cantidad mínima
    const quantityInput = document.getElementById('quantity');
    const form = document.querySelector('form');
    
    form.addEventListener('submit', function(e) {
        if (parseFloat(quantityInput.value) <= 0) {
            e.preventDefault();
            alert('La cantidad debe ser mayor a cero');
            quantityInput.focus();
        }
    });
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
