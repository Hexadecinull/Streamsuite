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

const SearchPage = {
    currentPage: 1,
    totalPages: 1,
    isLoading: false,
    currentQuery: '',

    init() {
        const params = new URLSearchParams(window.location.search);
        this.currentQuery = params.get('q') || '';

        const queryDisplay = document.getElementById('search-query-display');
        if (queryDisplay && this.currentQuery) {
            queryDisplay.innerHTML = `Results for <span>"${this.escapeHtml(this.currentQuery)}"</span>`;
        }

        if (this.currentQuery) {
            this.loadResults(1);
        } else {
            this.renderEmpty();
        }

        this.setupInfiniteScroll();
    },

    async loadResults(page) {
        if (this.isLoading || page > this.totalPages) return;
        this.isLoading = true;
        this.showLoader(true);

        try {
            const data = await Api.get(
                `/search.php?q=${encodeURIComponent(this.currentQuery)}&page=${page}`
            );
            this.totalPages = data.total_pages;
            this.renderResults(data.results, page === 1);
            this.currentPage = page;

            const count = document.getElementById('result-count');
            if (count && page === 1) {
                count.textContent = data.total_results
                    ? `${data.total_results.toLocaleString()} results`
                    : '';
            }
        } catch {
            Toast.show('Search failed. Please try again.');
        } finally {
            this.isLoading = false;
            this.showLoader(false);
        }
    },

    renderResults(items, replace) {
        const container = document.getElementById('search-results');
        if (!container) return;

        if (replace) container.innerHTML = '';

        if (items.length === 0 && replace) {
            container.innerHTML = `
                <div class="no-results">
                    <div class="no-results-icon">◎</div>
                    <div class="no-results-title">No results found</div>
                    <p>Try a different search term or browse our catalog.</p>
                </div>`;
            return;
        }

        container.insertAdjacentHTML('beforeend',
            items.map(item => this.renderCard(item)).join('')
        );
        LazyLoader.refresh();
    },

    renderCard(item) {
        const badge = typeof AgeBadge !== 'undefined' ? AgeBadge.get(item.rating) : null;
        return `
            <a href="/detail?id=${item.id}&type=${item.media_type}" class="card">
                <div class="card-poster">
                    <img data-src="${item.poster_url}"
                         src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 2 3'%3E%3C/svg%3E"
                         alt="${this.escapeHtml(item.title)}">
                </div>
                <div class="card-body">
                    <div class="card-title">${this.escapeHtml(item.title)}</div>
                    <div class="card-meta">
                        <span class="rating">&#9733; ${item.rating?.toFixed(1) || '—'}</span>
                        <span>${item.year || ''}</span>
                        ${badge ? `<span class="age-badge ${badge.cls}">${badge.label}</span>` : ''}
                        <span class="badge">${item.media_type === 'tv' ? 'Series' : 'Movie'}</span>
                    </div>
                </div>
            </a>`;
    },

    renderEmpty() {
        const container = document.getElementById('search-results');
        if (container) {
            container.innerHTML = `
                <div class="no-results">
                    <div class="no-results-icon">◎</div>
                    <div class="no-results-title">Search for something</div>
                    <p>Enter a movie or series title in the search bar above.</p>
                </div>`;
        }
    },

    showLoader(show) {
        const loader = document.getElementById('search-loader');
        if (loader) loader.style.display = show ? 'block' : 'none';
    },

    setupInfiniteScroll() {
        const sentinel = document.getElementById('scroll-sentinel');
        if (!sentinel) return;

        const observer = new IntersectionObserver((entries) => {
            if (entries[0].isIntersecting && !this.isLoading && this.currentPage < this.totalPages) {
                this.loadResults(this.currentPage + 1);
            }
        }, { rootMargin: '300px' });

        observer.observe(sentinel);
    },

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text ?? '';
        return div.innerHTML;
    },
};

if (document.getElementById('search-page')) {
    SearchPage.init();
}
