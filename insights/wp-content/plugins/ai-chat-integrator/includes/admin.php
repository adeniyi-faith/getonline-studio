<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class AI_Chat_Admin {
    public static function init() {
        add_action('admin_menu', [__CLASS__, 'admin_menu']);
        add_action('admin_init', [__CLASS__, 'register_settings']);
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_assets']);
        add_action('wp_ajax_ai_chat_import_csv', [__CLASS__, 'ajax_import_csv']);
        add_action('wp_ajax_ai_chat_escalate', [__CLASS__, 'ajax_escalate']);
    }

    public static function admin_menu() {
        add_menu_page('AI Chat', 'AI Chat', 'manage_options', 'ai-chat', [__CLASS__, 'page_dashboard'], 'dashicons-format-chat', 58);
        add_submenu_page('ai-chat', 'Settings', 'Settings', 'manage_options', 'ai-chat-settings', [__CLASS__, 'page_settings']);
        add_submenu_page('ai-chat', 'Widget', 'Widget & Shortcode', 'manage_options', 'ai-chat-widget', [__CLASS__, 'page_widget']);
        add_submenu_page('ai-chat', 'Knowledge Base', 'Knowledge Base', 'manage_options', 'ai-chat-kb', [__CLASS__, 'page_kb']);
        add_submenu_page('ai-chat', 'Conversations', 'Conversations', 'manage_options', 'ai-chat-conversations', [__CLASS__, 'page_conversations']);
        add_submenu_page('ai-chat', 'Analytics', 'Analytics', 'manage_options', 'ai-chat-analytics', [__CLASS__, 'page_analytics']);
    }

    public static function enqueue_assets($hook) {
        if (strpos($hook, 'ai-chat') === false) return;
        wp_enqueue_style('ai-chat-admin', plugin_dir_url(__FILE__) . '../assets/admin.css', [], AI_CHAT_VERSION);
        wp_enqueue_script('ai-chat-admin', plugin_dir_url(__FILE__) . '../assets/admin.js', ['jquery'], AI_CHAT_VERSION, true);
        wp_localize_script('ai-chat-admin', 'AI_CHAT_ADMIN', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ai_chat_admin'),
        ]);
    }

    public static function register_settings() {
        register_setting('ai_chat_settings_group', 'ai_chat_api_key');
        register_setting('ai_chat_settings_group', 'ai_chat_provider');
        register_setting('ai_chat_settings_group', 'ai_chat_widget_mode');
        register_setting('ai_chat_settings_group', 'ai_chat_primary_color');
        register_setting('ai_chat_settings_group', 'ai_chat_position');
        register_setting('ai_chat_settings_group', 'ai_chat_welcome');
        register_setting('ai_chat_settings_group', 'ai_chat_escalation_email');
        register_setting('ai_chat_settings_group', 'ai_chat_retention_days');
    }

    // Dashboard overview
    public static function page_dashboard() {
        if (!current_user_can('manage_options')) wp_die('Unauthorized');
        ?>
        <div class="wrap">
            <h1>AI Chat Dashboard</h1>
            <p>Welcome, <?php echo esc_html(wp_get_current_user()->display_name); ?>. Plugin by Faith Adeniyi.</p>
            <h2>Quick stats</h2>
            <?php
            global $wpdb;
            $total = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}ai_chat_conversations");
            $messages = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}ai_chat_messages");
            echo "<ul><li>Total conversations: <strong>".intval($total)."</strong></li><li>Total messages: <strong>".intval($messages)."</strong></li></ul>";
            ?>
            <p>Use the menu to configure settings, import KB, and view conversations/analytics.</p>
        </div>
        <?php
    }

    public static function page_settings() {
        if (!current_user_can('manage_options')) wp_die('Unauthorized');
        ?>
        <div class="wrap">
            <h1>AI Chat Settings</h1>
            <form method="post" action="options.php">
                <?php settings_fields('ai_chat_settings_group'); do_settings_sections('ai-chat'); ?>
                <table class="form-table">
                    <tr><th>API Key</th><td><input type="password" name="ai_chat_api_key" value="<?php echo esc_attr(get_option('ai_chat_api_key')); ?>" style="width:50%"/></td></tr>
                    <tr><th>Provider</th><td><select name="ai_chat_provider"><option value="openai" <?php selected(get_option('ai_chat_provider'),'openai'); ?>>OpenAI</option></select></td></tr>
                    <tr><th>Widget mode</th><td><select name="ai_chat_widget_mode"><option value="floating" <?php selected(get_option('ai_chat_widget_mode'),'floating'); ?>>Floating</option><option value="inline" <?php selected(get_option('ai_chat_widget_mode'),'inline'); ?>>Inline</option></select></td></tr>
                    <tr><th>Primary color</th><td><input type="text" name="ai_chat_primary_color" value="<?php echo esc_attr(get_option('ai_chat_primary_color')); ?>" /></td></tr>
                    <tr><th>Position</th><td><select name="ai_chat_position"><option value="bottom-right" <?php selected(get_option('ai_chat_position'),'bottom-right'); ?>>Bottom right</option><option value="bottom-left" <?php selected(get_option('ai_chat_position'),'bottom-left'); ?>>Bottom left</option></select></td></tr>
                    <tr><th>Welcome message</th><td><input type="text" name="ai_chat_welcome" value="<?php echo esc_attr(get_option('ai_chat_welcome')); ?>" style="width:60%"/></td></tr>
                    <tr><th>Escalation email</th><td><input type="email" name="ai_chat_escalation_email" value="<?php echo esc_attr(get_option('ai_chat_escalation_email')); ?>" style="width:50%"/></td></tr>
                    <tr><th>Conversation retention (days)</th><td><input type="number" name="ai_chat_retention_days" value="<?php echo esc_attr(get_option('ai_chat_retention_days')); ?>" /></td></tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    public static function page_widget() {
        if (!current_user_can('manage_options')) wp_die('Unauthorized');
        ?>
        <div class="wrap">
            <h1>Widget & Shortcode</h1>
            <p>Mode: <?php echo esc_html(get_option('ai_chat_widget_mode')); ?></p>
            <p>Shortcode for inline embed: <code>[ai_chat_widget]</code></p>
            <h2>Preview</h2>
            <div id="ai-chat-preview" style="min-height:200px;border:1px solid #ddd;padding:10px;">
                <div style="padding:10px;background:#f9f9f9;border-radius:6px;">
                    <?php echo esc_html(get_option('ai_chat_welcome')); ?>
                </div>
            </div>
            <h2>Custom CSS</h2>
            <p>Place custom CSS in your theme or use a plugin to add CSS. Example:</p>
            <pre>
#ai-chat-root .ai-message-user{ text-align:right; }
#ai-chat-root .ai-message-bot{ text-align:left; }
            </pre>
        </div>
        <?php
    }

    public static function page_kb() {
        if (!current_user_can('manage_options')) wp_die('Unauthorized');
        global $wpdb;
        // handle manual add
        if(isset($_POST['ai_kb_add']) && check_admin_referer('ai_kb_add_action','ai_kb_add_nonce')) {
            $title = sanitize_text_field($_POST['kb_title']);
            $content = wp_kses_post($_POST['kb_content']);
            $wpdb->insert("{$wpdb->prefix}ai_chat_kb", ['title'=>$title,'content'=>$content,'source_type'=>'manual','created_at'=>current_time('mysql')]);
            echo '<div class="updated"><p>FAQ saved.</p></div>';
        }
        ?>
        <div class="wrap">
            <h1>Knowledge Base</h1>
            <h2>Add FAQ / Manual Entry</h2>
            <form method="post">
                <?php wp_nonce_field('ai_kb_add_action','ai_kb_add_nonce'); ?>
                <table class="form-table">
                    <tr><th>Title</th><td><input type="text" name="kb_title" style="width:60%"/></td></tr>
                    <tr><th>Content</th><td><textarea name="kb_content" rows="6" style="width:60%"></textarea></td></tr>
                </table>
                <button class="button button-primary" type="submit" name="ai_kb_add">Add FAQ</button>
            </form>

            <h2>Import FAQs (CSV)</h2>
            <p>CSV format: title,content</p>
            <form id="ai-chat-import-csv" method="post" enctype="multipart/form-data">
                <?php wp_nonce_field('ai_chat_import','ai_chat_import_nonce'); ?>
                <input type="file" name="kb_csv" accept=".csv" />
                <button class="button" id="ai-chat-upload-btn">Upload & Import</button>
            </form>
            <div id="ai-chat-import-result"></div>

            <h2>Existing KB</h2>
            <table class="widefat">
                <thead><tr><th>ID</th><th>Title</th><th>Source</th><th>Created</th></tr></thead>
                <tbody>
                    <?php
                    $rows = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ai_chat_kb ORDER BY created_at DESC LIMIT 200");
                    foreach($rows as $r){
                        echo '<tr><td>'.intval($r->id).'</td><td>'.esc_html($r->title).'</td><td>'.esc_html($r->source_type).'</td><td>'.esc_html($r->created_at).'</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    public static function page_conversations() {
        if (!current_user_can('manage_options')) wp_die('Unauthorized');
        global $wpdb;
        $rows = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ai_chat_conversations ORDER BY started_at DESC LIMIT 100");
        ?>
        <div class="wrap">
            <h1>Conversations</h1>
            <table class="widefat">
                <thead><tr><th>ID</th><th>User</th><th>Session</th><th>Started</th><th>Last</th><th>Actions</th></tr></thead>
                <tbody>
                <?php foreach($rows as $r){
                    echo '<tr>';
                    echo '<td>'.intval($r->id).'</td>';
                    echo '<td>'.esc_html($r->user_id).'</td>';
                    echo '<td>'.esc_html($r->session_id).'</td>';
                    echo '<td>'.esc_html($r->started_at).'</td>';
                    echo '<td>'.esc_html($r->last_activity).'</td>';
                    echo '<td><a href="admin.php?page=ai-chat-conversations&view='.intval($r->id).'">View</a></td>';
                    echo '</tr>';
                } ?>
                </tbody>
            </table>

            <?php
            if(isset($_GET['view'])){
                $cid = intval($_GET['view']);
                $msgs = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}ai_chat_messages WHERE conversation_id = %d ORDER BY created_at ASC", $cid));
                echo '<h2>Conversation #'.intval($cid).'</h2><div style="border:1px solid #ddd;padding:10px;">';
                foreach($msgs as $m){
                    $who = esc_html($m->role);
                    $content = wp_kses_post($m->content);
                    echo '<p><strong>'. $who .'</strong>: '. $content .'</p>';
                }
                echo '</div>';
            }
            ?>

        </div>
        <?php
    }

    public static function page_analytics() {
        if (!current_user_can('manage_options')) wp_die('Unauthorized');
        global $wpdb;
        $total = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}ai_chat_conversations");
        $avg = $wpdb->get_var("SELECT AVG(m.cnt) FROM (SELECT COUNT(*) cnt FROM {$wpdb->prefix}ai_chat_messages GROUP BY conversation_id) m");
        ?>
        <div class="wrap">
            <h1>Analytics</h1>
            <p>Total conversations: <strong><?php echo intval($total); ?></strong></p>
            <p>Average messages per conversation: <strong><?php echo round(floatval($avg),2); ?></strong></p>
            <h2>Top Questions (basic)</h2>
            <?php
            // naive top user messages by frequency
            $top = $wpdb->get_results("SELECT content, COUNT(*) as c FROM {$wpdb->prefix}ai_chat_messages WHERE role='user' GROUP BY content ORDER BY c DESC LIMIT 20");
            echo '<ol>';
            foreach($top as $t){ echo '<li>'.esc_html(mb_strimwidth($t->content,0,120,'...')).' <em>('.intval($t->c).')</em></li>'; }
            echo '</ol>';
            ?>
        </div>
        <?php
    }

    // AJAX handlers
    public static function ajax_import_csv() {
        if(!current_user_can('manage_options')) wp_send_json_error('Unauthorized',403);
        check_admin_referer('ai_chat_import','ai_chat_import_nonce');
        if(empty($_FILES['kb_csv'])) wp_send_json_error('No file uploaded',400);
        $f = $_FILES['kb_csv']['tmp_name'];
        $handle = fopen($f,'r');
        if(!$handle) wp_send_json_error('Could not open file',500);
        global $wpdb;
        $count=0;
        while(($row = fgetcsv($handle)) !== false) {
            if(count($row) < 2) continue;
            $title = sanitize_text_field($row[0]);
            $content = sanitize_text_field($row[1]);
            $wpdb->insert("{$wpdb->prefix}ai_chat_kb", ['title'=>$title,'content'=>$content,'source_type'=>'csv','created_at'=>current_time('mysql')]);
            $count++;
        }
        fclose($handle);
        wp_send_json_success(['imported'=>$count]);
    }

    public static function ajax_escalate() {
        if(!current_user_can('manage_options')) wp_send_json_error('Unauthorized',403);
        check_admin_referer('ai_chat_admin','nonce');
        $conversation_id = intval($_POST['conversation_id']);
        global $wpdb;
        $msgs = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}ai_chat_messages WHERE conversation_id=%d ORDER BY created_at ASC", $conversation_id));
        $body = "Conversation #{$conversation_id}\n\n";
        foreach($msgs as $m){
            $body .= strtoupper($m->role).": " . strip_tags($m->content) . "\n\n";
        }
        $to = get_option('ai_chat_escalation_email', get_option('admin_email'));
        $subject = "Escalated conversation #{$conversation_id}";
        wp_mail($to, $subject, $body);
        wp_send_json_success('Escalated');
    }
}
