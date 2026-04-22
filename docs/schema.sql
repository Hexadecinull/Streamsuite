-- StreamSuite Database Schema
-- MySQL 8.0+ / MariaDB 10.6+
-- UTF-8 throughout

SET NAMES utf8mb4;
SET time_zone = '+00:00';

CREATE TABLE IF NOT EXISTS users (
    id           INT UNSIGNED     AUTO_INCREMENT PRIMARY KEY,
    email        VARCHAR(255)     NOT NULL UNIQUE,
    password     VARCHAR(255)     NOT NULL,
    display_name VARCHAR(100),
    avatar_seed  VARCHAR(50),
    theme        VARCHAR(30)      DEFAULT 'obsidian',
    font         VARCHAR(50)      DEFAULT 'satoshi',
    created_at   TIMESTAMP        DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP        DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS sessions (
    id           VARCHAR(128)     PRIMARY KEY,
    user_id      INT UNSIGNED     NOT NULL,
    ip           VARCHAR(45),
    user_agent   TEXT,
    expires_at   TIMESTAMP        NOT NULL,
    created_at   TIMESTAMP        DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id  (user_id),
    INDEX idx_expires  (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS catalog (
    id             INT UNSIGNED     AUTO_INCREMENT PRIMARY KEY,
    tmdb_id        INT              NOT NULL,
    media_type     ENUM('movie','tv') NOT NULL,
    title          VARCHAR(500)     NOT NULL,
    original_title VARCHAR(500),
    overview       TEXT,
    poster_path    VARCHAR(300),
    backdrop_path  VARCHAR(300),
    release_date   DATE,
    year           SMALLINT,
    rating         DECIMAL(4,2),
    vote_count     INT              DEFAULT 0,
    popularity     DECIMAL(10,3)    DEFAULT 0,
    runtime        SMALLINT,
    genres         JSON,
    countries      JSON,
    languages      JSON,
    status         VARCHAR(50),
    tagline        VARCHAR(500),
    trailer_key    VARCHAR(50),
    cast_json      JSON,
    is_featured    TINYINT(1)       DEFAULT 0,
    cached_at      TIMESTAMP        DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_tmdb    (tmdb_id, media_type),
    INDEX idx_type          (media_type),
    INDEX idx_featured      (is_featured),
    INDEX idx_year          (year),
    INDEX idx_rating        (rating),
    INDEX idx_popularity    (popularity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tmdb_cache (
    cache_key    VARCHAR(255)     PRIMARY KEY,
    data         LONGTEXT         NOT NULL,
    expires_at   TIMESTAMP        NOT NULL,
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS watch_history (
    id           INT UNSIGNED     AUTO_INCREMENT PRIMARY KEY,
    user_id      INT UNSIGNED,
    guest_token  VARCHAR(64),
    catalog_id   INT UNSIGNED     NOT NULL,
    episode_id   INT,
    progress_sec INT              DEFAULT 0,
    duration_sec INT              DEFAULT 0,
    percent      DECIMAL(5,2)     DEFAULT 0,
    last_watched TIMESTAMP        DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    -- Composite unique keys enable ON DUPLICATE KEY UPDATE to work correctly
    UNIQUE KEY uniq_user_entry  (user_id,    catalog_id, episode_id),
    UNIQUE KEY uniq_guest_entry (guest_token, catalog_id, episode_id),
    FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE,
    FOREIGN KEY (catalog_id) REFERENCES catalog(id)  ON DELETE CASCADE,
    INDEX idx_user        (user_id),
    INDEX idx_guest       (guest_token),
    INDEX idx_last_watched (last_watched)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS favorites (
    id           INT UNSIGNED     AUTO_INCREMENT PRIMARY KEY,
    user_id      INT UNSIGNED,
    guest_token  VARCHAR(64),
    catalog_id   INT UNSIGNED     NOT NULL,
    added_at     TIMESTAMP        DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_user_fav  (user_id,     catalog_id),
    UNIQUE KEY uniq_guest_fav (guest_token, catalog_id),
    FOREIGN KEY (user_id)    REFERENCES users(id)   ON DELETE CASCADE,
    FOREIGN KEY (catalog_id) REFERENCES catalog(id) ON DELETE CASCADE,
    INDEX idx_user  (user_id),
    INDEX idx_guest (guest_token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS site_settings (
    setting_key  VARCHAR(100)     PRIMARY KEY,
    setting_val  TEXT             NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO site_settings VALUES
    ('site_name',       'StreamSuite'),
    ('site_tagline',    'Stream everything. Own nothing. Pay nothing.'),
    ('tmdb_language',   'en-US'),
    ('default_theme',   'obsidian'),
    ('default_font',    'satoshi'),
    ('allow_guests',    '1'),
    ('maintenance',     '0')
ON DUPLICATE KEY UPDATE setting_val = VALUES(setting_val);
