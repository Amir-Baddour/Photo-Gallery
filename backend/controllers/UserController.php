<?php
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/UserSkeleton.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class UserController {
    private $userModel;
    
    private $secretKey = 'your-very-secret-key';

    public function __construct($conn) {
        $this->userModel = new UserModel($conn);
    }

    /**
     * Handle user registration:
     *   - Expects JSON body: { "fullname": "", "email": "", "password": "" }
     *   - Hashes the password with SHA256
     *   - Creates the user in the database
     */
    public function register() {
        header('Content-Type: application/json');

        $inputJSON = file_get_contents('php://input');
        $input = json_decode($inputJSON, true);

        $fullname = $input['fullname'] ?? '';
        $email    = $input['email'] ?? '';
        $password = $input['password'] ?? '';

        if (empty($fullname) || empty($email) || empty($password)) {
            echo json_encode([
                'success' => false,
                'message' => 'Missing required fields.'
            ]);
            return;
        }

        $existingUser = $this->userModel->getUserByEmail($email);
        if ($existingUser) {
            echo json_encode([
                'success' => false,
                'message' => 'User with this email already exists.'
            ]);
            return;
        }

        $hashedPassword = hash('sha256', $password);

        $user = new UserSkeleton(null, $fullname, $email, $hashedPassword, null);

        $userId = $this->userModel->createUser($user);

        if ($userId) {
            echo json_encode([
                'success' => true,
                'message' => 'User registered successfully.',
                'user_id' => $userId
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error registering user.'
            ]);
        }
    }

    /**
     * Handle user login:
     *   - Expects JSON body: { "email": "", "password": "" }
     *   - Verifies the email/password
     *   - Returns a JWT if credentials are correct along with the user_id
     */
    public function login() {
        header('Content-Type: application/json');

        $inputJSON = file_get_contents('php://input');
        $input = json_decode($inputJSON, true);

        $email    = $input['email'] ?? '';
        $password = $input['password'] ?? '';

        if (empty($email) || empty($password)) {
            echo json_encode([
                'success' => false,
                'message' => 'Missing email or password.'
            ]);
            return;
        }

        $foundUser = $this->userModel->getUserByEmail($email);
        if (!$foundUser) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid email or password.'
            ]);
            return;
        }

        $hashedPassword = hash('sha256', $password);
        if ($hashedPassword !== $foundUser->getPassword()) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid email or password.'
            ]);
            return;
        }

        $payload = [
            'user_id' => $foundUser->getId(),
            'email'   => $foundUser->getEmail(),
            'iat'     => time(),
            'exp'     => time() + 3600 // 1 hour expiration
        ];

        $jwt = JWT::encode($payload, $this->secretKey, 'HS256');

        echo json_encode([
            'success' => true,
            'message' => 'Login successful.',
            'token'   => $jwt,
            'user_id' => $foundUser->getId()
        ]);
    }
}
