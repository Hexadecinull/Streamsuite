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

header('Content-Type: application/json; charset=utf-8');

$checks = [];

$checks['php_version']       = PHP_VERSION;
$checks['php_version_ok']    = version_compare(PHP_VERSION, '8.1.0', '>=');
$checks['pdo_mysql']         = extension_loaded('pdo_mysql');
$checks['curl']              = extension_loaded('curl');
$checks['allow_url_fopen']   = (bool) ini_get('allow_url_fopen');
$checks['json']              = extension_loaded('json');

$configPath = __DIR__ . '/../includes/config.php';
$checks['config_exists'] = file_exists($configPath);

if ($checks['config_exists']) {
    require_once $configPath;
    $checks['tmdb_key_set']    = defined('TMDB_API_KEY') && TMDB_API_KEY !== '' && TMDB_API_KEY !== 'your_tmdb_v3_api_key';
    $checks['jwt_secret_set']  = defined('JWT_SECRET')   && JWT_SECRET   !== 'REPLACE_WITH_A_LONG_RANDOM_STRING';
    $checks['db_host']         = defined('DB_HOST') ? DB_HOST : null;
    $checks['db_name']         = defined('DB_NAME') ? DB_NAME : null;
    $checks['db_user']         = defined('DB_USER') ? DB_USER : null;
    $checks['db_pass_set']     = defined('DB_PASS') && DB_PASS !== '';

    require_once __DIR__ . '/../includes/db.php';
    try {
        $pdo = getDB();
        $checks['db_connect'] = 'ok';
        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        $checks['db_tables'] = $tables;
        $required = ['catalog', 'tmdb_cache', 'watch_history', 'favorites', 'sessions', 'users', 'site_settings'];
        $missing  = array_values(array_diff($required, $tables));
        $checks['missing_tables'] = $missing;
    } catch (Throwable $e) {
        $checks['db_connect'] = 'FAILED: ' . $e->getMessage();
        $checks['db_tables']  = [];
        $checks['missing_tables'] = [];
    }

    if ($checks['tmdb_key_set'] ?? false) {
        $tmdbUrl = 'https://api.themoviedb.org/3/configuration?api_key=' . TMDB_API_KEY;
        $tmdbOk  = false;
        if (extension_loaded('curl')) {
            $ch = curl_init($tmdbUrl);
            curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 6, CURLOPT_SSL_VERIFYPEER => true]);
            $res = curl_exec($ch);
            curl_close($ch);
            if ($res) {
                $data = json_decode($res, true);
                $tmdbOk = isset($data['images']);
            }
        } elseif (ini_get('allow_url_fopen')) {
            $res = @file_get_contents($tmdbUrl);
            if ($res) {
                $data = json_decode($res, true);
                $tmdbOk = isset($data['images']);
            }
        }
        $checks['tmdb_reachable'] = $tmdbOk;
    }
} else {
    $checks['config_exists_hint'] = 'Copy includes/config.example.php to includes/config.php and fill in your values';
}

echo json_encode(['success' => true, 'checks' => $checks], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
