<?php
// login.php - User Login
$page_title = 'Sign In';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

// Redirect if already logged in
if (is_logged_in()) {
    redirect('/PortfolioForge/dashboard/index.php');
}

$errors = [];
$identity = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF verification
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF token validation failed.");
    }

    $identity = trim($_POST['identity'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($identity) || empty($password)) {
        $errors[] = 'Please enter both username/email and password.';
    } else {
        try {
            // Find user by username or email
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$identity, $identity]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Login successful, setup session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                set_flash('success', 'Welcome back, ' . $user['username'] . '!');
                
                // Redirect based on role
                if ($user['role'] === 'admin') {
                    redirect('/PortfolioForge/admin/dashboard.php');
                } else {
                    redirect('/PortfolioForge/dashboard/index.php');
                }
            } else {
                $errors[] = 'Invalid username/email or password.';
            }
        } catch (PDOException $e) {
            $errors[] = 'An error occurred during sign in. Please try again.';
        }
    }
}
require_once __DIR__ . '/includes/header.php';
?>

<div class="row justify-content-center py-5">
    <div class="col-md-5">
        <div class="pf-card p-5">
            <div class="text-center mb-4">
                <i class="bi bi-hexagon-fill h1 text-primary"></i>
                <h2 class="pf-font-title mt-2">Sign in to PortfolioForge</h2>
                <p class="text-muted small">Enter your details to manage your premium portfolio.</p>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger border-0 rounded-3 shadow-sm small">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo escape($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="/PortfolioForge/login.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo escape(generate_csrf_token()); ?>">
                
                <div class="mb-3">
                    <label for="identity" class="form-label small fw-bold">Username or Email Address</label>
                    <input type="text" class="form-control" id="identity" name="identity" value="<?php echo escape($identity); ?>" required autocomplete="off">
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label small fw-bold">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>

                <button type="submit" class="btn pf-btn-primary w-100 py-2.5">Sign In</button>
            </form>

            <div class="text-center mt-4 pt-2 border-top border-light-subtle">
                <p class="text-muted small mb-0">Don't have an account? <a href="/PortfolioForge/register.php" class="text-decoration-none">Create one</a></p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
