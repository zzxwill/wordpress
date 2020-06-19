=== Plugin Name ===
Contributors: StephenCronin
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=sjc@scratch99.com&currency_code=&amount=&return=&item_name=WP-Ajax_the_views
Tags: WP-PostViews, post views, caching, W3 Total Cache, WP Super Cache, Ajax, jQuery
Requires at least: 2.8.0
Tested up to: 3.5.1
Stable tag: 1.1
Extends WP-PostViews so that the number of post views is displayed via Ajax, meaning it will be accurate even if a caching plugin is being used.

== Description ==
The [Ajax_the_views WordPress plugin](http://www.scratch99.com/wordpress-plugin-ajax-the-views/ "WordPress plugin that allows WP-PostViews to show page view counts even if a caching plugin is being used") extends [WP-PostViews](http://lesterchan.net/wordpress/readme/wp-postviews.html) so that the number of post views is displayed via Ajax, meaning it will be accurate even if a caching plugin is being used. 

Note: WP-PostViews can *count* views when caching plugin is being used, but can't *display* the count. This fixes that.

= Requirements = 
This plugin requires the [WP-PostViews](http://lesterchan.net/wordpress/readme/wp-postviews.html) plugin to be installed and active.

= Usage =
First, install the plugin and activate it.

Second, clear the page cache. How to do this will differ depending on which caching plugin you are using (ie W3 Total Cache, WP Super Cache, etc).

= How Does It Work? =
To show the number of pages views on a post / page etc, you will have already added the following line to your theme:

<?php if(function_exists('the_views')) { the_views(); } ?>   

Unfortunately, when page is cached, the count is cached as well. Although WP-PostViews can count page views behind the scenes, it won't be able to display them. Each time the page is cleared from the cache (for example when a comment is made), the count will be updated, but then the page will get cached again and the count will freeze until the cache is next cleared. 

This plugin filters the_views function and replaces the count with the text "Please wait", wrapped in span tags with an id containing the post ID. It also adds some JavaScript to the footer of the page, which finds all the counts on the page (there may be more than one) and builds a list of post IDs. The plugin then sends an Ajax request to the server, which looks up the counts for these posts and sends them back to the requesting page, which then updates the count fields.

= Performance = 
The point of using a caching plugin is to minimise server resources and speed up page loading.

This plugin is going to take up more server resources than it would otherwise: The server script will run once for each page load and will look up the database to get the count. On pages with more than one post, such as the home page, it will have to look up each one. 

In the bigger scheme of things, you are only losing a small bit of what you've gained by using a caching plugin (a normal page load without caching will use a *lot* more resources than this), but nonetheless you need to be aware it will have some impact on performance. 

= Acknowledgments =
The changes in version 1.1 were created by Vlad Lasky, the [Australian WordPress Expert](http://wpexpert.com.au/)

= Support: =
This plugin is officially not supported (due to my time constraints), but if you leave a comment on the plugin's home page or [contact me](http://www.scratch99.com/contact/), I will help if I can.

= Disclaimer =
This plugin is released under the [GPL licence](http://www.gnu.org/copyleft/gpl.html). I do not accept any responsibility for any damages or losses, direct or indirect, that may arise from using the plugin or these instructions. This software is provided as is, with absolutely no warranty. Please refer to the full version of the GPL license for more information.

== Installation ==
1. Download the plugin file and unzip it.
1. Upload the `ajax-the-views` folder to the `wp-content/plugins/` folder.
1. Activate the Ajax_the_views plugin within WordPress.

Alternatively, you can install the plugin automatically through the WordPress Admin interface by going to Plugins -> Add New and searching for Ajax_the_views.

= Upgrade =
1. Download the plugin file and unzip it.
1. Upload the `ajax-the-views` folder to the `wp-content/plugins/` folder, overwriting the existing files.
1. Deactivate the Ajax_the_views plugin within WordPress, then reactivate it (to make sure any new settings are created).

Alternatively, you can update this plugin through the WordPress Admin interface.

== Screenshots ==
No screenshots exist at this time, but you can see the plugin in action on the home page of my 
[WordPress development blog](http://www.scratch99.com/).

== Changelog ==

= 1.1 (15th March 2013) =
* Bug Fix: Prevent the "Please wait" message from displaying if the caching plugin was deactivated (credit Vlad Lasky)
* Improvement: Use the wp_enqueue_scripts hook instead of wp_print_styles, in line with WordPress best practice (credit Vlad Lasky)

= 1.0 (17th September 2010) =
* Initial Release