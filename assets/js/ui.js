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

const Toast = {
    container: null,

    ensureContainer() {
        if (!this.container) {
            this.container = document.createElement('div');
            this.container.className = 'toast-container';
            document.body.appendChild(this.container);
        }
    },

    show(message, duration = 3000) {
        this.ensureContainer();
        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.textContent = message;
        this.container.appendChild(toast);

        requestAnimationFrame(() => toast.classList.add('toast-visible'));

        setTimeout(() => {
            toast.classList.remove('toast-visible');
            setTimeout(() => toast.remove(), 300);
        }, duration);
    },
};

const Modal = {
    overlay: null,

    create(content, options = {}) {
        this.close();
        this.overlay = document.createElement('div');
        this.overlay.className = 'modal-overlay';
        const modal = document.createElement('div');
        modal.className = 'modal';
        if (options.className) modal.classList.add(options.className);
        modal.innerHTML = content;
        this.overlay.appendChild(modal);
        document.body.appendChild(this.overlay);

        requestAnimationFrame(() => this.overlay.classList.add('modal-visible'));

        if (options.closeOnClick) {
            this.overlay.addEventListener('click', (e) => {
                if (e.target === this.overlay) this.close();
            });
        }

        const closeBtn = modal.querySelector('[data-close-modal]');
        if (closeBtn) closeBtn.addEventListener('click', () => this.close());

        document.addEventListener('keydown', this._escHandler = (e) => {
            if (e.key === 'Escape') this.close();
        });

        return modal;
    },

    close() {
        if (this.overlay) {
            this.overlay.remove();
            this.overlay = null;
        }
        if (this._escHandler) {
            document.removeEventListener('keydown', this._escHandler);
            this._escHandler = null;
        }
    },
};

const LazyLoader = {
    observer: null,

    init() {
        this.observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    const src = img.dataset.src;
                    if (src) {
                        img.src = src;
                        img.removeAttribute('data-src');
                        img.addEventListener('load', () => img.classList.add('img-loaded'), { once: true });
                        img.addEventListener('error', () => {
                            img.src = '/assets/img/placeholder-poster.svg';
                        }, { once: true });
                    }
                    this.observer.unobserve(img);
                }
            });
        }, { rootMargin: '200px' });

        document.querySelectorAll('img[data-src]').forEach(img => this.observer.observe(img));
    },

    refresh() {
        if (this.observer) {
            document.querySelectorAll('img[data-src]').forEach(img => this.observer.observe(img));
        }
    },
};

const History = {
    storageKey: 'ss_history',

    getAll() {
        return JSON.parse(localStorage.getItem(this.storageKey) || '[]');
    },

    addLocal(catalogId, progressSec, meta = {}) {
        const history = this.getAll().filter(h => h.id !== catalogId);
        history.unshift({
            id:         catalogId,
            progress:   progressSec,
            watched_at: new Date().toISOString(),
            title:      meta.title  || '',
            poster:     meta.poster || '',
            type:       meta.type   || 'movie',
        });
        localStorage.setItem(this.storageKey, JSON.stringify(history.slice(0, 200)));
    },

    remove(catalogId) {
        const history = this.getAll().filter(h => h.id !== catalogId);
        localStorage.setItem(this.storageKey, JSON.stringify(history));
    },

    clear() {
        localStorage.removeItem(this.storageKey);
    },
};

const Favorites = {
    storageKey: 'ss_favorites',

    getAll() {
        return JSON.parse(localStorage.getItem(this.storageKey) || '[]');
    },

    isFavorite(catalogId) {
        return this.getAll().some(f => f.id === catalogId);
    },

    toggle(catalogId, meta = {}) {
        const favs  = this.getAll();
        const index = favs.findIndex(f => f.id === catalogId);

        if (index >= 0) {
            favs.splice(index, 1);
            localStorage.setItem(this.storageKey, JSON.stringify(favs));
            return false;
        }

        favs.push({
            id:       catalogId,
            title:    meta.title  || '',
            poster:   meta.poster || '',
            type:     meta.type   || 'movie',
            added_at: new Date().toISOString(),
        });
        localStorage.setItem(this.storageKey, JSON.stringify(favs));
        return true;
    },

    clear() {
        localStorage.removeItem(this.storageKey);
    },
};

document.addEventListener('DOMContentLoaded', () => {
    LazyLoader.init();
});

const AgeBadge = {
    get(voteAverage) {
        const prefs = (function() {
            try { return JSON.parse(localStorage.getItem('ss_prefs') || '{}'); }
            catch { return {}; }
        })();
        if (!prefs.showAgeBadges && prefs.showAgeBadges !== undefined) return null;
        const r = parseFloat(voteAverage) || 0;
        if (r === 0) return null;
        if (r >= 8.5) return { label: 'G',    cls: 'age-badge-g' };
        if (r >= 7.0) return { label: 'PG',   cls: 'age-badge-pg' };
        if (r >= 5.5) return { label: 'PG-13',cls: 'age-badge-pg13' };
        if (r >= 4.0) return { label: 'R',    cls: 'age-badge-r' };
        return { label: '18+', cls: 'age-badge-18' };
    },
};
