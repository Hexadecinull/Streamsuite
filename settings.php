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

$pageTitle       = 'Settings';
$pageDescription = 'Customize your StreamSuite experience.';
$extraCss        = ['/assets/css/settings.css'];
require_once __DIR__ . '/includes/head.php';
?>

<header class="site-header">
    <div class="container header-inner">
        <a href="/" class="logo">
            <img src="/assets/img/logo-mark.svg" alt="" class="logo-mark">
            <span>StreamSuite</span>
        </a>
        <nav class="nav-links">
            <a href="/" class="nav-link">Home</a>
            <a href="/browse?type=movie" class="nav-link">Movies</a>
            <a href="/browse?type=tv" class="nav-link">Series</a>
            <a href="/browse" class="nav-link">Browse</a>
        </nav>
        <button class="mobile-menu-btn btn btn-icon">&#9776;</button>
    </div>
</header>

<main id="settings-page" class="container">
    <h1 class="settings-page-title text-2xl">Settings</h1>

    <div class="settings-layout">

        <div class="settings-section">
            <div class="settings-section-title">Appearance</div>

            <div class="settings-row">
                <div>
                    <div class="settings-row-label">Theme</div>
                    <div class="settings-row-desc">Choose your color scheme</div>
                </div>
                <div id="theme-swatches" class="theme-swatches settings-control"></div>
            </div>

            <div class="settings-row">
                <div>
                    <div class="settings-row-label">Font</div>
                    <div class="settings-row-desc">Display typeface</div>
                </div>
                <div id="font-options" class="font-options settings-control"></div>
            </div>

            <div class="settings-row">
                <div>
                    <div class="settings-row-label">Font Size</div>
                    <div class="settings-row-desc">Text size across the site</div>
                </div>
                <div class="settings-control font-size-control">
                    <button id="font-size-decrease" class="btn btn-secondary btn-icon">A&minus;</button>
                    <span id="font-size-display">15px</span>
                    <button id="font-size-increase" class="btn btn-secondary btn-icon">A+</button>
                </div>
            </div>
        </div>

        <div class="settings-section">
            <div class="settings-section-title">Player</div>

            <div class="settings-row">
                <div>
                    <div class="settings-row-label">Autoplay Next Episode</div>
                    <div class="settings-row-desc">Automatically play the next episode</div>
                </div>
                <div class="settings-control">
                    <label class="toggle-switch">
                        <input type="checkbox" id="toggle-autoplay">
                        <div class="toggle-track"><div class="toggle-thumb"></div></div>
                    </label>
                </div>
            </div>

            <div class="settings-row">
                <div>
                    <div class="settings-row-label">Remember Position</div>
                    <div class="settings-row-desc">Resume from where you left off</div>
                </div>
                <div class="settings-control">
                    <label class="toggle-switch">
                        <input type="checkbox" id="toggle-remember-pos">
                        <div class="toggle-track"><div class="toggle-thumb"></div></div>
                    </label>
                </div>
            </div>
        </div>

        <div class="settings-section">
            <div class="settings-section-title">Data</div>

            <div class="settings-row">
                <div>
                    <div class="settings-row-label">Watch History</div>
                    <div class="settings-row-desc">Your viewing activity</div>
                </div>
                <div class="settings-control" style="gap:0.5rem;">
                    <a href="/history" class="btn btn-secondary btn-sm">View</a>
                    <button id="clear-history-btn" class="btn btn-ghost btn-sm"
                            style="color:var(--c-red);">Clear</button>
                </div>
            </div>

            <div class="settings-row">
                <div>
                    <div class="settings-row-label">Favorites</div>
                    <div class="settings-row-desc">Your saved titles</div>
                </div>
                <div class="settings-control">
                    <a href="/favorites" class="btn btn-secondary btn-sm">View</a>
                </div>
            </div>

            <div class="settings-row">
                <div>
                    <div class="settings-row-label">Guest Data</div>
                    <div class="settings-row-desc">All local data stored on this device</div>
                </div>
                <div class="settings-control" style="gap:0.5rem;">
                    <button id="export-data-btn" class="btn btn-secondary btn-sm">Export JSON</button>
                    <button id="clear-all-btn" class="btn btn-ghost btn-sm"
                            style="color:var(--c-red);">Clear All</button>
                </div>
            </div>
        </div>

        <div class="settings-section">
            <div class="settings-section-title">Account</div>
            <div id="account-section">
                <div class="browse-loader" style="height:60px;display:flex;align-items:center;justify-content:center;"></div>
            </div>
        </div>

        <div class="settings-section">
            <div class="settings-section-title">About</div>
            <div class="about-item">
                <span class="about-item-label">Version</span>
                <span class="about-item-value">1.0.0</span>
            </div>
            <div class="about-item">
                <span class="about-item-label">License</span>
                <span class="about-item-value">GPL-3.0</span>
            </div>
            <div class="about-item">
                <span class="about-item-label">Source Code</span>
                <a href="https://github.com/SSMG4/streamsuite" class="about-item-value"
                   style="color:var(--c-accent);" target="_blank" rel="noopener">GitHub</a>
            </div>
            <div class="about-item" style="font-size:0.75rem;color:var(--c-text-3);padding-top:0.75rem;border:none;">
                This product uses the TMDB API but is not endorsed or certified by TMDB.
            </div>
        </div>

    </div>
</main>

<div class="mobile-drawer">
    <a href="/" class="mobile-nav-item">Home</a>
    <a href="/browse?type=movie" class="mobile-nav-item">Movies</a>
    <a href="/browse?type=tv" class="mobile-nav-item">Series</a>
    <a href="/browse" class="mobile-nav-item">Browse</a>
    <a href="/favorites" class="mobile-nav-item">Favorites</a>
    <a href="/history" class="mobile-nav-item">History</a>
    <a href="/settings" class="mobile-nav-item">Settings</a>
    <hr style="border-color:var(--c-border);margin:0.5rem 0;">
    <a href="/login" class="mobile-nav-item">Login / Register</a>
</div>

<script src="/assets/js/api.js"></script>
<script src="/assets/js/ui.js"></script>
<script src="/assets/js/settings.js"></script>
<script src="/assets/js/app.js"></script>
</body>
</html>
