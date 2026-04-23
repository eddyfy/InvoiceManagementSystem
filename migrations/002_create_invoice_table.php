<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once '../DBH.php'; // Include the database connection handler

$stmt = "CREATE TABLE IF NOT EXISTS invoices (
    id INT AUTO_INCREMENT PRIMARY KEY,

    user_id INT NOT NULL, 
    
    invoice_number VARCHAR(50) UNIQUE,
    
    customer_name VARCHAR(150) NOT NULL,
    customer_email VARCHAR(150),
    
    total DECIMAL(10,2) NOT NULL,
    
    status ENUM('draft', 'sent', 'paid') DEFAULT 'draft',
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);";

try{
    DBH::getConnection()->exec($stmt); // Execute the SQL statement to create the invoices table
    echo "Invoices table created successfully.";
} catch (PDOException $e) {
    die("Error creating invoices table: " . $e->getMessage()); // Handle any errors that occur during table creation
}