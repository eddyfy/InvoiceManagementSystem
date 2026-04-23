<?php
class InvoiceItem{
    protected string $tableName = 'invoice_items';
    public $id;
    public $invoice_id;
    public $description;
    public $quantity;
    public $price;
    public $subtotal;   

}