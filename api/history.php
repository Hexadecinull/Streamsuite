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

$guestToken = getGuestToken();
$userId     = getAuthUserId();

try {
    $db = getDB();
} catch (Throwable) {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        jsonSuccess(['page' => 1, 'results' => []]);
    }
    jsonError('Database unavailable', 503);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $page   = sanitizeInt($_GET['page'] ?? 1, 1, 100);
    $limit  = 20;
    $offset = ($page - 1) * $limit;

    $stmt = $db->prepare(
        'SELECT wh.catalog_id, wh.episode_id, wh.progress_sec, wh.duration_sec,
                wh.percent, wh.last_watched,
                c.title, c.poster_path, c.media_type, c.year
         FROM watch_history wh
         JOIN catalog c ON wh.catalog_id = c.id
         WHERE (wh.user_id = ? OR wh.guest_token = ?)
         ORDER BY wh.last_watched DESC
         LIMIT ? OFFSET ?'
    );
    $stmt->execute([$userId, $guestToken, $limit, $offset]);
    $rows = $stmt->fetchAll();

    $tmdb    = new TMDB(TMDB_API_KEY);
    $results = array_map(function (array $row) use ($tmdb): array {
        return [
            'catalog_id'   => (int) $row['catalog_id'],
            'episode_id'   => $row['episode_id'] ? (int) $row['episode_id'] : null,
            'progress_sec' => (int) $row['progress_sec'],
            'duration_sec' => (int) $row['duration_sec'],
            'percent'      => (float) $row['percent'],
            'last_watched' => $row['last_watched'],
            'title'        => $row['title'],
            'media_type'   => $row['media_type'],
            'year'         => $row['year'],
            'poster_url'   => $tmdb->posterUrl($row['poster_path'] ?? ''),
        ];
    }, $rows);

    jsonSuccess(['page' => $page, 'results' => $results]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input       = json_decode(file_get_contents('php://input'), true) ?? [];
    $catalogId   = sanitizeInt($input['catalog_id'] ?? 0, 1);
    $episodeId   = isset($input['episode_id']) ? sanitizeInt($input['episode_id'], 1) : null;
    $progressSec = sanitizeInt($input['progress_sec'] ?? 0, 0);
    $durationSec = sanitizeInt($input['duration_sec'] ?? 0, 0);
    $percent     = $durationSec > 0
        ? round(($progressSec / $durationSec) * 100, 2)
        : (float) ($input['percent'] ?? 0);

    if (!$catalogId) jsonError('Missing catalog_id', 422);

    $stmt = $db->prepare(
        'INSERT INTO watch_history
             (user_id, guest_token, catalog_id, episode_id, progress_sec, duration_sec, percent)
         VALUES (?, ?, ?, ?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE
             progress_sec = VALUES(progress_sec),
             duration_sec = VALUES(duration_sec),
             percent      = VALUES(percent),
             last_watched = NOW()'
    );
    $stmt->execute([$userId, $guestToken, $catalogId, $episodeId, $progressSec, $durationSec, $percent]);
    jsonSuccess(['saved' => true]);
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if (isset($_GET['all'])) {
        $stmt = $db->prepare(
            'DELETE FROM watch_history WHERE user_id = ? OR guest_token = ?'
        );
        $stmt->execute([$userId, $guestToken]);
        jsonSuccess(['cleared' => true]);
    }

    $input     = json_decode(file_get_contents('php://input'), true) ?? [];
    $catalogId = sanitizeInt($input['catalog_id'] ?? 0, 1);
    if (!$catalogId) jsonError('Missing catalog_id', 422);

    $stmt = $db->prepare(
        'DELETE FROM watch_history
         WHERE catalog_id = ? AND (user_id = ? OR guest_token = ?)'
    );
    $stmt->execute([$catalogId, $userId, $guestToken]);
    jsonSuccess(['removed' => true]);
}

jsonError('Method not allowed', 405);
