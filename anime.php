<?php
/*
 * StreamSuite — Free, open-source streaming website
 * Copyright (C) 2026  StreamSuite Contributors
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

$pageTitle       = 'Anime';
$pageDescription = 'Watch anime series and movies on StreamSuite for free.';
$extraCss        = ['/assets/css/browse.css', '/assets/css/anime.css'];
$activePage      = 'anime';
require_once __DIR__ . '/includes/head.php';
require_once __DIR__ . '/includes/nav.php';
?>

<main id="anime-page" class="container">
    <div class="browse-header">
        <a href="/" class="back-btn">&#8592; Home</a>
        <h1 class="text-2xl">&#127875; Anime</h1>
    </div>

    <div class="anime-hero">
        <p class="anime-hero-text">Discover anime series and animated films — filtered from TMDB&rsquo;s Animation genre with Japanese origin.</p>
        <div class="anime-tabs" id="anime-tabs">
            <button class="trending-tab active" data-filter="popular">Popular</button>
            <button class="trending-tab" data-filter="trending">Trending</button>
            <button class="trending-tab" data-filter="top_rated">Top Rated</button>
        </div>
    </div>

    <div class="browse-search-bar" style="margin-bottom:1.5rem;">
        <input type="text" id="anime-search-input" placeholder="Search anime&#8230;" autocomplete="off">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
            <circle cx="6.5" cy="6.5" r="5" stroke="currentColor" stroke-width="1.6"/>
            <path d="M10.5 10.5L14 14" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
        </svg>
    </div>

    <div id="browse-results" class="browse-grid"></div>
    <div id="browse-loader" class="browse-loader" style="display:none;"></div>
    <div id="scroll-sentinel" style="height:20px;"></div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script src="/assets/js/api.js"></script>
<script src="/assets/js/ui.js"></script>
<script src="/assets/js/anime.js"></script>
<script src="/assets/js/app.js"></script>
</body>
</html>
