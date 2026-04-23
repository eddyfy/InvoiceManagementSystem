<?php 
declare(strict_types=1);
require_once './Config.php'; // Include the configuration file to load environment variables
require_once './utils.php';
require_once './DBH.php'; // Include the database connection handler
require_once './autoloader.php';

class AuthController{
    public function showLoginForm(): void {
        require './views/login_form.php'; // Include the login form view to display it to the user
    }
    public function handleLogin(): void {
    
        $errors = []; // Initialize an array to store validation errors
        if(isset($_POST['csrf_token'], $_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])){ // Check if the CSRF token from the form matches the one stored in the session
            unset($_SESSION['csrf_token']); // Unset the CSRF token from the session to prevent reuse
            $email = strtolower(trim($_POST['email'] ?? ''));
            $password = trim($_POST['password'] ?? '');

            //validation for email
            if (empty($email)) {
                $errors["email"][] = "Email is required.";
            }else{
                if (isLongerThan($email, 150)) {
                    $errors["email"][] = "Email must be less than 150 characters.";
                }
                if (!isEmail($email)) {
                    $errors["email"][] = "Invalid email format.";
                }   
            }
    
             //validation for password  
            if(empty($password)){
                $errors["password"][] = "Password is required.";
            }else if(isShorterThan($password, 6)){
                $errors["password"][] = "Password must be at least 6 characters.";
            }else if(isLongerThan($password, 255)){
                $errors["password"][] = "Password must be less than 255 characters.";
            }
            

            if(!empty($errors)){//if there are validation errors, store them in the session and redirect back to the login form
                $_SESSION['errors'] = $errors; // Store the errors in the session to display them on the form
                $_SESSION['old'] = ['email' => $email]; // Store the old input values in the session to repopulate the form
                header('Location: ' . Config::get('baseProjectFolder') . '/login'); // Redirect back to the login form if there are validation errors
                exit();
            }else{//no errors
                $userModel = new User();
                $user = $userModel->findByEmail($email);

                if (!$user || !password_verify($password, $user->password)) { // Check if the user exists and if the password is correct
                    $_SESSION['errors'] = ['email' => ['Invalid email or password.'], 'password' => ['Invalid email or password.']]; // Store the error message in the session to display it on the form
                    $_SESSION['old'] = ['email' => $email];
                    $_SESSION['message'] = "Login failed! Please check your credentials and try again."; // Store an error message in the session to display it on the login form
                    header('Location: ' . Config::get('baseProjectFolder') . '/login');
                    exit();
                }else{
                    session_regenerate_id(true);
                    $_SESSION['user'] = [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email
                    ];
                    $_SESSION['message'] = "Login successful! Welcome back, {$user->name}."; // Store a success message in the session to display it on the invoice form page
                    header('Location: ' . Config::get('baseProjectFolder') . '/dashboard'); // Redirect to the invoice form if login is successful
                    exit();
                }

            }
    
        }else{
            echo "Invalid CSRF token. Please try again.";
            exit();
        }
        
        exit();
    }

    public function logout(): void {
        session_destroy(); // Destroy the session to log the user out
        header('Location: ' . Config::get('baseProjectFolder') . '/dashboard'); // Redirect to the home page after logging out
        exit();
    }
    public function showSignupForm(): void {
   
        require './views/signup_form.php'; // Include the signup form view to display it to the user
    }
    public function handleSignup(): void {
       
        $errors = [];
        // Validate input
        if(isset($_POST['csrf_token'], $_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])){ // Check if the CSRF token from the form matches the one stored in the session
            unset($_SESSION['csrf_token']); // Unset the CSRF token from the session to prevent reuse
            $name = sanitizeInput(trim($_POST['name'] ?? ''));
            $email = strtolower(trim($_POST['email'] ?? ''));
            $password = trim($_POST['password'] ?? '');
            $confirm_password = trim($_POST['confirm_password'] ?? '');

             //validation for name
             if (empty($name)) {
                $errors["name"][] = "Name is required.";
            }else{
                if (isLongerThan($name, 100)) {
                    $errors["name"][] = "Name must be less than 100 characters.";
                }
            }

             //validationfor email
             if (empty($email)) {
                $errors["email"][] = "Email is required.";
            }else{ 
                if (isLongerThan($email, 150)) {
                    $errors["email"][] = "Email must be less than 150 characters.";
                }
                if (!isEmail($email)) {
                    $errors["email"][] = "Invalid email format.";
                }   
            }
    
             //validation for password  
            if(empty($password)){
                $errors["password"][] = "Password is required.";
            }else if(isShorterThan($password, 6)){
                $errors["password"][] = "Password must be at least 6 characters.";
            }else if(isLongerThan($password, 255)){
                $errors["password"][] = "Password must be less than 255 characters.";
            }

             //validation for confirm password  
             if(empty($confirm_password)){
                $errors["confirm_password"][] = "Confirm Password is required.";
            }else if($confirm_password !== $password){
                $errors["confirm_password"][] = "Passwords do not match.";
            }

            if(!empty($errors)){//if there are validation errors, store them in the session and redirect back to the signup form
                $_SESSION['errors'] = $errors; // Store the errors in the session to display them on the form
                $_SESSION['old'] = ['name' => $name, 'email' => $email ]; // Store the old input values in the session to repopulate the form
                header('Location: ' . Config::get('baseProjectFolder') . '/signup'); // Redirect back to the signup form if there are validation errors
                exit();
            }else{//no errors

                $userModel = new User();
                try{
                     $user = $userModel->create([
                        'name' => $name,
                        'email' => $email,
                        'password' => password_hash($password, PASSWORD_DEFAULT)
                    ]);

                }catch (RuntimeException $e) {
                    if ($e->getMessage() === 'duplicate') {
                        $_SESSION['errors'] = ['email' => ['Email already exists. Please use a different email.']]; // Store the error message in the session to display it on the form 
                        $_SESSION['old'] = ['name' => $name, 'email' => $email];
                        header('Location: ' . Config::get('baseProjectFolder') . '/signup');
                        exit();
                    }
                }
                $_SESSION['message'] = "Signup successful! Please log in."; // Store a success message in the session to display it on the login page
                header('Location: ' . Config::get('baseProjectFolder') . '/login'); // Redirect to the login page after successful signup
                exit();
            }
             

        }else{
            echo "Invalid CSRF token. Please try again.";
            exit();
        }
    }

}