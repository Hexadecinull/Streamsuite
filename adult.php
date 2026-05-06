<?php
/*
 * StreamSuite — Free, open-source streaming website
 * Copyright (C) 2026  StreamSuite Contributors
 * (GPL-3.0 license)
 */

$pageTitle       = '18+ Content';
$pageDescription = 'Adult content on StreamSuite. Must be enabled in settings.';
$extraCss        = ['/assets/css/browse.css'];
$activePage      = 'adult';
require_once __DIR__ . '/includes/head.php';
require_once __DIR__ . '/includes/nav.php';
?>

<main id="adult-page" class="container" style="padding-top:1.5rem;">
    <div id="adult-gate" style="display:none;text-align:center;padding:5rem 1rem;max-width:480px;margin:0 auto;">
        <div style="font-size:2.5rem;margin-bottom:1rem;">&#128274;</div>
        <h1 style="font-size:1.5rem;margin-bottom:0.75rem;">18+ Content Disabled</h1>
        <p style="color:var(--c-text-2);margin-bottom:1.5rem;line-height:1.7;">
            This section contains adult content. You must enable <strong>Show 18+ content</strong>
            in Settings to access it. You must be 18 years or older to proceed.
        </p>
        <button class="btn btn-primary" id="open-settings-btn">Open Settings</button>
    </div>

    <div id="adult-content" style="display:none;">
        <div class="browse-header">
            <a href="/" class="back-btn">&#8592; Home</a>
            <h1 class="text-2xl">&#128274; 18+ Content</h1>
            <span style="background:rgba(224,92,92,0.12);color:var(--c-red);padding:0.25rem 0.65rem;border-radius:var(--radius-s);font-size:0.72rem;font-weight:700;font-family:var(--font-mono);">ADULTS ONLY</span>
        </div>

        <form id="adult-filter-form" class="filter-bar">
            <select id="adult-type" aria-label="Type">
                <option value="movie">Movies</option>
                <option value="tv">Series</option>
            </select>
            <select id="adult-sort" aria-label="Sort by">
                <option value="popularity">Popular</option>
                <option value="vote_average">Top Rated</option>
                <option value="release_date">Newest</option>
            </select>
            <button type="submit" class="btn btn-primary btn-sm">Apply</button>
        </form>

        <div id="adult-grid" class="browse-grid"></div>
        <div id="adult-loader" class="browse-loader" style="display:none;"></div>
        <div id="scroll-sentinel" style="height:20px;"></div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script src="/assets/js/api.js"></script>
<script src="/assets/js/ui.js"></script>
<script src="/assets/js/adult.js"></script>
<script src="/assets/js/app.js"></script>
</body>
</html>
