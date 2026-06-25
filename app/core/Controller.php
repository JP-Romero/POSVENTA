<?php
/*
 * Base Controller
 * Loads the models and views
 */
class Controller {
    // Load model
    public function model($model) {
        // Require model file
        require_once '../app/models/' . $model . '.php';

        // Instantiate model
        return new $model();
    }

    // Load view
    public function view($view, $data = []) {
        if (file_exists('../app/views/' . $view . '.php')) {
            extract($data);
            require_once '../app/views/' . $view . '.php';
        } else {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'View not found: ' . $view]);
        }
    }

    // Get CSRF token for views
    public function getCsrfToken() {
        return generateCsrfToken();
    }

    // Validate CSRF token from POST
    public function validateCsrf() {
        $token = $_POST['csrf_token'] ?? '';
        if (!validateCsrfToken($token)) {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'CSRF validation failed']);
            exit;
        }
    }
}
