<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="h4 mb-0">Detalles de la Categoría</h2>
                    <div class="btn-group">
                        <a href="/categories/<?= $category['id'] ?>/edit" class="btn btn-warning btn-sm">
                            <i class="bi bi-pencil"></i> Editar
                        </a>
                        <form action="/categories/<?= $category['id'] ?>" method="POST" class="ms-2" 
                              onsubmit="return confirm('¿Estás seguro de eliminar esta categoría?')">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="bi bi-trash"></i> Eliminar
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h5 class="card-subtitle mb-1 text-muted">ID</h5>
                        <p class="card-text"><?= $category['id'] ?></p>
                    </div>
                    
                    <div class="mb-3">
                        <h5 class="card-subtitle mb-1 text-muted">Nombre</h5>
                        <p class="card-text"><?= htmlspecialchars($category['name']) ?></p>
                    </div>
                    
                    <div class="mb-3">
                        <h5 class="card-subtitle mb-1 text-muted">Descripción</h5>
                        <p class="card-text"><?= !empty($category['description']) ? nl2br(htmlspecialchars($category['description'])) : '<span class="text-muted">Sin descripción</span>' ?></p>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="card-subtitle mb-1 text-muted">Creado</h5>
                            <p class="card-text">
                                <i class="bi bi-calendar3"></i> <?= date('d/m/Y H:i', strtotime($category['created_at'])) ?>
                            </p>
                        </div>
                        <?php if (!empty($category['updated_at'])): ?>
                        <div class="col-md-6">
                            <h5 class="card-subtitle mb-1 text-muted">Actualizado</h5>
                            <p class="card-text">
                                <i class="bi bi-arrow-repeat"></i> <?= date('d/m/Y H:i', strtotime($category['updated_at'])) ?>
                            </p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-footer text-muted">
                    <a href="/categories" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Volver al listado
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
