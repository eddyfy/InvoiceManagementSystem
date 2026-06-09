<?php
namespace App\Models;
use PDO;
class InvoiceItem extends Model{
    protected string $tableName = 'invoice_items';
    public int $id;
    public int $invoice_id;
    public string $description;
    public int $quantity;
    public float $price;
    public float $subtotal;   

    public function getByInvoiceId(int $invoiceId): array {
        $stmt = $this->pdo->prepare("SELECT * FROM invoice_items WHERE invoice_id = :invoice_id");
        $stmt->execute(['invoice_id' => $invoiceId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteByInvoiceId(int $invoiceId): bool {
        $stmt = $this->pdo->prepare("DELETE FROM invoice_items WHERE invoice_id = :invoice_id");
        return $stmt->execute(['invoice_id' => $invoiceId]);

    }

}