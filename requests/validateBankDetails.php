<?php
function validateBankDetails(){
    $errors = [];
    $bank_account_number = trim($_POST['bank_account_number'] ?? '');
    $bank_account_name = trim($_POST['bank_account_name'] ?? '');
    $bank_name = trim($_POST['bank_name'] ?? '');

    // validation for bank account number
    validateBankAccountNumber($bank_account_number, $errors);
    // validation for bank account name
    validateBankAccountName($bank_account_name, $errors);
    // validation for bank name
    validateBankName($bank_name, $errors);

    // echo "Errors: " . json_encode($errors); // Debugging line to check errors
    if(!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['old'] = [
            'bank_account_number' => $bank_account_number,
            'bank_account_name' => $bank_account_name,
            'bank_name' => $bank_name
        ];
        header('Location: ' . Config::get('baseProjectFolder') . '/dashboard');
        exit();
    }

    return [
        'bank_account_number' => $bank_account_number,
        'bank_account_name' => strtolower($bank_account_name),
        'bank_name' => strtolower($bank_name),
        'errors' => $errors
    ];
}