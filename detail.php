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
require_once __DIR__ . '/includes/nav.php';
?>

<main id="detail-page">
    <div id="detail-backdrop" class="detail-backdrop"></div>

    <div class="container detail-container">
        <a href="javascript:history.back()" class="back-btn detail-back">&#8592; Back</a>

        <div class="detail-layout">
            <div class="detail-poster">
                <img id="detail-poster"
                     src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 2 3'%3E%3C/svg%3E"
                     alt="">
            </div>
            <div class="detail-info">
                <h1 id="detail-title" class="text-3xl"></h1>
                <div class="detail-meta" id="detail-meta">
                    <span id="detail-year"></span>
                    <span class="rating">&#9733; <span id="detail-rating"></span></span>
                    <span id="detail-runtime"></span>
                    <span id="detail-age-badge"></span>
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

<script src="/assets/js/api.js"></script>
<script src="/assets/js/ui.js"></script>
<script src="/assets/js/detail.js"></script>
<script src="/assets/js/app.js"></script>
</body>
</html>
