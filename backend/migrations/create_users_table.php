<?php
require_once __DIR__ . '/../connection/db.php';

try {
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        fullname VARCHAR(50) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=INNODB;";

    $conn->exec($sql);
    echo 'Users table created successfully.';
} catch (PDOException $e) {
    echo 'Error creating users table: ' . $e->getMessage();
}
?>
