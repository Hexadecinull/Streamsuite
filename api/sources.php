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
require_once __DIR__ . '/../includes/response.php';

$catalogId = sanitizeInt($_GET['catalog_id'] ?? 0, 1);
$type      = $_GET['type'] ?? '';
$season    = sanitizeInt($_GET['season']  ?? 0, 0, 50);
$episode   = sanitizeInt($_GET['episode'] ?? 0, 0, 200);

if (!$catalogId)                            jsonError('Missing catalog_id', 422);
if (!in_array($type, ['movie', 'tv'], true)) jsonError('Invalid type', 422);
if ($type === 'tv' && (!$season || !$episode)) jsonError('Season and episode required for TV', 422);

$tmdbId = $catalogId;

try {
    $db   = getDB();
    $stmt = $db->prepare('SELECT tmdb_id FROM catalog WHERE id = ? LIMIT 1');
    $stmt->execute([$catalogId]);
    $catalog = $stmt->fetch();

    if (!$catalog) {
        $stmt2 = $db->prepare('SELECT tmdb_id FROM catalog WHERE tmdb_id = ? LIMIT 1');
        $stmt2->execute([$catalogId]);
        $catalog = $stmt2->fetch();
    }

    if ($catalog) {
        $tmdbId = (int) $catalog['tmdb_id'];
    }
} catch (Throwable) {}

const EMBED_PROVIDERS = [
    'vidsrc'     => ['movie' => 'https://vidsrc.to/embed/movie/{tmdb_id}',
                     'tv'    => 'https://vidsrc.to/embed/tv/{tmdb_id}/{season}/{episode}',
                     'label' => 'VidSrc', 'priority' => 1],
    'vidsrc2'    => ['movie' => 'https://vidsrc.me/embed/movie?tmdb={tmdb_id}',
                     'tv'    => 'https://vidsrc.me/embed/tv?tmdb={tmdb_id}&season={season}&episode={episode}',
                     'label' => 'VidSrc 2', 'priority' => 2],
    'autoembed'  => ['movie' => 'https://autoembed.cc/movie/tmdb/{tmdb_id}',
                     'tv'    => 'https://autoembed.cc/tv/tmdb/{tmdb_id}-{season}-{episode}',
                     'label' => 'AutoEmbed', 'priority' => 3],
    'superembed' => ['movie' => 'https://multiembed.mov/directstream.php?video_id={tmdb_id}&tmdb=1',
                     'tv'    => 'https://multiembed.mov/directstream.php?video_id={tmdb_id}&tmdb=1&s={season}&e={episode}',
                     'label' => 'SuperEmbed', 'priority' => 4],
    '2embed'     => ['movie' => 'https://www.2embed.cc/embed/{tmdb_id}',
                     'tv'    => 'https://www.2embed.cc/embedtv/{tmdb_id}&s={season}&e={episode}',
                     'label' => '2Embed', 'priority' => 5],
    'embedsu'    => ['movie' => 'https://embed.su/embed/movie/{tmdb_id}',
                     'tv'    => 'https://embed.su/embed/tv/{tmdb_id}/{season}/{episode}',
                     'label' => 'Embed.su', 'priority' => 6],
];

$sources = [];
foreach (EMBED_PROVIDERS as $key => $provider) {
    $template = $type === 'tv' ? $provider['tv'] : $provider['movie'];
    $url      = str_replace(
        ['{tmdb_id}', '{season}', '{episode}'],
        [$tmdbId,     $season,    $episode],
        $template
    );
    $sources[] = [
        'id'       => $key,
        'label'    => $provider['label'],
        'url'      => $url,
        'priority' => $provider['priority'],
    ];
}

usort($sources, fn ($a, $b) => $a['priority'] <=> $b['priority']);
jsonSuccess(['sources' => $sources]);

const EMBED_PROVIDERS = [
    'vidsrc'     => ['movie' => 'https://vidsrc.to/embed/movie/{tmdb_id}',
                     'tv'    => 'https://vidsrc.to/embed/tv/{tmdb_id}/{season}/{episode}',
                     'label' => 'VidSrc', 'priority' => 1],
    'vidsrc2'    => ['movie' => 'https://vidsrc.me/embed/movie?tmdb={tmdb_id}',
                     'tv'    => 'https://vidsrc.me/embed/tv?tmdb={tmdb_id}&season={season}&episode={episode}',
                     'label' => 'VidSrc 2', 'priority' => 2],
    'autoembed'  => ['movie' => 'https://autoembed.cc/movie/tmdb/{tmdb_id}',
                     'tv'    => 'https://autoembed.cc/tv/tmdb/{tmdb_id}-{season}-{episode}',
                     'label' => 'AutoEmbed', 'priority' => 3],
    'superembed' => ['movie' => 'https://multiembed.mov/directstream.php?video_id={tmdb_id}&tmdb=1',
                     'tv'    => 'https://multiembed.mov/directstream.php?video_id={tmdb_id}&tmdb=1&s={season}&e={episode}',
                     'label' => 'SuperEmbed', 'priority' => 4],
    '2embed'     => ['movie' => 'https://www.2embed.cc/embed/{tmdb_id}',
                     'tv'    => 'https://www.2embed.cc/embedtv/{tmdb_id}&s={season}&e={episode}',
                     'label' => '2Embed', 'priority' => 5],
    'embedsu'    => ['movie' => 'https://embed.su/embed/movie/{tmdb_id}',
                     'tv'    => 'https://embed.su/embed/tv/{tmdb_id}/{season}/{episode}',
                     'label' => 'Embed.su', 'priority' => 6],
];

$sources = [];
foreach (EMBED_PROVIDERS as $key => $provider) {
    $template = $type === 'tv' ? $provider['tv'] : $provider['movie'];
    $url      = str_replace(
        ['{tmdb_id}', '{season}', '{episode}'],
        [$tmdbId,     $season,    $episode],
        $template
    );
    $sources[] = [
        'id'       => $key,
        'label'    => $provider['label'],
        'url'      => $url,
        'priority' => $provider['priority'],
    ];
}

usort($sources, fn ($a, $b) => $a['priority'] <=> $b['priority']);
jsonSuccess(['sources' => $sources]);
