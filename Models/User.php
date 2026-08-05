<?php
 // Include the database connection handler to enable database interactions
 namespace App\Models;
 use PDOException;
 use RuntimeException;
class User extends Model{

    protected string $tableName = 'users';

    public int $id;
    public string $firstname;
    public string $lastname;
    public string $email;
    public string $password;
    public string $created_at;
    public string $updated_at;

    public ?string $business_name = null;
    public ?string $business_address = null;
    public ?string $business_phone = null;
    public ?string $business_email = null;
    public ?string $bank_account_name = null;
    public ?string $bank_account_number = null;
    public ?string $bank_name = null;
    public bool $has_business_details = false;

    function findByEmail(string $email): ?object {
        $sql = "SELECT * FROM {$this->tableName} WHERE email = :email";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':email' => $email]);
            $data = $stmt->fetch();
            return $data ? $this->hydrate($data) : null; // Use the hydrate method to create and return a User object if data is found
        } catch (PDOException $e) {
            throw new RuntimeException("Database error: " . $e->getMessage());
        }
    }
    
    function deleteById(int $userId): bool {
        $sql = "DELETE FROM {$this->tableName} WHERE id = :id";
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([':id' => $userId]); // Execute the delete statement and return true if successful
        } catch (PDOException $e) {
            throw new RuntimeException("Database error: " . $e->getMessage());
        }
    }

 
    
}
