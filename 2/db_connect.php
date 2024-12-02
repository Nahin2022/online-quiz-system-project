<?php

$host = 'localhost';  // Database host
$db = 'test';         // Name of the database
$user = 'root';       // Database username (default for XAMPP is usually 'root')
$pass = '';           // Database password (empty by default in XAMPP)

try {
    // Create a new PDO instance and connect to the database
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    
    // Set the PDO error mode to exception to handle errors
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    
} catch (PDOException $e) {
    // If there is an error, catch it and display a message
    die("Error: Could not connect to the database. " . $e->getMessage());
}
?>
