<?php

/**
 * Route the application through public/index.php so the site
 * can be served from the domain root (twncolors.com) without
 * requiring /public/ in the URL.
 *
 * The root .htaccess forwards dynamic requests here; static
 * assets (CSS, JS, images) are served directly from public/ by
 * the same .htaccess rewrite rules.
 */

require __DIR__ . '/public/index.php';
