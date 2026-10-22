<?php
// portfolio.php - Public Portfolio Page
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/database.php';

$username = trim($_GET['username'] ?? '');

if (empty($username)) {
    die("Error: No username specified.");
}

// Fetch user and portfolio
try {
    $stmt = $pdo->prepare("SELECT u.id as user_id, u.username, p.* FROM users u JOIN portfolios p ON u.id = p.user_id WHERE u.username = ?");
    $stmt->execute([$username]);
    $portfolio = $stmt->fetch();
    
    if (!$portfolio) {
        die("Error: Portfolio profile not found for user '" . escape($username) . "'.");
    }

    $portfolio_id = $portfolio['id'];

    // Increment View Counter (ignore admin/owner views if logged in, but simple increment is standard)
    if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] !== $portfolio['user_id']) {
        $update_views = $pdo->prepare("UPDATE portfolios SET views = views + 1 WHERE id = ?");
        $update_views->execute([$portfolio_id]);
    }

    // Fetch projects
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE portfolio_id = ? ORDER BY created_at DESC");
    $stmt->execute([$portfolio_id]);
    $projects = $stmt->fetchAll();

    // Fetch skills
    $stmt = $pdo->prepare("SELECT * FROM skills WHERE portfolio_id = ? ORDER BY category, name ASC");
    $stmt->execute([$portfolio_id]);
    $skills = $stmt->fetchAll();

    // Fetch education
    $stmt = $pdo->prepare("SELECT * FROM education WHERE portfolio_id = ? ORDER BY start_date DESC");
    $stmt->execute([$portfolio_id]);
    $education = $stmt->fetchAll();

    // Fetch experience
    $stmt = $pdo->prepare("SELECT * FROM experience WHERE portfolio_id = ? ORDER BY start_date DESC");
    $stmt->execute([$portfolio_id]);
    $experience = $stmt->fetchAll();

    // Fetch resume
    $stmt = $pdo->prepare("SELECT * FROM resumes WHERE portfolio_id = ?");
    $stmt->execute([$portfolio_id]);
    $resume = $stmt->fetch();

} catch (PDOException $e) {
    die("Database error occurred.");
}

// Determine which visual layout/template to render for this portfolio
$layout = pf_resolve_layout($portfolio['layout'] ?? 'minimal');
$template_file = __DIR__ . '/templates/portfolio-' . $layout . '.php';

if (!file_exists($template_file)) {
    $template_file = __DIR__ . '/templates/portfolio-minimal.php';
}

require $template_file;
