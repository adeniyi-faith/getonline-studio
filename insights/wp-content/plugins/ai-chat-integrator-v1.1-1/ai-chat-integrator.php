<?php
/**
 * Plugin Name: AI Chat Integrator
 * Description: AI-powered chatbot with multi-provider fallback, mobile-optimized UI, analytics, KB import and more.
 * Version: 1.1
 * Author: Faith Adeniyi
 * Text Domain: ai-chat-integrator
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define('AI_CHAT_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('AI_CHAT_PLUGIN_URL', plugin_dir_url(__FILE__));
define('AI_CHAT_VERSION', '1.1');

require_once AI_CHAT_PLUGIN_DIR . 'includes/installer.php';
require_once AI_CHAT_PLUGIN_DIR . 'includes/admin.php';
require_once AI_CHAT_PLUGIN_DIR . 'includes/rest.php';
require_once AI_CHAT_PLUGIN_DIR . 'includes/frontend.php';
require_once AI_CHAT_PLUGIN_DIR . 'includes/analytics.php';

register_activation_hook(__FILE__, ['AI_Chat_Installer', 'activate']);
register_deactivation_hook(__FILE__, ['AI_Chat_Installer', 'deactivate']);

add_action('plugins_loaded', function(){
    AI_Chat_Admin::init();
    AI_Chat_REST::init();
    AI_Chat_Frontend::init();
    AI_Chat_Analytics::init();
});
