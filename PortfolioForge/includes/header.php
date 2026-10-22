<?php
// includes/header.php
if (!ob_get_level()) {
    ob_start();
}
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? escape($page_title) . ' - PortfolioForge' : 'PortfolioForge - Premium Portfolio Builder'; ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts (Outfit & Inter) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <!-- Custom Style -->
    <link rel="stylesheet" href="/PortfolioForge/assets/css/style.css">
</head>
<body>
<!-- Decorative ambient bubbles (purely visual, sits behind all content) -->
<div class="pf-bg-bubbles" aria-hidden="true">
    <span class="pf-bubble pf-bubble-1"></span>
    <span class="pf-bubble pf-bubble-2"></span>
    <span class="pf-bubble pf-bubble-3"></span>
    <span class="pf-bubble pf-bubble-4"></span>
    <span class="pf-bubble pf-bubble-5"></span>
</div>
<nav class="navbar navbar-expand-lg fixed-top pf-navbar">
    <div class="container">
        <a class="navbar-brand pf-brand" href="/PortfolioForge/index.php">
            <i class="bi bi-hexagon-fill pf-brand-icon"></i> PortfolioForge
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link pf-nav-link" href="/PortfolioForge/index.php">Home</a>
                </li>
                <?php if (is_logged_in()): ?>
                    <?php if (is_admin()): ?>
                        <li class="nav-item">
                            <a class="nav-link pf-nav-link text-warning" href="/PortfolioForge/admin/dashboard.php">Admin Panel</a>
                        </li>
                        <li class="nav-item">
                            <span class="nav-link pf-nav-link" style="cursor:default;">Hi, <?php echo escape($_SESSION['username']); ?></span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link pf-nav-link text-danger" href="/PortfolioForge/logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link pf-nav-link" href="/PortfolioForge/dashboard/index.php">Dashboard</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle pf-nav-link pf-user-dropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Hi, <?php echo escape($_SESSION['username']); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow pf-dropdown-menu">
                                <li><a class="dropdown-item" href="/PortfolioForge/dashboard/profile.php">Edit Profile</a></li>
                                <?php
                                // Fetch portfolio username to render clean public link
                                require_once __DIR__ . '/../config/database.php';
                                $pf_check = get_user_portfolio($pdo, $_SESSION['user_id']);
                                if ($pf_check):
                                ?>
                                    <li><a class="dropdown-item" href="/PortfolioForge/portfolio.php?username=<?php echo escape($_SESSION['username']); ?>" target="_blank">View Portfolio</a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="/PortfolioForge/logout.php">Logout</a></li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link pf-nav-link text-danger" href="/PortfolioForge/logout.php">Logout</a>
                        </li>
                    <?php endif; ?>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link pf-nav-link" href="/PortfolioForge/login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn pf-btn-primary ms-lg-2" href="/PortfolioForge/register.php">Get Started</a>
                    </li>
                <?php endif; ?>
                <!-- Theme Toggle Switch -->
                <li class="nav-item ms-lg-3 mt-3 mt-lg-0">
                    <button class="btn btn-link nav-link p-0" id="theme-toggle" aria-label="Toggle theme">
                        <svg class="pf-theme-icon-light" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6zm0 1a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0zm0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13zm8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5zM3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8zm10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0zm-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0zm9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707zM4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708z"/>
                        </svg>
                        <svg class="pf-theme-icon-dark d-none" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M6 .278a.768.768 0 0 1 .08.858 7.208 7.208 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.280 7.277 1.341 0 2.61-.366 3.692-1.009a.75.75 0 0 1 .902.946 7.314 7.314 0 0 1-1.258 2.037c-1.341 1.34-3.195 2.163-5.242 2.163C3.383 16 0 12.618 0 8.596c0-3.329 2.23-6.143 5.278-7.14a.77.77 0 0 1 .722.822zm.5 1.037c-2.42 1.082-4.043 3.518-4.043 6.28 0 3.702 3.011 6.697 6.716 6.697 2.443 0 4.582-1.311 5.766-3.268a7.22 7.22 0 0 1-5.478-4.574A7.218 7.218 0 0 1 6.5 1.315z"/>
                        </svg>
                    </button>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="pf-page-wrapper">
<div class="container mt-5 pt-4">
<?php
// Display flash messages automatically in layout headers
echo display_flash();
?>
</div>
