<?php
declare(strict_types=1);
namespace App\Requests;

use App\Config;
class ValidateBusinessDetails{
    public static function validate(): array{
        $errors = [];
        $business_name = strtolower(trim($_POST['business_name'] ?? ''));
        $business_address = trim($_POST['business_address'] ?? '');
        $business_email = strtolower(trim($_POST['business_email'] ?? ''));
        $business_phone = trim($_POST['business_phone'] ?? '');
        $bank_account_number = trim($_POST['bank_account_number'] ?? '');
        $bank_account_name = trim($_POST['bank_account_name'] ?? '');
        $bank_name = trim($_POST['bank_name'] ?? '');
        $source = $_POST['source'];


        // Validation for all fields based on the source of the request (profile update or modal)

        Validators::validateBusinessName($business_name, $errors, $source);
        Validators::validateBusinessAddress($business_address, $errors, $source);
        Validators::validateBusinessEmail($business_email, $errors, $source);
        Validators::validateBusinessPhone($business_phone, $errors, $source);
        Validators::validateBankAccountNumber($bank_account_number, $errors, $source);
        Validators::validateBankAccountName($bank_account_name, $errors, $source);
        Validators::validateBankName($bank_name, $errors, $source);
       
        // echo "Errors: " . json_encode($errors); // Debugging line to check errors
        if(!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = [
                'business_name' => $business_name,
                'business_address' => $business_address,
                'business_email' => $business_email,
                'business_phone' => $business_phone,
                'bank_account_number' => $bank_account_number,
                'bank_account_name' => $bank_account_name,
                'bank_name' => $bank_name
            ];
            header('Location: ' . Config::get('baseProjectFolder') . '/dashboard');
            exit();
        }
        
        return [
            'business_name' => $business_name,
            'business_address' => $business_address,
            'business_email' => $business_email,
            'business_phone' => $business_phone,
            'bank_account_number' => $bank_account_number,
            'bank_account_name' => strtolower($bank_account_name),
            'bank_name' => strtolower($bank_name),
            // 'errors' => $errors
        ];
    }
}
