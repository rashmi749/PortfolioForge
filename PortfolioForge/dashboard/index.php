<?php
// dashboard/index.php - User Dashboard Home
$page_title = 'Dashboard';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';

// Enforce login
require_login();
block_admin_from_dashboard();

require_once __DIR__ . '/../includes/header.php';

$user_id = $_SESSION['user_id'];
$portfolio = get_user_portfolio($pdo, $user_id);

$portfolio_id = $portfolio['id'];

// Get Stats
try {
    // Total Projects
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM projects WHERE portfolio_id = ?");
    $stmt->execute([$portfolio_id]);
    $total_projects = $stmt->fetchColumn();

    // Total Skills
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM skills WHERE portfolio_id = ?");
    $stmt->execute([$portfolio_id]);
    $total_skills = $stmt->fetchColumn();

    // Total Messages
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM contacts WHERE portfolio_id = ?");
    $stmt->execute([$portfolio_id]);
    $total_messages = $stmt->fetchColumn();

    // Unread Messages
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM contacts WHERE portfolio_id = ? AND is_read = 0");
    $stmt->execute([$portfolio_id]);
    $unread_messages = $stmt->fetchColumn();
} catch (PDOException $e) {
    $total_projects = 0;
    $total_skills = 0;
    $total_messages = 0;
    $unread_messages = 0;
}
?>

<div class="row g-4">
    <!-- Sidebar Navigation -->
    <div class="col-md-3">
        <div class="pf-dashboard-sidebar pf-card border-0">
            <h4 class="h6 text-muted uppercase fw-bold mb-3 px-3">Manage Profile</h4>
            <nav class="nav flex-column gap-1">
                <a class="pf-sidebar-link active" href="/PortfolioForge/dashboard/index.php">
                    <i class="bi bi-speedometer2"></i> Overview
                </a>
                <a class="pf-sidebar-link" href="/PortfolioForge/dashboard/profile.php">
                    <span>👤</span> Profile Info
                </a>
                <a class="pf-sidebar-link" href="/PortfolioForge/dashboard/projects.php">
                    <span>💼</span> Projects
                </a>
                <a class="pf-sidebar-link" href="/PortfolioForge/dashboard/skills.php">
                    <span>⚡</span> Skills
                </a>
                <a class="pf-sidebar-link" href="/PortfolioForge/dashboard/education.php">
                    <span>🎓</span> Education
                </a>
                <a class="pf-sidebar-link" href="/PortfolioForge/dashboard/experience.php">
                    <span>🏢</span> Experience
                </a>
                <a class="pf-sidebar-link d-flex justify-content-between align-items-center" href="/PortfolioForge/dashboard/messages.php">
                    <span>✉️ Messages</span>
                    <?php if ($unread_messages > 0): ?>
                        <span class="badge bg-danger rounded-pill"><?php echo $unread_messages; ?></span>
                    <?php endif; ?>
                </a>
            </nav>
            <hr class="my-4 border-light-subtle">
            <a href="/PortfolioForge/portfolio.php?username=<?php echo escape($_SESSION['username']); ?>" target="_blank" class="btn pf-btn-secondary w-100 mb-2">View Portfolio</a>
            <a href="/PortfolioForge/logout.php" class="btn btn-outline-danger w-100 rounded-pill btn-sm">Logout</a>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="col-md-9">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="pf-font-title h2 mb-1">Welcome back, <?php echo escape($portfolio['full_name']); ?>!</h1>
                <p class="text-muted small mb-0">Here is how your portfolio is doing today.</p>
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="pf-card border-0 p-4">
                    <span class="text-muted small fw-bold">PORTFOLIO VIEWS</span>
                    <h3 class="display-5 pf-font-title mt-2 mb-0"><?php echo number_format($portfolio['views']); ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="pf-card border-0 p-4">
                    <span class="text-muted small fw-bold">TOTAL PROJECTS</span>
                    <h3 class="display-5 pf-font-title mt-2 mb-0"><?php echo $total_projects; ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="pf-card border-0 p-4">
                    <span class="text-muted small fw-bold">UNREAD MESSAGES</span>
                    <h3 class="display-5 pf-font-title mt-2 mb-0 <?php echo $unread_messages > 0 ? 'text-danger' : ''; ?>">
                        <?php echo $unread_messages; ?>
                    </h3>
                </div>
            </div>
        </div>

        <!-- Quick Info Panel -->
        <div class="pf-card border-0 p-5">
            <h2 class="pf-font-title h4 mb-3">Portfolio Management Checklist</h2>
            <p class="text-muted small">Get the most out of PortfolioForge by setting up all areas of your dynamic CV page.</p>
            
            <div class="list-group list-group-flush mt-4 pf-checklist">
                <div class="list-group-item bg-transparent d-flex justify-content-between align-items-center py-3 border-light-subtle">
                    <div>
                        <h6 class="mb-1 fw-bold">Basic Information & Theme</h6>
                        <p class="text-muted small mb-0">Set your job title, professional bio, profile image, and switch between Light/Dark themes.</p>
                    </div>
                    <a href="/PortfolioForge/dashboard/profile.php" class="btn pf-btn-secondary btn-sm px-3">Edit</a>
                </div>
                <div class="list-group-item bg-transparent d-flex justify-content-between align-items-center py-3 border-light-subtle">
                    <div>
                        <h6 class="mb-1 fw-bold">Projects Showcase</h6>
                        <p class="text-muted small mb-0">Add details, image thumbnails, live demo link, and repository link to highlight your engineering work.</p>
                    </div>
                    <a href="/PortfolioForge/dashboard/projects.php" class="btn pf-btn-secondary btn-sm px-3">Manage</a>
                </div>
                <div class="list-group-item bg-transparent d-flex justify-content-between align-items-center py-3 border-0">
                    <div>
                        <h6 class="mb-1 fw-bold">Timeline Milestones</h6>
                        <p class="text-muted small mb-0">Add your academic background and professional history to compose a dynamic interactive resume.</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="/PortfolioForge/dashboard/education.php" class="btn pf-btn-secondary btn-sm">Education</a>
                        <a href="/PortfolioForge/dashboard/experience.php" class="btn pf-btn-secondary btn-sm">Experience</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
