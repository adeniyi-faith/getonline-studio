
<?php
/**
 * Plugin Name:       Bible Exposition Automator
 * Description:       Generates Bible expositions: quotes scripture, asks Gemini to return a Title and HTML body, formats output.
 * Version:           1.4.1
 * Author:            Faith Adeniyi
 * License:           GPL-2.0-or-later
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class BEA_Simple {

    const OPT = 'bea_options';
    const LOG = 'bea_log';
    const HOOK = 'bea_generate_post_hook';

    private $defaults = array(
        'bea_bible_api_key'=>'',
        'bea_gemini_api_key'=>'',
        'bea_bible_id'=>'de4e12af7f28f599-01',
        'bea_references'=>"JHN.3.16",
        'bea_ref_mode'=>'list',
        'bea_list_index'=>0,
        'bea_prompt'=>'',
        'bea_post_status'=>'draft',
        'bea_post_author'=>1,
        'bea_post_category'=>0,
        'bea_schedule'=>'hourly',
    );

    public function __construct(){
        add_action('admin_menu',array($this,'menu'));
        add_action('admin_init',array($this,'init'));
        add_action('admin_post_bea_manual_generate',array($this,'manual'));
        add_action(self::HOOK,array($this,'run'));
        register_activation_hook(__FILE__,array($this,'activate'));
        register_deactivation_hook(__FILE__,array($this,'deactivate'));
    }

    public function menu(){
        add_menu_page('Bible Automator','Bible Automator','manage_options','bible_automator',array($this,'page'),'dashicons-book-alt',20);
    }

    public function init(){
        register_setting('bea_group', self::OPT, array($this,'sanitize'));
        add_settings_section('s','API & Options',null,'bible_automator');
        add_settings_field('bea_bible_api_key','API.Bible Key',array($this,'field_text'),'bible_automator','s',array('id'=>'bea_bible_api_key'));
        add_settings_field('bea_gemini_api_key','Gemini API Key',array($this,'field_text'),'bible_automator','s',array('id'=>'bea_gemini_api_key'));
        add_settings_field('bea_references','References (one per line)',array($this,'field_area'),'bible_automator','s',array('id'=>'bea_references'));
        add_settings_field('bea_post_status','Post Status',array($this,'field_select'),'bible_automator','s',array('id'=>'bea_post_status','options'=>array('draft'=>'Draft','publish'=>'Publish')));

        $opts = get_option(self::OPT);
        if ($opts === false) {
            $d = $this->defaults;
            $d['bea_prompt'] = "Title: Provide a short 6-12 word title on a single line starting with 'Title:'.\n\nBody: Provide HTML body. Rules:\n- Start Body with blockquote that contains the verse text and reference.\n- Use <h2> for headings and <strong> for bold emphasis.\n- Allowed tags: p, br, h2, h3, strong, em, ul, ol, li, blockquote, a.\n\nNow produce the Title line, a blank line, then the Body HTML.\nReference: %%REFERENCE%%\nVerseText: %%CONTENT%%";
            add_option(self::OPT,$d);
        }
    }

    public function sanitize($input){
        $out = $this->defaults;
        foreach($out as $k=>$v) if(isset($input[$k])) $out[$k] = sanitize_text_field($input[$k]);
        $out['bea_list_index'] = isset($input['bea_list_index']) ? intval($input['bea_list_index']) : 0;
        return $out;
    }

    public function field_text($args){ $id=$args['id']; $v=esc_attr($this->get($id)); echo "<input type='text' name='".self::OPT."[{$id}]' value='{$v}' class='regular-text'>"; }
    public function field_area($args){ $id=$args['id']; $v=esc_textarea($this->get($id)); echo "<textarea name='".self::OPT."[{$id}]' rows=6 cols=60>{$v}</textarea>"; }
    public function field_select($args){ $id=$args['id']; $opts=$args['options']; $cur=$this->get($id); echo "<select name='".self::OPT."[{$id}]'>"; foreach($opts as $val=>$label){ $sel = selected($cur,$val,false); echo "<option value='".esc_attr($val)."' {$sel}>".esc_html($label)."</option>"; } echo "</select>"; }

    private function get($k){ $opts = get_option(self::OPT,$this->defaults); return isset($opts[$k])?$opts[$k]:$this->defaults[$k]; }

    public function page(){
        if(!current_user_can('manage_options')) return;
        $log = get_option(self::LOG,array());
        ?>
        <div class="wrap"><h1><?php echo esc_html(get_admin_page_title());?></h1>
        <form method="post" action="options.php"><?php settings_fields('bea_group'); do_settings_sections('bible_automator'); submit_button('Save Settings'); ?></form>
        <h2>Manual</h2><form method="post" action="<?php echo esc_url(admin_url('admin-post.php'));?>"><?php wp_nonce_field('bea_manual');?><input type="hidden" name="action" value="bea_manual_generate"><?php submit_button('Generate Exposition Now'); ?></form>
        <h2>Log</h2><?php if(empty($log)) echo '<p>No runs yet.</p>'; else { echo '<table class="widefat"><thead><tr><th>Time</th><th>Type</th><th>Message</th></tr></thead><tbody>'; foreach(array_reverse($log) as $e){ echo '<tr><td>'.esc_html(date('Y-m-d H:i:s',$e['time'])).'</td><td>'.esc_html($e['type']).'</td><td>'.wp_kses_post($e['message']).'</td></tr>'; } echo '</tbody></table>'; } ?></div>
        <?php
    }

    private function append_log($type,$msg){ $log=get_option(self::LOG,array()); $log[]=array('time'=>time(),'type'=>$type,'message'=>$msg); if(count($log)>200) $log=array_slice($log,-200); update_option(self::LOG,$log); }

    public function manual(){
        if(!current_user_can('manage_options')) wp_die('Unauthorized'); if(!isset($_POST['_wpnonce'])||!wp_verify_nonce($_POST['_wpnonce'],'bea_manual')) wp_die('Nonce fail'); $res=$this->run(true); if($res) set_transient('bea_admin_notice','Generated: Post ID '.intval($res),20); else set_transient('bea_admin_notice','Failed. Check log.',20); wp_safe_redirect(admin_url('admin.php?page=bible_automator')); exit;
    }

    private function get_next_scripture(){
        $mode = $this->get('bea_ref_mode');
        if($mode==='traverse') return false; // keep simple: list only here
        $refs = array_filter(array_map('trim',explode("\n",$this->get('bea_references'))));
        if(empty($refs)){ $this->append_log('error','No references set'); return false; }
        $idx = intval($this->get('bea_list_index'));
        if($idx >= count($refs)) $idx = 0;
        $ref = $refs[$idx];
        // update index
        $opts = get_option(self::OPT); $opts['bea_list_index'] = ($idx+1)%max(1,count($refs)); update_option(self::OPT,$opts);
        $api = $this->get('bea_bible_api_key'); $bid = $this->get('bea_bible_id');
        if(empty($api)||empty($bid)){ $this->append_log('error','Missing API key or bible id'); return false; }
        $url = "https://api.scripture.api.bible/v1/bibles/{$bid}/verses/".rawurlencode($ref)."?content-type=html";
        $call = function() use($url,$api){ return wp_remote_get($url,array('headers'=>array('api-key'=>$api),'timeout'=>15)); };
        $r = $this->http_retry($call,3,1);
        if(is_wp_error($r)){ $this->append_log('error','Failed scripture fetch: '.$r->get_error_message()); return false; }
        if(wp_remote_retrieve_response_code($r)!=200){ $this->append_log('error','Bible API HTTP '.wp_remote_retrieve_response_code($r)); return false; }
        $b = json_decode(wp_remote_retrieve_body($r),true);
        if(empty($b['data'])){ $this->append_log('error','Bible API returned unexpected'); return false; }
        $ref_text = isset($b['data']['reference'])?$b['data']['reference']:$ref;
        $content_html = isset($b['data']['content'])?$b['data']['content']:'';
        $content = wp_strip_all_tags($content_html);
        $this->append_log('info','Fetched '.$ref_text);
        return array('reference'=>$ref_text,'content'=>$content);
    }

    private function http_retry($call,$max=3,$delay=1){
        $attempt=0; $d=$delay;
        while($attempt<$max){ $attempt++; $res = call_user_func($call); if(is_wp_error($res)){ $this->append_log('warning','Attempt '.$attempt.' error: '.$res->get_error_message()); } else { $code=wp_remote_retrieve_response_code($res); if($code>=200&&$code<300) return $res; $this->append_log('warning','Attempt '.$attempt.' HTTP '.$code); } sleep($d); $d*=2; } return new WP_Error('failed','All attempts failed'); }

    private function generate_title_and_body($ref,$content){
        $api = $this->get('bea_gemini_api_key'); if(empty($api)){ $this->append_log('error','Missing Gemini key'); return false; }
        $prompt = $this->get('bea_prompt');
        if(empty($prompt)){ $opts = get_option(self::OPT); $prompt = isset($opts['bea_prompt'])?$opts['bea_prompt']:''; }
        $prompt = str_replace('%%REFERENCE%%',$ref,$prompt); $prompt = str_replace('%%CONTENT%%',$content,$prompt);
        $endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';
        $headers = array('Content-Type'=>'application/json','X-goog-api-key'=>$api);
        $body = array('contents'=>array(array('parts'=>array(array('text'=>$prompt)))));
        $call = function() use($endpoint,$headers,$body){ return wp_remote_post($endpoint,array('headers'=>$headers,'body'=>wp_json_encode($body),'timeout'=>60)); };
        $r = $this->http_retry($call,3,2);
        if(is_wp_error($r)){ $this->append_log('error','Gemini error: '.$r->get_error_message()); return false; }
        $code = wp_remote_retrieve_response_code($r);
        if($code!=200 && $code!=201){ $this->append_log('error','Gemini HTTP '.$code); return false; }
        $b = json_decode(wp_remote_retrieve_body($r),true);
        $text = '';
        if(isset($b['candidates'][0]['content']['parts'][0]['text'])) $text = $b['candidates'][0]['content']['parts'][0]['text'];
        elseif(isset($b['outputs'][0]['content'][0]['text'])) $text = $b['outputs'][0]['content'][0]['text'];
        else { $this->append_log('error','Gemini response parse failed'); return false; }
        // Normalize
        $text = preg_replace("/\r\n|\r/","\n",$text);
        // Extract Title line
        $title = null; $body_html = $text;
        if(preg_match('/^Title:\s*(.+)$/mi',$text,$m)){ $title = trim($m[1]); $parts = preg_split('/^Title:.*$/mi',$text,2); if(isset($parts[1])) $body_html = trim($parts[1]); }
        // fallback to first h2 or first sentence
        if(!$title){ if(preg_match('/<h2[^>]*>(.*?)<\/h2>/i',$text,$m2)) $title = wp_strip_all_tags($m2[1]); else { $s = strip_tags($text); $s = preg_replace('/\s+/',' ',trim($s)); $title = wp_trim_words($s,10,$more=''); } }
        // ensure blockquote present
        if(stripos($body_html,'<blockquote')===false){
            $quote = '<blockquote>'.esc_html($ref).' — "'.esc_html($content).'"</blockquote>';
            $body_html = $quote . "\n\n" . $body_html;
        }
        // convert markdown-like bold and headings
        $body_html = preg_replace('/\*\*(.+?)\*\*/s','<strong>$1</strong>',$body_html);
        $body_html = preg_replace('/^###\s*(.+)$/m','<h3>$1</h3>',$body_html);
        $body_html = preg_replace('/^##\s*(.+)$/m','<h2>$1</h2>',$body_html);
        // sanitize allowed tags
        $allowed = array('p'=>array(),'br'=>array(),'h2'=>array(),'h3'=>array(),'strong'=>array(),'em'=>array(),'ul'=>array(),'ol'=>array(),'li'=>array(),'blockquote'=>array(),'a'=>array('href'=>true,'rel'=>true,'target'=>true));
        $body_html = wp_kses($body_html,$allowed);
        return array('title'=>$title,'body'=>$body_html);
    }

    private function create_post($title,$body){
        $opts = get_option(self::OPT);
        $data = array('post_title'=>wp_strip_all_tags($title),'post_content'=>$body,'post_status'=>$opts['bea_post_status'],'post_author'=>intval($opts['bea_post_author'])?:get_current_user_id(),'post_category'=>$opts['bea_post_category']?array(intval($opts['bea_post_category'])):array());
        $id = wp_insert_post($data,true);
        if(is_wp_error($id)){ $this->append_log('error','Post create failed: '.$id->get_error_message()); return false; }
        add_post_meta($id,'_bea_generated',current_time('mysql'));
        $this->append_log('success','Created post '.$id.' for '.$title);
        return $id;
    }

    public function run($manual=false){
        $script = $this->get_next_scripture();
        if(!$script) return false;
        $this->append_log('info','Using scripture '.$script['reference']);
        $out = $this->generate_title_and_body($script['reference'],$script['content']);
        if(!$out) return false;
        $post = $this->create_post($out['title'],$out['body']);
        return $post;
    }

    public function activate(){ if(!wp_next_scheduled(self::HOOK)) wp_schedule_event(time(),'hourly',self::HOOK); $this->append_log('info','Activated'); }
    public function deactivate(){ $t = wp_next_scheduled(self::HOOK); if($t) wp_unschedule_event($t,self::HOOK); $this->append_log('info','Deactivated'); }
}

new BEA_Simple();
