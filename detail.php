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

$pageTitle       = 'Detail';
$pageDescription = 'Watch on StreamSuite.';
$extraCss        = ['/assets/css/detail.css'];
require_once __DIR__ . '/includes/head.php';
?>

<header class="site-header">
    <div class="container header-inner">
        <a href="/" class="logo">
            <img src="/assets/img/logo-mark.svg" alt="" class="logo-mark">
            <span>StreamSuite</span>
        </a>
        <nav class="nav-links">
            <a href="/" class="nav-link">Home</a>
            <a href="/browse?type=movie" class="nav-link">Movies</a>
            <a href="/browse?type=tv" class="nav-link">Series</a>
            <a href="/browse" class="nav-link">Browse</a>
        </nav>
        <div class="header-actions">
            <form class="search-bar" action="/search" method="get">
                <input type="text" name="q" placeholder="Search&#8230;" aria-label="Search">
                <button type="submit" class="btn btn-icon" aria-label="Search">&#9906;</button>
            </form>
            <button class="mobile-menu-btn btn btn-icon" aria-label="Open menu" aria-expanded="false">&#9776;</button>
        </div>
    </div>
</header>

<main id="detail-page">
    <div id="detail-backdrop" class="detail-backdrop"></div>

    <div class="container detail-container">
        <div class="detail-poster">
            <img id="detail-poster"
                 src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 2 3'%3E%3C/svg%3E"
                 alt="">
        </div>
        <div class="detail-info">
            <h1 id="detail-title" class="text-3xl"></h1>
            <div class="detail-meta">
                <span id="detail-year"></span>
                <span class="rating">&#9733; <span id="detail-rating"></span></span>
                <span id="detail-runtime"></span>
                <span id="detail-genres"></span>
            </div>
            <p id="detail-tagline" class="tagline"></p>
            <div class="detail-actions">
                <a id="watch-btn" href="#" class="btn btn-primary">&#9654; Watch Now</a>
                <button id="favorite-btn" class="btn btn-secondary">+ Favorites</button>
                <button id="share-btn" class="btn btn-ghost">&#8599; Share</button>
            </div>
            <p id="detail-overview" class="overview"></p>
        </div>
    </div>

    <div class="container">

        <section class="detail-section cast-section">
            <h2>Cast</h2>
            <div id="cast-container" class="cast-grid"></div>
        </section>

        <section id="tv-section" class="detail-section" style="display:none;">
            <h2>Episodes</h2>
            <select id="season-selector" class="season-select"></select>
            <div id="episodes-container" class="episodes-list"></div>
        </section>

        <section id="trailer-section" class="detail-section">
            <h2>Trailer</h2>
            <div id="trailer-container"></div>
        </section>

        <section class="detail-section">
            <h2>More Like This</h2>
            <div id="related-container" class="related-grid"></div>
        </section>

    </div>
</main>

<div class="mobile-drawer">
    <a href="/" class="mobile-nav-item">Home</a>
    <a href="/browse?type=movie" class="mobile-nav-item">Movies</a>
    <a href="/browse?type=tv" class="mobile-nav-item">Series</a>
    <a href="/browse" class="mobile-nav-item">Browse</a>
    <a href="/favorites" class="mobile-nav-item">Favorites</a>
    <a href="/history" class="mobile-nav-item">History</a>
    <a href="/settings" class="mobile-nav-item">Settings</a>
    <hr style="border-color:var(--c-border);margin:0.5rem 0;">
    <a href="/login" class="mobile-nav-item">Login / Register</a>
</div>

<script src="/assets/js/api.js"></script>
<script src="/assets/js/ui.js"></script>
<script src="/assets/js/detail.js"></script>
<script src="/assets/js/app.js"></script>
</body>
</html>
