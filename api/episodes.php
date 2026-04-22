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
$seasonNum = sanitizeInt($_GET['season']     ?? 1, 1, 100);
if (!$catalogId) jsonError('Missing catalog_id', 422);

$db   = getDB();
$stmt = $db->prepare('SELECT tmdb_id FROM catalog WHERE id = ?');
$stmt->execute([$catalogId]);
$catalog = $stmt->fetch();
if (!$catalog) jsonError('Not found', 404);

$guestToken = getGuestToken();
$userId     = getAuthUserId();

$tmdb = new TMDB(TMDB_API_KEY);
$data = $tmdb->tvSeason((int) $catalog['tmdb_id'], $seasonNum);

$progressStmt = $db->prepare(
    'SELECT episode_id, progress_sec, duration_sec, percent
     FROM watch_history
     WHERE catalog_id = ? AND (user_id = ? OR guest_token = ?)'
);
$progressStmt->execute([$catalogId, $userId, $guestToken]);
$progressMap = [];
foreach ($progressStmt->fetchAll() as $row) {
    if ($row['episode_id']) {
        $progressMap[(int) $row['episode_id']] = $row;
    }
}

$episodes = array_map(function (array $ep) use ($tmdb, $progressMap): array {
    $epId     = (int) $ep['id'];
    $progress = $progressMap[$epId] ?? null;
    return [
        'id'             => $epId,
        'episode_number' => (int) $ep['episode_number'],
        'season_number'  => (int) $ep['season_number'],
        'title'          => $ep['name'] ?? '',
        'overview'       => $ep['overview'] ?? '',
        'still_url'      => $tmdb->backdropUrl($ep['still_path'] ?? '', 'w300'),
        'runtime'        => (int) ($ep['runtime'] ?? 0),
        'air_date'       => $ep['air_date'] ?? null,
        'watch_progress' => $progress ? [
            'progress_sec' => (int) $progress['progress_sec'],
            'duration_sec' => (int) $progress['duration_sec'],
            'percent'      => (float) $progress['percent'],
        ] : null,
    ];
}, $data['episodes'] ?? []);

jsonSuccess($episodes);
