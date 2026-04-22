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

$pageTitle       = 'Discover';
$pageDescription = 'Stream everything. Own nothing. Pay nothing.';
$extraCss        = ['/assets/css/home.css'];
require_once __DIR__ . '/includes/head.php';
?>

<header class="site-header">
    <div class="container header-inner">
        <a href="/" class="logo">
            <img src="/assets/img/logo-mark.svg" alt="" class="logo-mark">
            <span>StreamSuite</span>
        </a>
        <nav class="nav-links">
            <a href="/" class="nav-link active">Home</a>
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

<main id="home-page">
    <div id="featured-container" class="featured-section skeleton" style="min-height:70vh;"></div>

    <div class="content-rows container">

        <section class="content-row" id="continue-row" hidden>
            <div class="row-header">
                <h2 class="text-xl">Continue Watching</h2>
                <a href="/history" class="see-all">History &rarr;</a>
            </div>
            <div class="row-items" id="continue-items"></div>
        </section>

        <div id="rows-container"></div>

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
<script src="/assets/js/home.js"></script>
<script src="/assets/js/app.js"></script>
</body>
</html>
