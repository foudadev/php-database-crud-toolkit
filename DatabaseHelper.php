<?php

// Database connection class using Singleton pattern
class Database
{
    private static $instance = null;
    private $conn;

    // Private constructor to prevent multiple instantiations
    private function __construct()
    {
        $servername = "localhost";
        $username = "username";
        $password = "password";
        $dbname = "database_name";

        // Create mysqli connection
        $this->conn = new mysqli($servername, $username, $password, $dbname);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    // Get the single instance of the connection
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    // Get the connection object
    public function getConnection()
    {
        return $this->conn;
    }
}

// Helper function for binding parameters and executing queries
function executeQuery($query, $params = [], $returnResults = false)
{
    $conn = Database::getInstance()->getConnection();
    $stmt = $conn->prepare($query);

    if ($stmt === false) {
        throw new Exception("Error preparing statement: " . $conn->error);
    }

    if (!empty($params)) {
        $types = str_repeat('s', count($params)); // Adjust type as needed
        $stmt->bind_param($types, ...$params);
    }

    if (!$stmt->execute()) {
        throw new Exception("Error executing query: " . $stmt->error);
    }

    if ($returnResults) {
        return $stmt->get_result();
    }

    return $stmt->affected_rows > 0;
}

// Select records from a table
function select(string $table, array $columns, string $condition = null, string $orderBy = null, int $limit = null)
{
    $columns = implode(", ", $columns);
    $query = "SELECT $columns FROM `$table`";
    $params = [];

    if ($condition) {
        $query .= " WHERE $condition";
    }
    if ($orderBy) {
        $query .= " ORDER BY $orderBy";
    }
    if ($limit) {
        $query .= " LIMIT ?";
        $params[] = $limit;
    }

    $result = executeQuery($query, $params, true);
    return $result->fetch_all(MYSQLI_ASSOC) ?: [];
}

// Example usage of select function
// $users = select("users", ["id", "name", "email"], "status = 1", "created_at DESC", 10);
// print_r($users);

// Select all records from a table
function selectAll(string $table, string $orderBy = null, int $limit = null)
{
    return select($table, ['*'], null, $orderBy, $limit);
}

// Example usage of selectAll function
// $allUsers = selectAll("users", "id ASC", 50);
// print_r($allUsers);

// Find a record or throw an error if not found
function findOrFail(string $table, string $condition)
{
    $query = "SELECT 1 FROM `$table` WHERE $condition LIMIT 1";
    $result = executeQuery($query, [], true);

    if ($result->num_rows === 0) {
        throw new Exception("Element not found");
    }

    return true;
}

// Example usage of findOrFail function
// try {
//     findOrFail("users", "id = 5");
//     echo "User found!";
// } catch (Exception $e) {
//     echo $e->getMessage();
// }

// Insert new record into a table
function insert(string $table, array $data)
{
    $columns = implode(", ", array_keys($data));
    $placeholders = implode(", ", array_fill(0, count($data), '?'));
    $values = array_values($data);

    $query = "INSERT INTO `$table` ($columns) VALUES ($placeholders)";
    return executeQuery($query, $values);
}

// Example usage of insert function
// $newUser = insert("users", ["name" => "John Doe", "email" => "john@example.com", "status" => 1]);
// echo $newUser ? "User inserted successfully!" : "Failed to insert user.";

// Update an existing record
function update(string $table, array $data, string $condition)
{
    findOrFail($table, $condition);

    $columns = implode(" = ?, ", array_keys($data)) . " = ?";
    $values = array_values($data);

    $query = "UPDATE `$table` SET $columns WHERE $condition";
    return executeQuery($query, $values);
}

// Example usage of update function
// $updateUser = update("users", ["email" => "newemail@example.com", "status" => 1], "id = 5");
// echo $updateUser ? "User updated successfully!" : "Failed to update user.";

// Delete a record from a table
function delete(string $table, string $condition)
{
    findOrFail($table, $condition);

    $query = "DELETE FROM `$table` WHERE $condition";
    return executeQuery($query);
}

// Example usage of delete function
// $deleteUser = delete("users", "id = 5");
// echo $deleteUser ? "User deleted successfully!" : "Failed to delete user.";

?>
