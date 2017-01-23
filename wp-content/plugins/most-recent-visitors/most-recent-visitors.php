<?php
/*
Plugin Name: Most Recent Visitors
Plugin URI: http://www.kylogs.com/blog/archives/507.html
Description: Show most recently visitors on your siderbar widget
Author: Chen Ju
Version: 0.3
Author URI: http://www.kylogs.com/blog
*/ 

/*  Copyright 2008  Chen Ju  (email : sammy105@gmail.com)

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
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if (!class_exists('cj_latsted_visitors')) {
    class cj_latsted_visitors	{
		
		/**
		* @var string   The name the options are saved under in the database.
		*/
		var $adminOptionsName = "cj_latsted_visitors_options";
		
		
		/**
		* @var string   The name of the database table used by the plugin
		*/	
		var $db_table_name = "";
		var $bot=array('31.135.145.*',
			'61.135.145.*',
			'61.135.146.*',
			'159.226.50.*',
			'202.108.11.*',
			'202.108.22.*',
			'202.108.23.*',
			'202.108.249.*',
			'202.108.250.*',
			'220.181.19.*',
			'66.196.90.*',
			'66.196.91.*',
			'68.142.249.*',  
			'68.142.250.*',  
			'68.142.251.*',  
			'72.30.101.*',  
			'72.30.102.*',  
			'72.30.103.*',  
			'72.30.104.*',  
			'72.30.107.*',  
			'72.30.110.*',  
			'72.30.111.*',  
			'72.30.128.*',  
			'72.30.129.*',  
			'72.30.131.*',  
			'72.30.133.*',  
			'72.30.134.*',  
			'72.30.135.*',  
			'72.30.216.*',  
			'72.30.226.*',  
			'72.30.252.*',  
			'72.30.97.*',  
			'72.30.98.*',  
			'72.30.99.*',  
			'74.6.74.*',
			'74.6.8.*',
			'194.116.229.5',
			'202.165.102.*',
			'202.160.178.*',
			'202.160.179.*',
			'202.160.180.*',
			'202.160.181.*',
			'202.160.183.*',
			'64.233.161.*',
			'64.233.189.*',
			'66.102.11.*',
			'66.102.7.*',
			'66.102.9.*',
			'66.249.64.*',
			'66.249.65.*',
			'66.249.66.*',
			'66.249.71.*',
			'66.249.72.*',
			'72.14.207.*',
			'216.239.33.*',
			'216.239.35.*',
			'216.239.37.*',
			'216.239.39.*',
			'216.239.51.*',
			'216.239.53.*',
			'216.239.55.*',
			'216.239.57.*',
			'216.239.59.*',
			'65.55.209.110',
   			'65.55.209.119',
   			'65.55.209.120',
   			'65.55.209.121',
   			'65.55.209.122',
   			'65.55.209.123',
   			'65.55.209.125',
   			'65.55.209.127',
   			'65.55.209.128',
			'202.106.182.188',
			'65.55.109.*',
			'65.55.110.*',
			'202.108.7.202',
			'61.135.168.123',
			'213.56.56.190',
			'157.158.97.65',
			'202.85.54.90',
			'202.99.210.148',
			'201.254.118.134',
			'203.84.171.68',
			'72.55.153.109',
			'207.138.99.34',
			'201.27.196.190',
			'213.251.184.207',
			'203.82.52.210]',
			'80.63.1.213',
			'209.216.205.248',
			'203.169.119.178',
			'116.16.149.53',
			'124.126.1.116',
			'202.108.1.*'
		);

		var $adminOptions;
		/**
		* PHP 4 Compatible Constructor
		*/
		function cj_latsted_visitors(){$this->__construct();}
		
		/**
		* PHP 5 Constructor
		*/		
		function __construct(){
			global $wpdb;

    /**
    * In this version, we don't support settings on admin menu because we think it's not necessary 
    */
	//	add_action("admin_menu", array(&$this,"add_admin_pages"));
		
		
		
		register_activation_hook(__FILE__,array(&$this,"install_on_activation"));
		add_action("plugins_loaded",array(&$this,"register_widget_latest_visitors"));
		add_action("wp_head", array(&$this,"add_css"));
		
		// collect information about visitors;
		// we don't collect the same visitor twice.
		add_action('wp_head', array(&$this,"cj_latest_visitors_collect"));
    
		//	$this->adminOptions = $this->getAdminOptions();
		
		//*****************************************************************************************
		// These lines allow the plugin to be translated into different languages
		// You will need to create the appropriate language files
		// this assumes your language files will be in the format: latest_visitors-locationcode.mo
		// This also assumes your text domain will be: latest_visitors 
		// For more info: http://codex.wordpress.org/Translating_WordPress
		//*****************************************************************************************
		$latest_visitors_locale = get_locale();
		$latest_visitors_mofile = dirname(__FILE__) . "/languages/latest_visitors-".$latest_visitors_locale.".mo";
		load_textdomain("latest_visitors", $latest_visitors_mofile);
		
			$this->db_table_name = $wpdb->prefix . "cj_latest_visitors";

		}
		  
		
		
		
		/**
		* collect information about visitors;
		* 
		*/
		function cj_latest_visitors_collect(){
			//first, get the infor from db;
			global $wpdb;
			$max_recoder=20;
			$result = $wpdb->get_results("SELECT * FROM $this->db_table_name order by id DESC",ARRAY_A);
			$count = get_option('cj_latest_visitors_show');
			$len=count($result);
			$latest= $result[0]; 
			$oldest= $result[$len-1];						
			$from=$_SERVER['HTTP_REFERER'];
			$to=$_SERVER['REQUEST_URI'];
	        if(strpos($to,'trackback')&&!strpos($to,'tag')) return;
			$cj_tmie=time();
			$cj_ip=$_SERVER['REMOTE_ADDR'];
			//if($from==""||$from==' ') return;
			if($this->isBot($cj_ip)) return;
    	$cj_location=" ";//an place hold
    	
    	$to2=parse_url($to);
    	$to3=$to2['host'];
    	$si=parse_url(get_option('siteurl'));
    	$si=$si['host'];
		
		$ff=parse_url($from);
		$ff1=$ff['host'];
    	if($ff1==$si) return;
    	if(($cj_ip==$latest['ip'])){
					return;
				}
		
			if($len< $max_recoder){			
				 
				$insert = "INSERT INTO " . $this->db_table_name .
            " (fromurl, tourl, time, ip, location) " .
            "VALUES ('" . $from . "', '" . $wpdb->escape($to) . "', '" . time() . "', '" . $cj_ip . "', '" . $cj_location . "');";
         $rr= $wpdb->query( $insert );
         
        return;
       }
      $dd=$oldest['id'];
      
			$wpdb->query("DELETE FROM $this->db_table_name WHERE id=$dd");
			
      $insert = "INSERT INTO " . $this->db_table_name .
            " (fromurl, tourl, time, ip, location) " .
            "VALUES ('" . $wpdb->escape($from) . "','" . $wpdb->escape($to) . "','" . time() . "','" . $cj_ip . "','" . $cj_location . "')";
      $rr=$wpdb->query( $insert );
     }

		
		/**
		* Retrieves the options from the database.
		* @return array
		*/
		function getAdminOptions() {
		$adminOptions = array("cj_latest_visitors_title" => "Value",
		"cj_latest_visitors_show" => "Value");
		$savedOptions = get_option($this->adminOptionsName);
		if (!empty($savedOptions)) {
			foreach ($savedOptions as $key => $option) {
				$adminOptions[$key] = $option;
			}
		}
		update_option($this->adminOptionsName, $adminOptions);
		return $adminOptions;
		}
		
		/**
		* Saves the admin options to the database.
		*/
		function saveAdminOptions(){
			update_option($this->adminOptionsName, $this->adminOptions);
		}
		
		function add_admin_pages(){
				add_submenu_page('options-general.php', "Latest Visitors", "Latest Visitors", 10, "Latest Visitors", array(&$this,"output_sub_admin_page_0"));
		}
		
		/**
		* Outputs the HTML for the admin sub page.
		*/
		function output_sub_admin_page_0(){
			?>
			<div class="wrap">
				<h2>Admin Menu Placeholder for Latest Visitors a subpage of 'options-general.php'</h2>
				<p>You can modify the content that is output to this page by modifying the method <strong>output_sub_admin_page_0</strong></p>
			</div>
			<?php
		} 
		
		/**
		* Creates or updates the database table, and adds a database table version number to the WordPress options.
		*/
		function install_on_activation() {
			global $wpdb;
			$plugin_db_version = "0.8";
			
			$installed_ver = get_option( "latest_visitors_db_version" );
			//only run installation if not installed or if previous version installed
		//	if ($installed_ver === false || $installed_ver != $plugin_db_version) {
		
				//*****************************************************************************************
				// Create the sql - You will need to edit this to include the columns you need
				// Using the dbdelta function to allow the table to be updated if this is an update.
				// Read the limitations of the dbdelta function here: http://codex.wordpress.org/Creating_Tables_with_Plugins
				// remember to update the version number every time you want to make a change.
				//*****************************************************************************************
			           		$sql = "CREATE TABLE " . $this->db_table_name . " (
										fromurl VARCHAR(255),
										tourl   VARCHAR(255),
										time bigint(11),
										ip   VARCHAR(255),
										id mediumint(9) NOT NULL AUTO_INCREMENT,
										location VARCHAR(255),
										UNIQUE KEY id (id)
															);";
			
 
			

			
				require_once(ABSPATH . "wp-admin/upgrade-functions.php");
				dbDelta($sql);
				//add a database version number for future upgrade purposes
				update_option("latest_visitors_db_version", $plugin_db_version);
		//	}
			
			// add options
			add_option('latest_visitors_db_version',$plugin_db_version);
			add_option("cj_latest_visitors_title","Most Recent Visitors");
			add_option("cj_latest_visitors_show","5");
			add_option("cj_latest_visitors_ip","true");
			//add_option("cj_latest_visitors_")
			
		}
		
		/**
		* Registers the widget and the widget control for use
		*/
		function register_widget_latest_visitors($args) {
			register_sidebar_widget("Latest Visitors",array(&$this,"widget_latest_visitors"));
			register_widget_control("Latest Visitors",array(&$this,"widget_latest_visitors_control"),500,400);
		}
		
		
		/**
		* Contains the widget logic
		*/
		function widget_latest_visitors($args) {
			global $wpdb;
			extract($args);
			$num=intval(get_option('cj_latest_visitors_show'));
			
			if($num>20) $num=20;
			$result = $wpdb->get_results("SELECT * FROM $this->db_table_name order by id DESC",ARRAY_A);
			$count = get_option('cj_latest_visitors_show');
			$cj_ip=get_option('cj_latest_visitors_ip');
			$len=count($result);
			if($num>$len) $num=$len;
			?>
			<?php echo $before_widget; ?>
			<?php echo $before_title . get_option("cj_latest_visitors_title") . $after_title; ?>
			
			<div class="cj_latest_visitors">
					<p>
							<ul class="cj_uls">
								
								<?php
											$cl="cj_single";
											$day;
											$hour;
											$minute;
											$second;
											$inv;
											for($i=0;$i< $num ; $i++) {
												$value=$result[$i];
												$to_rel=$value['tourl'];
												$to_ser=get_option('siteurl');
												$temp=parse_url($to_ser);
												$tourl='http://'.$temp['host'].$to_rel;
												$inv=time()-$value['time'];
												$day=(int)($inv/3600/24);
											//	$inv=$inv-$day*3600*24;
												$hour=(int)($inv/3600);
											//	$inv=$inv%3600;												
												$minute=(int)($inv/60);
												$second=$inv%60;
												$f=parse_url($value['fromurl']);
												$f1=$f['host'];
												$t=parse_url($value['tourl']);
												$t1=$t['host'];
												$cj_w='';
												
												//$cj_w.='';
												if($cj_ip=='true'){
												$cj_w.='<li class=cj_li>[<a href=http://www.whois-search.com/whois/'.$value['ip'];
												$cj_w.='>'.$value['ip'].'</a>] AT   ';
												}else {
													$cj_w.='<li class=cj_li>[Guest ] AT ';
												}
												
												$cj_w.='<span class=cj_left>';
												$sign=-1;
												if($day>0) {$cj_w.=round($day,2).' day(s)'; $sign=1;}
												else if($sign==-1&&$hour-0>0.001) {$cj_w.=round($hour,2).' hour(s)';$sign=1; }
												else if($sign==-1&&$minute-0>0.001) {$cj_w.=round($minute,1).' minute(s)';$sign=1; }
												else if($sign==-1) {$cj_w.=round($second,1).' second(s)'; }
												$cj_w.=' ago</span><br/>';
												$cj_w.=' from <a class=from href=';
												if($f1==""||$f1==" "){
													$cj_w.='> Unknown</a><br/>';
												}else{									
													$cj_w.=$value['fromurl'].'>';
													$cj_w.=$f1.'</a> <br/>';
												}
												$cj_w.='visited <a href=';
												$cj_w.=$tourl;
												$cj_w.='> here</a>';
												
												$cj_w.='</li><br/>';
												if($cl=='cj_single') $cl='cj_double';
												else $cl='cj_single';
												echo $cj_w;
											}
								?>
							
							</ul>
				  </p>
		  </div>
			<?php echo $after_widget; ?>
			<?php
		}
		
		
		/**
		* Contains the widget control html
		*/
		function widget_latest_visitors_control() {
		if ( $_POST["latest_visitors_submit"]=='1' ) {
			update_option('cj_latest_visitors_title',$_POST['cj_title']);
			update_option('cj_latest_visitors_show',$_POST['cj_show']);
			if($_POST['cj_ip']==true) update_option('cj_latest_visitors_ip','true');
			else	update_option('cj_latest_visitors_ip','false');
		}
		$title=get_option('cj_latest_visitors_title');
		$show=get_option('cj_latest_visitors_show');
		$ip=get_option('cj_latest_visitors_ip');
		$checked='';
		if($ip=='true') $checked='checked';
		
		?>
	
		<p style="text-align:left;"><label for="visitor-title">Title: <input class="widefat" style="width: 200px;" id="cj_title" name="cj_title" type="text" value="<?php  echo $title ?>" /></label></p>
    <p style="text-align:left;"><label for="visitor-title">The number of visitors to show: <input class="widefat" style="width: 200px;" id="cj_show" name="cj_show" type="text" value="<?php  echo $show ?>" /></label></p>
    <p style="text-align:left;"><label for="visitor-ip">Show Guest's ip on sidebar: <input  id="cj_ip" name="cj_ip" type="checkbox" <?php  echo $checked ?> /></label></p>
 
		<!--//*****************************************************************************************-->
		<!-- // Include your form here to collect the widget options-->
		<!-- // Pre-fix all your form elements with widgetizer-->
		<!-- // Do not include a <form> tag or a submit button, they will be handled for you-->
		<!--//*****************************************************************************************-->
		<!-- This is required, so we know that it is our widget options that are being changed -->
		<input type="hidden" id="latest_visitors_submit" name="latest_visitors_submit" value="1" />
		<?php
		}
		
		/**
		* Adds a link to the stylesheet to the header
		*/
		function add_css(){
		echo '<link rel="stylesheet" href="'.get_bloginfo('wpurl').'/wp-content/plugins/most-recent-visitors/css/style.css" type="text/css" media="screen"  />'; 
		}
		function isBot($s){
		$temp='';
		$arr=explode('.',$s);
		$temp=$arr[0].'.'.$arr[1].'.'.$arr[2].'.*';
		$flag=false;
		//yahoo,baidu,google
		if(ini_get('browscap')) {
			$browser= get_browser(NULL, true);
			if($browser['crawler']) {
				return true;
				}
        }

        if (isset($_SERVER['HTTP_USER_AGENT'])){
           $agent= $_SERVER['HTTP_USER_AGENT'];
           $crawlers= array(
            "/Googlebot/",
            "/Yahoo! Slurp;/",
            "/msnbot/",
            "/Mediapartners-Google/",
            "/Scooter/",
            "/Yahoo-MMCrawler/",
            "/FAST-WebCrawler/",
            "/Yahoo-MMCrawler/",
            "/Yahoo! Slurp/",
            "/FAST-WebCrawler/",
            "/FAST Enterprise Crawler/",
            "/grub-client-/",
            "/MSIECrawler/",
            "/NPBot/",
            "/NameProtect/i",
            "/ZyBorg/i",
            "/worio bot heritrix/i",
            "/Ask Jeeves/",
            "/libwww-perl/i",
            "/Gigabot/i",
            "/bot@bot.bot/i",
            "/SeznamBot/i",
			"/bot/",
			"/Bot/",
           );
         foreach($crawlers as $c) {
            if(preg_match($c, $agent)) {
                return true;
             }
          }
         }

		foreach($this->bot as $value){
			if($value==$s) return true;
			if($value==$temp) return true;
		}
		return false;
	
	}

    }
	
}

//instantiate the class
if (class_exists('cj_latsted_visitors')) {
	$cj_latsted_visitors = new cj_latsted_visitors();
}
?>