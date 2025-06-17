<?php
require_once 'BaseController.php';

class DashboardController extends BaseController {
    public function index() {
        requireAuth();
        
        // Aquí iría la lógica para obtener los datos del dashboard
        $data = [
            'pageTitle' => 'Panel de Control',
            'user' => [
                'name' => $_SESSION['username']
            ]
        ];
        
        $this->render('dashboard/index', $data);
    }
}
