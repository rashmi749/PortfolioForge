<?php
// dashboard/skills.php - Manage Portfolio Skills
$page_title = 'Manage Skills';
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
$skill_to_edit = null;

// Handle Delete Action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $skill_id = intval($_GET['id']);
    try {
        $stmt = $pdo->prepare("DELETE FROM skills WHERE id = ? AND portfolio_id = ?");
        $stmt->execute([$skill_id, $portfolio_id]);
        set_flash('success', 'Skill removed successfully.');
    } catch (PDOException $e) {
        set_flash('danger', 'Failed to remove skill.');
    }
    redirect('/PortfolioForge/dashboard/skills.php');
}

// Handle Edit Fetch
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $skill_id = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT * FROM skills WHERE id = ? AND portfolio_id = ?");
    $stmt->execute([$skill_id, $portfolio_id]);
    $skill_to_edit = $stmt->fetch();
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF token validation failed.");
    }

    $skill_id = isset($_POST['skill_id']) ? intval($_POST['skill_id']) : null;
    $name = trim($_POST['name'] ?? '');
    $percentage = isset($_POST['percentage']) ? intval($_POST['percentage']) : 100;
    $category = trim($_POST['category'] ?? 'General');

    if (empty($name)) {
        $errors[] = 'Skill name is required.';
    }

    if ($percentage < 0 || $percentage > 100) {
        $errors[] = 'Percentage must be between 0 and 100.';
    }

    if (empty($errors)) {
        try {
            if ($skill_id) {
                // UPDATE
                $stmt = $pdo->prepare("UPDATE skills SET name = ?, percentage = ?, category = ? WHERE id = ? AND portfolio_id = ?");
                $stmt->execute([$name, $percentage, $category, $skill_id, $portfolio_id]);
                set_flash('success', 'Skill updated successfully.');
            } else {
                // INSERT
                $stmt = $pdo->prepare("INSERT INTO skills (portfolio_id, name, percentage, category) VALUES (?, ?, ?, ?)");
                $stmt->execute([$portfolio_id, $name, $percentage, $category]);
                set_flash('success', 'Skill added successfully.');
            }
            redirect('/PortfolioForge/dashboard/skills.php');
        } catch (PDOException $e) {
            $errors[] = 'An error occurred while saving the skill.';
        }
    }
}

// Fetch all existing skills
try {
    $stmt = $pdo->prepare("SELECT * FROM skills WHERE portfolio_id = ? ORDER BY category, name ASC");
    $stmt->execute([$portfolio_id]);
    $skills = $stmt->fetchAll();
} catch (PDOException $e) {
    $skills = [];
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
                <a class="pf-sidebar-link active" href="/PortfolioForge/dashboard/skills.php">
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
                <?php echo $skill_to_edit ? 'Edit Skill' : 'Add New Skill'; ?>
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

            <form action="/PortfolioForge/dashboard/skills.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo escape(generate_csrf_token()); ?>">
                
                <?php if ($skill_to_edit): ?>
                    <input type="hidden" name="skill_id" value="<?php echo $skill_to_edit['id']; ?>">
                <?php endif; ?>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="category" class="form-label small fw-bold">Category</label>
                        <select class="form-select" id="category" name="category">
                            <?php
                                $pf_skill_categories = ['General', 'Frontend', 'Backend', 'Database', 'Design', 'Tools & DevOps', 'Soft Skills', 'Other'];
                                $pf_current_category = $skill_to_edit['category'] ?? 'General';
                                // If an existing skill's category isn't in the preset list, still include it so it's not lost
                                if (!empty($pf_current_category) && !in_array($pf_current_category, $pf_skill_categories)) {
                                    array_splice($pf_skill_categories, count($pf_skill_categories) - 1, 0, [$pf_current_category]);
                                }
                            ?>
                            <?php foreach ($pf_skill_categories as $pf_cat): ?>
                                <option value="<?php echo escape($pf_cat); ?>" <?php echo ($pf_current_category === $pf_cat) ? 'selected' : ''; ?>><?php echo escape($pf_cat); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Pick a category first — Skill Name suggestions below will match it.</small>
                    </div>
                    <div class="col-md-5">
                        <label for="name" class="form-label small fw-bold">Skill Name</label>
                        <input type="text" class="form-control" id="name" name="name" list="pf-skill-suggestions" value="<?php echo escape($skill_to_edit['name'] ?? ''); ?>" required placeholder="e.g. JavaScript, PHP, Figma" autocomplete="off">
                        <datalist id="pf-skill-suggestions"></datalist>
                    </div>
                    <div class="col-md-3">
                        <label for="percentage" class="form-label small fw-bold">Proficiency (%)</label>
                        <input type="number" class="form-control" id="percentage" name="percentage" value="<?php echo escape($skill_to_edit['percentage'] ?? 100); ?>" min="0" max="100" required>
                    </div>
                </div>

                <script>
                (function () {
                    // Suggested skill names per category — shown as dropdown options in the
                    // Skill Name field once a category is chosen above.
                    var pfSkillsByCategory = {
                        'Frontend': ['HTML5', 'CSS3', 'JavaScript', 'TypeScript', 'React', 'Vue.js', 'Angular', 'Svelte', 'Next.js', 'Tailwind CSS', 'Bootstrap', 'jQuery'],
                        'Backend': ['PHP', 'Node.js', 'Python', 'Java', 'C#', 'Ruby', 'Go', 'Laravel', 'Express.js', 'Django', 'Spring Boot', 'REST APIs'],
                        'Database': ['MySQL', 'PostgreSQL', 'MongoDB', 'SQLite', 'Redis', 'Firebase', 'Microsoft SQL Server', 'Oracle DB'],
                        'Design': ['Figma', 'Adobe Photoshop', 'Adobe Illustrator', 'Adobe XD', 'Sketch', 'UI/UX Design', 'Canva', 'Wireframing'],
                        'Tools & DevOps': ['Git', 'Docker', 'Kubernetes', 'GitHub Actions', 'Jenkins', 'AWS', 'Azure', 'Linux', 'Postman', 'Nginx'],
                        'Soft Skills': ['Communication', 'Team Leadership', 'Problem Solving', 'Project Management', 'Time Management', 'Collaboration'],
                        'General': ['JavaScript', 'PHP', 'Python', 'HTML5', 'CSS3', 'Git', 'MySQL', 'Communication'],
                        'Other': []
                    };

                    var categorySelect = document.getElementById('category');
                    var nameInput = document.getElementById('name');
                    var datalist = document.getElementById('pf-skill-suggestions');

                    function pfRefreshSuggestions() {
                        var chosen = categorySelect.value;
                        var options = pfSkillsByCategory[chosen] || [];
                        datalist.innerHTML = '';
                        options.forEach(function (skill) {
                            var opt = document.createElement('option');
                            opt.value = skill;
                            datalist.appendChild(opt);
                        });
                    }

                    categorySelect.addEventListener('change', pfRefreshSuggestions);
                    pfRefreshSuggestions(); // populate on initial load (handles edit mode too)
                })();
                </script>

                <div class="mt-4">
                    <button type="submit" class="btn pf-btn-primary py-2 px-4">
                        <?php echo $skill_to_edit ? 'Update Skill' : 'Add Skill'; ?>
                    </button>
                    <?php if ($skill_to_edit): ?>
                        <a href="/PortfolioForge/dashboard/skills.php" class="btn pf-btn-secondary py-2 px-4 ms-2">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Skills List Panel -->
        <div class="pf-card border-0 p-5">
            <h2 class="pf-font-title h4 mb-4">Your Skills</h2>
            
            <?php if (empty($skills)): ?>
                <p class="text-muted small mb-0">No skills added yet. Use the form above to record your skills.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table align-middle text-muted small">
                        <thead>
                            <tr class="text-dark">
                                <th scope="col">Name</th>
                                <th scope="col">Category</th>
                                <th scope="col">Proficiency</th>
                                <th scope="col" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($skills as $skill): ?>
                                <tr>
                                    <td class="fw-bold text-dark"><?php echo escape($skill['name']); ?></td>
                                    <td><span class="badge bg-secondary-subtle text-secondary"><?php echo escape($skill['category']); ?></span></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2" style="width: 150px;">
                                            <div class="progress flex-grow-1" style="height: 6px;">
                                                <div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo $skill['percentage']; ?>%;" aria-valuenow="<?php echo $skill['percentage']; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <span><?php echo $skill['percentage']; ?>%</span>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <a href="/PortfolioForge/dashboard/skills.php?action=edit&id=<?php echo $skill['id']; ?>" class="btn btn-sm btn-outline-primary me-2">Edit</a>
                                        <a href="/PortfolioForge/dashboard/skills.php?action=delete&id=<?php echo $skill['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to remove this skill?');">Delete</a>
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
