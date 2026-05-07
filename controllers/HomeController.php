<?php
declare(strict_types=1);

class HomeController{
    public function index(): void {
        require './views/home.php'; 
    }
    public function dashboard(): void{
        requireAuth();
       
        require './views/dashboard.php';
    }
}