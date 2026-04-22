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

$db         = getDB();
$guestToken = getGuestToken();
$userId     = getAuthUserId();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['catalog_id'])) {
        $catalogId = sanitizeInt($_GET['catalog_id'], 1);
        $stmt = $db->prepare(
            'SELECT 1 FROM favorites
             WHERE catalog_id = ? AND (user_id = ? OR guest_token = ?)'
        );
        $stmt->execute([$catalogId, $userId, $guestToken]);
        jsonSuccess(['in_favorites' => (bool) $stmt->fetch()]);
    }

    $stmt = $db->prepare(
        'SELECT c.id, c.tmdb_id, c.media_type, c.title, c.poster_path,
                c.release_date, c.rating, c.year, f.added_at
         FROM favorites f
         JOIN catalog c ON f.catalog_id = c.id
         WHERE f.user_id = ? OR f.guest_token = ?
         ORDER BY f.added_at DESC'
    );
    $stmt->execute([$userId, $guestToken]);
    $rows = $stmt->fetchAll();

    $tmdb    = new TMDB(TMDB_API_KEY);
    $results = array_map(function (array $row) use ($tmdb): array {
        return [
            'id'         => (int) $row['id'],
            'tmdb_id'    => (int) $row['tmdb_id'],
            'media_type' => $row['media_type'],
            'title'      => $row['title'],
            'poster_url' => $tmdb->posterUrl($row['poster_path'] ?? ''),
            'rating'     => (float) $row['rating'],
            'year'       => $row['year'],
            'added_at'   => $row['added_at'],
        ];
    }, $rows);

    jsonSuccess(['results' => $results]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input     = json_decode(file_get_contents('php://input'), true) ?? [];
    $catalogId = sanitizeInt($input['catalog_id'] ?? 0, 1);
    if (!$catalogId) jsonError('Missing catalog_id', 422);

    try {
        $stmt = $db->prepare(
            'INSERT IGNORE INTO favorites (user_id, guest_token, catalog_id)
             VALUES (?, ?, ?)'
        );
        $stmt->execute([$userId, $guestToken, $catalogId]);
        jsonSuccess(['added' => true]);
    } catch (PDOException) {
        jsonError('Failed to add favorite', 500);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $input     = json_decode(file_get_contents('php://input'), true) ?? [];
    $catalogId = sanitizeInt($input['catalog_id'] ?? 0, 1);
    if (!$catalogId) jsonError('Missing catalog_id', 422);

    $stmt = $db->prepare(
        'DELETE FROM favorites
         WHERE catalog_id = ? AND (user_id = ? OR guest_token = ?)'
    );
    $stmt->execute([$catalogId, $userId, $guestToken]);
    jsonSuccess(['removed' => true]);
}

jsonError('Method not allowed', 405);
