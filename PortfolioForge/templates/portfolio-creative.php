<?php
/**
 * templates/portfolio-creative.php
 * "Creative Studio" layout - colorful editorial style for designers/creatives.
 * Available variables: $portfolio, $projects, $skills, $education, $experience, $resume, $portfolio_id
 */
$theme = $portfolio['theme'] === 'apple-dark' ? 'dark' : 'light';

// Build a lightweight "process" strip out of experience + education (most recent first, max 4)
$process_items = array_slice(array_merge($experience, $education), 0, 4);
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="<?php echo $theme; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo escape($portfolio['full_name']); ?><?php echo !empty($portfolio['title']) ? ' - ' . escape($portfolio['title']) : ' - Portfolio'; ?></title>
    <?php
        $pf_seo_desc = !empty($portfolio['bio']) ? mb_substr(trim(preg_replace('/\s+/', ' ', strip_tags($portfolio['bio']))), 0, 160) : ($portfolio['full_name'] . "'s professional portfolio.");
        $pf_seo_image = !empty($portfolio['profile_image']) ? '/PortfolioForge/uploads/' . $portfolio['profile_image'] : '/PortfolioForge/assets/img/og-default.png';
        $pf_seo_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/PortfolioForge/portfolio.php?username=' . rawurlencode($portfolio['username']);
    ?>
    <meta name="description" content="<?php echo escape($pf_seo_desc); ?>">
    <meta property="og:type" content="profile">
    <meta property="og:title" content="<?php echo escape($portfolio['full_name']); ?><?php echo !empty($portfolio['title']) ? ' - ' . escape($portfolio['title']) : ''; ?>">
    <meta property="og:description" content="<?php echo escape($pf_seo_desc); ?>">
    <meta property="og:image" content="<?php echo escape($pf_seo_url); ?>">
    <meta property="og:url" content="<?php echo escape($pf_seo_url); ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo escape($portfolio['full_name']); ?>">
    <meta name="twitter:description" content="<?php echo escape($pf_seo_desc); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/PortfolioForge/assets/css/style.css">
    <style>
        :root {
            --pf-cr-bg: #fbf4f1;
            --pf-cr-ink: #2a1f3d;
            --pf-cr-accent: #c76b52;
            --pf-cr-lilac: #a892c9;
            --pf-cr-muted: #7a6f85;
            --pf-cr-card: #ffffff;
        }
        [data-bs-theme="dark"] {
            --pf-cr-bg: #1a1420;
            --pf-cr-ink: #f3ecf7;
            --pf-cr-card: #241b2e;
            --pf-cr-muted: #b4a7c2;
        }
        body.pf-tpl-creative { background: var(--pf-cr-bg) !important; color: var(--pf-cr-ink) !important; }
        .pf-tpl-creative h1, .pf-tpl-creative h2, .pf-tpl-creative h3, .pf-tpl-creative h4 {
            font-family: 'Playfair Display', serif; color: var(--pf-cr-ink);
        }
        .pf-tpl-creative .text-muted { color: var(--pf-cr-muted) !important; }
        .pf-cr-navbar { background: rgba(251,244,241,0.85); backdrop-filter: blur(14px); border-bottom: 1px solid rgba(0,0,0,0.06); }
        [data-bs-theme="dark"] .pf-cr-navbar { background: rgba(26,20,32,0.85); border-color: rgba(255,255,255,0.08); }
        .pf-cr-brand { font-family: 'Playfair Display', serif; font-weight: 700; color: var(--pf-cr-ink) !important; letter-spacing: 0.02em; }
        .pf-cr-nav-link { color: var(--pf-cr-muted) !important; font-weight: 500; }
        .pf-cr-nav-link:hover { color: var(--pf-cr-accent) !important; }
        .pf-cr-btn-primary {
            background: var(--pf-cr-accent); color: #fff; border-radius: 980px; padding: 0.65rem 1.75rem; font-weight: 600; border: none;
            transition: transform 0.2s ease;
        }
        .pf-cr-btn-primary:hover { transform: translateY(-2px); color: #fff; }
        .pf-cr-btn-outline {
            border: 1px solid var(--pf-cr-ink); color: var(--pf-cr-ink); border-radius: 980px; padding: 0.65rem 1.75rem; font-weight: 600; background: transparent;
        }
        .pf-cr-hero {
            padding-top: 8rem; padding-bottom: 3rem;
            background: radial-gradient(circle at 80% 20%, rgba(168,146,201,0.25), transparent 55%);
        }
        .pf-cr-kicker {
            font-size: 0.8rem; letter-spacing: 0.15em; text-transform: uppercase; color: var(--pf-cr-accent); font-weight: 600;
        }
        .pf-cr-avatar-blob {
            width: 280px; height: 280px; border-radius: 50% 50% 45% 55% / 55% 45% 55% 45%;
            overflow: hidden; margin: 0 auto; background: linear-gradient(135deg, var(--pf-cr-lilac), var(--pf-cr-accent));
            display: flex; align-items: center; justify-content: center; box-shadow: 0 20px 50px rgba(199,107,82,0.25);
        }
        .pf-cr-avatar-blob img { width: 100%; height: 100%; object-fit: cover; }
        .pf-cr-card {
            background: var(--pf-cr-card); border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(42,31,61,0.06);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        .pf-cr-card:hover { transform: translateY(-5px); box-shadow: 0 16px 40px rgba(42,31,61,0.12); }
        .pf-cr-sidebar-panel {
            background: var(--pf-cr-ink); color: #fff; border-radius: 20px; padding: 2rem;
        }
        .pf-cr-sidebar-panel h3 { color: #fff; }
        .pf-cr-sidebar-item { border-bottom: 1px solid rgba(255,255,255,0.1); padding: 0.9rem 0; }
        .pf-cr-sidebar-item:last-child { border-bottom: none; }
        .pf-cr-sidebar-icon { color: var(--pf-cr-lilac); }
        .pf-cr-project-thumb {
            height: 190px; border-radius: 20px 20px 0 0; background: linear-gradient(135deg, rgba(168,146,201,0.35), rgba(199,107,82,0.25));
            display: flex; align-items: center; justify-content: center; font-size: 2rem; color: var(--pf-cr-accent);
        }
        .pf-cr-process-step {
            width: 46px; height: 46px; border-radius: 50%; background: var(--pf-cr-accent); color: #fff;
            display: flex; align-items: center; justify-content: center; font-weight: 700; margin: 0 auto 1rem;
            font-family: 'Playfair Display', serif;
        }
        .pf-cr-form-control {
            background: var(--pf-cr-card); border: 1px solid rgba(0,0,0,0.08); border-radius: 12px; padding: 0.65rem 0.9rem; color: var(--pf-cr-ink);
        }
        [data-bs-theme="dark"] .pf-cr-form-control { border-color: rgba(255,255,255,0.1); }
        .pf-cr-form-control:focus { border-color: var(--pf-cr-accent); box-shadow: 0 0 0 3px rgba(199,107,82,0.15); }
        .pf-cr-social {
            width: 42px; height: 42px; border-radius: 50%; border: 1px solid rgba(42,31,61,0.15); color: var(--pf-cr-ink);
            display: flex; align-items: center; justify-content: center; transition: all 0.2s ease;
        }
        [data-bs-theme="dark"] .pf-cr-social { border-color: rgba(255,255,255,0.15); }
        .pf-cr-social:hover { background: var(--pf-cr-accent); border-color: var(--pf-cr-accent); color: #fff; }
    </style>
</head>
<body class="pf-tpl-creative" data-bs-spy="scroll" data-bs-target="#portfolioNav" data-bs-offset="80">
<?php require __DIR__ . '/../includes/share_widget.php'; ?>

<div class="scroll-progress-bar" id="scroll-progress"></div>

<nav class="navbar navbar-expand-lg fixed-top pf-cr-navbar" id="portfolioNav">
    <div class="container">
        <a class="navbar-brand pf-cr-brand" href="#about"><?php echo escape($portfolio['full_name']); ?></a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                <li class="nav-item"><a class="nav-link pf-cr-nav-link" href="#about">About</a></li>
                <?php if (!empty($projects)): ?><li class="nav-item"><a class="nav-link pf-cr-nav-link" href="#projects">Work</a></li><?php endif; ?>
                <?php if (!empty($skills)): ?><li class="nav-item"><a class="nav-link pf-cr-nav-link" href="#skills">What I Do</a></li><?php endif; ?>
                <?php if (!empty($process_items)): ?><li class="nav-item"><a class="nav-link pf-cr-nav-link" href="#process">Process</a></li><?php endif; ?>
                <li class="nav-item"><a class="nav-link pf-cr-nav-link" href="#contact">Contact</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero -->
<section id="about" class="pf-cr-hero">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-7 order-2 order-lg-1 text-center text-lg-start">
                <div class="pf-cr-kicker mb-2">Creative Portfolio</div>
                <h1 class="display-3 fw-bold mb-3"><?php echo escape($portfolio['full_name']); ?></h1>
                <?php if (!empty($portfolio['available_for_hire'])): ?>
                    <span class="badge rounded-pill text-bg-success mb-3"><i class="bi bi-check-circle-fill me-1"></i>Available for Hire</span>
                <?php endif; ?>
                <h2 class="h4 mb-3" style="color: var(--pf-cr-accent); font-family: 'Inter', sans-serif; font-weight: 600;"><?php echo escape($portfolio['title']); ?></h2>
                <p class="lead text-muted mb-4 mx-auto mx-lg-0" style="max-width: 550px;"><?php echo nl2br(escape($portfolio['bio'])); ?></p>
                <div class="d-flex flex-wrap justify-content-center justify-content-lg-start gap-3 mb-4">
                    <a href="#projects" class="btn pf-cr-btn-primary">View My Work</a>
                    <a href="#contact" class="btn pf-cr-btn-outline">Get In Touch</a>
                </div>
                <div class="d-flex justify-content-center justify-content-lg-start gap-3">
                    <?php if (!empty($portfolio['github'])): ?><a href="<?php echo escape($portfolio['github']); ?>" target="_blank" class="pf-cr-social"><i class="bi bi-github"></i></a><?php endif; ?>
                    <?php if (!empty($portfolio['linkedin'])): ?><a href="<?php echo escape($portfolio['linkedin']); ?>" target="_blank" class="pf-cr-social"><i class="bi bi-linkedin"></i></a><?php endif; ?>
                    <?php if (!empty($portfolio['twitter'])): ?><a href="<?php echo escape($portfolio['twitter']); ?>" target="_blank" class="pf-cr-social"><i class="bi bi-twitter-x"></i></a><?php endif; ?>
                    <?php if (!empty($portfolio['website'])): ?><a href="<?php echo escape($portfolio['website']); ?>" target="_blank" class="pf-cr-social"><i class="bi bi-globe"></i></a><?php endif; ?>
                </div>
            </div>
            <div class="col-lg-5 order-1 order-lg-2">
                <div class="pf-cr-avatar-blob">
                    <?php if (!empty($portfolio['profile_image'])): ?>
                        <img src="/PortfolioForge/uploads/<?php echo escape($portfolio['profile_image']); ?>" alt="<?php echo escape($portfolio['full_name']); ?>">
                    <?php else: ?>
                        <i class="bi bi-person-fill" style="font-size: 6rem; color: rgba(255,255,255,0.85);"></i>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Selected Work + What I Do sidebar -->
<div class="container py-5">
    <div class="row g-4">
        <div class="col-lg-8">
            <?php if (!empty($projects)): ?>
                <div id="projects">
                    <div class="d-flex justify-content-between align-items-end mb-4">
                        <h2 class="h3 fw-bold mb-0">Selected Work</h2>
                        <?php if ($resume): ?>
                            <a href="/PortfolioForge/uploads/<?php echo escape($resume['file_path']); ?>" target="_blank" class="text-decoration-none small fw-semibold" style="color: var(--pf-cr-accent);">Download CV <i class="bi bi-arrow-down-circle"></i></a>
                        <?php endif; ?>
                    </div>
                    <div class="row g-4">
                        <?php foreach ($projects as $proj): ?>
                            <div class="col-md-6">
                                <div class="pf-cr-card h-100 d-flex flex-column overflow-hidden">
                                    <?php if (!empty($proj['image'])): ?>
                                        <img src="/PortfolioForge/uploads/<?php echo escape($proj['image']); ?>" alt="Project thumbnail" style="height: 190px; object-fit: cover; border-radius: 20px 20px 0 0;">
                                    <?php else: ?>
                                        <div class="pf-cr-project-thumb"><i class="bi bi-brush-fill"></i></div>
                                    <?php endif; ?>
                                    <div class="p-4 d-flex flex-column flex-grow-1">
                                        <h3 class="h6 fw-bold mb-2" style="font-family: 'Inter', sans-serif;"><?php echo escape($proj['title']); ?></h3>
                                        <p class="text-muted small flex-grow-1"><?php echo nl2br(escape($proj['description'])); ?></p>
                                        <div class="d-flex gap-3 mt-2">
                                            <?php if (!empty($proj['link'])): ?><a href="<?php echo escape($proj['link']); ?>" target="_blank" class="text-decoration-none small fw-semibold" style="color: var(--pf-cr-accent);">View Project <i class="bi bi-arrow-up-right"></i></a><?php endif; ?>
                                            <?php if (!empty($proj['github_link'])): ?><a href="<?php echo escape($proj['github_link']); ?>" target="_blank" class="text-decoration-none small text-muted"><i class="bi bi-github"></i></a><?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="pf-cr-card p-5 text-center">
                    <p class="text-muted mb-0">No projects added yet.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- What I Do sidebar (built from skills) -->
        <div class="col-lg-4" id="skills">
            <div class="pf-cr-sidebar-panel h-100">
                <h3 class="h5 mb-3">What I Do</h3>
                <?php if (!empty($skills)): ?>
                    <?php foreach (array_slice($skills, 0, 6) as $skill): ?>
                        <div class="pf-cr-sidebar-item d-flex align-items-center gap-3">
                            <i class="bi bi-stars pf-cr-sidebar-icon"></i>
                            <div>
                                <div class="fw-semibold small"><?php echo escape($skill['name']); ?></div>
                                <div class="text-white-50" style="font-size: 0.75rem;"><?php echo escape($skill['category']); ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-white-50 small mb-0">Skills will appear here once added.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Process (built from experience/education) -->
<?php if (!empty($process_items)): ?>
<section id="process" class="container py-5 border-top" style="border-color: rgba(0,0,0,0.06) !important;">
    <div class="text-center mb-5">
        <div class="pf-cr-kicker mb-2">How I Work</div>
        <h2 class="h3 fw-bold">My Background</h2>
    </div>
    <div class="row g-4 text-center">
        <?php foreach ($process_items as $i => $item): ?>
            <div class="col-md-3 col-6">
                <div class="pf-cr-process-step"><?php echo str_pad($i + 1, 2, '0', STR_PAD_LEFT); ?></div>
                <h4 class="h6 fw-bold mb-1" style="font-family: 'Inter', sans-serif;">
                    <?php echo escape($item['position'] ?? (($item['degree'] ?? '') . ' ' . ($item['field_of_study'] ?? ''))); ?>
                </h4>
                <p class="text-muted small mb-0"><?php echo escape($item['company'] ?? ($item['institution'] ?? '')); ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- Contact -->
<section id="contact" class="py-5 mt-3" style="background: var(--pf-cr-ink);">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-5 text-white">
                <div class="pf-cr-kicker mb-2" style="color: var(--pf-cr-lilac);">Let's Talk</div>
                <h2 class="h3 fw-bold mb-3 text-white">Let's create something beautiful.</h2>
                <p class="text-white-50 mb-4">I'm always open to discussing new projects and opportunities.</p>
                <div class="d-flex flex-column gap-2 text-white-50 small">
                    <?php if (!empty($portfolio['website'])): ?><div><i class="bi bi-globe me-2"></i><?php echo escape($portfolio['website']); ?></div><?php endif; ?>
                    <div><i class="bi bi-person-badge me-2"></i><?php echo escape($portfolio['title']); ?></div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="pf-cr-card p-5">
                    <div id="form-alert-container"></div>
                    <form id="portfolio-contact-form">
                        <input type="hidden" name="portfolio_id" value="<?php echo $portfolio_id; ?>">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Your Name</label>
                                <input type="text" class="form-control pf-cr-form-control" name="name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Your Email</label>
                                <input type="email" class="form-control pf-cr-form-control" name="email" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Subject</label>
                            <input type="text" class="form-control pf-cr-form-control" name="subject">
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Message</label>
                            <textarea class="form-control pf-cr-form-control" name="message" rows="4" required></textarea>
                        </div>
                        <button type="submit" class="btn pf-cr-btn-primary w-100 py-2">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<footer class="py-4 text-center">
    <p class="text-muted small mb-0">&copy; <?php echo date('Y'); ?> <?php echo escape($portfolio['full_name']); ?>. Built with PortfolioForge.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="/PortfolioForge/assets/js/main.js"></script>
</body>
</html>
