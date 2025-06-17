<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Sistema de Inventario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            height: 100vh;
            display: flex;
            align-items: center;
            background-color: #f5f5f5;
        }
        .form-register {
            width: 100%;
            max-width: 400px;
            padding: 15px;
            margin: auto;
        }
        .form-register .form-floating:focus-within {
            z-index: 2;
        }
        .form-register input[type="text"],
        .form-register input[type="email"] {
            margin-bottom: -1px;
            border-bottom-right-radius: 0;
            border-bottom-left-radius: 0;
        }
        .form-register input[type="password"] {
            margin-bottom: 10px;
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }
    </style>
</head>
<body class="text-center">
    <main class="form-register">
        <form method="POST" action="/auth/register">
            <h1 class="h3 mb-3 fw-normal">Crear Cuenta</h1>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <div class="form-floating">
                <input type="text" class="form-control" id="full_name" name="full_name" placeholder="Nombre completo" required autofocus>
                <label for="full_name">Nombre completo</label>
            </div>
            
            <div class="form-floating">
                <input type="email" class="form-control" id="email" name="email" placeholder="nombre@ejemplo.com" required>
                <label for="email">Correo electrónico</label>
            </div>
            
            <div class="form-floating">
                <input type="text" class="form-control" id="username" name="username" placeholder="Usuario" required>
                <label for="username">Nombre de usuario</label>
            </div>
            
            <div class="form-floating">
                <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" required>
                <label for="password">Contraseña</label>
            </div>
            
            <div class="form-floating">
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirmar contraseña" required>
                <label for="confirm_password">Confirmar contraseña</label>
            </div>

            <button class="w-100 btn btn-lg btn-primary" type="submit">Registrarse</button>
            
            <p class="mt-3">
                ¿Ya tienes una cuenta? <a href="/auth/login">Inicia sesión aquí</a>
            </p>
            
            <p class="mt-3 mb-3 text-muted">&copy; <?= date('Y') ?> Sistema de Inventario</p>
        </form>
    </main>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
