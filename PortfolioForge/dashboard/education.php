<?php
// dashboard/education.php - Manage Education Timeline
$page_title = 'Manage Education';
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
$edu_to_edit = null;

// Handle Delete Action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $edu_id = intval($_GET['id']);
    try {
        $stmt = $pdo->prepare("DELETE FROM education WHERE id = ? AND portfolio_id = ?");
        $stmt->execute([$edu_id, $portfolio_id]);
        set_flash('success', 'Education record deleted successfully.');
    } catch (PDOException $e) {
        set_flash('danger', 'Failed to delete education record.');
    }
    redirect('/PortfolioForge/dashboard/education.php');
}

// Handle Edit Fetch
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $edu_id = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT * FROM education WHERE id = ? AND portfolio_id = ?");
    $stmt->execute([$edu_id, $portfolio_id]);
    $edu_to_edit = $stmt->fetch();
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF token validation failed.");
    }

    $edu_id = isset($_POST['edu_id']) ? intval($_POST['edu_id']) : null;
    $institution = trim($_POST['institution'] ?? '');
    $degree = trim($_POST['degree'] ?? '');
    $field_of_study = trim($_POST['field_of_study'] ?? '');
    $start_date = $_POST['start_date'] ?? '';
    $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
    $description = trim($_POST['description'] ?? '');

    if (empty($institution) || empty($degree) || empty($start_date)) {
        $errors[] = 'Institution, Degree, and Start Date are required.';
    }

    if (empty($errors)) {
        try {
            if ($edu_id) {
                // UPDATE
                $stmt = $pdo->prepare("UPDATE education SET institution = ?, degree = ?, field_of_study = ?, start_date = ?, end_date = ?, description = ? WHERE id = ? AND portfolio_id = ?");
                $stmt->execute([$institution, $degree, $field_of_study, $start_date, $end_date, $description, $edu_id, $portfolio_id]);
                set_flash('success', 'Education updated successfully.');
            } else {
                // INSERT
                $stmt = $pdo->prepare("INSERT INTO education (portfolio_id, institution, degree, field_of_study, start_date, end_date, description) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$portfolio_id, $institution, $degree, $field_of_study, $start_date, $end_date, $description]);
                set_flash('success', 'Education added successfully.');
            }
            redirect('/PortfolioForge/dashboard/education.php');
        } catch (PDOException $e) {
            $errors[] = 'An error occurred while saving the education record.';
        }
    }
}

// Fetch all education records
try {
    $stmt = $pdo->prepare("SELECT * FROM education WHERE portfolio_id = ? ORDER BY start_date DESC");
    $stmt->execute([$portfolio_id]);
    $education_list = $stmt->fetchAll();
} catch (PDOException $e) {
    $education_list = [];
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
                <a class="pf-sidebar-link active" href="/PortfolioForge/dashboard/education.php">
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
        <!-- Form Panel -->
        <div class="pf-card border-0 p-5 mb-4">
            <h2 class="pf-font-title h3 mb-4">
                <?php echo $edu_to_edit ? 'Edit Education' : 'Add New Education'; ?>
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

            <form action="/PortfolioForge/dashboard/education.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo escape(generate_csrf_token()); ?>">
                
                <?php if ($edu_to_edit): ?>
                    <input type="hidden" name="edu_id" value="<?php echo $edu_to_edit['id']; ?>">
                <?php endif; ?>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="institution" class="form-label small fw-bold">Institution / School Name</label>
                        <input type="text" class="form-control" id="institution" name="institution" value="<?php echo escape($edu_to_edit['institution'] ?? ''); ?>" required placeholder="e.g. Stanford University">
                    </div>
                    <div class="col-md-6">
                        <label for="degree" class="form-label small fw-bold">Degree</label>
                        <input type="text" class="form-control" id="degree" name="degree" value="<?php echo escape($edu_to_edit['degree'] ?? ''); ?>" required placeholder="e.g. Bachelor of Science">
                    </div>
                    <div class="col-md-4">
                        <label for="field_of_study" class="form-label small fw-bold">Field of Study</label>
                        <input type="text" class="form-control" id="field_of_study" name="field_of_study" value="<?php echo escape($edu_to_edit['field_of_study'] ?? ''); ?>" placeholder="e.g. Computer Science">
                    </div>
                    <div class="col-md-4">
                        <label for="start_date" class="form-label small fw-bold">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo escape($edu_to_edit['start_date'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label for="end_date" class="form-label small fw-bold">End Date (Leave blank if ongoing)</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo escape($edu_to_edit['end_date'] ?? ''); ?>">
                    </div>
                    <div class="col-12">
                        <label for="description" class="form-label small fw-bold">Description / Honors (Optional)</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Describe achievements, courseworks, or activities..."><?php echo escape($edu_to_edit['description'] ?? ''); ?></textarea>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn pf-btn-primary py-2 px-4">
                        <?php echo $edu_to_edit ? 'Update Education' : 'Add Education'; ?>
                    </button>
                    <?php if ($edu_to_edit): ?>
                        <a href="/PortfolioForge/dashboard/education.php" class="btn pf-btn-secondary py-2 px-4 ms-2">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- List Panel -->
        <div class="pf-card border-0 p-5">
            <h2 class="pf-font-title h4 mb-4">Your Education History</h2>
            
            <?php if (empty($education_list)): ?>
                <p class="text-muted small mb-0">No education history recorded yet. Use the form above to build your timeline.</p>
            <?php else: ?>
                <div class="pf-timeline">
                    <?php foreach ($education_list as $edu): ?>
                        <div class="pf-timeline-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h4 class="h6 mb-1 text-dark fw-bold"><?php echo escape($edu['degree']); ?> in <?php echo escape($edu['field_of_study']); ?></h4>
                                    <p class="mb-1 text-muted fw-medium small"><?php echo escape($edu['institution']); ?></p>
                                    <p class="mb-2 text-muted small">
                                        <?php 
                                            $start = date('M Y', strtotime($edu['start_date']));
                                            $end = $edu['end_date'] ? date('M Y', strtotime($edu['end_date'])) : 'Present';
                                            echo "$start - $end";
                                        ?>
                                    </p>
                                    <?php if (!empty($edu['description'])): ?>
                                        <p class="text-muted small mb-0"><?php echo nl2br(escape($edu['description'])); ?></p>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <a href="/PortfolioForge/dashboard/education.php?action=edit&id=<?php echo $edu['id']; ?>" class="btn btn-sm btn-outline-primary me-2">Edit</a>
                                    <a href="/PortfolioForge/dashboard/education.php?action=delete&id=<?php echo $edu['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
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
