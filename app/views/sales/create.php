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
    <div class="row g-4">
        <!-- Productos disponibles -->
        <div class="col-lg-6 col-xl">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-primary text-white">
                    <i class="bi bi-box-seam me-2"></i> Productos disponibles
                </div>
                <div class="card-body bg-light">
                    <div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-primary">
                                <tr>
                                    <th>Producto</th>
                                    <th>Código</th>
                                    <th>Precio</th>
                                    <th>Stock</th>
                                    <th>Cantidad</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="productsList">
                                <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td class="align-middle">
                                            <span
                                                class="fw-semibold text-dark"><?= htmlspecialchars($product['name']) ?></span>
                                        </td>
                                        <td class="align-middle">
                                            <span
                                                class="badge bg-secondary-subtle text-secondary-emphasis px-2 py-1 rounded-pill"><?= htmlspecialchars($product['code']) ?></span>
                                        </td>
                                        <td class="align-middle">$<?= number_format($product['price'], 2) ?></td>
                                        <td class="align-middle"><span
                                                class="text-success fw-bold"><?= $product['stock'] ?></span></td>
                                        <td class="align-middle">
                                            <form method="POST" action="/sales/addToCart"
                                                class="d-flex align-items-center gap-2 mb-0">
                                                <input type="hidden" name="add_product_id" value="<?= $product['id'] ?>">
                                                <input type="hidden" name="add_product_name"
                                                    value="<?= htmlspecialchars($product['name']) ?>">
                                                <input type="hidden" name="add_product_code"
                                                    value="<?= htmlspecialchars($product['code']) ?>">
                                                <input type="hidden" name="add_product_price"
                                                    value="<?= $product['price'] ?>">
                                                <input type="hidden" name="add_product_qty" value="1">
                                                <button type="submit" class="btn btn-sm btn-primary rounded-circle"
                                                    title="Agregar">
                                                    <i class="bi bi-plus-lg"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Carrito -->
        <div class="col-lg-6 col-xl">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-success text-white">
                    <i class="bi bi-cart-check me-2"></i> Carrito de venta
                </div>
                <div class="card-body bg-light">
                    <div class="table-responsive mb-4">
                        <table class="table table-hover align-middle mb-0" id="productsTable">
                            <thead class="table-success">
                                <tr>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio</th>
                                    <th>Subtotal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="saleItems">
                                <?php if (!empty($_SESSION['sale_cart'])): ?>
                                    <?php $subtotal = 0; ?>
                                    <?php foreach ($_SESSION['sale_cart'] as $pid => $item): ?>
                                        <?php $item_subtotal = $item['price'] * $item['qty'];
                                        $subtotal += $item_subtotal; ?>
                                        <tr>
                                            <td>
                                                <div class="fw-bold text-dark-emphasis">
                                                    <?= htmlspecialchars($item['name']) ?>
                                                </div>
                                                <small
                                                    class="text-muted fst-italic"><?= htmlspecialchars($item['code']) ?></small>
                                            </td>
                                            <td class="align-middle"><span
                                                    class="badge bg-primary-subtle text-primary-emphasis px-2 py-1 rounded-pill"><?= $item['qty'] ?></span>
                                            </td>
                                            <td class="align-middle">$<?= number_format($item['price'], 2) ?></td>
                                            <td class="align-middle">$<?= number_format($item_subtotal, 2) ?></td>
                                            <td class="align-middle">
                                                <form method="POST" action="/sales/removeFromCart" style="display:inline;">
                                                    <input type="hidden" name="remove_product_id" value="<?= $pid ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger rounded-circle"
                                                        title="Eliminar">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr id="noItemsRow">
                                        <td colspan="5" class="text-center text-muted py-4">
                                            No hay productos agregados
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                            <tfoot>
                                <?php $total = $subtotal ?? 0; ?>
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Subtotal:</td>
                                    <td class="text-end fw-bold" id="subtotal">
                                        $<?= number_format($subtotal ?? 0, 2) ?>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr class="table-active">
                                    <td colspan="3" class="text-end fw-bold">Total:</td>
                                    <td class="text-end fw-bold fs-5" id="total">
                                        $<?= number_format($total ?? 0, 2) ?>
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <form id="saleForm" method="POST" action="/sales/store" class="needs-validation" novalidate>
                    <div class="d-flex flex-column flex-md-row justify-content-end gap-2 mt-4">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle"></i> Guardar Venta
                        </button>
                    </div>
                    </form>
                    <!-- Campos ocultos para el formulario -->
                    <input type="hidden" id="subtotal_value" name="subtotal" value="0">

                    <input type="hidden" id="total_value" name="total" value="0">
                </div>
            </div>
        </div>
    </div>
</div>
</div>


<!-- Plantilla para ítems de venta -->
<template id="saleItemTemplate">
    <tr data-product-id="">
        <td>
            <div class="fw-bold product-name"></div>
            <small class="text-muted product-code"></small>
            <input type="hidden" name="items[][product_id]" class="product-id">
        </td>
        <td class="text-center">
            <input type="number" name="items[][quantity]" class="form-control form-control-sm text-center quantity"
                min="1" value="1" required>
        </td>
        <td class="text-end">
            <input type="number" name="items[][price]" class="form-control form-control-sm text-end price" min="0.01"
                step="0.01" required>
        </td>
        <td class="text-end item-subtotal">$0.00</td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-danger remove-item" title="Eliminar">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    </tr>
</template>

<?php include __DIR__ . '/../layouts/footer.php'; ?>

<!-- JavaScript para el formulario de venta -->
<?php
// --- Lógica PHP para carrito temporal ---
if (!isset($_SESSION)) {
    session_start();
}
if (!isset($_SESSION['sale_cart'])) {
    $_SESSION['sale_cart'] = [];
}
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['add_product_id'])
    && isset($_POST['add_product_qty'])
) {
    $pid = (int) $_POST['add_product_id'];
    $qty = (int) $_POST['add_product_qty'];
    $name = $_POST['add_product_name'] ?? '';
    $code = $_POST['add_product_code'] ?? '';
    $price = (float) ($_POST['add_product_price'] ?? 0);
    // Si ya está en el carrito, suma cantidad
    if (isset($_SESSION['sale_cart'][$pid])) {
        $_SESSION['sale_cart'][$pid]['qty'] += $qty;
    } else {
        $_SESSION['sale_cart'][$pid] = [
            'name' => $name,
            'code' => $code,
            'price' => $price,
            'qty' => $qty
        ];
    }
}
// Eliminar producto del carrito
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['remove_product_id'])
) {
    $pid = (int) $_POST['remove_product_id'];
    unset($_SESSION['sale_cart'][$pid]);
}
?>
</script>

<style>
    .quantity {
        width: 70px;
    }

    .price {
        width: 100px;
    }

    .item-subtotal {
        min-width: 100px;
    }

    .select2-container--bootstrap-5 {
        width: 100% !important;
    }

    .table th {
        white-space: nowrap;
    }
</style>