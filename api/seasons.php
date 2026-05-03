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
$mediaType = 'tv';

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

if ($mediaType !== 'tv') jsonError('Not a TV series', 422);

$tmdb    = new TMDB(TMDB_API_KEY);
$data    = $tmdb->tv($tmdbId);
$seasons = [];

foreach ($data['seasons'] ?? [] as $season) {
    if ((int) $season['season_number'] === 0) continue;
    $seasons[] = [
        'id'             => (int) $season['id'],
        'season_number'  => (int) $season['season_number'],
        'name'           => $season['name'] ?? 'Season ' . $season['season_number'],
        'episode_count'  => (int) ($season['episode_count'] ?? 0),
        'overview'       => $season['overview'] ?? '',
        'air_date'       => $season['air_date'] ?? null,
        'poster_url'     => $tmdb->posterUrl($season['poster_path'] ?? ''),
    ];
}

jsonSuccess($seasons);
