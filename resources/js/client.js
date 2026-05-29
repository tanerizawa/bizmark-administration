/**
 * Client Portal JavaScript Entry Point
 * Bizmark.ID — Portal Klien
 *
 * Stack:
 * - Alpine.js v3.15.1 (replaces CDN in client layout)
 * - @alpinejs/collapse (accordion, expandable sections)
 * - Font Awesome v7 (imported via client.css)
 */

// Alpine.js core + plugins
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import focus from '@alpinejs/focus';

// Register plugins
Alpine.plugin(collapse);
Alpine.plugin(focus);

// ── Alpine Stores ──────────────────────────────────────────────────
/**
 * Theme store — persists dark/light preference in localStorage.
 * Usage in Blade: x-bind:data-theme="$store.theme.current"
 *                 @click="$store.theme.toggle()"
 */
Alpine.store('theme', {
    current: localStorage.getItem('client-theme') ||
             (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'),
    toggle() {
        this.current = this.current === 'dark' ? 'light' : 'dark';
        localStorage.setItem('client-theme', this.current);
        document.documentElement.setAttribute('data-theme', this.current);
    },
    init() {
        document.documentElement.setAttribute('data-theme', this.current);
        // Listen for OS preference changes
        window.matchMedia('(prefers-color-scheme: dark)')
              .addEventListener('change', (e) => {
                  if (!localStorage.getItem('client-theme')) {
                      this.current = e.matches ? 'dark' : 'light';
                      document.documentElement.setAttribute('data-theme', this.current);
                  }
              });
    },
});

// ── Global helpers ─────────────────────────────────────────────────

/**
 * Fix viewport height for mobile browsers (CSS --vh variable).
 */
function setVh() {
    document.documentElement.style.setProperty('--vh', `${window.innerHeight * 0.01}px`);
}
setVh();
window.addEventListener('resize', setVh, { passive: true });
window.addEventListener('orientationchange', setVh, { passive: true });

/**
 * API fetch with CSRF token and standardised error handling.
 */
window.apiFetch = async function (url, options = {}) {
    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    const defaults = {
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': token || '',
            'X-Requested-With': 'XMLHttpRequest',
        },
    };
    const config = { ...defaults, ...options };
    if (options.headers) {
        config.headers = { ...defaults.headers, ...options.headers };
    }
    const response = await fetch(url, config);
    if (!response.ok) {
        const text = await response.text();
        let message = `HTTP ${response.status}`;
        try {
            const json = JSON.parse(text);
            message = json.message || json.error || message;
        } catch (_) { /* ignore */ }
        throw new Error(message);
    }
    return response.json();
};

// ── Alpine Components ──────────────────────────────────────────────

/**
 * KBLI search component (used in services/index.blade.php).
 */
Alpine.data('kbliSearch', () => ({
    query: '',
    results: [],
    loading: false,
    focused: false,
    noResults: false,
    errorMsg: '',
    async search() {
        this.noResults = false;
        this.errorMsg = '';
        if (this.query.trim().length < 3) {
            this.results = [];
            return;
        }
        this.loading = true;
        try {
            const data = await window.apiFetch(
                `/api/kbli/search?q=${encodeURIComponent(this.query.trim())}`
            );
            this.results = data.data ?? data ?? [];
            this.noResults = this.results.length === 0;
        } catch (err) {
            this.errorMsg = 'Gagal memuat hasil. Coba lagi.';
            this.results = [];
        } finally {
            this.loading = false;
        }
    },
}));

/**
 * Command palette — keyboard-driven launcher (⌘K / Ctrl+K / /).
 * Used by <x-ui.command-palette> in client layout.
 */
Alpine.data('portalCmdk', (initialCommands = []) => ({
    isOpen: false,
    isMac: /Mac|iPhone|iPad/.test(navigator.platform || ''),
    query: '',
    cursor: 0,
    commands: initialCommands,
    liveResults: [],
    liveLoading: false,
    _searchTimer: null,
    _searchRequestId: 0,

    init() {
        // Allow runtime extensions (e.g. per-page commands)
        if (Array.isArray(window.portalCommands)) {
            this.commands = [...this.commands, ...window.portalCommands];
        }
    },

    open() {
        this.isOpen = true;
        this.query = '';
        this.cursor = 0;
        this.liveResults = [];
        this.$nextTick(() => this.$refs.search?.focus());
    },
    close() {
        this.isOpen = false;
        clearTimeout(this._searchTimer);
    },

    onQueryInput() {
        this.cursor = 0;
        clearTimeout(this._searchTimer);
        const q = this.query.trim();
        if (q.length < 2) {
            this.liveResults = [];
            this.liveLoading = false;
            return;
        }
        this.liveLoading = true;
        const requestId = ++this._searchRequestId;
        this._searchTimer = setTimeout(async () => {
            try {
                const res = await fetch('/client/search?q=' + encodeURIComponent(q), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin',
                });
                if (!res.ok) {
                    throw new Error('Search request failed');
                }
                const data = await res.json();
                if (requestId === this._searchRequestId) {
                    this.liveResults = data.results || [];
                }
            } catch (e) {
                if (requestId === this._searchRequestId) {
                    this.liveResults = [];
                }
            } finally {
                if (requestId === this._searchRequestId) {
                    this.liveLoading = false;
                }
            }
        }, 300);
    },

    get filtered() {
        const q = this.query.trim().toLowerCase();
        // If live results exist, merge them with static commands (live results first)
        if (this.liveResults.length > 0) {
            const staticFiltered = this.commands.filter(c =>
                c.label.toLowerCase().includes(q) ||
                (c.group || '').toLowerCase().includes(q) ||
                (c.shortcut || '').toLowerCase().includes(q)
            );
            return [...this.liveResults, ...staticFiltered];
        }
        if (!q) return this.commands;
        return this.commands.filter(c =>
            c.label.toLowerCase().includes(q) ||
            (c.group || '').toLowerCase().includes(q) ||
            (c.shortcut || '').toLowerCase().includes(q)
        );
    },

    get groupedFiltered() {
        const groups = {};
        this.filtered.forEach(c => {
            const g = c.group || 'Lainnya';
            (groups[g] = groups[g] || []).push(c);
        });
        return Object.keys(groups).map(name => ({ name, items: groups[name] }));
    },

    globalIndex(item) {
        return this.filtered.findIndex(c => c.id === item.id);
    },

    moveCursor(delta) {
        const max = this.filtered.length;
        if (max === 0) return;
        this.cursor = (this.cursor + delta + max) % max;
        // Scroll into view
        this.$nextTick(() => {
            const selected = this.$el.querySelector('[aria-selected="true"]');
            selected?.scrollIntoView({ block: 'nearest' });
        });
    },

    execute() {
        const item = this.filtered[this.cursor];
        if (item) this.executeItem(item);
    },

    executeItem(item) {
        this.close();
        if (item.action === 'theme') {
            Alpine.store('theme').toggle();
            return;
        }
        if (item.action === 'logout') {
            const form = document.getElementById('client-logout-form');
            if (form) form.submit();
            return;
        }
        if (item.url) {
            window.location.href = item.url;
        }
    },
}));

// ── Global keyboard shortcuts ──────────────────────────────────────
/**
 * Two-key sequences (g d → dashboard, g a → applications, etc.)
 * and single-key shortcuts (c → create, ? → cheatsheet).
 */
(() => {
    let pendingG = false;
    let pendingTimer = null;

    const isTypingTarget = (el) =>
        el && (el.matches('input, textarea, select, [contenteditable]') ||
               el.closest('[role="dialog"]'));

    const goto = (path) => { window.location.href = path; };

    document.addEventListener('keydown', (e) => {
        if (isTypingTarget(e.target)) return;
        if (e.metaKey || e.ctrlKey || e.altKey) return;

        // Sequence: g <key>
        if (pendingG) {
            pendingG = false;
            clearTimeout(pendingTimer);
            const map = {
                d: '/client/dashboard',
                a: '/client/applications',
                p: '/client/projects',
                s: '/client/services',
                v: '/client/vault',
                n: '/client/notifications',
            };
            if (map[e.key]) {
                e.preventDefault();
                goto(map[e.key]);
            }
            return;
        }

        if (e.key === 'g') {
            pendingG = true;
            pendingTimer = setTimeout(() => { pendingG = false; }, 1000);
            return;
        }

        if (e.key === 'c') {
            e.preventDefault();
            goto('/client/applications/create');
        }

        if (e.key === '?') {
            e.preventDefault();
            window.dispatchEvent(new CustomEvent('cmdk-open'));
        }
    });
})();

// ── Start Alpine ───────────────────────────────────────────────────
window.Alpine = Alpine;
Alpine.start();
