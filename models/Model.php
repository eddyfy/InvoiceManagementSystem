<?php
require_once './DBH.php'; // Include the database connection handler to enable database interactions
abstract class Model{

    public PDO $pdo;
    protected string $tableName;
    public function __construct(){
        $this->pdo = DBH::getConnection(); // Store the PDO connection from the database handler for use in database operations
    }
    
    public function create(array $data): object {
        $columns = array_keys($data);
        $placeholders = array_map(fn($col) => ':' . $col, $columns);

        $sql = "INSERT INTO {$this->tableName} (" . implode(',', $columns) . ") VALUES (" . implode(',', $placeholders) . ")";
       
        try{
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($data);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                throw new RuntimeException("duplicate");
            }
            throw new RuntimeException("Database error: " . $e->getMessage());
        }
    
        $id = (int) $this->pdo->lastInsertId();
        $data['id'] = $id; 
        
        return $this->hydrate($data); // Use the hydrate method to create and return an object with the data    

    } 

    function hydrate(array $data): object {
        $object = new static(); // Create a new instance of the calling class (e.g., User, Invoice)

        foreach ($data as $key => $value) {
            if (property_exists($object, $key)) {
                $object->$key = $value; // Set the property value if it exists in the object
            } else {
                echo "Property missing: $key\n"; // Output a message if the property is missing in the object
            }
        }

        return $object; // Return the hydrated object with properties set from the data array
    }

}
  
