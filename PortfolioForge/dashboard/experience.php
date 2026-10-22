<?php
// dashboard/experience.php - Manage Professional Experience Timeline
$page_title = 'Manage Experience';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';

// Enforce login
require_login();
block_admin_from_dashboard();


$user_id = $_SESSION['user_id'];
$portfolio = get_user_portfolio($pdo, $user_id);
$portfolio_id = $portfolio['id'];

// Fetch unread messages count for sidebar badge
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM contacts WHERE portfolio_id = ? AND is_read = 0");
    $stmt->execute([$portfolio_id]);
    $unread_messages = $stmt->fetchColumn();
} catch (PDOException $e) {
    $unread_messages = 0;
}

$errors = [];
$exp_to_edit = null;

// Handle Delete Action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $exp_id = intval($_GET['id']);
    try {
        $stmt = $pdo->prepare("DELETE FROM experience WHERE id = ? AND portfolio_id = ?");
        $stmt->execute([$exp_id, $portfolio_id]);
        set_flash('success', 'Experience record deleted successfully.');
    } catch (PDOException $e) {
        set_flash('danger', 'Failed to delete experience record.');
    }
    redirect('/PortfolioForge/dashboard/experience.php');
}

// Handle Edit Fetch
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $exp_id = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT * FROM experience WHERE id = ? AND portfolio_id = ?");
    $stmt->execute([$exp_id, $portfolio_id]);
    $exp_to_edit = $stmt->fetch();
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF token validation failed.");
    }

    $exp_id = isset($_POST['exp_id']) ? intval($_POST['exp_id']) : null;
    $company = trim($_POST['company'] ?? '');
    $position = trim($_POST['position'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $start_date = $_POST['start_date'] ?? '';
    $current = isset($_POST['current']) ? 1 : 0;
    $end_date = ($current === 1 || empty($_POST['end_date'])) ? null : $_POST['end_date'];
    $description = trim($_POST['description'] ?? '');

    if (empty($company) || empty($position) || empty($start_date)) {
        $errors[] = 'Company, Position, and Start Date are required.';
    }

    if (empty($errors)) {
        try {
            if ($exp_id) {
                // UPDATE
                $stmt = $pdo->prepare("UPDATE experience SET company = ?, position = ?, location = ?, start_date = ?, end_date = ?, current = ?, description = ? WHERE id = ? AND portfolio_id = ?");
                $stmt->execute([$company, $position, $location, $start_date, $end_date, $current, $description, $exp_id, $portfolio_id]);
                set_flash('success', 'Experience updated successfully.');
            } else {
                // INSERT
                $stmt = $pdo->prepare("INSERT INTO experience (portfolio_id, company, position, location, start_date, end_date, current, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$portfolio_id, $company, $position, $location, $start_date, $end_date, $current, $description]);
                set_flash('success', 'Experience added successfully.');
            }
            redirect('/PortfolioForge/dashboard/experience.php');
        } catch (PDOException $e) {
            $errors[] = 'An error occurred while saving the experience record.';
        }
    }
}

// Fetch all experience records
try {
    $stmt = $pdo->prepare("SELECT * FROM experience WHERE portfolio_id = ? ORDER BY start_date DESC");
    $stmt->execute([$portfolio_id]);
    $experience_list = $stmt->fetchAll();
} catch (PDOException $e) {
    $experience_list = [];
}
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row g-4">
    <!-- Sidebar Navigation -->
    <div class="col-md-3">
        <div class="pf-dashboard-sidebar pf-card border-0">
            <h4 class="h6 text-muted uppercase fw-bold mb-3 px-3">Manage Profile</h4>
            <nav class="nav flex-column gap-1">
                <a class="pf-sidebar-link" href="/PortfolioForge/dashboard/index.php">
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
                <a class="pf-sidebar-link active" href="/PortfolioForge/dashboard/experience.php">
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
        <!-- Form Panel -->
        <div class="pf-card border-0 p-5 mb-4">
            <h2 class="pf-font-title h3 mb-4">
                <?php echo $exp_to_edit ? 'Edit Experience' : 'Add New Experience'; ?>
            </h2>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger border-0 rounded-3 shadow-sm small">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo escape($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="/PortfolioForge/dashboard/experience.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo escape(generate_csrf_token()); ?>">
                
                <?php if ($exp_to_edit): ?>
                    <input type="hidden" name="exp_id" value="<?php echo $exp_to_edit['id']; ?>">
                <?php endif; ?>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="company" class="form-label small fw-bold">Company / Employer Name</label>
                        <input type="text" class="form-control" id="company" name="company" value="<?php echo escape($exp_to_edit['company'] ?? ''); ?>" required placeholder="e.g. Google LLC">
                    </div>
                    <div class="col-md-6">
                        <label for="position" class="form-label small fw-bold">Job Position / Title</label>
                        <input type="text" class="form-control" id="position" name="position" value="<?php echo escape($exp_to_edit['position'] ?? ''); ?>" required placeholder="e.g. Senior Software Engineer">
                    </div>
                    <div class="col-md-4">
                        <label for="location" class="form-label small fw-bold">Location</label>
                        <input type="text" class="form-control" id="location" name="location" value="<?php echo escape($exp_to_edit['location'] ?? ''); ?>" placeholder="e.g. Mountain View, CA">
                    </div>
                    <div class="col-md-4">
                        <label for="start_date" class="form-label small fw-bold">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo escape($exp_to_edit['start_date'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label for="end_date" class="form-label small fw-bold">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo escape($exp_to_edit['end_date'] ?? ''); ?>" <?php echo (isset($exp_to_edit['current']) && $exp_to_edit['current'] == 1) ? 'disabled' : ''; ?>>
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="current" name="current" value="1" <?php echo (isset($exp_to_edit['current']) && $exp_to_edit['current'] == 1) ? 'checked' : ''; ?> onchange="document.getElementById('end_date').disabled = this.checked;">
                            <label class="form-check-label small" for="current">
                                I currently work here
                            </label>
                        </div>
                    </div>
                    <div class="col-12">
                        <label for="description" class="form-label small fw-bold">Job Description (Optional)</label>
                        <textarea class="form-control" id="description" name="description" rows="4" placeholder="Describe your duties, projects, and accomplishments..."><?php echo escape($exp_to_edit['description'] ?? ''); ?></textarea>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn pf-btn-primary py-2 px-4">
                        <?php echo $exp_to_edit ? 'Update Experience' : 'Add Experience'; ?>
                    </button>
                    <?php if ($exp_to_edit): ?>
                        <a href="/PortfolioForge/dashboard/experience.php" class="btn pf-btn-secondary py-2 px-4 ms-2">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- List Panel -->
        <div class="pf-card border-0 p-5">
            <h2 class="pf-font-title h4 mb-4">Your Experience History</h2>
            
            <?php if (empty($experience_list)): ?>
                <p class="text-muted small mb-0">No professional history recorded yet. Use the form above to build your timeline.</p>
            <?php else: ?>
                <div class="pf-timeline">
                    <?php foreach ($experience_list as $exp): ?>
                        <div class="pf-timeline-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h4 class="h6 mb-1 text-dark fw-bold"><?php echo escape($exp['position']); ?></h4>
                                    <p class="mb-1 text-muted fw-medium small"><?php echo escape($exp['company']); ?> <?php echo !empty($exp['location']) ? ' - ' . escape($exp['location']) : ''; ?></p>
                                    <p class="mb-2 text-muted small">
                                        <?php 
                                            $start = date('M Y', strtotime($exp['start_date']));
                                            $end = ($exp['current'] == 1) ? 'Present' : date('M Y', strtotime($exp['end_date']));
                                            echo "$start - $end";
                                        ?>
                                    </p>
                                    <?php if (!empty($exp['description'])): ?>
                                        <p class="text-muted small mb-0"><?php echo nl2br(escape($exp['description'])); ?></p>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <a href="/PortfolioForge/dashboard/experience.php?action=edit&id=<?php echo $exp['id']; ?>" class="btn btn-sm btn-outline-primary me-2">Edit</a>
                                    <a href="/PortfolioForge/dashboard/experience.php?action=delete&id=<?php echo $exp['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
