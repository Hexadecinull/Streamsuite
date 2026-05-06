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

$pageTitle = 'Watch';
$extraCss  = ['/assets/css/player.css'];
require_once __DIR__ . '/includes/head.php';
?>

<header class="site-header">
    <div class="header-inner">
        <a href="/" class="logo">
            <img src="/assets/img/logo-mark.svg" alt="" class="logo-mark">
            <span>StreamSuite</span>
        </a>
        <div style="flex:1;min-width:0;overflow:hidden;">
            <div id="player-title-nav" style="font-size:0.9rem;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;color:var(--c-text-2);"></div>
            <div id="episode-label-nav" style="font-size:0.72rem;color:var(--c-accent);font-family:var(--font-mono);"></div>
        </div>
        <div style="display:flex;align-items:center;gap:0.5rem;flex-shrink:0;">
            <a id="back-to-detail" href="/" class="watch-back-btn" title="Back to details">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg>
                <span>Back</span>
            </a>
            <a href="/" class="watch-home-btn" title="Home">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            </a>
        </div>
    </div>
</header>

<main class="player-page">
    <div class="player-frame-wrapper">
        <div id="player-loading" class="player-loading">
            <div class="player-loading-spinner"></div>
            <div class="player-loading-label" id="player-loading-label">Loading&#8230;</div>
        </div>
        <div id="player-error" class="player-error">
            <div class="player-error-icon">&#9634;</div>
            <div class="player-error-title">Playback failed</div>
            <div class="player-error-desc">All servers failed to load. Try a different server below or reload the page.</div>
        </div>
        <iframe id="player-frame"
                allowfullscreen
                allow="fullscreen"></iframe>
    </div>

    <div class="player-ui">
        <div class="player-toolbar">
            <div class="server-selector">
                <span class="server-label">Server</span>
                <div id="server-buttons" class="server-buttons"></div>
            </div>
            <div class="player-actions">
                <button id="reload-btn" class="btn btn-secondary btn-sm">&#8635; Retry</button>
            </div>
        </div>

        <div id="episode-nav" class="episode-nav" style="display:none;">
            <a id="episode-prev" href="#" class="episode-nav-btn" hidden>&#8592; Previous</a>
            <span style="font-size:0.8rem;color:var(--c-text-3);font-family:var(--font-mono);" id="episode-label-bar"></span>
            <a id="episode-next" href="#" class="episode-nav-btn" hidden>Next &#8594;</a>
        </div>
    </div>
</main>

<script src="/assets/js/api.js"></script>
<script src="/assets/js/ui.js"></script>
<script src="/assets/js/player.js"></script>
<script src="/assets/js/app.js"></script>
</body>
</html>
