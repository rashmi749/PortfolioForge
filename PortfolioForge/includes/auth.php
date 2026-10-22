<?php
/**
 * PortfolioForge Authentication Helpers
 * Session checks and role authorization.
 */

require_once __DIR__ . '/functions.php';

/**
 * Check if the user is authenticated.
 *
 * @return bool
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}

/**
 * Check if the logged-in user is an admin.
 *
 * @return bool
 */
function is_admin() {
    return is_logged_in() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Enforce authentication. Redirects to login page if unauthorized.
 *
 * @return void
 */
function require_login() {
    if (!is_logged_in()) {
        set_flash('danger', 'Please log in to access this page.');
        redirect('/PortfolioForge/login.php');
    }
}

/**
 * Enforce admin role. Redirects to main dashboard if unauthorized.
 *
 * @return void
 */
function require_admin() {
    require_login();
    if (!is_admin()) {
        set_flash('danger', 'Access denied. Administrator privileges required.');
        redirect('/PortfolioForge/dashboard/index.php');
    }
}

/**
 * Admin accounts don't need a personal portfolio — send them to the Admin Panel instead
 * of the regular user portfolio dashboard.
 *
 * @return void
 */
function block_admin_from_dashboard() {
    if (is_admin()) {
        redirect('/PortfolioForge/admin/dashboard.php');
    }
}
