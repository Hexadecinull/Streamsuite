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

$query           = htmlspecialchars(trim($_GET['q'] ?? ''), ENT_QUOTES, 'UTF-8');
$pageTitle       = $query ? 'Search: ' . $query : 'Search';
$pageDescription = $query ? "Search results for \"{$query}\" on StreamSuite." : 'Search StreamSuite.';
$extraCss        = ['/assets/css/browse.css'];
require_once __DIR__ . '/includes/head.php';
require_once __DIR__ . '/includes/nav.php';
?>

<main id="search-page" class="container">
    <div class="browse-header">
        <a href="javascript:history.back()" class="back-btn">&#8592; Back</a>
        <h1 class="text-2xl" id="search-query-display">Search</h1>
    </div>

    <div id="search-suggestion" style="display:none;margin-bottom:1rem;padding:0.75rem 1rem;background:var(--c-bg-3);border-radius:var(--radius-m);border:1px solid var(--c-border);font-size:0.875rem;">
        Did you mean: <a id="suggestion-link" href="#" style="color:var(--c-accent);font-weight:600;"></a>?
    </div>

    <div id="search-results" class="browse-grid"></div>
    <div id="search-loader" class="browse-loader" style="display:none;"></div>
    <div id="scroll-sentinel" style="height:20px;"></div>
</main>

<script src="/assets/js/api.js"></script>
<script src="/assets/js/ui.js"></script>
<script src="/assets/js/search.js"></script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script src="/assets/js/app.js"></script>
</body>
</html>
