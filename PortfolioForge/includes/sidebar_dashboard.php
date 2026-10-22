<?php
/**
 * Reusable Dashboard Sidebar Component
 * Include after setting $active_page variable.
 * 
 * Usage: Set $active_page to one of: 'overview','profile','projects','skills','education','experience','messages'
 */
?>
<div class="col-md-3">
    <div class="pf-dashboard-sidebar pf-card border-0">
        <h4 class="h6 text-muted fw-bold mb-3 px-3" style="text-transform:uppercase; letter-spacing:.08em;">Manage Profile</h4>
        <nav class="nav flex-column gap-1">
            <a class="pf-sidebar-link <?php echo ($active_page === 'overview') ? 'active' : ''; ?>" href="/PortfolioForge/dashboard/index.php">
                <span>&#9783;</span> Overview
            </a>
            <a class="pf-sidebar-link <?php echo ($active_page === 'profile') ? 'active' : ''; ?>" href="/PortfolioForge/dashboard/profile.php">
                <span>&#128100;</span> Profile Info
            </a>
            <a class="pf-sidebar-link <?php echo ($active_page === 'projects') ? 'active' : ''; ?>" href="/PortfolioForge/dashboard/projects.php">
                <span>&#128188;</span> Projects
            </a>
            <a class="pf-sidebar-link <?php echo ($active_page === 'skills') ? 'active' : ''; ?>" href="/PortfolioForge/dashboard/skills.php">
                <span>&#9889;</span> Skills
            </a>
            <a class="pf-sidebar-link <?php echo ($active_page === 'education') ? 'active' : ''; ?>" href="/PortfolioForge/dashboard/education.php">
                <span>&#127891;</span> Education
            </a>
            <a class="pf-sidebar-link <?php echo ($active_page === 'experience') ? 'active' : ''; ?>" href="/PortfolioForge/dashboard/experience.php">
                <span>&#127970;</span> Experience
            </a>
            <a class="pf-sidebar-link d-flex justify-content-between align-items-center <?php echo ($active_page === 'messages') ? 'active' : ''; ?>" href="/PortfolioForge/dashboard/messages.php">
                <span>&#9993;&#65039; Messages</span>
                <?php if (isset($unread_messages) && $unread_messages > 0): ?>
                    <span class="badge bg-danger rounded-pill"><?php echo $unread_messages; ?></span>
                <?php endif; ?>
            </a>
        </nav>
        <hr class="my-4 border-light-subtle">
        <a href="/PortfolioForge/portfolio.php?username=<?php echo escape($_SESSION['username']); ?>" target="_blank" class="btn pf-btn-secondary w-100 mb-2">View Portfolio</a>
        <a href="/PortfolioForge/logout.php" class="btn btn-outline-danger w-100 rounded-pill btn-sm">Logout</a>
    </div>
</div>
