<?php 
declare(strict_types=1);
namespace App\Requests;
use App\Config;
use App\Utils;
class ValidateChangePassword{
    public static function validate(object $user): array{

        $errors = [];
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validation for new password
        if (empty($currentPassword)) {
            $errors["current_password"][] = "Current password is required.";
            $_SESSION['errors'] = $errors;
            $_SESSION['message'] = "Current password is required.";
            header('Location: ' . Config::get('baseProjectFolder') . '/dashboard?section=profile');
            exit();
        } else if (!password_verify($currentPassword, $user->password)) {
            $errors['profile']["current_password"][] = "Current password is incorrect.";
            $_SESSION['errors'] = $errors;
            $_SESSION['message'] = "Current password is incorrect.";
            header('Location: ' . Config::get('baseProjectFolder') . '/dashboard?section=profile');
            exit();
        }else{
            if (empty($newPassword)) {
                $errors['profile']["new_password"][] = "New password is required.";
            } else if (Utils::isShorterThan($newPassword, 6)) {
                $errors['profile']["new_password"][] = "New password must be at least 6 characters.";
            } else if (Utils::isLongerThan($newPassword, 255)) {
                $errors['profile']["new_password"][] = "New password must be less than 255 characters.";
            } else if ($newPassword === $currentPassword) {
                $errors['profile']["new_password"][] = "New password must be different from the current password.";
            }
        }

        if(empty($confirmPassword)){
            $errors['profile']["confirm_password"][] = "Confirm Password is required.";
        }else if($confirmPassword !== $newPassword){
            $errors['profile']["confirm_password"][] = "Passwords do not match.";        
        }

        if(!empty($errors)){
            $_SESSION['errors'] = $errors;
            header('Location: ' . Config::get('baseProjectFolder') . '/dashboard?section=profile');
            exit();
        } 

        return [
            'new_password' => $newPassword
        ];
    }

}