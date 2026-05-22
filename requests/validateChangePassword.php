<?php 
function validateChangePassword(object $user): array{

    $errors = [];
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
    }
    validateConfirmPassword($confirmPassword, $newPassword, $errors);
    if(!empty($errors)){
        $_SESSION['errors'] = $errors;
        header('Location: ' . Config::get('baseProjectFolder') . '/dashboard');
        exit();
    } 

    return [
        'new_password' => $newPassword
    ];
}