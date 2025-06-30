<?php include __DIR__ . '/../layouts/header.php'; ?>
 
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2"><?= $title ?></h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="/sales/create" class="btn btn-success">
                        <i class="bi bi-plus"></i> Nueva Venta
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
                                            <td>$<?= number_format($sale['total'], 2) ?></td>
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
                                                <a href="/sales/<?= $sale['id'] ?>/show" class="btn btn-sm btn-info" 
                                                   title="Ver detalles">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <?php if ($sale['status'] !== 'cancelled' && $_SESSION['role'] === 'admin'): ?>
                                                    <a href="/sales/<?= $sale['id'] ?>/edit" class="btn btn-sm btn-warning" 
                                                       title="Editar">
                                                        <i class="bi bi-pencil"></i>
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
