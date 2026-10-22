<?php
// register.php - User Registration
$page_title = 'Create an Account';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

// Redirect if already logged in
if (is_logged_in()) {
    redirect('/PortfolioForge/dashboard/index.php');
}

$errors = [];
$username = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF verification
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF token validation failed.");
    }

    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validation checks
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $errors[] = 'All fields are required.';
    }

    if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
        $errors[] = 'Username must be between 3 and 20 alphanumeric characters or underscores.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters long.';
    }

    if ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match.';
    }

    // Check availability if no validation errors
    if (empty($errors)) {
        try {
            // Check username
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $errors[] = 'Username is already taken.';
            }

            // Check email
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $errors[] = 'Email is already registered.';
            }

            // Proceed to insert within transaction if still no errors
            if (empty($errors)) {
                $pdo->beginTransaction();

                $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')");
                $stmt->execute([$username, $email, $hashed_password]);
                $user_id = $pdo->lastInsertId();

                // Create default portfolio profile matching the username
                $stmt = $pdo->prepare("INSERT INTO portfolios (user_id, full_name, title, bio) VALUES (?, ?, 'Professional Developer', 'Welcome to my portfolio.')");
                $stmt->execute([$user_id, $username]);

                $pdo->commit();

                // Start Session
                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $username;
                $_SESSION['role'] = 'user';

                set_flash('success', 'Account created successfully! Welcome to your dashboard.');
                redirect('/PortfolioForge/dashboard/index.php');
            }
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log('Registration error: ' . $e->getMessage());
            $errors[] = 'An error occurred during registration. Please try again.';
        }
    }
}
?>

<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="row justify-content-center py-5">
    <div class="col-md-5">
        <div class="pf-card p-5">
            <div class="text-center mb-4">
                <i class="bi bi-hexagon-fill h1 text-primary"></i>
                <h2 class="pf-font-title mt-2">Create your account</h2>
                <p class="text-muted small">Join PortfolioForge and forge your custom page today.</p>
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

            <form action="/PortfolioForge/register.php" method="POST" class="needs-validation" novalidate>
                <input type="hidden" name="csrf_token" value="<?php echo escape(generate_csrf_token()); ?>">
                
                <div class="mb-3">
                    <label for="username" class="form-label small fw-bold">Username</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?php echo escape($username); ?>" required autocomplete="off">
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label small fw-bold">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo escape($email); ?>" required autocomplete="off">
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label small fw-bold">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>

                <div class="mb-4">
                    <label for="confirm_password" class="form-label small fw-bold">Confirm Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>

                <button type="submit" class="btn pf-btn-primary w-100 py-2.5">Sign Up</button>
            </form>

            <div class="text-center mt-4 pt-2 border-top border-light-subtle">
                <p class="text-muted small mb-0">Already have an account? <a href="/PortfolioForge/login.php" class="text-decoration-none">Sign In</a></p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
