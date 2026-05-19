<?php
/*
Plugin Name: AI Chat Integrator
Description: AI-powered chatbot with multi-provider fallback (OpenAI, Claude, Gemini, Cohere, Mistral).
Version: 1.1
Author: Faith Adeniyi
*/

if (!defined('ABSPATH')) exit;

// Safe includes
$includes = ['includes/admin.php','includes/frontend.php','includes/rest.php','includes/analytics.php'];
foreach ($includes as $inc) {
    $path = plugin_dir_path(__FILE__) . $inc;
    if (file_exists($path)) {
        require_once $path;
    } else {
        error_log("AI Chat Integrator: Missing include file - $path");
    }
}
