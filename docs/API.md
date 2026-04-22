# API Reference

All endpoints live under `/api/` and return JSON:

```json
{ "success": true,  "data": { ... } }
{ "success": false, "error": "Human-readable message" }
```

HTTP status codes are used correctly: `200` success, `201` created, `400` bad request, `401` unauthorized, `404` not found, `422` validation error, `500` server error.

---

## Authentication

Every request may include both headers. Endpoints that work for guests will fall back to the guest token if no auth token is present.

```
X-Guest-Token: <uuid>           # generated in localStorage, identifies guest sessions
Authorization: Bearer <token>   # returned by /api/auth.php?action=login|register
```

---

## Endpoints

### Home

**`GET /api/home.php`**

Returns the featured item and content rows for the home page.

**Response:**
```json
{
  "featured": {
    "id": 550, "tmdb_id": 550, "media_type": "movie",
    "title": "Fight Club", "overview": "...",
    "poster_url": "https://image.tmdb.org/...",
    "backdrop_url": "https://image.tmdb.org/...",
    "rating": 8.4, "year": "1999"
  },
  "rows": [
    {
      "id": "trending",
      "title": "Trending Now",
      "items": [ { "id": 550, "title": "...", "poster_url": "...", ... } ]
    }
  ]
}
```

---

### Browse

**`GET /api/browse.php`**

| Parameter | Type | Default | Notes |
|---|---|---|---|
| `type` | `movie` \| `tv` | `movie` | |
| `genre` | integer | — | TMDB genre ID |
| `year` | integer | — | Release year |
| `sort` | `popularity` \| `vote_average` \| `release_date` \| `original_title` | `popularity` | |
| `order` | `asc` \| `desc` | `desc` | |
| `page` | integer 1–500 | `1` | |

**Response:**
```json
{
  "page": 1, "total_pages": 45, "total_results": 900,
  "results": [
    { "id": 550, "tmdb_id": 550, "media_type": "movie",
      "title": "Fight Club", "poster_url": "...", "rating": 8.4, "year": "1999" }
  ]
}
```

---

### Search

**`GET /api/search.php`**

| Parameter | Type | Notes |
|---|---|---|
| `q` | string | Required. Max 200 characters. |
| `page` | integer | Default 1 |
| `type` | `movie` \| `tv` \| `all` | Default `all`. Filters TMDB multi-search results. `person` results are always excluded. |

**Response:** Same shape as browse — `{ page, total_pages, results[] }`.

---

### Detail

**`GET /api/detail.php?id=<tmdb_id>`**

Returns full metadata for a movie or TV series. On first request, fetches from TMDB and writes to the `catalog` table. Subsequent requests serve from cache.

**Response:**
```json
{
  "id": 550, "tmdb_id": 550, "media_type": "movie",
  "title": "Fight Club", "original_title": "Fight Club",
  "overview": "...", "tagline": "...",
  "poster_url": "https://image.tmdb.org/...",
  "backdrop_url": "https://image.tmdb.org/...",
  "release_date": "1999-10-15", "year": 1999,
  "rating": 8.4, "vote_count": 28000, "runtime": 139,
  "genres": ["Drama", "Thriller"],
  "countries": ["United States of America"],
  "trailer_key": "SUXWAEX2jlg",
  "cast": [
    { "name": "Brad Pitt", "character": "Tyler Durden", "profile_path": "https://..." }
  ]
}
```

---

### Seasons

**`GET /api/seasons.php?catalog_id=<id>`**

Returns the season list for a TV series. `catalog_id` is the local catalog table ID (same as `id` in the detail response).

**Response:**
```json
[
  { "id": 12345, "season_number": 1, "name": "Season 1",
    "episode_count": 10, "air_date": "2019-09-25",
    "overview": "...", "poster_url": "..." }
]
```

---

### Episodes

**`GET /api/episodes.php?catalog_id=<id>&season=<n>`**

Returns the episode list for a specific season, with watch progress for the current user/guest if available.

**Response:**
```json
[
  {
    "id": 99988, "episode_number": 1, "season_number": 1,
    "title": "Pilot", "overview": "...",
    "still_url": "https://image.tmdb.org/...",
    "runtime": 58, "air_date": "2019-09-25",
    "watch_progress": { "progress_sec": 1200, "duration_sec": 3480, "percent": 34.48 }
  }
]
```

`watch_progress` is `null` if the episode has not been started.

---

### Sources

**`GET /api/sources.php`**

| Parameter | Type | Notes |
|---|---|---|
| `catalog_id` | integer | Required |
| `type` | `movie` \| `tv` | Required |
| `season` | integer | Required for TV |
| `episode` | integer | Required for TV |

**Response:**
```json
{
  "sources": [
    { "id": "vidsrc",    "label": "VidSrc",     "url": "https://vidsrc.to/embed/movie/550",    "priority": 1 },
    { "id": "autoembed", "label": "AutoEmbed",   "url": "https://autoembed.cc/movie/tmdb/550",  "priority": 3 }
  ]
}
```

All sources are free. Priority determines the default order; lower = first.

---

### Related

**`GET /api/related.php?catalog_id=<id>`**

Returns up to 18 recommended or similar titles from TMDB.

---

### Continue Watching

**`GET /api/continue.php`** — list of in-progress items (percent < 95)

**`GET /api/continue.php?catalog_id=<id>`** — progress for a single title

**`POST /api/continue.php`**

```json
{ "catalog_id": 550, "episode_id": null, "progress_sec": 1800, "duration_sec": 8340 }
```

`percent` is computed server-side from `progress_sec / duration_sec`.

---

### Watch History

**`GET /api/history.php?page=<n>`** — paginated history (20 per page)

**`POST /api/history.php`** — same body as continue POST

**`DELETE /api/history.php`** — remove one entry: `{ "catalog_id": 550 }`

**`DELETE /api/history.php?all=1`** — clear all history

---

### Favorites

**`GET /api/favorites.php`** — full favorites list

**`GET /api/favorites.php?catalog_id=<id>`** — check if a title is favorited: `{ "in_favorites": true }`

**`POST /api/favorites.php`** — `{ "catalog_id": 550 }`

**`DELETE /api/favorites.php`** — `{ "catalog_id": 550 }`

---

### Auth

All auth actions are `POST` with a JSON body (except `me` and `logout` which can be `GET`).

**`POST /api/auth.php?action=register`**
```json
{ "email": "user@example.com", "password": "min8chars", "display_name": "Optional" }
```
Returns `{ "token": "...", "user": { "id": 1, "display_name": "...", "email": "..." } }` — status `201`.

**`POST /api/auth.php?action=login`**

Same body shape (no `display_name`). Returns same response shape — status `200`.

**`POST /api/auth.php?action=logout`**

Invalidates the session. Send `Authorization: Bearer <token>` header.

**`GET /api/auth.php?action=me`**

Returns the authenticated user's profile. Requires `Authorization: Bearer <token>`.
