<?php
declare(strict_types=1);
namespace App;

 // Use the Config class from the App namespace
// reusable validation functions for string length and email format
Class Utils{
    public static function isLongerThan(string $str, int $length): bool {
        return strlen($str) > $length;
    }

    // Helper function to check if a string is a valid email address
    public static function isEmail(string $str): bool {
        return filter_var($str, FILTER_VALIDATE_EMAIL) !== false;
    }

    // Helper function to check if a string is shorter than a specified length
    public static function isShorterThan(string $str, int $length): bool {
        return strlen($str) < $length;
    }

    // Helper function to sanitize user input to prevent XSS attacks
    public static function sanitizeInput(string $str): string {
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    }

    // Helper function to check if the user is authenticated, and if not, redirect to the login page
    public static function requireAuth():void{
            if (!isset($_SESSION['user'])) {
            header('Location: ' . Config::get('baseProjectFolder') . '/login');
            exit();
        }
    }

    // Helper to get old input value with fallback
    public static function old(array $old, string $key, string $fallback = ''): string {
        return htmlspecialchars((string)($old[$key] ?? $fallback), ENT_QUOTES, 'UTF-8');
    }

    // Helper to display field error
    public static function fieldError(array $errors, string $key, ?int $index = null, ?string $subKey = null): string {
        if ($index !== null && $subKey !== null) {
            // e.g. $errors['items'][0]['quantity'][0]
            $message = $errors[$key][$index][$subKey][0] ?? null;
        } elseif ($subKey !== null) {
            // e.g. $errors['profile']['last_name'][0]
            $message = $errors[$key][$subKey][0] ?? null;
        } else {
            // e.g. $errors['last_name'][0]
            $message = $errors[$key][0] ?? null;
        }

        if ($message) {
            return '<span class="field-error">' . htmlspecialchars($message) . '</span>';
        }
        return '';
    }
}
