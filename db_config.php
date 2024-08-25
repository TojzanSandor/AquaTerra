<?php
$host = 'localhost';
$dbname = 'at'; 
$username = 'at'; 
$password = 'VxcKnLGUSlR4ZO0'; 
$dsn = 'mysql:host=localhost;dbname=at;charset=utf8mb4'; 
$db_username = 'at'; 
$db_password = 'VxcKnLGUSlR4ZO0'; 

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>