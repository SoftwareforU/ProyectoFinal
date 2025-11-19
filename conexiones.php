<?php
ini_set('display_errors',1);
error_reporting(E_ALL);

$host   = 'localhost';
$port   = 3306;
$dbname = 'coope'; 
$username   = 'emilio';
$password   = 'nacional';

// Montas el DSN  
$dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    die($e->getMessage());
}
return $pdo;

?>
