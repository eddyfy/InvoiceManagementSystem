<?php
declare(strict_types=1);
namespace App\Requests;
use App\Config;
class ValidateInvoice{
public static function validate(){
            $errors = []; 
            
                    unset($_SESSION['csrf_token']); // Unset the CSRF token from the session to prevent reuse
                    
                    $user_id = $_POST['user_id'] ?? null;
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
                    
                    Validators::validateInvoiceNumber($invoice_number, $errors);
                    Validators::validateInvoiceDate($invoice_date, $errors);
                    Validators::validateCustomerName($customer_name, $errors);
                    Validators::validateCustomerEmail($customer_email, $errors);
                    Validators::validateInvoiceItems($items, $errors);

                    if(!empty($errors)){
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
                        //var_dump($errors); exit();
                        //var_dump($_SESSION); exit();
                        header('Location: ' . Config::get('baseProjectFolder') . '/invoice'); // Redirect back to the invoice form if there are validation errors
                        exit();
                    }

                    return [
                        'user_id' => $user_id,
                        'invoice_number' => $invoice_number,
                        'invoice_date' => $invoice_date,
                        'customer_name' => $customer_name,
                        'customer_email' => $customer_email,
                        'items' => $items,
                        'subtotal' => $subtotal,
                        'discount' => $discount,
                        'tax_rate' => $tax_rate,
                        'tax_amount' => $tax_amount,
                        'grand_total' => $grand_total,
                        'notes' => $notes
                    ];             
    }
}