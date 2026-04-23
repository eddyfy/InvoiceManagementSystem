<?php
class Invoice extends Model{
    protected string $tableName = 'invoices';

    public $id;
    public $customer_name;
    public $customer_email;
    public $date;
    public $total;
    public $status;
    public $created_at;
}