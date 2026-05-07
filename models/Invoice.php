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
            SELECT invoice_number, customer_name, customer_email, invoice_date, grand_total, status
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
            SELECT invoice_number, customer_name, customer_email, invoice_date, grand_total, status
            FROM invoices 
            WHERE user_id = :user_id
            ORDER BY created_at DESC
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}