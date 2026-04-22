# StreamSuite

**Free, open-source streaming website.** Browse, search, and watch movies and TV series through publicly available embed providers — no account required, no subscriptions, no paywalls.

[![License: GPL-3.0](https://img.shields.io/badge/license-GPL--3.0-blue.svg)](LICENSE)
[![PHP](https://img.shields.io/badge/PHP-8.1%2B-777bb4.svg)](https://php.net)
[![Deploy](https://img.shields.io/github/actions/workflow/status/Hexadecinull/Streamsuite/deploy.yml?label=deploy)](https://github.com/Hexadecinull/Streamsuite/actions)
[![Lint](https://img.shields.io/github/actions/workflow/status/Hexadecinull/Streamsuite/lint.yml?label=lint)](https://github.com/Hexadecinull/Streamsuite/actions)

**Live:** [streamsuite.ct.ws](https://streamsuite.ct.ws)

---

## What It Is

StreamSuite is a self-hosted web app that uses TMDB for metadata and free public embed providers for video. You bring a server (or use the free InfinityFree hosting at ct.ws), add a free TMDB API key, and you have a fully working streaming site for zero cost.

---

## Features

- **No account needed** — everything works as a guest out of the box
- **6 free embed providers** with one-click switching if a source is down (VidSrc, AutoEmbed, SuperEmbed, 2Embed, Embed.su)
- **Browse** by genre, year, sort order — movies and TV series
- **Full-text search** via TMDB multi-search
- **Detail pages** — cast, trailer, related titles, and full episode list for TV
- **Episode navigation** — previous/next buttons on the player
- **Watch history** and **favorites** — stored locally, optionally synced to an account
- **Continue watching** — resumes from where you left off (progress saved every 15 seconds)
- **5 themes** — Obsidian, Midnight, Forest, Ember, Paper
- **3 fonts** — Satoshi, DM Mono, System UI
- **Guest → account merge** — history and favorites carry over on login/register
- Fully **responsive** — mobile, tablet, desktop

---

## Quick Start

```bash
git clone https://github.com/Hexadecinull/Streamsuite.git
cd Streamsuite

# Import database schema
mysql -u root -p -e "CREATE DATABASE streamsuite;"
mysql -u root -p streamsuite < docs/schema.sql

# Configure
cp includes/config.example.php includes/config.php
# Edit includes/config.php — set DB credentials and TMDB API key

# Serve
php -S localhost:8080
```

Full guide → [`docs/INSTALL.md`](docs/INSTALL.md)

---

## Requirements

| | Minimum |
|---|---|
| PHP | 8.1 (`pdo_mysql`, `json`) |
| MySQL | 8.0 |
| Apache | 2.4 (`mod_rewrite`, `mod_headers`) |
| TMDB API Key | Free at [themoviedb.org](https://www.themoviedb.org/settings/api) |

---

## Documentation

| Document | |
|---|---|
| [`docs/INSTALL.md`](docs/INSTALL.md) | Full installation guide — InfinityFree and VPS |
| [`docs/API.md`](docs/API.md) | API endpoint reference with request/response examples |
| [`docs/EMBED_SOURCES.md`](docs/EMBED_SOURCES.md) | How the embed provider system works |
| [`docs/THEMING.md`](docs/THEMING.md) | All CSS variables, how to create custom themes |
| [`docs/schema.sql`](docs/schema.sql) | Database schema |
| [`CONTRIBUTING.md`](CONTRIBUTING.md) | Dev setup, code style, PR process |
| [`SECURITY.md`](SECURITY.md) | Vulnerability reporting and hardening checklist |
| [`CHANGELOG.md`](CHANGELOG.md) | What changed in each release |

---

## Project Structure

```
streamsuite/
├── .github/workflows/   CI — lint (PHP/JS/CSS) and FTP deploy
├── api/                 JSON API endpoints
├── assets/
│   ├── css/             Tokens, themes, layout, components, page styles
│   ├── fonts/           Satoshi + DM Mono (self-hosted WOFF2)
│   ├── icons/           SVG sprite
│   ├── img/             Logos and poster placeholder
│   └── js/              Vanilla ES2022 modules
├── docs/                Technical documentation
├── includes/            Shared PHP: config, DB, TMDB client, auth, response
├── CONTRIBUTING.md      How to contribute
├── SECURITY.md          Security policy
├── CHANGELOG.md         Release history
└── *.php                Page entry points
```

---

## Legal

This product uses the [TMDB API](https://www.themoviedb.org/) for metadata and is not endorsed or certified by TMDB.

StreamSuite does not host, store, or distribute any video content. All streams are sourced from third-party embed providers. See [`docs/EMBED_SOURCES.md`](docs/EMBED_SOURCES.md).

---

## License

[GNU General Public License v3.0](LICENSE)
