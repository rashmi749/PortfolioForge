<?php
// admin/dashboard.php - Admin Dashboard Home
$page_title = 'Admin Control Panel';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';

// Enforce admin privileges
require_admin();

require_once __DIR__ . '/../includes/header.php';

// Fetch Admin Stats
try {
    // Total Users
    $total_users = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();

    // Total Portfolios
    $total_portfolios = $pdo->query("SELECT COUNT(*) FROM portfolios")->fetchColumn();

    // Total Inquiries
    $total_messages = $pdo->query("SELECT COUNT(*) FROM contacts")->fetchColumn();

    // Layout / Template usage breakdown
    $layout_counts = [];
    $stmt = $pdo->query("SELECT layout, COUNT(*) as cnt FROM portfolios GROUP BY layout");
    foreach ($stmt->fetchAll() as $row) {
        $layout_counts[$row['layout']] = (int) $row['cnt'];
    }
} catch (PDOException $e) {
    $total_users = 0;
    $total_portfolios = 0;
    $total_messages = 0;
    $layout_counts = [];
}
?>

<div class="row g-4">
    <!-- Admin Sidebar Navigation -->
    <div class="col-md-3">
        <div class="pf-dashboard-sidebar pf-card border-0">
            <h4 class="h6 text-muted uppercase fw-bold mb-3 px-3">Administration</h4>
            <nav class="nav flex-column gap-1">
                <a class="pf-sidebar-link active" href="/PortfolioForge/admin/dashboard.php">
                    <i class="bi bi-bar-chart-fill"></i> Admin Stats
                </a>
                <a class="pf-sidebar-link" href="/PortfolioForge/admin/users.php">
                    <span>👥</span> Manage Users
                </a>
                <a class="pf-sidebar-link" href="/PortfolioForge/admin/portfolios.php">
                    <span>💼</span> Portfolios
                </a>
            </nav>
            <hr class="my-4 border-light-subtle">
            <a href="/PortfolioForge/logout.php" class="btn btn-outline-danger w-100 rounded-pill btn-sm">Logout</a>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="col-md-9">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="pf-font-title h2 mb-1">System Administration</h1>
                <p class="text-muted small mb-0">Platform overview and moderator console.</p>
            </div>
        </div>

        <!-- System Stats -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="pf-card border-0 p-4">
                    <span class="text-muted small fw-bold">TOTAL REGISTERED USERS</span>
                    <h3 class="display-5 pf-font-title mt-2 mb-0"><?php echo number_format($total_users); ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="pf-card border-0 p-4">
                    <span class="text-muted small fw-bold">ACTIVE PORTFOLIOS</span>
                    <h3 class="display-5 pf-font-title mt-2 mb-0"><?php echo number_format($total_portfolios); ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="pf-card border-0 p-4">
                    <span class="text-muted small fw-bold">MESSAGES PROCESSED</span>
                    <h3 class="display-5 pf-font-title mt-2 mb-0"><?php echo number_format($total_messages); ?></h3>
                </div>
            </div>
        </div>

        <!-- Layout / Template Usage -->
        <div class="pf-card border-0 p-5 mb-4">
            <h2 class="pf-font-title h4 mb-1">Portfolio Layout Usage</h2>
            <p class="text-muted small mb-4">Which templates creators are choosing for their public portfolio.</p>
            <div class="row g-4">
                <?php foreach (pf_get_layouts() as $layout_id => $layout_info): ?>
                    <?php $count = $layout_counts[$layout_id] ?? 0; ?>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background: rgba(0,0,0,0.02);">
                            <div class="pf-feature-icon pf-feature-icon-sm" style="background: <?php echo escape($layout_info['preview_bg']); ?>; color: <?php echo escape($layout_info['preview_fg']); ?>;">
                                <i class="bi <?php echo escape($layout_info['icon']); ?>"></i>
                            </div>
                            <div>
                                <div class="fw-bold"><?php echo escape($layout_info['name']); ?></div>
                                <small class="text-muted"><?php echo number_format($count); ?> portfolio<?php echo $count === 1 ? '' : 's'; ?></small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Quick Administration Info -->
        <div class="pf-card border-0 p-5">
            <h2 class="pf-font-title h4 mb-3">System Admin Functions</h2>
            <p class="text-muted small">Moderate platforms' profiles and user registration status.</p>
            
            <div class="list-group list-group-flush mt-4">
                <div class="list-group-item bg-transparent d-flex justify-content-between align-items-center py-3 border-light-subtle">
                    <div>
                        <h6 class="mb-1 fw-bold">User Accounts Moderation</h6>
                        <p class="text-muted small mb-0">List active accounts, delete accounts, or promote standard accounts to Administrators.</p>
                    </div>
                    <a href="/PortfolioForge/admin/users.php" class="btn pf-btn-secondary btn-sm px-3">Manage Users &rarr;</a>
                </div>
                <div class="list-group-item bg-transparent d-flex justify-content-between align-items-center py-3 border-0">
                    <div>
                        <h6 class="mb-1 fw-bold">Portfolios Moderation</h6>
                        <p class="text-muted small mb-0">Browse and verify all created profiles. Reset view metrics or delete offensive portfolios.</p>
                    </div>
                    <a href="/PortfolioForge/admin/portfolios.php" class="btn pf-btn-secondary btn-sm px-3">Manage Portfolios &rarr;</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
