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

class ProgressTracker {
    constructor(catalogId, episodeId = null) {
        this.catalogId   = catalogId;
        this.episodeId   = episodeId;
        this.startTime   = Date.now();
        this.interval    = null;
        this.watchTime   = 0;
        this.durationSec = 0;
    }

    start() {
        this.interval = setInterval(() => this.save(), 15000);
        window.addEventListener('beforeunload', () => this.save());
    }

    setInitialProgress(seconds, duration = 0) {
        this.watchTime   = seconds;
        this.durationSec = duration;
    }

    async save() {
        const elapsed = Math.floor((Date.now() - this.startTime) / 1000) + this.watchTime;
        const payload = {
            catalog_id:   this.catalogId,
            progress_sec: elapsed,
            duration_sec: this.durationSec,
        };
        if (this.episodeId) payload.episode_id = this.episodeId;
        try {
            await Api.post('/continue.php', payload);
            History.addLocal(this.catalogId, elapsed);
        } catch {}
    }

    stop() {
        clearInterval(this.interval);
        this.save();
    }
}

class SourceManager {
    constructor(sources, iframeId) {
        this.sources      = sources;
        this.currentIndex = 0;
        this.iframe       = document.getElementById(iframeId);
    }

    current() {
        return this.sources[this.currentIndex];
    }

    load(index) {
        if (index < 0 || index >= this.sources.length) return;
        this.currentIndex = index;

        const loading = document.getElementById('player-loading');
        if (loading) loading.style.display = 'flex';
        this.iframe.src = 'about:blank';

        const timeoutId = setTimeout(() => {
            if (index + 1 < this.sources.length) {
                this.load(index + 1);
            } else {
                if (loading) loading.style.display = 'none';
                const errEl = document.getElementById('player-error');
                if (errEl) errEl.style.display = 'flex';
            }
        }, 12000);

        requestAnimationFrame(() => {
            this.iframe.src = this.current().url;
        });

        this.iframe.addEventListener('load', () => {
            clearTimeout(timeoutId);
            if (loading) loading.style.display = 'none';
        }, { once: true });

        this.renderButtons();
    }

    renderButtons() {
        const container = document.getElementById('server-buttons');
        if (!container) return;

        container.innerHTML = this.sources.map((source, i) => `
            <button class="server-btn ${i === this.currentIndex ? 'active' : ''}"
                    data-index="${i}">
                <span class="server-status"></span>
                ${this.escapeHtml(source.label)}
            </button>`
        ).join('');

        container.querySelectorAll('.server-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const idx = parseInt(btn.dataset.index);
                if (idx !== this.currentIndex) this.load(idx);
            });
        });
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text ?? '';
        return div.innerHTML;
    }
}

const Player = {
    sourceManager:   null,
    progressTracker: null,

    async init() {
        const params    = new URLSearchParams(window.location.search);
        const catalogId = parseInt(params.get('id') || '0');
        const type      = params.get('type') || 'movie';
        const season    = parseInt(params.get('s')  || '0');
        const episode   = parseInt(params.get('e')  || '0');

        if (!catalogId) return;

        try {
            const sourceParams = type === 'tv'
                ? `/sources.php?catalog_id=${catalogId}&type=tv&season=${season}&episode=${episode}`
                : `/sources.php?catalog_id=${catalogId}&type=movie`;

            const [sourceData, titleData] = await Promise.all([
                Api.get(sourceParams),
                Api.get(`/detail.php?id=${catalogId}&type=${type}`),
            ]);

            const titleEl = document.getElementById('player-title');
            if (titleEl) titleEl.textContent = titleData.title || 'Unknown';

            document.title = (titleData.title || 'Watch') + ' — StreamSuite';

            const backBtn = document.getElementById('back-to-detail');
            if (backBtn) backBtn.href = `/detail?id=${catalogId}`;

            if (type === 'tv' && season > 0 && episode > 0) {
                const episodeLabelEl = document.getElementById('episode-label');
                try {
                    const epData = await Api.get(`/episodes.php?catalog_id=${catalogId}&season=${season}`);
                    const ep = epData.find(e => e.episode_number === episode);
                    if (ep && episodeLabelEl) {
                        episodeLabelEl.textContent = `S${String(season).padStart(2,'0')}E${String(episode).padStart(2,'0')} · ${ep.title}`;
                    }
                } catch {}

                const prevBtn = document.getElementById('episode-prev');
                const nextBtn = document.getElementById('episode-next');

                if (prevBtn) {
                    if (episode > 1) {
                        prevBtn.href = `/watch?id=${catalogId}&type=tv&s=${season}&e=${episode - 1}`;
                    } else if (season > 1) {
                        prevBtn.href = `/watch?id=${catalogId}&type=tv&s=${season - 1}&e=1`;
                    } else {
                        prevBtn.hidden = true;
                    }
                }
                if (nextBtn) {
                    nextBtn.href = `/watch?id=${catalogId}&type=tv&s=${season}&e=${episode + 1}`;
                }
            } else {
                const episodeNav = document.getElementById('episode-nav');
                if (episodeNav) episodeNav.style.display = 'none';
            }

            this.sourceManager = new SourceManager(sourceData.sources, 'player-frame');
            this.sourceManager.load(0);

            const reloadBtn = document.getElementById('reload-btn');
            if (reloadBtn) {
                reloadBtn.addEventListener('click', () => {
                    this.sourceManager.load(this.sourceManager.currentIndex);
                });
            }

            const episodeId = type === 'tv' ? episode : null;
            this.progressTracker = new ProgressTracker(catalogId, episodeId);

            const prefs = JSON.parse(localStorage.getItem('ss_prefs') || '{}');
            if (prefs.rememberPos !== false) {
                const continueData = await Api.get(`/continue.php?catalog_id=${catalogId}`).catch(() => null);
                if (continueData?.progress_sec) {
                    this.progressTracker.setInitialProgress(
                        continueData.progress_sec,
                        continueData.duration_sec || 0
                    );
                }
            }

            this.progressTracker.start();

        } catch (error) {
            console.error('Player init failed:', error);
            const errEl = document.getElementById('player-error');
            if (errEl) errEl.style.display = 'flex';
        }
    },

    destroy() {
        if (this.progressTracker) this.progressTracker.stop();
    },
};

document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('player-frame')) Player.init();
});

window.addEventListener('beforeunload', () => Player.destroy());
