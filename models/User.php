<?php
 // Include the database connection handler to enable database interactions
require "./models/Model.php";
class User extends Model{

    protected string $tableName = 'users';

    public  $id;
    public $name;
    public $email;
    public $password;
    public $created_at;
    public $updated_at;

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

    
    
}
