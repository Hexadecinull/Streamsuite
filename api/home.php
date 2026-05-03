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
require_once __DIR__ . '/../includes/tmdb.php';
require_once __DIR__ . '/../includes/response.php';

$tmdb = new TMDB(TMDB_API_KEY);

function mapItem(array $item, TMDB $tmdb, string $forceType = ''): array {
    $mediaType   = $forceType ?: ($item['media_type'] ?? 'movie');
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
    ];
}

try {
    $trendingRaw   = $tmdb->trending('all', 'week');
    $trendingItems = $trendingRaw['results'] ?? [];

    $popularMovies = $tmdb->discover('movie', ['sort_by' => 'popularity.desc', 'vote_count.gte' => 50])['results'] ?? [];
    $popularTV     = $tmdb->discover('tv',    ['sort_by' => 'popularity.desc', 'vote_count.gte' => 20])['results'] ?? [];

    $validTypes    = ['movie', 'tv'];
    $trendingValid = array_values(array_filter(
        $trendingItems,
        fn ($i) => in_array($i['media_type'] ?? '', $validTypes, true)
    ));

    $featured = null;
    if (!empty($trendingValid)) {
        usort($trendingValid, fn ($a, $b) => ($b['vote_average'] ?? 0) <=> ($a['vote_average'] ?? 0));
        $pool         = array_slice($trendingValid, 0, 5);
        $featuredItem = $pool[0];
        $featured     = [
            'id'           => (int) $featuredItem['id'],
            'tmdb_id'      => (int) $featuredItem['id'],
            'media_type'   => $featuredItem['media_type'],
            'title'        => $featuredItem['media_type'] === 'movie'
                ? ($featuredItem['title'] ?? '')
                : ($featuredItem['name']  ?? ''),
            'overview'     => $featuredItem['overview']     ?? '',
            'poster_url'   => $tmdb->posterUrl($featuredItem['poster_path']   ?? '', 'w500'),
            'backdrop_url' => $tmdb->backdropUrl($featuredItem['backdrop_path'] ?? ''),
            'rating'       => round((float) ($featuredItem['vote_average'] ?? 0), 1),
            'year'         => substr(
                $featuredItem['release_date'] ?? $featuredItem['first_air_date'] ?? '',
                0, 4
            ),
        ];
    }

    $rows = [
        [
            'id'    => 'trending',
            'title' => 'Trending Now',
            'items' => array_map(
                fn ($i) => mapItem($i, $tmdb),
                array_slice($trendingValid, 0, 12)
            ),
        ],
        [
            'id'    => 'popular_movies',
            'title' => 'Popular Movies',
            'items' => array_map(
                fn ($i) => mapItem($i, $tmdb, 'movie'),
                array_slice($popularMovies, 0, 12)
            ),
        ],
        [
            'id'    => 'popular_tv',
            'title' => 'Popular Series',
            'items' => array_map(
                fn ($i) => mapItem($i, $tmdb, 'tv'),
                array_slice($popularTV, 0, 12)
            ),
        ],
    ];

    jsonSuccess(['featured' => $featured, 'rows' => $rows]);
} catch (Throwable) {
    jsonSuccess(['featured' => null, 'rows' => []]);
}
