<?php
class Invoice extends Model{
    protected string $tableName = 'invoices';

    public int $id;
    public  int $user_id;    
    public string $invoice_number; 
    public string $invoice_date;
    public string $customer_name;
    public string $customer_email;
    public float $subtotal;
    public float $tax_rate;
    public float $tax_amount;
    public float $discount;
    public float $grand_total;
    public string $notes;
    public string $created_at;

    public function update(int $invoiceId, int $userId, array $data): bool {
        $stmt = $this->pdo->prepare("
            UPDATE invoices SET 
                invoice_number = :invoice_number,
                invoice_date = :invoice_date,
                customer_name = :customer_name,
                customer_email = :customer_email,
                subtotal = :subtotal,
                tax_rate = :tax_rate,
                tax_amount = :tax_amount,
                discount = :discount,
                grand_total = :grand_total,
                notes = :notes,
                status = :status
            WHERE id = :id AND user_id = :user_id
        ");
        return $stmt->execute([
            'invoice_number' => $data['invoice_number'],
            'invoice_date' => $data['invoice_date'],
            'customer_name' => $data['customer_name'],
            'customer_email' => $data['customer_email'],
            'subtotal' => $data['subtotal'],
            'tax_rate' => $data['tax_rate'],
            'tax_amount' => $data['tax_amount'],
            'discount' => $data['discount'],
            'grand_total' => $data['grand_total'],
            'notes' => $data['notes'],
            'status' => $data['status'],
            'id' => $invoiceId,
            'user_id' => $userId
        ]);
    }
    public function getStats(int $userId): array {
        $stmt = $this->pdo->prepare("
            SELECT 
                COUNT(*) as total_invoices,
                SUM(CASE WHEN status = 'paid' THEN grand_total ELSE 0 END) as revenue,
                SUM(CASE WHEN status != 'paid' THEN grand_total ELSE 0 END) as outstanding,
                COUNT(CASE WHEN status != 'paid' THEN 1 END) as unpaid_count
            FROM invoices 
            WHERE user_id = :user_id
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getRecentByUser(int $userId, int $limit = 5): array {
        $stmt = $this->pdo->prepare("
            SELECT id, invoice_number, customer_name, customer_email, invoice_date, grand_total, status
            FROM invoices 
            WHERE user_id = :user_id
            ORDER BY created_at DESC 
            LIMIT :limit
        ");
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllByUser(int $userId): array {
        $stmt = $this->pdo->prepare("
            SELECT id, invoice_number, customer_name, customer_email, invoice_date, grand_total, status 
            FROM invoices 
            WHERE user_id = :user_id
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getById(int $invoiceId, int $userId): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM invoices WHERE id = :id AND user_id = :user_id");
        $stmt->execute(['id' => $invoiceId, 'user_id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function deleteById(int $invoiceId, int $userId): bool {
        $stmt = $this->pdo->prepare("DELETE FROM invoices WHERE id = :id AND user_id = :user_id");
        return $stmt->execute(['id' => $invoiceId, 'user_id' => $userId]);
    }
}
