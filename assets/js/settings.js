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

const SettingsPage = {
    prefs: {},
    THEMES: ['obsidian', 'midnight', 'forest', 'ember', 'paper'],
    FONTS: ['satoshi', 'mono', 'system'],

    init() {
        this.prefs = JSON.parse(localStorage.getItem('ss_prefs') || '{}');
        this.prefs.theme    = this.prefs.theme    || 'obsidian';
        this.prefs.font     = this.prefs.font     || 'satoshi';
        this.prefs.fontSize = this.prefs.fontSize || 15;
        this.prefs.autoplay = this.prefs.autoplay !== false;
        this.prefs.rememberPos = this.prefs.rememberPos !== false;

        this.setupThemeSwatches();
        this.setupFontButtons();
        this.setupFontSize();
        this.setupToggles();
        this.setupDataActions();
        this.loadAccountSection();
    },

    save() {
        localStorage.setItem('ss_prefs', JSON.stringify(this.prefs));
    },

    setupThemeSwatches() {
        const container = document.getElementById('theme-swatches');
        if (!container) return;

        const themeColors = {
            obsidian: ['#0c0c0d', '#e8c97e'],
            midnight: ['#060610', '#7b8de8'],
            forest:   ['#080f0c', '#5ce08a'],
            ember:    ['#0f0b08', '#e07a5c'],
            paper:    ['#f4f2ee', '#2d2b3a'],
        };

        container.innerHTML = this.THEMES.map(theme => {
            const [bg, accent] = themeColors[theme] || ['#000', '#fff'];
            return `
                <button class="theme-swatch ${this.prefs.theme === theme ? 'active' : ''}"
                        data-theme="${theme}"
                        title="${theme.charAt(0).toUpperCase() + theme.slice(1)}"
                        style="background:${bg};border-color:${this.prefs.theme === theme ? accent : 'transparent'}">
                    <span style="position:absolute;bottom:4px;right:4px;width:10px;height:10px;border-radius:50%;background:${accent};"></span>
                </button>`;
        }).join('');

        container.querySelectorAll('.theme-swatch').forEach(btn => {
            btn.addEventListener('click', () => {
                const theme = btn.dataset.theme;
                this.prefs.theme = theme;
                this.save();
                document.documentElement.dataset.theme = theme;
                container.querySelectorAll('.theme-swatch').forEach(b => {
                    b.classList.toggle('active', b.dataset.theme === theme);
                });
                Toast.show(`Theme changed to ${theme}`);
            });
        });
    },

    setupFontButtons() {
        const container = document.getElementById('font-options');
        if (!container) return;

        const labels = { satoshi: 'Satoshi', mono: 'DM Mono', system: 'System' };
        container.innerHTML = this.FONTS.map(font => `
            <button class="font-option-btn ${this.prefs.font === font ? 'active' : ''}"
                    data-font="${font}">${labels[font]}</button>
        `).join('');

        container.querySelectorAll('.font-option-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                this.prefs.font = btn.dataset.font;
                this.save();
                document.documentElement.dataset.font = btn.dataset.font;
                container.querySelectorAll('.font-option-btn').forEach(b => {
                    b.classList.toggle('active', b.dataset.font === btn.dataset.font);
                });
            });
        });
    },

    setupFontSize() {
        const display  = document.getElementById('font-size-display');
        const decrease = document.getElementById('font-size-decrease');
        const increase = document.getElementById('font-size-increase');
        if (!display || !decrease || !increase) return;

        const update = () => {
            display.textContent = this.prefs.fontSize + 'px';
            document.documentElement.style.setProperty('--font-size-base', this.prefs.fontSize + 'px');
            this.save();
        };

        display.textContent = this.prefs.fontSize + 'px';

        decrease.addEventListener('click', () => {
            if (this.prefs.fontSize > 12) { this.prefs.fontSize--; update(); }
        });

        increase.addEventListener('click', () => {
            if (this.prefs.fontSize < 22) { this.prefs.fontSize++; update(); }
        });
    },

    setupToggles() {
        const autoplay = document.getElementById('toggle-autoplay');
        const rememberPos = document.getElementById('toggle-remember-pos');

        if (autoplay) {
            autoplay.checked = this.prefs.autoplay;
            autoplay.addEventListener('change', () => {
                this.prefs.autoplay = autoplay.checked;
                this.save();
            });
        }

        if (rememberPos) {
            rememberPos.checked = this.prefs.rememberPos;
            rememberPos.addEventListener('change', () => {
                this.prefs.rememberPos = rememberPos.checked;
                this.save();
            });
        }
    },

    setupDataActions() {
        const clearHistoryBtn = document.getElementById('clear-history-btn');
        const clearAllBtn     = document.getElementById('clear-all-btn');
        const exportBtn       = document.getElementById('export-data-btn');

        if (clearHistoryBtn) {
            clearHistoryBtn.addEventListener('click', async () => {
                if (!confirm('Clear all watch history?')) return;
                History.clear();
                try { await Api.delete('/history.php?all=1'); } catch {}
                Toast.show('Watch history cleared');
            });
        }

        if (clearAllBtn) {
            clearAllBtn.addEventListener('click', () => {
                if (!confirm('This will clear all local data including history, favorites, and preferences. Continue?')) return;
                localStorage.clear();
                Toast.show('All data cleared');
                setTimeout(() => window.location.reload(), 1000);
            });
        }

        if (exportBtn) {
            exportBtn.addEventListener('click', () => {
                const data = {
                    history:   History.getAll(),
                    favorites: Favorites.getAll(),
                    prefs:     JSON.parse(localStorage.getItem('ss_prefs') || '{}'),
                    exported:  new Date().toISOString(),
                };
                const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
                const url  = URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href     = url;
                link.download = 'streamsuite-data.json';
                link.click();
                URL.revokeObjectURL(url);
            });
        }
    },

    loadAccountSection() {
        const section  = document.getElementById('account-section');
        const authToken = localStorage.getItem('ss_auth_token');

        if (!section) return;

        if (authToken) {
            Api.get('/auth.php?action=me').then(user => {
                section.innerHTML = `
                    <div class="account-info">
                        <div class="account-avatar">${(user.display_name || 'U')[0].toUpperCase()}</div>
                        <div>
                            <div class="account-name">${this.escapeHtml(user.display_name || 'User')}</div>
                            <div class="account-email">${this.escapeHtml(user.email)}</div>
                        </div>
                    </div>
                    <div class="settings-row">
                        <div></div>
                        <div class="settings-control">
                            <button id="logout-btn" class="btn btn-secondary">Log Out</button>
                        </div>
                    </div>`;

                document.getElementById('logout-btn')?.addEventListener('click', async () => {
                    await Api.post('/auth.php?action=logout', {}).catch(() => {});
                    localStorage.removeItem('ss_auth_token');
                    Toast.show('Logged out');
                    setTimeout(() => window.location.reload(), 800);
                });
            }).catch(() => {
                localStorage.removeItem('ss_auth_token');
            });
        } else {
            section.innerHTML = `
                <p style="color:var(--c-text-3);font-size:0.85rem;margin-bottom:1rem;">
                    Sign in to sync your history and favorites across devices.
                </p>
                <div class="settings-control" style="gap:0.75rem;">
                    <a href="/login" class="btn btn-primary">Login</a>
                    <a href="/register" class="btn btn-secondary">Create Account</a>
                </div>`;
        }
    },

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text ?? '';
        return div.innerHTML;
    },
};

if (document.getElementById('settings-page')) {
    SettingsPage.init();
}
