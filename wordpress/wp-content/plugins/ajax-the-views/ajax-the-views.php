<?php
/*
Plugin Name: Ajax_the_views
Plugin URI: http://www.scratch.com/wordpress-plugin-ajax-the-views/
Description: Extends WP-PostViews so that the number of post views is displayed via Ajax, meaning it will be accurate even if a caching plugin is being used. Note: WP-PostViews can *count* views when caching plugin is being used, but can't *display* the count. This fixes that.
Version: 1.1
Date: 15 March 2013
Author: Stephen Cronin
Author URI: http://www.scratch99.com/
   
   Copyright 2010 - 2013  Stephen Cronin  (email : sjc@scratch99.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/

// ****** Add jQuery just in case (although WP-PostViews should already have loaded it) *******
add_action('wp_enqueue_scripts', 'ajax_the_views_scripts');
function ajax_the_views_scripts() {
	if(defined('WP_CACHE') && WP_CACHE && function_exists('the_views')) {
		wp_enqueue_script('jquery');
	}
}
// ********************************************************************************


// ****** Filter the_views function to add class and id we'll need ******
if(defined('WP_CACHE') && WP_CACHE && function_exists('the_views')) {
	add_filter('the_views', 'ajax_the_views');
}
function ajax_the_views($content){
	global $post;
	return '<span class="ajax-the-views" id="ajax-the-views-'.$post->ID.'">Please wait</span>';
}
// ***********************************************************


// ****** add the necessary jQuery to the footer ******
add_action('wp_footer', 'ajax_the_views_footer');
function ajax_the_views_footer() {
	if(defined('WP_CACHE') && WP_CACHE && function_exists('the_views')) {
		?>
		<script type="text/javascript">
		// Added by Ajax_the_views 
		jQuery(document).ready(function($) {
			var postIDs = new Array();
			var i = 0;
			// get a list of post id numbers that need the number of views
			$('.ajax-the-views').each(function(){
				postIDs[i] = $(this).attr('id').replace('ajax-the-views-','');
				i++;
			});
			// create the JSON string to go to the server (only if there's a views field on the page)
			if (postIDs.length > 0) {
				var sjcJSON = '{';
				for (i=0; i<=postIDs.length-1; i++) {
					sjcJSON += '"'+i+'":"'+ postIDs[i] + '"';
					if (i<postIDs.length-1) {
						sjcJSON += ',';
					}
				}
				sjcJSON += '}';
				// Send the Ajax request to the server and update the number of views appropriately
				var thisURL = '<?php echo wp_nonce_url(plugins_url(dirname(plugin_basename(__FILE__))).'/ajax-the-views-server.php', 'do-you-really-want-to-see-the-views'); ?>&ajax_the_views='+sjcJSON;
				$.getJSON(thisURL , function(data) {
					for (i=0; i<=postIDs.length-1; i++) {
						$('#ajax-the-views-'+postIDs[i]).text(data[i]);
					}
				});
			}
		});
		</script>
		<?php
	}
	else { ?>
		<!-- Ajax_the_views disabled: Cache not detected -->
		<?php
	}	
}
// *********************************************

?>