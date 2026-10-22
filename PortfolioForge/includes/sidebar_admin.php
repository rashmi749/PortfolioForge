<?php
/**
 * Reusable Admin Sidebar Component
 * Set $admin_active_page to: 'overview','users','portfolios'
 */
?>
<div class="col-md-3">
    <div class="pf-dashboard-sidebar pf-card border-0">
        <h4 class="h6 text-muted fw-bold mb-3 px-3" style="text-transform:uppercase; letter-spacing:.08em;">Administration</h4>
        <nav class="nav flex-column gap-1">
            <a class="pf-sidebar-link <?php echo ($admin_active_page === 'overview') ? 'active' : ''; ?>" href="/PortfolioForge/admin/dashboard.php">
                <span>&#9783;</span> Admin Stats
            </a>
            <a class="pf-sidebar-link <?php echo ($admin_active_page === 'users') ? 'active' : ''; ?>" href="/PortfolioForge/admin/users.php">
                <span>&#128101;</span> Manage Users
            </a>
            <a class="pf-sidebar-link <?php echo ($admin_active_page === 'portfolios') ? 'active' : ''; ?>" href="/PortfolioForge/admin/portfolios.php">
                <span>&#128188;</span> Portfolios
            </a>
        </nav>
        <hr class="my-4 border-light-subtle">
        <a href="/PortfolioForge/logout.php" class="btn btn-outline-danger w-100 rounded-pill btn-sm">Logout</a>
    </div>
</div>
