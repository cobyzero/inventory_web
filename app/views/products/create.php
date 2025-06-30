<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'): ?>
    <div class="alert alert-danger mt-4">Acceso denegado. No tienes permisos para crear productos.</div>
    <?php include __DIR__ . '/../layouts/footer.php'; ?>
    <?php exit; ?>
<?php endif; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Nuevo Producto</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?= base_url('products') ?>" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="/products">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="code" class="form-label">Código <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= isset($errors['code']) ? 'is-invalid' : '' ?>" 
                           id="code" name="code" value="<?= htmlspecialchars($product['code'] ?? '') ?>">
                    <?php if (isset($errors['code'])): ?>
                        <div class="invalid-feedback"><?= $errors['code'] ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label for="category_id" class="form-label">Categoría <span class="text-danger">*</span></label>
                    <select class="form-select <?= isset($errors['category_id']) ? 'is-invalid' : '' ?>" 
                            id="category_id" name="category_id">
                        <option value="">Seleccione una categoría</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>" 
                                <?= (isset($product['category_id']) && $product['category_id'] == $category['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['category_id'])): ?>
                        <div class="invalid-feedback"><?= $errors['category_id'] ?></div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="name" class="form-label">Nombre <span class="text-danger">*</span></label>
                <input type="text" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                       id="name" name="name" value="<?= htmlspecialchars($product['name'] ?? '') ?>">
                <?php if (isset($errors['name'])): ?>
                    <div class="invalid-feedback"><?= $errors['name'] ?></div>
                <?php endif; ?>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Descripción</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?= 
                    htmlspecialchars($product['description'] ?? '') 
                ?></textarea>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="price" class="form-label">Precio <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" min="0" 
                               class="form-control <?= isset($errors['price']) ? 'is-invalid' : '' ?>" 
                               id="price" name="price" value="<?= htmlspecialchars($product['price'] ?? '0.00') ?>">
                        <?php if (isset($errors['price'])): ?>
                            <div class="invalid-feedback"><?= $errors['price'] ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="cost" class="form-label">Costo</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" min="0" 
                               class="form-control <?= isset($errors['cost']) ? 'is-invalid' : '' ?>" 
                               id="cost" name="cost" value="<?= htmlspecialchars($product['cost'] ?? '0.00') ?>">
                        <?php if (isset($errors['cost'])): ?>
                            <div class="invalid-feedback"><?= $errors['cost'] ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-2">
                    <label for="stock" class="form-label">Stock</label>
                    <input type="number" min="0" 
                           class="form-control <?= isset($errors['stock']) ? 'is-invalid' : '' ?>" 
                           id="stock" name="stock" value="<?= htmlspecialchars($product['stock'] ?? '0') ?>">
                    <?php if (isset($errors['stock'])): ?>
                        <div class="invalid-feedback"><?= $errors['stock'] ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-2">
                    <label for="min_stock" class="form-label">Mínimo</label>
                    <input type="number" min="0" 
                           class="form-control <?= isset($errors['min_stock']) ? 'is-invalid' : '' ?>" 
                           id="min_stock" name="min_stock" value="<?= htmlspecialchars($product['min_stock'] ?? '5') ?>">
                    <?php if (isset($errors['min_stock'])): ?>
                        <div class="invalid-feedback"><?= $errors['min_stock'] ?></div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Guardar
                </button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
