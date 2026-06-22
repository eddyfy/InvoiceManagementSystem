<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Models\Invoice;
use App\Utils;  

class HomeController{
    public function index(): void {
        unset($_SESSION['previous_page']);
        $logoutMessage = '';
        if (isset($_SESSION['message'])) {
            $logoutMessage = $_SESSION['message'];
            unset($_SESSION['message']);
        }
        require './views/home.php'; 
    }
    public function dashboard(): void{
        Utils::requireAuth();
        
        unset($_SESSION['previous_page']);
        $userId = $_SESSION['user']['id'];
        $invoiceModel = new Invoice();

        $page = (int) ($_GET['page'] ?? 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;


        $stats = $invoiceModel->getStats($userId);
        $recentInvoices = $invoiceModel->getRecentByUser($userId, 3);
        $allInvoices = $invoiceModel->getPaginatedByUserId($userId, $limit, $offset);
        $totalInvoices = $invoiceModel->countByUserId($userId);
        $totalPages = (int) ceil($totalInvoices / $limit);

        $_SESSION['csrf_token'] = password_hash(bin2hex(random_bytes(32)), PASSWORD_DEFAULT);
        $errors = [];
        $old =[];
        if(isset($_SESSION['errors'])){
            $old = $_SESSION['old'] ?? [];
            $errors = $_SESSION['errors'];  
            unset($_SESSION['errors'], $_SESSION['old']);
        }
        require './views/dashboard.php';
    }
}