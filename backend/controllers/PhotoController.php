<?php
require_once __DIR__ . '/../models/PhotoModel.php';
require_once __DIR__ . '/../models/PhotoSkeleton.php';

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
        
        // Read form fields from $_POST
        $user_id     = $_POST['user_id']     ?? null;
        $title       = $_POST['title']       ?? '';
        $description = $_POST['description'] ?? '';
        $tags        = $_POST['tags']        ?? '';

        // Validate required fields
        if (!$user_id || empty($title)) {
            echo json_encode([
                'success' => false,
                'message' => 'Missing required fields (user_id, title).'
            ]);
            return;
        }

        // Handle the uploaded file if it exists
        $imagePath = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            // Where to store uploaded files (ensure this folder exists and is writable)
            $uploadDir = __DIR__ . '/../uploads/';
            // Generate a unique filename
            $fileName = time() . '_' . basename($_FILES['image']['name']);
            $targetFile = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                // Store the relative path in the DB (e.g., "uploads/filename.jpg")
                $imagePath = 'uploads/' . $fileName;
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error moving uploaded file.'
                ]);
                return;
            }
        } else {
            // If no file was uploaded, you could handle that case or allow a blank path
            echo json_encode([
                'success' => false,
                'message' => 'Image file is required.'
            ]);
            return;
        }

        // Create a PhotoSkeleton
        $photo = new PhotoSkeleton(null, $user_id, $title, $description, $tags, $imagePath, null);
        
        // Save to DB
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
     * Retrieve a photo by its ID.
     * Expects GET parameter: id
     */
    public function getPhotoById() {
        header('Content-Type: application/json');
        
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            echo json_encode([
                'success' => false,
                'message' => 'Photo ID is required.'
            ]);
            return;
        }
        
        $photo = $this->photoModel->getPhotoById($id);
        if ($photo) {
            echo json_encode([
                'success' => true,
                'photo'   => $photo
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Photo not found.'
            ]);
        }
    }
    
    /**
     * Update a photo via multipart/form-data.
     * Expects:
     *   - $_POST: id, title, description, tags
     *   - $_FILES['image']: optional new file
     */
    public function updatePhoto() {
        header('Content-Type: application/json');
    
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
    
        // Fetch existing photo as an associative array
        $existingPhoto = $this->photoModel->getPhotoById($id);
        if (!$existingPhoto) {
            echo json_encode([
                'success' => false,
                'message' => 'Photo not found.'
            ]);
            return;
        }
    
        // Default to existing image path using array access
        $imagePath = $existingPhoto['image_path'];
    
        // Check if a new file was uploaded
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../uploads/';
            $fileName = time() . '_' . basename($_FILES['image']['name']);
            $targetFile = $uploadDir . $fileName;
    
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $imagePath = 'uploads/' . $fileName;
                // Optionally, delete the old file if needed.
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
     * Expects GET parameter: id
     */
    public function deletePhoto() {
        header('Content-Type: application/json');
        
        $id = $_GET['id'] ?? null;

        if (!$id) {
            echo json_encode([
                'success' => false,
                'message' => 'Photo ID is required.'
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


    
    /**
     * Retrieve all photos.
     */
    public function getAllPhotos() {
        header('Content-Type: application/json');
        
        $photos = $this->photoModel->getAllPhotos();
        echo json_encode([
            'success' => true,
            'photos'  => $photos
        ]);
    }
}
