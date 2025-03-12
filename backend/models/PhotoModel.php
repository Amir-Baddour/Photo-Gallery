<?php
require_once __DIR__ . '/../connection/db.php';

class PhotoModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Creates a new photo and returns the inserted photo ID.
    public function createPhoto($photo) {
        // Here, $photo can still be a PhotoSkeleton object or an array.
        // If it's a PhotoSkeleton, just call the getters. If it's an array, use array keys.
        try {
            $sql = "INSERT INTO photos (user_id, title, description, tags, image_path, created_at)
                    VALUES (:user_id, :title, :description, :tags, :image_path, NOW())";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':user_id'    => is_array($photo) ? $photo['user_id'] : $photo->getUserId(),
                ':title'      => is_array($photo) ? $photo['title'] : $photo->getTitle(),
                ':description'=> is_array($photo) ? $photo['description'] : $photo->getDescription(),
                ':tags'       => is_array($photo) ? $photo['tags'] : $photo->getTags(),
                ':image_path' => is_array($photo) ? $photo['image_path'] : $photo->getImagePath(),
            ]);
            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            error_log("Database Insert Error: " . $e->getMessage());
            return null;
        }
    }

    // Retrieves a photo by its ID; returns an associative array or null.
    public function getPhotoById($id) {
        $sql = "SELECT * FROM photos WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return [
                'id'          => $row['id'],
                'user_id'     => $row['user_id'],
                'title'       => $row['title'],
                'description' => $row['description'],
                'tags'        => $row['tags'],
                'image_path'  => $row['image_path'],
                'created_at'  => $row['created_at']
            ];
        }
        return null;
    }

    // Updates a photo; returns the number of affected rows.
    // Accepts either an array or a PhotoSkeleton, similar to createPhoto.
    public function updatePhoto($photo) {
        try {
            $sql = "UPDATE photos 
                    SET title = :title, description = :description, tags = :tags, image_path = :image_path
                    WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':title'      => is_array($photo) ? $photo['title'] : $photo->getTitle(),
                ':description'=> is_array($photo) ? $photo['description'] : $photo->getDescription(),
                ':tags'       => is_array($photo) ? $photo['tags'] : $photo->getTags(),
                ':image_path' => is_array($photo) ? $photo['image_path'] : $photo->getImagePath(),
                ':id'         => is_array($photo) ? $photo['id'] : $photo->getId()
            ]);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            error_log("Database Update Error: " . $e->getMessage());
            return false;
        }
    }

    // Deletes a photo by its ID; returns the number of affected rows.
    // In PhotoModel.php

    public function deletePhoto($id) {
        try {
            $sql = "DELETE FROM photos WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $id]);
            
            // Perform a SELECT to check if the photo still exists
            $checkSql = "SELECT COUNT(*) as cnt FROM photos WHERE id = :id";
            $checkStmt = $this->conn->prepare($checkSql);
            $checkStmt->execute([':id' => $id]);
            $result = $checkStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result && $result['cnt'] == 0) {
                // The photo is gone; return a success value (1)
                return 1;
            } else {
                // The photo still exists (or something went wrong)
                return 0;
            }
        } catch (PDOException $e) {
            error_log("Database Delete Error: " . $e->getMessage());
            return false;
        }
    }


    // Retrieves all photos as associative arrays, ordered by creation date in descending order.
    public function getAllPhotos() {
        $sql = "SELECT * FROM photos ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Directly return the array of rows
        return $rows;
    }
}
