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

const FavoritesPage = {
    async init() {
        this.render(await this.load());
        this.setupClearButton();
    },

    async load() {
        const localItems = Favorites.getAll();
        if (localItems.length > 0) return localItems;

        try {
            const data = await Api.get('/favorites.php');
            return data.results || [];
        } catch {
            return [];
        }
    },

    render(items) {
        const container = document.getElementById('favorites-results');
        const empty     = document.getElementById('favorites-empty');
        const count     = document.getElementById('favorites-count');

        if (!container) return;

        if (count) {
            count.textContent = items.length
                ? `${items.length} title${items.length !== 1 ? 's' : ''}`
                : '';
        }

        if (items.length === 0) {
            container.innerHTML = '';
            if (empty) empty.hidden = false;
            return;
        }

        if (empty) empty.hidden = true;

        container.innerHTML = items.map(item => this.renderCard(item)).join('');
        LazyLoader.refresh();
        this.attachRemoveButtons(items);
    },

    renderCard(item) {
        const id      = item.id || item.catalog_id;
        const poster  = item.poster || item.poster_url || '/assets/img/placeholder-poster.svg';
        const year    = item.year || '';
        const rating  = item.rating ? item.rating.toFixed(1) : '—';

        return `
            <div class="card" data-fav-id="${id}">
                <a href="/detail?id=${id}">
                    <div class="card-poster">
                        <img data-src="${this.escapeHtml(poster)}"
                             src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 2 3'%3E%3C/svg%3E"
                             alt="${this.escapeHtml(item.title)}">
                    </div>
                </a>
                <div class="card-body">
                    <div class="card-title">${this.escapeHtml(item.title)}</div>
                    <div class="card-meta">
                        <span class="rating">★ ${rating}</span>
                        <span>${year}</span>
                    </div>
                    <button class="btn btn-ghost btn-sm remove-fav-btn"
                            data-id="${id}"
                            style="margin-top:0.4rem;width:100%;font-size:0.75rem;color:var(--c-red);">
                        Remove
                    </button>
                </div>
            </div>`;
    },

    attachRemoveButtons(items) {
        document.querySelectorAll('.remove-fav-btn').forEach(btn => {
            btn.addEventListener('click', async (e) => {
                e.preventDefault();
                const id = parseInt(btn.dataset.id);
                Favorites.toggle(id);
                try { await Api.delete('/favorites.php', { catalog_id: id }); } catch {}
                const card = btn.closest('[data-fav-id]');
                if (card) card.remove();
                const remaining = document.querySelectorAll('[data-fav-id]').length;
                const count = document.getElementById('favorites-count');
                if (count) count.textContent = remaining
                    ? `${remaining} title${remaining !== 1 ? 's' : ''}` : '';
                if (remaining === 0) {
                    const empty = document.getElementById('favorites-empty');
                    if (empty) empty.hidden = false;
                }
                Toast.show('Removed from favorites');
            });
        });
    },

    setupClearButton() {
        const btn = document.getElementById('clear-favorites-btn');
        if (!btn) return;
        btn.addEventListener('click', async () => {
            if (!confirm('Remove all favorites?')) return;
            Favorites.clear();
            try { await Api.delete('/favorites.php', { all: true }); } catch {}
            const container = document.getElementById('favorites-results');
            if (container) container.innerHTML = '';
            const empty = document.getElementById('favorites-empty');
            if (empty) empty.hidden = false;
            Toast.show('Favorites cleared');
        });
    },

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text ?? '';
        return div.innerHTML;
    },
};

if (document.getElementById('favorites-page')) {
    FavoritesPage.init();
}
