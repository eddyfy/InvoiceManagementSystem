<?php
namespace App\Controllers;
use App\Config;
use App\Models\User;
use App\Requests\ValidatePersonalInfo;
use App\Requests\ValidateChangePassword;
use App\Requests\ValidateBusinessDetails;
use App\RateLimiter;

use PDOException;

class UserController{

    function updatePersonalInfo(){   
        $key = "update_profile_" . ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
        $rateLimiter = new RateLimiter($key, 5, 60); 
        if (!$rateLimiter->attempt()) { 
            $seconds = $rateLimiter->remainingSeconds(); 
            $_SESSION['message'] = "Too many attempts. Try again in {$seconds} seconds.";
            header('Location: ' . Config::get('baseProjectFolder') . '/dashboard?section=profile');
            exit();
        }
        
        $validated = ValidatePersonalInfo::validate();
        $userModel = new User();
        try {
            $userModel->pdo->beginTransaction();
        
            $updatedUser = $userModel->update($validated['user_id'], [
                'firstname' => $validated['firstname'],
                'lastname' => $validated['lastname'],
                'email' => $validated['email']
            ]);
            $_SESSION['user']['firstname'] = $updatedUser->firstname;
            $_SESSION['user']['lastname'] = $updatedUser->lastname;
            $_SESSION['user']['email'] = $updatedUser->email;
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

        $key = 'change_password_' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
        $rateLimiter = new RateLimiter($key, 3, 300); // Create a new instance of the RateLimiter class with a maximum of 5 attempts and a decay time of 60 seconds

        if (!$rateLimiter->attempt()) { // Check if the user has exceeded the maximum number of login attempts
            $seconds = $rateLimiter->remainingSeconds(); // Get the remaining seconds until the user can attempt to log in again
            $_SESSION['message'] = "Too many attempts. Try again in {$seconds} seconds.";
            header('Location: ' . Config::get('baseProjectFolder') . '/dashboard?section=profile');
            exit();
        }
        
        $userModel = new User();
        $user = $userModel->findByEmail($_SESSION['user']['email']) ?? die("User not found.");
        $userId = $_SESSION['user']['id'];

        $validated = ValidateChangePassword::validate($user);

        try {
            $userModel->pdo->beginTransaction();
            $userModel->update($userId, [
                'password' => password_hash($validated['new_password'], PASSWORD_DEFAULT)
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

    function saveBusinessDetails(){
        $userModel = new User();
        $userId = $_SESSION['user']['id'];
        $validated = ValidateBusinessDetails::validate();
        try {
            $userModel->pdo->beginTransaction();
            $updatedUser = $userModel->update($userId, [
                'business_name' => $validated['business_name'],
                'business_address' => $validated['business_address'],
                'business_phone' => $validated['business_phone'],
                'business_email' => $validated['business_email'],
                'bank_account_number' => $validated['bank_account_number'],
                'bank_account_name' => $validated['bank_account_name'],
                'bank_name' => $validated['bank_name'],
            ]);
            $_SESSION['user']['business_name'] = $updatedUser->business_name;
            $_SESSION['user']['business_address'] = $updatedUser->business_address;
            $_SESSION['user']['business_phone'] = $updatedUser->business_phone;
            $_SESSION['user']['business_email'] = $updatedUser->business_email;
            $_SESSION['user']['bank_account_name'] = $updatedUser->bank_account_name;
            $_SESSION['user']['bank_account_number'] = $updatedUser->bank_account_number;
            $_SESSION['user']['bank_name'] = $updatedUser->bank_name;
            $_SESSION['user']['has_business_details'] = $updatedUser->has_business_details;
            $userModel->pdo->commit();
            $_SESSION['message'] = "Business details added successfully.";
            header('Location: ' . Config::get('baseProjectFolder') . '/dashboard');
            exit();
        } catch (PDOException $e) {
            $userModel->pdo->rollback();
            error_log("Database error: " . $e->getMessage());
            echo "An error occurred while saving business details.";
        }
    }
    

}