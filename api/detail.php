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

$id   = trim($_GET['id'] ?? '');
$hint = trim($_GET['type'] ?? '');
if (!$id || !ctype_digit($id)) jsonError('Missing or invalid ID', 422);
$id = (int) $id;
if ($hint !== 'movie' && $hint !== 'tv') $hint = '';

$tmdb = new TMDB(TMDB_API_KEY);

try {
    $db = getDB();

    if ($hint !== '') {
        $stmt = $db->prepare(
            'SELECT * FROM catalog WHERE tmdb_id = ? AND media_type = ? ORDER BY cached_at DESC LIMIT 1'
        );
        $stmt->execute([$id, $hint]);
    } else {
        $stmt = $db->prepare(
            'SELECT * FROM catalog WHERE tmdb_id = ? ORDER BY cached_at DESC LIMIT 1'
        );
        $stmt->execute([$id]);
    }
    $local = $stmt->fetch();

    if ($local && !empty($local['title'])) {
        jsonSuccess([
            'id'             => (int) $local['id'],
            'tmdb_id'        => (int) $local['tmdb_id'],
            'media_type'     => $local['media_type'],
            'title'          => $local['title'],
            'original_title' => $local['original_title'],
            'overview'       => $local['overview'],
            'poster_url'     => $tmdb->posterUrl($local['poster_path'], 'w500'),
            'backdrop_url'   => $tmdb->backdropUrl($local['backdrop_path']),
            'release_date'   => $local['release_date'],
            'year'           => (int) $local['year'],
            'rating'         => round((float) $local['rating'], 1),
            'vote_count'     => (int) $local['vote_count'],
            'runtime'        => (int) $local['runtime'],
            'genres'         => json_decode($local['genres']   ?? '[]', true),
            'countries'      => json_decode($local['countries'] ?? '[]', true),
            'tagline'        => $local['tagline'],
            'trailer_key'    => $local['trailer_key'],
            'cast'           => json_decode($local['cast_json'] ?? '[]', true),
        ]);
    }
} catch (Throwable) {
    $db = null;
}

if ($hint === 'tv') {
    $tmdbData  = $tmdb->tv($id);
    $mediaType = 'tv';
} else {
    $tmdbData  = $tmdb->movie($id);
    $mediaType = 'movie';
    if (empty($tmdbData['title'])) {
        $tmdbData  = $tmdb->tv($id);
        $mediaType = 'tv';
    }
}

if (empty($tmdbData) || (isset($tmdbData['status_code']) && $tmdbData['status_code'] !== 1)) {
    jsonError('Title not found', 404);
}

$title         = $mediaType === 'movie' ? ($tmdbData['title'] ?? '') : ($tmdbData['name']          ?? '');
$originalTitle = $mediaType === 'movie' ? ($tmdbData['original_title']  ?? '') : ($tmdbData['original_name'] ?? '');
$releaseDate   = $mediaType === 'movie' ? ($tmdbData['release_date']    ?? null) : ($tmdbData['first_air_date'] ?? null);
$year          = $releaseDate ? (int) substr($releaseDate, 0, 4) : null;
$runtime       = $mediaType === 'movie'
    ? (int) ($tmdbData['runtime'] ?? 0)
    : (int) ($tmdbData['episode_run_time'][0] ?? 0);

$trailerKey = '';
foreach ($tmdbData['videos']['results'] ?? [] as $video) {
    if ($video['type'] === 'Trailer' && $video['site'] === 'YouTube') {
        $trailerKey = $video['key'];
        break;
    }
}

$cast = array_slice(
    array_map(function (array $person) use ($tmdb): array {
        return [
            'name'         => $person['name'],
            'character'    => $person['character'] ?? '',
            'profile_path' => $tmdb->profileUrl($person['profile_path'] ?? ''),
        ];
    }, $tmdbData['credits']['cast'] ?? []),
    0, 10
);

$catalogId = $id;

if ($db !== null) {
    try {
        $db->prepare(
            'INSERT INTO catalog
                 (tmdb_id, media_type, title, original_title, overview, poster_path, backdrop_path,
                  release_date, year, rating, vote_count, popularity, runtime, genres, countries,
                  tagline, trailer_key, cast_json, cached_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
             ON DUPLICATE KEY UPDATE
                 title = VALUES(title), original_title = VALUES(original_title),
                 overview = VALUES(overview), poster_path = VALUES(poster_path),
                 backdrop_path = VALUES(backdrop_path), release_date = VALUES(release_date),
                 year = VALUES(year), rating = VALUES(rating), vote_count = VALUES(vote_count),
                 popularity = VALUES(popularity), runtime = VALUES(runtime), genres = VALUES(genres),
                 countries = VALUES(countries), tagline = VALUES(tagline), trailer_key = VALUES(trailer_key),
                 cast_json = VALUES(cast_json), cached_at = NOW()'
        )->execute([
            $id, $mediaType, $title, $originalTitle, $tmdbData['overview'] ?? '',
            $tmdbData['poster_path']   ?? '', $tmdbData['backdrop_path']  ?? '',
            $releaseDate, $year, $tmdbData['vote_average'] ?? 0, $tmdbData['vote_count'] ?? 0,
            $tmdbData['popularity'] ?? 0, $runtime,
            json_encode(array_column($tmdbData['genres'] ?? [], 'name')),
            json_encode(array_column($tmdbData['production_countries'] ?? [], 'name')),
            $tmdbData['tagline'] ?? '', $trailerKey, json_encode($cast),
        ]);

        $inserted = (int) $db->lastInsertId();
        if ($inserted > 0) {
            $catalogId = $inserted;
        } else {
            $stmt2 = $db->prepare('SELECT id FROM catalog WHERE tmdb_id = ? AND media_type = ? LIMIT 1');
            $stmt2->execute([$id, $mediaType]);
            $resolved = (int) $stmt2->fetchColumn();
            if ($resolved > 0) $catalogId = $resolved;
        }
    } catch (Throwable) {}
}

jsonSuccess([
    'id'             => $catalogId,
    'tmdb_id'        => $id,
    'media_type'     => $mediaType,
    'title'          => $title,
    'original_title' => $originalTitle,
    'overview'       => $tmdbData['overview'] ?? '',
    'poster_url'     => $tmdb->posterUrl($tmdbData['poster_path']   ?? '', 'w500'),
    'backdrop_url'   => $tmdb->backdropUrl($tmdbData['backdrop_path'] ?? ''),
    'release_date'   => $releaseDate,
    'year'           => $year,
    'rating'         => round((float) ($tmdbData['vote_average'] ?? 0), 1),
    'vote_count'     => (int) ($tmdbData['vote_count'] ?? 0),
    'runtime'        => $runtime,
    'genres'         => array_column($tmdbData['genres'] ?? [], 'name'),
    'countries'      => array_column($tmdbData['production_countries'] ?? [], 'name'),
    'tagline'        => $tmdbData['tagline'] ?? '',
    'trailer_key'    => $trailerKey,
    'cast'           => $cast,
]);
