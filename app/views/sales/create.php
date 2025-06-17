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

            <form id="saleForm" method="POST" action="/sales/store" class="needs-validation" novalidate>
                <div class="row">
                    <!-- Datos del Cliente -->
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-user me-1"></i>
                                Datos del Cliente
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="customer_id" class="form-label">Cliente <span class="text-danger">*</span></label>
                                    <select class="form-select select2 <?= !empty($errors['customer_id']) ? 'is-invalid' : '' ?>" 
                                            id="customer_id" name="customer_id" required>
                                        <option value="">Seleccione un cliente</option>
                                        <?php foreach ($customers as $customer): ?>
                                            <option value="<?= $customer['id'] ?>" <?= ($sale['customer_id'] ?? '') == $customer['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($customer['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">
                                        <?= $errors['customer_id'] ?? 'Seleccione un cliente' ?>
                                    </div>
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#newCustomerModal">
                                            <i class="fas fa-plus"></i> Nuevo Cliente
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="payment_method" class="form-label">Método de Pago <span class="text-danger">*</span></label>
                                    <select class="form-select <?= !empty($errors['payment_method']) ? 'is-invalid' : '' ?>" 
                                            id="payment_method" name="payment_method" required>
                                        <?php foreach ($payment_methods as $value => $label): ?>
                                            <option value="<?= $value ?>" <?= ($sale['payment_method'] ?? 'cash') === $value ? 'selected' : '' ?>>
                                                <?= $label ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">
                                        <?= $errors['payment_method'] ?? 'Seleccione un método de pago' ?>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="status" class="form-label">Estado <span class="text-danger">*</span></label>
                                    <select class="form-select <?= !empty($errors['status']) ? 'is-invalid' : '' ?>" 
                                            id="status" name="status" required>
                                        <?php foreach ($statuses as $value => $label): ?>
                                            <option value="<?= $value ?>" <?= ($sale['status'] ?? 'completed') === $value ? 'selected' : '' ?>>
                                                <?= $label ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">
                                        <?= $errors['status'] ?? 'Seleccione un estado' ?>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notas</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3"><?= htmlspecialchars($sale['notes'] ?? '') ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Productos -->
                    <div class="col-md-8">
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-boxes me-1"></i>
                                    Productos
                                </div>
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                                    <i class="fas fa-plus"></i> Agregar Producto
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover" id="productsTable">
                                        <thead>
                                            <tr>
                                                <th>Producto</th>
                                                <th class="text-center" style="width: 100px;">Cantidad</th>
                                                <th class="text-end" style="width: 120px;">Precio Unit.</th>
                                                <th class="text-end" style="width: 140px;">Subtotal</th>
                                                <th style="width: 40px;"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="saleItems">
                                            <!-- Los ítems se agregarán aquí dinámicamente -->
                                            <tr id="noItemsRow">
                                                <td colspan="5" class="text-center text-muted py-4">
                                                    No hay productos agregados
                                                </td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="3" class="text-end fw-bold">Subtotal:</td>
                                                <td class="text-end fw-bold" id="subtotal">$0.00</td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" class="text-end">Impuesto (%):</td>
                                                <td class="text-end">
                                                    <input type="number" class="form-control form-control-sm text-end" 
                                                           id="tax_percent" name="tax_percent" value="0" min="0" max="100" step="0.01" 
                                                           style="width: 80px; display: inline-block;">
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" class="text-end">Descuento:</td>
                                                <td class="text-end">
                                                    <input type="number" class="form-control form-control-sm text-end" 
                                                           id="discount" name="discount" value="0" min="0" step="0.01" 
                                                           style="width: 100px; display: inline-block;">
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr class="table-active">
                                                <td colspan="3" class="text-end fw-bold">Total:</td>
                                                <td class="text-end fw-bold fs-5" id="total">$0.00</td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                    <button type="button" class="btn btn-secondary me-md-2" onclick="clearForm()">
                                        <i class="fas fa-times"></i> Cancelar
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Guardar Venta
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Campos ocultos para el formulario -->
                <input type="hidden" id="subtotal_value" name="subtotal" value="0">
                <input type="hidden" id="tax_value" name="tax" value="0">
                <input type="hidden" id="total_value" name="total" value="0">
            </form>
 

<!-- Modal para agregar producto -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProductModalLabel">Agregar Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="productSearch" class="form-label">Buscar Producto</label>
                    <input type="text" class="form-control" id="productSearch" placeholder="Nombre o código del producto">
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Código</th>
                                <th>Precio</th>
                                <th>Stock</th>
                                <th style="width: 150px;">Cantidad</th>
                                <th style="width: 100px;"></th>
                            </tr>
                        </thead>
                        <tbody id="productsList">
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><?= htmlspecialchars($product['name']) ?></td>
                                    <td><?= htmlspecialchars($product['code']) ?></td>
                                    <td>$<?= number_format($product['price'], 2) ?></td>
                                    <td><?= $product['stock'] ?></td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm quantity-input" 
                                               min="1" max="<?= $product['stock'] ?>" value="1" 
                                               data-product-id="<?= $product['id'] ?>">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary add-product-btn" 
                                                data-product-id="<?= $product['id'] ?>"
                                                data-product-name="<?= htmlspecialchars($product['name']) ?>"
                                                data-product-code="<?= htmlspecialchars($product['code']) ?>"
                                                data-product-price="<?= $product['price'] ?>"
                                                onclick="addProductToSale({
                                                    id: <?= $product['id'] ?>,
                                                    name: '<?= htmlspecialchars($product['name']) ?>',
                                                    code: '<?= htmlspecialchars($product['code']) ?>',
                                                    price: <?= $product['price'] ?>,
                                                    quantity: 1
                                                })">
                                            <i class="fas fa-plus"></i> Agregar
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para nuevo cliente -->
<div class="modal fade" id="newCustomerModal" tabindex="-1" aria-labelledby="newCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newCustomerModalLabel">Nuevo Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="newCustomerForm">
                    <div class="mb-3">
                        <label for="customer_name" class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="customer_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="customer_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="customer_email">
                    </div>
                    <div class="mb-3">
                        <label for="customer_phone" class="form-label">Teléfono</label>
                        <input type="tel" class="form-control" id="customer_phone">
                    </div>
                    <div class="mb-3">
                        <label for="customer_address" class="form-label">Dirección</label>
                        <textarea class="form-control" id="customer_address" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="saveCustomerBtn">Guardar Cliente</button>
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
            <input type="number" name="items[][price]" class="form-control form-control-sm text-end price" 
                   min="0.01" step="0.01" required>
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
<script>
// Variables globales
let products = <?= json_encode($products) ?>;
let customers = <?= json_encode($customers) ?>;
let saleItems = [];

// Inicialización cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar Select2 para búsqueda de clientes
    if ($.fn.select2) {
        $('.select2').select2({
            theme: 'bootstrap-5',
            placeholder: 'Seleccione un cliente',
            allowClear: true
        });
    }
    
    // Búsqueda de productos
    $('#productSearch').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        
        $('#productsList tr').each(function() {
            const productName = $(this).find('td:first').text().toLowerCase();
            const productCode = $(this).find('td:nth-child(2)').text().toLowerCase();
            
            if (productName.includes(searchTerm) || productCode.includes(searchTerm)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
    
    // Agregar producto a la venta
    $(document).on('click', '.add-product-btn', function() {
        const productId = $(this).data('product-id');
        const productName = $(this).data('product-name');
        const productCode = $(this).data('product-code');
        const productPrice = parseFloat($(this).data('product-price'));
        const quantity = parseInt($(this).closest('tr').find('.quantity-input').val());
        
        addProductToSale({
            id: productId,
            name: productName,
            code: productCode,
            price: productPrice,
            quantity: quantity
        });
        
        // Cerrar el modal
        $('#addProductModal').modal('hide');
    });
    
    // Eliminar ítem de la venta
    $(document).on('click', '.remove-item', function() {
        $(this).closest('tr').remove();
        updateTotals();
        
        // Mostrar mensaje si no hay ítems
        if ($('#saleItems tr:not(#noItemsRow)').length === 0) {
            $('#noItemsRow').show();
        }
    });
    
    // Actualizar totales al cambiar cantidad o precio
    $(document).on('input', '.quantity, .price', function() {
        updateItemSubtotal($(this).closest('tr'));
        updateTotals();
    });
    
    // Guardar nuevo cliente
    $('#saveCustomerBtn').on('click', function() {
        const customerData = {
            name: $('#customer_name').val(),
            email: $('#customer_email').val(),
            phone: $('#customer_phone').val(),
            address: $('#customer_address').val()
        };
        
        if (!customerData.name) {
            alert('El nombre del cliente es obligatorio');
            return;
        }
        
        // Aquí iría la llamada AJAX para guardar el cliente
        // Por ahora, simulamos la respuesta
        const newCustomerId = customers.length + 1;
        const newCustomer = {
            id: newCustomerId,
            name: customerData.name,
            email: customerData.email,
            phone: customerData.phone,
            address: customerData.address
        };
        
        // Agregar el nuevo cliente al select
        const option = new Option(newCustomer.name, newCustomerId, true, true);
        $('#customer_id').append(option).trigger('change');
        
        // Limpiar el formulario
        $('#newCustomerForm')[0].reset();
        
        // Cerrar el modal
        $('#newCustomerModal').modal('hide');
        
        // Agregar a la lista de clientes (solo en memoria)
        customers.push(newCustomer);
    });
    
    // Calcular impuestos y totales cuando cambien los valores
    $('#tax_percent, #discount').on('input', updateTotals);
    
    // Validación del formulario
    $('#saleForm').on('submit', function(e) {
        e.preventDefault();
        
        // Validar que haya al menos un ítem
        if ($('#saleItems tr:not(#noItemsRow)').length === 0) {
            alert('Debe agregar al menos un producto a la venta');
            return false;
        }
        
        // Actualizar campos ocultos con los totales
        const subtotal = parseFloat($('#subtotal').text().replace('$', '').replace(/,/g, '')) || 0;
        const taxPercent = parseFloat($('#tax_percent').val()) || 0;
        const tax = (subtotal * taxPercent) / 100;
        const discount = parseFloat($('#discount').val()) || 0;
        const total = subtotal + tax - discount;
        
        $('#subtotal_value').val(subtotal.toFixed(2));
        $('#tax_value').val(tax.toFixed(2));
        $('#total_value').val(total.toFixed(2));
        
        // Enviar el formulario
        this.submit();
    });
    
    // Inicializar tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
});

// Función para agregar un producto a la venta
function addProductToSale(product) {
    // Verificar si el producto ya está en la venta
    const existingRow = $(`#saleItems tr[data-product-id="${product.id}"]`);
    
    if (existingRow.length > 0) {
        // Si el producto ya está en la venta, actualizar la cantidad
        const quantityInput = existingRow.find('.quantity');
        const newQuantity = parseInt(quantityInput.val()) + product.quantity;
        quantityInput.val(newQuantity).trigger('input');
    } else {
        // Si el producto no está en la venta, agregarlo
        const template = document.getElementById('saleItemTemplate');
        const clone = template.content.cloneNode(true);
        
        const row = clone.querySelector('tr');
        row.dataset.productId = product.id;
        
        row.querySelector('.product-name').textContent = product.name;
        row.querySelector('.product-code').textContent = product.code;
        row.querySelector('.product-id').value = product.id;
        row.querySelector('.quantity').value = product.quantity;
        row.querySelector('.price').value = product.price.toFixed(2);
        
        // Actualizar subtotal del ítem
        updateItemSubtotal($(row));
        
        // Agregar a la tabla
        $('#noItemsRow').hide();
        $('#saleItems').append(row);
        
        // Actualizar totales
        updateTotals();
    }
}

// Función para actualizar el subtotal de un ítem
function updateItemSubtotal(row) {
    const quantity = parseFloat(row.find('.quantity').val()) || 0;
    const price = parseFloat(row.find('.price').val()) || 0;
    const subtotal = quantity * price;
    
    row.find('.item-subtotal').text('$' + subtotal.toFixed(2));
}

// Función para actualizar los totales
function updateTotals() {
    let subtotal = 0;
    
    // Calcular subtotal sumando los subtotales de cada ítem
    $('#saleItems tr:not(#noItemsRow)').each(function() {
        const quantity = parseFloat($(this).find('.quantity').val()) || 0;
        const price = parseFloat($(this).find('.price').val()) || 0;
        subtotal += quantity * price;
    });
    
    // Calcular impuestos y descuentos
    const taxPercent = parseFloat($('#tax_percent').val()) || 0;
    const tax = (subtotal * taxPercent) / 100;
    const discount = parseFloat($('#discount').val()) || 0;
    const total = subtotal + tax - discount;
    
    // Actualizar la interfaz
    $('#subtotal').text('$' + subtotal.toFixed(2));
    $('#tax_value').val(tax.toFixed(2));
    $('#discount').val(discount.toFixed(2));
    $('#total').text('$' + total.toFixed(2));
    
    // Actualizar campos ocultos
    $('#subtotal_value').val(subtotal.toFixed(2));
    $('#total_value').val(total.toFixed(2));
}

// Función para limpiar el formulario
function clearForm() {
    if (confirm('¿Está seguro de que desea cancelar esta venta? Se perderán todos los datos ingresados.')) {
        window.location.href = '/sales';
    }
}
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
