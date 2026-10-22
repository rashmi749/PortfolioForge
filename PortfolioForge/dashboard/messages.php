<?php
// dashboard/messages.php - Manage Portfolio Inquiries / Contact Messages
$page_title = 'Contact Inquiries';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';

// Enforce login
require_login();
block_admin_from_dashboard();


$user_id = $_SESSION['user_id'];
$portfolio = get_user_portfolio($pdo, $user_id);
$portfolio_id = $portfolio['id'];

// Handle Actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $msg_id = intval($_GET['id']);
    $action = $_GET['action'];
    
    try {
        if ($action === 'read') {
            $stmt = $pdo->prepare("UPDATE contacts SET is_read = 1 WHERE id = ? AND portfolio_id = ?");
            $stmt->execute([$msg_id, $portfolio_id]);
            set_flash('success', 'Message marked as read.');
        } elseif ($action === 'delete') {
            $stmt = $pdo->prepare("DELETE FROM contacts WHERE id = ? AND portfolio_id = ?");
            $stmt->execute([$msg_id, $portfolio_id]);
            set_flash('success', 'Message deleted successfully.');
        }
    } catch (PDOException $e) {
        set_flash('danger', 'Action execution failed.');
    }
    redirect('/PortfolioForge/dashboard/messages.php');
}

// Fetch all messages
try {
    $stmt = $pdo->prepare("SELECT * FROM contacts WHERE portfolio_id = ? ORDER BY created_at DESC");
    $stmt->execute([$portfolio_id]);
    $messages = $stmt->fetchAll();
} catch (PDOException $e) {
    $messages = [];
}

// Fetch unread messages count for sidebar badge
$unread_messages = 0;
foreach ($messages as $m) {
    if (!$m['is_read']) {
        $unread_messages++;
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
                <a class="pf-sidebar-link" href="/PortfolioForge/dashboard/profile.php">
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
                <a class="pf-sidebar-link active d-flex justify-content-between align-items-center" href="/PortfolioForge/dashboard/messages.php">
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
            <h1 class="pf-font-title h3 mb-4">Contact Messages</h1>

            <?php if (empty($messages)): ?>
                <p class="text-muted small mb-0">No contact messages received yet.</p>
            <?php else: ?>
                <div class="accordion border-0 pf-accordion" id="messagesAccordion">
                    <?php foreach ($messages as $index => $msg): ?>
                        <div class="accordion-item border-0 border-bottom border-light-subtle bg-transparent mb-2 <?php echo !$msg['is_read'] ? 'fw-bold text-dark' : ''; ?>">
                            <h2 class="accordion-header" id="heading<?php echo $msg['id']; ?>">
                                <button class="accordion-button collapsed bg-transparent border-0 px-3 py-3 shadow-none d-flex justify-content-between text-muted" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $msg['id']; ?>" aria-expanded="false" aria-controls="collapse<?php echo $msg['id']; ?>">
                                    <div class="d-flex align-items-center gap-3 text-truncate">
                                        <span class="badge <?php echo $msg['is_read'] ? 'bg-secondary-subtle text-secondary' : 'bg-primary'; ?> rounded-pill">
                                            <?php echo $msg['is_read'] ? 'Read' : 'New'; ?>
                                        </span>
                                        <span class="text-dark fw-semibold small text-truncate"><?php echo escape($msg['name']); ?></span>
                                        <span class="small text-muted text-truncate d-none d-md-inline" style="max-width: 300px;"><?php echo escape($msg['subject'] ?: '(No Subject)'); ?></span>
                                    </div>
                                    <small class="text-muted ms-auto pe-3 small text-nowrap"><?php echo date('M d, Y H:i', strtotime($msg['created_at'])); ?></small>
                                </button>
                            </h2>
                            <div id="collapse<?php echo $msg['id']; ?>" class="accordion-collapse collapse" aria-labelledby="heading<?php echo $msg['id']; ?>" data-bs-parent="#messagesAccordion">
                                <div class="accordion-body px-3 py-3 text-muted small bg-light-subtle rounded-3">
                                    <div class="mb-3">
                                        <strong>From:</strong> <?php echo escape($msg['name']); ?> (<a href="mailto:<?php echo escape($msg['email']); ?>"><?php echo escape($msg['email']); ?></a>)<br>
                                        <strong>Subject:</strong> <?php echo escape($msg['subject'] ?: '(No Subject)'); ?><br>
                                        <strong>Date:</strong> <?php echo date('F d, Y h:i A', strtotime($msg['created_at'])); ?>
                                    </div>
                                    <div class="p-3 border rounded bg-white text-dark mb-3">
                                        <?php echo nl2br(escape($msg['message'])); ?>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <?php if (!$msg['is_read']): ?>
                                            <a href="/PortfolioForge/dashboard/messages.php?action=read&id=<?php echo $msg['id']; ?>" class="btn btn-sm pf-btn-primary py-1 px-3">Mark as Read</a>
                                        <?php endif; ?>
                                        <a href="mailto:<?php echo escape($msg['email']); ?>?subject=Re: <?php echo rawurlencode($msg['subject']); ?>" class="btn btn-sm pf-btn-secondary py-1 px-3">Reply by Email</a>
                                        <a href="/PortfolioForge/dashboard/messages.php?action=delete&id=<?php echo $msg['id']; ?>" class="btn btn-sm btn-outline-danger py-1 px-3" onclick="return confirm('Are you sure you want to delete this message?');">Delete</a>
                                    </div>
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
