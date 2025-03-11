<?php
require_once __DIR__ . '/../models/PhotoModel.php';
require_once __DIR__ . '/../models/PhotoSkeleton.php';

class PhotoController {
    private $photoModel;
    
    public function __construct($db) {
        $this->photoModel = new PhotoModel($db);
    }
    
    /**
     * Create a new photo.
     * Expects JSON body with fields: user_id, title, description, tags, image_path
     */
    public function createPhoto() {
        header('Content-Type: application/json');
        
        $inputJSON = file_get_contents('php://input');
        $input = json_decode($inputJSON, true);

        $user_id     = $input['user_id']     ?? null;
        $title       = $input['title']       ?? '';
        $description = $input['description'] ?? '';
        $tags        = $input['tags']        ?? '';
        $image_path  = $input['image_path']  ?? '';

        if (!$user_id || empty($title) || empty($image_path)) {
            echo json_encode([
                'success' => false,
                'message' => 'Missing required fields (user_id, title, image_path).'
            ]);
            return;
        }
        
        $photo = new PhotoSkeleton(null, $user_id, $title, $description, $tags, $image_path, null);
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
     * Update a photo.
     * Expects JSON body with fields: id, title, description, tags, image_path
     */
    public function updatePhoto() {
        header('Content-Type: application/json');
        
        $inputJSON = file_get_contents('php://input');
        $input = json_decode($inputJSON, true);

        $id          = $input['id']          ?? null;
        $title       = $input['title']       ?? '';
        $description = $input['description'] ?? '';
        $tags        = $input['tags']        ?? '';
        $image_path  = $input['image_path']  ?? '';
        
        if (!$id) {
            echo json_encode([
                'success' => false,
                'message' => 'Photo ID is required.'
            ]);
            return;
        }
        
        $photo = $this->photoModel->getPhotoById($id);
        if (!$photo) {
            echo json_encode([
                'success' => false,
                'message' => 'Photo not found.'
            ]);
            return;
        }
        
        $photo->setTitle($title);
        $photo->setDescription($description);
        $photo->setTags($tags);
        $photo->setImagePath($image_path);
        
        $updatedRows = $this->photoModel->updatePhoto($photo);
        
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
     * We can accept the ID from GET or JSON body. Let's keep GET for simplicity.
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
        if ($deletedRows) {
            echo json_encode([
                'success' => true,
                'message' => 'Photo deleted successfully.'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error deleting photo.'
            ]);
        }
    }
    
    /**
     * Retrieve all photos.
     * Typically done via GET request with no body.
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
