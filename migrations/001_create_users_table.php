<?php
declare(strict_types=1);
namespace App\Migrations;

require_once __DIR__ . '/../autoloader.php';

use App\DBH;
use PDOException;

$stmt = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(100) NOT NULL,
    lastname VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,

    business_name VARCHAR(255) DEFAULT NULL,
    business_address TEXT DEFAULT NULL,
    business_phone VARCHAR(20) DEFAULT NULL,
    business_email VARCHAR(150) DEFAULT NULL,

    bank_account_name VARCHAR(255) DEFAULT NULL,
    bank_account_number VARCHAR(10) DEFAULT NULL,
    bank_name VARCHAR(100) DEFAULT NULL,

    has_business_details BOOLEAN GENERATED ALWAYS AS (
        CASE 
            WHEN business_name IS NOT NULL AND TRIM(business_name) != ''
             AND bank_account_name IS NOT NULL AND TRIM(bank_account_name) != ''
             AND bank_account_number IS NOT NULL AND TRIM(bank_account_number) != ''
             AND bank_name IS NOT NULL AND TRIM(bank_name) != ''
            THEN TRUE 
            ELSE FALSE 
        END
    ) STORED,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);";

try {
    DBH::getConnection()->exec($stmt);
    echo "Users table created successfully.";
} catch (PDOException $e) {
    error_log("Error creating users table: " . $e->getMessage());
    echo "An error occurred while creating the users table.";
    exit();
}