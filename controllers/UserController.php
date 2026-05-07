<?php
class UserController{
    function updateProfile(){
        if(isset($_POST['csrf_token'], $_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])){
            $userModel = new User();
            $userId = $_SESSION['user']['id'];
            $firstname = ucfirst(sanitizeInput(trim($_POST['firstname'] ?? '')));
            $lastname = ucfirst(sanitizeInput(trim($_POST['lastname'] ?? '')));
            $email = strtolower(trim($_POST['email'] ?? ''));

            // validation for firstname
            if (empty($firstname)) {
                $errors["firstname"][] = "First name is required.";
            } else {
                if (isLongerThan($firstname, 100)) {
                    $errors["firstname"][] = "First name must be less than 100 characters.";
                }
            }
            // validation for lastname
            if (empty($lastname)) { 
                $errors["lastname"][] = "Last name is required.";
            } else {
                if (isLongerThan($lastname, 100)) {
                    $errors["lastname"][] = "Last name must be less than 100 characters.";
                }
            }
            // validation for email
            if (empty($email)) {
                $errors["email"][] = "Email is required.";
            } else {
                if (isLongerThan($email, 150)) {
                    $errors["email"][] = "Email must be less than 150 characters.";
                }
                if (!isEmail($email)) {
                    $errors["email"][] = "Invalid email format.";
                }
            }
            if(!empty($errors)){
                $_SESSION['errors'] = $errors;
                $_SESSION['old'] = ['firstname' => $firstname, 'lastname' => $lastname, 'email' => $email];
                header('Location: ' . Config::get('baseProjectFolder') . '/dashboard');
                exit();
            } else {
                try {
                    $userModel->pdo->beginTransaction();
                    $stmt = $userModel->pdo->prepare("UPDATE users SET firstname = :firstname, lastname = :lastname, email = :email WHERE id = :id");
                    $stmt->execute([
                        ':firstname' => $firstname,
                        ':lastname' => $lastname,
                        ':email' => $email,
                        ':id' => $userId
                    ]);
                    $_SESSION['user']['firstname'] = $firstname;
                    $_SESSION['user']['lastname'] = $lastname;
                    $_SESSION['user']['email'] = $email;
                    $userModel->pdo->commit();
                    $_SESSION['message'] = "Profile updated successfully.";
                    header('Location: ' . Config::get('baseProjectFolder') . '/dashboard');
                    exit();

                } catch (PDOException $e) {
                    $userModel->pdo->rollback();
                    die("Database error: " . $e->getMessage());
                }
            }
        } else {
            echo "Invalid CSRF token.";
        }
    }

    function changePassword(){
            if(isset($_POST['csrf_token'], $_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])){
                // Implement password change logic here, including validation and updating the database
                $userModel = new User();
                $errors = [];
                $user = $userModel->findByEmail($_SESSION['user']['email']) ?? die("User not found.");
            
                $userId = $_SESSION['user']['id'];
                $currentPassword = $_POST['current_password'] ?? '';
        
                $newPassword = $_POST['new_password'] ?? '';
                $confirmPassword = $_POST['confirm_password'] ?? '';

                    // Validation for new password
                if (empty($currentPassword)) {
                    $errors["current_password"][] = "Current password is required.";
                    $_SESSION['errors'] = $errors;
                    $_SESSION['message'] = "Current password is required.";
                    header('Location: ' . Config::get('baseProjectFolder') . '/dashboard');
                    exit();
                } else if (!password_verify($currentPassword, $user->password)) {
                    $errors["current_password"][] = "Current password is incorrect.";
                    $_SESSION['errors'] = $errors;
                    $_SESSION['message'] = "Current password is incorrect.";
                    header('Location: ' . Config::get('baseProjectFolder') . '/dashboard');
                    exit();
                }else{
                    if (empty($newPassword)) {
                        $errors["new_password"][] = "New password is required.";
                    } else if (isShorterThan($newPassword, 6)) {
                        $errors["new_password"][] = "New password must be at least 6 characters.";
                    } else if (isLongerThan($newPassword, 255)) {
                        $errors["new_password"][] = "New password must be less than 255 characters.";
                    } else if ($newPassword === $currentPassword) {
                        $errors["new_password"][] = "New password must be different from the current password.";
                    }

                    if (empty($confirmPassword)) {
                        $errors["confirm_password"][] = "Please confirm your new password.";
                    } else if ($newPassword !== $confirmPassword) {
                        $errors["confirm_password"][] = "New password and confirmation do not match.";
                    }

                    if(!empty($errors)){
                        $_SESSION['errors'] = $errors;
                        // $_SESSION['message'] = "Please correct the errors and try again.";
                        header('Location: ' . Config::get('baseProjectFolder') . '/dashboard');
                        exit();
                    } else {
                        try {
                            $userModel->pdo->beginTransaction();
                            $stmt = $userModel->pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
                            $stmt->execute([
                                ':password' => password_hash($newPassword, PASSWORD_DEFAULT),
                                ':id' => $userId
                            ]);
                            $userModel->pdo->commit();
                            $_SESSION['message'] = "Password changed successfully.";
                            header('Location: ' . Config::get('baseProjectFolder') . '/dashboard');
                            exit();

                        } catch (PDOException $e) {
                            $userModel->pdo->rollback();
                            die("Database error: " . $e->getMessage());
                        }
                    }   
                }
    
            } else {
                echo "Invalid CSRF token.";
            }
    }
    function deleteAccount(){
        if(isset($_POST['csrf_token'], $_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])){
            $userModel = new User();
            $userId = $_SESSION['user']['id'];
            try {
                $userModel->pdo->beginTransaction();
                $stmt = $userModel->pdo->prepare("DELETE FROM users WHERE id = :id");
                $stmt->execute([':id' => $userId]);
                $userModel->pdo->commit();
                session_destroy();
                session_start();

                $_SESSION['message'] = "Account deleted successfully.";
                header('Location: ' . Config::get('baseProjectFolder') . '/login');
                exit();

            } catch (PDOException $e) {
                $userModel->pdo->rollback();
                die("Database error: " . $e->getMessage());
            }
        } else {
            echo "Invalid CSRF token.";
        }
    }

}