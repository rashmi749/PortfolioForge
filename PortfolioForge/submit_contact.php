<?php
// submit_contact.php - Contact Form AJAX Processor
header('Content-Type: application/json');

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/mail.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$portfolio_id = isset($_POST['portfolio_id']) ? intval($_POST['portfolio_id']) : null;
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? 'New Inquiry from Portfolio');
$message = trim($_POST['message'] ?? '');

// Validation
if (!$portfolio_id || empty($name) || empty($email) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}

try {
    // Verify portfolio exists and fetch owner details
    $stmt = $pdo->prepare("SELECT p.full_name as owner_name, u.email as owner_email FROM portfolios p JOIN users u ON p.user_id = u.id WHERE p.id = ?");
    $stmt->execute([$portfolio_id]);
    $owner = $stmt->fetch();

    if (!$owner) {
        echo json_encode(['success' => false, 'message' => 'Portfolio profile not found.']);
        exit;
    }

    // 1. Record message to Database first
    $stmt = $pdo->prepare("INSERT INTO contacts (portfolio_id, name, email, subject, message) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$portfolio_id, $name, $email, $subject, $message]);

    // 2. Dispatch Email Alert using PHPMailer configuration
    $email_body = "
    <h2>New Portfolio Message Recieved</h2>
    <p>Hi <strong>{$owner['owner_name']}</strong>,</p>
    <p>You have received a new contact inquiry through your PortfolioForge page.</p>
    <hr style='border:0; border-top:1px solid #eee;'>
    <p><strong>From:</strong> {$name} ({$email})</p>
    <p><strong>Subject:</strong> {$subject}</p>
    <p><strong>Message:</strong></p>
    <blockquote style='background:#f9f9f9; border-left:4px solid #ccc; padding:10px 15px; margin:0;'>
        " . nl2br(escape($message)) . "
    </blockquote>
    <hr style='border:0; border-top:1px solid #eee;'>
    <p style='font-size:12px; color:#888;'>Reply directly to this email to contact the inquirer. Manage your messages at your PortfolioForge Dashboard.</p>";

    // Trigger sendMail wrapper
    $mail_sent = sendMail($owner['owner_email'], "PortfolioForge - Inquiry: {$subject}", $email_body);
    
    // We notify success as long as message is recorded in database, email is a nice overlay alert
    echo json_encode([
        'success' => true, 
        'message' => 'Your message was sent successfully! The portfolio owner has been notified.'
    ]);
    exit;

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database failure. Please try again later.']);
    exit;
}
