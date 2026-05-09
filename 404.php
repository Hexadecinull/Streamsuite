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
$pageTitle       = '404 • Page Not Found';
$pageDescription = 'This page does not exist.';
require_once __DIR__ . '/includes/head.php';
require_once __DIR__ . '/includes/nav.php';
?>

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

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script src="/assets/js/api.js"></script>
<script src="/assets/js/ui.js"></script>
<script src="/assets/js/app.js"></script>
</body>
</html>
