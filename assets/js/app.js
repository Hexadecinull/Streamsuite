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

const App = {
    init() {
        this.loadPreferences();
        this.setupMobileMenu();
        this.setupSearch();
        this.updateNavState();
    },

    loadPreferences() {
        const prefs = JSON.parse(localStorage.getItem('ss_prefs') || '{}');
        if (prefs.theme) {
            document.documentElement.dataset.theme = prefs.theme;
        }
        if (prefs.font) {
            document.documentElement.dataset.font = prefs.font;
        }
        if (prefs.fontSize) {
            document.documentElement.style.setProperty('--font-size-base', prefs.fontSize + 'px');
        }
    },

    setupMobileMenu() {
        const btn    = document.querySelector('.mobile-menu-btn');
        const drawer = document.querySelector('.mobile-drawer');
        if (!btn || !drawer) return;

        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            drawer.classList.toggle('open');
            btn.setAttribute('aria-expanded', drawer.classList.contains('open'));
        });

        document.addEventListener('click', (e) => {
            if (!drawer.contains(e.target) && !btn.contains(e.target)) {
                drawer.classList.remove('open');
                btn.setAttribute('aria-expanded', 'false');
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && drawer.classList.contains('open')) {
                drawer.classList.remove('open');
                btn.setAttribute('aria-expanded', 'false');
            }
        });
    },

    setupSearch() {
        const form = document.querySelector('.search-bar');
        if (!form) return;
        const input = form.querySelector('input');
        if (!input) return;

        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const query = input.value.trim();
            if (query) {
                window.location.href = '/search?q=' + encodeURIComponent(query);
            }
        });
    },

    updateNavState() {
        const path = window.location.pathname.replace(/\/$/, '') || '/';
        document.querySelectorAll('.nav-link').forEach(link => {
            const href = link.getAttribute('href') || '';
            const isActive = href === path || (href !== '/' && path.startsWith(href));
            link.classList.toggle('active', isActive);
        });
    },
};

document.addEventListener('DOMContentLoaded', () => App.init());
