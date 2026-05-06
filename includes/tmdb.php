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

class TMDB {
    private string $apiKey;
    private string $baseUrl   = 'https://api.themoviedb.org/3';
    private string $imageBase = 'https://image.tmdb.org/t/p/';
    private int    $cacheTTL     = 86400;
    private int    $trendingTTL  = 3600;

    public function __construct(string $apiKey) {
        $this->apiKey = $apiKey;
    }

    private function fetch(string $endpoint, array $params = [], bool $isTrending = false): array {
        $params['api_key']  = $this->apiKey;
        $lang = '';
        $headerLang = $_SERVER['HTTP_X_CONTENT_LANG'] ?? '';
        if (preg_match('/^[a-z]{2}-[A-Z]{2}$/', $headerLang)) {
            $lang = $headerLang;
        } elseif (defined('TMDB_LANG') && TMDB_LANG) {
            $lang = TMDB_LANG;
        } else {
            $lang = 'en-US';
        }
        $params['language'] = $lang;
        $url      = $this->baseUrl . $endpoint . '?' . http_build_query($params);
        $cacheKey = 'tmdb_' . md5($url);

        $cached = $this->getCache($cacheKey);
        if ($cached !== null) return $cached;

        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 10,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_USERAGENT      => 'StreamSuite/1.0 (+https://streamsuite.ct.ws)',
            ]);
            $raw = curl_exec($ch);
            curl_close($ch);
            if ($raw === false) return [];
        } else {
            $ctx = stream_context_create([
                'http' => [
                    'timeout'    => 10,
                    'user_agent' => 'StreamSuite/1.0 (+https://streamsuite.ct.ws)',
                ],
            ]);
            $raw = @file_get_contents($url, false, $ctx);
            if ($raw === false) return [];
        }

        $data = json_decode($raw, true) ?? [];
        $ttl  = $isTrending ? $this->trendingTTL : $this->cacheTTL;
        $this->setCache($cacheKey, $data, $ttl);
        return $data;
    }

    private function getCache(string $key): ?array {
        try {
            $db   = getDB();
            $stmt = $db->prepare(
                'SELECT data FROM tmdb_cache WHERE cache_key = ? AND expires_at > NOW()'
            );
            $stmt->execute([$key]);
            $row = $stmt->fetch();
            return $row ? (json_decode($row['data'], true) ?? null) : null;
        } catch (Throwable) {
            return null;
        }
    }

    private function setCache(string $key, array $data, int $ttl): void {
        try {
            $db = getDB();
            $db->prepare(
                'INSERT INTO tmdb_cache (cache_key, data, expires_at)
                 VALUES (?, ?, DATE_ADD(NOW(), INTERVAL ? SECOND))
                 ON DUPLICATE KEY UPDATE
                     data       = VALUES(data),
                     expires_at = VALUES(expires_at)'
            )->execute([$key, json_encode($data), $ttl]);
        } catch (Throwable) {}
    }

    public function trending(string $type = 'all', string $window = 'week', int $page = 1): array {
        return $this->fetch("/trending/{$type}/{$window}", ['page' => $page], true);
    }

    public function movie(int $id): array {
        return $this->fetch(
            "/movie/{$id}",
            ['append_to_response' => 'credits,videos,recommendations,similar']
        );
    }

    public function tv(int $id): array {
        return $this->fetch(
            "/tv/{$id}",
            ['append_to_response' => 'credits,videos,recommendations,similar']
        );
    }

    public function tvSeason(int $showId, int $season): array {
        return $this->fetch("/tv/{$showId}/season/{$season}");
    }

    public function search(string $query, int $page = 1): array {
        return $this->fetch('/search/multi', ['query' => $query, 'page' => $page]);
    }

    public function discover(string $type, array $params = []): array {
        return $this->fetch("/discover/{$type}", $params);
    }

    public function posterUrl(string $path, string $size = 'w300'): string {
        return $path
            ? $this->imageBase . $size . $path
            : '/assets/img/placeholder-poster.svg';
    }

    public function backdropUrl(string $path, string $size = 'original'): string {
        return $path ? $this->imageBase . $size . $path : '';
    }

    public function profileUrl(string $path, string $size = 'w185'): string {
        return $path ? $this->imageBase . $size . $path : '';
    }
}
