<?php
/*
Plugin Name: Smart Category Ordering
Plugin URI: http://www.tierra-innovation.com/
Description: Allows you to alphabetize categories by post title, by trimming leading text, by post date.
Author: Tierra Innovation
Version: 1.5
Author URI: http://www.tierra-innovation.com


Changes:
 1.5   - Applied patch from Mike Schroder to detect Multiple Category Selection Widget and disable sorting if found


*/

/*
 * This is a modified version (under the MIT License) of a plugin
 * originally developed by Tierra Innovation for WNET.org.
 * 
 * This plugin is currently available for use in all personal
 * or commercial projects under both MIT and GPL licenses. This
 * means that you can choose the license that best suits your
 * project, and use it accordingly.
 *
 * MIT License: http://www.tierra-innovation.com/license/MIT-LICENSE.txt
 * GPL2 License: http://www.tierra-innovation.com/license/GPL-LICENSE.txt
 */

$ordering_option = null;

register_activation_hook(__FILE__, '_smart_ordering_activate');
register_deactivation_hook(__FILE__, '_smart_ordering_deactivate');
add_action('admin_menu', '_smart_ordering_add_manage_page');
add_action('parse_query', '_smart_ordering_parse_query');

return;

function _smart_ordering_posts_orderby_request($orderby) {
	global $ordering_option;
	
	if ($ordering_option && ($ordering_option["order_by"] == "Smart Title"))
		$orderby = "smart_post_title {$ordering_option["dir"]}";
	return $orderby;

}

var_dump($orderby);

function _smart_ordering_posts_fields_request($fields) {
	global $ordering_option;
	if ($ordering_option && ($ordering_option["order_by"] == "Smart Title")) {
		$fields .= <<<EOT
, case when lcase(substring_index(post_title, ' ', 1)) in ('a', 'an', 'the')
    then concat(
      substring(post_title, locate(' ', post_title) + 1),
      ', ',
      substring_index(post_title, ' ', 1)
    )
    else post_title
  end as smart_post_title
EOT;
	}
	return $fields;
}

function _smart_ordering_activate() {
	add_option('smart_ordering_options', array(), 'The Smart Ordering plugin settings.');
	return true;
}

function _smart_ordering_deactivate() {
	delete_option('smart_ordering_options');
	return true;
}

function _smart_ordering_parse_query(&$wp_query) {

	// we only run on category pages
	if (!is_category())
		return;

	// no options, no work
	$options = get_option('smart_ordering_options');
	if (!$options)
		return;

     // Detect MCSW, and disable if it's currently running its own search
     global $wpmm_search_vars;

     if ($wpmm_search_vars)
             return;

	// get the option for the category or the first parent category that includes children
	$category = get_category_by_path($wp_query->query_vars["category_name"]);
	$option = $category ? $options[$category->cat_ID] : false;

	if (!$option && $category) {
		while ($category->parent != "0") {
			$category = get_category($category->parent);
			if (!$category)
				break;
			$option = $options[$category->cat_ID];
			if ($option && $option["include_children"])
				break;
			else
				$option = false;
		}
	}

	// default to the catch-all option
	if (!$option)
		$option = isset($options[0]) ? $options[0] : array_shift($options);
		
	if ($option) {

		$wp_query->query_vars["posts_per_page"] = $option["num_posts"];
		$wp_query->query_vars["orderby"] = $option["order_by"] == "Date" ? "date" : "title";
		$wp_query->query_vars["order"] = $option["dir"];
		
		if ($option["order_by"] == "Smart Title") {
			global $ordering_option;
			$ordering_option = $option;
			
			add_action('posts_orderby_request', '_smart_ordering_posts_orderby_request');
			add_action('posts_fields_request', '_smart_ordering_posts_fields_request');
		}
	}
}

function _smart_ordering_add_manage_page() {

	add_options_page(
		'Smart Category Ordering', // page title
		'Smart Category Ordering', // sub-menu title
		'manage_options', // access/capa
		'smart-category-ordering.php', // file
		'_smart_ordering_manage_page' // function
	);

}

function _smart_ordering_manage_page() {

	$options = get_option('smart_ordering_options');

	if (_smart_ordering_get_var("add_category")) {
		$cat_id = _smart_ordering_get_var("cat_id");
		$num_posts = intval(_smart_ordering_get_var("num_posts"));
		$order_by = _smart_ordering_get_var("order_by");
		$include_children = _smart_ordering_get_var("include_children");
		$dir = _smart_ordering_get_var("dir");
		if (($num_posts > 0) && in_array($order_by, array("Smart Title", "Title", "Date")) && in_array($dir, array("asc", "desc"))) {
			$option = array("num_posts" => $num_posts, "order_by" => $order_by, "dir" => $dir, "include_children" => $include_children);
			if ($options)
				$options[$cat_id] = $option;
			else
				$options = array($cat_id => $option);
		}
		
		update_option('smart_ordering_options', $options);
	}
	else if (_smart_ordering_get_var("deleteit")) {
		$cat_ids = isset($_POST["cat_id"]) ? $_POST["cat_id"] : false;
		if (($cat_ids !== false) && is_array($cat_ids)) {
			foreach ($cat_ids as $cat_id)
				unset($options[$cat_id]);
			update_option('smart_ordering_options', $options);
		}
	}
	
	$other_category_option = false;
	$category_list_options = array();
	if ($options) {
		foreach ($options as $cat_id => $option) {
			if ($cat_id == 0)
				$other_category_option = $option;
			else {
				$cat_name = substr(get_category_parents($cat_id, true, ' &raquo; '), 0, -8);
				$order_by = _smart_ordering_get_order_by($option['order_by'], $option['dir']);
				// include children defaults to true...
				$include_children = !isset($option['include_children']) || $option['include_children'] ? "yes" : "no";
				$category_list_options[] = "<tr><th scope='col' class='check-column'><input type='checkbox' name='cat_id[]' value='{$cat_id}' /></th><td>{$cat_name}</td><td>{$option['num_posts']}</td><td>{$order_by}</td><td>{$include_children}</td></tr>";
			}
		}
	}
	if ($other_category_option) {
		$cat_name = "(Catch-all)";
		$order_by = _smart_ordering_get_order_by($other_category_option['order_by'], $other_category_option['dir']);
		$category_list_options[] = "<tr><th scope='col' class='check-column'><input type='checkbox' name='cat_id[]' value='{$cat_id}' /></th><td>{$cat_name}</td><td>{$other_category_option['num_posts']}</td><td>{$order_by}</td><td>n/a</td></tr>";
	}
	$category_list = implode("\n", $category_list_options);

	$all_cat_names = array();
	$all_cat_ids = get_all_category_ids();
	foreach ($all_cat_ids as $cat_id)
		$all_cat_names[substr(get_category_parents($cat_id, false, ' &raquo; '), 0, -8)] = $cat_id;
	ksort($all_cat_names);
	$all_categories_list_options = array();
	foreach ($all_cat_names as $cat_name => $cat_id)
		$all_categories_list_options[] = "<option value='{$cat_id}'>{$cat_name}</option>";
	$all_categories_list_options[] = "<option value='0'>(Catch-all)</option>";
	$all_categories_list = implode("\n", $all_categories_list_options);

	echo <<<EOT
<div class='wrap'>

	<div id='icon-options-general' class='icon32'><img src='http://tierra-innovation.com/wordpress-cms/logos/src/smart-category-ordering/1.5/default.gif' alt='' title='' /><br /></div>

	<h2>Smart Category Ordering</h2>

	<p>Smart Ordering is a plugin that allows you to sort your post content (per category).  You can apply your sorting to a single category, or have your settings follow a category tree, covering the parent and children categories.</p>

	<p>**Note** When sorting by "Smart Title", that option is there to the leading 'a', 'an' or 'the' from a post title when sorting.  This is the only difference between "Smart Title" and "Title."</p>

EOT;

	if (count($category_list_options) > 0) {
		echo <<<EOT
	<form name="categories" method="post">

		<input type="hidden" name="page" value="smart-category-ordering" />

		<br class="clear" />

		<h3>Saved Ordering</h3>

		<table id="ordered_collections" class="widefat">
		<thead>
			<tr>
				<th scope="col" class="check-column"><input type="checkbox" onclick="auto_check(this);" /></th>
				<th>Category</th>
				<th># Posts to Show</th>
				<th>Order By</th>
				<th>Include Children</th>
			</tr>
		</thead>
		<tbody>
			{$category_list}
		</tbody>
		</table>

		<div class="tablenav">

			<div class="alignleft"><input type="submit" value="Delete" name="deleteit" class="button-secondary delete" /></div>	  

		</div>

	</form>
EOT;
	}
	echo <<<EOT
	<form name="addcategory" method="post">

		<input type="hidden" name="page" value="smart-category-ordering"/>

		<style type='text/css'>
			select.smore { width: 120px; }
		</style>

		<br />

		<h3>Add New Sorting</h3>

		<ul>
			<li><strong>Category:</strong> 
				<select name="cat_id">
					{$all_categories_list}
				</select>
			</li>
			<li><strong>Post Count:</strong> <input type="text" name="num_posts" size="5" value="10" /> (indicates number of posts per page)</li>
			<li><strong>Order By:</strong>  
				<select name="order_by" class="smore">
					<option value="Smart Title">Smart Title</option>
					<option value="Title">Title</option>
					<option value="Date">Date</option>
				</select>
				in 
				<select name="dir" class="smore">
					<option value="asc">Ascending</option>
					<option value="desc">Decending</option>
				</select> order
			</li>
			<li><strong>Include Children:</strong> <input type="checkbox" name="include_children" value="1" checked /> (checked if you want this feature added to all sub categories within the parent category.</li>
		</ul>

		<p><input type='submit' name='add_category' class="button-secondary delete" value='Add New Sorting' /></p>

	</form>
</div>

<script>
  function auto_check(checkbox) {
	var list = document.categories['cat_id[]'];
	if (list.length) {
		for (var i=0; i<list.length; i++)
			list[i].checked = checkbox.checked;
	}
	else
		list.checked = checkbox.checked;
  }
</script>
EOT;
}

function _smart_ordering_get_order_by($order_by, $dir) {
	return $order_by . "/" . ($dir == "asc" ? "Ascending" : "Decending");
}

function _smart_ordering_get_var($key, $default = false) {
	return isset($_POST[$key]) ? trim($_POST[$key]) : $default;
}

?>