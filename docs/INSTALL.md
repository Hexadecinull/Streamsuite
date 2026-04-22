# Installation Guide

This guide covers deploying StreamSuite on shared hosting (InfinityFree / ct.ws) and on a VPS with Apache. Both paths are fully free.

---

## Requirements

| Requirement | Minimum | Recommended |
|---|---|---|
| PHP | 8.1 | 8.2+ |
| MySQL | 8.0 | 8.0+ |
| MariaDB (alternative) | 10.6 | 10.11+ |
| Apache | 2.4 | 2.4 |
| PHP extensions | `pdo_mysql`, `json` | + `mbstring` |
| TMDB API key | Free | Free |

> **Note:** PHP's `allow_url_fopen` must be `On` (the default on most hosts) — it's used for TMDB API calls.

---

## Getting a Free TMDB API Key

1. Register at [themoviedb.org](https://www.themoviedb.org/signup) (free)
2. Go to **Settings → API → Create**
3. Select **Developer**, fill in the form (use your site URL as the application URL)
4. Copy the **API Key (v3 auth)** — this is what goes in `config.php`

---

## Option A — InfinityFree / ct.ws (Free Hosting)

This is the path for `streamsuite.ct.ws`.

### Step 1: Create the Database

1. Log in to your InfinityFree control panel
2. Go to **MySQL Databases** → **Create Database**
3. Note the database name, username, password, and hostname (usually `sql***.infinityfree.com`)

### Step 2: Import the Schema

1. Open **phpMyAdmin** from the control panel
2. Select your new database
3. Click **Import** → Choose file → select `docs/schema.sql`
4. Click **Go**

### Step 3: Upload Files

**Via GitHub Actions (recommended):**

1. Fork this repository on GitHub
2. In your fork: **Settings → Secrets and variables → Actions → New repository secret**
   - `FTP_USERNAME` — your InfinityFree FTP username (format: `epiz_XXXXXXX`)
   - `FTP_PASSWORD` — your InfinityFree FTP password
3. Push any commit to `main` — the deploy workflow will upload everything to `/htdocs/`

**Via FTP manually:**

1. Connect to `ftp.ct.ws` with your credentials
2. Upload everything except: `.git/`, `.github/`, `docs/`, `*.md`, `composer.json`, `includes/config.php`
3. Upload target: `/htdocs/`

### Step 4: Configure

Create `includes/config.php` by copying `includes/config.example.php` and filling in your values:

```php
define('DB_HOST',    'sql123.infinityfree.com');  // your actual SQL host
define('DB_NAME',    'epiz_XXXXXXX_streamsuite');  // your database name
define('DB_USER',    'epiz_XXXXXXX');              // your DB username
define('DB_PASS',    'your_db_password');

define('TMDB_API_KEY', 'your_tmdb_v3_api_key');
define('TMDB_LANG',    'en-US');

define('JWT_SECRET',   'a_long_random_string_you_generated');
define('APP_URL',      'https://streamsuite.ct.ws');
define('APP_ENV',      'production');

define('GUEST_TOKEN_HEADER', 'HTTP_X_GUEST_TOKEN');
```

Generate `JWT_SECRET` locally:
```bash
php -r "echo bin2hex(random_bytes(32));"
```

Upload `includes/config.php` manually via FTP (it is excluded from the auto-deploy on purpose).

### Step 5: Verify

Visit `https://streamsuite.ct.ws` — the home page should load with trending content. If it's blank or showing PHP errors, check:
- `config.php` credentials match your InfinityFree database panel
- phpMyAdmin shows the schema tables were imported
- The FTP upload reached `/htdocs/` not `/htdocs/streamsuite/`

---

## Option B — VPS / Self-Hosted Apache

### Step 1: Clone the Repository

```bash
git clone https://github.com/Hexadecinull/Streamsuite.git /var/www/streamsuite
cd /var/www/streamsuite
```

### Step 2: Create the Database

```bash
mysql -u root -p
```

```sql
CREATE DATABASE streamsuite CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'streamsuite'@'localhost' IDENTIFIED BY 'a_strong_password';
GRANT SELECT, INSERT, UPDATE, DELETE ON streamsuite.* TO 'streamsuite'@'localhost';
FLUSH PRIVILEGES;
EXIT;

mysql -u root -p streamsuite < /var/www/streamsuite/docs/schema.sql
```

### Step 3: Configure Apache Virtual Host

```apache
<VirtualHost *:443>
    ServerName yourdomain.com
    DocumentRoot /var/www/streamsuite

    <Directory /var/www/streamsuite>
        AllowOverride All
        Require all granted
    </Directory>

    SSLEngine on
    SSLCertificateFile    /etc/letsencrypt/live/yourdomain.com/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/yourdomain.com/privkey.pem

    ErrorLog  ${APACHE_LOG_DIR}/streamsuite-error.log
    CustomLog ${APACHE_LOG_DIR}/streamsuite-access.log combined
</VirtualHost>
```

Enable and restart:
```bash
a2enmod rewrite headers deflate expires
a2ensite streamsuite
systemctl reload apache2
```

### Step 4: Configure the Application

```bash
cp includes/config.example.php includes/config.php
nano includes/config.php
```

Set file permissions:
```bash
chmod 640 includes/config.php
chown www-data:www-data includes/config.php
```

### Step 5: Set Up HTTPS (Let's Encrypt)

```bash
apt install certbot python3-certbot-apache
certbot --apache -d yourdomain.com
```

---

## Troubleshooting

| Symptom | Likely Cause | Fix |
|---|---|---|
| Blank white page | PHP error with `display_errors Off` | Check server error log |
| "Title not found" on detail page | Bad TMDB API key | Double-check `TMDB_API_KEY` in config.php |
| Progress not saving | Missing UNIQUE key on watch_history | Re-import `docs/schema.sql` |
| Can't log in (always 401) | `JWT_SECRET` regenerating at runtime | Make sure it's a static string in config.php |
| 404 on clean URLs | `mod_rewrite` not enabled or `AllowOverride None` | Enable `mod_rewrite`, set `AllowOverride All` |
| Images not loading | `allow_url_fopen` is Off | Enable in `php.ini` or use cURL (see below) |
| HTTPS loop on shared host | Cloudflare + direct HTTPS check | `.htaccess` already handles `X-Forwarded-Proto` |

### Switching to cURL for TMDB Requests

If `allow_url_fopen` is disabled on your host, open `includes/tmdb.php` and replace the `file_get_contents` fetch with:

```php
$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 10,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_USERAGENT      => 'StreamSuite/1.0',
]);
$raw = curl_exec($ch);
curl_close($ch);
```

---

## Updating

```bash
git pull origin main
# Re-import schema only if the CHANGELOG mentions schema changes
# mysql -u streamsuite -p streamsuite < docs/schema.sql
```

The FTP deploy GitHub Action handles updates automatically on push to `main`.
