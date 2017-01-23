=== Plugin Name ===
Contributors: Joerg Schueler
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=schueler%40tionet%2ede&item_name=WpJsListPageShortCode%20Donation&no_shipping=0&no_note=1&tax=0&currency_code=EUR&lc=DE&bn=PP%2dDonationsBF&charset=UTF%2d8
Tags: ip, ipaddress, browser, os, location, about you, visitor, widget, sidebar
Requires at least: 2.7
Tested up to: 3.3
Stable tag: 1.23

Displays IP address, operating system, browser type and origin of the visitor by shortcode or as Widget in Sidebar.

== Description ==
Displays IP address, operating system, browser type and origin of the visitor by shortcode or as Widget in Sidebar.

**Usage**

1. Place the widget to your Sidebar.

2. You can use the shortcode [about-visitor] to include informations in your posts or pages.
optional Parameters:
ip=0 to hide IpAddress
os=0 to hide Operating System
browser=0 to hide the Browser
location=1 to show the location/origin
Examples:
List of default informations: [about-visitor]
List of default informations including location: [about-visitor location=1]
List only the IP Adress: [about-visitor os=0 browser=0]

3. Without sidebar you can display informations by including in php-code by using then function 'js_aboutvisitor_display'.
parameter array:
ip=>0 to hide IpAddress
os=>0 to hide Operating System
browser=>0 to hide the Browser
location=>1 to show the location/origin
Examples:
List of default informations: <?php if (function_exists('js_aboutvisitor_display')) { js_aboutvisitor_display(); } ?>
List of default informations including location: <?php if (function_exists('js_aboutvisitor_display')) { js_aboutvisitor_display(array(location=>1)); } ?>
List only the IP Adress: <?php if (function_exists('js_aboutvisitor_display')) { js_aboutvisitor_display(array(os=>0;browser=>0)); } ?>

== Installation ==

1. Download and unzip the last version of this plugin.
2. Upload the wp-js-about-visitor folder to ./wp-content/plugins/
3. Go to WP Admin panel > Plugins > activate "WP Js About Visitor".
4. Place the widget to your Sidebar or include function 'js_aboutvisitor_display' anywhere you want.

== Frequently Asked Questions ==

== Changelog ==

= 1.23 =
* fixed errors with php 5.3 or higher
* updated browser detection by Browscap.ini

= 1.22 =
* updated browser detection by Browscap.ini

= 1.21 =
* Belorussian Language by www.fatcow.com

= 1.2 =
* updated browser detection by Browscap.ini
* WP 2.9 support

= 1.1 =
* shortcode [about-visitor] added to include informations in posts or pages.
* function 'js_aboutvisitor_display' added to display in php-code.
* fixed some problems

= 1.01 =
* fixed problem if country location service is down.

= 1.0 =
* first release