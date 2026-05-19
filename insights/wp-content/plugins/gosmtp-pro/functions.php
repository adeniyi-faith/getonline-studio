<?php
/*
* gosmtp
* https://gosmtp.net
* (c) Softaculous Team
*/

// Are we being accessed directly ?
if(!defined('GOSMTP_PRO_VERSION')) {
	exit('Hacking Attempt !');
}

function gosmtp_pro_activation(){
	update_option('gosmtp_pro_version', GOSMTP_PRO_VERSION);
}

function gosmtp_pro_is_network_active($plugin_name){
	$is_network_wide = false;
	
	// Handling network site
	if(!is_multisite()){
		return $is_network_wide;
	}
	
	$_tmp_plugins = get_site_option('active_sitewide_plugins');

	if(!empty($_tmp_plugins) && preg_grep('/.*\/'.$plugin_name.'\.php$/', array_keys($_tmp_plugins))){
		$is_network_wide = true;
	}
	
	return $is_network_wide;
}

function gosmtp_pro_update_checker(){
	$current_version = get_option('gosmtp_pro_version', '0.0');
	$version = (int) str_replace('.', '', $current_version);

	// No update required
	if($current_version == GOSMTP_PRO_VERSION){
		return true;
	}
	
	$is_network_wide = gosmtp_pro_is_network_active('gosmtp-pro');
	
	if($is_network_wide){
		$free_ins = get_site_option('gosmtp_free_installed');
	}else{
		$free_ins = get_option('gosmtp_free_installed');
	}
	
	// If plugin runing reached here it means GoSMTP free installed 
	if(empty($free_ins)){
		if($is_network_wide){
			update_site_option('gosmtp_free_installed', time());
		}else{
			update_option('gosmtp_free_installed', time());
		}
	}
	
	update_option('gosmtp_version_pro_nag', time());
	update_option('gosmtp_version_free_nag', time());
	update_option('gosmtp_pro_version', GOSMTP_PRO_VERSION);
}


// Load license data
function gosmtp_pro_load_license($parent = 0){
	
	global $gosmtp;
		
	if(!empty($parent)){
		$license_field = 'softaculous_pro_license';
		$license_api_url = 'https://a.softaculous.com/softwp/';
		$prods = apply_filters('softaculous_pro_products', []);
	}else{
		$license_field = 'gosmtp_license';
		$license_api_url = GOSMTP_API;
		$prods = [];
	}
	
	// Load license
	$gosmtp->license = get_option($license_field, array());
	
	// Update license details as well
	if(!empty($gosmtp->license) && !empty($gosmtp->license['license']) && (time() - @$gosmtp->license['last_update']) >= 86400){
		
		$resp = wp_remote_get($license_api_url.'license.php?license='.$gosmtp->license['license'].'&prods='.implode(',', $prods).'&url='.rawurlencode(site_url()));
		
		// Did we get a response ?
		if(is_array($resp)){
			
			$tosave = json_decode($resp['body'], true);
			
			// Is it the license ?
			if(!empty($tosave['license'])){
				$tosave['last_update'] = time();
				update_option($license_field, $tosave);
			}
			
		}
		
	}

	// If the license is Free or Expired check for Softaculous Pro license
	if(empty($gosmtp->license) || empty($gosmtp->license['active'])){
		
		if(function_exists('softaculous_pro_load_license')){
			$softaculous_license = softaculous_pro_load_license();
			if(!empty($softaculous_license['license']) && 
				(!empty($softaculous_license['active']) || empty($gosmtp->license['license']))
			){
				$gosmtp->license = $softaculous_license;
			}
		}elseif(empty($parent)){
			$gosmtp->license = get_option('softaculous_pro_license', []);
			
			if(!empty($gosmtp->license)){
				gosmtp_pro_load_license(1);
			}
		}
	}
	
}

add_filter('softaculous_pro_products', 'gosmtp_softaculous_pro_products', 10, 1);
function gosmtp_softaculous_pro_products($r = []){
	$r['gosmtp'] = 'gosmtp';
	return $r;
}

// Add our license key if ANY
function gosmtp_pro_updater_filter_args($queryArgs){
	
	global $gosmtp;
	
	if (!empty($gosmtp->license['license'])){
		$queryArgs['license'] = $gosmtp->license['license'];
	}
	
	$queryArgs['url'] = rawurlencode(site_url());
	
	return $queryArgs;
}

// Handle the Check for update link and ask to install license key
function gosmtp_pro_updater_check_link($final_link){
	
	global $gosmtp;
	
	if(empty($gosmtp->license['license'])){
		return '<a href="'.admin_url('admin.php?page=gosmtp-license').'">Install GoSMTP Pro License Key</a>';
	}
	
	return $final_link;
}

// Prevent update of gosmtp free
function gosmtp_pro_get_free_version_num(){
		
	if(defined('GOSMTP_VERSION')){
		return GOSMTP_VERSION;
	}
	
	// In case of gosmtp deactive
	return gosmtp_pro_file_get_version_num('gosmtp/gosmtp.php');
}

// Prevent update of gosmtp free
function gosmtp_pro_file_get_version_num($plugin){
	
	// In case of gosmtp deactive
	include_once(ABSPATH . 'wp-admin/includes/plugin.php');
	$plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/'.$plugin);
	
	if(empty($plugin_data)){
		return false;
	}
	
	return $plugin_data['Version'];
	
}

// Prevent update of gosmtp free
function gosmtp_pro_disable_manual_update_for_plugin($transient){
	$plugin = 'gosmtp/gosmtp.php';
	
	// Is update available?
	if(!isset($transient->response) || !isset($transient->response[$plugin])){
		return $transient;
	}
	
	$free_version = gosmtp_pro_get_free_version_num();
	$pro_version = GOSMTP_PRO_VERSION;
	
	if(!empty($GLOBALS['gosmtp_pro_is_upgraded'])){
		$pro_version = gosmtp_pro_file_get_version_num('gosmtp-pro/gosmtp-pro.php');
	}
	
	// Update the gosmtp version to the equivalent of Pro version
	if(!empty($pro_version) && version_compare($free_version, $pro_version, '<')){
		$transient->response[$plugin]->new_version = $pro_version;
		$transient->response[$plugin]->package = 'https://downloads.wordpress.org/plugin/gosmtp.'.$pro_version.'.zip';
	}else{
		unset($transient->response[$plugin]);
	}

	return $transient;
}

// Auto update free version after update pro version
function gosmtp_pro_update_free_after_pro($upgrader_object, $options){
	
	// Check if the action is an update for the plugins
	if($options['action'] != 'update' || $options['type'] != 'plugin'){
		return;
	}
		
	// Define the slugs for the free and pro plugins
	$free_slug = 'gosmtp/gosmtp.php'; 
	$pro_slug = 'gosmtp-pro/gosmtp-pro.php';

	// Check if the pro plugin is in the list of updated plugins
	if( 
		(isset($options['plugins']) && in_array($pro_slug, $options['plugins']) && !in_array($free_slug, $options['plugins'])) ||
		(isset($options['plugin']) && $pro_slug == $options['plugin'])
	){
	
		// Trigger the update for the free plugin
		$current_version = gosmtp_pro_get_free_version_num();
		
		if(empty($current_version)){
			return;
		}
		
		$GLOBALS['gosmtp_pro_is_upgraded'] = true;
		
		// This will set the 'update_plugins' transient again
		wp_update_plugins();

		// Check for updates for the free plugin
		$update_plugins = get_site_transient('update_plugins');
		
		if(empty($update_plugins) || !isset($update_plugins->response[$free_slug]) || version_compare($update_plugins->response[$free_slug]->new_version, $current_version, '<=')){
			return;
		}
		
		require_once(ABSPATH . 'wp-admin/includes/plugin.php');
		require_once(ABSPATH . 'wp-admin/includes/class-wp-upgrader.php');
		
		$skin = wp_doing_ajax()? new WP_Ajax_Upgrader_Skin() : null;
		
		$upgrader = new Plugin_Upgrader($skin);
		$upgraded = $upgrader->upgrade($free_slug);
		
		if(!is_wp_error($upgraded) && $upgraded){
			// Re-active free plugins
			if( file_exists( WP_PLUGIN_DIR . '/'.  $free_slug ) && is_plugin_inactive($free_slug) ){
				activate_plugin($free_slug); // TODO for network
			}
			
			// Re-active pro plugins
			if( file_exists( WP_PLUGIN_DIR . '/'.  $pro_slug ) && is_plugin_inactive($pro_slug) ){
				activate_plugin($pro_slug); // TODO for network
			}
		}
	}
}

function gosmtp_pro_api_url($main_server = 0, $suffix = 'gosmtp'){
	
	global $gosmtp;
	
	$r = array(
		'https://s0.softaculous.com/a/softwp/',
		'https://s1.softaculous.com/a/softwp/',
		'https://s2.softaculous.com/a/softwp/',
		'https://s3.softaculous.com/a/softwp/',
		'https://s4.softaculous.com/a/softwp/',
		'https://s5.softaculous.com/a/softwp/',
		'https://s7.softaculous.com/a/softwp/',
		'https://s8.softaculous.com/a/softwp/'
	);
	
	$mirror = $r[array_rand($r)];
	
	// If the license is newly issued, we need to fetch from API only
	if(!empty($main_server) || empty($gosmtp->license['last_edit']) || 
		(!empty($gosmtp->license['last_edit']) && (time() - 3600) < $gosmtp->license['last_edit'])
	){
		$mirror = GOSMTP_API;
	}
	
	if(!empty($suffix)){
		$mirror = str_replace('/softwp', '/'.$suffix, $mirror);
	}
	
	return $mirror;
	
}