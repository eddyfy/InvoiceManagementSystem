<?php 
declare(strict_types=1);
// require_once './Config.php'; // Include the configuration file to load environment variables
// require_once './utils.php';
// require_once './DBH.php'; // Include the database connection handler
require_once './autoloader.php';
require_once './requests/validators.php';
require_once './requests/validateLogin.php';
require_once './requests/validateSignup.php';


class AuthController{

    public function showLoginForm(): void {
        $_SESSION['csrf_token'] = password_hash(bin2hex(random_bytes(32)), PASSWORD_DEFAULT);
        $errors = $_SESSION['errors'] ?? [];
        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['errors'], $_SESSION['old']); // Clear errors and old input after using them
        require './views/login_form.php'; // Include the login form view to display it to the user
    }
    public function handleLogin(): void {
        
        $validated = validateLogin();

        $userModel = new User();
        $user = $userModel->findByEmail($validated['email']);

        if (!$user || !password_verify($validated['password'], $user->password)) { // Check if the user exists and if the password is correct
            $_SESSION['errors'] = ['email' => ['Invalid email or password.'], 'password' => ['Invalid email or password.']]; // Store the error message in the session to display it on the form
            $_SESSION['old'] = ['email' => $validated['email']]; // Store the old input value in the session to repopulate the form
            $_SESSION['message'] = "Login failed! Please check your credentials and try again."; // Store an error message in the session to display it on the login form
            header('Location: ' . Config::get('baseProjectFolder') . '/login');
            exit();
        }else{
            session_regenerate_id(true);
            $_SESSION['user'] = [
                'id' => $user->id,
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'email' => $user->email,
                'has_bank_details' => $user->has_bank_details,
                'bank_account_name' => $user->bank_account_name,
                'bank_account_number' => $user->bank_account_number,
                'bank_name' => $user->bank_name
            ];
            $_SESSION['message'] = "Login successful! Welcome back, {$user->firstname}."; // Store a success message in the session to display it on the invoice form page
            if(isset($_SESSION['previous_page']) && $_SESSION['previous_page'] === "invoice_form"){
                unset($_SESSION['previous_page']);
                header('Location: ' . Config::get('baseProjectFolder') . '/invoice'); // Redirect to the invoice form if login is successful
                exit();
            }
            header('Location: ' . Config::get('baseProjectFolder') . '/dashboard'); // Redirect to the invoice form if login is successful
            exit();
        }
    }

    public function logout(): void {
        $_SESSION = [];
        session_destroy(); // Destroy the session to log the user out
        session_start();
        $_SESSION['message'] = 'You have been logged out.'; // store a logout message in the session  to display on the home page when user logs out
        header('Location: ' . Config::get('baseProjectFolder') . '/'); // Redirect to the home page after logging out
        exit();
    }
    public function showSignupForm(): void {
        $_SESSION['csrf_token'] = password_hash(bin2hex(random_bytes(32)), PASSWORD_DEFAULT);
        $errors = $_SESSION['errors'] ?? [];  
        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['errors'], $_SESSION['old']); // Clear errors and old input after using
        require './views/signup_form.php'; // Include the signup form view to display it to the user
    }
    public function handleSignup(): void { 

        $validated = validateSignup();
        $userModel = new User();
        try{
                $user = $userModel->create([
                'firstname' => $validated['firstname'],
                'lastname' => $validated['lastname'],
                'email' => $validated['email'],
                'password' => password_hash($validated['password'], PASSWORD_DEFAULT)
            ]);

        }catch (RuntimeException $e) {
            if ($e->getMessage() === 'duplicate') {
                $_SESSION['errors'] = ['email' => ['Email already exists. Please use a different email.']]; // Store the error message in the session to display it on the form 
                $_SESSION['old'] = ['firstname' => $validated['firstname'], 'lastname' => $validated['lastname'], 'email' => $validated['email']];
                header('Location: ' . Config::get('baseProjectFolder') . '/signup');
                exit();
            }
            $_SESSION['message'] = "An unexpected error occurred. Please try again later."; // Store a generic error message in the session to display it on the signup form
            header('Location: ' . Config::get('baseProjectFolder') . '/signup');
            exit();
        }

        $_SESSION['message'] = "Signup successful! Please log in."; // Store a success message in the session to display it on the login page
        header('Location: ' . Config::get('baseProjectFolder') . '/login'); // Redirect to the login page after successful signup
        exit();
            
    }
}