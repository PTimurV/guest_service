<?php

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Убираем начальный слеш и разделяем по слешу
$uriParts = explode('/', trim($requestUri, '/'));

$controller = new GuestController();

if ($uriParts[0] === 'guests') {
    if (isset($uriParts[1]) && is_numeric($uriParts[1])) {
        $id = (int)$uriParts[1];
        switch ($requestMethod) {
            case 'GET':
                $controller->getById($id);
                break;
            case 'PUT':
            case 'PATCH':
                $controller->update($id);
                break;
            case 'DELETE':
                $controller->delete($id);
                break;
            default:
                http_response_code(405);
                echo json_encode(['error' => 'Method Not Allowed'], JSON_UNESCAPED_UNICODE);
        }
    } else {
        switch ($requestMethod) {
            case 'GET':
                $controller->getAll();
                break;
            case 'POST':
                $controller->create();
                break;
            default:
                http_response_code(405);
                echo json_encode(['error' => 'Method Not Allowed'], JSON_UNESCAPED_UNICODE);
        }
    }
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Not Found'], JSON_UNESCAPED_UNICODE);
}
