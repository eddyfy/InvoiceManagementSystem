<?php
declare(strict_types=1);
namespace App\Requests; 
use App\Config;
use App\Utils;

class ValidateUpdateProfile{
        public static function validate(): array{
        $errors = [];
        
        $userId = $_SESSION['user']['id'];
        $firstname = ucfirst(Utils::sanitizeInput(trim($_POST['firstname'] ?? '')));
        $lastname = ucfirst(Utils::sanitizeInput(trim($_POST['lastname'] ?? '')));
        $email = strtolower(trim($_POST['email'] ?? ''));
        $bank_account_number = trim($_POST['bank_account_number'] ?? '');
        $bank_account_name = trim($_POST['bank_account_name'] ?? '');
        $bank_name = trim($_POST['bank_name'] ?? '');

        // validation for firstname
        Validators::validateProfileFirstName($firstname, $errors);
        // validation for lastname
        Validators::validateProfileLastName($lastname, $errors);
        // validation for email 
        Validators::validateProfileEmail($email, $errors);
        // validation for bank account number
        Validators::validateProfileBankAccountNumber($bank_account_number, $errors);
        // validation for bank account name
        Validators::validateProfileBankAccountName($bank_account_name, $errors);
        // validation for bank name
        Validators::validateProfileBankName($bank_name, $errors);
        
        if(!empty($errors)){
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = ['firstname' => $firstname, 'lastname' => $lastname, 'email' => $email, 'bank_account_number' => $bank_account_number, 'bank_account_name' => $bank_account_name, 'bank_name' => $bank_name];
            header('Location: ' . Config::get('baseProjectFolder') . '/dashboard');
            exit();
        } 
        return [
            'user_id' => $userId,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email,
            'bank_account_number' => $bank_account_number,
            'bank_account_name' => strtolower($bank_account_name),
            'bank_name' => strtolower($bank_name)
        ];
    }
}