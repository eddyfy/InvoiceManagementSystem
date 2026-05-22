<?php
require_once './autoloader.php';
require_once './requests/validateUpdateProfile.php';
require_once './requests/validateChangePassword.php';
require_once './requests/validators.php';

class UserController{
    function updateProfile(){   
        
        $validated = validateUpdateProfile();
        $userModel = new User();
        try {
            $userModel->pdo->beginTransaction();
            $stmt = $userModel->pdo->prepare("UPDATE users SET firstname = :firstname, lastname = :lastname, email = :email WHERE id = :id");
            $stmt->execute([
                ':firstname' => $validated['firstname'],
                ':lastname' => $validated['lastname'],
                ':email' => $validated['email'],
                ':id' => $validated['user_id']
            ]);
            $_SESSION['user']['firstname'] = $validated['firstname'];
            $_SESSION['user']['lastname'] = $validated['lastname'];
            $_SESSION['user']['email'] = $validated['email'];
            $userModel->pdo->commit();
            $_SESSION['message'] = "Profile updated successfully.";
            header('Location: ' . Config::get('baseProjectFolder') . '/dashboard');
            exit();
        } catch (PDOException $e) {
            $userModel->pdo->rollback();
            error_log("Database error: " . $e->getMessage());
            echo "An error occurred while updating the profile.";
        }
    }

    function changePassword(){
        $userModel = new User();
        $user = $userModel->findByEmail($_SESSION['user']['email']) ?? die("User not found.");
        $userId = $_SESSION['user']['id'];

        $validated = validateChangePassword($user);

        try {
            $userModel->pdo->beginTransaction();
            $stmt = $userModel->pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
            $stmt->execute([
                ':password' => password_hash($validated['new_password'], PASSWORD_DEFAULT),
                ':id' => $userId
            ]);
            $userModel->pdo->commit();
            $_SESSION['message'] = "Password changed successfully.";
            header('Location: ' . Config::get('baseProjectFolder') . '/dashboard');
            exit();

        } catch (PDOException $e) {
            $userModel->pdo->rollback();
            error_log("Database error: " . $e->getMessage());
            echo "An error occurred while changing the password.";
        }
    }



    function deleteAccount(){

            $userModel = new User();
            $userId = $_SESSION['user']['id'];
            try {
                $userModel->pdo->beginTransaction();
                $userModel->deleteById($userId); // Delete the user account, which will also cascade delete related invoices due to foreign key constraints
                $userModel->pdo->commit();
                session_destroy();
                session_start();
                $_SESSION['message'] = "Account deleted successfully.";
                header('Location: ' . Config::get('baseProjectFolder') . '/login');
                exit();
            } catch (PDOException $e) {
                $userModel->pdo->rollback();
                error_log("Database error: " . $e->getMessage());
                echo "An error occurred while deleting the account.";
            }
    }
}