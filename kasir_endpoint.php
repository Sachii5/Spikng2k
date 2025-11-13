<?php
// kasir_endpoint.php - File khusus untuk API endpoints
require_once 'models/KasirModel.php';
require_once 'controllers/KasirController.php';

// Set header JSON untuk semua response
header('Content-Type: application/json');

try {
    $controller = new KasirController();

    if (isset($_GET['action']) && $_GET['action'] === 'getDetail') {
        $controller->getDetail();
    } else {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Endpoint not found'
        ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
