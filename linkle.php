<?php
/*
Plugin Name: Linkle
Description Quick links to a number of different web sites
Author: Michael Smit, Mike Lay
Version: 0.6.1
*/

include_once 'LinkleOptions.php';
include_once 'LinkleMatchInfo.php';
include_once 'tinymce/LinkleButton.php';

function linkle_substitute($match){
	$match_info = LinkleMatchInfo::build_from_match($match);
	global $linkle_options;
	return '<span class="linkle_link" link_type="'.htmlentities($match_info->get_link_type()).'" link_term="'.htmlentities($match_info->get_link_term()).'" link_text="'.htmlentities($match_info->get_link_text()).'">'.$linkle_options->process_tag($match_info).'</span>';
}

$linkle_options = NULL;
function linkle($content){
	global $linkle_options;
	$linkle_options = LinkleOptions::build_from_options();
	return preg_replace_callback(
		LinkleMatchInfo::get_match_string(),
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
		linkle_update_options();
	}
	foreach(array_keys($_REQUEST) as $key){
		if(ereg("^submit_([0-9]+)", $key, $match)){
			echo '<div id="message" class="updated fade">';
			echo '<p>Link Type Suggested. Thanks!</p>';
			echo '</div>';
			linkle_suggest_link($match[1], $_REQUEST);
			return;
		}
	}
	linkle_print_form();
	echo '</div>';
}

function linkle_suggest_link($count, $map){
	if(get_magic_quotes_gpc())
		$map = array_map(stripslashes, $map);
	$type = $map["linkle_handler_".$count."_type"];
	$description = $map["linkle_handler_".$count."_description"];
	$code = $map["linkle_handler_".$count."_code"];
	$message = "<linkle_suggestion>\n";
	$message = $message."<type>".$type."</type>\n";
	$message = $message."<description>".$description."</description>\n";
	$message = $message."<code><![CDATA[\n";
	$message = $message.$code;
	$message = $message."]]></code>\n";
	$message = $message."</linkle_suggestion>";
	//NO SPAM
	$username = "linkle_sugestions";
	$domain = "4thmouse.com";
	mail($username."@".$domain, "Suggestion for type ".$type, $message);
}

function linkle_update_options() {
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
<label>Handler Code ($match, $text, $term, $properties)</label><br />
<textarea name="linkle_handler_<?php echo $count?>_code" cols="70" rows="10"><?php echo htmlentities($info->get_code())?></textarea>
<?php if(strcmp($info->get_code() ,"") != 0){?>
<p class="submit">
<input type="submit" name="submit_<?php echo $count?>" value = "Suggest This Link Type to The Linkle Team!"/>
</p>
<?php }?>
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
