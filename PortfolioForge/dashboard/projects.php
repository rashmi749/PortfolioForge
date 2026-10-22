<?php
// dashboard/projects.php - Manage Portfolio Projects
$page_title = 'Manage Projects';
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
$project_to_edit = null;

// Handle Delete Action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $project_id = intval($_GET['id']);
    try {
        // Find project and verify ownership
        $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ? AND portfolio_id = ?");
        $stmt->execute([$project_id, $portfolio_id]);
        $project = $stmt->fetch();
        
        if ($project) {
            // Delete image file if exists
            if (!empty($project['image'])) {
                $file_path = __DIR__ . '/../uploads/' . $project['image'];
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
            // Delete record
            $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
            $stmt->execute([$project_id]);
            set_flash('success', 'Project deleted successfully.');
        }
    } catch (PDOException $e) {
        set_flash('danger', 'Failed to delete project.');
    }
    redirect('/PortfolioForge/dashboard/projects.php');
}

// Handle Edit Fetch
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $project_id = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ? AND portfolio_id = ?");
    $stmt->execute([$project_id, $portfolio_id]);
    $project_to_edit = $stmt->fetch();
}

// Handle Form Submission (Add or Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF token validation failed.");
    }

    $project_id = isset($_POST['project_id']) ? intval($_POST['project_id']) : null;
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $link = trim($_POST['link'] ?? '');
    $github_link = trim($_POST['github_link'] ?? '');

    if (empty($title)) {
        $errors[] = 'Project title is required.';
    }

    if (empty($errors)) {
        try {
            $image_filename = null;
            
            // Handle existing image if updating
            if ($project_id) {
                $stmt = $pdo->prepare("SELECT image FROM projects WHERE id = ? AND portfolio_id = ?");
                $stmt->execute([$project_id, $portfolio_id]);
                $image_filename = $stmt->fetchColumn();
            }

            // Process new file upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                $allowed_images = [
                    'image/jpeg' => 'jpg',
                    'image/png'  => 'png',
                    'image/webp' => 'webp',
                    'image/gif'  => 'gif'
                ];
                $upload_dir = __DIR__ . '/../uploads/';
                $uploaded = upload_file($_FILES['image'], $allowed_images, 2 * 1024 * 1024, $upload_dir);
                
                // Unlink old one if applicable
                if (!empty($image_filename) && file_exists($upload_dir . $image_filename)) {
                    unlink($upload_dir . $image_filename);
                }
                $image_filename = $uploaded;
            }

            if ($project_id) {
                // UPDATE
                $stmt = $pdo->prepare("UPDATE projects SET title = ?, description = ?, link = ?, github_link = ?, image = ? WHERE id = ? AND portfolio_id = ?");
                $stmt->execute([$title, $description, $link, $github_link, $image_filename, $project_id, $portfolio_id]);
                set_flash('success', 'Project updated successfully.');
            } else {
                // INSERT
                $stmt = $pdo->prepare("INSERT INTO projects (portfolio_id, title, description, link, github_link, image) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$portfolio_id, $title, $description, $link, $github_link, $image_filename]);
                set_flash('success', 'Project added successfully.');
            }
            redirect('/PortfolioForge/dashboard/projects.php');
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
    }
}

// Fetch all existing projects
try {
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE portfolio_id = ? ORDER BY created_at DESC");
    $stmt->execute([$portfolio_id]);
    $projects = $stmt->fetchAll();
} catch (PDOException $e) {
    $projects = [];
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
                <a class="pf-sidebar-link active" href="/PortfolioForge/dashboard/projects.php">
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
        <!-- Form Panel -->
        <div class="pf-card border-0 p-5 mb-4">
            <h2 class="pf-font-title h3 mb-4">
                <?php echo $project_to_edit ? 'Edit Project' : 'Add New Project'; ?>
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

            <form action="/PortfolioForge/dashboard/projects.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo escape(generate_csrf_token()); ?>">
                
                <?php if ($project_to_edit): ?>
                    <input type="hidden" name="project_id" value="<?php echo $project_to_edit['id']; ?>">
                <?php endif; ?>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="title" class="form-label small fw-bold">Project Title</label>
                        <input type="text" class="form-control" id="title" name="title" value="<?php echo escape($project_to_edit['title'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="image" class="form-label small fw-bold">Project Image Thumbnail</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        <small class="text-muted d-block mt-1">JPG, PNG, WEBP. Max 2MB.</small>
                    </div>
                    <div class="col-md-6">
                        <label for="link" class="form-label small fw-bold">Live Demo URL (Optional)</label>
                        <input type="url" class="form-control" id="link" name="link" value="<?php echo escape($project_to_edit['link'] ?? ''); ?>" placeholder="https://example.com">
                    </div>
                    <div class="col-md-6">
                        <label for="github_link" class="form-label small fw-bold">GitHub Repository URL (Optional)</label>
                        <input type="url" class="form-control" id="github_link" name="github_link" value="<?php echo escape($project_to_edit['github_link'] ?? ''); ?>" placeholder="https://github.com/username/project">
                    </div>
                    <div class="col-12">
                        <label for="description" class="form-label small fw-bold">Project Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4" required><?php echo escape($project_to_edit['description'] ?? ''); ?></textarea>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn pf-btn-primary py-2 px-4">
                        <?php echo $project_to_edit ? 'Update Project' : 'Add Project'; ?>
                    </button>
                    <?php if ($project_to_edit): ?>
                        <a href="/PortfolioForge/dashboard/projects.php" class="btn pf-btn-secondary py-2 px-4 ms-2">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Projects Grid Showcase -->
        <div class="pf-card border-0 p-5">
            <h2 class="pf-font-title h4 mb-4">Your Projects</h2>
            
            <?php if (empty($projects)): ?>
                <p class="text-muted small mb-0">No projects added yet. Use the form above to add your first project.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table align-middle text-muted small">
                        <thead>
                            <tr class="text-dark">
                                <th scope="col" style="width: 80px;">Thumbnail</th>
                                <th scope="col">Title</th>
                                <th scope="col" style="width: 250px;">Links</th>
                                <th scope="col" class="text-end" style="width: 150px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($projects as $proj): ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($proj['image'])): ?>
                                            <img src="/PortfolioForge/uploads/<?php echo escape($proj['image']); ?>" alt="thumbnail" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-secondary rounded text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; font-weight: bold;">
                                                P
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-dark d-block"><?php echo escape($proj['title']); ?></span>
                                        <span class="d-inline-block text-truncate" style="max-width: 250px;">
                                            <?php echo escape($proj['description']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!empty($proj['link'])): ?>
                                            <a href="<?php echo escape($proj['link']); ?>" target="_blank" class="d-block text-decoration-none">Live Demo</a>
                                        <?php endif; ?>
                                        <?php if (!empty($proj['github_link'])): ?>
                                            <a href="<?php echo escape($proj['github_link']); ?>" target="_blank" class="d-block text-decoration-none text-success">GitHub Repo</a>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <a href="/PortfolioForge/dashboard/projects.php?action=edit&id=<?php echo $proj['id']; ?>" class="btn btn-sm btn-outline-primary me-2">Edit</a>
                                        <a href="/PortfolioForge/dashboard/projects.php?action=delete&id=<?php echo $proj['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this project?');">Delete</a>
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
