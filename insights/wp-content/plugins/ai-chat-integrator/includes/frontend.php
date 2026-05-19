<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class AI_Chat_Frontend {
    public static function init() {
        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue']);
        add_shortcode('ai_chat_widget', [__CLASS__, 'shortcode']);
        add_action('wp_footer', [__CLASS__, 'maybe_render_floating']);
    }

    public static function enqueue() {
        wp_register_style('ai-chat-frontend', plugin_dir_url(__FILE__) . '../assets/frontend.css', [], AI_CHAT_VERSION);
        wp_enqueue_style('ai-chat-frontend');
        wp_register_script('ai-chat-frontend', plugin_dir_url(__FILE__) . '../assets/chat-widget.js', ['jquery'], AI_CHAT_VERSION, true);
        wp_enqueue_script('ai-chat-frontend');
        wp_localize_script('ai-chat-frontend', 'AI_CHAT', [
            'ajax_url' => esc_url(rest_url('ai-chat/v1/message')),
            'nonce' => wp_create_nonce('wp_rest'),
            'welcome' => get_option('ai_chat_welcome'),
            'primary_color' => get_option('ai_chat_primary_color'),
            'position' => get_option('ai_chat_position'),
            'escalation_nonce' => wp_create_nonce('ai_chat_admin'),
        ]);
    }

    public static function shortcode($atts) {
        $atts = shortcode_atts(['mode'=>'inline'], $atts);
        return '<div id="ai-chat-root"></div><script>document.addEventListener("DOMContentLoaded", function(){ document.getElementById("ai-chat-root").classList.add("ai-inline"); });</script>';
    }

    public static function maybe_render_floating() {
        $mode = get_option('ai_chat_widget_mode','floating');
        if($mode !== 'floating') return;
        echo '<div id="ai-chat-root"></div>';
    }
}
