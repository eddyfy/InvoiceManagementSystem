<?php
declare(strict_types=1);
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../DBH.php'; // Include the database connection handler

$stmt = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(100) NOT NULL,
    lastname VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);";

try {
    DBH::getConnection()->exec($stmt); // Execute the SQL statement to create the users table
    echo "Users table created successfully.";
} catch (PDOException $e) {
    die("Error creating users table: " . $e->getMessage()); // Handle any errors that occur during table creation
}