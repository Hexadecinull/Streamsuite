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

$pageTitle       = 'Trending';
$pageDescription = 'What everyone is watching right now on StreamSuite.';
$extraCss        = ['/assets/css/browse.css'];
$activePage      = 'trending';
require_once __DIR__ . '/includes/head.php';
require_once __DIR__ . '/includes/nav.php';
?>

<main id="trending-page" class="container">
    <div class="browse-header">
        <a href="/" class="back-btn">&#8592; Home</a>
        <h1 class="text-2xl">&#128293; Trending</h1>
        <div class="trending-tabs" id="trending-tabs">
            <button class="trending-tab active" data-window="week">This Week</button>
            <button class="trending-tab" data-window="day">Today</button>
        </div>
    </div>

    <div id="browse-results" class="browse-grid"></div>
    <div id="browse-loader" class="browse-loader" style="display:none;"></div>
    <div id="scroll-sentinel" style="height:20px;"></div>
</main>

<script src="/assets/js/api.js"></script>
<script src="/assets/js/ui.js"></script>
<script src="/assets/js/trending.js"></script>
<script src="/assets/js/app.js"></script>
</body>
</html>
