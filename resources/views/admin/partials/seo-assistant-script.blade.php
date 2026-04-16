<script>
/**
 * SEO Assistant — rule-based analysis
 * Requires: #title, #meta_description, #sea-keyword, #sea-panel elements
 * Integrates with Quill editor if `quill` variable exists in outer scope.
 *
 * Scoring (100 pts total):
 *   Without keyword → title 50 + meta 50
 *   With keyword    → title 25 + meta 25 + kw-in-title 20 + kw-in-content 20 + kw-in-meta 10
 */
(function () {
    'use strict';

    /* ── Guard ────────────────────────────────────────── */
    const panel = document.getElementById('sea-panel');
    if (!panel) return;

    /* ── Element refs ─────────────────────────────────── */
    const titleEl    = document.getElementById('title');
    const metaDescEl = document.getElementById('meta_description');
    const keywordEl  = document.getElementById('sea-keyword');
    const bar        = document.getElementById('sea-bar');
    const badge      = document.getElementById('sea-badge');
    const scoreEl    = document.getElementById('sea-score');
    const labelEl    = document.getElementById('sea-label');
    const suggestEl  = document.getElementById('sea-suggestions');

    /* ── Icon templates ───────────────────────────────── */
    const ICONS = {
        pass: '<svg class="mt-px h-3.5 w-3.5 flex-shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" d="m4.5 12.75 6 6 9-13.5"/></svg>',
        warn: '<svg class="mt-px h-3.5 w-3.5 flex-shrink-0 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>',
        fail: '<svg class="mt-px h-3.5 w-3.5 flex-shrink-0 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M6 18 18 6M6 6l12 12"/></svg>',
    };

    const ROW_BG  = { pass: 'bg-emerald-50/70', warn: 'bg-amber-50/70',  fail: 'bg-red-50/70'   };
    const ROW_TXT = { pass: 'text-emerald-700',  warn: 'text-amber-700',  fail: 'text-red-600'  };

    /* ── Get plain text from Quill or hidden textarea ─── */
    function getContentText() {
        try {
            if (typeof quill !== 'undefined') return quill.getText().toLowerCase();
        } catch (_) {}
        return (document.getElementById('content')?.value || '').toLowerCase();
    }

    /* ── Core analysis ──────────────────────────────────*/
    function analyze() {
        const title   = (titleEl?.value   || '').trim();
        const meta    = (metaDescEl?.value || '').trim();
        const keyword = (keywordEl?.value  || '').trim().toLowerCase();
        const content = getContentText();

        const hasKw = keyword.length > 0;
        const items = [];
        let   score = 0;

        /* — Title (25 pts with kw, 50 pts without) — */
        const tW   = hasKw ? 25 : 50;
        const tLen = title.length;

        if (tLen === 0) {
            items.push({ type: 'fail', msg: 'Title is missing' });
        } else if (tLen < 30) {
            items.push({ type: 'fail', msg: `Title too short — ${tLen} chars (aim for 50–60)` });
        } else if (tLen < 50) {
            score += Math.round(tW * 0.5);
            items.push({ type: 'warn', msg: `Title is ${tLen} chars — aim for 50–60` });
        } else if (tLen <= 60) {
            score += tW;
            items.push({ type: 'pass', msg: `Title length is good (${tLen} chars)` });
        } else if (tLen <= 70) {
            score += Math.round(tW * 0.6);
            items.push({ type: 'warn', msg: `Title slightly long — ${tLen} chars (keep under 60)` });
        } else {
            items.push({ type: 'fail', msg: `Title too long — ${tLen} chars (keep under 60)` });
        }

        /* — Meta description (25 pts with kw, 50 pts without) — */
        const mW   = hasKw ? 25 : 50;
        const mLen = meta.length;

        if (mLen === 0) {
            items.push({ type: 'fail', msg: 'Meta description is missing' });
        } else if (mLen < 80) {
            items.push({ type: 'fail', msg: `Meta description too short — ${mLen} chars (aim for 120–160)` });
        } else if (mLen < 120) {
            score += Math.round(mW * 0.5);
            items.push({ type: 'warn', msg: `Meta description is ${mLen} chars — aim for 120–160` });
        } else if (mLen <= 160) {
            score += mW;
            items.push({ type: 'pass', msg: `Meta description length is good (${mLen} chars)` });
        } else if (mLen <= 200) {
            score += Math.round(mW * 0.6);
            items.push({ type: 'warn', msg: `Meta description slightly long — ${mLen} chars (keep under 160)` });
        } else {
            items.push({ type: 'fail', msg: `Meta description too long — ${mLen} chars (keep under 160)` });
        }

        /* — Keyword checks (only when keyword entered) — */
        if (hasKw) {
            if (title.toLowerCase().includes(keyword)) {
                score += 20;
                items.push({ type: 'pass', msg: `Keyword "${keyword}" found in title` });
            } else {
                items.push({ type: 'fail', msg: `Keyword "${keyword}" missing from title` });
            }

            if (content.includes(keyword)) {
                score += 20;
                items.push({ type: 'pass', msg: `Keyword "${keyword}" found in content` });
            } else {
                items.push({ type: 'fail', msg: `Keyword "${keyword}" missing from content` });
            }

            if (meta.toLowerCase().includes(keyword)) {
                score += 10;
                items.push({ type: 'pass', msg: `Keyword "${keyword}" found in meta description` });
            } else {
                items.push({ type: 'warn', msg: `Keyword "${keyword}" missing from meta description` });
            }
        }

        renderScore(Math.min(100, Math.max(0, score)));
        renderSuggestions(items);
    }

    /* ── Render score ───────────────────────────────── */
    function renderScore(s) {
        scoreEl.textContent = s;
        bar.style.width     = s + '%';

        let barCls, badgeCls, label;

        if (s < 40) {
            barCls   = 'absolute inset-y-0 left-0 rounded-full bg-red-500 transition-all duration-500';
            badgeCls = 'rounded-full bg-red-50 px-2.5 py-1 text-xs font-bold text-red-600';
            label    = 'Needs Work';
        } else if (s < 70) {
            barCls   = 'absolute inset-y-0 left-0 rounded-full bg-amber-400 transition-all duration-500';
            badgeCls = 'rounded-full bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-600';
            label    = 'Could Be Better';
        } else if (s < 90) {
            barCls   = 'absolute inset-y-0 left-0 rounded-full bg-brand-primary transition-all duration-500';
            badgeCls = 'rounded-full bg-accent-light px-2.5 py-1 text-xs font-bold text-brand-primary';
            label    = 'Good';
        } else {
            barCls   = 'absolute inset-y-0 left-0 rounded-full bg-emerald-500 transition-all duration-500';
            badgeCls = 'rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-600';
            label    = 'Excellent';
        }

        bar.className   = barCls;
        badge.className = badgeCls;
        badge.textContent  = s;
        labelEl.textContent = label;
    }

    /* ── Render suggestions list ────────────────────── */
    function renderSuggestions(items) {
        suggestEl.innerHTML = items.map(function (it) {
            return (
                '<div class="flex items-start gap-2 rounded-xl px-3 py-2 ' + ROW_BG[it.type] + '">' +
                    ICONS[it.type] +
                    '<span class="leading-relaxed ' + ROW_TXT[it.type] + '">' + it.msg + '</span>' +
                '</div>'
            );
        }).join('');
    }

    /* ── Debounced trigger ───────────────────────────── */
    let timer;
    function debounced() {
        clearTimeout(timer);
        timer = setTimeout(analyze, 280);
    }

    /* ── Event bindings ─────────────────────────────── */
    [titleEl, metaDescEl, keywordEl].forEach(function (el) {
        if (el) el.addEventListener('input', debounced);
    });

    // Hook into Quill if it's defined in the enclosing scope
    try {
        if (typeof quill !== 'undefined') {
            quill.on('text-change', debounced);
        }
    } catch (_) {}

    // Run initial analysis
    analyze();
}());
</script>
