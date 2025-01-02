<?php
// Database configuration
$host = 'localhost';      // Server host
$port = '5432';           // PostgreSQL port (default: 5432)
$dbname = 'CommunitySharing'; // Database name
$user = 'postgres';       // PostgreSQL username (replace if different)
$password = '122002'; // PostgreSQL password (replace with your actual password)

try {
    // Create a new PDO instance for PostgreSQL
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);

    // Set error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connected successfully to the database!";
} catch (PDOException $e) {
    // Catch and display any connection errors
    die("Database connection failed: " . $e->getMessage());
}
