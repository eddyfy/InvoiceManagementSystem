<?php 

declare(strict_types=1);
function validateLogin(): array{
    $errors = []; // Initialize an array to store validation errors            
    // global $email, $password;
    // var_dump($_POST);
    
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

   
    //validation for email
    validateEmail($email, $errors);
    
        //validation for password  
    validatePassword($password, $errors);
    
    if(!empty($errors)){
        $_SESSION['errors'] = $errors; // Store the errors in the session to display them on the form
        $_SESSION['old'] = ['email' => $email]; // Store the old input values in the session to repopulate the form
        header('Location: ' . Config::get('baseProjectFolder') . '/login'); // Redirect back to the login form if there are validation errors
        exit();
    }
    
    return [
        'email' => $email,
        'password' => $password
    ];
}