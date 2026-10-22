<?php
// index.php - Landing Page
$page_title = 'Forge Premium Portfolios';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/config/database.php';

// Fetch recent portfolios to show in explore section
try {
    $stmt = $pdo->query("SELECT p.*, u.username FROM portfolios p JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC LIMIT 6");
    $recent_portfolios = $stmt->fetchAll();
} catch (PDOException $e) {
    $recent_portfolios = [];
}

// Fetch live platform stats for the hero section
try {
    $portfolio_count = (int) $pdo->query("SELECT COUNT(*) FROM portfolios")->fetchColumn();
    $user_count = (int) $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $views_count = (int) $pdo->query("SELECT COALESCE(SUM(views), 0) FROM portfolios")->fetchColumn();
} catch (PDOException $e) {
    $portfolio_count = 0;
    $user_count = 0;
    $views_count = 0;
}

// Friendly rounding so the counters still look good on a fresh install
function pf_format_count($n) {
    if ($n >= 1000) {
        return round($n / 1000, 1) . 'k+';
    }
    return $n . '+';
}
?>

<div class="pf-portfolio-hero py-5 mb-5">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 text-center text-lg-start">
                <span class="pf-badge-pill mb-3 d-inline-flex align-items-center gap-2">
                    <i class="bi bi-stars"></i> Now with dark mode &amp; live previews
                </span>
                <h1 class="display-3 pf-font-title mb-3">Your work, forged in elegance.</h1>
                <p class="lead text-muted mb-4">
                    Create a stunning, premium portfolio in minutes. Inspired by Apple's minimalist aesthetics. Fully responsive, lightning fast.
                </p>
                <div class="d-flex justify-content-center justify-content-lg-start gap-3 flex-wrap mb-5">
                    <?php if (is_logged_in()): ?>
                        <a href="/PortfolioForge/dashboard/index.php" class="btn pf-btn-primary btn-lg px-4">Go to Dashboard</a>
                    <?php else: ?>
                        <a href="/PortfolioForge/register.php" class="btn pf-btn-primary btn-lg px-4"><i class="bi bi-magic me-2"></i>Create Your Portfolio</a>
                        <a href="#explore" class="btn pf-btn-secondary btn-lg px-4">Explore Portfolios</a>
                    <?php endif; ?>
                </div>

                <!-- Live Stats -->
                <div class="row g-4 pf-hero-stats justify-content-center justify-content-lg-start">
                    <div class="col-4">
                        <h3 class="pf-font-title mb-0"><?php echo pf_format_count($user_count); ?></h3>
                        <small class="text-muted">Creators</small>
                    </div>
                    <div class="col-4">
                        <h3 class="pf-font-title mb-0"><?php echo pf_format_count($portfolio_count); ?></h3>
                        <small class="text-muted">Portfolios Live</small>
                    </div>
                    <div class="col-4">
                        <h3 class="pf-font-title mb-0"><?php echo pf_format_count($views_count); ?></h3>
                        <small class="text-muted">Profile Views</small>
                    </div>
                </div>
            </div>

            <!-- Hero Visual: CSS/SVG browser mockup, no external images required -->
            <div class="col-lg-6">
                <div class="pf-hero-visual mx-auto">
                    <div class="pf-mock-browser">
                        <div class="pf-mock-browser-bar">
                            <span class="pf-mock-dot" style="background:#ff5f57;"></span>
                            <span class="pf-mock-dot" style="background:#febc2e;"></span>
                            <span class="pf-mock-dot" style="background:#28c840;"></span>
                            <div class="pf-mock-url"><i class="bi bi-lock-fill"></i> portfolioforge.app/you</div>
                        </div>
                        <div class="pf-mock-browser-body">
                            <?php
                                // Drop a real screenshot at assets/img/hero-preview.jpg (or .png/.webp)
                                // to show it here instead of the plain gray placeholder mock.
                                $pf_hero_image = null;
                                foreach (['jpg', 'jpeg', 'png', 'webp'] as $pf_ext) {
                                    if (file_exists(__DIR__ . '/assets/img/hero-preview.' . $pf_ext)) {
                                        $pf_hero_image = 'assets/img/hero-preview.' . $pf_ext;
                                        break;
                                    }
                                }
                            ?>
                            <?php if ($pf_hero_image): ?>
                                <img src="/PortfolioForge/<?php echo $pf_hero_image; ?>" alt="Portfolio preview" class="pf-mock-image">
                            <?php else: ?>
                                <div class="pf-mock-avatar"><i class="bi bi-person-fill"></i></div>
                                <div class="pf-mock-line pf-mock-line-lg"></div>
                                <div class="pf-mock-line pf-mock-line-sm"></div>
                                <div class="pf-mock-chip-row">
                                    <span class="pf-mock-chip"></span>
                                    <span class="pf-mock-chip"></span>
                                    <span class="pf-mock-chip"></span>
                                </div>
                                <div class="pf-mock-cards">
                                    <div class="pf-mock-card"></div>
                                    <div class="pf-mock-card"></div>
                                    <div class="pf-mock-card"></div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="pf-floating-card pf-floating-card-1"><i class="bi bi-graph-up-arrow"></i> Views up 32%</div>
                    <div class="pf-floating-card pf-floating-card-2"><i class="bi bi-check-circle-fill text-success"></i> Published</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Features Section -->
<div class="container py-5 my-5">
    <div class="text-center mb-5">
        <h2 class="pf-font-title h1">Features built for creators.</h2>
        <p class="text-muted">Every detail polished to perfection.</p>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="pf-card p-4 h-100 pf-card-hover border-0">
                <div class="pf-feature-icon mb-3"><i class="bi bi-gem"></i></div>
                <h3 class="h5 pf-font-title">Apple-Inspired Design</h3>
                <p class="text-muted small mb-0">Clean lines, generous whitespace, modern typography, and high-end glassmorphic details.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="pf-card p-4 h-100 pf-card-hover border-0">
                <div class="pf-feature-icon mb-3"><i class="bi bi-moon-stars-fill"></i></div>
                <h3 class="h5 pf-font-title">Light &amp; Dark Modes</h3>
                <p class="text-muted small mb-0">A gorgeous iOS-style theme switch that automatically adapts to and saves viewer preferences.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="pf-card p-4 h-100 pf-card-hover border-0">
                <div class="pf-feature-icon mb-3"><i class="bi bi-layout-text-window-reverse"></i></div>
                <h3 class="h5 pf-font-title">Rich Content Sections</h3>
                <p class="text-muted small mb-0">Add details of projects, education timelines, work experience milestones, and custom resumes.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="pf-card p-4 h-100 pf-card-hover border-0">
                <div class="pf-feature-icon mb-3"><i class="bi bi-phone-fill"></i></div>
                <h3 class="h5 pf-font-title">Fully Responsive</h3>
                <p class="text-muted small mb-0">Your portfolio looks flawless on desktop, tablet, and mobile without any extra setup.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="pf-card p-4 h-100 pf-card-hover border-0">
                <div class="pf-feature-icon mb-3"><i class="bi bi-share-fill"></i></div>
                <h3 class="h5 pf-font-title">One-Click Sharing</h3>
                <p class="text-muted small mb-0">Get a clean, memorable link for your portfolio that's ready to share on any platform.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="pf-card p-4 h-100 pf-card-hover border-0">
                <div class="pf-feature-icon mb-3"><i class="bi bi-shield-lock-fill"></i></div>
                <h3 class="h5 pf-font-title">Secure by Default</h3>
                <p class="text-muted small mb-0">Encrypted authentication and safe file handling keep your account and data protected.</p>
            </div>
        </div>
    </div>
</div>

<!-- How It Works Section -->
<div class="container py-5 my-3 border-top border-light-subtle">
    <div class="text-center mb-5">
        <h2 class="pf-font-title h1">Live in three simple steps.</h2>
        <p class="text-muted">From sign up to sharing your link, it only takes a few minutes.</p>
    </div>
    <div class="row g-4">
        <div class="col-md-4">
            <div class="pf-step h-100 p-4 text-center">
                <div class="pf-step-number mb-3">1</div>
                <div class="pf-feature-icon pf-feature-icon-sm mb-3 mx-auto"><i class="bi bi-person-plus-fill"></i></div>
                <h3 class="h5 pf-font-title">Create an account</h3>
                <p class="text-muted small mb-0">Sign up for free in seconds &mdash; no credit card required.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="pf-step h-100 p-4 text-center">
                <div class="pf-step-number mb-3">2</div>
                <div class="pf-feature-icon pf-feature-icon-sm mb-3 mx-auto"><i class="bi bi-pencil-square"></i></div>
                <h3 class="h5 pf-font-title">Fill in your details</h3>
                <p class="text-muted small mb-0">Add your projects, skills, education, and experience with our simple dashboard.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="pf-step h-100 p-4 text-center">
                <div class="pf-step-number mb-3">3</div>
                <div class="pf-feature-icon pf-feature-icon-sm mb-3 mx-auto"><i class="bi bi-rocket-takeoff-fill"></i></div>
                <h3 class="h5 pf-font-title">Publish &amp; share</h3>
                <p class="text-muted small mb-0">Your portfolio goes live instantly at a clean, shareable link.</p>
            </div>
        </div>
    </div>
</div>

<!-- Explore Section -->
<div id="explore" class="container py-5 border-top border-light-subtle">
    <div class="text-center mb-5">
        <h2 class="pf-font-title h1">Explore featured profiles.</h2>
        <p class="text-muted">See portfolios crafted by members of our community.</p>
    </div>

    <div class="row g-4">
        <?php if (!empty($recent_portfolios)): ?>
            <?php foreach ($recent_portfolios as $portfolio): ?>
                <div class="col-md-4">
                    <div class="pf-card pf-card-hover h-100 d-flex flex-column p-4">
                        <div class="d-flex align-items-center mb-3">
                            <?php if (!empty($portfolio['profile_image'])): ?>
                                <img src="/PortfolioForge/uploads/<?php echo escape($portfolio['profile_image']); ?>" alt="Profile" class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                            <?php else: ?>
                                <div class="rounded-circle me-3 bg-secondary d-flex align-items-center justify-content-center text-white" style="width: 50px; height: 50px; font-weight: bold;">
                                    <?php echo strtoupper(substr($portfolio['full_name'], 0, 1)); ?>
                                </div>
                            <?php endif; ?>
                            <div>
                                <h3 class="h6 mb-0 pf-font-title"><?php echo escape($portfolio['full_name']); ?></h3>
                                <small class="text-muted"><?php echo escape($portfolio['title']); ?></small>
                            </div>
                        </div>
                        <p class="text-muted small flex-grow-1">
                            <?php echo escape(substr($portfolio['bio'] ?? '', 0, 100)) . (strlen($portfolio['bio'] ?? '') > 100 ? '...' : ''); ?>
                        </p>
                        <a href="/PortfolioForge/portfolio.php?username=<?php echo escape($portfolio['username']); ?>" class="btn pf-btn-primary btn-sm mt-3 w-100" target="_blank">View Portfolio</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <div class="pf-card p-5 max-width-600 mx-auto border-0 shadow-sm">
                    <p class="text-muted mb-0">No portfolios created yet. Be the first to build yours!</p>
                    <a href="/PortfolioForge/register.php" class="btn pf-btn-primary mt-3">Register Now</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Final CTA Section -->
<div class="container py-5 my-4">
    <div class="pf-cta-banner text-center p-5">
        <i class="bi bi-hexagon-fill pf-cta-icon mb-3"></i>
        <h2 class="pf-font-title h1 mb-3">Ready to forge your portfolio?</h2>
        <p class="text-muted mb-4 mx-auto" style="max-width: 500px;">Join <?php echo pf_format_count($user_count); ?> creators already showcasing their work with PortfolioForge.</p>
        <?php if (is_logged_in()): ?>
            <a href="/PortfolioForge/dashboard/index.php" class="btn pf-btn-primary btn-lg px-5">Go to Dashboard</a>
        <?php else: ?>
            <a href="/PortfolioForge/register.php" class="btn pf-btn-primary btn-lg px-5">Get Started Free</a>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
