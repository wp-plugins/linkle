<?php
/*
Plugin Name: Linkle
Description Quick links to a number of different web sites
Author: Michael Smit, Mike Lay
Version: 0.6
*/

//changelog
//sort link types in editor
//added description field (and added LinkleLinkInfo class to manage link type info)
//added some more default links

//old changlog
//small modification in linkle.php for
//use with linkle.js
//sizeable changes to linkle.js

//old changelog
//small form modifications for javascript id hooks
//in linkle.php
//added linkle.js file for collapsing forms
//no changes to LinkleOptions.php

//old changelog
//moved all the options stuff into a class called LinkleOptions

include_once 'LinkleOptions.php';

function linkle_substitute($match){
	global $linkle_options;
	return $linkle_options->process_tag($match);
}

$linkle_options = NULL;
function linkle($content){
	global $linkle_options;
	$linkle_options = LinkleOptions::build_from_options();
	return preg_replace_callback(
		"/\[ln +([^\]]+)\]([^\[]*)(\[text]([^\[]*)\[\/text\])?\[\/ln\]/",
		linkle_substitute,
		$content);
}



add_filter('the_content','linkle');
add_filter('the_excerpt','linkle');
add_filter('comment_text', 'linkle');

///////////////////////////////////////
//admin page
function set_linkle_options() {
	LinkleOptions::install();
}

function unset_linkle_options() {
	LinkleOptions::uninstall();
}

function linkle_options() {
	echo '<div class="wrap"><h2>Linkle Options</h2>';
	if($_REQUEST['submit']) {
		update_linkle_options();
	}
	linkle_print_form();
	echo '</div>';
}

function update_linkle_options() {
	$options = LinkleOptions::build_from_request();
	$options->store();
	echo '<div id="message" class="updated fade">';
	echo '<p>Options Updated</p>';
	echo '</div>';
}

function linkle_print_handler_entry($key, $info, $count){
	?>
<div id="linkle_entry_<?=$count?>">
<label>Name</label>
<input type="text" name="linkle_handler_<?php echo $count?>_type" value="<?php echo htmlentities($key)?>"/><br />
<label>Description</label>
<textarea cols="70" rows="1" name="linkle_handler_<?php echo $count?>_description"><?php echo htmlentities($info->get_description())?></textarea><br/>
<label>Handler Code ($match, $text, $term)</label><br />
<textarea name="linkle_handler_<?php echo $count?>_code" cols="70" rows="10"><?php echo htmlentities($info->get_code())?></textarea>
</div>
	<?php
}


function linkle_print_form() {
	$options = LinkleOptions::build_from_options();
	?>
<script language="Javascript" type="text/javascript" src="../wp-content/plugins/linkle/linkle.js"></script>
<form method="post">
<p class="submit">
<input type="submit" name="submit" value="Save Changes" />
</p>
<fieldset id="linkle_custom_options" class="options">
<legend>Custom Link Handlers</legend>
<div id="linkle_search"> </div>
<input type="hidden" name="linkle_handler_count" value="<?php echo $options->count() + 1?>"/>
	<?php
	$options->each('linkle_print_handler_entry');
	linkle_print_handler_entry("",LinkleLinkInfo::build("",""),$options->count());
	?>
</fieldset>
<p class="submit">
<input type="submit" name="submit" value="Save Changes" />
</p>
</form>
	<?php
}

function modify_menu_for_linkle() {
	add_options_page(
		'Linkle',
		'Linkle',
		'manage_options',
		__FILE__,
		'linkle_options'
		);
}
add_action('admin_menu', 'modify_menu_for_linkle');
register_activation_hook(__FILE__, "set_linkle_options");
register_deactivation_hook(__FILE__, "unset_linkle_options");
?>
