<?php
require_once __DIR__ . '/../models/PhotoModel.php';
require_once __DIR__ . '/../models/PhotoSkeleton.php';
require_once __DIR__ . '/../../vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class PhotoController {
    private $photoModel;
    
    public function __construct($db) {
        $this->photoModel = new PhotoModel($db);
    }
    
    /**
     * Create a new photo via multipart/form-data.
     * Expects:
     *   - $_POST: user_id, title, description, tags
     *   - $_FILES['image']: the uploaded file
     */
    public function createPhoto() {
        header('Content-Type: application/json');
        
        $user_id     = $_POST['user_id']     ?? null;
        $title       = $_POST['title']       ?? '';
        $description = $_POST['description'] ?? '';
        $tags        = $_POST['tags']        ?? '';

        if (!$user_id || empty($title)) {
            echo json_encode([
                'success' => false,
                'message' => 'Missing required fields (user_id, title).'
            ]);
            return;
        }
        
        $imagePath = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../uploads/';
            $fileName = time() . '_' . basename($_FILES['image']['name']);
            $targetFile = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $imagePath = 'uploads/' . $fileName;
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error moving uploaded file.'
                ]);
                return;
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Image file is required.'
            ]);
            return;
        }

        // Create PhotoSkeleton (or use an array)
        $photo = new PhotoSkeleton(null, $user_id, $title, $description, $tags, $imagePath, null);
        $photoId = $this->photoModel->createPhoto($photo);
        
        if ($photoId) {
            echo json_encode([
                'success' => true,
                'message' => 'Photo created successfully.',
                'photo_id' => $photoId
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error creating photo.'
            ]);
        }
    }
    
    /**
     * Retrieve all photos for the logged-in user.
     * Uses the JWT token from the Authorization header.
     */
    public function getAllPhotos() {
        header('Content-Type: application/json');
        
        $headers = apache_request_headers();
        if (!isset($headers['Authorization'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized: No token provided.']);
            return;
        }
        $token = str_replace('Bearer ', '', $headers['Authorization']);
        try {
            $decoded = JWT::decode($token, new Key('your-very-secret-key', 'HS256'));
            $user_id = $decoded->user_id;
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Invalid token.']);
            return;
        }
        
        $photos = $this->photoModel->getPhotosByUserId($user_id);
        echo json_encode(['success' => true, 'photos' => $photos]);
    }
    
    /**
     * Update a photo via multipart/form-data.
     * Expects:
     *   - $_POST: id, title, description, tags
     *   - $_FILES['image']: optional new file
     * Checks that the photo belongs to the logged-in user.
     */
    public function updatePhoto() {
        header('Content-Type: application/json');
    
        $headers = apache_request_headers();
        if (!isset($headers['Authorization'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized: No token provided.']);
            return;
        }
        $token = str_replace('Bearer ', '', $headers['Authorization']);
        try {
            $decoded = JWT::decode($token, new Key('your-very-secret-key', 'HS256'));
            $user_id = $decoded->user_id;
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Invalid token.']);
            return;
        }
    
        $id          = $_POST['id']          ?? null;
        $title       = $_POST['title']       ?? '';
        $description = $_POST['description'] ?? '';
        $tags        = $_POST['tags']        ?? '';
    
        if (!$id) {
            echo json_encode([
                'success' => false,
                'message' => 'Photo ID is required.'
            ]);
            return;
        }
    
        $existingPhoto = $this->photoModel->getPhotoById($id);
        if (!$existingPhoto) {
            echo json_encode([
                'success' => false,
                'message' => 'Photo not found.'
            ]);
            return;
        }
        
        // Check if the photo belongs to the logged in user
        if ($existingPhoto['user_id'] != $user_id) {
            echo json_encode([
                'success' => false,
                'message' => 'Unauthorized: You can only edit your own photos.'
            ]);
            return;
        }
    
        $imagePath = $existingPhoto['image_path'];
    
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../uploads/';
            $fileName = time() . '_' . basename($_FILES['image']['name']);
            $targetFile = $uploadDir . $fileName;
    
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $imagePath = 'uploads/' . $fileName;
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error moving uploaded file.'
                ]);
                return;
            }
        }
    
        // Update the photo array with new values
        $existingPhoto['title'] = $title;
        $existingPhoto['description'] = $description;
        $existingPhoto['tags'] = $tags;
        $existingPhoto['image_path'] = $imagePath;
    
        $updatedRows = $this->photoModel->updatePhoto($existingPhoto);
        
        if ($updatedRows) {
            echo json_encode([
                'success' => true,
                'message' => 'Photo updated successfully.'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error updating photo.'
            ]);
        }
    }
    
    /**
     * Delete a photo.
     * Expects GET parameter: id.
     * Checks that the photo belongs to the logged-in user.
     */
    public function deletePhoto() {
        header('Content-Type: application/json');
    
        $headers = apache_request_headers();
        if (!isset($headers['Authorization'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized: No token provided.']);
            return;
        }
        $token = str_replace('Bearer ', '', $headers['Authorization']);
        try {
            $decoded = JWT::decode($token, new Key('your-very-secret-key', 'HS256'));
            $user_id = $decoded->user_id;
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Invalid token.']);
            return;
        }
    
        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Photo ID is required.']);
            return;
        }
    
        $existingPhoto = $this->photoModel->getPhotoById($id);
        if (!$existingPhoto) {
            echo json_encode(['success' => false, 'message' => 'Photo not found.']);
            return;
        }
    
        if ($existingPhoto['user_id'] != $user_id) {
            echo json_encode([
                'success' => false,
                'message' => 'Unauthorized: You can only delete your own photos.'
            ]);
            return;
        }
    
        $deletedRows = $this->photoModel->deletePhoto($id);
        if ($deletedRows === false) {
            echo json_encode([
                'success' => false,
                'message' => 'Database error deleting photo.'
            ]);
        } elseif ($deletedRows === 0) {
            echo json_encode([
                'success' => false,
                'message' => 'No photo found with that ID.'
            ]);
        } else {
            echo json_encode([
                'success' => true,
                'message' => 'Photo deleted successfully.'
            ]);
        }
    }
}
