<?php
/*
 * StreamSuite — Free, open-source streaming website
 * Copyright (C) 2026  StreamSuite Contributors
 * (GPL-3.0 license)
 */

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/tmdb.php';
require_once __DIR__ . '/../includes/response.php';

$type = in_array($_GET['type'] ?? 'movie', ['movie', 'tv'], true) ? $_GET['type'] : 'movie';
$sort = in_array($_GET['sort'] ?? 'popularity', ['popularity', 'vote_average', 'release_date'], true)
    ? $_GET['sort'] : 'popularity';
$page = max(1, (int) ($_GET['page'] ?? 1));

try {
    $tmdb   = new TMDB(TMDB_API_KEY);
    $params = [
        'sort_by'           => $sort . '.desc',
        'include_adult'     => 'true',
        'with_genres'       => '10749',
        'vote_count.gte'    => 5,
        'page'              => $page,
    ];
    $data    = $tmdb->discover($type, $params);
    $results = array_map(function (array $item) use ($tmdb, $type): array {
        $date = $type === 'movie' ? ($item['release_date'] ?? '') : ($item['first_air_date'] ?? '');
        return [
            'id'         => (int) $item['id'],
            'tmdb_id'    => (int) $item['id'],
            'media_type' => $type,
            'title'      => $type === 'movie' ? ($item['title'] ?? '') : ($item['name'] ?? ''),
            'poster_url' => $tmdb->posterUrl($item['poster_path'] ?? ''),
            'rating'     => round((float) ($item['vote_average'] ?? 0), 1),
            'year'       => substr($date, 0, 4),
        ];
    }, $data['results'] ?? []);
    jsonSuccess(['page' => $page, 'total_pages' => min((int)($data['total_pages'] ?? 1), 50), 'results' => $results]);
} catch (Throwable) {
    jsonSuccess(['page' => 1, 'total_pages' => 1, 'results' => []]);
}
