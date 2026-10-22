<?php
/**
 * PortfolioForge Global Utility Functions
 * Security, sanitization, file uploads, and session management.
 */

// Start session securely if not already active
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    // If running under HTTPS, you'd set cookie_secure:
    // ini_set('session.cookie_secure', 1);
    session_start();
}

/**
 * Escape string for secure output in HTML (XSS prevention).
 *
 * @param string $string
 * @return string
 */
function escape($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Generate a CSRF token and store it in session.
 *
 * @return string
 */
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token.
 *
 * @param string $token
 * @return bool
 */
function verify_csrf_token($token) {
    if (!isset($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Redirect to a specific URL and exit.
 *
 * @param string $url
 * @return void
 */
function redirect($url) {
    if (ob_get_level()) {
        ob_end_clean();
    }
    header("Location: " . $url);
    exit;
}

/**
 * Set a flash message in the session.
 *
 * @param string $type success, danger, info, warning
 * @param string $message
 * @return void
 */
function set_flash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Display and clear flash message.
 *
 * @return string HTML Alert
 */
function display_flash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        
        $type = escape($flash['type']);
        $msg = escape($flash['message']);
        
        return "
        <div class='alert alert-{$type} alert-dismissible fade show border-0 shadow-sm rounded-3' role='alert' style='backdrop-filter: blur(10px); background: rgba(255, 255, 255, 0.85);'>
            <div class='d-flex align-items-center'>
                <span class='me-2'>" . ($type === 'success' ? '✓' : '⚠️') . "</span>
                <div>{$msg}</div>
            </div>
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>";
    }
    return '';
}

/**
 * Securely handles file uploads.
 *
 * @param array $file The $_FILES['name'] array
 * @param array $allowed_mime Allowed MIME types
 * @param int $max_size Maximum file size in bytes
 * @param string $upload_dir Target upload folder path
 * @return string Filename on success
 * @throws Exception
 */
function upload_file($file, $allowed_mime, $max_size, $upload_dir) {
    // Check for PHP upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        switch ($file['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new Exception("File size exceeds limit.");
            case UPLOAD_ERR_NO_FILE:
                throw new Exception("No file was uploaded.");
            default:
                throw new Exception("Upload error: " . $file['error']);
        }
    }

    // Validate size
    if ($file['size'] > $max_size) {
        throw new Exception("File size must be less than " . ($max_size / (1024 * 1024)) . "MB.");
    }

    // Validate MIME type securely using finfo
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    if (!array_key_exists($mime, $allowed_mime)) {
        throw new Exception("Invalid file type. Allowed formats: " . implode(', ', array_keys($allowed_mime)));
    }

    // Create target directory if it doesn't exist
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Generate unique sanitized filename
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $new_filename = bin2hex(random_bytes(16)) . '.' . strtolower($ext);
    $target_file = rtrim($upload_dir, '/') . '/' . $new_filename;

    if (!move_uploaded_file($file['tmp_name'], $target_file)) {
        throw new Exception("Failed to save uploaded file.");
    }

    return $new_filename;
}

/**
 * Returns helper paths.
 *
 * @param string $path
 * @return string
 */
function asset_url($path) {
    // Assuming root-relative paths for simplicity in development
    return '/PortfolioForge/assets/' . ltrim($path, '/');
}

/**
 * Get User Portfolio details from user ID.
 *
 * @param PDO $pdo
 * @param int $user_id
 * @return array|bool
 */
function get_user_portfolio($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT * FROM portfolios WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $portfolio = $stmt->fetch();

    if (!$portfolio && !is_admin()) {
        // Some accounts (e.g. the seeded admin) may not have a portfolio row yet — create a default one instead of dead-ending.
        $username = $_SESSION['username'] ?? 'User';
        $stmt = $pdo->prepare("INSERT INTO portfolios (user_id, full_name, title, bio) VALUES (?, ?, 'Professional Developer', 'Welcome to my portfolio.')");
        $stmt->execute([$user_id, $username]);

        $stmt = $pdo->prepare("SELECT * FROM portfolios WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $portfolio = $stmt->fetch();
    }

    return $portfolio;
}

/**
 * Registry of available portfolio layouts/templates.
 * Add a new entry here + a matching templates/portfolio-{id}.php file to add a new layout.
 *
 * @return array
 */
function pf_get_layouts() {
    return [
        'minimal' => [
            'name'        => 'Minimal Apple',
            'description' => 'Clean glassmorphism, generous whitespace, light or dark mode.',
            'icon'        => 'bi-gem',
            'preview_bg'  => '#f5f5f7',
            'preview_fg'  => '#0a3d78',
        ],
        'developer' => [
            'name'        => 'Developer Dark',
            'description' => 'Bold dark theme with a code-inspired hero and stat counters.',
            'icon'        => 'bi-code-slash',
            'preview_bg'  => '#0f1117',
            'preview_fg'  => '#7c5cff',
        ],
        'creative' => [
            'name'        => 'Creative Studio',
            'description' => 'Colorful, editorial layout for designers and creatives.',
            'icon'        => 'bi-palette-fill',
            'preview_bg'  => '#f6ece7',
            'preview_fg'  => '#c76b52',
        ],
    ];
}

/**
 * Return a safe layout id, falling back to 'minimal' if unknown/missing.
 *
 * @param string|null $layout
 * @return string
 */
function pf_resolve_layout($layout) {
    $layouts = pf_get_layouts();
    return isset($layouts[$layout]) ? $layout : 'minimal';
}
