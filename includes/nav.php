<style>
    /* Enhanced Navigation Styles */
    .main-header {
        background: linear-gradient(135deg, #0b6fc7 0%, #0a5da8 100%) !important;
        box-shadow: 0 4px 20px rgba(11, 111, 199, 0.4);
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
        filter: brightness(1.1);
    }

    .brand-image:hover {
        transform: scale(1.05);
        filter: brightness(1.2) drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
    }

    /* Main Navigation Items */
    .navbar-nav {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .nav-item {
        position: relative;
    }

    .nav-item .nav-link {
        color: rgba(255, 255, 255, 0.95) !important;
        font-weight: 600;
        padding: 0.75rem 1.5rem !important;
        border-radius: 10px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.95rem;
        background: rgba(255, 255, 255, 0.05);
        border: 2px solid transparent;
    }

    .nav-item .nav-link::before {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%) scaleX(0);
        width: 60%;
        height: 3px;
        background: white;
        border-radius: 3px 3px 0 0;
        transition: transform 0.3s ease;
    }

    .nav-item .nav-link:hover {
        color: white !important;
        background: rgba(255, 255, 255, 0.15);
        transform: translateY(-2px);
        border-color: rgba(255, 255, 255, 0.3);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    .nav-item .nav-link:hover::before {
        transform: translateX(-50%) scaleX(1);
    }

    .nav-item .nav-link.active {
        color: white !important;
        background: rgba(255, 255, 255, 0.2);
        border-color: rgba(255, 255, 255, 0.4);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    }

    .nav-item .nav-link.active::before {
        transform: translateX(-50%) scaleX(1);
    }

    .nav-item .nav-link i {
        font-size: 1.1rem;
        transition: all 0.3s ease;
    }

    .nav-item .nav-link:hover i,
    .nav-item .nav-link.active i {
        transform: scale(1.15);
    }

    /* Badge for active indicator */
    .nav-item .nav-link.active::after {
        content: '';
        position: absolute;
        top: 8px;
        right: 8px;
        width: 8px;
        height: 8px;
        background: #4ade80;
        border-radius: 50%;
        border: 2px solid white;
        box-shadow: 0 0 8px rgba(74, 222, 128, 0.6);
        animation: pulse 2s infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            opacity: 1;
            transform: scale(1);
        }

        50% {
            opacity: 0.7;
            transform: scale(1.1);
        }
    }

    /* Mobile Hamburger */
    .navbar-toggler {
        border: 2px solid rgba(255, 255, 255, 0.4) !important;
        border-radius: 10px !important;
        padding: 0.6rem 0.85rem !important;
        background: rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease;
    }

    .navbar-toggler:hover {
        background: rgba(255, 255, 255, 0.2);
        border-color: rgba(255, 255, 255, 0.6) !important;
    }

    .navbar-toggler:focus {
        box-shadow: 0 0 0 0.25rem rgba(255, 255, 255, 0.3) !important;
        outline: none;
    }

    .navbar-toggler-icon {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.95%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2.5' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e") !important;
        width: 1.5rem;
        height: 1.5rem;
    }

    /* Mobile Responsiveness */
    @media (max-width: 768px) {
        .main-header {
            padding: 0.5rem 0;
        }

        .navbar-collapse {
            background: rgba(11, 111, 199, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            margin-top: 1rem;
            padding: 1rem;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        .navbar-nav {
            flex-direction: column;
            width: 100%;
            gap: 0.5rem;
        }

        .nav-item {
            width: 100%;
        }

        .nav-item .nav-link {
            width: 100%;
            justify-content: center;
            padding: 1rem 1.5rem !important;
        }

        .nav-item .nav-link.active::after {
            right: 15px;
        }
    }

    @media (max-width: 576px) {
        .brand-image {
            max-height: 38px;
        }

        .nav-item .nav-link {
            font-size: 0.9rem;
            padding: 0.85rem 1.25rem !important;
        }

        .nav-item .nav-link i {
            font-size: 1rem;
        }
    }

    /* Container adjustments */
    .container {
        max-width: 1200px;
    }

    /* Navbar height fix */
    .content-wrapper {
        margin-top: 0;
        padding-top: 20px;
    }
</style>

<!-- Enhanced Navbar -->
<nav class="main-header navbar navbar-expand-md navbar-light">
    <div class="container">
        <a href="index.php" class="navbar-brand">
            <img src="http://172.31.147.158/image/Indogrosir_logo.jpg" alt="SPI 2K Logo"
                class="brand-image">
        </a>

        <button class="navbar-toggler" type="button" data-toggle="collapse"
            data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false"
            aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarCollapse">
            <!-- Navigation Links -->
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a href="index.php?page=dashboard"
                        class="nav-link <?php echo ($_GET['page'] ?? 'dashboard') == 'dashboard' ? 'active' : ''; ?>">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="index.php?page=kasir"
                        class="nav-link <?php echo ($_GET['page'] ?? '') == 'kasir' ? 'active' : ''; ?>">
                        <i class="fas fa-cash-register"></i>
                        <span>Detail Kasir</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="index.php?page=customer"
                        class="nav-link <?php echo ($_GET['page'] ?? '') == 'customer' ? 'active' : ''; ?>">
                        <i class="fas fa-users"></i>
                        <span>Customer & Salesman</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<!-- /.navbar -->

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">