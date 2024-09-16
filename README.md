# Installation Steps 

1 . Configure the Database
```
$servername = "your_server_name";
$username = "your_username";
$password = "your_password";
$dbname = "your_database_name";
```
2. Include the Helper Functions
```
// Include the helper functions in your PHP project
  require_once 'path-to-project/DatabaseHelper.php';
```
# Examples
```
// Fetch user records with specific columns and conditions
$users = select("users", ["id", "name", "email"], "status = 1", "created_at DESC", 10);
print_r($users);
```
```
// Fetch all user records with ordering and limit
$allUsers = selectAll("users", "created_at DESC", 10);
print_r($allUsers);
```
```
// Insert a new user record
$success = insert("users", ["name" => "John Doe", "email" => "john@example.com", "status" => 1]);
echo $success ? "User inserted successfully!" : "Failed to insert user.";
```
```
// Update an existing user record
$success = update("users", ["email" => "newemail@example.com", "status" => 1], "id = 5");
echo $success ? "User updated successfully!" : "Failed to update user.";
```
```
// Delete a user record
$success = delete("users", "id = 5");
echo $success ? "User deleted successfully!" : "Failed to delete user.";
```
```
// Check if a user with a specific ID exists
$exists = findOrFail("users", "id = 5");
echo $exists ? "Record found!" : "Record not found.";
```
# php-database-crud-toolkit
This repository contains a set of reusable PHP helper functions for interacting with a MySQL database. The functions provide an easy and efficient way to perform common database operations such as SELECT, INSERT, UPDATE, and DELETE without the need to write repetitive SQL queries.
