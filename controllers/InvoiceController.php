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
                
                $user_id = $_POST['user_id'] ?? null;
                // print_r($_POST); 
                $invoice_number = trim($_POST['invoice_number'] ?? '');
                $invoice_date = trim($_POST['invoice_date'] ?? '');
                $customer_name = trim($_POST['customer_name'] ?? '');
                $customer_email = strtolower(trim($_POST['customer_email'] ?? ''));
                $subtotal = $_POST['subtotal'] ?? 0;
                $items = $_POST['items'] ?? [];
                $discount = (float) ($_POST['discount'] ?? 0);
                $tax_rate = (float) ($_POST['tax_rate'] ?? 0);
                $tax_amount = (float) ($_POST['tax_amount'] ?? 0);
                $grand_total = (float) ($_POST['grand_total'] ?? 0);
                $notes = trim($_POST['notes'] ?? ''); 
                
                if(empty($invoice_number)){
                    $errors["invoice_number"][] = "Invoice number is required.";
                }
                if(empty($invoice_date)){
                    $errors["invoice_date"][] = "Invoice date is required.";
                }
                if(empty($customer_name)){
                    $errors["customer_name"][] = "Customer name is required.";
                }else if(isLongerThan($customer_name, 255)){
                    $errors["customer_name"][] = "Customer name must be less than 255 characters.";
                }
                if(empty($customer_email)){
                    $errors["customer_email"][] = "Customer email is required.";
                }else if(isLongerThan($customer_email, 255)){
                    $errors["customer_email"][] = "Customer email must be less than 255 characters.";
                }else if(!isEmail($customer_email)){
                    $errors["customer_email"][] = "Invalid email format.";
                }
                if(empty($items) || !is_array($items)){
                    $errors["items_general"][] = "At least one item is required.";
                    
                }else{
                    foreach($items as $index => $item){
                        if(empty($item['description'])){
                            $errors["items"][$index]['description'][] = "Description is required.";
                        }else if(isLongerThan($item['description'], 255)){
                            $errors["items"][$index]['description'][] = "Description must be less than 255 characters.";
                        }
                        if(empty($item['quantity']) || !is_numeric($item['quantity']) || $item['quantity'] <= 0){
                            $errors["items"][$index]['quantity'][] = "Quantity must be a positive number.";
                        }
                        if(empty($item['price']) || !is_numeric($item['price']) || $item['price'] < 0){
                            $errors["items"][$index]['price'][] = "Price must be a non-negative number.";
                        }
                    }
                    foreach($items as $index => $item){
                        $item['description'] = strtolower(trim($item['description'] ?? ''));
                    }
                }       
                if(!empty($errors)){//if there are validation errors, store them in the session and redirect back to the invoice form
                    $_SESSION['errors'] = $errors; // Store the errors in the session to display them on the form
                    $_SESSION['old'] = [
                        'invoice_number' => $invoice_number,
                        'invoice_date' => $invoice_date,
                        'customer_name' => $customer_name,
                        'customer_email' => $customer_email,
                        'items' => $items,
                        'discount' => $discount,
                        'tax_rate' => $tax_rate,
                        'tax_amount' => $tax_amount,
                        'notes' => $notes
                    ]; // Store the old input values in the session to repopulate the form
                    header('Location: ' . Config::get('baseProjectFolder') . '/invoice'); // Redirect back to the invoice form if there are validation errors
                    exit();
                }else{
                    $invoiceModel = new Invoice();
                    $invoiceItemModel = new InvoiceItem();
                    try{
                        $invoiceModel->pdo->beginTransaction(); // Start a database transaction to ensure data integrity during invoice creation
                        $invoice = $invoiceModel->create([
                            'user_id' => $user_id,
                            'invoice_number' => $invoice_number,
                            'invoice_date' => $invoice_date,
                            'customer_name' => $customer_name,
                            'customer_email' => $customer_email,
                            'subtotal' => $subtotal,
                            'tax_rate' => $tax_rate,
                            'tax_amount' => $tax_amount,
                            'discount' => $discount,
                            'grand_total' => $grand_total,
                            'notes' => $notes
                        ]); 
                        
                        
                        // print_r($invoice); 
                        
                        $items = array_map(function($item) use ($invoice){
                            return [
                                'invoice_id' => $invoice->id,
                                'description' => $item['description'],
                                'quantity' => $item['quantity'],
                                'price' => $item['price'],
    
                            ];
                        }, $items);
                        foreach($items as $item){
                            $invoiceItemModel->create($item);
                        } // Call the create method of the InvoiceItem  
                         // Output a success message with the created invoice ID
                        $invoiceModel->pdo->commit();
                        $_SESSION['message'] = "Invoice created successfully with ID: " . $invoice->id; // Store a success message in the session to display on the dashboard
                        header('Location: ' . Config::get('baseProjectFolder') . '/dashboard');
                        // Call the create method of the Invoice model to handle the invoice creation logic, passing the invoice data and items
                    }catch(Exception $e){
                        $invoiceModel->pdo->rollBack(); // Roll back the transaction if an error occurs during invoice creation to maintain data integrity
                        die("Error creating invoice: " . $e->getMessage()); // Handle any errors that occur during invoice creation
                    }
                }
            
            } 
    }
    
    
} 