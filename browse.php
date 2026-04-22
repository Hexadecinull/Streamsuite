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

$pageTitle       = 'Browse';
$pageDescription = 'Browse movies and TV series on StreamSuite.';
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
            <a href="/browse" class="nav-link active">Browse</a>
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

<main id="browse-page" class="container">
    <div class="browse-header">
        <h1 class="text-2xl">Browse</h1>
        <span id="result-count" class="result-count"></span>
    </div>

    <form id="filter-form" class="filter-bar">
        <select name="type" aria-label="Media type">
            <option value="movie">Movies</option>
            <option value="tv">Series</option>
        </select>
        <select name="genre" aria-label="Genre">
            <option value="">All Genres</option>
            <option value="28">Action</option>
            <option value="12">Adventure</option>
            <option value="16">Animation</option>
            <option value="35">Comedy</option>
            <option value="80">Crime</option>
            <option value="99">Documentary</option>
            <option value="18">Drama</option>
            <option value="10751">Family</option>
            <option value="14">Fantasy</option>
            <option value="36">History</option>
            <option value="27">Horror</option>
            <option value="10402">Music</option>
            <option value="9648">Mystery</option>
            <option value="10749">Romance</option>
            <option value="878">Sci-Fi</option>
            <option value="53">Thriller</option>
            <option value="10752">War</option>
            <option value="37">Western</option>
        </select>
        <input type="number" name="year" placeholder="Year" min="1900" max="2026" aria-label="Release year">
        <select name="sort" aria-label="Sort by">
            <option value="popularity">Popularity</option>
            <option value="vote_average">Rating</option>
            <option value="release_date">Release Date</option>
            <option value="original_title">Title</option>
        </select>
        <select name="order" aria-label="Sort order">
            <option value="desc">Descending</option>
            <option value="asc">Ascending</option>
        </select>
        <button type="submit" class="btn btn-primary">Apply</button>
    </form>

    <div id="browse-results" class="browse-grid"></div>
    <div id="browse-loader" class="browse-loader" style="display:none;"></div>
    <div id="scroll-sentinel" style="height:20px;"></div>
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
<script src="/assets/js/browse.js"></script>
<script src="/assets/js/app.js"></script>
</body>
</html>
