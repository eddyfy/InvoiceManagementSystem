<?php 
 // Include the configuration file to load environment variables

require 'autoloader.php'; // Include the autoloader to automatically load class files when they are instantiated

class DBH {
    private static ?PDO $pdo = null; // store the connection

    public static function getConnection(): PDO {
        if (self::$pdo === null) { // only create a new connection if one doesn't exist
            $username = Config::get("username");
            $password = Config::get("password");
            $host = Config::get("host");
            $dbname = Config::get("dbname");

            try {
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ];
                self::$pdo = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8mb4", $username, $password, $options);
            } catch (PDOException $e) {
                error_log("Database connection error: " . $e->getMessage());
                echo "Database connection failed. Please try again later.";
                exit();
            }
        }
        return self::$pdo;
    }
}
