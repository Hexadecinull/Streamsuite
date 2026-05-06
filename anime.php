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

    <form id="anime-filter-form" class="filter-bar">
        <select id="anime-genre" aria-label="Genre">
            <option value="">All Anime Genres</option>
            <option value="16">Animation</option>
            <option value="35">Comedy</option>
            <option value="18">Drama</option>
            <option value="10765">Sci-Fi &amp; Fantasy</option>
            <option value="10759">Action &amp; Adventure</option>
            <option value="9648">Mystery</option>
            <option value="10762">Kids</option>
        </select>
        <input type="number" id="anime-year" placeholder="Year" min="1960" max="2026" aria-label="Year">
        <select id="anime-sort" aria-label="Sort by">
            <option value="popularity">Popularity</option>
            <option value="vote_average">Rating</option>
            <option value="first_air_date">Release Date</option>
        </select>
        <button type="submit" class="btn btn-primary btn-sm">Apply</button>
    </form>

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
