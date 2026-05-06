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

const Api = {
    baseUrl: '/api',

    getGuestToken() {
        let token = localStorage.getItem('ss_guest');
        if (!token) {
            token = crypto.randomUUID();
            localStorage.setItem('ss_guest', token);
        }
        return token;
    },

    getAuthToken() {
        return localStorage.getItem('ss_auth_token');
    },

    async request(endpoint, options = {}) {
        const url     = this.baseUrl + endpoint;
        const headers = {
            'Content-Type':   'application/json',
            'X-Guest-Token':  this.getGuestToken(),
            'X-Content-Lang': (function() {
                try { return JSON.parse(localStorage.getItem('ss_prefs') || '{}').contentLang || 'en-US'; }
                catch { return 'en-US'; }
            })(),
            ...options.headers,
        };

        const authToken = this.getAuthToken();
        if (authToken) headers['Authorization'] = `Bearer ${authToken}`;

        try {
            const response = await fetch(url, { ...options, headers });

            const contentType = response.headers.get('content-type') || '';
            if (!contentType.includes('application/json')) {
                throw new Error(`Server error (HTTP ${response.status}) — expected JSON but got ${contentType.split(';')[0]}`);
            }

            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(data.error || `HTTP ${response.status}`);
            }

            return data.data;
        } catch (error) {
            if (error.name !== 'TypeError') {
                console.error('API Error:', error.message || error);
            }
            throw error;
        }
    },

    get(endpoint) {
        return this.request(endpoint, { method: 'GET' });
    },

    post(endpoint, body) {
        return this.request(endpoint, {
            method: 'POST',
            body:   JSON.stringify(body),
        });
    },

    delete(endpoint, body) {
        return this.request(endpoint, {
            method: 'DELETE',
            body:   body ? JSON.stringify(body) : undefined,
        });
    },
};
