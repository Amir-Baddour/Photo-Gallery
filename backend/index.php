<?php
header("Content-Type: application/json");

require_once __DIR__ . '/connection/db.php';

$api = $_GET['api'] ?? '';
$action = $_GET['action'] ?? '';

if (empty($api) || empty($action)) {
    echo json_encode([
        'success' => false,
        'message' => 'API and action parameters are required.'
    ]);
    exit;
}

switch ($api) {
    case 'user':
        require_once __DIR__ . '/controllers/UserController.php';
        $controller = new UserController($conn);
        switch ($action) {
            case 'register':
                $controller->register();
                break;
            case 'login':
                $controller->login();
                break;
            default:
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid user action.'
                ]);
                break;
        }
        break;
    case 'photo':
        require_once __DIR__ . '/controllers/PhotoController.php';
        $controller = new PhotoController($conn);
        switch ($action) {
            case 'create':
                $controller->createPhoto();
                break;
            case 'get':
                $controller->getPhotoById();
                break;
            case 'update':
                $controller->updatePhoto();
                break;
            case 'delete':
                $controller->deletePhoto();
                break;
            case 'getAll':
                $controller->getAllPhotos();
                break;
            default:
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid photo action.'
                ]);
                break;
        }
        break;
    default:
        echo json_encode([
            'success' => false,
            'message' => 'Invalid API endpoint.'
        ]);
        break;
}
?>
