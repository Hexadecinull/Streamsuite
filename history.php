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

$pageTitle       = 'Watch History';
$pageDescription = 'Your watch history on StreamSuite.';
$extraCss        = ['/assets/css/browse.css'];
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
                <input type="text" name="q" placeholder="Search..." aria-label="Search">
                <button type="submit" class="btn btn-icon">&#9906;</button>
            </form>
            <button class="mobile-menu-btn btn btn-icon">&#9776;</button>
        </div>
    </div>
</header>

<main id="history-page" class="container">
    <div class="browse-header">
        <h1 class="text-2xl">Watch History</h1>
        <div style="display:flex;align-items:center;gap:1rem;">
            <span id="history-count" class="result-count"></span>
            <button id="clear-history-btn" class="btn btn-ghost btn-sm"
                    style="font-size:0.8rem;color:var(--c-red);">Clear All</button>
        </div>
    </div>

    <div id="history-results" class="browse-grid"></div>

    <div id="history-empty" hidden style="text-align:center;padding:5rem 2rem;color:var(--c-text-3);">
        <div style="font-size:3rem;margin-bottom:1rem;opacity:0.3;">&#9203;</div>
        <div style="font-size:1.1rem;font-weight:600;color:var(--c-text-2);margin-bottom:0.5rem;">Nothing watched yet</div>
        <p style="margin-bottom:1.5rem;">Your watch history will appear here after you start watching.</p>
        <a href="/" class="btn btn-primary">Browse Home</a>
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
<script src="/assets/js/history.js"></script>
<script src="/assets/js/app.js"></script>
</body>
</html>
