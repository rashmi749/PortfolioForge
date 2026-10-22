<?php
// dashboard/profile.php - Edit Basic Profile, Socials, Theme, and Resume
$page_title = 'Edit Profile';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';

// Enforce login
require_login();
block_admin_from_dashboard();


$user_id = $_SESSION['user_id'];
$portfolio = get_user_portfolio($pdo, $user_id);
$portfolio_id = $portfolio['id'];

// Fetch current resume
try {
    $stmt = $pdo->prepare("SELECT * FROM resumes WHERE portfolio_id = ?");
    $stmt->execute([$portfolio_id]);
    $resume = $stmt->fetch();
} catch (PDOException $e) {
    $resume = false;
}

// Fetch unread messages count for sidebar badge
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM contacts WHERE portfolio_id = ? AND is_read = 0");
    $stmt->execute([$portfolio_id]);
    $unread_messages = $stmt->fetchColumn();
} catch (PDOException $e) {
    $unread_messages = 0;
}

$errors = [];
$success_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF check
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF token validation failed.");
    }

    $full_name = trim($_POST['full_name'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $github = trim($_POST['github'] ?? '');
    $linkedin = trim($_POST['linkedin'] ?? '');
    $twitter = trim($_POST['twitter'] ?? '');
    $website = trim($_POST['website'] ?? '');
    $theme = trim($_POST['theme'] ?? 'apple-light');
    $layout = pf_resolve_layout(trim($_POST['layout'] ?? 'minimal'));
    $available_for_hire = isset($_POST['available_for_hire']) ? 1 : 0;

    if (empty($full_name)) {
        $errors[] = 'Full Name is required.';
    }

    // Process file uploads if no core errors
    if (empty($errors)) {
        $profile_image = $portfolio['profile_image'];
        $resume_file = $resume ? $resume['file_path'] : '';

        // 1. Profile Image Upload
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            try {
                $allowed_images = [
                    'image/jpeg' => 'jpg',
                    'image/png'  => 'png',
                    'image/webp' => 'webp',
                    'image/gif'  => 'gif'
                ];
                $upload_dir = __DIR__ . '/../uploads/';
                $uploaded_filename = upload_file($_FILES['profile_image'], $allowed_images, 2 * 1024 * 1024, $upload_dir);
                
                // Delete old image if exists
                if (!empty($profile_image) && file_exists($upload_dir . $profile_image)) {
                    unlink($upload_dir . $profile_image);
                }
                $profile_image = $uploaded_filename;
            } catch (Exception $e) {
                $errors[] = 'Avatar image error: ' . $e->getMessage();
            }
        }

        // 2. Resume File Upload
        if (isset($_FILES['resume']) && $_FILES['resume']['error'] !== UPLOAD_ERR_NO_FILE) {
            try {
                $allowed_files = [
                    'application/pdf' => 'pdf'
                ];
                $upload_dir = __DIR__ . '/../uploads/';
                $uploaded_resume = upload_file($_FILES['resume'], $allowed_files, 5 * 1024 * 1024, $upload_dir);

                // Delete old resume if exists
                if (!empty($resume_file) && file_exists($upload_dir . $resume_file)) {
                    unlink($upload_dir . $resume_file);
                }
                $resume_file = $uploaded_resume;
                
                // Write or update resumes table
                if ($resume) {
                    $stmt = $pdo->prepare("UPDATE resumes SET file_path = ?, uploaded_at = CURRENT_TIMESTAMP WHERE portfolio_id = ?");
                    $stmt->execute([$resume_file, $portfolio_id]);
                } else {
                    $stmt = $pdo->prepare("INSERT INTO resumes (portfolio_id, file_path, label) VALUES (?, ?, 'Curriculum Vitae')");
                    $stmt->execute([$portfolio_id, $resume_file]);
                }
                // Refresh resume variable
                $stmt = $pdo->prepare("SELECT * FROM resumes WHERE portfolio_id = ?");
                $stmt->execute([$portfolio_id]);
                $resume = $stmt->fetch();

            } catch (Exception $e) {
                $errors[] = 'Resume upload error: ' . $e->getMessage();
            }
        }

        // Save Portfolio basic details if still no errors
        if (empty($errors)) {
            try {
                $stmt = $pdo->prepare("UPDATE portfolios SET full_name = ?, title = ?, bio = ?, profile_image = ?, github = ?, linkedin = ?, twitter = ?, website = ?, theme = ?, layout = ?, available_for_hire = ? WHERE id = ?");
                $stmt->execute([$full_name, $title, $bio, $profile_image, $github, $linkedin, $twitter, $website, $theme, $layout, $available_for_hire, $portfolio_id]);
                
                // Refresh portfolio details
                $portfolio = get_user_portfolio($pdo, $user_id);
                set_flash('success', 'Profile updated successfully.');
                redirect('/PortfolioForge/dashboard/profile.php');
            } catch (PDOException $e) {
                $errors[] = 'Failed to update database profile details.';
            }
        }
    }
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
                <a class="pf-sidebar-link active" href="/PortfolioForge/dashboard/profile.php">
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
        <div class="pf-card border-0 p-5">
            <h1 class="pf-font-title h3 mb-4">Edit Profile Info</h1>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger border-0 rounded-3 shadow-sm small">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo escape($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="/PortfolioForge/dashboard/profile.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo escape(generate_csrf_token()); ?>">

                <div class="row g-4 mb-4">
                    <!-- Image Preview & Input -->
                    <div class="col-md-3 text-center">
                        <label class="form-label d-block small fw-bold">Profile Picture</label>
                        <div class="mb-3">
                            <img id="avatar-preview" src="<?php echo !empty($portfolio['profile_image']) ? '/PortfolioForge/uploads/' . escape($portfolio['profile_image']) : 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=150&h=150&fit=crop'; ?>" alt="Preview" class="rounded-circle border" style="width: 120px; height: 120px; object-fit: cover;">
                        </div>
                        <input type="file" name="profile_image" class="form-control form-control-sm image-preview-input" data-preview-target="avatar-preview" accept="image/*">
                        <small class="text-muted d-block mt-1 xx-small">JPG, PNG, WEBP. Max 2MB.</small>
                    </div>

                    <!-- Personal Information -->
                    <div class="col-md-9">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="full_name" class="form-label small fw-bold">Full Name</label>
                                <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo escape($portfolio['full_name']); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="title" class="form-label small fw-bold">Job Title / Tagline</label>
                                <input type="text" class="form-control" id="title" name="title" value="<?php echo escape($portfolio['title']); ?>">
                            </div>
                            <div class="col-12">
                                <label for="bio" class="form-label small fw-bold">Professional Bio</label>
                                <textarea class="form-control" id="bio" name="bio" rows="4"><?php echo escape($portfolio['bio']); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-4 border-light-subtle">

                <!-- Social Profiles -->
                <h3 class="pf-font-title h5 mb-3">Online Links</h3>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="github" class="form-label small fw-bold">GitHub Profile URL</label>
                        <input type="url" class="form-control" id="github" name="github" value="<?php echo escape($portfolio['github']); ?>" placeholder="https://github.com/username">
                    </div>
                    <div class="col-md-6">
                        <label for="linkedin" class="form-label small fw-bold">LinkedIn Profile URL</label>
                        <input type="url" class="form-control" id="linkedin" name="linkedin" value="<?php echo escape($portfolio['linkedin']); ?>" placeholder="https://linkedin.com/in/username">
                    </div>
                    <div class="col-md-6">
                        <label for="twitter" class="form-label small fw-bold">Twitter Profile URL</label>
                        <input type="url" class="form-control" id="twitter" name="twitter" value="<?php echo escape($portfolio['twitter']); ?>" placeholder="https://twitter.com/username">
                    </div>
                    <div class="col-md-6">
                        <label for="website" class="form-label small fw-bold">Personal Website URL</label>
                        <input type="url" class="form-control" id="website" name="website" value="<?php echo escape($portfolio['website']); ?>" placeholder="https://example.com">
                    </div>
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="available_for_hire" name="available_for_hire" <?php echo !empty($portfolio['available_for_hire']) ? 'checked' : ''; ?>>
                            <label class="form-check-label small fw-bold" for="available_for_hire">Show "Available for Hire" badge on my portfolio</label>
                        </div>
                    </div>
                </div>

                <hr class="my-4 border-light-subtle">

                <!-- Portfolio Layout / Template Picker -->
                <h3 class="pf-font-title h5 mb-1">Portfolio Layout</h3>
                <p class="text-muted small mb-3">Choose the template used on your public portfolio page. You can change this any time.</p>
                <div class="row g-3 mb-4">
                    <?php
                    $current_layout = pf_resolve_layout($portfolio['layout'] ?? 'minimal');
                    foreach (pf_get_layouts() as $layout_id => $layout_info):
                    ?>
                        <div class="col-md-4">
                            <label class="pf-layout-option d-block h-100 <?php echo $current_layout === $layout_id ? 'active' : ''; ?>">
                                <input type="radio" name="layout" value="<?php echo escape($layout_id); ?>" <?php echo $current_layout === $layout_id ? 'checked' : ''; ?> class="d-none pf-layout-radio">
                                <div class="pf-layout-preview mb-3" style="background: <?php echo escape($layout_info['preview_bg']); ?>;">
                                    <i class="bi <?php echo escape($layout_info['icon']); ?>" style="color: <?php echo escape($layout_info['preview_fg']); ?>;"></i>
                                </div>
                                <h4 class="h6 pf-font-title mb-1"><?php echo escape($layout_info['name']); ?></h4>
                                <p class="text-muted x-small mb-2"><?php echo escape($layout_info['description']); ?></p>
                                <span class="pf-layout-check"><i class="bi bi-check-circle-fill"></i> Selected</span>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="row g-4 mb-4">
                    <!-- Resume Attachment -->
                    <div class="col-md-6">
                        <h3 class="pf-font-title h5 mb-3">Resume Attachment</h3>
                        <div class="mb-3">
                            <label for="resume" class="form-label small fw-bold">Upload PDF Resume</label>
                            <input type="file" class="form-control" id="resume" name="resume" accept="application/pdf">
                            <small class="text-muted d-block mt-1">Only PDF format is allowed. Max size 5MB.</small>
                        </div>
                        <?php if ($resume): ?>
                            <div class="alert alert-secondary border-0 p-3 rounded-3 d-flex justify-content-between align-items-center">
                                <div class="small text-truncate me-2">
                                    <strong>Attached:</strong> <?php echo escape($resume['label']); ?>
                                </div>
                                <a href="/PortfolioForge/uploads/<?php echo escape($resume['file_path']); ?>" target="_blank" class="btn btn-link btn-sm p-0">View PDF</a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Theme configuration -->
                    <div class="col-md-6">
                        <h3 class="pf-font-title h5 mb-3">Light / Dark Mode</h3>
                        <label for="theme" class="form-label small fw-bold">Choose Aesthetics</label>
                        <select class="form-select" id="theme" name="theme">
                            <option value="apple-light" <?php echo $portfolio['theme'] === 'apple-light' ? 'selected' : ''; ?>>Light Mode</option>
                            <option value="apple-dark" <?php echo $portfolio['theme'] === 'apple-dark' ? 'selected' : ''; ?>>Dark Mode</option>
                        </select>
                        <small class="text-muted d-block mt-2">Note: the Developer Dark layout always displays in dark mode.</small>
                    </div>
                </div>

                <button type="submit" class="btn pf-btn-primary py-2.5 px-4 mt-3">Save Profile</button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
