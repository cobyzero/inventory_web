<?php
require_once __DIR__ . '/../layouts/header.php';
?>
<div class="container mt-5">
    <h2>Bienvenido al área de clientes</h2>
    <p>Aquí puedes ver los productos y realizar un pedido.</p>
    <a href="/products" class="btn btn-primary">Ver productos</a>
    <a href="/sales/create" class="btn btn-success">Hacer un pedido</a>
</div>
<?php
require_once __DIR__ . '/../layouts/footer.php';
?>
