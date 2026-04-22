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

// ─── Database ────────────────────────────────────────────────────────────────
// InfinityFree example: sql123.infinityfree.com
define('DB_HOST', 'localhost');
// InfinityFree example: epiz_XXXXXXX_streamsuite
define('DB_NAME', 'streamsuite');
// InfinityFree example: epiz_XXXXXXX
define('DB_USER', 'root');
define('DB_PASS', '');

// ─── TMDB API ────────────────────────────────────────────────────────────────
// Free key at: https://www.themoviedb.org/settings/api
// Use the "API Key (v3 auth)" value — NOT the Bearer token
define('TMDB_API_KEY', '');
define('TMDB_LANG',    'en-US');

// ─── Session Secret ──────────────────────────────────────────────────────────
// IMPORTANT: Must be a static, long, random string.
// Never use random_bytes() here — it regenerates on every request and breaks all sessions.
// Generate once with: php -r "echo bin2hex(random_bytes(32));"
define('JWT_SECRET', 'REPLACE_WITH_A_LONG_RANDOM_STRING');

// ─── Application ─────────────────────────────────────────────────────────────
define('APP_URL',  'https://streamsuite.ct.ws');
define('APP_ENV',  'production');    // Set to 'development' locally to see PHP errors

// ─── Internal ────────────────────────────────────────────────────────────────
// Apache translates the X-Guest-Token HTTP header to HTTP_X_GUEST_TOKEN.
// Do not change this unless your server rewrites headers differently.
define('GUEST_TOKEN_HEADER', 'HTTP_X_GUEST_TOKEN');
