<?php
/**
Plugin Name: Multiple Markers On Google Map
Plugin URI: https://oscaralderete.com
Description: Let's put multiple markers on a map on your WordPress page. Define all markers you need on its modern reactive admin page (powered by Vue 3).
Version: 1.0
Author: Oscar Alderete <wordpress@oscaralderete.com>
Author URI: https://oscaralderete.com
*/
if(!defined('WPINC')){
	die;
}

require plugin_dir_path(__FILE__) . 'includes/MultiMarkersOnGmap.php';

MultiMarkersOnGmap::$dir = __DIR__;
MultiMarkersOnGmap::$uri = plugin_dir_url(__FILE__);
MultiMarkersOnGmap::$path = plugin_dir_path(__FILE__);

//Add shortcode
add_shortcode(MultiMarkersOnGmap::$code, function(){
	return MultiMarkersOnGmap::getView();
});

//Register scripts to use 
add_action('wp_enqueue_scripts', function(){
	//add key to Google maps API url
	MultiMarkersOnGmap::overpassScripts();
	//load scripts + styles
	MultiMarkersOnGmap::loadScripts();
});

//Admin page
add_action('admin_menu', function(){
	MultiMarkersOnGmap::adminMenu();
});

//Register plugin settings
add_action('admin_init', function(){
	MultiMarkersOnGmap::adminInit();
});

//Register scripts to use 
add_action('admin_enqueue_scripts', function(){
	MultiMarkersOnGmap::loadScripts('admin');
});

//Ajax request listener
add_action('wp_ajax_' . MultiMarkersOnGmap::$slug . MultiMarkersOnGmap::$ajaxAdminListener, function(){
	MultiMarkersOnGmap::processAjaxRequest();
});