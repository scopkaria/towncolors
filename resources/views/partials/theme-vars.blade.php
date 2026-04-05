@php
    $_theme = \App\Models\Setting::instance();
    $_hex   = ltrim($_theme->primary_color ?? '#F97316', '#');
    $_r     = hexdec(substr($_hex, 0, 2));
    $_g     = hexdec(substr($_hex, 2, 2));
    $_b     = hexdec(substr($_hex, 4, 2));
@endphp
<style>
    :root {
        --primary-color:    {{ $_theme->primary_color    ?? '#F97316' }};
        --secondary-color:  {{ $_theme->secondary_color  ?? '#EA580C' }};
        --background-color: {{ $_theme->background_color ?? '#FFFFFF' }};
        --primary-glow:     rgba({{ $_r }}, {{ $_g }}, {{ $_b }}, 0.15);
        --color-ink:        #0F172A;
        --color-muted:      #71717A;
    }
    html.dark {
        --background-color: #000000;
        --primary-glow:     rgba({{ $_r }}, {{ $_g }}, {{ $_b }}, 0.20);
        --color-ink:        #FFFFFF;
        --color-muted:      #A1A1AA;
    }
</style>
