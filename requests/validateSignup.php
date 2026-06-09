<?php
declare(strict_types=1);
namespace App\Requests;
use App\Config;
use App\Utils;

class ValidateSignup{
    public static function validate(): array{
        $errors = [];
        // Validate input
            unset($_SESSION['csrf_token']); // Unset the CSRF token from the session to prevent reuse
            $firstname = Utils::sanitizeInput(trim($_POST['firstname'] ?? ''));
            $lastname = Utils::sanitizeInput(trim($_POST['lastname'] ?? ''));
            $email = strtolower(trim($_POST['email'] ?? ''));
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

             //validation for firstname
            Validators::validateFirstName($firstname, $errors);

             //validation for lastname
            Validators::validateLastName($lastname, $errors);

             //validation for email
            Validators::validateEmail($email, $errors);
                
            
             //validation for password  
            Validators::validatePassword($password, $errors);
             //validation for confirm password  
            Validators::validateConfirmPassword($confirm_password, $password, $errors);
            if(!empty($errors)){//if there are validation errors, store them in the session and redirect back to the signup form
                $_SESSION['errors'] = $errors; // Store the errors in the session to display them on the form
                $_SESSION['old'] = ['firstname' => $firstname, 'lastname' => $lastname, 'email' => $email ]; // Store the old input values in the session to repopulate the form
                header('Location: ' . Config::get('baseProjectFolder') . '/signup'); // Redirect back to the signup form if there are validation errors
                exit();
            }
            return [
                'firstname' => $firstname,
                'lastname' => $lastname,
                'email' => $email,
                'password' => $password
            ];
    }
}