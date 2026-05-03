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

$allowedTypes = ['movie', 'tv'];
$allowedSorts = ['popularity', 'vote_average', 'release_date', 'original_title'];
$allowedOrder = ['asc', 'desc'];

$type  = in_array($_GET['type'] ?? 'movie', $allowedTypes) ? $_GET['type'] : 'movie';
$page  = sanitizeInt($_GET['page'] ?? 1, 1, 500);
$genre = (int) ($_GET['genre'] ?? 0);
$year  = (int) ($_GET['year']  ?? 0);

$sortRaw   = $_GET['sort']  ?? 'popularity';
$orderRaw  = $_GET['order'] ?? 'desc';
$sortField = in_array($sortRaw, $allowedSorts) ? $sortRaw : 'popularity';
$sortOrder = in_array($orderRaw, $allowedOrder) ? $orderRaw : 'desc';

$params = [
    'sort_by' => $sortField . '.' . $sortOrder,
    'page'    => $page,
    'vote_count.gte' => 10,
];

if ($genre > 0) $params['with_genres'] = $genre;

if ($year > 1900 && $year <= (int) date('Y') + 1) {
    if ($type === 'movie') {
        $params['primary_release_year'] = $year;
    } else {
        $params['first_air_date_year'] = $year;
    }
}

$tmdb = new TMDB(TMDB_API_KEY);

try {
    $data = $tmdb->discover($type, $params);
} catch (Throwable) {
    jsonSuccess(['page' => $page, 'total_pages' => 1, 'total_results' => 0, 'results' => []]);
}

$results = array_map(function (array $item) use ($tmdb, $type): array {
    $releaseDate = $type === 'movie'
        ? ($item['release_date']    ?? '')
        : ($item['first_air_date']  ?? '');

    return [
        'id'         => (int) $item['id'],
        'tmdb_id'    => (int) $item['id'],
        'media_type' => $type,
        'title'      => $type === 'movie' ? ($item['title'] ?? '') : ($item['name'] ?? ''),
        'poster_url' => $tmdb->posterUrl($item['poster_path'] ?? ''),
        'rating'     => round((float) ($item['vote_average'] ?? 0), 1),
        'year'       => substr($releaseDate, 0, 4),
        'overview'   => $item['overview'] ?? '',
    ];
}, $data['results'] ?? []);

jsonSuccess([
    'page'          => (int) ($data['page']          ?? $page),
    'total_pages'   => (int) ($data['total_pages']   ?? 1),
    'total_results' => (int) ($data['total_results'] ?? 0),
    'results'       => $results,
]);
