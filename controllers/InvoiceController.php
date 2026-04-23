<?php
declare(strict_types=1);
require_once './Config.php'; // Include the configuration file to load environment variables

class InvoiceController{
    public function showInvoiceForm(): void {
        // session_start();
        require './views/invoice_form.php'; // Include the invoice form view to display it to the user
    }
    public function handleInvoiceSubmission(): void {
       // Create a new instance of the Config class to access configuration settings
            requireAuth(); 
            $errors = []; 
            if(isset($_POST['csrf_token'], $_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])){ // Check if the CSRF token from the form matches the one stored in the session
                unset($_SESSION['csrf_token']); // Unset the CSRF token from the session to prevent reuse
                $invoiceModel = new Invoice();
                print_r($_POST);
                // $invoiceModel->create(); // Call the createInvoice method of the Invoice model to handle the invoice creation logic
                
            } // Store a success message in
    }
} 