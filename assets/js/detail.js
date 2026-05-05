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

const DetailPage = {
    itemId: null,
    mediaType: null,

    async init() {
        const params = new URLSearchParams(window.location.search);
        this.itemId = params.get('id');
        const typeHint = params.get('type') || '';
        if (!this.itemId) return;

        try {
            const url = typeHint
                ? `/detail.php?id=${this.itemId}&type=${typeHint}`
                : `/detail.php?id=${this.itemId}`;
            const data = await Api.get(url);
            this.mediaType = data.media_type;
            this.renderMain(data);
            this.renderCast(data.cast);

            if (data.media_type === 'tv') {
                const tvSection = document.getElementById('tv-section');
                if (tvSection) tvSection.style.display = '';
                await this.loadSeasons(this.itemId);
            }

            this.renderTrailer(data.trailer_key);
            this.loadRelated(this.itemId);
        } catch (error) {
            console.error('Failed to load detail:', error);
        }
    },

    renderMain(data) {
        document.title = `${data.title} — StreamSuite`;

        const set = (id, value) => {
            const el = document.getElementById(id);
            if (el) el.textContent = value ?? '';
        };

        set('detail-title',    data.title);
        set('detail-year',     data.year || '');
        set('detail-rating',   data.rating ? data.rating.toFixed(1) : '—');
        set('detail-runtime',  this.formatRuntime(data.runtime));
        set('detail-genres',   data.genres?.join(' · ') || '');
        set('detail-tagline',  data.tagline || '');
        set('detail-overview', data.overview || '');

        const poster   = document.getElementById('detail-poster');
        const backdrop = document.getElementById('detail-backdrop');
        if (poster)   poster.src = data.poster_url;
        if (backdrop) backdrop.style.backgroundImage = `url('${data.backdrop_url}')`;

        const watchBtn = document.getElementById('watch-btn');
        if (watchBtn) {
            watchBtn.href = `/watch?id=${this.itemId}&type=${data.media_type}`;
        }

        const shareBtn = document.getElementById('share-btn');
        if (shareBtn) {
            shareBtn.addEventListener('click', () => {
                const url = window.location.href;
                if (navigator.share) {
                    navigator.share({ title: data.title, url }).catch(() => {});
                } else {
                    navigator.clipboard.writeText(url);
                    Toast.show('Link copied to clipboard');
                }
            });
        }

        const favBtn = document.getElementById('favorite-btn');
        if (favBtn) {
            const isFav = Favorites.isFavorite(parseInt(this.itemId));
            this.updateFavBtn(favBtn, isFav);
            favBtn.addEventListener('click', () => {
                favBtn.classList.add('fav-pop');
                favBtn.addEventListener('animationend', () => favBtn.classList.remove('fav-pop'), { once: true });
                const added = Favorites.toggle(parseInt(this.itemId), {
                    title:  data.title,
                    poster: data.poster_url,
                    type:   data.media_type,
                });
                this.updateFavBtn(favBtn, added);
                Toast.show(added ? 'Added to favorites' : 'Removed from favorites');
                const method = added ? Api.post : Api.delete;
                method.call(Api, '/favorites.php', { catalog_id: parseInt(this.itemId) }).catch(() => {});
            });
        }
    },

    updateFavBtn(btn, isFav) {
        btn.textContent = isFav ? '✓ Favorited' : '+ Favorites';
        btn.classList.toggle('btn-favorited', isFav);
    },

    renderCast(cast) {
        const container = document.getElementById('cast-container');
        if (!container || !cast?.length) {
            const section = document.querySelector('.cast-section');
            if (section) section.style.display = 'none';
            return;
        }

        container.innerHTML = cast.slice(0, 8).map(person => `
            <div class="cast-card">
                <div class="cast-photo">
                    ${person.profile_path
                        ? `<img src="${person.profile_path}" alt="${this.escapeHtml(person.name)}" loading="lazy">`
                        : `<div class="cast-photo-placeholder">◉</div>`}
                </div>
                <div class="cast-name">${this.escapeHtml(person.name)}</div>
                <div class="cast-character">${this.escapeHtml(person.character || '')}</div>
            </div>`
        ).join('');
    },

    async loadSeasons(catalogId) {
        const seasons = await Api.get(`/seasons.php?catalog_id=${catalogId}`);

        const selector = document.getElementById('season-selector');
        if (!selector) return;

        selector.innerHTML = seasons.map(s =>
            `<option value="${s.season_number}">
                ${this.escapeHtml(s.name)} (${s.episode_count} ep.)
            </option>`
        ).join('');

        selector.addEventListener('change', async () => {
            const episodes = await Api.get(
                `/episodes.php?catalog_id=${catalogId}&season=${selector.value}`
            );
            this.renderEpisodes(episodes, parseInt(selector.value));
        });

        if (seasons.length > 0) {
            const episodes = await Api.get(
                `/episodes.php?catalog_id=${catalogId}&season=1`
            );
            this.renderEpisodes(episodes, 1);
        }
    },

    renderEpisodes(episodes, seasonNum) {
        const container = document.getElementById('episodes-container');
        if (!container) return;

        container.innerHTML = episodes.map(ep => {
            const progress = ep.watch_progress;
            return `
                <div class="episode-item">
                    <div class="episode-still">
                        <img src="${ep.still_url || '/assets/img/placeholder-poster.svg'}"
                             alt="${this.escapeHtml(ep.title)}" loading="lazy">
                    </div>
                    <div class="episode-info">
                        <div class="episode-header">
                            <span class="episode-number">E${String(ep.episode_number).padStart(2, '0')}</span>
                            <span class="episode-title">${this.escapeHtml(ep.title)}</span>
                            ${ep.runtime ? `<span class="episode-runtime">${ep.runtime}m</span>` : ''}
                        </div>
                        <p class="episode-overview">${this.escapeHtml(ep.overview || '')}</p>
                        ${progress?.percent > 0
                            ? `<div class="progress-bar"><div class="progress-fill" style="width:${progress.percent}%"></div></div>`
                            : ''}
                    </div>
                    <a href="/watch?id=${this.itemId}&type=tv&s=${seasonNum}&e=${ep.episode_number}"
                       class="btn btn-secondary btn-sm">&#9654; Play</a>
                </div>`;
        }).join('');
    },

    renderTrailer(key) {
        const container = document.getElementById('trailer-container');
        const section   = document.getElementById('trailer-section');
        if (!key) {
            if (section) section.style.display = 'none';
            return;
        }
        if (container) {
            container.innerHTML = `
                <div class="trailer-frame">
                    <iframe src="https://www.youtube.com/embed/${key}?rel=0"
                            allow="autoplay; encrypted-media; picture-in-picture"
                            allowfullscreen></iframe>
                </div>`;
        }
    },

    async loadRelated(catalogId) {
        try {
            const data = await Api.get(`/related.php?catalog_id=${catalogId}`);
            const container = document.getElementById('related-container');
            if (!container || !data.results?.length) return;
            container.innerHTML = data.results.slice(0, 6).map(item => this.renderCard(item)).join('');
            LazyLoader.refresh();
        } catch {}
    },

    renderCard(item) {
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
                        <span class="rating">★ ${item.rating?.toFixed(1) || '—'}</span>
                        <span>${item.year || ''}</span>
                    </div>
                </div>
            </a>`;
    },

    formatRuntime(minutes) {
        if (!minutes) return '';
        const h = Math.floor(minutes / 60);
        const m = minutes % 60;
        return h > 0 ? `${h}h ${m}m` : `${m}m`;
    },

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text ?? '';
        return div.innerHTML;
    },
};

if (document.getElementById('detail-page')) {
    DetailPage.init();
}
