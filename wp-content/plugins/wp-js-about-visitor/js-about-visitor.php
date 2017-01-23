<?php
/*
Plugin Name: WP Js About Visitor
Plugin URI: http://www.joergschueler.de/dev/wp/about-visitor/
Description: Displays IP address, operating system, browser type and origin of the visitor by shortcode or as Widget in Sidebar.
Author: Joerg Schueler
Author URI: http://www.joergschueler.de/
Version: 1.23
*/

function php_get_browser($agent = NULL) {
	$agent = $agent?$agent:$_SERVER['HTTP_USER_AGENT'];
         $yu = array();
	$q_s = array("#\.#","#\*#","#\?#");
	$q_r = array("\.",".*",".?");

	if (version_compare(phpversion(), '5.3.0', '>=')) {
		$brows = parse_ini_file(str_replace(ABSPATH, '', dirname(__FILE__))."/php_browscap.ini",true,INI_SCANNER_RAW);
	} else {
		$brows = parse_ini_file(str_replace(ABSPATH, '', dirname(__FILE__))."/php_browscap.ini",true);
	}
         foreach($brows as $k => $t) {
		if(fnmatch($k,$agent)) {
			$yu['browser_name_pattern'] = $k;
			$pat = preg_replace($q_s,$q_r,$k);
			$yu['browser_name_regex'] = strtolower("^$pat$");
			foreach($brows as $g=>$r) {
				if ($t['Parent'] == $g) {
					foreach($brows as $a => $b) {
						if ($r['Parent'] == $a) {
							$yu = array_merge($yu,$b,$r,$t);
							foreach($yu as $d => $z) {
								$l = strtolower($d);
								$hu[$l] = $z;
							}
						}
					}
				}
			}
		  break;
		}
	}
  return $hu;
}

function php_get_location($ip = ''){
         $re = '<acronym title="' . __('Service not available.', 'js-about-visitor') . '">--</arconym>';
         if (preg_match('/\b(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\b/',$ip,$mts)) {
		$ctx = stream_context_create(array('http' => array('timeout' => 2)));
		$location = file_get_contents("http://api.hostip.info/country.php?ip=".$ip, False, $ctx);
                 if ((strlen($location) > 0) && (strlen($location) < 4)) {
                 	$re = '<a href="http://www.hostip.info">' . $location . '</a>';
                 }
	}
  return $re;
}

function js_aboutvisitor_check_options($args) {
	$def = array(
		'title' => __('About You', 'js-about-visitor'),
		'ip' => '1',
		'os' => '1',
		'browser' => '1',
		'location' => '0'
		);
         if (!is_array($args)) { $args = array(); }
  return array_merge($def, $args);
}

function js_aboutvisitor_userlist($attr) {
         $options = js_aboutvisitor_check_options($attr);
         $out = "<ul>";
	if (($options['ip'] == 1) || ($options['location'] == 1)) {
		if ($ip_address = $_SERVER["HTTP_X_FORWARDED_FOR"]) {
			if (strpos($ip_address, ',') !== false) {
				$ip_address = explode(',', $ip_address);
				$ip_address = $ip_address[0];
			}
		} else {
			$ip_address = $_SERVER["REMOTE_ADDR"];
		}
		if ($options['ip'] == 1) {
			$out .= "<li>" . __('IP', 'js-about-visitor') . ": <b>" . $ip_address . "</b></li>";
		}
		if ($options['location'] == 1) {
         		$out .= "<li>" . __('Location', 'js-about-visitor') . ": <b>" . php_get_location($ip_address) . "</b></li>";
		}
	}
	if (($options['os'] == 1) || ($options['browser'] == 1)) {
		$browser = php_get_browser(null);
		if (($options['browser'] == 1) && ($browser[parent] != "")) {
			$out .= "<li>" . __('Browser', 'js-about-visitor') . ": <b>" . $browser[parent] . "</b></li>";
                 }
                 if (($options['os'] == 1) && ($browser[platform] != "")) {
			$out .= "<li>" . __('OS', 'js-about-visitor') . ": <b>" . $browser[platform] . "</b></li>";
		}
	}
	$out .= "</ul>";
  return $out;
}

function js_aboutvisitor_shortcode($atts, $content, $tag) {
	global $post;
         $def = js_aboutvisitor_check_options(array('class' => $tag));
	$atts = shortcode_atts($def, $atts); // merge user params with defaults
         if($tag == 'about-visitor') {
         	$out = js_aboutvisitor_userlist($atts);
         }
  return $out;
}

function widget_js_aboutvisitor($args) {
	extract($args);
         $options = js_aboutvisitor_check_options(get_option("js_about_visitor"));
	echo $before_widget;
	echo $before_title;
	echo $options['title'];
	echo $after_title;

         echo js_aboutvisitor_userlist($options);

         echo $after_widget;
}

function widget_js_aboutvisitor_control() {
         $options = js_aboutvisitor_check_options(get_option("js_about_visitor"));
	if ($_POST['js_aboutvisitor-Submit']) {
		$options['title'] = htmlspecialchars($_POST['js_aboutvisitor-WidgetTitle']);
		$options['ip'] = (isset($_POST['js_aboutvisitor-WidgetIp'])) ? "1" : "0";
		$options['os'] = (isset($_POST['js_aboutvisitor-WidgetOs'])) ? "1" : "0";
		$options['browser'] = (isset($_POST['js_aboutvisitor-WidgetBrowser'])) ? "1" : "0";
		$options['location'] = (isset($_POST['js_aboutvisitor-WidgetLocation'])) ? "1" : "0";
		update_option("js_about_visitor", $options);
	}
?>
	<p style="text-align: left;"><?php echo __('Widget Title', 'js-about-visitor') ?>:
         <input type="text" id="widgettitle" name="js_aboutvisitor-WidgetTitle" value="<?php echo $options['title'];?>" /></p>
         <input type="checkbox" <?php if($options['ip'] == 1) echo "checked=\"checked\""; ?>" value="1" id="js_aboutvisitor-WidgetIp" name="js_aboutvisitor-WidgetIp"/> <?php echo __('IP-Address', 'js-about-visitor') ?><br>
         <input type="checkbox" <?php if($options['location'] == 1) echo "checked=\"checked\""; ?>" value="1" id="js_aboutvisitor-WidgetLocation" name="js_aboutvisitor-WidgetLocation"/> <?php echo __('Location', 'js-about-visitor') ?><br>
	<input type="checkbox" <?php if($options['browser'] == 1) echo "checked=\"checked\""; ?>" value="1" id="js_aboutvisitor-WidgetBrowser" name="js_aboutvisitor-WidgetBrowser"/> <?php echo __('Browser', 'js-about-visitor') ?><br>
	<input type="checkbox" <?php if($options['os'] == 1) echo "checked=\"checked\""; ?>" value="1" id="js_aboutvisitor-WidgetOs" name="js_aboutvisitor-WidgetOs"/> <?php echo __('OS', 'js-about-visitor') ?><br>
	<input type="hidden" id="js_aboutvisitor-Submit" name="js_aboutvisitor-Submit" value="1" />
<?php
}

function js_aboutvisitor_init() {
	load_plugin_textdomain('js-about-visitor', str_replace(ABSPATH, '', dirname(__FILE__)), dirname(plugin_basename(__FILE__)));
         /*load_plugin_textdomain('js-about-visitor', 'wp-content/plugins/wp-js-about-visitor');*/
	register_sidebar_widget('JS About Visitor', 'widget_js_aboutvisitor');
	register_widget_control('JS About Visitor', 'widget_js_aboutvisitor_control', 250, 100 );
}

function js_aboutvisitor_display( $args = array() ) {
	echo js_aboutvisitor_userlist( $args );
}

add_action('init', 'js_aboutvisitor_init');

add_shortcode('about-visitor', 	'js_aboutvisitor_shortcode');

?>