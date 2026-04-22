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

const HistoryPage = {
    async init() {
        this.render(await this.load());
        this.setupClearButton();
    },

    async load() {
        try {
            const data = await Api.get('/history.php?page=1');
            if (data.results && data.results.length > 0) return data.results;
        } catch {}
        return History.getAll();
    },

    render(items) {
        const container = document.getElementById('history-results');
        const empty     = document.getElementById('history-empty');
        const count     = document.getElementById('history-count');

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
        this.attachRemoveButtons();
    },

    renderCard(item) {
        const id      = item.id || item.catalog_id;
        const poster  = item.poster || item.poster_url || '/assets/img/placeholder-poster.svg';
        const percent = Math.min(100, Math.max(0, item.percent || 0));
        const title   = item.title || 'Unknown';
        const date    = item.watched_at || item.last_watched;
        const dateStr = date ? new Date(date).toLocaleDateString(undefined, {
            month: 'short', day: 'numeric', year: 'numeric'
        }) : '';

        return `
            <div class="card card-continue" data-hist-id="${id}">
                <a href="/detail?id=${id}">
                    <div class="card-poster">
                        <img data-src="${this.escapeHtml(poster)}"
                             src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 2 3'%3E%3C/svg%3E"
                             alt="${this.escapeHtml(title)}">
                        ${percent > 0 ? `<div class="card-continue-progress" style="width:${percent}%"></div>` : ''}
                    </div>
                </a>
                <div class="card-body">
                    <div class="card-title">${this.escapeHtml(title)}</div>
                    <div class="card-meta">
                        ${percent > 0 ? `<span>${Math.round(percent)}% watched</span>` : ''}
                        ${dateStr ? `<span>${dateStr}</span>` : ''}
                    </div>
                    <button class="btn btn-ghost btn-sm remove-hist-btn"
                            data-id="${id}"
                            style="margin-top:0.4rem;width:100%;font-size:0.75rem;color:var(--c-text-3);">
                        Remove
                    </button>
                </div>
            </div>`;
    },

    attachRemoveButtons() {
        document.querySelectorAll('.remove-hist-btn').forEach(btn => {
            btn.addEventListener('click', async (e) => {
                e.preventDefault();
                const id = parseInt(btn.dataset.id);
                History.remove(id);
                try { await Api.delete('/history.php', { catalog_id: id }); } catch {}
                const card = btn.closest('[data-hist-id]');
                if (card) card.remove();
                const remaining = document.querySelectorAll('[data-hist-id]').length;
                const count = document.getElementById('history-count');
                if (count) count.textContent = remaining
                    ? `${remaining} title${remaining !== 1 ? 's' : ''}` : '';
                if (remaining === 0) {
                    const empty = document.getElementById('history-empty');
                    if (empty) empty.hidden = false;
                }
            });
        });
    },

    setupClearButton() {
        const btn = document.getElementById('clear-history-btn');
        if (!btn) return;
        btn.addEventListener('click', async () => {
            if (!confirm('Clear all watch history?')) return;
            History.clear();
            try { await Api.delete('/history.php?all=1'); } catch {}
            const container = document.getElementById('history-results');
            if (container) container.innerHTML = '';
            const empty = document.getElementById('history-empty');
            if (empty) empty.hidden = false;
            Toast.show('History cleared');
        });
    },

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text ?? '';
        return div.innerHTML;
    },
};

if (document.getElementById('history-page')) {
    HistoryPage.init();
}
