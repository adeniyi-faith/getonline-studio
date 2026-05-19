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
        // Simple KB retrieval: naive keyword match
        $kb_context = '';
        $words = preg_split('/\s+/', $message);
        $candidates = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ai_chat_kb WHERE 1=1 LIMIT 8");
        foreach($candidates as $cand) {
            foreach($words as $w) {
                if(strlen($w) > 3 && stripos($cand->content, $w) !== false) {
                    $kb_context = $cand->content;
                    break 2;
                }
            }
        }

        $bot_name = get_option('ai_chat_bot_name','Assistant');
        // Build prompt
        $prompt = "You are $bot_name. Use the knowledge base if present.\n\n";
        if($kb_context) {
            $prompt .= "Context: " . wp_strip_all_tags($kb_context) . "\n\n";
        }
        $prompt .= "User: " . $message;

        // Call provider with fallback
        $reply = self::call_provider_with_fallback($prompt);
        if(is_wp_error($reply)) {
            $reply_text = 'Sorry, I could not process your request right now.';
            // more detailed error for admins (not shown to public)
            error_log('AI Chat provider error: ' . $reply->get_error_message());
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

    public static function call_provider_with_fallback($prompt) {
        $order = get_option('ai_chat_provider_order', 'openai,claude,gemini,cohere,mistral');
        $providers = array_map('trim', explode(',', $order));
        foreach($providers as $p) {
            $key = get_option('ai_chat_api_' . $p);
            if(empty($key)) continue;
            $res = call_user_func([__CLASS__, 'call_provider_' . $p], $prompt, $key);
            if(!is_wp_error($res)) {
                return $res;
            } else {
                // log error and continue to next provider
                error_log('AI provider '.$p.' failed: '.$res->get_error_message());
            }
        }
        return new WP_Error('no_providers','No providers available or all providers failed', ['status'=>500]);
    }

    // Provider implementations (basic examples)
    public static function call_provider_openai($prompt, $key) {
        $url = 'https://api.openai.com/v1/chat/completions';
        $body = [
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role'=>'system','content'=>'You are a helpful assistant.'],
                ['role'=>'user','content'=>$prompt]
            ],
            'max_tokens' => 512,
            'temperature' => 0.2,
        ];
        $args = [
            'headers' => [
                'Authorization' => 'Bearer ' . $key,
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
        if($code >= 400) {
            $msg = isset($data['error']['message']) ? $data['error']['message'] : 'OpenAI error';
            return new WP_Error('openai_error', $msg);
        }
        if(isset($data['choices'][0]['message']['content'])) {
            return $data['choices'][0]['message']['content'];
        }
        if(isset($data['choices'][0]['text'])) {
            return $data['choices'][0]['text'];
        }
        return new WP_Error('openai_no_reply','No reply from OpenAI');
    }

    public static function call_provider_claude($prompt, $key) {
        // Minimal example for Anthropic Claude — admin should confirm endpoint & payload
        $url = 'https://api.anthropic.com/v1/complete';
        $body = [
            'model' => 'claude-2.1', 'prompt' => $prompt, 'max_tokens' => 512
        ];
        $args = [
            'headers' => [
                'x-api-key' => $key,
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
        if($code >= 400) {
            $msg = isset($data['error']) ? json_encode($data['error']) : 'Claude error';
            return new WP_Error('claude_error', $msg);
        }
        if(isset($data['completion'])) return $data['completion'];
        if(isset($data['choices'][0]['text'])) return $data['choices'][0]['text'];
        return new WP_Error('claude_no_reply','No reply from Claude');
    }

    public static function call_provider_gemini($prompt, $key) {
        // Placeholder example for Google Gemini (admin should supply correct endpoint & key)
        $url = 'https://gemini.googleapis.com/v1/models/text-bison:generate';
        $body = ['prompt' => ['text' => $prompt], 'maxOutputTokens' => 512];
        $args = [
            'headers' => [
                'Authorization' => 'Bearer ' . $key,
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
        if($code >= 400) {
            $msg = isset($data['error']) ? json_encode($data['error']) : 'Gemini error';
            return new WP_Error('gemini_error', $msg);
        }
        if(isset($data['candidates'][0]['content'])) return $data['candidates'][0]['content'];
        if(isset($data['output'])) return is_array($data['output']) ? json_encode($data['output']) : $data['output'];
        return new WP_Error('gemini_no_reply','No reply from Gemini');
    }

    public static function call_provider_cohere($prompt, $key) {
        $url = 'https://api.cohere.ai/generate';
        $body = ['model'=>'command-xlarge-nightly','prompt'=>$prompt,'max_tokens'=>300];
        $args = [
            'headers' => [
                'Authorization' => 'Bearer ' . $key,
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
        if($code >= 400) {
            $msg = isset($data['message']) ? $data['message'] : 'Cohere error';
            return new WP_Error('cohere_error',$msg);
        }
        if(isset($data['generations'][0]['text'])) return $data['generations'][0]['text'];
        return new WP_Error('cohere_no_reply','No reply from Cohere');
    }

    public static function call_provider_mistral($prompt, $key) {
        // Placeholder for Mistral API
        $url = 'https://api.mistral.ai/generate';
        $body = ['input' => $prompt, 'max_tokens' => 300];
        $args = [
            'headers' => [
                'Authorization' => 'Bearer ' . $key,
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
        if($code >= 400) {
            $msg = isset($data['error']) ? json_encode($data['error']) : 'Mistral error';
            return new WP_Error('mistral_error', $msg);
        }
        if(isset($data['generated_text'])) return $data['generated_text'];
        if(isset($data['outputs'][0]['text'])) return $data['outputs'][0]['text'];
        return new WP_Error('mistral_no_reply','No reply from Mistral');
    }
}
