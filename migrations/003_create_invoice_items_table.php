<?php
namespace App\Migrations;
use App\DBH;
use PDOException;
require_once  __DIR__ . '/../autoloader.php';
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
    error_log("Error creating invoice items table: " . $e->getMessage());
    echo "An error occurred while creating the invoice items table.";
    exit();
}