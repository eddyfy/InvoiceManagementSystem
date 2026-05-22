<?php
// require_once 'validators.php';
function validateUpdateProfile(): array{
            $errors = [];
            
            $userId = $_SESSION['user']['id'];
            $firstname = ucfirst(sanitizeInput(trim($_POST['firstname'] ?? '')));
            $lastname = ucfirst(sanitizeInput(trim($_POST['lastname'] ?? '')));
            $email = strtolower(trim($_POST['email'] ?? ''));

            // validation for firstname
            validateFirstName($firstname, $errors);
            // validation for lastname
            validateLastName($lastname, $errors);
            // validation for email
            validateEmail($email, $errors);
            if(!empty($errors)){
                $_SESSION['errors'] = $errors;
                $_SESSION['old'] = ['firstname' => $firstname, 'lastname' => $lastname, 'email' => $email];
                header('Location: ' . Config::get('baseProjectFolder') . '/dashboard');
                exit();
            } 
    return [
        'user_id' => $userId,
        'firstname' => $firstname,
        'lastname' => $lastname,
        'email' => $email
    ];
}