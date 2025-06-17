<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Inventory.php';

class DashboardController extends BaseController {
    private $productModel;
    private $inventoryModel;
    
    public function __construct() {
        parent::__construct();
        $this->productModel = new Product($this->db);
        $this->inventoryModel = new Inventory($this->db);
    }
    
    public function index() {
        requireAuth();
        
        // Obtener contadores
        $productCount = $this->productModel->countAll();
        $inventoryStats = $this->inventoryModel->getInventoryStats();
        $salesThisMonth = $this->getSalesThisMonth();
        
        $data = [
            'pageTitle' => 'Panel de Control',
            'user' => [
                'name' => $_SESSION['username']
            ],
            'productCount' => $productCount,
            'inStockCount' => $inventoryStats['total_quantity'] ?? 0,
            'salesThisMonth' => $salesThisMonth,
            'lowStockProducts' => $inventoryStats['low_stock'] ?? []
        ];
        
        $this->render('dashboard/index', $data);
    }
    
    private function getSalesThisMonth() {
        $query = "SELECT COUNT(*) as count FROM sales";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }
}
