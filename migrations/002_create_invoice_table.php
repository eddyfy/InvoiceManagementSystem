<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once  __DIR__ . '/../autoloader.php';

$stmt = "CREATE TABLE IF NOT EXISTS invoices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,

    invoice_number VARCHAR(50) NOT NULL,
    invoice_date DATE NOT NULL,

    customer_name VARCHAR(150) NOT NULL,
    customer_email VARCHAR(150),

    subtotal DECIMAL(10,2) NOT NULL,
    tax_rate DECIMAL(5,2) DEFAULT 0,
    tax_amount DECIMAL(10,2) DEFAULT 0,
    discount DECIMAL(10,2) DEFAULT 0,
    grand_total DECIMAL(10,2) NOT NULL,

    notes TEXT,
    status ENUM('draft', 'sent', 'paid', 'overdue') DEFAULT 'draft',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_invoice_per_user (user_id, invoice_number)
);";

try{
    DBH::getConnection()->exec($stmt); // Execute the SQL statement to create the invoices table
    echo "Invoices table created successfully.";
} catch (PDOException $e) {
    error_log("Error creating invoices table: " . $e->getMessage());
    echo "An error occurred while creating the invoices table.";
    exit();
}