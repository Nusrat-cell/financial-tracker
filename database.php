<?php
function getDatabaseConnection() {
    $host = '127.0.0.1';
    $db = 'financial_tracker';
    $user = 'root'; // XAMPP default
    $pass = '';     // XAMPP default
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ];

    try {
        return new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
        die(json_encode(["error" => "DB Connection Failed: " . $e->getMessage()]));
    }
}
