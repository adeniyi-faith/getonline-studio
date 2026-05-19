<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class AI_Chat_Installer {
    public static function activate() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $tables = [];

        $tables[] = "CREATE TABLE {$wpdb->prefix}ai_chat_conversations (
            id BIGINT NOT NULL AUTO_INCREMENT,
            user_id BIGINT NULL,
            session_id VARCHAR(128) NULL,
            started_at DATETIME NULL,
            last_activity DATETIME NULL,
            meta LONGTEXT NULL,
            PRIMARY KEY (id)
        ) {$charset_collate};";

        $tables[] = "CREATE TABLE {$wpdb->prefix}ai_chat_messages (
            id BIGINT NOT NULL AUTO_INCREMENT,
            conversation_id BIGINT NULL,
            role VARCHAR(20) NULL,
            content LONGTEXT NULL,
            created_at DATETIME NULL,
            rating TINYINT NULL,
            PRIMARY KEY (id)
        ) {$charset_collate};";

        $tables[] = "CREATE TABLE {$wpdb->prefix}ai_chat_kb (
            id BIGINT NOT NULL AUTO_INCREMENT,
            title VARCHAR(255) NULL,
            content LONGTEXT NULL,
            source_type VARCHAR(50) NULL,
            source_meta LONGTEXT NULL,
            created_at DATETIME NULL,
            PRIMARY KEY (id)
        ) {$charset_collate};";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        foreach($tables as $sql) {
            dbDelta( $sql );
        }

        // Default options
        $defaults = [
            'ai_chat_api_key' => '',
            'ai_chat_provider' => 'openai',
            'ai_chat_widget_mode' => 'floating',
            'ai_chat_primary_color' => '#0a84ff',
            'ai_chat_position' => 'bottom-right',
            'ai_chat_welcome' => 'Hi! How can I help you today?',
            'ai_chat_escalation_email' => get_option('admin_email'),
            'ai_chat_retention_days' => 365
        ];
        foreach($defaults as $k => $v) {
            add_option($k, $v);
        }
    }

    public static function deactivate() {
        // Nothing destructive on deactivate. Admin can remove data via settings later.
    }
}
