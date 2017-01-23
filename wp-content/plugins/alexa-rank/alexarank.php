<?php
/*
Plugin Name: Alexa Rank
Plugin URI: http://www.coolcode.cn/show-152-1.html
Description: This plugin allows you add the Alexa Rank into your blog.
Version: 1.7
Author: andot,bawbaw
Author URI: http://www.coolcode.cn/
*/

class AlexaRank {
    var $pluginpath = "/wp-content/plugins/alex-arank";
    function AlexaRank() {
        $this->pluginpath = get_settings('siteurl') . $this->pluginpath;
    }
    
    function add_css() {
        echo "<link rel=\"stylesheet\" href=\"{$this->pluginpath}/alexarank.css\" />\n";
    }

    function add_js() {
        if ((!defined('PHPRPC_JS_CLIENT_LOADED')) || (PHPRPC_JS_CLIENT_LOADED == false)) {
            echo "<script type=\"text/javascript\" src=\"{$this->pluginpath}/phprpc_client.js\"></script>\n";
            define('PHPRPC_JS_CLIENT_LOADED', true);
        }
        echo "<script type=\"text/javascript\" src=\"{$this->pluginpath}/alexarank.js\"></script>\n";
    }
    function bar() {
        echo "<span id=\"alexa_container\">Alexa<a href=\"http://www.alexa.com/data/details/main?q=&amp;url=";
        echo urlencode(get_settings('siteurl')) . "\" style=\"display: inline; padding: 0; margin: 0\">";
        echo "<span id=\"alexa_bar\"><span id=\"alexa_border\"><span id=\"alexa_rank\"></span></span></span>\r\n";
        echo "</a></span>\r\n";
        echo "<script type=\"text/javascript\">AlexaRank();</script>\r\n";
    }
}

$AlexaRank = new AlexaRank();
add_action('wp_head', array(&$AlexaRank, 'add_css'));
add_action('wp_head', array(&$AlexaRank, 'add_js'));
?>