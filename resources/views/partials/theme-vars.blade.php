@php
    $_theme = \App\Models\Setting::instance();
    $_hex   = ltrim($_theme->primary_color ?? '#FFB162', '#');
    $_r     = hexdec(substr($_hex, 0, 2));
    $_g     = hexdec(substr($_hex, 2, 2));
    $_b     = hexdec(substr($_hex, 4, 2));
@endphp
<style>
    :root {
        --primary-color:    {{ $_theme->primary_color    ?? '#FFB162' }};
        --secondary-color:  {{ $_theme->secondary_color  ?? '#A35139' }};
        --background-color: {{ $_theme->background_color ?? '#EEE9DF' }};
        --primary-glow:     rgba({{ $_r }}, {{ $_g }}, {{ $_b }}, 0.12);
        --color-ink:        #1B2632;
        --color-muted:      #2C3B4D;
        --card-surface:     #C9C1B1;
    }
    html.dark {
        --background-color: #111A24;
        --primary-glow:     rgba({{ $_r }}, {{ $_g }}, {{ $_b }}, 0.10);
        --color-ink:        #EEE9DF;
        --color-muted:      #9E9480;
        --card-surface:     #1B2632;
    }
</style>
