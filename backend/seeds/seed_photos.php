<?php

require_once __DIR__ . '/../connection/db.php';

try {
    // Fetch user ID for the given email
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute(['email' => 'abbass.hassan.a7@gmail.com']);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("Error: User not found.\n");
    }

    $user_id = $user['id'];

    // Photo data
    $photos = [
        [
            'title' => 'Paris Winter Stroll',
            'description' => 'Eiffel Tower on a cloudy day.',
            'tags' => '#Paris #EiffelTower #Travel #Winter',
            'image_path' => 'uploads/Paris.jpg',
        ],
        [
            'title' => 'Serene Autumn View',
            'description' => 'A quiet lakeside bench with mountain views.',
            'tags' => '#Autumn #Nature #Mountains #Lake #Switzerland',
            'image_path' => 'uploads/Swizerland.jpg',
        ],
        [
            'title' => 'Lake Bliss',
            'description' => 'Swans glide on a scenic lake with mountain views.',
            'tags' => '#Lake #Swans #Nature #Mountains #Switzerland',
            'image_path' => 'uploads/Sea.jpg',
        ]
    ];

    // Insert photos into the database
    $sql = "INSERT INTO photos (user_id, title, description, tags, image_path, created_at) 
            VALUES (:user_id, :title, :description, :tags, :image_path, NOW())";

    $stmt = $conn->prepare($sql);

    foreach ($photos as $photo) {
        $stmt->execute([
            'user_id' => $user_id,
            'title' => $photo['title'],
            'description' => $photo['description'],
            'tags' => $photo['tags'],
            'image_path' => $photo['image_path']
        ]);
    }

    echo "Seeded 3 photos for user: Abbas Hassan.\n";

} catch (PDOException $e) {
    echo "Error seeding photos: " . $e->getMessage();
}
