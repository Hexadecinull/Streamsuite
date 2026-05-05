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
require_once __DIR__ . '/../includes/tmdb.php';
require_once __DIR__ . '/../includes/response.php';

$filter = $_GET['filter'] ?? 'popular';
$page   = max(1, (int) ($_GET['page'] ?? 1));

try {
    $tmdb = new TMDB(TMDB_API_KEY);

    if ($filter === 'trending') {
        $raw     = $tmdb->trending('tv', 'week', $page);
        $results = array_filter($raw['results'] ?? [], function (array $item): bool {
            $genres  = $item['genre_ids'] ?? [];
            $origin  = $item['origin_country'] ?? [];
            $lang    = $item['original_language'] ?? '';
            return in_array(16, $genres, true)
                && (in_array('JP', $origin, true) || $lang === 'ja');
        });
        $total = (int) ($raw['total_pages'] ?? 1);
    } else {
        $sort = $filter === 'top_rated' ? 'vote_average.desc' : 'popularity.desc';
        $params = [
            'sort_by'               => $sort,
            'with_genres'           => '16',
            'with_original_language'=> 'ja',
            'vote_count.gte'        => $filter === 'top_rated' ? 200 : 20,
            'page'                  => $page,
        ];
        $raw     = $tmdb->discover('tv', $params);
        $results = $raw['results'] ?? [];
        $total   = (int) ($raw['total_pages'] ?? 1);
    }

    $items = array_map(function (array $item) use ($tmdb): array {
        $title = $item['name'] ?? $item['title'] ?? '';
        $date  = $item['first_air_date'] ?? $item['release_date'] ?? '';
        return [
            'id'         => (int) $item['id'],
            'tmdb_id'    => (int) $item['id'],
            'media_type' => 'tv',
            'title'      => $title,
            'poster_url' => $tmdb->posterUrl($item['poster_path'] ?? ''),
            'rating'     => round((float) ($item['vote_average'] ?? 0), 1),
            'year'       => substr($date, 0, 4),
            'overview'   => $item['overview'] ?? '',
        ];
    }, array_values($results));

    jsonSuccess(['page' => $page, 'total_pages' => min($total, 50), 'results' => $items]);
} catch (Throwable) {
    jsonSuccess(['page' => 1, 'total_pages' => 1, 'results' => []]);
}
