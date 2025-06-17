<!DOCTYPE html>
<html lang="es" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Inventario - <?= $title ?? 'Inicio' ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --sidebar-width: 250px;
        }
        
        body {
            font-size: 0.9rem;
            background-color: #f8f9fa;
        }
        
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: #212529;
            color: #fff;
            transition: all 0.3s;
            z-index: 1000;
            padding-top: 56px; /* Altura del navbar */
            overflow-y: auto;
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            min-height: calc(100vh - 56px);
            transition: all 0.3s;
        }
        
        .navbar {
            padding: 0.5rem 1rem;
            background: #fff !important;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            position: fixed;
            width: 100%;
            z-index: 1030;
        }
        
        .nav-link {
            color: #dee2e6;
            padding: 0.5rem 1rem;
            margin: 0.2rem 0.5rem;
            border-radius: 0.25rem;
        }
        
        .nav-link:hover, .nav-link.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: #fff;
        }
        
        .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .card {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            font-weight: 600;
        }
        
        .table th {
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            color: #6c757d;
            border-top: none;
            border-bottom: 1px solid #dee2e6;
        }
        
        .table td {
            vertical-align: middle;
        }
        
        .badge {
            font-weight: 500;
            padding: 0.35em 0.65em;
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
            }
            
            .sidebar.show {
                margin-left: 0;
            }
            
            .main-content {
                margin-left: 0;
                width: 100%;
            }
            
            .main-content.sidebar-collapsed {
                margin-left: 0;
            }
        }
        
        /* Scrollbar personalizada */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <?php include __DIR__ . '/sidebar.php'; ?>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
