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

const AnimePage = {
    page:       1,
    totalPages: 1,
    loading:    false,
    filter:     'popular',

    async init() {
        const tabs = document.getElementById('anime-tabs');
        if (tabs) {
            tabs.querySelectorAll('.trending-tab').forEach(btn => {
                btn.addEventListener('click', () => {
                    tabs.querySelectorAll('.trending-tab').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    this.filter = btn.dataset.filter;
                    this.reset();
                });
            });
        }

        const searchInput = document.getElementById('anime-search-input');
        if (searchInput) {
            let timer = null;
            searchInput.addEventListener('input', () => {
                clearTimeout(timer);
                timer = setTimeout(() => {
                    const q = searchInput.value.trim();
                    if (q.length >= 2) {
                        window.location.href = '/search?q=' + encodeURIComponent(q);
                    }
                }, 500);
            });
            searchInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' && searchInput.value.trim()) {
                    window.location.href = '/search?q=' + encodeURIComponent(searchInput.value.trim());
                }
            });
        }

        await this.load();
        this.setupInfiniteScroll();
    },

    reset() {
        this.page = 1;
        this.totalPages = 1;
        const grid = document.getElementById('browse-results');
        if (grid) grid.innerHTML = '';
        this.load();
    },

    async load() {
        if (this.loading || this.page > this.totalPages) return;
        this.loading = true;
        const loader = document.getElementById('browse-loader');
        if (loader) loader.style.display = 'block';
        try {
            const data = await Api.get(`/anime.php?filter=${this.filter}&page=${this.page}`);
            this.totalPages = data.total_pages || 1;
            this.renderItems(data.results || []);
            this.page++;
        } catch {}
        finally {
            this.loading = false;
            if (loader) loader.style.display = 'none';
        }
    },

    renderItems(items) {
        const grid = document.getElementById('browse-results');
        if (!grid) return;
        items.forEach(item => {
            const rating = item.rating ? parseFloat(item.rating).toFixed(1) : '—';
            const badge  = typeof AgeBadge !== 'undefined' ? AgeBadge.get(item.rating) : null;
            const el = document.createElement('a');
            el.className = 'card';
            el.href = `/detail?id=${item.id}&type=tv`;
            el.innerHTML = `
                <div class="card-poster">
                    <img data-src="${this.esc(item.poster_url)}"
                         src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 2 3'%3E%3C/svg%3E"
                         alt="${this.esc(item.title)}">
                </div>
                <div class="card-body">
                    <div class="card-title">${this.esc(item.title)}</div>
                    <div class="card-meta">
                        <span class="card-rating">&#9733; ${rating}</span>
                        <span>${item.year || ''}</span>
                        ${badge ? `<span class="age-badge ${badge.cls}">${badge.label}</span>` : ''}
                    </div>
                </div>`;
            grid.appendChild(el);
        });
        LazyLoader.refresh();
    },

    setupInfiniteScroll() {
        const sentinel = document.getElementById('scroll-sentinel');
        if (!sentinel || !window.IntersectionObserver) return;
        new IntersectionObserver((entries) => {
            if (entries[0].isIntersecting) this.load();
        }, { rootMargin: '200px' }).observe(sentinel);
    },

    esc(text) {
        const d = document.createElement('div');
        d.textContent = text ?? '';
        return d.innerHTML;
    },
};

if (document.getElementById('anime-page')) AnimePage.init();
