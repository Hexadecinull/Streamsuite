# Changelog

All notable changes are documented here. Follows [Keep a Changelog](https://keepachangelog.com/en/1.0.0/) format.

---

## [1.0.0] — 2026-04-22

### Added
- Full project structure: home, browse, search, detail, player, favorites, history, settings, login, register, 404 pages
- 6 free embed providers: VidSrc, VidSrc 2, AutoEmbed, SuperEmbed, 2Embed, Embed.su
- Individual server selector buttons on the player with active state
- Episode navigation (previous / next) on the player for TV series
- Season selector and episode list on detail pages
- Watch progress tracking (saves every 15 seconds, resumes on revisit)
- Continue Watching section on the home page
- Favorites — stored in localStorage, optionally synced to account
- Watch history — paginated, removable per entry, clearable in bulk
- Guest → account merge on login and register
- 5 themes: Obsidian, Midnight, Forest, Ember, Paper
- 3 font options: Satoshi, DM Mono, System UI
- Adjustable font size (12–22px)
- Autoplay next episode preference
- Remember position preference
- Data export (JSON download)
- TMDB metadata caching in MySQL to reduce API calls
- Composite UNIQUE keys on `watch_history` so progress saves are idempotent
- Lazy image loading with IntersectionObserver and error fallback to placeholder
- SVG icon sprite
- Self-hosted Satoshi and DM Mono fonts
- GPL-3.0 license headers on all PHP and JS source files
- CI: PHP syntax check, ESLint, Stylelint, JSON validation
- CI: FTP deploy to InfinityFree on push to main
- Apache 2.4 `.htaccess` with CSP, compression, cache headers, Cloudflare HTTPS support
- Comprehensive documentation

### Fixed
- `includes/auth.php` was missing entirely — caused fatal error on every authenticated API call
- `api/detail.php` always fetched movie data even for TV shows
- `api/browse.php` used `primary_release_year` for TV series (should be `first_air_date_year`)
- `api/search.php` returned person results from TMDB multi-search, breaking card rendering
- `api/continue.php` and `api/favorites.php` returned raw DB rows without poster URLs
- `ui.js` `Favorites.toggle()` returned wrong boolean for items at index 0
- `ui.js` `Favorites.clear()` was called but never defined
- `app.js` did not restore saved font family from preferences
- `config.example.php` called `random_bytes()` at runtime for `JWT_SECRET`, invalidating all sessions
- `watch.php` iframe wrapper class didn't match player.css
- `watch.php` mobile drawer was a HTML comment placeholder
- TV section on detail page was permanently hidden
- Schema `watch_history` had no composite UNIQUE key — `ON DUPLICATE KEY UPDATE` never fired
- `.htaccess` used deprecated Apache 2.2 `Order Allow,Deny` syntax
