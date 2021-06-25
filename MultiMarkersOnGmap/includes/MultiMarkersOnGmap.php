<?php
/*
@author: Oscar Alderete <me@oscaralderete.com>
@website: https://oscaralderete.com
@editor: NetBeans IDE v11.0
*/
class MultiMarkersOnGmap {

public static $code = 'MultiMarkersOnGmap';
public static $slug = 'multimarkersongmap';
public static $ajaxAdminListener = '_process_ajax_request';
public static $dir;
public static $uri;
public static $path;

private static $title = 'Multi Markers On Gmap';
private static $permission = 'administrator';
private static $icon = 'dashicons-location';
private static $version = '1.0.0';
private static $s = ['result'=>'ERROR', 'msg'=>'Error code 3001'];
private static $scripts = [
	['type'=>'remote', 'src'=>'https://maps.googleapis.com/maps/api/js'],
	['type'=>'local', 'src'=>'scripts.js']
];
private static $adminScripts = [
	['type'=>'local', 'src'=>'vue.3.1.1.js'],
	['type'=>'local', 'src'=>'oa-toast.js', 'folder'=>'admin'],
	['type'=>'local', 'src'=>'oa-dialogs.js', 'folder'=>'admin'],
	['type'=>'local', 'src'=>'scripts.js', 'folder'=>'admin']
];
private static $styles = [
	['type'=>'local', 'src'=>'styles.css']
];
private static $adminStyles = [
	['type'=>'local', 'src'=>'oa-loader.css', 'folder'=>'admin'],
	['type'=>'local', 'src'=>'oa-toast.css', 'folder'=>'admin'],
	['type'=>'local', 'src'=>'oa-dialogs.css', 'folder'=>'admin'],
	['type'=>'local', 'src'=>'styles.css', 'folder'=>'admin']
];


//publics
public function overpassScripts(){
	//google maps key
	if($googleMapKey = get_option('google_map_key')){
		//option already exists
	}
	else{
		$googleMapKey = '';
	}
	self::$scripts[0]['src'] .= '?key=' . $googleMapKey;
}

public static function loadScripts(string $zone='public'){
	$handle = self::$code . '_js';
	$order = 1;
	$scripts = $zone == 'public' ? self::$scripts : self::$adminScripts;
	foreach($scripts as $i) {
		if($i['type'] == 'local'){
			$uri = self::$uri . (isset($i['folder']) ? $i['folder'] : 'public') . '/js/' . $i['src'];
			$version = self::$version;
			$onFooter = true;
		}
		else{
			$uri = $i['src'];
			$version = null;
			$onFooter = false;
		}
		wp_register_script($handle . $order, $uri, [], $version, $onFooter);
		wp_enqueue_script($handle . $order);
		$order++;
	}
	//styles
	$styles = $zone == 'public' ? (isset(self::$styles) ? self::$styles : []) : (isset(self::$adminStyles) ? self::$adminStyles : []);
	$handle = self::$code . '_css';
	$order = 1;
	foreach($styles as $i) {
		if($i['type'] == 'local'){
			$uri = self::$uri . (isset($i['folder']) ? $i['folder'] : 'public') . '/css/' . $i['src'];
			$version = self::$version;
			$onFooter = true;
		}
		else{
			$uri = $i['src'];
			$version = null;
			$onFooter = false;
		}
		wp_enqueue_style($handle . $order, $uri, [], $version);
		$order++;
	}
}

public static function getView(string $type = 'public'){
	//default
	$bus = ['$pageData'];
	$rem = [];
	//markers
	if($r = get_option('markers')){
		$markers = unserialize($r);
	}
	else{
		$markers = [];
	}
	//map height
	if($r = get_option('map_height')){
		$map_height = $r;
	}
	else{
		$map_height = 200;
	}
	switch($type){
		case 'admin':
			//google maps key
			if($googleMapKey = get_option('google_map_key')){
				//option already exists
			}
			else{
				$googleMapKey = '';
			}
			$view = 'admin/template/settings';
			//preparing page data
			$rem[] = json_encode([
				'key' => $googleMapKey,
				'markers' => $markers,
				'ajax_action' => self::$slug . '_process_ajax_request',
				'map_height' => $map_height,
			]);
			break;
		default:
			$view = 'public/template/gmap';
			//preparing page data
			$rem[] = json_encode([
				'markers' => $markers,
				'marker_uri' => self::$uri . 'public/img/marker.svg',
				'map_height' => $map_height
			]);
	}
	return str_replace($bus, $rem, file_get_contents(self::$dir . '/' . $view . '.html'));
}

public static function adminMenu(){
	$t = self::$title;
	add_menu_page($t, $t, self::$permission, self::$slug . '/admin-page', function(){
		echo self::getView('admin');
	}, self::$icon, 6);
}

public static function adminInit(){
	register_setting(self::$slug, 'google_map_key');
	register_setting(self::$slug, 'markers');
	register_setting(self::$slug, 'map_height');
}

public static function processAjaxRequest(){
	$s = self::$s;
	switch($_POST['type']){
		case 'save_marker':
			$s = self::saveMarkers($_POST['data']);
			break;
		case 'save_api_key':
			$s = self::saveApiKey($_POST['data']);
			break;
		case 'save_map_height':
			$s = self::saveMapHeight($_POST['data']);
			break;
		default:
			$s['msg'] = 'Error code 2001';
	}
	sleep(1);
	echo json_encode($s);
	wp_die();
}


//privates
private static function saveMarkers(array $post){
	$s = self::$s;
	//save/update option
	if(update_option('markers', serialize($post['markers']))){
		$s['result'] = 'OK';
		$s['msg'] = 'Marker has been ' . $post['action'] . 'd!';
	}
	else{
		$s['msg'] = 'Error trying to save marker!';
	}
	return $s;
}

private static function saveApiKey(array $post){
	$s = self::$s;
	//check for changes
	$googleMapKey = get_option('google_map_key');
	if($googleMapKey == $post['key']){
		$s['msg'] = 'Nothing to update because API key hasn\'t changed!';
		return $s;
	}
	//save/update option
	if(update_option('google_map_key', $post['key'])){
		$s['result'] = 'OK';
		$s['msg'] = 'Your API key has been updated!';
	}
	else{
		$s['msg'] = 'Error trying to save API key!';
	}
	return $s;
}

private static function saveMapHeight(array $post){
	$s = self::$s;
	$key = 'map_height';
	//check for changes
	$map_height = get_option($key);
	if($map_height == $post[$key]){
		$s['msg'] = 'Nothing to update because map height hasn\'t changed!';
		return $s;
	}
	//save/update option
	if(update_option($key, $post[$key])){
		$s['result'] = 'OK';
		$s['msg'] = 'Your map height has been updated!';
	}
	else{
		$s['msg'] = 'Error trying to save map height!';
	}
	return $s;
}

}
