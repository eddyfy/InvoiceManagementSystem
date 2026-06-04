<?php
// require_once 'validators.php';
function validateUpdateProfile(): array{
    $errors = [];
    
    $userId = $_SESSION['user']['id'];
    $firstname = ucfirst(sanitizeInput(trim($_POST['firstname'] ?? '')));
    $lastname = ucfirst(sanitizeInput(trim($_POST['lastname'] ?? '')));
    $email = strtolower(trim($_POST['email'] ?? ''));
    $bank_account_number = trim($_POST['bank_account_number'] ?? '');
    $bank_account_name = trim($_POST['bank_account_name'] ?? '');
    $bank_name = trim($_POST['bank_name'] ?? '');

    // validation for firstname
    validateProfileFirstName($firstname, $errors);
    // validation for lastname
    validateProfileLastName($lastname, $errors);
    // validation for email 
    validateProfileEmail($email, $errors);
    // validation for bank account number
    validateProfileBankAccountNumber($bank_account_number, $errors);
    // validation for bank account name
    validateProfileBankAccountName($bank_account_name, $errors);
    // validation for bank name
    validateProfileBankName($bank_name, $errors);
    
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