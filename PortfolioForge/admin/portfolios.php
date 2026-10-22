<?php
// admin/portfolios.php - Moderate Portfolio Profiles
$page_title = 'Manage Portfolios';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';

// Enforce admin privileges
require_admin();

// Process Actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $target_pf_id = intval($_GET['id']);
    $action = $_GET['action'];

    try {
        if ($action === 'reset_views') {
            $stmt = $pdo->prepare("UPDATE portfolios SET views = 0 WHERE id = ?");
            $stmt->execute([$target_pf_id]);
            set_flash('success', 'Portfolio view metrics reset to 0.');
        } elseif ($action === 'set_layout') {
            $new_layout = pf_resolve_layout($_GET['layout'] ?? 'minimal');
            $stmt = $pdo->prepare("UPDATE portfolios SET layout = ? WHERE id = ?");
            $stmt->execute([$new_layout, $target_pf_id]);
            set_flash('success', 'Portfolio layout updated.');
        } elseif ($action === 'reset_profile') {
            // Fetch and delete profile image file
            $stmt = $pdo->prepare("SELECT profile_image FROM portfolios WHERE id = ?");
            $stmt->execute([$target_pf_id]);
            $avatar = $stmt->fetchColumn();

            if (!empty($avatar)) {
                $file = __DIR__ . '/../uploads/' . $avatar;
                if (file_exists($file)) {
                    unlink($file);
                }
            }

            // Revert details to clean defaults
            $stmt = $pdo->prepare("UPDATE portfolios SET title = 'Professional Developer', bio = 'Welcome to my portfolio.', profile_image = NULL, github = NULL, linkedin = NULL, twitter = NULL, website = NULL, views = 0, theme = 'apple-light', layout = 'minimal' WHERE id = ?");
            $stmt->execute([$target_pf_id]);
            
            // Delete associated projects/skills/timeline history
            $stmt = $pdo->prepare("DELETE FROM projects WHERE portfolio_id = ?");
            $stmt->execute([$target_pf_id]);
            $stmt = $pdo->prepare("DELETE FROM skills WHERE portfolio_id = ?");
            $stmt->execute([$target_pf_id]);
            $stmt = $pdo->prepare("DELETE FROM education WHERE portfolio_id = ?");
            $stmt->execute([$target_pf_id]);
            $stmt = $pdo->prepare("DELETE FROM experience WHERE portfolio_id = ?");
            $stmt->execute([$target_pf_id]);
            $stmt = $pdo->prepare("DELETE FROM resumes WHERE portfolio_id = ?");
            $stmt->execute([$target_pf_id]);

            set_flash('success', 'Portfolio has been reset to default values.');
        }
    } catch (PDOException $e) {
        set_flash('danger', 'Moderator action failed.');
    }
    redirect('/PortfolioForge/admin/portfolios.php');
}

require_once __DIR__ . '/../includes/header.php';

// Fetch Portfolios List
try {
    $stmt = $pdo->prepare("SELECT p.*, u.username FROM portfolios p JOIN users u ON p.user_id = u.id ORDER BY p.views DESC");
    $stmt->execute();
    $portfolios_list = $stmt->fetchAll();
} catch (PDOException $e) {
    $portfolios_list = [];
}
?>

<div class="row g-4">
    <!-- Admin Sidebar Navigation -->
    <div class="col-md-3">
        <div class="pf-dashboard-sidebar pf-card border-0">
            <h4 class="h6 text-muted uppercase fw-bold mb-3 px-3">Administration</h4>
            <nav class="nav flex-column gap-1">
                <a class="pf-sidebar-link" href="/PortfolioForge/admin/dashboard.php">
                    <i class="bi bi-bar-chart-fill"></i> Admin Stats
                </a>
                <a class="pf-sidebar-link" href="/PortfolioForge/admin/users.php">
                    <span>👥</span> Manage Users
                </a>
                <a class="pf-sidebar-link active" href="/PortfolioForge/admin/portfolios.php">
                    <span>💼</span> Portfolios
                </a>
            </nav>
            <hr class="my-4 border-light-subtle">
            <a href="/PortfolioForge/logout.php" class="btn btn-outline-danger w-100 rounded-pill btn-sm">Logout</a>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="col-md-9">
        <div class="pf-card border-0 p-5">
            <h1 class="pf-font-title h3 mb-4">Moderate Portfolios</h1>

            <?php if (empty($portfolios_list)): ?>
                <p class="text-muted small mb-0">No active portfolios found on the system.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table align-middle text-muted small">
                        <thead>
                            <tr class="text-dark">
                                <th scope="col">Owner</th>
                                <th scope="col">Job Title</th>
                                <th scope="col">Views</th>
                                <th scope="col">Theme</th>
                                <th scope="col">Layout</th>
                                <th scope="col" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($portfolios_list as $pf): ?>
                                <tr>
                                    <td>
                                        <span class="fw-bold text-dark d-block"><?php echo escape($pf['full_name']); ?></span>
                                        <small class="text-muted">@<?php echo escape($pf['username']); ?></small>
                                    </td>
                                    <td><?php echo escape($pf['title'] ?: '(No title)'); ?></td>
                                    <td><?php echo number_format($pf['views']); ?></td>
                                    <td><span class="badge bg-secondary-subtle text-secondary"><?php echo escape($pf['theme']); ?></span></td>
                                    <td>
                                        <form method="GET" action="/PortfolioForge/admin/portfolios.php" class="d-inline">
                                            <input type="hidden" name="action" value="set_layout">
                                            <input type="hidden" name="id" value="<?php echo $pf['id']; ?>">
                                            <select name="layout" class="form-select form-select-sm" style="min-width: 140px;" onchange="this.form.submit()">
                                                <?php foreach (pf_get_layouts() as $layout_id => $layout_info): ?>
                                                    <option value="<?php echo escape($layout_id); ?>" <?php echo pf_resolve_layout($pf['layout'] ?? 'minimal') === $layout_id ? 'selected' : ''; ?>><?php echo escape($layout_info['name']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </form>
                                    </td>
                                    <td class="text-end text-nowrap">
                                        <a href="/PortfolioForge/portfolio.php?username=<?php echo escape($pf['username']); ?>" target="_blank" class="btn btn-sm btn-outline-info me-2">
                                            View Page
                                        </a>
                                        <a href="/PortfolioForge/admin/portfolios.php?action=reset_views&id=<?php echo $pf['id']; ?>" class="btn btn-sm btn-outline-primary me-2">
                                            Reset Views
                                        </a>
                                        <a href="/PortfolioForge/admin/portfolios.php?action=reset_profile&id=<?php echo $pf['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to reset this portfolio to default? All added projects, skills, timelines, and resumes will be permanently deleted.');">
                                            Reset Profile
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
