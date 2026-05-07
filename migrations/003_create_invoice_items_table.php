<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once '../DBH.php'; // Include the database connection handler
$stmt = "CREATE TABLE IF NOT EXISTS invoice_items (
    id INT AUTO_INCREMENT PRIMARY KEY,

    invoice_id INT NOT NULL,

    description VARCHAR(255) NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,

    subtotal DECIMAL(10,2) GENERATED ALWAYS AS (quantity * price) STORED,

    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE
);";
try{
    DBH::getConnection()->exec($stmt); // Execute the SQL statement to create the invoice_items table
    echo "Invoice items table created successfully.";
} catch (PDOException $e) {
    die("Error creating invoice items table: " . $e->getMessage()); // Handle any errors that occur during table creation
}