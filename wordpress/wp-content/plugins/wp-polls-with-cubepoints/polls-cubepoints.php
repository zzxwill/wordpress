<?php
/*
+----------------------------------------------------------------+								|
|																							|
|	File Written By:																	|
|	- Jonathan Lau & Peter Zhang														|
|	- http://cubepoints.com														|
|																							|
|	File Information:																	|
|	- Extension for CubePoints Plugin															|
|	- wp-content/plugins/wp-polls/polls-cubepoints.php						|
|																							|
+----------------------------------------------------------------+
*/


### Check Whether User Can Manage Polls
if(!current_user_can('manage_polls')) {
	die('Access Denied');
}


### Variables Variables Variables
$base_name = plugin_basename('wp-polls/polls-cubepoints.php');
$base_page = 'admin.php?page='.$base_name;
$id = intval($_GET['id']);


### If Form Is Submitted
if($_POST['Submit']) {
	$poll_cubepoints = intval($_POST['poll_cubepoints']);
	update_option('poll_cubepoints', $poll_cubepoints);
	echo '<div class="updated"><p><strong>Settings Updated</strong></p></div>';
}
?>
<form id="poll_options_form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo plugin_basename(__FILE__); ?>">
<div class="wrap">
	<div id="icon-wp-polls" class="icon32"><br /></div>
	<h2><?php _e('CubePoints', 'wp-polls'); ?></h2>

	<h3><?php _e('Points Setting', 'wp-polls'); ?></h3>

	<?php
	if(!function_exists('cp_alterPoints')){
		echo '<div class="error"><p><strong>The <a href="http://cubepoints.com" target="_blank">CubePoints Plugin</a> must be enabled before this would work!</strong></p></div>';
	}
	?>

	<table class="form-table">
		<tr valign="top">
			<th scope="row" style="width:300px;"><label for="poll_cubepoints">Number of points for each poll done:</label></th>
			<td valign="middle" width="190"><input type="text" id="poll_cubepoints" name="poll_cubepoints" value="<?php echo get_option('poll_cubepoints'); ?>" size="20" /></td>
			<td><input type="button" onclick="document.getElementById('poll_cubepoints').value='0'" value="Do not add points for referal" class="button" /></td>
		</tr>
	</table>
	
	<br />
	<p><i><strong>Recommended settings for <a href="admin.php?page=wp-polls/polls-options.php">Poll Options</a>:</strong><br />- Logging Method: Logged By Username<br />- Who Is Allowed To Vote: Registered Users Only</i></p>

	<!-- Submit Button -->
	<p class="submit">
		<input type="submit" name="Submit" class="button" value="<?php _e('Save Changes', 'wp-polls'); ?>" />
	</p>
</div> 
</form> 