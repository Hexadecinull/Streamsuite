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

const App = {
    init() {
        this.loadPreferences();
        this.setupPanels();
        this.setupMobileMenu();
        this.setupSearch();
        this.setupSettings();
        this.setupAuth();
        this.updateNavState();
        this.syncSettingsUI();
    },

    loadPreferences() {
        const prefs = this.getPrefs();
        if (prefs.theme) document.documentElement.dataset.theme = prefs.theme;
        if (prefs.font)  document.documentElement.dataset.font  = prefs.font;
        if (prefs.fontSize) {
            document.documentElement.style.setProperty('--font-size-base', prefs.fontSize + 'px');
        }
    },

    getPrefs() {
        try { return JSON.parse(localStorage.getItem('ss_prefs') || '{}'); }
        catch { return {}; }
    },

    setPrefs(obj) {
        const current = this.getPrefs();
        localStorage.setItem('ss_prefs', JSON.stringify(Object.assign(current, obj)));
    },

    setupPanels() {
        const openModal = (overlay) => {
            overlay.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) closeAll();
            }, { once: false });
        };

        const closeAll = () => {
            ['settings-overlay', 'account-overlay'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.style.display = 'none';
            });
            const drawer = document.getElementById('mobile-drawer');
            if (drawer) drawer.style.display = 'none';
            document.body.style.overflow = '';
        };

        const wire = (btnId, overlayId) => {
            const btn = document.getElementById(btnId);
            const overlay = document.getElementById(overlayId);
            if (btn && overlay) btn.addEventListener('click', () => openModal(overlay));
        };

        wire('settings-btn',    'settings-overlay');
        wire('account-btn',     'account-overlay');
        wire('mobile-settings-btn', 'settings-overlay');
        wire('mobile-account-btn',  'account-overlay');

        document.querySelectorAll('.modal-close').forEach(btn => {
            btn.addEventListener('click', closeAll);
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeAll();
        });

        document.querySelectorAll('.modal-overlay').forEach(overlay => {
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) closeAll();
            });
        });

        document.querySelectorAll('.modal-tabs').forEach(tabBar => {
            tabBar.querySelectorAll('.modal-tab').forEach(tab => {
                tab.addEventListener('click', () => {
                    const box = tabBar.closest('.modal-box');
                    if (!box) return;
                    tabBar.querySelectorAll('.modal-tab').forEach(t => t.classList.remove('active'));
                    tab.classList.add('active');
                    const panel = tab.dataset.tab;
                    box.querySelectorAll('.modal-tab-panel').forEach(p => {
                        p.style.display = p.dataset.panel === panel ? '' : 'none';
                    });
                });
            });
        });
    },

    setupMobileMenu() {
        const btn    = document.getElementById('mobile-menu-btn');
        const drawer = document.getElementById('mobile-drawer');
        if (!btn || !drawer) return;

        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const isOpen = drawer.style.display !== 'none';
            drawer.style.display = isOpen ? 'none' : 'block';
            btn.setAttribute('aria-expanded', String(!isOpen));
        });

        document.addEventListener('click', (e) => {
            if (!drawer.contains(e.target) && !btn.contains(e.target)) {
                drawer.style.display = 'none';
                btn.setAttribute('aria-expanded', 'false');
            }
        });

        const mobileInput = document.getElementById('mobile-search-input');
        if (mobileInput) {
            mobileInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' && mobileInput.value.trim()) {
                    window.location.href = '/search?q=' + encodeURIComponent(mobileInput.value.trim());
                }
            });
        }
    },

    setupSearch() {
        const wrap     = document.getElementById('search-wrap');
        const input    = document.getElementById('search-input');
        const form     = document.getElementById('search-form');
        const dropdown = document.getElementById('search-dropdown');
        if (!wrap || !input || !dropdown) return;

        let debounceTimer = null;
        let lastQuery     = '';

        const history = this.getSearchHistory();

        const openDropdown = () => {
            dropdown.classList.add('open');
            input.setAttribute('aria-expanded', 'true');
        };

        const closeDropdown = () => {
            dropdown.classList.remove('open');
            input.setAttribute('aria-expanded', 'false');
        };

        const renderHistory = () => {
            const hist = this.getSearchHistory();
            if (!hist.length) { closeDropdown(); return; }
            dropdown.innerHTML = `
                <div class="search-dropdown-section">
                    <div class="search-dropdown-label">Recent</div>
                    ${hist.slice(0, 6).map((q, i) => `
                        <div class="search-history-item">
                            <div class="search-history-query" data-q="${this.esc(q)}">&#128337; ${this.esc(q)}</div>
                            <button data-index="${i}" aria-label="Remove">&#10005;</button>
                        </div>`).join('')}
                </div>`;
            openDropdown();

            dropdown.querySelectorAll('.search-history-query').forEach(el => {
                el.addEventListener('click', () => {
                    input.value = el.dataset.q;
                    closeDropdown();
                    window.location.href = '/search?q=' + encodeURIComponent(el.dataset.q);
                });
            });

            dropdown.querySelectorAll('.search-history-item button').forEach(btn => {
                btn.addEventListener('click', () => {
                    this.removeSearchHistory(parseInt(btn.dataset.index));
                    renderHistory();
                });
            });
        };

        const renderResults = (results, query) => {
            if (!results.length) { closeDropdown(); return; }
            dropdown.innerHTML = `
                <div class="search-dropdown-section">
                    <div class="search-dropdown-label">Results</div>
                    ${results.slice(0, 7).map(item => {
                        const type  = item.media_type === 'tv' ? 'Series' : 'Movie';
                        const year  = item.year || '';
                        const label = [type, year].filter(Boolean).join(' · ');
                        return `
                            <a class="search-dropdown-item" href="/detail?id=${item.id}&type=${item.media_type}">
                                <img src="${this.esc(item.poster_url)}" alt="" loading="lazy"
                                     onerror="this.src='/assets/img/placeholder-poster.svg'">
                                <div class="search-dropdown-item-info">
                                    <div class="search-dropdown-item-title">${this.esc(item.title)}</div>
                                    <div class="search-dropdown-item-meta">${label}</div>
                                </div>
                            </a>`;
                    }).join('')}
                    <a class="search-dropdown-item" href="/search?q=${encodeURIComponent(query)}"
                       style="color:var(--c-accent);font-size:0.8rem;padding:0.5rem 0.9rem;">
                        See all results for &ldquo;${this.esc(query)}&rdquo; &rarr;
                    </a>
                </div>`;
            openDropdown();
        };

        input.addEventListener('focus', () => {
            if (!input.value.trim()) renderHistory();
        });

        input.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            const q = input.value.trim();
            if (!q) { renderHistory(); return; }
            if (q === lastQuery) return;
            lastQuery = q;
            debounceTimer = setTimeout(async () => {
                try {
                    const data = await Api.get('/search.php?q=' + encodeURIComponent(q) + '&page=1');
                    renderResults(data.results || [], q);
                } catch {}
            }, 280);
        });

        document.addEventListener('click', (e) => {
            if (!wrap.contains(e.target)) closeDropdown();
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') { closeDropdown(); input.blur(); }
        });

        if (form) {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                const q = input.value.trim();
                if (!q) return;
                this.addSearchHistory(q);
                closeDropdown();
                window.location.href = '/search?q=' + encodeURIComponent(q);
            });
        }
    },

    getSearchHistory() {
        try { return JSON.parse(localStorage.getItem('ss_search_history') || '[]'); }
        catch { return []; }
    },

    addSearchHistory(q) {
        const hist = this.getSearchHistory().filter(h => h !== q);
        hist.unshift(q);
        localStorage.setItem('ss_search_history', JSON.stringify(hist.slice(0, 20)));
    },

    removeSearchHistory(index) {
        const hist = this.getSearchHistory();
        hist.splice(index, 1);
        localStorage.setItem('ss_search_history', JSON.stringify(hist));
    },

    esc(text) {
        const d = document.createElement('div');
        d.textContent = text ?? '';
        return d.innerHTML;
    },

    syncSettingsUI() {
        const prefs = this.getPrefs();

        const themePicker = document.getElementById('theme-picker');
        if (themePicker) {
            themePicker.querySelectorAll('.theme-swatch').forEach(sw => {
                sw.classList.toggle('active', (sw.dataset.theme || '') === (prefs.theme || ''));
            });
        }

        const fontPicker = document.getElementById('font-picker');
        if (fontPicker) fontPicker.value = prefs.font || '';

        const defaultServer = document.getElementById('default-server-picker');
        if (defaultServer) defaultServer.value = String(prefs.defaultServer || 0);

        const toggleAdult   = document.getElementById('toggle-adult');
        const toggleResume  = document.getElementById('toggle-resume');
        const toggleAutoplay = document.getElementById('toggle-autoplay');
        if (toggleAdult)    toggleAdult.checked    = !!prefs.showAdult;
        if (toggleResume)   toggleResume.checked   = prefs.rememberPos !== false;
        if (toggleAutoplay) toggleAutoplay.checked = prefs.autoplay !== false;
    },

    setupSettings() {
        const themePicker    = document.getElementById('theme-picker');
        const fontPicker     = document.getElementById('font-picker');
        const defaultServer  = document.getElementById('default-server-picker');
        const toggleAdult    = document.getElementById('toggle-adult');
        const toggleResume   = document.getElementById('toggle-resume');
        const toggleAutoplay = document.getElementById('toggle-autoplay');

        if (themePicker) {
            themePicker.querySelectorAll('.theme-swatch').forEach(sw => {
                sw.addEventListener('click', () => {
                    const theme = sw.dataset.theme || '';
                    document.documentElement.dataset.theme = theme;
                    this.setPrefs({ theme });
                    themePicker.querySelectorAll('.theme-swatch').forEach(s => {
                        s.classList.toggle('active', s.dataset.theme === sw.dataset.theme);
                    });
                });
            });
        }

        if (fontPicker) {
            fontPicker.addEventListener('change', () => {
                const font = fontPicker.value;
                document.documentElement.dataset.font = font;
                this.setPrefs({ font });
            });
        }

        if (defaultServer) {
            defaultServer.addEventListener('change', () => {
                this.setPrefs({ defaultServer: parseInt(defaultServer.value) });
            });
        }

        if (toggleAdult)    toggleAdult.addEventListener('change',    () => this.setPrefs({ showAdult:    toggleAdult.checked }));
        if (toggleResume)   toggleResume.addEventListener('change',   () => this.setPrefs({ rememberPos:  toggleResume.checked }));
        if (toggleAutoplay) toggleAutoplay.addEventListener('change', () => this.setPrefs({ autoplay:     toggleAutoplay.checked }));
    },

    setupAuth() {
        const tabs          = document.querySelectorAll('.auth-tab');
        const loginForm     = document.getElementById('login-form');
        const registerForm  = document.getElementById('register-form');
        const loginError    = document.getElementById('login-error');
        const registerError = document.getElementById('register-error');
        const pwInput       = document.getElementById('reg-password');
        const pwFill        = document.getElementById('pw-strength-fill');
        const pwLabel       = document.getElementById('pw-strength-label');
        const avatarBtn     = document.getElementById('avatar-upload-btn');
        const avatarInput   = document.getElementById('avatar-input');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                tabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                const isLogin = tab.dataset.tab === 'login';
                if (loginForm)    loginForm.classList.toggle('hidden', !isLogin);
                if (registerForm) registerForm.classList.toggle('hidden', isLogin);
            });
        });

        if (pwInput && pwFill && pwLabel) {
            pwInput.addEventListener('input', () => {
                const strength = this.passwordStrength(pwInput.value);
                const colors   = ['#e05c5c', '#f59e0b', '#60b8f0', '#5ce08a'];
                const labels   = ['Too short', 'Weak', 'Fair', 'Strong'];
                const score    = Math.min(3, Math.max(0, strength));
                pwFill.style.width      = ((score + 1) * 25) + '%';
                pwFill.style.background = colors[score];
                pwLabel.textContent     = pwInput.value.length < 8 ? 'Min 8 characters' : labels[score];
            });
        }

        if (avatarBtn && avatarInput) {
            avatarBtn.addEventListener('click', () => avatarInput.click());
            avatarInput.addEventListener('change', () => {
                const file = avatarInput.files[0];
                if (!file) return;
                if (file.size > 1048576) {
                    Toast.show('Avatar must be under 1 MB', 'error');
                    avatarInput.value = '';
                    return;
                }
                const reader = new FileReader();
                reader.onload = (e) => {
                    avatarBtn.innerHTML = `<img src="${e.target.result}" alt="Avatar preview">`;
                };
                reader.readAsDataURL(file);
            });
        }

        if (loginForm) {
            loginForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const email    = document.getElementById('login-email').value.trim();
                const password = document.getElementById('login-password').value;
                if (!email || !password) return;
                const btn = loginForm.querySelector('button[type=submit]');
                btn.disabled = true;
                btn.textContent = 'Signing in…';
                try {
                    const data = await Api.post('/auth.php?action=login', { email, password });
                    if (data.token) {
                        localStorage.setItem('ss_token', data.token);
                        localStorage.setItem('ss_user', JSON.stringify(data.user));
                        Toast.show('Welcome back, ' + (data.user.display_name || 'there') + '!');
                        document.getElementById('account-panel').classList.remove('open');
                        document.getElementById('overlay-backdrop').classList.remove('open');
                        this.updateAccountUI(data.user);
                    }
                } catch (err) {
                    if (loginError) { loginError.textContent = err.message || 'Login failed'; loginError.style.display = 'block'; }
                } finally {
                    btn.disabled = false; btn.textContent = 'Sign In';
                }
            });
        }

        if (registerForm) {
            registerForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const name     = document.getElementById('reg-name').value.trim();
                const email    = document.getElementById('reg-email').value.trim();
                const password = document.getElementById('reg-password').value;
                if (!name || !email || password.length < 8) {
                    if (registerError) { registerError.textContent = 'Fill all fields. Password min 8 chars.'; registerError.style.display = 'block'; }
                    return;
                }
                const btn = registerForm.querySelector('button[type=submit]');
                btn.disabled = true; btn.textContent = 'Creating…';
                try {
                    const data = await Api.post('/auth.php?action=register', { display_name: name, email, password });
                    if (data.token) {
                        localStorage.setItem('ss_token', data.token);
                        localStorage.setItem('ss_user', JSON.stringify(data.user));
                        Toast.show('Account created! Welcome, ' + name + '!');
                        document.getElementById('account-panel').classList.remove('open');
                        document.getElementById('overlay-backdrop').classList.remove('open');
                        this.updateAccountUI(data.user);
                    }
                } catch (err) {
                    if (registerError) { registerError.textContent = err.message || 'Registration failed'; registerError.style.display = 'block'; }
                } finally {
                    btn.disabled = false; btn.textContent = 'Create Account';
                }
            });
        }

        const stored = localStorage.getItem('ss_user');
        if (stored) {
            try { this.updateAccountUI(JSON.parse(stored)); } catch {}
        }
    },

    updateAccountUI(user) {
        const btn = document.getElementById('account-btn');
        if (btn && user) {
            const initials = (user.display_name || 'U').charAt(0).toUpperCase();
            btn.innerHTML = `<span style="font-size:0.75rem;font-weight:700;">${initials}</span>`;
            btn.classList.add('active');
        }
    },

    passwordStrength(pw) {
        if (pw.length < 8) return 0;
        let score = 1;
        if (/[A-Z]/.test(pw) && /[a-z]/.test(pw)) score++;
        if (/\d/.test(pw)) score++;
        if (/[^A-Za-z0-9]/.test(pw)) score++;
        return Math.min(3, score - 1);
    },

    updateNavState() {
        const path = window.location.pathname.replace(/\/$/, '') || '/';
        document.querySelectorAll('.nav-link').forEach(link => {
            const href    = link.getAttribute('href') || '';
            const isActive = href === path || (href !== '/' && path.startsWith(href));
            link.classList.toggle('active', isActive);
        });
    },
};

document.addEventListener('DOMContentLoaded', () => App.init());
