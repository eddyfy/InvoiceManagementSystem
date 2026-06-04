<?php
declare(strict_types=1);
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once  __DIR__ . '/../autoloader.php';

$stmt = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(100) NOT NULL,
    lastname VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,

    bank_account_name VARCHAR(255) DEFAULT NULL,
    bank_account_number VARCHAR(10) DEFAULT NULL,
    bank_name VARCHAR(100) DEFAULT NULL,
    has_bank_details BOOLEAN GENERATED ALWAYS AS (
        CASE 
            WHEN bank_account_name   IS NOT NULL AND TRIM(bank_account_name)   != '' 
             AND bank_account_number IS NOT NULL AND TRIM(bank_account_number) != '' 
             AND bank_name           IS NOT NULL AND TRIM(bank_name)           != '' 
            THEN TRUE 
            ELSE FALSE 
        END
    ) STORED,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);";

try {
    DBH::getConnection()->exec($stmt); // Execute the SQL statement to create the users table
    echo "Users table created successfully.";
} catch (PDOException $e) {
    error_log("Error creating users table: " . $e->getMessage()); 
    echo "An error occurred while creating the users table.";
    exit();
}