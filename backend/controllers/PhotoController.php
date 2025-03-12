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
    
    // Handles photo creation with form data and file upload
    public function createPhoto() {
        header('Content-Type: application/json');
        
        $user_id     = $_POST['user_id']     ?? null;
        $title       = $_POST['title']       ?? '';
        $description = $_POST['description'] ?? '';
        $tags        = $_POST['tags']        ?? '';

        if (!$user_id || empty($title)) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields (user_id, title).']);
            return;
        }
        
        // Process image upload
        $imagePath = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../uploads/';
            $fileName = time() . '_' . basename($_FILES['image']['name']);
            $targetFile = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $imagePath = 'uploads/' . $fileName;
            } else {
                echo json_encode(['success' => false, 'message' => 'Error moving uploaded file.']);
                return;
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Image file is required.']);
            return;
        }

        // Create photo entry
        $photo = new PhotoSkeleton(null, $user_id, $title, $description, $tags, $imagePath, null);
        $photoId = $this->photoModel->createPhoto($photo);
        
        echo json_encode([
            'success' => $photoId ? true : false,
            'message' => $photoId ? 'Photo created successfully.' : 'Error creating photo.',
            'photo_id' => $photoId
        ]);
    }
    
    // Retrieves all photos for the authenticated user
    public function getAllPhotos() {
        header('Content-Type: application/json');
        
        $headers = apache_request_headers();
        if (!isset($headers['Authorization'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized: No token provided.']);
            return;
        }

        try {
            $token = str_replace('Bearer ', '', $headers['Authorization']);
            $decoded = JWT::decode($token, new Key('your-very-secret-key', 'HS256'));
            $user_id = $decoded->user_id;
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Invalid token.']);
            return;
        }
        
        $photos = $this->photoModel->getPhotosByUserId($user_id);
        echo json_encode(['success' => true, 'photos' => $photos]);
    }
    
    // Updates an existing photo (title, description, tags, and optional new image)
    public function updatePhoto() {
        header('Content-Type: application/json');
    
        $headers = apache_request_headers();
        if (!isset($headers['Authorization'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized: No token provided.']);
            return;
        }

        try {
            $token = str_replace('Bearer ', '', $headers['Authorization']);
            $decoded = JWT::decode($token, new Key('your-very-secret-key', 'HS256'));
            $user_id = $decoded->user_id;
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Invalid token.']);
            return;
        }
    
        $id = $_POST['id'] ?? null;
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $tags = $_POST['tags'] ?? '';

        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Photo ID is required.']);
            return;
        }

        // Fetch existing photo and validate ownership
        $existingPhoto = $this->photoModel->getPhotoById($id);
        if (!$existingPhoto) {
            echo json_encode(['success' => false, 'message' => 'Photo not found.']);
            return;
        }
        if ($existingPhoto['user_id'] != $user_id) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized: You can only edit your own photos.']);
            return;
        }
    
        $imagePath = $existingPhoto['image_path'];
    
        // Handle new image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../uploads/';
            $fileName = time() . '_' . basename($_FILES['image']['name']);
            $targetFile = $uploadDir . $fileName;
    
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $imagePath = 'uploads/' . $fileName;
            } else {
                echo json_encode(['success' => false, 'message' => 'Error moving uploaded file.']);
                return;
            }
        }
    
        // Update photo details
        $existingPhoto['title'] = $title;
        $existingPhoto['description'] = $description;
        $existingPhoto['tags'] = $tags;
        $existingPhoto['image_path'] = $imagePath;
    
        $updatedRows = $this->photoModel->updatePhoto($existingPhoto);
        
        echo json_encode([
            'success' => $updatedRows ? true : false,
            'message' => $updatedRows ? 'Photo updated successfully.' : 'Error updating photo.'
        ]);
    }
    
    // Deletes a photo after verifying user ownership
    public function deletePhoto() {
        header('Content-Type: application/json');
    
        $headers = apache_request_headers();
        if (!isset($headers['Authorization'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized: No token provided.']);
            return;
        }

        try {
            $token = str_replace('Bearer ', '', $headers['Authorization']);
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
    
        // Fetch existing photo and validate ownership
        $existingPhoto = $this->photoModel->getPhotoById($id);
        if (!$existingPhoto) {
            echo json_encode(['success' => false, 'message' => 'Photo not found.']);
            return;
        }
        if ($existingPhoto['user_id'] != $user_id) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized: You can only delete your own photos.']);
            return;
        }
    
        $deletedRows = $this->photoModel->deletePhoto($id);
        echo json_encode([
            'success' => $deletedRows > 0,
            'message' => $deletedRows > 0 ? 'Photo deleted successfully.' : 'Error deleting photo.'
        ]);
    }
}
