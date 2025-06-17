<?php include __DIR__ . '/../layouts/header.php'; ?>
 
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2"><?= $title ?></h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="/sales/create" class="btn btn-success">
                        <i class="fas fa-plus"></i> Nueva Venta
                    </a>
                </div>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $_SESSION['success'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-filter me-1"></i>
                    Filtros
                </div>
                <div class="card-body">
                    <form method="get" class="row g-3">
                        <div class="col-md-3">
                            <label for="status" class="form-label">Estado</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Todos</option>
                                <?php foreach ($statuses as $value => $label): ?>
                                    <option value="<?= $value ?>" <?= (isset($_GET['status']) && $_GET['status'] === $value) ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="payment_method" class="form-label">Método de Pago</label>
                            <select class="form-select" id="payment_method" name="payment_method">
                                <option value="">Todos</option>
                                <?php foreach ($payment_methods as $value => $label): ?>
                                    <option value="<?= $value ?>" <?= (isset($_GET['payment_method']) && $_GET['payment_method'] === $value) ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="search" class="form-label">Buscar</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="search" name="search" 
                                       placeholder="N° de factura, cliente..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <a href="/sales" class="btn btn-secondary">
                                <i class="fas fa-sync-alt"></i> Limpiar
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>N° Factura</th>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Total</th>
                                    <th>Método de Pago</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($sales)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center">No se encontraron ventas</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($sales as $sale): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($sale['invoice_number_formatted']) ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($sale['created_at'])) ?></td>
                                            <td><?= htmlspecialchars($sale['customer_name'] ?? 'Cliente no especificado') ?></td>
                                            <td class="text-end">$<?= number_format($sale['total'], 2) ?></td>
                                            <td>
                                                <?= $payment_methods[$sale['payment_method']] ?? ucfirst($sale['payment_method']) ?>
                                            </td>
                                            <td>
                                                <?php
                                                $status_class = [
                                                    'completed' => 'success',
                                                    'pending' => 'warning',
                                                    'cancelled' => 'danger'
                                                ][$sale['status']] ?? 'secondary';
                                                ?>
                                                <span class="badge bg-<?= $status_class ?>">
                                                    <?= $statuses[$sale['status']] ?? ucfirst($sale['status']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="/sales/<?= $sale['id'] ?>" class="btn btn-sm btn-info" 
                                                   title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <?php if ($sale['status'] !== 'cancelled'): ?>
                                                    <a href="/sales/<?= $sale['id'] ?>/edit" class="btn btn-sm btn-warning" 
                                                       title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <?php if ($total_pages > 1): ?>
                        <nav aria-label="Page navigation" class="mt-4">
                            <ul class="pagination justify-content-center">
                                <?php if ($current_page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?= $current_page - 1 ?><?= !empty($_GET['status']) ? '&status=' . urlencode($_GET['status']) : '' ?><?= !empty($_GET['payment_method']) ? '&payment_method=' . urlencode($_GET['payment_method']) : '' ?><?= !empty($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?>">
                                            Anterior
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
                                        <a class="page-link" href="?page=<?= $i ?><?= !empty($_GET['status']) ? '&status=' . urlencode($_GET['status']) : '' ?><?= !empty($_GET['payment_method']) ? '&payment_method=' . urlencode($_GET['payment_method']) : '' ?><?= !empty($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?>">
                                            <?= $i ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if ($current_page < $total_pages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?= $current_page + 1 ?><?= !empty($_GET['status']) ? '&status=' . urlencode($_GET['status']) : '' ?><?= !empty($_GET['payment_method']) ? '&payment_method=' . urlencode($_GET['payment_method']) : '' ?><?= !empty($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?>">
                                            Siguiente
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
            </div> 

<?php include __DIR__ . '/../layouts/footer.php'; ?>
