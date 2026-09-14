<?php
/**
 * Shared boot + admin-only gate for every screen under /wp/admin/.
 * Mirrors the same pattern as /wp/studio-admin.php so both admin
 * areas behave identically and share one login.
 */

define('WP_USE_THEMES', false);
require_once __DIR__ . '/../wp-load.php';

if (!is_user_logged_in() || !current_user_can('manage_options')) {
    wp_redirect('/wp/u-login.php?redirect_to=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}
