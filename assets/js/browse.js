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

const BrowsePage = {
    currentPage: 1,
    totalPages:  1,
    isLoading:   false,
    filters: {
        type:  'movie',
        genre: '',
        year:  '',
        sort:  'popularity',
        order: 'desc',
    },

    init() {
        this.parseUrlParams();
        this.syncFormToFilters();
        this.updateHeading();
        this.setupFilters();
        this.setupBrowseSearch();
        this.loadPage(1);
        this.setupInfiniteScroll();
    },

    updateHeading() {
        const h = document.getElementById('browse-heading');
        if (!h) return;
        if (this.filters.type === 'movie') h.textContent = 'Movies';
        else if (this.filters.type === 'tv') h.textContent = 'Series';
        else h.textContent = 'Browse';
    },

    setupBrowseSearch() {
        const input = document.getElementById('browse-search-input');
        if (!input) return;
        let timer = null;
        input.addEventListener('input', () => {
            clearTimeout(timer);
            timer = setTimeout(() => {
                const q = input.value.trim();
                if (q.length >= 2) {
                    window.location.href = '/search?q=' + encodeURIComponent(q);
                }
            }, 500);
        });
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                const q = input.value.trim();
                if (q) window.location.href = '/search?q=' + encodeURIComponent(q);
            }
        });
    },

    parseUrlParams() {
        const params = new URLSearchParams(window.location.search);
        const allowed = ['type', 'genre', 'year', 'sort', 'order'];
        allowed.forEach(key => {
            if (params.has(key)) this.filters[key] = params.get(key);
        });
    },

    syncFormToFilters() {
        const form = document.getElementById('filter-form');
        if (!form) return;
        Object.entries(this.filters).forEach(([key, val]) => {
            if (form[key]) form[key].value = val;
        });
    },

    setupFilters() {
        const form = document.getElementById('filter-form');
        if (!form) return;

        form.addEventListener('submit', (e) => {
            e.preventDefault();
            ['type', 'genre', 'year', 'sort', 'order'].forEach(key => {
                if (form[key]) this.filters[key] = form[key].value;
            });
            this.currentPage = 1;
            this.totalPages  = 1;
            this.clearResults();
            this.loadPage(1);
            this.updateUrl();
        });
    },

    updateUrl() {
        const params = new URLSearchParams();
        Object.entries(this.filters).forEach(([key, val]) => {
            if (val) params.set(key, val);
        });
        window.history.replaceState({}, '', `${window.location.pathname}?${params}`);
    },

    async loadPage(page) {
        if (this.isLoading || page > this.totalPages) return;
        this.isLoading = true;
        this.showLoader(true);

        try {
            const params = new URLSearchParams({ ...this.filters, page });
            const data   = await Api.get(`/browse.php?${params}`);
            this.totalPages = data.total_pages;
            this.renderItems(data.results, page === 1);
            this.currentPage = page;

            const countEl = document.getElementById('result-count');
            if (countEl && page === 1) {
                countEl.textContent = data.total_results
                    ? `${data.total_results.toLocaleString()} titles`
                    : '';
            }
        } catch {
            Toast.show('Failed to load content. Please try again.');
        } finally {
            this.isLoading = false;
            this.showLoader(false);
        }
    },

    renderItems(items, replace) {
        const container = document.getElementById('browse-results');
        if (!container) return;

        if (replace) container.innerHTML = '';

        if (items.length === 0 && replace) {
            container.innerHTML = `
                <div class="no-results">
                    <div class="no-results-icon">&#9685;</div>
                    <div class="no-results-title">No results found</div>
                    <p>Try adjusting the filters above.</p>
                </div>`;
            return;
        }

        container.insertAdjacentHTML('beforeend',
            items.map(item => this.renderCard(item)).join('')
        );
        LazyLoader.refresh();
    },

    renderCard(item) {
        const rating = item.rating ? parseFloat(item.rating).toFixed(1) : '—';
        const badge  = typeof AgeBadge !== 'undefined' ? AgeBadge.get(item.rating) : null;
        return `
            <a href="/detail?id=${item.id}&type=${item.media_type}" class="card">
                <div class="card-poster">
                    <img data-src="${this.escapeHtml(item.poster_url)}"
                         src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 2 3'%3E%3C/svg%3E"
                         alt="${this.escapeHtml(item.title)}">
                </div>
                <div class="card-body">
                    <div class="card-title">${this.escapeHtml(item.title)}</div>
                    <div class="card-meta">
                        <span class="rating">&#9733; ${rating}</span>
                        <span>${item.year || ''}</span>
                        ${badge ? `<span class="age-badge ${badge.cls}">${badge.label}</span>` : ''}
                    </div>
                </div>
            </a>`;
    },

    clearResults() {
        const container = document.getElementById('browse-results');
        if (container) container.innerHTML = '';
    },

    showLoader(show) {
        const loader = document.getElementById('browse-loader');
        if (loader) loader.style.display = show ? 'block' : 'none';
    },

    setupInfiniteScroll() {
        const sentinel = document.getElementById('scroll-sentinel');
        if (!sentinel) return;

        const observer = new IntersectionObserver((entries) => {
            if (entries[0].isIntersecting && !this.isLoading && this.currentPage < this.totalPages) {
                this.loadPage(this.currentPage + 1);
            }
        }, { rootMargin: '400px' });

        observer.observe(sentinel);
    },

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text ?? '';
        return div.innerHTML;
    },
};

if (document.getElementById('browse-page')) {
    BrowsePage.init();
}
