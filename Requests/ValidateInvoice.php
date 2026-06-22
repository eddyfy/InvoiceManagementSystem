<?php
declare(strict_types=1);
namespace App\Requests;
use App\Config;

class ValidateInvoice{
public static function validate(string $redirectTo = '/invoice'): array{
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

                    //for dealing with public invoice generation
                    $source = $_POST['source'] ?? 'public'; 
                    $business_section_visible = $_POST['business_section_visible'];
                    $business_name = strtolower(trim($_POST['business_name'] ?? ''));
                    $business_address = trim($_POST['business_address'] ?? '');
                    $business_email = strtolower(trim($_POST['business_email'] ?? ''));
                    $business_phone = trim($_POST['business_phone'] ?? '');
                    $bank_account_number = trim($_POST['bank_account_number'] ?? '');
                    $bank_account_name = trim($_POST['bank_account_name'] ?? '');
                    $bank_name = trim($_POST['bank_name'] ?? '');
                    echo $source;
                    echo $business_section_visible;

                    if($source === 'public' && $business_section_visible === 'true'){
                            Validators::validateBusinessName($business_name, $errors, $source);
                            Validators::validateBusinessAddress($business_address, $errors, $source);
                            Validators::validateBusinessEmail($business_email, $errors, $source);
                            Validators::validateBusinessPhone($business_phone, $errors, $source);
                            Validators::validateBankAccountNumber($bank_account_number, $errors, $source);
                            Validators::validateBankAccountName($bank_account_name, $errors, $source);
                            Validators::validateBankName($bank_name, $errors, $source);
                            // die("PUBLIC!!");
                    }

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
                        header('Location: ' . Config::get('baseProjectFolder') . $redirectTo);  // Redirect back to the invoice form if there are validation errors
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
                        'notes' => $notes,
                        'business_name' => $business_name ?? '',
                        'business_address' => $business_address ?? '',
                        'business_email' => $business_email ?? '',
                        'business_phone' => $business_phone ?? '',
                        'bank_account_number' => $bank_account_number ?? '',
                        'bank_account_name' => $bank_account_name ?? '',
                        'bank_name' => $bank_name ?? ''
                    ];             
    }
}