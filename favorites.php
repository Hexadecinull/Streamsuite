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

$pageTitle       = 'Favorites';
$pageDescription = 'Your saved titles on StreamSuite.';
$extraCss        = ['/assets/css/browse.css'];
require_once __DIR__ . '/includes/head.php';
require_once __DIR__ . '/includes/nav.php';
?>

<main id="favorites-page" class="container" style="padding-top:1.5rem;padding-bottom:4rem;">
    <div class="browse-header">
        <a href="/" class="back-btn">&#8592; Home</a>
        <h1 class="text-2xl">Favorites</h1>
        <div style="margin-left:auto;display:flex;align-items:center;gap:1rem;">
            <span id="fav-count" style="color:var(--c-text-3);font-size:0.85rem;font-family:var(--font-mono);"></span>
            <button id="clear-fav-btn" class="btn btn-ghost btn-sm" style="color:var(--c-red);">Clear All</button>
        </div>
    </div>
    <div id="fav-grid" class="browse-grid"></div>
    <div id="fav-empty" style="display:none;text-align:center;padding:4rem 1rem;color:var(--c-text-3);">
        <div style="font-size:2rem;margin-bottom:0.75rem;">&#10084;</div>
        <div>No favorites yet. Add some titles to see them here.</div>
        <a href="/" class="btn btn-primary" style="margin-top:1.25rem;">Browse titles</a>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script src="/assets/js/api.js"></script>
<script src="/assets/js/ui.js"></script>
<script src="/assets/js/favorites.js"></script>
<script src="/assets/js/app.js"></script>
</body>
</html>
