{{-- ══════════════════════════════════════════
  SEO Assistant — sidebar panel HTML
  Requires seo-assistant-script partial for JS
═══════════════════════════════════════════ --}}
<div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel space-y-4" id="sea-panel">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            <svg class="h-4 w-4 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" d="m21 21-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0Z"/>
            </svg>
            <h2 class="font-display text-base text-brand-ink">SEO Assistant</h2>
        </div>
        <span id="sea-badge" class="rounded-full bg-warm-200 px-2.5 py-1 text-xs font-bold text-stone-400">—</span>
    </div>

    {{-- Score bar --}}
    <div>
        <div class="relative h-2.5 w-full overflow-hidden rounded-full bg-warm-200">
            <div id="sea-bar"
                 class="absolute inset-y-0 left-0 w-0 rounded-full bg-stone-300 transition-all duration-500">
            </div>
        </div>
        <div class="mt-1.5 flex items-center justify-between">
            <span id="sea-label" class="text-xs text-brand-muted">Start writing to get suggestions</span>
            <span id="sea-score" class="text-xs font-bold text-brand-ink"></span>
        </div>
    </div>

    {{-- Focus keyword input --}}
    <div>
        <label for="sea-keyword" class="block text-xs font-semibold text-brand-muted">
            Focus Keyword
            <span class="ml-1 font-normal text-brand-muted/60">(optional)</span>
        </label>
        <input type="text"
               id="sea-keyword"
               placeholder="e.g. freelance design"
               autocomplete="off"
               class="mt-1.5 w-full rounded-xl border border-warm-300/50 bg-warm-200/50 px-3 py-2 text-xs text-brand-ink placeholder-stone-400 transition
                      focus:border-brand-primary focus:bg-warm-100 focus:outline-none focus:ring-1 focus:ring-brand-primary/30">
    </div>

    {{-- Suggestions list --}}
    <div id="sea-suggestions" class="space-y-1.5 empty:hidden">
        {{-- Populated by JS --}}
    </div>

</div>
