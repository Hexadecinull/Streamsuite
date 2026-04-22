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

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';

header('Content-Type: application/xml; charset=utf-8');
header('X-Robots-Tag: noindex');

$base = rtrim(APP_URL, '/');
$now  = date('Y-m-d');

$static = [
    ['loc' => '/',       'priority' => '1.0', 'changefreq' => 'daily'],
    ['loc' => '/browse', 'priority' => '0.8', 'changefreq' => 'daily'],
    ['loc' => '/search', 'priority' => '0.6', 'changefreq' => 'weekly'],
];

$catalog = [];
try {
    $db   = getDB();
    $stmt = $db->query(
        'SELECT tmdb_id, cached_at FROM catalog
         WHERE title IS NOT NULL AND title != ""
         ORDER BY cached_at DESC
         LIMIT 5000'
    );
    $catalog = $stmt->fetchAll();
} catch (Throwable) {}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

foreach ($static as $url) {
    $loc = htmlspecialchars($base . $url['loc'], ENT_XML1, 'UTF-8');
    echo "  <url>\n";
    echo "    <loc>{$loc}</loc>\n";
    echo "    <lastmod>{$now}</lastmod>\n";
    echo "    <changefreq>{$url['changefreq']}</changefreq>\n";
    echo "    <priority>{$url['priority']}</priority>\n";
    echo "  </url>\n";
}

foreach ($catalog as $row) {
    $loc     = htmlspecialchars($base . '/detail?id=' . (int) $row['tmdb_id'], ENT_XML1, 'UTF-8');
    $lastmod = substr($row['cached_at'] ?? $now, 0, 10);
    echo "  <url>\n";
    echo "    <loc>{$loc}</loc>\n";
    echo "    <lastmod>{$lastmod}</lastmod>\n";
    echo "    <changefreq>monthly</changefreq>\n";
    echo "    <priority>0.5</priority>\n";
    echo "  </url>\n";
}

echo '</urlset>' . "\n";
