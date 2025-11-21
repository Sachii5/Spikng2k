<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SPI Monitoring - Indogrosir</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.1.0/css/adminlte.min.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <?php
    // Load page-specific CSS
    $page = $_GET['page'] ?? 'dashboard';
    $cssFiles = [
        'dashboard' => 'assets/css/dashboard.css',
        'kasir' => 'assets/css/kasir.css',
        'customer' => 'assets/css/customer.css'
    ];

    if (isset($cssFiles[$page]) && file_exists($cssFiles[$page])) {
        echo '<link rel="stylesheet" href="' . $cssFiles[$page] . '">';
    }
    ?>
</head>

<body class="hold-transition layout-top-nav">
    <div class="wrapper">