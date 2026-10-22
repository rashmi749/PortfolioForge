<?php
/**
 * templates/portfolio-developer.php
 * "Developer Dark" layout - bold dark theme, code-inspired hero, stat counters.
 * Available variables: $portfolio, $projects, $skills, $education, $experience, $resume, $portfolio_id
 */

// This layout is always dark, regardless of the light/dark mode setting.
$grouped_skills = [];
foreach ($skills as $skill) {
    $grouped_skills[$skill['category']][] = $skill;
}
$top_skills = array_slice($skills, 0, 8);
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/PortfolioForge/assets/css/style.css">
    <style>
        :root {
            --pf-dev-bg: #0b0d12;
            --pf-dev-surface: #12151d;
            --pf-dev-border: rgba(255,255,255,0.08);
            --pf-dev-accent: #7c5cff;
            --pf-dev-accent-2: #22d3ee;
            --pf-dev-text: #e6e8ef;
            --pf-dev-muted: #8b90a3;
        }
        body.pf-tpl-developer {
            background: var(--pf-dev-bg) !important;
            color: var(--pf-dev-text) !important;
        }
        .pf-tpl-developer h1, .pf-tpl-developer h2, .pf-tpl-developer h3, .pf-tpl-developer h4 { color: #fff; }
        .pf-tpl-developer .text-muted { color: var(--pf-dev-muted) !important; }
        .pf-dev-navbar {
            background: rgba(11,13,18,0.85);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--pf-dev-border);
        }
        .pf-dev-brand { font-family: 'JetBrains Mono', monospace; font-weight: 700; color: #fff !important; }
        .pf-dev-brand .accent { color: var(--pf-dev-accent); }
        .pf-dev-nav-link { color: var(--pf-dev-muted) !important; font-weight: 500; }
        .pf-dev-nav-link:hover { color: #fff !important; }
        .pf-dev-btn-primary {
            background: linear-gradient(135deg, var(--pf-dev-accent), #a78bfa);
            color: #fff; border: none; border-radius: 10px; font-weight: 600; padding: 0.65rem 1.5rem;
            transition: transform 0.2s ease;
        }
        .pf-dev-btn-primary:hover { transform: translateY(-2px); color: #fff; }
        .pf-dev-btn-outline {
            border: 1px solid var(--pf-dev-border); color: #fff; border-radius: 10px; font-weight: 600; padding: 0.65rem 1.5rem; background: transparent;
        }
        .pf-dev-btn-outline:hover { border-color: var(--pf-dev-accent); color: #fff; }
        .pf-dev-hero { padding-top: 8rem; padding-bottom: 4rem; position: relative; overflow: hidden; }
        .pf-dev-hero::before {
            content: ''; position: absolute; top: -100px; right: -100px; width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(124,92,255,0.25), transparent 70%); border-radius: 50%;
        }
        .pf-dev-kicker {
            display: inline-block; background: rgba(124,92,255,0.12); color: var(--pf-dev-accent);
            font-family: 'JetBrains Mono', monospace; font-size: 0.8rem; padding: 0.3rem 0.9rem; border-radius: 980px; border: 1px solid rgba(124,92,255,0.3);
        }
        .pf-dev-avatar-frame {
            width: 260px; height: 260px; border-radius: 24px; overflow: hidden; background: var(--pf-dev-surface);
            border: 1px solid var(--pf-dev-border); display: flex; align-items: center; justify-content: center;
            margin: 0 auto; position: relative;
        }
        .pf-dev-avatar-frame img { width: 100%; height: 100%; object-fit: cover; }
        .pf-dev-code-chip {
            position: absolute; bottom: -20px; left: -30px; background: var(--pf-dev-surface); border: 1px solid var(--pf-dev-border);
            border-radius: 12px; padding: 0.75rem 1rem; font-family: 'JetBrains Mono', monospace; font-size: 0.7rem; color: var(--pf-dev-accent-2);
            box-shadow: 0 10px 30px rgba(0,0,0,0.4);
        }
        .pf-dev-card {
            background: var(--pf-dev-surface); border: 1px solid var(--pf-dev-border); border-radius: 16px; transition: all 0.25s ease;
        }
        .pf-dev-card:hover { transform: translateY(-4px); border-color: rgba(124,92,255,0.4); }
        .pf-dev-stat-icon {
            width: 44px; height: 44px; border-radius: 12px; background: rgba(124,92,255,0.15); color: var(--pf-dev-accent);
            display: flex; align-items: center; justify-content: center; font-size: 1.1rem;
        }
        .pf-dev-skill-bar { height: 6px; background: rgba(255,255,255,0.08); border-radius: 980px; overflow: hidden; }
        .pf-dev-skill-bar-fill { height: 100%; background: linear-gradient(90deg, var(--pf-dev-accent), var(--pf-dev-accent-2)); border-radius: 980px; }
        .pf-dev-project-thumb {
            height: 170px; border-radius: 16px 16px 0 0; background: linear-gradient(135deg, rgba(124,92,255,0.25), rgba(34,211,238,0.15));
            display: flex; align-items: center; justify-content: center; font-size: 2rem; color: var(--pf-dev-accent);
        }
        .pf-dev-timeline { border-left: 2px solid var(--pf-dev-border); padding-left: 1.5rem; margin-left: 0.5rem; }
        .pf-dev-timeline-item { position: relative; margin-bottom: 2rem; }
        .pf-dev-timeline-item::before {
            content: ''; position: absolute; left: -1.62rem; top: 5px; width: 10px; height: 10px; border-radius: 50%;
            background: var(--pf-dev-accent); box-shadow: 0 0 0 4px rgba(124,92,255,0.2);
        }
        .pf-dev-form-control {
            background: var(--pf-dev-bg); border: 1px solid var(--pf-dev-border); color: #fff; border-radius: 10px; padding: 0.65rem 0.9rem;
        }
        .pf-dev-form-control:focus { border-color: var(--pf-dev-accent); box-shadow: 0 0 0 3px rgba(124,92,255,0.2); color: #fff; background: var(--pf-dev-bg); }
        .pf-dev-social {
            width: 40px; height: 40px; border-radius: 10px; border: 1px solid var(--pf-dev-border); color: var(--pf-dev-muted);
            display: flex; align-items: center; justify-content: center; transition: all 0.2s ease;
        }
        .pf-dev-social:hover { color: #fff; border-color: var(--pf-dev-accent); }
    </style>
</head>
<body class="pf-tpl-developer" data-bs-spy="scroll" data-bs-target="#portfolioNav" data-bs-offset="80">
<?php require __DIR__ . '/../includes/share_widget.php'; ?>

<div class="scroll-progress-bar" id="scroll-progress"></div>

<nav class="navbar navbar-expand-lg fixed-top pf-dev-navbar" id="portfolioNav">
    <div class="container">
        <a class="navbar-brand pf-dev-brand" href="#about">&lt;/&gt; <?php echo escape($portfolio['full_name']); ?></a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                <li class="nav-item"><a class="nav-link pf-dev-nav-link" href="#about">About</a></li>
                <?php if (!empty($skills)): ?><li class="nav-item"><a class="nav-link pf-dev-nav-link" href="#skills">Skills</a></li><?php endif; ?>
                <?php if (!empty($projects)): ?><li class="nav-item"><a class="nav-link pf-dev-nav-link" href="#projects">Projects</a></li><?php endif; ?>
                <?php if (!empty($experience) || !empty($education)): ?><li class="nav-item"><a class="nav-link pf-dev-nav-link" href="#history">Timeline</a></li><?php endif; ?>
                <li class="nav-item"><a class="nav-link pf-dev-nav-link" href="#contact">Contact</a></li>
                <?php if ($resume): ?>
                    <li class="nav-item ms-lg-2"><a class="btn pf-dev-btn-primary btn-sm" href="/PortfolioForge/uploads/<?php echo escape($resume['file_path']); ?>" target="_blank">Resume</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero -->
<section id="about" class="pf-dev-hero">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-7 order-2 order-lg-1">
                <span class="pf-dev-kicker mb-3 d-inline-block">$ whoami</span>
                <h1 class="display-4 fw-bold mb-2">Hi, I'm <?php echo escape($portfolio['full_name']); ?></h1>
                <?php if (!empty($portfolio['available_for_hire'])): ?>
                    <span class="badge rounded-pill text-bg-success mb-2"><i class="bi bi-check-circle-fill me-1"></i>Available for Hire</span>
                <?php endif; ?>
                <h2 class="h4 mb-3" style="color: var(--pf-dev-accent-2);"><?php echo escape($portfolio['title']); ?></h2>
                <p class="lead text-muted mb-4" style="max-width: 600px;"><?php echo nl2br(escape($portfolio['bio'])); ?></p>
                <div class="d-flex flex-wrap gap-3 mb-4">
                    <a href="#projects" class="btn pf-dev-btn-primary">View My Work</a>
                    <?php if ($resume): ?>
                        <a href="/PortfolioForge/uploads/<?php echo escape($resume['file_path']); ?>" target="_blank" class="btn pf-dev-btn-outline"><i class="bi bi-download me-1"></i> Download CV</a>
                    <?php endif; ?>
                </div>
                <div class="d-flex gap-3">
                    <?php if (!empty($portfolio['github'])): ?><a href="<?php echo escape($portfolio['github']); ?>" target="_blank" class="pf-dev-social"><i class="bi bi-github"></i></a><?php endif; ?>
                    <?php if (!empty($portfolio['linkedin'])): ?><a href="<?php echo escape($portfolio['linkedin']); ?>" target="_blank" class="pf-dev-social"><i class="bi bi-linkedin"></i></a><?php endif; ?>
                    <?php if (!empty($portfolio['twitter'])): ?><a href="<?php echo escape($portfolio['twitter']); ?>" target="_blank" class="pf-dev-social"><i class="bi bi-twitter-x"></i></a><?php endif; ?>
                    <?php if (!empty($portfolio['website'])): ?><a href="<?php echo escape($portfolio['website']); ?>" target="_blank" class="pf-dev-social"><i class="bi bi-globe"></i></a><?php endif; ?>
                </div>
            </div>
            <div class="col-lg-5 order-1 order-lg-2 text-center">
                <div class="pf-dev-avatar-frame">
                    <?php if (!empty($portfolio['profile_image'])): ?>
                        <img src="/PortfolioForge/uploads/<?php echo escape($portfolio['profile_image']); ?>" alt="<?php echo escape($portfolio['full_name']); ?>">
                    <?php else: ?>
                        <i class="bi bi-person-fill" style="font-size: 5rem; color: var(--pf-dev-accent);"></i>
                    <?php endif; ?>
                    <div class="pf-dev-code-chip d-none d-md-block">
                        role: "<?php echo escape(substr($portfolio['title'] ?: 'Developer', 0, 18)); ?>"
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="row g-3 mt-5">
            <div class="col-6 col-md-3">
                <div class="pf-dev-card p-3 d-flex align-items-center gap-3">
                    <div class="pf-dev-stat-icon"><i class="bi bi-kanban-fill"></i></div>
                    <div><div class="fw-bold h5 mb-0"><?php echo count($projects); ?>+</div><small class="text-muted">Projects</small></div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="pf-dev-card p-3 d-flex align-items-center gap-3">
                    <div class="pf-dev-stat-icon"><i class="bi bi-cpu-fill"></i></div>
                    <div><div class="fw-bold h5 mb-0"><?php echo count($skills); ?>+</div><small class="text-muted">Skills</small></div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="pf-dev-card p-3 d-flex align-items-center gap-3">
                    <div class="pf-dev-stat-icon"><i class="bi bi-briefcase-fill"></i></div>
                    <div><div class="fw-bold h5 mb-0"><?php echo count($experience); ?>+</div><small class="text-muted">Roles</small></div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="pf-dev-card p-3 d-flex align-items-center gap-3">
                    <div class="pf-dev-stat-icon"><i class="bi bi-eye-fill"></i></div>
                    <div><div class="fw-bold h5 mb-0"><?php echo number_format($portfolio['views']); ?></div><small class="text-muted">Views</small></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Skills -->
<?php if (!empty($skills)): ?>
<section id="skills" class="container py-5">
    <div class="mb-4">
        <span class="pf-dev-kicker mb-2 d-inline-block">Skills.json</span>
        <h2 class="h3 fw-bold">Technologies I Work With</h2>
    </div>
    <div class="row g-4">
        <?php foreach ($top_skills as $skill): ?>
            <div class="col-md-6">
                <div class="d-flex justify-content-between small mb-1">
                    <span class="fw-semibold"><?php echo escape($skill['name']); ?></span>
                    <span class="text-muted"><?php echo $skill['percentage']; ?>%</span>
                </div>
                <div class="pf-dev-skill-bar"><div class="pf-dev-skill-bar-fill" style="width: <?php echo $skill['percentage']; ?>%;"></div></div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- Projects -->
<?php if (!empty($projects)): ?>
<section id="projects" class="container py-5">
    <div class="mb-4">
        <span class="pf-dev-kicker mb-2 d-inline-block">Featured Projects</span>
        <h2 class="h3 fw-bold">Some of My Recent Work</h2>
    </div>
    <div class="row g-4">
        <?php foreach ($projects as $proj): ?>
            <div class="col-md-4">
                <div class="pf-dev-card h-100 d-flex flex-column overflow-hidden">
                    <?php if (!empty($proj['image'])): ?>
                        <img src="/PortfolioForge/uploads/<?php echo escape($proj['image']); ?>" alt="Project thumbnail" style="height: 170px; object-fit: cover; border-radius: 16px 16px 0 0;">
                    <?php else: ?>
                        <div class="pf-dev-project-thumb"><i class="bi bi-code-square"></i></div>
                    <?php endif; ?>
                    <div class="p-4 d-flex flex-column flex-grow-1">
                        <h3 class="h6 fw-bold mb-2"><?php echo escape($proj['title']); ?></h3>
                        <p class="text-muted small flex-grow-1"><?php echo nl2br(escape($proj['description'])); ?></p>
                        <div class="d-flex gap-3 mt-2">
                            <?php if (!empty($proj['link'])): ?><a href="<?php echo escape($proj['link']); ?>" target="_blank" class="text-decoration-none small" style="color: var(--pf-dev-accent-2);">Live Demo <i class="bi bi-arrow-up-right"></i></a><?php endif; ?>
                            <?php if (!empty($proj['github_link'])): ?><a href="<?php echo escape($proj['github_link']); ?>" target="_blank" class="text-decoration-none small text-muted"><i class="bi bi-github"></i> Code</a><?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- Timeline -->
<?php if (!empty($experience) || !empty($education)): ?>
<section id="history" class="container py-5">
    <div class="mb-4">
        <span class="pf-dev-kicker mb-2 d-inline-block">Timeline</span>
        <h2 class="h3 fw-bold">Experience &amp; Education</h2>
    </div>
    <div class="row g-5">
        <?php if (!empty($experience)): ?>
            <div class="col-md-6">
                <h3 class="h6 text-uppercase text-muted mb-4">Experience</h3>
                <div class="pf-dev-timeline">
                    <?php foreach ($experience as $exp): ?>
                        <div class="pf-dev-timeline-item">
                            <h4 class="h6 fw-bold mb-1"><?php echo escape($exp['position']); ?></h4>
                            <p class="mb-1 text-muted small"><?php echo escape($exp['company']); ?><?php echo !empty($exp['location']) ? ' &middot; ' . escape($exp['location']) : ''; ?></p>
                            <p class="mb-2 small" style="color: var(--pf-dev-accent-2);">
                                <?php
                                    $start = date('M Y', strtotime($exp['start_date']));
                                    $end = ($exp['current'] == 1) ? 'Present' : date('M Y', strtotime($exp['end_date']));
                                    echo "$start - $end";
                                ?>
                            </p>
                            <?php if (!empty($exp['description'])): ?><p class="text-muted small mb-0"><?php echo nl2br(escape($exp['description'])); ?></p><?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        <?php if (!empty($education)): ?>
            <div class="col-md-6">
                <h3 class="h6 text-uppercase text-muted mb-4">Education</h3>
                <div class="pf-dev-timeline">
                    <?php foreach ($education as $edu): ?>
                        <div class="pf-dev-timeline-item">
                            <h4 class="h6 fw-bold mb-1"><?php echo escape($edu['degree']); ?> in <?php echo escape($edu['field_of_study']); ?></h4>
                            <p class="mb-1 text-muted small"><?php echo escape($edu['institution']); ?></p>
                            <p class="mb-2 small" style="color: var(--pf-dev-accent-2);">
                                <?php
                                    $start = date('M Y', strtotime($edu['start_date']));
                                    $end = $edu['end_date'] ? date('M Y', strtotime($edu['end_date'])) : 'Present';
                                    echo "$start - $end";
                                ?>
                            </p>
                            <?php if (!empty($edu['description'])): ?><p class="text-muted small mb-0"><?php echo nl2br(escape($edu['description'])); ?></p><?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<!-- Contact -->
<section id="contact" class="container py-5 mb-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="pf-dev-card p-5">
                <div class="text-center mb-4">
                    <span class="pf-dev-kicker mb-2 d-inline-block">Get In Touch</span>
                    <h2 class="h3 fw-bold">Let's Work Together</h2>
                    <p class="text-muted small mb-0">Have a project in mind? Send a message.</p>
                </div>
                <div id="form-alert-container"></div>
                <form id="portfolio-contact-form">
                    <input type="hidden" name="portfolio_id" value="<?php echo $portfolio_id; ?>">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Your Name</label>
                        <input type="text" class="form-control pf-dev-form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Your Email</label>
                        <input type="email" class="form-control pf-dev-form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Subject</label>
                        <input type="text" class="form-control pf-dev-form-control" name="subject">
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold">Message</label>
                        <textarea class="form-control pf-dev-form-control" name="message" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn pf-dev-btn-primary w-100 py-2">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</section>

<footer class="py-4 text-center" style="border-top: 1px solid var(--pf-dev-border);">
    <p class="text-muted small mb-0">&copy; <?php echo date('Y'); ?> <?php echo escape($portfolio['full_name']); ?>. Built with PortfolioForge.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="/PortfolioForge/assets/js/main.js"></script>
</body>
</html>
