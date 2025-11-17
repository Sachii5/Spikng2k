<style>
    /* Enhanced Navigation Styles */
    .main-header {
        background: #0b6fc7 !important;
        box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3);
        border: none !important;
        padding: 0.75rem 0;
    }

    .navbar-brand {
        display: flex;
        align-items: center;
        padding: 0.5rem 0;
    }

    .brand-image {
        max-height: 45px;
        width: auto;
        transition: all 0.3s ease;
        background: rgba(255, 255, 255, 0.9);
        padding: 8px 12px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .brand-image:hover {
        transform: scale(1.05);
        background: rgba(255, 255, 255, 1);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    .navbar-nav .nav-link {
        color: rgba(255, 255, 255, 0.9) !important;
        font-weight: 600;
        padding: 0.75rem 1.25rem !important;
        border-radius: 8px;
        transition: all 0.3s ease;
        position: relative;
        margin: 0 0.25rem;
    }

    .navbar-nav .nav-link:hover {
        color: white !important;
        background: rgba(255, 255, 255, 0.15);
        transform: translateY(-1px);
    }

    .navbar-nav .nav-link:focus {
        color: white !important;
        background: rgba(255, 255, 255, 0.2);
    }

    .dropdown-toggle::after {
        margin-left: 0.5rem;
        border-top: 0.3em solid rgba(255, 255, 255, 0.8);
        border-right: 0.3em solid transparent;
        border-bottom: 0;
        border-left: 0.3em solid transparent;
    }

    .dropdown-menu {
        background: white !important;
        border: none !important;
        border-radius: 16px !important;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15), 0 8px 25px rgba(0, 0, 0, 0.1) !important;
        padding: 0.75rem 0 !important;
        margin-top: 0.75rem !important;
        min-width: 280px;
        animation: slideDownFade 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        overflow: hidden;
    }

    @keyframes slideDownFade {
        from {
            opacity: 0;
            transform: translateY(-15px) scale(0.95);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .dropdown-item {
        padding: 1rem 1.5rem !important;
        color: #2c3e50 !important;
        font-weight: 500;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 0;
        position: relative;
        margin: 0.25rem 0.75rem;
        border-radius: 10px;
        display: flex;
        align-items: center;
        font-size: 0.95rem;
    }

    .dropdown-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: #0b6fc7;
        border-radius: 0 4px 4px 0;
        transform: scaleY(0);
        transition: transform 0.3s ease;
    }

    .dropdown-item:hover {
        background: #0b6fc7 !important;
        color: white !important;
        transform: translateX(8px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    }

    .dropdown-item:hover::before {
        transform: scaleY(1);
    }

    .dropdown-item.active {
        background: #0b6fc7 !important;
        color: white !important;
        font-weight: 600;
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    }

    .dropdown-item.active::before {
        transform: scaleY(1);
    }

    .dropdown-item i {
        width: 24px;
        text-align: center;
        margin-right: 1rem;
        font-size: 1rem;
        opacity: 0.8;
        transition: all 0.3s ease;
    }

    .dropdown-item:hover i,
    .dropdown-item.active i {
        opacity: 1;
        transform: scale(1.1);
    }

    /* Dropdown arrow */
    .dropdown-menu::before {
        content: '';
        position: absolute;
        top: -8px;
        left: 20px;
        width: 0;
        height: 0;
        border-left: 8px solid transparent;
        border-right: 8px solid transparent;
        border-bottom: 8px solid white;
        filter: drop-shadow(0 -2px 4px rgba(0, 0, 0, 0.1));
    }

    .navbar-toggler {
        border: 2px solid rgba(255, 255, 255, 0.3) !important;
        border-radius: 8px !important;
        padding: 0.5rem 0.75rem !important;
    }

    .navbar-toggler:focus {
        box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.25) !important;
    }

    .navbar-toggler-icon {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.8%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e") !important;
    }

    /* Mobile Responsiveness */
    @media (max-width: 768px) {
        .main-header {
            padding: 0.5rem 0;
        }

        .navbar-nav {
            margin-top: 1rem;
            padding: 1rem 0;
        }

        .navbar-nav .nav-link {
            margin: 0.25rem 0;
            text-align: center;
        }

        .dropdown-menu {
            position: static !important;
            transform: none !important;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1) !important;
            background: rgba(255, 255, 255, 0.95) !important;
            border-radius: 12px !important;
            margin: 0.75rem 0 !important;
            backdrop-filter: blur(10px);
        }

        .dropdown-item {
            color: #2c3e50 !important;
            text-align: center;
            margin: 0.25rem 0.5rem !important;
        }

        .dropdown-item:hover {
            background: #0b6fc7 !important;
            color: white !important;
            transform: translateX(5px);
        }
    }

    /* Container adjustments */
    .container {
        max-width: 1200px;
    }

    /* Smooth transitions */
    * {
        transition: all 0.3s ease;
    }
</style>

<!-- Enhanced Navbar -->
<nav class="main-header navbar navbar-expand-md navbar-light navbar-white">
    <div class="container">
        <a href="index.php">
            <img src="http://172.31.147.158/image/Indogrosir_logo.jpg" alt="SPI 2K Logo"
                class="brand-image elevation-3">
        </a>

        <button class="navbar-toggler order-1" type="button" data-toggle="collapse"
            data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false"
            aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse order-3" id="navbarCollapse">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a id="ddstore" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                        class="nav-link dropdown-toggle">
                        <i class="fas fa-chart-line mr-2"></i>Penjualan
                    </a>
                    <ul aria-labelledby="ddstore" class="dropdown-menu">
                        <li>
                            <a href="index.php?page=dashboard" class="dropdown-item <?php echo ($_GET['page'] ?? 'dashboard') == 'dashboard' ? 'active' : ''; ?>">
                                <i class="fas fa-tachometer-alt"></i>Dashboard Monitoring
                            </a>
                        </li>
                        <li>
                            <a href="index.php?page=kasir" class="dropdown-item <?php echo ($_GET['page'] ?? '') == 'kasir' ? 'active' : ''; ?>">
                                <i class="fas fa-cash-register"></i>Detail Kasir
                            </a>
                        </li>
                        <li>
                            <a href="index.php?page=customer" class="dropdown-item <?php echo ($_GET['page'] ?? '') == 'customer' ? 'active' : ''; ?>">
                                <i class="fas fa-users"></i>Data Customer
                            </a>
                        </li>
                        <li>
                            <a href="index.php?page=rkm" class="dropdown-item <?php echo ($_GET['page'] ?? '') == 'rkm' ? 'active' : ''; ?>">
                                <i class="fas fa-users"></i>RKM
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
<!-- /.navbar -->

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">