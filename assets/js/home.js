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

const HomePage = {
    async load() {
        await Promise.all([
            this.loadFeaturedAndRows(),
            this.loadContinueWatching(),
        ]);
    },

    async loadFeaturedAndRows() {
        try {
            const data = await Api.get('/home.php');
            this.renderFeatured(data.featured);
            this.renderRows(data.rows);
        } catch {
            const fc = document.getElementById('featured-container');
            if (fc) fc.classList.remove('skeleton');
        }
    },

    async loadContinueWatching() {
        try {
            const data   = await Api.get('/continue.php');
            const items  = (data.results || []).filter(i => i.percent < 95 && i.percent > 1);
            if (items.length === 0) return;

            const section = document.getElementById('continue-row');
            if (!section) return;
            section.hidden = false;

            const container = document.getElementById('continue-items');
            if (!container) return;

            container.innerHTML = items.slice(0, 12).map(item => {
                const pct  = Math.min(100, Math.max(0, item.percent || 0));
                const href = item.media_type === 'tv'
                    ? `/watch?id=${item.catalog_id}&type=tv`
                    : `/watch?id=${item.catalog_id}&type=movie`;
                return `
                    <a href="${href}" class="card card-continue">
                        <div class="card-poster">
                            <img data-src="${this.escapeHtml(item.poster_url)}"
                                 src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 2 3'%3E%3C/svg%3E"
                                 alt="${this.escapeHtml(item.title)}">
                            <div class="card-continue-progress" style="width:${pct}%"></div>
                        </div>
                        <div class="card-body">
                            <div class="card-title">${this.escapeHtml(item.title)}</div>
                            <div class="card-continue-meta">${Math.round(pct)}% watched</div>
                        </div>
                    </a>`;
            }).join('');

            LazyLoader.refresh();
        } catch {}
    },

    renderFeatured(item) {
        const container = document.getElementById('featured-container');
        if (!container) return;
        container.classList.remove('skeleton');

        if (!item) {
            container.style.display = 'none';
            return;
        }

        const rating = item.rating ? parseFloat(item.rating).toFixed(1) : '—';
        const type   = item.media_type === 'tv' ? 'Series' : 'Movie';

        container.innerHTML = `
            <div class="featured-backdrop" style="background-image:url('${this.escapeHtml(item.backdrop_url)}')"></div>
            <div class="featured-content container">
                <h1 class="text-hero">${this.escapeHtml(item.title)}</h1>
                <div class="featured-meta">
                    <span class="rating">&#9733; ${rating}</span>
                    <span>${item.year || ''}</span>
                    <span>${type}</span>
                </div>
                <p class="featured-overview">${this.escapeHtml(item.overview || '')}</p>
                <div class="featured-actions">
                    <a href="/watch?id=${item.id}&type=${item.media_type}" class="btn btn-primary">&#9654; Watch Now</a>
                    <button class="btn btn-secondary" id="featured-fav-btn">+ Favorites</button>
                    <a href="/detail?id=${item.id}&type=${item.media_type}" class="btn btn-ghost">&#9432; Details</a>
                </div>
            </div>`;

        const favBtn = document.getElementById('featured-fav-btn');
        if (favBtn) {
            const isFav = Favorites.isFavorite(parseInt(item.id));
            if (isFav) favBtn.textContent = '&#10003; Favorited';

            favBtn.addEventListener('click', () => {
                const added = Favorites.toggle(parseInt(item.id), {
                    title:  item.title,
                    poster: item.poster_url,
                    type:   item.media_type,
                });
                favBtn.textContent = added ? '&#10003; Favorited' : '+ Favorites';
                Toast.show(added ? 'Added to favorites' : 'Removed from favorites');
                const method = added ? Api.post : Api.delete;
                method.call(Api, '/favorites.php', { catalog_id: parseInt(item.id) }).catch(() => {});
            });
        }
    },

    renderRows(rows) {
        const container = document.getElementById('rows-container');
        if (!container || !rows?.length) return;

        const rowTypeMap = {
            trending:      { href: '/browse',           label: 'See all' },
            popular_movies:{ href: '/browse?type=movie', label: 'Browse Movies' },
            popular_tv:    { href: '/browse?type=tv',    label: 'Browse Series' },
        };

        container.innerHTML = rows.map(row => {
            const meta = rowTypeMap[row.id] || { href: '/browse', label: 'See all' };
            return `
                <section class="content-row">
                    <div class="row-header">
                        <h2 class="text-xl">${this.escapeHtml(row.title)}</h2>
                        <a href="${meta.href}" class="see-all">${meta.label} &rarr;</a>
                    </div>
                    <div class="row-items">
                        ${row.items.map(item => this.renderCard(item)).join('')}
                    </div>
                </section>`;
        }).join('');

        LazyLoader.refresh();
    },

    renderCard(item) {
        const rating = item.rating ? parseFloat(item.rating).toFixed(1) : '—';
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
                    </div>
                </div>
            </a>`;
    },

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text ?? '';
        return div.innerHTML;
    },
};

if (document.getElementById('home-page')) {
    HomePage.load();
}
