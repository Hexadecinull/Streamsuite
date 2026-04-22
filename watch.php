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

$pageTitle = 'Watch — StreamSuite';
$extraCss  = ['/assets/css/player.css'];
require_once __DIR__ . '/includes/head.php';
?>

<header class="site-header">
    <div class="container header-inner">
        <a href="/" class="logo">
            <img src="/assets/img/logo-mark.svg" alt="" class="logo-mark">
            <span>StreamSuite</span>
        </a>
        <a id="back-to-detail" href="/" class="btn btn-ghost">&#8592; Back</a>
    </div>
</header>

<main class="player-page">
    <div class="player-header">
        <h1 id="player-title" class="text-l">Loading&#8230;</h1>
        <div id="episode-label" class="player-episode-label"></div>
    </div>

    <div class="player-frame-wrapper">
        <div id="player-loading" class="player-loading">
            <div class="player-loading-spinner"></div>
        </div>
        <div id="player-error" style="
            display:none;position:absolute;inset:0;background:#000;
            align-items:center;justify-content:center;text-align:center;padding:2rem;
            color:var(--c-text-2);flex-direction:column;gap:1rem;">
            <p style="font-size:1.1rem;font-weight:600;">Failed to load</p>
            <p style="color:var(--c-text-3);font-size:0.85rem;">
                Try a different server below, or reload the page.
            </p>
        </div>
        <iframe id="player-frame"
                allowfullscreen
                allow="autoplay; encrypted-media; picture-in-picture; fullscreen"
                sandbox="allow-scripts allow-same-origin allow-presentation allow-forms"></iframe>
    </div>

    <div class="player-toolbar">
        <div class="server-selector">
            <span class="server-label">Server</span>
            <div id="server-buttons" class="server-buttons"></div>
        </div>
        <div class="player-actions">
            <button id="reload-btn" class="btn btn-secondary btn-sm">&#8635; Reload</button>
            <a href="/" class="btn btn-ghost btn-sm" style="color:var(--c-text-3);">&#8962; Home</a>
        </div>
    </div>

    <div id="episode-nav" class="episode-nav">
        <a id="episode-prev" href="#" class="episode-nav-prev">&#8592; Previous Episode</a>
        <a id="episode-next" href="#" class="episode-nav-next">Next Episode &#8594;</a>
    </div>
</main>

<script src="/assets/js/api.js"></script>
<script src="/assets/js/ui.js"></script>
<script src="/assets/js/player.js"></script>
<script src="/assets/js/app.js"></script>
</body>
</html>
