<?php
$host = 'localhost';  // Default sa XAMPP
$dbname = 'spcc_database';  // Pangalan ng database mo
$username = 'root';  // Default user sa XAMPP
$password = '';  // Walang password ang root sa XAMPP

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
