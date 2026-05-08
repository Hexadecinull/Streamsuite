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

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/response.php';

$catalogId = sanitizeInt($_GET['catalog_id'] ?? 0, 1);
$type      = $_GET['type'] ?? '';
$season    = sanitizeInt($_GET['season']  ?? 0, 0, 50);
$episode   = sanitizeInt($_GET['episode'] ?? 0, 0, 200);

if (!$catalogId)                               jsonError('Missing catalog_id', 422);
if (!in_array($type, ['movie', 'tv'], true))   jsonError('Invalid type', 422);
if ($type === 'tv' && (!$season || !$episode)) jsonError('Season and episode required for TV', 422);

$tmdbId = $catalogId;

try {
    $db   = getDB();
    $stmt = $db->prepare('SELECT tmdb_id FROM catalog WHERE id = ? LIMIT 1');
    $stmt->execute([$catalogId]);
    $catalog = $stmt->fetch();

    if (!$catalog) {
        $stmt2 = $db->prepare('SELECT tmdb_id FROM catalog WHERE tmdb_id = ? AND media_type = ? LIMIT 1');
        $stmt2->execute([$catalogId, $type]);
        $catalog = $stmt2->fetch();
    }

    if (!$catalog) {
        $stmt3 = $db->prepare('SELECT tmdb_id FROM catalog WHERE tmdb_id = ? LIMIT 1');
        $stmt3->execute([$catalogId]);
        $catalog = $stmt3->fetch();
    }

    if ($catalog) {
        $tmdbId = (int) $catalog['tmdb_id'];
    }
} catch (Throwable) {}

$providers = [
    [
        'id'       => 'superembed',
        'label'    => 'Server 1',
        'movie'    => 'https://multiembed.mov/directstream.php?video_id={tmdb_id}&tmdb=1',
        'tv'       => 'https://multiembed.mov/directstream.php?video_id={tmdb_id}&tmdb=1&s={season}&e={episode}',
        'priority' => 1,
    ],
    [
        'id'       => 'player2048',
        'label'    => 'Server 2',
        'movie'    => 'https://www.2embed.skin/embedtv/{tmdb_id}',
        'tv'       => 'https://www.2embed.skin/embedtv/{tmdb_id}&s={season}&e={episode}',
        'priority' => 2,
    ],
    [
        'id'       => 'autoembed',
        'label'    => 'Server 3',
        'movie'    => 'https://player.autoembed.cc/embed/movie/{tmdb_id}',
        'tv'       => 'https://player.autoembed.cc/embed/tv/{tmdb_id}/{season}/{episode}',
        'priority' => 3,
    ],
    [
        'id'       => 'smashystream',
        'label'    => 'Server 4',
        'movie'    => 'https://player.smashy.stream/movie/{tmdb_id}',
        'tv'       => 'https://player.smashy.stream/tv/{tmdb_id}?s={season}&e={episode}',
        'priority' => 4,
    ],
    [
        'id'       => 'embedsoap',
        'label'    => 'Server 5',
        'movie'    => 'https://www.embedsoap.net/embed/movie/?id={tmdb_id}',
        'tv'       => 'https://www.embedsoap.net/embed/tv/?id={tmdb_id}&s={season}&e={episode}',
        'priority' => 5,
    ],
    [
        'id'       => 'nontongo',
        'label'    => 'Server 6',
        'movie'    => 'https://www.NontonGo.net/embed/movie/{tmdb_id}',
        'tv'       => 'https://www.NontonGo.net/embed/tv/{tmdb_id}/{season}/{episode}',
        'priority' => 6,
    ],
];

$sources = [];
foreach ($providers as $provider) {
    $template = $type === 'tv' ? $provider['tv'] : $provider['movie'];
    $url      = str_replace(
        ['{tmdb_id}', '{season}', '{episode}'],
        [$tmdbId,     $season,    $episode],
        $template
    );
    $sources[] = [
        'id'       => $provider['id'],
        'label'    => $provider['label'],
        'url'      => $url,
        'priority' => $provider['priority'],
    ];
}

jsonSuccess(['sources' => $sources]);
