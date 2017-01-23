<?php
/*
Plugin Name: Landing sites
Plugin URI: http://wordpress.org/extend/plugins/landing-sites/
Description: When visitors is referred to your site from a search engine, the plugin is showing them related posts to their search on your blog.
Version: 1.5.1
Author: devil1591
*/

if (function_exists('load_plugin_textdomain')) {
	load_plugin_textdomain('landing-sites', false, dirname(plugin_basename(__FILE__))); 
}

function ls_get_delim($ref) {
    // Search engine match array
    // Used for fast delimiter lookup for single host search engines.
    // Non .com Google/MSN/Yahoo referrals are checked for after this array is checked

    $search_engines = array(
		'google.fr' => 'q',
		'google.com' => 'q',
		'search.yahoo.com' => 'p',
		'fr.search.yahoo.com' => 'p',
		'bing.com' => 'q',
		'rechercher.aliceadsl.fr' => 'qs',
		'search.lycos.fr' => 'query',
		'search.lycos.com' => 'query',
		'recherche.aol.fr' => 'q',
		'search.aol.com' => 'q',
		'lemoteur.fr' => 'rdata',
		'sfr.fr' => 'q',
		'ask.com' => 'q',
		'hotbot.com' => 'query',
		'advertising.yahoo.com' => 'q',
		'search.netscape.com' => 'query',
		'looksmart.com' => 'key',
		'search.earthlink.net' => 'q',
		'mamma.com' => 'q'
	);

    $delim = false;

    // Check to see if we have a host match in our lookup array
    if (isset($search_engines[$ref])) {
        $delim = $search_engines[$ref];
    } else {
		// Lets check for referrals for international TLDs and sites with strange formats        
		if (strpos($ref, 'google.') !== false && strpos($ref, 'reader') === false)
			$delim = 'q';
		elseif (strpos($ref, '.search.yahoo.') !== false)
			$delim = 'q';
	    elseif (strpos($ref, 'exalead.') !== false)
			$delim = 'q';
		elseif (strpos($ref, 'search.aol.') !== false)
			$delim = 'query';
		elseif (strpos($ref, '.ask.com') !== false)
			$delim = 'q';
		elseif (strpos($ref, 'recherche.aol.fr') !== false)
			$delim = (strpos($_SERVER['HTTP_REFERER'], 'query')!==false)?'query':'q';
	}

    return $delim;
}

function ls_get_terms($d, $decode=true) {
    $terms       = null;
    $query_array = array();
    $query_terms = null;

    // Get raw query
    $query = explode($d.'=', $_SERVER['HTTP_REFERER']);
    $query = explode('&', $query[1]);
    $query = urldecode($query[0]);

    // Remove quotes, split into words, and format for HTML display
    $query = str_replace("'", '', $query);
    $query = str_replace('"', '', $query);
    $query_array = preg_split('/[\s,\+\.]+/',$query);
    $query_terms = implode(' ', $query_array);
	if ($decode == true) {
	    return htmlspecialchars(urldecode($query_terms));
	}
	return $query_terms;
}

function ls_get_refer() {
    // Break out quickly so we don't waste CPU cycles on non referrals
    if (!isset($_SERVER['HTTP_REFERER']) || ($_SERVER['HTTP_REFERER'] == '')) return false;

    $referer_info = parse_url($_SERVER['HTTP_REFERER']);
    $referer = $referer_info['host'];

    // Remove www. is it exists
    if (substr($referer, 0, 4) == 'www.')
        $referer = substr($referer, 4);

    return $referer;
}

function ls_related($limit=5, $len=10, $before_title='', $after_title='', $before_post='', $after_post='', $show_pass_post=false, $show_excerpt=false) {
    global $wpdb, $id;

    // Did we come from a search engine? 
    $referer = ls_get_refer();
    if (!$referer) return false;

    $delimiter = ls_get_delim($referer);

    if ($delimiter) { 
        $terms = $wpdb->escape(ls_get_terms($delimiter));

        $time_difference = get_option('gmt_offset');
        $now = gmdate('Y-m-d H:i:s', (time()+($time_difference*3600)));

        // Primary SQL query
        $sql = 'SELECT ID, post_title, post_content,'
				. "MATCH (post_title, post_content) AGAINST ('".$terms."') AS score "
				. 'FROM '.$wpdb->posts.' WHERE '
				. "MATCH (post_title, post_content) AGAINST ('".$terms."') "
				. "AND post_date <= '".$now."' "
				. "AND (post_status IN ('publish', 'static')) "
				. "AND post_type = 'post' ";
        if ($show_pass_post == false) {
			$sql .= "AND post_password = '' ";
		}
        $sql .= 'ORDER BY score DESC LIMIT '.$limit;
        $results = $wpdb->get_results($sql);
        $output = '';

        if ($results) {
            foreach ($results as $result) {
                $title = stripslashes(apply_filters('the_title', $result->post_title));
                $permalink = get_permalink($result->ID);
                $post_content = strip_tags($result->post_content);
                $post_content = stripslashes($post_content);
                $output .= $before_title.'<a href="'.$permalink.'" rel="bookmark" title="'.__('Permanent Link to:', 'landing-sites').$title.'">'.$title.'</a>'.$after_title;
                if ($show_excerpt == true) {
                    $words = explode(' ', $post_content); 
                    $post_strip = implode(' ', array_slice($words, 0, $len));
                    $output .= $before_post . $post_strip . $after_post;
                }
            }
            echo $output;
        } else {
            echo $before_title.__('No related posts.', 'landing-sites').$after_title;
        }
    }
	return false;
}

function ls_search_engines($before_title='', $after_title='') {
    // Search engine array
    // Used for linking to other search engines.
	$search_engines = array(
		'google.fr?q=' => 'Google',
		'fr.search.yahoo.com/search?q=' => 'Yahoo!',
		'bing.com/search?q=' => 'Bing',
		'www.exalead.fr/search/web/results/?q=' => 'Exalead',
		'fr.ask.com/web?q=' => 'Ask'
	);
	
	// Did we come from a search engine?
    $referer = ls_get_refer();
    if (!$referer) return false;

    $delimiter = ls_get_delim($referer);

    if ($delimiter) { 
        $terms = ls_get_terms($delimiter);
        $terms_url = ls_get_terms($delimiter, false);
		foreach ($search_engines as $url=>$name) {
			echo $before_title.'<a href="http://'.$url.$terms_url.'" rel="external"> '.$terms.__('on', 'landing-sites').' '.$name.'</a>'.$after_title;
		}
	}
	return false;
}

// Return true if the referer is a search engine
function ls_getinfo($what) {

    // Did we come from a search engine? 
    $referer = ls_get_refer();
    if (!$referer) return false;
    $delimiter = ls_get_delim($referer);

    if ($delimiter) { 
        $terms = ls_get_terms($delimiter);
        if ($what == 'isref') { 
			return ($terms != ''?true:false);
		}
        if ($what == 'referrer') {
            $parsed = parse_url($_SERVER['HTTP_REFERER']);
            echo '<a href="http://'.$parsed['host'].'">'.$parsed['host'].'</a>';
        }
        if ($what == 'terms') { echo $terms; }
    } 
} 

function ls_install() {
    global $wpdb;
    
    $wpdb->hide_errors();
    $wpdb->query('ALTER TABLE '.$wpdb->posts.' ENGINE = MYISAM;');
    $wpdb->query('ALTER TABLE '.$wpdb->posts.' ADD FULLTEXT post_related (post_title, post_content);');
    $wpdb->show_errors();
}

register_activation_hook(__FILE__, 'ls_install');

function ls_set_header() {
	if (ls_getinfo('isref')) header('Vary: Referer', false);
}

add_action('init', 'ls_set_header');

?>