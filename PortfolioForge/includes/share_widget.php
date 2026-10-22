<?php
/**
 * includes/share_widget.php
 * Floating share button for public portfolio pages.
 * Expects $portfolio (with 'username' via join) and $portfolio_id in scope.
 * Include this INSIDE the <body> of a portfolio template, e.g.:
 *   <?php require __DIR__ . '/../includes/share_widget.php'; ?>
 */
$pf_share_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https://' : 'http://')
    . $_SERVER['HTTP_HOST'] . '/PortfolioForge/portfolio.php?username=' . rawurlencode($portfolio['username']);
$pf_share_qr = 'https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=' . rawurlencode($pf_share_url);
?>
<div class="pf-share-widget">
    <button type="button" class="pf-share-toggle" onclick="document.getElementById('pfSharePanel').classList.toggle('pf-share-open')" title="Share this portfolio">
        <i class="bi bi-share-fill"></i>
    </button>
    <div id="pfSharePanel" class="pf-share-panel">
        <p class="small fw-bold mb-2">Share this portfolio</p>
        <div class="input-group input-group-sm mb-3">
            <input type="text" class="form-control" id="pfShareLink" value="<?php echo escape($pf_share_url); ?>" readonly>
            <button class="btn btn-outline-secondary" type="button" onclick="pfCopyShareLink()">Copy</button>
        </div>
        <div class="d-flex gap-2 mb-3">
            <a class="btn btn-sm btn-outline-secondary flex-fill" target="_blank" rel="noopener"
               href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo rawurlencode($pf_share_url); ?>"><i class="bi bi-linkedin"></i></a>
            <a class="btn btn-sm btn-outline-secondary flex-fill" target="_blank" rel="noopener"
               href="https://twitter.com/intent/tweet?url=<?php echo rawurlencode($pf_share_url); ?>&text=<?php echo rawurlencode('Check out my portfolio:'); ?>"><i class="bi bi-twitter-x"></i></a>
            <a class="btn btn-sm btn-outline-secondary flex-fill" target="_blank" rel="noopener"
               href="https://api.whatsapp.com/send?text=<?php echo rawurlencode($pf_share_url); ?>"><i class="bi bi-whatsapp"></i></a>
        </div>
        <img src="<?php echo escape($pf_share_qr); ?>" alt="QR code for this portfolio" width="120" height="120" class="pf-share-qr">
    </div>
</div>

<script>
function pfCopyShareLink() {
    const input = document.getElementById('pfShareLink');
    input.select();
    input.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(input.value).then(() => {
        const btn = event.target;
        const original = btn.textContent;
        btn.textContent = 'Copied!';
        setTimeout(() => { btn.textContent = original; }, 1500);
    });
}
</script>

<style>
.pf-share-widget { position: fixed; bottom: 24px; right: 24px; z-index: 1050; }
.pf-share-toggle {
    width: 52px; height: 52px; border-radius: 50%; border: none;
    background: var(--accent-color, #0d6efd); color: #fff; font-size: 1.1rem;
    box-shadow: 0 6px 20px rgba(0,0,0,0.25); cursor: pointer;
}
.pf-share-panel {
    display: none; position: absolute; bottom: 64px; right: 0;
    width: 260px; background: var(--card-bg, #fff); color: var(--body-color, #111);
    border: 1px solid var(--border-color, #e5e5e5); border-radius: 14px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2); padding: 1rem;
}
.pf-share-panel.pf-share-open { display: block; }
.pf-share-qr { display: block; margin: 0 auto; border-radius: 8px; }
@media print {
    .pf-share-widget { display: none !important; }
}
</style>
