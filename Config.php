<?php
declare(strict_types=1);

function loadEnv(string $filePath): void
{
    if (!file_exists($filePath)) {
        // Silently fail or log - don't echo in config file during production
        error_log("Environment file not found: " . $filePath);
        return;
    }

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        $line = trim($line);

        // Skip empty lines and comments
        if (empty($line) || strpos($line, '#') === 0) {
            continue;
        }

        // Split only on first = sign
        $parts = explode('=', $line, 2);
        if (count($parts) !== 2) {
            continue;
        }

        $key   = trim($parts[0]);
        $value = trim($parts[1]);

        // Remove surrounding quotes if present
        $value = trim($value, "\"'");

        $_ENV[$key] = $value;
        putenv("$key=$value");
    }
}

// Load environment variables
loadEnv(__DIR__ . '/.env');

class Config
{
    /**
     * Get config value
     */
    public static function get(string $key, $default = null)
    {
        // Better way: use a switch or property array instead of dynamic access
        switch ($key) {
            case 'username':
                return getenv('DB_USER') ?: '';
            case 'password':
                return getenv('DB_PASS') ?: '';
            case 'host':
                return getenv('DB_HOST') ?: 'localhost';
            case 'dbname':
                return getenv('DB_NAME') ?: '';
            case 'baseProjectFolder':
                return getenv('APP_ENV') === 'dev' ? '/invoicemanager' : '/';
            default:
                return $default;
        }
    }
}

// Create global instance
// $config = new Config();