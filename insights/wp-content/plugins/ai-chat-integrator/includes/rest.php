<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class AI_Chat_REST {
    public static function init() {
        add_action('rest_api_init', [__CLASS__, 'routes']);
    }

    public static function routes() {
        register_rest_route('ai-chat/v1', '/message', [
            'methods' => 'POST',
            'callback' => [__CLASS__, 'handle_message'],
            'permission_callback' => '__return_true',
        ]);
        register_rest_route('ai-chat/v1', '/conversations/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [__CLASS__, 'get_conversation'],
            'permission_callback' => [__CLASS__, 'admin_only'],
        ]);
    }

    public static function admin_only() {
        return current_user_can('manage_options');
    }

    public static function handle_message($req) {
        $params = json_decode($req->get_body(), true);
        if(empty($params['message'])) {
            return new WP_Error('no_message','No message provided', ['status'=>400]);
        }
        $message = sanitize_text_field($params['message']);
        $session = isset($_COOKIE['ai_chat_session']) ? sanitize_text_field($_COOKIE['ai_chat_session']) : bin2hex(random_bytes(8));
        // set cookie
        setcookie('ai_chat_session', $session, time() + 60*60*24*30, '/');
        global $wpdb;
        // find or create conversation by session
        $conv = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}ai_chat_conversations WHERE session_id=%s", $session));
        if(!$conv) {
            $wpdb->insert("{$wpdb->prefix}ai_chat_conversations", ['user_id'=>get_current_user_id() ?: null, 'session_id'=>$session, 'started_at'=>current_time('mysql'), 'last_activity'=>current_time('mysql')]);
            $conv_id = $wpdb->insert_id;
        } else {
            $conv_id = $conv->id;
            $wpdb->update("{$wpdb->prefix}ai_chat_conversations", ['last_activity'=>current_time('mysql')], ['id'=>$conv_id]);
        }
        // store user message
        $wpdb->insert("{$wpdb->prefix}ai_chat_messages", ['conversation_id'=>$conv_id,'role'=>'user','content'=>$message,'created_at'=>current_time('mysql')]);
        // Simple KB retrieval: find top KB entry with matching words (very naive)
        $kb_context = '';
        $words = preg_split('/\s+/', $message);
        $candidates = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ai_chat_kb WHERE 1=1 LIMIT 5");
        foreach($candidates as $cand) {
            foreach($words as $w) {
                if(strlen($w) > 3 && stripos($cand->content, $w) !== false) {
                    $kb_context = $cand->content;
                    break 2;
                }
            }
        }
        // Build prompt
        $prompt = "System: You are a helpful assistant. Use the knowledge base if present.\n\n";
        if($kb_context) {
            $prompt .= "Context: " . wp_strip_all_tags($kb_context) . "\n\n";
        }
        $prompt .= "User: " . $message;
        // Call provider
        $reply = self::call_provider($prompt);
        if(is_wp_error($reply)) {
            $reply_text = 'Sorry, an error occurred.';
        } else {
            $reply_text = wp_kses_post($reply);
        }
        // store bot message
        $wpdb->insert("{$wpdb->prefix}ai_chat_messages", ['conversation_id'=>$conv_id,'role'=>'bot','content'=>$reply_text,'created_at'=>current_time('mysql')]);
        // return
        return rest_ensure_response(['reply' => $reply_text, 'conversation_id' => $conv_id]);
    }

    public static function get_conversation($req) {
        $id = intval($req['id']);
        global $wpdb;
        $msgs = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}ai_chat_messages WHERE conversation_id=%d ORDER BY created_at ASC", $id));
        return rest_ensure_response($msgs);
    }

    public static function call_provider($prompt) {
        $api_key = get_option('ai_chat_api_key');
        $provider = get_option('ai_chat_provider','openai');
        if(empty($api_key)) return new WP_Error('no_api_key','No API key configured', ['status'=>500]);

        if($provider === 'openai') {
            $url = 'https://api.openai.com/v1/chat/completions';
            $body = [
                'model' => 'gpt-4o-mini', // placeholder; admin can change later
                'messages' => [
                    ['role'=>'system','content'=>'You are a helpful assistant.'],
                    ['role'=>'user','content'=>$prompt]
                ],
                'max_tokens' => 512,
                'temperature' => 0.2,
            ];
            $args = [
                'headers' => [
                    'Authorization' => 'Bearer ' . $api_key,
                    'Content-Type' => 'application/json'
                ],
                'body' => wp_json_encode($body),
                'timeout' => 60
            ];
            $resp = wp_remote_post($url, $args);
            if(is_wp_error($resp)) return $resp;
            $code = wp_remote_retrieve_response_code($resp);
            $body = wp_remote_retrieve_body($resp);
            $data = json_decode($body, true);
            if(isset($data['error'])) return new WP_Error('provider_error', $data['error']['message']);
            // attempt to extract text
            if(isset($data['choices'][0]['message']['content'])) {
                return $data['choices'][0]['message']['content'];
            }
            if(isset($data['choices'][0]['text'])) {
                return $data['choices'][0]['text'];
            }
            return new WP_Error('no_reply','No reply from provider');
        }

        return new WP_Error('unknown_provider','Unknown provider');
    }
}
