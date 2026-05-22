<?php
function validateSignup(): array{
          $errors = [];
        // Validate input
         // Check if the CSRF token from the form matches the one stored in the session
            unset($_SESSION['csrf_token']); // Unset the CSRF token from the session to prevent reuse
            $firstname = sanitizeInput(trim($_POST['firstname'] ?? ''));
            $lastname = sanitizeInput(trim($_POST['lastname'] ?? ''));
            $email = strtolower(trim($_POST['email'] ?? ''));
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

             //validation for firstname
            validateFirstName($firstname, $errors);

             //validation for lastname
            validateLastName($lastname, $errors);

             //validation for email
            validateEmail($email, $errors);
                
            
             //validation for password  
            validatePassword($password, $errors);
             //validation for confirm password  
            validateConfirmPassword($confirm_password, $password, $errors);
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