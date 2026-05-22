<?php
declare(strict_types=1);
require_once __DIR__ . '/autoloader.php'; // Ensure the autoloader is included to load classes automatically

// reusable validation functions for string length and email format
function isLongerThan(string $str, int $length): bool {
    return strlen($str) > $length;
}

// Helper function to check if a string is a valid email address
function isEmail(string $str): bool {
    return filter_var($str, FILTER_VALIDATE_EMAIL) !== false;
}

// Helper function to check if a string is shorter than a specified length
function isShorterThan(string $str, int $length): bool {
    return strlen($str) < $length;
}

// Helper function to sanitize user input to prevent XSS attacks
function sanitizeInput(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// Helper function to check if the user is authenticated, and if not, redirect to the login page
function requireAuth():void{
        if (!isset($_SESSION['user'])) {
        header('Location: ' . Config::get('baseProjectFolder') . '/login');
        exit();
    }
}

// Helper to get old input value with fallback
function old(array $old, string $key, string $fallback = ''): string {
    return htmlspecialchars((string)($old[$key] ?? $fallback), ENT_QUOTES, 'UTF-8');
}

// Helper to display field error
function fieldError(array $errors, string $key, ?int $index = null, ?string $subKey = null): string {
    if ($index !== null && $subKey !== null) {
        $message = $errors[$key][$index][$subKey][0] ?? null;
    } else {
        $message = $errors[$key][0] ?? null;
    }

    if ($message) {
        return '<span class="field-error">' . htmlspecialchars($message) . '</span>';
    }
    return '';
}
