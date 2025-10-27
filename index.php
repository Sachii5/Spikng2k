<?php
require_once "component/header.php";
require_once "component/nav.php";

// Routing sederhana
$page = $_GET['page'] ?? 'dashboard';

switch ($page) {
    case 'kasir':
        require_once 'controllers/DashboardController.php';
        $controller = new DashboardController();
        $controller->kasir();
        break;

    case 'customer':
        require_once 'controllers/CustomerController.php';
        $controller = new CustomerController();
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
