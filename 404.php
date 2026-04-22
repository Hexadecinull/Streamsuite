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

http_response_code(404);
$pageTitle       = '404 — Page Not Found';
$pageDescription = 'This page does not exist.';
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
        </nav>
        <button class="mobile-menu-btn btn btn-icon">&#9776;</button>
    </div>
</header>

<main style="min-height:70vh;display:flex;align-items:center;justify-content:center;text-align:center;padding:2rem;">
    <div>
        <p style="font-family:var(--font-mono);font-size:5rem;font-weight:700;color:var(--c-border-2);line-height:1;margin-bottom:1rem;">404</p>
        <h1 style="font-size:1.5rem;font-weight:700;margin-bottom:0.75rem;">Page Not Found</h1>
        <p style="color:var(--c-text-3);margin-bottom:2rem;">The page you're looking for doesn't exist or was moved.</p>
        <div style="display:flex;gap:0.75rem;justify-content:center;flex-wrap:wrap;">
            <a href="/" class="btn btn-primary">Back to Home</a>
            <a href="/browse" class="btn btn-secondary">Browse Titles</a>
        </div>
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
<script src="/assets/js/app.js"></script>
</body>
</html>
