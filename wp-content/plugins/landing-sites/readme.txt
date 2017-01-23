=== Landing sites ===
Contributors: devil1591
Donate link: http://wordpress.org/donate/
Tags: google, referer, related, search, seo
Requires at least: 2.0.2
Tested up to: 3.7.1
Stable tag: trunk

When visitors is referred to your site from a search engine, the plugin is showing them related posts to their search on your blog.

== Description ==

When visitors is referred to your site from a search engine, they are definitely looking for something specific - often they just roughly check the page they land on and then closes the window if what they are looking for isn't there. Why not help them by showing them related posts to their search on your blog? This plugin lets you do that, works with a long list of search engines!

== Installation ==

1. Upload the folder `landing-sites` to your `/wp-content/plugins/` directory

2. Activate the plugin

3. Check FAQ to show the related posts list

== Details on functions ==

`ls_getinfo('isref')`
Returning true if the referrer is a supported search engine - used as a conditional tag

`ls_getinfo('terms')`
Outputs the search terms

`ls_getinfo('referrer')`
Outputs a link to the referring search engine

`ls_related()`
Outputs the list of related posts. This can be customized by passing variables to it. ls_related('limit', 'length', 'before title', 'after title', 'before post', 'after post', 'show password protected posts', 'show post excerpts').

`ls_search_engines()`
Outputs links to other search engines results.  This can be customized by passing variables to it. ls_search_engines('before_title', 'after_title').

In the code example in the FAQ, it outputs 5 related posts, 10 words per excerpt (if excerpts are enabled), list item start before title, list item close after post title, no content before and after posts, doesn't show password protected posts and doesn't show excerpts.

== Frequently Asked Questions ==

= How to show the related posts list? =

Add this code (in your index.php or somewhere else):

`<?php if (ls_getinfo('isref')) : ?>
   <h2><?php ls_getinfo('terms'); ?></h2>
   <p>You came here from <?php ls_getinfo('referrer'); ?> searching for <i><?php ls_getinfo('terms'); ?></i>. These posts might be of interest:</p>
   <ul>
     <?php ls_related(5, 10, '<li>', '</li>', '', '', false, false); ?>
   </ul>
<?php endif; ?>`

== Screenshots ==

1. A website showing related post when a visitor has searched for "landing sites screenshot"