<?php include __DIR__ . '/../layouts/header.php'; ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Panel de Control</h1> 
</div>


<div class="card">
    <div class="card-body">
        <div class="row">
           
          

                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="card text-white bg-primary">
                            <div class="card-body">
                                <h5 class="card-title">Productos</h5>
                                <h2 class="card-text">0</h2>
                                <p class="card-text">Productos registrados</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card text-white bg-success">
                            <div class="card-body">
                                <h5 class="card-title">Ventas</h5>
                                <h2 class="card-text">0</h2>
                                <p class="card-text">Ventas este mes</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card text-white bg-warning">
                            <div class="card-body">
                                <h5 class="card-title">Inventario</h5>
                                <h2 class="card-text">0</h2>
                                <p class="card-text">Productos en stock</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Resumen Reciente</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">Bienvenido al sistema de inventario, <?= htmlspecialchars($user['name']) ?>.</p>
                        <p class="card-text">Selecciona una opción del menú lateral para comenzar.</p>
                    </div>
                </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
