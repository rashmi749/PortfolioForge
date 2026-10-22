<?php
// admin/users.php - Moderate Registered User Accounts
$page_title = 'Manage Users';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';

// Enforce admin privileges
require_admin();

$admin_user_id = $_SESSION['user_id'];

// Process Actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $target_user_id = intval($_GET['id']);
    $action = $_GET['action'];

    // Prevent self-modifications
    if ($target_user_id === $admin_user_id) {
        set_flash('danger', 'You cannot modify or delete your own account.');
        redirect('/PortfolioForge/admin/users.php');
    }

    try {
        if ($action === 'toggle_role') {
            // Fetch target user's current role
            $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
            $stmt->execute([$target_user_id]);
            $current_role = $stmt->fetchColumn();

            $new_role = ($current_role === 'admin') ? 'user' : 'admin';
            $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
            $stmt->execute([$new_role, $target_user_id]);
            set_flash('success', 'User role updated successfully.');

        } elseif ($action === 'delete') {
            // Delete user avatar file if exists
            $stmt = $pdo->prepare("SELECT profile_image FROM portfolios WHERE user_id = ?");
            $stmt->execute([$target_user_id]);
            $avatar = $stmt->fetchColumn();

            if (!empty($avatar)) {
                $file = __DIR__ . '/../uploads/' . $avatar;
                if (file_exists($file)) {
                    unlink($file);
                }
            }

            // Cascade delete user
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$target_user_id]);
            set_flash('success', 'User account deleted successfully.');
        }
    } catch (PDOException $e) {
        set_flash('danger', 'Moderator action failed.');
    }
    redirect('/PortfolioForge/admin/users.php');
}

require_once __DIR__ . '/../includes/header.php';

// Fetch Users List
try {
    $stmt = $pdo->prepare("SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC");
    $stmt->execute();
    $users_list = $stmt->fetchAll();
} catch (PDOException $e) {
    $users_list = [];
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
                <a class="pf-sidebar-link active" href="/PortfolioForge/admin/users.php">
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
        <div class="pf-card border-0 p-5">
            <h1 class="pf-font-title h3 mb-4">Manage User Accounts</h1>

            <?php if (empty($users_list)): ?>
                <p class="text-muted small mb-0">No users registered on the platform.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table align-middle text-muted small">
                        <thead>
                            <tr class="text-dark">
                                <th scope="col">Username</th>
                                <th scope="col">Email</th>
                                <th scope="col">Role</th>
                                <th scope="col">Registered Date</th>
                                <th scope="col" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users_list as $user): ?>
                                <tr>
                                    <td class="fw-bold text-dark"><?php echo escape($user['username']); ?></td>
                                    <td><?php echo escape($user['email']); ?></td>
                                    <td>
                                        <span class="badge <?php echo $user['role'] === 'admin' ? 'bg-warning text-dark' : 'bg-secondary-subtle text-secondary'; ?>">
                                            <?php echo ucfirst($user['role']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                    <td class="text-end">
                                        <?php if ($user['id'] !== $admin_user_id): ?>
                                            <a href="/PortfolioForge/admin/users.php?action=toggle_role&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-outline-primary me-2">
                                                Toggle Role
                                            </a>
                                            <a href="/PortfolioForge/admin/users.php?action=delete&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this user? This will also purge their entire portfolio.');">
                                                Delete
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted small italic">Logged In</span>
                                        <?php endif; ?>
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
