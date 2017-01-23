<?php
/*
Receives AJAX calls from the Ajax_the_views plugin, gets the number of views, then returns them to the requesting page. 
*/

// load WordPress so we have access to the functions we need
require_once('../../../wp-config.php');
require_once(ABSPATH . 'wp-settings.php');

// lets just make sure there's nothing bad in the URL
foreach ($_GET as $key => $value) {
	$_GET[$key] = htmlentities(stripslashes($value), ENT_NOQUOTES);	
}

// don't proceed if we don't have enough info or if the nonce fails (removed the nonce check as the caching messes it up)
//if (!isset($_GET['ajax_the_views']) || !check_admin_referer('do-you-really-want-to-see-the-views')) {
if (!isset($_GET['ajax_the_views'])) {
	return;
}

// get the JSON string and decode it into an array
$atv_json_string = $_GET['ajax_the_views'];
$atv_post_id_array = json_decode($atv_json_string,true);

// if the output of json_decode isn't an array, let's get out of here.
if (!is_array($atv_post_id_array)) {
	return;
}

// get the options for WP-Postviews. We'll format it as per those options.
$atv_views_options = get_option('views_options');

// go through each item and create the array to return.
foreach ($atv_post_id_array as $value) {
	// get the views for this post id and format it
	$atv_post_views = intval(get_post_meta($value, 'views', true));
	$atv_return_array[] = str_replace('%VIEW_COUNT%', number_format_i18n($atv_post_views), $atv_views_options['template']);
}

echo json_encode($atv_return_array);

?>