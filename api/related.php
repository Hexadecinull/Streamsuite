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

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/tmdb.php';
require_once __DIR__ . '/../includes/response.php';

$catalogId = sanitizeInt($_GET['catalog_id'] ?? 0, 1);
if (!$catalogId) jsonError('Missing catalog_id', 422);

$tmdbId    = $catalogId;
$mediaType = 'movie';

try {
    $db   = getDB();
    $stmt = $db->prepare('SELECT tmdb_id, media_type FROM catalog WHERE id = ? LIMIT 1');
    $stmt->execute([$catalogId]);
    $catalog = $stmt->fetch();

    if (!$catalog) {
        $stmt2 = $db->prepare('SELECT tmdb_id, media_type FROM catalog WHERE tmdb_id = ? LIMIT 1');
        $stmt2->execute([$catalogId]);
        $catalog = $stmt2->fetch();
    }

    if ($catalog) {
        $tmdbId    = (int) $catalog['tmdb_id'];
        $mediaType = $catalog['media_type'];
    }
} catch (Throwable) {}

$tmdb      = new TMDB(TMDB_API_KEY);
$data      = $mediaType === 'movie'
    ? $tmdb->movie($tmdbId)
    : $tmdb->tv($tmdbId);

$rawItems = $data['recommendations']['results'] ?? $data['similar']['results'] ?? [];

$results = array_map(function (array $item) use ($tmdb, $mediaType): array {
    $releaseDate = $mediaType === 'movie'
        ? ($item['release_date']   ?? '')
        : ($item['first_air_date'] ?? '');
    $title = $mediaType === 'movie'
        ? ($item['title'] ?? '')
        : ($item['name']  ?? '');
    return [
        'id'         => (int) $item['id'],
        'tmdb_id'    => (int) $item['id'],
        'media_type' => $mediaType,
        'title'      => $title,
        'poster_url' => $tmdb->posterUrl($item['poster_path'] ?? ''),
        'rating'     => round((float) ($item['vote_average'] ?? 0), 1),
        'year'       => substr($releaseDate, 0, 4),
    ];
}, array_slice($rawItems, 0, 18));

jsonSuccess(['results' => $results]);
