<?php
namespace App\Controllers;
use App\Config;
use App\Models\User;
use App\Requests\ValidateUpdateProfile;
use App\Requests\ValidateChangePassword;
use App\Requests\ValidateBankDetails;

use PDOException;


class UserController{
    function updateProfile(){   
        
        $validated = ValidateUpdateProfile::validate();
        $userModel = new User();
        try {
            $userModel->pdo->beginTransaction();
            $stmt = $userModel->pdo->prepare("UPDATE users SET firstname = :firstname, lastname = :lastname, email = :email, bank_account_number = :bank_account_number, bank_account_name = :bank_account_name, bank_name = :bank_name WHERE id = :id");
            $stmt->execute([
                ':firstname' => $validated['firstname'],
                ':lastname' => $validated['lastname'],
                ':email' => $validated['email'],
                ':bank_account_number' => $validated['bank_account_number'],
                ':bank_account_name' => $validated['bank_account_name'],
                ':bank_name' => $validated['bank_name'],
                ':id' => $validated['user_id']
            ]);

            $_SESSION['user']['firstname'] = $validated['firstname'];
            $_SESSION['user']['lastname'] = $validated['lastname'];
            $_SESSION['user']['email'] = $validated['email'];
            $_SESSION['user']['bank_account_number'] = $validated['bank_account_number'];
            $_SESSION['user']['bank_account_name'] = $validated['bank_account_name'];
            $_SESSION['user']['bank_name'] = $validated['bank_name'];  
            $_SESSION['user']['has_bank_details'] = !empty($validated['bank_account_number']) && !empty($validated['bank_account_name']) && !empty($validated['bank_name']);
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

        $validated = ValidateChangePassword::validate($user);

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

    function saveBankDetails(){
        $userModel = new User();
        $userId = $_SESSION['user']['id'];
        ValidateBankDetails::validate();
        $validated = ValidateBankDetails::validate();
        try {
            $userModel->pdo->beginTransaction();
            $stmt = $userModel->pdo->prepare("UPDATE users SET bank_account_number = :bank_account_number, bank_account_name = :bank_account_name, bank_name = :bank_name WHERE id = :id");
            $stmt->execute([
                ':bank_account_number' => $validated['bank_account_number'],
                ':bank_account_name' => $validated['bank_account_name'],
                ':bank_name' => $validated['bank_name'],
                ':id' => $userId
            ]);
            $_SESSION['user']['bank_account_number'] = $validated['bank_account_number'];
            $_SESSION['user']['bank_account_name'] = $validated['bank_account_name'];
            $_SESSION['user']['bank_name'] = $validated['bank_name'];
            $_SESSION['user']['has_bank_details'] = true;
            $userModel->pdo->commit();
            $_SESSION['message'] = "Bank details added successfully.";
            header('Location: ' . Config::get('baseProjectFolder') . '/dashboard');
            exit();
        } catch (PDOException $e) {
            $userModel->pdo->rollback();
            error_log("Database error: " . $e->getMessage());
            echo "An error occurred while saving bank details.";
        }
    }
}