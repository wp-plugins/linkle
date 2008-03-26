<?php
add_filter('mce_plugins', 'linkle_mce_plugins');
add_filter('mce_buttons', 'linkle_mce_buttons');
add_action('tinymce_before_init', 'linkle_mce_before_init');

function linkle_mce_plugins($plugins) {
	$plugins[] = 'linkleplugin';
	return $plugins;
}
function linkle_mce_buttons($buttons) {
	$buttons[] = 'linkleplugin';
	return $buttons;
}
function linkle_mce_before_init() {
	$fullurl = get_bloginfo('wpurl').'/wp-content/plugins/linkle/tinymce/tinymce2/';
	echo 'tinyMCE.loadPlugin("linkleplugin", "'.$fullurl.'");';
}
?>
