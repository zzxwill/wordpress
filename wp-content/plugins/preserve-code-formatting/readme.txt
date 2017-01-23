=== Preserve Code Formatting ===
Contributors: coffee2code
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6ARCFJ9TX3522
Tags: code, formatting, post body, content, display, writing, escape, coffee2code
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 3.6
Tested up to: 3.8
Stable tag: 3.5

Preserve formatting of code for display by preventing its modification by WordPress and other plugins while also retaining whitespace.

== Description ==

This plugin preserves formatting of code for display by preventing its modification by WordPress and other plugins while also retaining whitespace.

NOTE: Use of the visual text editor will pose problems as it can mangle your intent in terms of `code` tags. I strongly suggest you not use the visual editor in conjunction with this plugin as I have taken no effort to make the two compatible.

Notes:

Basically, you can just paste code into `code`, `pre`, and/or other tags you additionally specify and this plugin will:

* Prevent WordPress from HTML-encoding text (i.e. single- and double-quotes will not become curly; "--" and "---" will not become en dash and em dash, respectively; "..." will not become a horizontal ellipsis, etc)
* Prevent most other plugins from modifying preserved code
* Prevent shortcodes from being processed
* Optionally preserve whitespace (in a variety of methods)
* Optionally preserve code added in comments

Keep these things in mind:

* ALL embedded HTML tags and HTML entities will be rendered as text to browsers, appearing exactly as you wrote them (including any `br` tags).
* By default this plugin filters 'the_content' (post content), 'the_excerpt' (post excerpt), and 'get_comment_text (comment content)'.

Example:

A post containing this within `code` tags:

`
$wpdb->query("
        INSERT INTO $tablepostmeta
        (post_id,meta_key,meta_value)
        VALUES ('$post_id','link','$extended')
");
`

Would, with this plugin enabled, look in a browser pretty much how it does above, instead of like:

`
$wpdb->query(&#8212;
INSERT INTO $tablepostmeta
(post_id,meta_key,meta_value)
VALUES ('$post_id','link','$extended')
&#8213;);
`

Links: [Plugin Homepage](http://coffee2code.com/wp-plugins/preserve-code-formatting/) | [Plugin Directory Page](http://wordpress.org/plugins/preserve-code-formatting/) | [Author Homepage](http://coffee2code.com)


== Installation ==

1. Whether installing or updating, whether this plugin or any other, it is always advisable to back-up your data before starting
1. Unzip `preserve-code-formatting.zip` inside the `/wp-content/plugins/` directory (or install via the built-in WordPress plugin installer)
1. Activate the plugin through the 'Plugins' admin menu in WordPress
1. Go to the `Settings` -> `Code Formatting` admin settings page (which you can also get to via the Settings link next to the plugin on the Manage Plugins page) and customize the settings.
1. Write a post with code contained within opening and closing `code` tags (using the HTML editor, not the Visual editor).


== Frequently Asked Questions ==

= Why does my code still display all funky?  (by the way, I'm using the visual editor) =

The visual editor has a tendency to screw up some of your intent, especially when you are attempting to include raw code. This plugin does not make any claims about working when you create posts with the visual editor enabled.

= Can I put shortcode examples within code tags and not have them be evaluated by WordPress? =

Yes, shortcodes within code tags (or any tag processed by this plugin) will be output as pure text and not be processed as shortcodes by WordPress.

= Does this plugin include unit tests? =

Yes.


== Screenshots ==

1. A screenshot of the plugin's admin options page.


== Changelog ==

= 3.5 (2014-01-11) =
* Add setting to control if code should be preserved in posts (default is true)
* Don't wrap 'pre' tags in 'pre' despite settings values
* Update plugin framework to 037
* Better singleton implementation:
    * Add `get_instance()` static method for returning/creating singleton instance
    * Make static variable 'instance' private
    * Make constructor protected
    * Make class final
    * Additional related changes in plugin framework (protected constructor, erroring `__clone()` and `__wakeup()`)
* Add unit tests
* Add checks to prevent execution of code if file is directly accessed
* Re-license as GPLv2 or later (from X11)
* Add 'License' and 'License URI' header tags to readme.txt and plugin file
* Use explicit path for require_once()
* Discontinue use of PHP4-style constructor
* Discontinue use of explicit pass-by-reference for objects
* Remove ending PHP close tag
* Minor documentation improvements
* Minor code reformatting (spacing, bracing)
* Note compatibility through WP 3.8+
* Drop compatibility with version of WP older than 3.6
* Add comments explaining use of base64_encode and base64_decode
* Update copyright date (2014)
* Regenerate .pot
* Change plugin description (to make it shorter)
* Change donate link
* Omit final closing PHP tag
* Add assets directory to plugin repository checkout
* Update screenshot
* Move screenshot into repo's assets directory
* Add banner

= 3.2 =
* Fix bug with settings form not appearing in MS
* Update plugin framework to 032
* Remove support for 'c2c_preserve_code_formatting' global
* Note compatibility through WP 3.3+
* Drop support for versions of WP older than 3.1
* Change parent constructor invocation
* Create 'lang' subdirectory and move .pot file into it
* Regenerate .pot
* Add 'Domain Path' directive to top of main plugin file
* Add link to plugin directory page to readme.txt
* Add text and FAQ question regarding how shortcodes are prevented from being evaluated
* Tweak installation instructions in readme.txt
* Update screenshot for WP 3.3
* Update copyright date (2012)

= 3.1 =
* Fix to properly register activation and uninstall hooks
* Update plugin framework to version v023
* Save a static version of itself in class variable $instance
* Deprecate use of global variable $c2c_preserve_code_formatting to store instance
* Add __construct(), activation(), and uninstall()
* Explicitly declare functions public and variable private
* Remove declarations of instance variable which have since become part of the plugin framework
* Note compatibility through WP 3.2+
* Drop compatibility with version of WP older than 3.0
* Minor code formatting changes (spacing)
* Update copyright date (2011)
* Add plugin homepage and author links in description in readme.txt

= 3.0 =
* Re-implementation by extending C2C_Plugin_016, which among other things adds support for:
    * Reset of options to default values
    * Better sanitization of input values
    * Offload of core/basic functionality to generic plugin framework
    * Additional hooks for various stages/places of plugin operation
    * Easier localization support
* Full localization support
* Change storing plugin instance in global variable to $c2c_preserve_code_formatting (instead of $preserve_code_formatting), to allow for external manipulation
* Rename class from 'PreserveCodeFormatting' to 'c2c_PreserveCodeFormatting'
* Remove docs from top of plugin file (all that and more are in readme.txt)
* Note compatibility with WP 2.9+, 3.0+
* Drop compatibility with versions of WP older than 2.8
* Add PHPDoc documentation
* Minor tweaks to code formatting (spacing)
* Add package info to top of plugin file
* Add Upgrade Notice section to readme.txt
* Update copyright date
* Remove trailing whitespace
* Add .pot file

= 2.5.4 =
* Fixed some borked code preservation by restoring some processing removed in previous release

= 2.5.3 =
* Fixed recently introduced bug affecting occasional code preservation by using a more robust alternative approach
* Fixed "Settings" link for plugin in plugin listing, which lead to blank settings page
* Changed help text for preservable tags settings input to be more explicit

= 2.5.2 =
* Fix to retain any attributes defined for tags being preserved
* Fixed recently introduced bug affecting occasional code preservation

= 2.5.1 =
* Fixed newly introduced bug that added unnecessary slashes to preserved code
* Fixed long-running bug where intended slashes in code got stripped on display (last remaining known bug)

= 2.5 =
* Fixed long-running bug that caused some preserved code to appear garbled
* Updated a lot of internal plugin management code
* Added 'Settings' link to plugin's plugin listing entry
* Used plugins_url() instead of hardcoded path
* Brought admin markup in line with modern conventions
* Minor reformatting (spacing)
* Noted compatibility through WP2.8+
* Dropped support for pre-WP2.6
* Updated copyright date
* Updated screenshot

= 2.0 =
* Completely rewritten
* Now properly handles code embedded in comments
* Created its own class to encapsulate plugin functionality
* Added admin options page under Options -> Code Formatting (or in WP 2.5: Settings -> Code Formatting). Options are now saved to database, negating need to customize code within the plugin source file.
* Removed function anti_wptexturize() as the new handling approach negates its need
* Changed description; updated installation instructions
* Added compatibility note
* Updated copyright date
* Moved into its own subdirectory; added readme.txt and screenshot
* Tested compatibility with WP 2.3.3 and 2.5

= 0.9 =
* Initial release


== Upgrade Notice ==

= 3.5 =
Recommended update: fix bug where 'pre' tag could get wrapped in '<pre>' tag; added setting to disable preserving code in posts; added unit tests; updated plugin framework; compatibility now WP 3.6-3.8+

= 3.3 =
Minor update. Highlights: added setting to control if code should be preserved in posts; prevent 'pre' tag from getting wrapped in 'pre'; updated plugin framework.

= 3.2 =
Recommended update. Highlights: fixed bug with settings not appearing in MS; updated plugin framework; noted compatibility with WP 3.3+; dropped compatibility with versions of WP older than 3.1.

= 3.1 =
Recommended update. Highlights: fixed numerous bugs; added a debug mode; updated compatibility through WP 3.2; dropped compatibility with version of WP older than 3.0; updated plugin framework; and more.

= 3.0.1 =
Trivial update: updated plugin framework to v021; noted compatibility with WP 3.1+ and updated copyright date.

= 3.0 =
Recommended update. Highlights: re-implementation using custom plugin framework; full localization support; misc non-functionality documentation and formatting tweaks; renamed class; verified WP 3.0 compatibility; dropped support for versions of WP older than 2.8.
