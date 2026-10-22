<?php
/**
 * templates/portfolio-minimal.php
 * "Minimal Apple" layout - clean glassmorphism, light or dark mode.
 * Available variables: $portfolio, $projects, $skills, $education, $experience, $resume, $portfolio_id
 */
$theme = $portfolio['theme'] === 'apple-dark' ? 'dark' : 'light';
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
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts (Outfit & Inter) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <!-- Custom Style -->
    <link rel="stylesheet" href="/PortfolioForge/assets/css/style.css">
</head>
<body data-bs-spy="scroll" data-bs-target="#portfolioNav" data-bs-offset="80">
<?php require __DIR__ . '/../includes/share_widget.php'; ?>

<!-- Scroll Progress Bar -->
<div class="scroll-progress-bar" id="scroll-progress"></div>

<!-- Portfolio Specific Navbar -->
<nav class="navbar navbar-expand-lg fixed-top pf-navbar" id="portfolioNav">
    <div class="container">
        <a class="navbar-brand pf-brand" href="#about">
            <?php echo escape($portfolio['full_name']); ?>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item"><a class="nav-link pf-nav-link" href="#about">About</a></li>
                <?php if (!empty($experience) || !empty($education)): ?>
                    <li class="nav-item"><a class="nav-link pf-nav-link" href="#history">Timeline</a></li>
                <?php endif; ?>
                <?php if (!empty($projects)): ?>
                    <li class="nav-item"><a class="nav-link pf-nav-link" href="#projects">Projects</a></li>
                <?php endif; ?>
                <?php if (!empty($skills)): ?>
                    <li class="nav-item"><a class="nav-link pf-nav-link" href="#skills">Skills</a></li>
                <?php endif; ?>
                <li class="nav-item"><a class="nav-link pf-nav-link" href="#contact">Contact</a></li>
                
                <?php if ($resume): ?>
                    <li class="nav-item ms-lg-2">
                        <a class="btn pf-btn-primary btn-sm" href="/PortfolioForge/uploads/<?php echo escape($resume['file_path']); ?>" target="_blank">Resume</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<div class="pf-page-wrapper">
    <!-- About / Hero Section -->
    <section id="about" class="pf-portfolio-hero py-5 mt-5">
        <div class="container">
            <div class="row align-items-center justify-content-center">
                <div class="col-md-3 text-center mb-4 mb-md-0">
                    <div class="pf-avatar-wrapper">
                        <img src="<?php echo !empty($portfolio['profile_image']) ? '/PortfolioForge/uploads/' . escape($portfolio['profile_image']) : 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=150&h=150&fit=crop'; ?>" alt="Profile avatar" class="pf-avatar">
                    </div>
                </div>
                <div class="col-md-8 text-center text-md-start">
                    <h1 class="display-4 pf-font-title mb-2"><?php echo escape($portfolio['full_name']); ?></h1>
                    <?php if (!empty($portfolio['available_for_hire'])): ?>
                        <span class="badge rounded-pill text-bg-success mb-2"><i class="bi bi-check-circle-fill me-1"></i>Available for Hire</span>
                    <?php endif; ?>
                    <h2 class="h4 text-primary mb-3"><?php echo escape($portfolio['title']); ?></h2>
                    <p class="lead text-muted mb-4" style="max-width: 700px;"><?php echo nl2br(escape($portfolio['bio'])); ?></p>
                    
                    <!-- Social Icons -->
                    <div class="d-flex justify-content-center justify-content-md-start gap-3">
                        <?php if (!empty($portfolio['github'])): ?>
                            <a href="<?php echo escape($portfolio['github']); ?>" target="_blank" class="btn pf-btn-secondary px-3 py-1.5 small">GitHub</a>
                        <?php endif; ?>
                        <?php if (!empty($portfolio['linkedin'])): ?>
                            <a href="<?php echo escape($portfolio['linkedin']); ?>" target="_blank" class="btn pf-btn-secondary px-3 py-1.5 small">LinkedIn</a>
                        <?php endif; ?>
                        <?php if (!empty($portfolio['twitter'])): ?>
                            <a href="<?php echo escape($portfolio['twitter']); ?>" target="_blank" class="btn pf-btn-secondary px-3 py-1.5 small">Twitter</a>
                        <?php endif; ?>
                        <?php if (!empty($portfolio['website'])): ?>
                            <a href="<?php echo escape($portfolio['website']); ?>" target="_blank" class="btn pf-btn-secondary px-3 py-1.5 small">Website</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- History / Timeline Section -->
    <?php if (!empty($experience) || !empty($education)): ?>
        <section id="history" class="container py-5 border-top border-light-subtle">
            <div class="row g-5">
                <!-- Experience -->
                <?php if (!empty($experience)): ?>
                    <div class="col-md-6">
                        <h2 class="pf-font-title h3 mb-4">Experience</h2>
                        <div class="pf-timeline">
                            <?php foreach ($experience as $exp): ?>
                                <div class="pf-timeline-item">
                                    <h4 class="h6 mb-1 text-dark-emphasis fw-bold"><?php echo escape($exp['position']); ?></h4>
                                    <p class="mb-1 text-muted small fw-medium"><?php echo escape($exp['company']); ?> <?php echo !empty($exp['location']) ? ' - ' . escape($exp['location']) : ''; ?></p>
                                    <p class="mb-2 text-muted x-small">
                                        <?php 
                                            $start = date('M Y', strtotime($exp['start_date']));
                                            $end = ($exp['current'] == 1) ? 'Present' : date('M Y', strtotime($exp['end_date']));
                                            echo "$start - $end";
                                        ?>
                                    </p>
                                    <?php if (!empty($exp['description'])): ?>
                                        <p class="text-muted small mb-0"><?php echo nl2br(escape($exp['description'])); ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Education -->
                <?php if (!empty($education)): ?>
                    <div class="col-md-6">
                        <h2 class="pf-font-title h3 mb-4">Education</h2>
                        <div class="pf-timeline">
                            <?php foreach ($education as $edu): ?>
                                <div class="pf-timeline-item">
                                    <h4 class="h6 mb-1 text-dark-emphasis fw-bold"><?php echo escape($edu['degree']); ?> in <?php echo escape($edu['field_of_study']); ?></h4>
                                    <p class="mb-1 text-muted small fw-medium"><?php echo escape($edu['institution']); ?></p>
                                    <p class="mb-2 text-muted x-small">
                                        <?php 
                                            $start = date('M Y', strtotime($edu['start_date']));
                                            $end = $edu['end_date'] ? date('M Y', strtotime($edu['end_date'])) : 'Present';
                                            echo "$start - $end";
                                        ?>
                                    </p>
                                    <?php if (!empty($edu['description'])): ?>
                                        <p class="text-muted small mb-0"><?php echo nl2br(escape($edu['description'])); ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    <?php endif; ?>

    <!-- Projects Section -->
    <?php if (!empty($projects)): ?>
        <section id="projects" class="container py-5 border-top border-light-subtle">
            <h2 class="pf-font-title h3 mb-5 text-center text-md-start">Featured Projects</h2>
            <div class="row g-4">
                <?php foreach ($projects as $proj): ?>
                    <div class="col-md-4">
                        <div class="pf-card pf-card-hover h-100 d-flex flex-column border-0">
                            <?php if (!empty($proj['image'])): ?>
                                <img src="/PortfolioForge/uploads/<?php echo escape($proj['image']); ?>" alt="Project thumbnail" class="card-img-top" style="height: 180px; object-fit: cover; border-top-left-radius: 18px; border-top-right-radius: 18px;">
                            <?php else: ?>
                                <div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 180px; border-top-left-radius: 18px; border-top-right-radius: 18px; font-weight: bold; font-size: 1.5rem;">
                                    <i class="bi bi-rocket-takeoff-fill me-2"></i> Showcase
                                </div>
                            <?php endif; ?>
                            <div class="p-4 d-flex flex-column flex-grow-1">
                                <h3 class="h5 pf-font-title mb-2"><?php echo escape($proj['title']); ?></h3>
                                <p class="text-muted small flex-grow-1"><?php echo nl2br(escape($proj['description'])); ?></p>
                                <div class="d-flex gap-3 mt-3">
                                    <?php if (!empty($proj['link'])): ?>
                                        <a href="<?php echo escape($proj['link']); ?>" target="_blank" class="text-decoration-none small">Live Demo &rarr;</a>
                                    <?php endif; ?>
                                    <?php if (!empty($proj['github_link'])): ?>
                                        <a href="<?php echo escape($proj['github_link']); ?>" target="_blank" class="text-decoration-none small text-success">GitHub &rarr;</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <!-- Skills Section -->
    <?php if (!empty($skills)): ?>
        <section id="skills" class="container py-5 border-top border-light-subtle">
            <h2 class="pf-font-title h3 mb-5 text-center text-md-start">Skills & Expertise</h2>
            
            <?php 
            // Group skills by category dynamically
            $grouped_skills = [];
            foreach ($skills as $skill) {
                $grouped_skills[$skill['category']][] = $skill;
            }
            ?>

            <div class="row g-4">
                <?php foreach ($grouped_skills as $category => $cat_skills): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="pf-card p-4 h-100 border-0">
                            <h3 class="h6 pf-font-title text-primary mb-3 text-uppercase fw-bold"><?php echo escape($category); ?></h3>
                            <div class="d-flex flex-column gap-3">
                                <?php foreach ($cat_skills as $skill): ?>
                                    <div>
                                        <div class="d-flex justify-content-between text-muted small mb-1">
                                            <span class="text-dark-emphasis fw-medium"><?php echo escape($skill['name']); ?></span>
                                            <span><?php echo $skill['percentage']; ?>%</span>
                                        </div>
                                        <div class="progress" style="height: 4px;">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo $skill['percentage']; ?>%;" aria-valuenow="<?php echo $skill['percentage']; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <!-- Contact Form Section -->
    <section id="contact" class="container py-5 border-top border-light-subtle mb-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="pf-card p-5 border-0">
                    <h2 class="pf-font-title h3 mb-2 text-center">Get in Touch</h2>
                    <p class="text-muted small text-center mb-4">Send a message directly to my inbox.</p>

                    <!-- Alert Box -->
                    <div id="form-alert-container"></div>

                    <form id="portfolio-contact-form">
                        <input type="hidden" name="portfolio_id" value="<?php echo $portfolio_id; ?>">
                        
                        <div class="mb-3">
                            <label for="name" class="form-label small fw-bold">Your Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label small fw-bold">Your Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="subject" class="form-label small fw-bold">Subject</label>
                            <input type="text" class="form-control" id="subject" name="subject">
                        </div>
                        <div class="mb-4">
                            <label for="message" class="form-label small fw-bold">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn pf-btn-primary w-100 py-2.5">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Layout Footer -->
<footer class="pf-footer py-5 mt-auto">
    <div class="container text-center">
        <p class="text-muted small">&copy; <?php echo date('Y'); ?> <?php echo escape($portfolio['full_name']); ?>. All rights reserved.</p>
        <p class="text-muted small mb-0">Powered by <a href="/PortfolioForge/index.php" class="text-decoration-none text-muted fw-bold">PortfolioForge</a></p>
    </div>
</footer>

<!-- Bootstrap 5 Bundle with Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Theme Controller JS -->
<script src="/PortfolioForge/assets/js/theme.js"></script>
<!-- Main Application JS -->
<script src="/PortfolioForge/assets/js/main.js"></script>
</body>
</html>
