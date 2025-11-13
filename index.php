<?php
require_once "component/header.php";
require_once "component/nav.php";

// Routing sederhana
$page = $_GET['page'] ?? 'dashboard';
$action = $_GET['action'] ?? 'index'; // Tambahkan ini

switch ($page) {
    case 'kasir':
        require_once 'controllers/KasirController.php';
        $controller = new KasirController();

        // Handle different actions
        if ($action === 'getDetail') {
            // API endpoint untuk get detail - return JSON dan exit
            $controller->getDetail();
            exit; // IMPORTANT: exit setelah JSON response agar tidak load footer
        } else {
            // Default index action
            $controller->index();
        }
        break;

    case 'customer':
        require_once 'controllers/CustomerController.php';
        $controller = new CustomerController();
        $controller->index();
        break;

    case 'rkm':
        require_once 'controllers/RkmController.php';
        $controller = new RkmController();
        $controller->index();
        break;

    case 'dashboard':
    default:
        require_once 'controllers/DashboardController.php';
        $controller = new DashboardController();
        $controller->index();
        break;
}

require_once "component/footer.php";
