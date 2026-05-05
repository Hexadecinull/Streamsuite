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
    featuredItems: [],
    featuredIndex: 0,
    rotateTimer:   null,

    async load() {
        await Promise.all([
            this.loadFeaturedAndRows(),
            this.loadContinueWatching(),
        ]);
    },

    async loadFeaturedAndRows() {
        try {
            const data = await Api.get('/home.php');
            this.renderFeatured(data.featured_pool || (data.featured ? [data.featured] : []), data.featured);
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
            if (!items.length) return;
            const section = document.getElementById('continue-row');
            if (!section) return;
            section.hidden = false;
            const container = document.getElementById('continue-items');
            if (!container) return;
            container.innerHTML = items.slice(0, 12).map(item => {
                const pct  = Math.min(100, Math.max(0, item.percent || 0));
                const href = `/watch?id=${item.catalog_id}&type=${item.media_type}`;
                return `
                    <a href="${href}" class="card card-continue">
                        <div class="card-poster">
                            <img data-src="${this.esc(item.poster_url)}"
                                 src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 2 3'%3E%3C/svg%3E"
                                 alt="${this.esc(item.title)}">
                            <div class="card-continue-progress" style="width:${pct}%"></div>
                        </div>
                        <div class="card-body">
                            <div class="card-title">${this.esc(item.title)}</div>
                            <div class="card-continue-meta">${Math.round(pct)}% watched</div>
                        </div>
                    </a>`;
            }).join('');
            LazyLoader.refresh();
        } catch {}
    },

    renderFeatured(pool, fallback) {
        const container = document.getElementById('featured-container');
        if (!container) return;
        container.classList.remove('skeleton');

        const items = pool && pool.length ? pool : (fallback ? [fallback] : []);
        if (!items.length) { container.style.display = 'none'; return; }

        this.featuredItems = items;
        this.featuredIndex = 0;
        this.showFeatured(0, container);
        this.buildDots(container);

        if (items.length > 1) {
            this.rotateTimer = setInterval(() => {
                this.featuredIndex = (this.featuredIndex + 1) % this.featuredItems.length;
                this.showFeatured(this.featuredIndex, container);
                this.updateDots();
            }, 9000);

            container.addEventListener('mouseenter', () => clearInterval(this.rotateTimer));
            container.addEventListener('mouseleave', () => {
                this.rotateTimer = setInterval(() => {
                    this.featuredIndex = (this.featuredIndex + 1) % this.featuredItems.length;
                    this.showFeatured(this.featuredIndex, container);
                    this.updateDots();
                }, 9000);
            });
        }
    },

    showFeatured(index, container) {
        const item   = this.featuredItems[index];
        if (!item) return;
        const rating = item.rating ? parseFloat(item.rating).toFixed(1) : '—';
        const type   = item.media_type === 'tv' ? 'Series' : 'Movie';
        const badge  = AgeBadge.get(item.vote_average);

        let backdropEl = container.querySelector('.featured-backdrop');
        if (!backdropEl) {
            backdropEl = document.createElement('div');
            backdropEl.className = 'featured-backdrop';
            container.insertBefore(backdropEl, container.firstChild);
        }
        backdropEl.style.backgroundImage = `url('${this.esc(item.backdrop_url)}')`;

        let contentEl = container.querySelector('.featured-content');
        if (!contentEl) {
            contentEl = document.createElement('div');
            contentEl.className = 'featured-content container';
            container.appendChild(contentEl);
        }

        contentEl.innerHTML = `
            <div class="featured-type-badge">${type}</div>
            <h1 class="text-hero">${this.esc(item.title)}</h1>
            <div class="featured-meta">
                <span class="featured-rating">&#9733; ${rating}</span>
                <span>${item.year || ''}</span>
                ${badge ? `<span class="age-badge ${badge.cls}">${badge.label}</span>` : ''}
            </div>
            <p class="featured-overview">${this.esc(item.overview || '')}</p>
            <div class="featured-actions">
                <a href="/watch?id=${item.id}&type=${item.media_type}" class="btn btn-primary">&#9654; Watch Now</a>
                <button class="btn btn-secondary featured-fav-btn" data-id="${item.id}" data-type="${item.media_type}" data-title="${this.esc(item.title)}" data-poster="${this.esc(item.poster_url)}">+ Favorites</button>
                <a href="/detail?id=${item.id}&type=${item.media_type}" class="btn btn-ghost">&#9432; Details</a>
            </div>`;

        const favBtn = contentEl.querySelector('.featured-fav-btn');
        if (favBtn) {
            if (Favorites.isFavorite(parseInt(item.id))) favBtn.textContent = '&#10003; Favorited';
            favBtn.addEventListener('click', () => {
                const added = Favorites.toggle(parseInt(favBtn.dataset.id), {
                    title:  favBtn.dataset.title,
                    poster: favBtn.dataset.poster,
                    type:   favBtn.dataset.type,
                });
                favBtn.textContent = added ? '&#10003; Favorited' : '+ Favorites';
                Toast.show(added ? 'Added to favorites' : 'Removed from favorites');
                const method = added ? Api.post : Api.delete;
                method.call(Api, '/favorites.php', { catalog_id: parseInt(favBtn.dataset.id) }).catch(() => {});
            });
        }
    },

    buildDots(container) {
        if (this.featuredItems.length <= 1) return;
        let dotsEl = container.querySelector('.featured-dots');
        if (!dotsEl) {
            dotsEl = document.createElement('div');
            dotsEl.className = 'featured-dots';
            container.appendChild(dotsEl);
        }
        dotsEl.innerHTML = this.featuredItems.map((_, i) =>
            `<button class="featured-dot ${i === 0 ? 'active' : ''}" data-index="${i}" aria-label="Slide ${i + 1}"></button>`
        ).join('');
        dotsEl.querySelectorAll('.featured-dot').forEach(dot => {
            dot.addEventListener('click', () => {
                clearInterval(this.rotateTimer);
                this.featuredIndex = parseInt(dot.dataset.index);
                this.showFeatured(this.featuredIndex, document.getElementById('featured-container'));
                this.updateDots();
            });
        });
    },

    updateDots() {
        document.querySelectorAll('.featured-dot').forEach((dot, i) => {
            dot.classList.toggle('active', i === this.featuredIndex);
        });
    },

    renderRows(rows) {
        const container = document.getElementById('rows-container');
        if (!container || !rows?.length) return;
        const meta = {
            trending:       { href: '/trending',          icon: '&#128293;', label: 'See all' },
            anime:          { href: '/anime',             icon: '&#127875;', label: 'All Anime' },
            popular_movies: { href: '/browse?type=movie', icon: '&#127916;', label: 'Browse Movies' },
            popular_tv:     { href: '/browse?type=tv',    icon: '&#128250;', label: 'Browse Series' },
            top_rated:      { href: '/browse?sort=vote_average', icon: '&#11088;', label: 'See all' },
            action:         { href: '/browse?type=movie&genre=28', icon: '&#128165;', label: 'Browse' },
            comedy:         { href: '/browse?type=movie&genre=35', icon: '&#128514;', label: 'Browse' },
        };
        container.innerHTML = rows.map(row => {
            const m = meta[row.id] || { href: '/browse', icon: '&#9654;', label: 'See all' };
            return `
                <section class="content-row">
                    <div class="row-header">
                        <div class="row-header-left">
                            <div class="row-icon">${m.icon}</div>
                            <h2 class="text-xl">${this.esc(row.title)}</h2>
                        </div>
                        <a href="${m.href}" class="see-all">${m.label} &rarr;</a>
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
        const badge  = AgeBadge.get(item.vote_average);
        return `
            <a href="/detail?id=${item.id}&type=${item.media_type}" class="card">
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
                </div>
            </a>`;
    },

    esc(text) {
        const d = document.createElement('div');
        d.textContent = text ?? '';
        return d.innerHTML;
    },
};

if (document.getElementById('home-page')) HomePage.load();
