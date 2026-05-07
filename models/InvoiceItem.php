<?php
class InvoiceItem extends Model{
    protected string $tableName = 'invoice_items';
    public int $id;
    public int $invoice_id;
    public string $description;
    public int $quantity;
    public float $price;
    public float $subtotal;   

}