<?php
require_once __DIR__ . '/../connection/connection.php';

try {
    $sql = "CREATE TABLE IF NOT EXISTS photos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        title VARCHAR(100) NOT NULL,
        description TEXT,
        tags VARCHAR(255),
        image_path VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=INNODB;";

    $conn->exec($sql);
    echo "Photos table created successfully.";
} catch (PDOException $e) {
    echo "Error creating photos table: " . $e->getMessage();
}
?>
