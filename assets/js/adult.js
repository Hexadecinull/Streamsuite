/*
 * StreamSuite — Free, open-source streaming website
 * Copyright (C) 2026  StreamSuite Contributors
 * (GPL-3.0 license)
 */

const AdultPage = {
    page: 1, totalPages: 1, loading: false,
    type: 'movie', sort: 'popularity',

    init() {
        const prefs = (function() {
            try { return JSON.parse(localStorage.getItem('ss_prefs') || '{}'); } catch { return {}; }
        })();

        const gate    = document.getElementById('adult-gate');
        const content = document.getElementById('adult-content');

        if (!prefs.showAdult) {
            if (gate)    gate.style.display    = 'block';
            if (content) content.style.display = 'none';

            const settingsBtn = document.getElementById('open-settings-btn');
            if (settingsBtn) {
                settingsBtn.addEventListener('click', () => {
                    const overlay = document.getElementById('settings-overlay');
                    if (overlay) { overlay.style.display = 'flex'; document.body.style.overflow = 'hidden'; }
                });
            }
            return;
        }

        if (gate)    gate.style.display    = 'none';
        if (content) content.style.display = 'block';

        const form = document.getElementById('adult-filter-form');
        if (form) {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.type = document.getElementById('adult-type')?.value || 'movie';
                this.sort = document.getElementById('adult-sort')?.value || 'popularity';
                this.reset();
            });
        }

        this.load();
        this.setupInfiniteScroll();
    },

    reset() {
        this.page = 1; this.totalPages = 1;
        const g = document.getElementById('adult-grid');
        if (g) g.innerHTML = '';
        this.load();
    },

    async load() {
        if (this.loading || this.page > this.totalPages) return;
        this.loading = true;
        const loader = document.getElementById('adult-loader');
        if (loader) loader.style.display = 'block';
        try {
            const data = await Api.get(`/adult.php?type=${this.type}&sort=${this.sort}&page=${this.page}`);
            this.totalPages = data.total_pages || 1;
            this.renderItems(data.results || []);
            this.page++;
        } catch {}
        finally { this.loading = false; if (loader) loader.style.display = 'none'; }
    },

    renderItems(items) {
        const grid = document.getElementById('adult-grid');
        if (!grid) return;
        items.forEach(item => {
            const el = document.createElement('a');
            el.className = 'card';
            el.href = `/detail?id=${item.id}&type=${item.media_type}`;
            el.innerHTML = `
                <div class="card-poster">
                    <img data-src="${item.poster_url}"
                         src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 2 3'%3E%3C/svg%3E"
                         alt="${item.title}">
                    <span class="badge badge-nsfw" style="position:absolute;top:0.5rem;left:0.5rem;">18+</span>
                </div>
                <div class="card-body">
                    <div class="card-title">${item.title}</div>
                    <div class="card-meta">
                        <span class="card-rating">&#9733; ${item.rating || '—'}</span>
                        <span>${item.year || ''}</span>
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
};

if (document.getElementById('adult-page')) AdultPage.init();
