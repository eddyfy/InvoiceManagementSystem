<?php
declare(strict_types=1);
require_once __DIR__ . '/autoloader.php'; // Ensure the autoloader is included to load classes automatically

// reusable validation functions for string length and email format
function isLongerThan($str, $length): bool {
    return strlen($str) > $length;
}

function isEmail($str): bool {
    return filter_var($str, FILTER_VALIDATE_EMAIL) !== false;
}

function isShorterThan($str, $length): bool {
    return strlen($str) < $length;
}

function sanitizeInput($str): string {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function requireAuth():void{
        if (!isset($_SESSION['user'])) {
        header('Location: ' . Config::get('baseProjectFolder') . '/login');
        exit();
    }
}