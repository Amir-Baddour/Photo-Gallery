<?php

require_once __DIR__ . '/../connection/db.php';

try {
    $hashedPassword = hash('sha256', 'Abbas123');

    $sql = "INSERT INTO users (fullname, email, password, created_at)
            VALUES (:fullname, :email, :password, NOW())";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':fullname' => 'Abbas Hassan',
        ':email'    => 'abbass.hassan.a7@gmail.com',
        ':password' => $hashedPassword
    ]);

    echo "Seeded user: Abbas Hassan.\n";

} catch (PDOException $e) {
    echo "Error seeding users: " . $e->getMessage();
}
