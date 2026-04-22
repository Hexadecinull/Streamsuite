# Embed Source System

StreamSuite does not host any video content. All streams are served through free, publicly accessible third-party embed providers via `<iframe>`.

---

## How It Works

1. The user selects a title (which has a TMDB ID)
2. `api/sources.php` generates embed URLs for every configured provider using the TMDB ID and, for TV, the season and episode number
3. The player page loads the first provider (lowest priority number) in an `<iframe>`
4. The user can switch providers via the server selector buttons if the current one fails
5. The switch is instant — no page reload, just a new `src` on the iframe

---

## Provider List

| ID | Label | Free | Movie | TV | Notes |
|---|---|---|---|---|---|
| `vidsrc` | VidSrc | Yes | `https://vidsrc.to/embed/movie/{tmdb_id}` | `https://vidsrc.to/embed/tv/{tmdb_id}/{season}/{episode}` | Most reliable |
| `vidsrc2` | VidSrc 2 | Yes | `https://vidsrc.me/embed/movie?tmdb={tmdb_id}` | `https://vidsrc.me/embed/tv?tmdb={tmdb_id}&season={season}&episode={episode}` | Mirror of VidSrc |
| `autoembed` | AutoEmbed | Yes | `https://autoembed.cc/movie/tmdb/{tmdb_id}` | `https://autoembed.cc/tv/tmdb/{tmdb_id}-{season}-{episode}` | Good coverage |
| `superembed` | SuperEmbed | Yes | `https://multiembed.mov/directstream.php?video_id={tmdb_id}&tmdb=1` | `...&s={season}&e={episode}` | Multi-source aggregator |
| `2embed` | 2Embed | Yes | `https://www.2embed.cc/embed/{tmdb_id}` | `https://www.2embed.cc/embedtv/{tmdb_id}&s={season}&e={episode}` | |
| `embedsu` | Embed.su | Yes | `https://embed.su/embed/movie/{tmdb_id}` | `https://embed.su/embed/tv/{tmdb_id}/{season}/{episode}` | Clean UI |

All six providers are free with no account or API key required.

---

## Adding a Provider

Edit the `EMBED_PROVIDERS` constant in `api/sources.php`:

```php
'myprovider' => [
    'movie'    => 'https://example.com/movie/{tmdb_id}',
    'tv'       => 'https://example.com/tv/{tmdb_id}/{season}/{episode}',
    'label'    => 'My Provider',
    'priority' => 7,
],
```

**Template placeholders:**

| Placeholder | Replaced with |
|---|---|
| `{tmdb_id}` | TMDB numeric ID of the title |
| `{season}` | Season number (TV only) |
| `{episode}` | Episode number (TV only) |

**Priority:** lower number = loaded first. Priority `1` is the default server. Users can switch freely.

---

## Content Security Policy

The `.htaccess` CSP `frame-src` directive explicitly lists each embed provider. If you add a new one, add its origin to the CSP too:

```apache
Header always set Content-Security-Policy \
    "... frame-src https://vidsrc.to https://vidsrc.me https://autoembed.cc \
               https://multiembed.mov https://www.2embed.cc https://embed.su \
               https://example.com ..."
```

---

## Legal Note

StreamSuite does not host, store, cache, or distribute any video content. It constructs URLs that point to third-party services. The legality of those third-party services varies by jurisdiction and is outside the scope of this project.
