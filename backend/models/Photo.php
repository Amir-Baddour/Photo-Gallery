<?php
require_once __DIR__ . '/../connection/db.php';
require_once __DIR__ . '/PhotoSkeleton.php';

class PhotoModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Creates a new photo and returns the inserted photo ID.
    public function createPhoto(PhotoSkeleton $photo) {
        try {
            $sql = "INSERT INTO photos (user_id, title, description, tags, image_path, created_at)
                    VALUES (:user_id, :title, :description, :tags, :image_path, NOW())";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':user_id'    => $photo->getUserId(),
                ':title'      => $photo->getTitle(),
                ':description'=> $photo->getDescription(),
                ':tags'       => $photo->getTags(),
                ':image_path' => $photo->getImagePath()
            ]);
            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            error_log("Database Insert Error: " . $e->getMessage());
            return null;
        }
    }

    // Retrieves a photo by its ID; returns a PhotoSkeleton instance or null.
    public function getPhotoById($id) {
        $sql = "SELECT * FROM photos WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new PhotoSkeleton(
                $row['id'],
                $row['user_id'],
                $row['title'],
                $row['description'],
                $row['tags'],
                $row['image_path'],
                $row['created_at']
            );
        }
        return null;
    }

    // Updates a photo; returns the number of affected rows.
    public function updatePhoto(PhotoSkeleton $photo) {
        try {
            $sql = "UPDATE photos 
                    SET title = :title, description = :description, tags = :tags, image_path = :image_path
                    WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':title'      => $photo->getTitle(),
                ':description'=> $photo->getDescription(),
                ':tags'       => $photo->getTags(),
                ':image_path' => $photo->getImagePath(),
                ':id'         => $photo->getId()
            ]);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            error_log("Database Update Error: " . $e->getMessage());
            return false;
        }
    }

    // Deletes a photo by its ID; returns the number of affected rows.
    public function deletePhoto($id) {
        try {
            $sql = "DELETE FROM photos WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            error_log("Database Delete Error: " . $e->getMessage());
            return false;
        }
    }

    // Retrieves all photos ordered by creation date in descending order.
    public function getAllPhotos() {
        $sql = "SELECT * FROM photos ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $photos = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $photos[] = new PhotoSkeleton(
                $row['id'],
                $row['user_id'],
                $row['title'],
                $row['description'],
                $row['tags'],
                $row['image_path'],
                $row['created_at']
            );
        }
        return $photos;
    }
}
?>
