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

$query = trim($_GET['q'] ?? '');
$page  = sanitizeInt($_GET['page'] ?? 1, 1, 500);
$type  = $_GET['type'] ?? 'all';

if (!$query) jsonError('Missing query parameter', 422);
if (strlen($query) > 200) $query = substr($query, 0, 200);

$tmdb = new TMDB(TMDB_API_KEY);

try {
    $data = $tmdb->search($query, $page);
} catch (Throwable) {
    jsonSuccess(['query' => $query, 'page' => $page, 'total_pages' => 1, 'results' => []]);
}

$validTypes = ['movie', 'tv'];

$results = array_values(array_map(
    function (array $item) use ($tmdb): array {
        $mediaType   = $item['media_type'];
        $title       = $mediaType === 'movie' ? ($item['title'] ?? '') : ($item['name'] ?? '');
        $releaseDate = $mediaType === 'movie'
            ? ($item['release_date']   ?? '')
            : ($item['first_air_date'] ?? '');
        return [
            'id'         => (int) $item['id'],
            'tmdb_id'    => (int) $item['id'],
            'media_type' => $mediaType,
            'title'      => $title,
            'poster_url' => $tmdb->posterUrl($item['poster_path'] ?? ''),
            'rating'     => round((float) ($item['vote_average'] ?? 0), 1),
            'year'       => substr($releaseDate, 0, 4),
            'overview'   => $item['overview'] ?? '',
        ];
    },
    array_filter($data['results'] ?? [], function (array $item) use ($type, $validTypes): bool {
        $mediaType = $item['media_type'] ?? '';
        if (!in_array($mediaType, $validTypes)) return false;
        if ($type !== 'all' && $mediaType !== $type)  return false;
        return true;
    })
));

jsonSuccess([
    'query'       => $query,
    'page'        => (int) ($data['page']        ?? $page),
    'total_pages' => (int) ($data['total_pages'] ?? 1),
    'results'     => $results,
]);
