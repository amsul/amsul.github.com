<?php
/*
Plugin Name: WassUp
Plugin URI: http://www.wpwp.org
Description: Analyze your visitors traffic with real-time stats, charts, and a lot of chronological information. Includes a sidebar widget of current online visitors and other statistics and an admin dashboard widget with chart. For Wordpress 2.2 or higher. Caution: don't upgrade when your site is busy!
Version: 1.8.3
Author: Michele Marcucci, Helene Duncker
Author URI: http://www.michelem.org/
Disclaimer: Use at your own risk. No warranty expressed or implied is provided.

Copyright (c) 2007-2011 Michele Marcucci
Released under the GNU General Public License (GPL)
http://www.gnu.org/licenses/gpl.txt
*/

//# Stop any attempt to call "wassup.php" directly.  -Helene D. 2008-01-27, 2010-10-20.
if (!defined('ABSPATH')) {
	header('HTTP/1.1 403 Forbidden');
	die('Illegal request - Permission Denied!');
} elseif (preg_match('#'.basename(__FILE__) .'#', $_SERVER['PHP_SELF'])) { 
	header('HTTP/1.1 403 Forbidden');
	wp_die('<strong>Permission Denied!</strong> You are not allowed to call this page directly.');
}
//wassup globals & constants
global $wp_version, $current_user, $user_level, $wassup_options;
$wassupversion="1.8.3";
$wassup_cookie_value="";
$wdebug_mode=false;	//turn on debugging (global)...Use cautiously! Will display errors from all plugins, not just WassUp
define('WASSUPDIR', dirname(__FILE__)); 	//new constant in v1.8
define('WASSUPFOLDER', plugin_basename(dirname(__FILE__)));
require_once(WASSUPDIR.'/lib/wassup.class.php');
require_once(WASSUPDIR.'/lib/main.php');
include_once(WASSUPDIR.'/lib/uadetector.class.php');

//WassUp works only in WP2.2 or higher
if (version_compare($wp_version, '2.2', '<')) {
	if (function_exists('deactivate_plugins')) {
		deactivate_plugins(__FILE__);
	}
	wp_die( '<strong style="color:#c00;background-color:#dff;padding:5px;">'.__("Sorry, WassUp requires WordPress 2.2 or higher to work","wassup").'.</strong>');
} elseif (version_compare($wp_version,'2.6','<')) {
	include_once(WASSUPDIR.'/lib/compat_functions.php');
}
//flexible "wp-content" paths since WordPress 2.6 -Helene D. 2009-04-04
$wassupurl = plugins_url(WASSUPFOLDER);
define('WASSUPURL', $wassupurl);
unset($wassupurl);	//to free memory

//check for valid export request
if (is_admin()) {
	if (isset($_GET['export']) && isset($_GET['whash'])) {
		$wassup_options = new wassupOptions;
		if ($_GET['whash'] == $wassup_options->whash) {
			export_wassup();
		}
	}
}

/**
 * add initial options and create table when Wassup activated
 *  -Helene D. 2/26/08, 2010-04-27.
 */
function wassup_install() {
	global $wpdb, $wassupversion, $wassup_options;

	//#Add/update wassup settings in Wordpress options table
	$wassup_options = new wassupOptions; //#settings initialized here
	$wassup_table = (!empty($wassup_options->wassup_table))? $wassup_options->wassup_table : $wpdb->prefix . "wassup";
	$wassup_meta_table = $wassup_table . "_meta";

	//# wassup should not be active during install
	$wassup_options->wassup_active = 0;

	//# reset hash
	$whash = $wassup_options->get_wp_hash();
	if (!empty($whash)) {
		$wassup_options->whash = $whash;
	}
	//# Add timestamp to optimize table once daily, start in 24 hours
	$wassup_options->wassup_optimize = current_time('timestamp')+(24*60*60);

        //# clear temporary values, wmark and wip, and wassup_alert_message
        $wassup_options->wmark = 0;     //#no preservation of delete/mark
        $wassup_options->wip = null;
        $wassup_options->wassup_alert_message = ""; //clear old messages

	//# initialize settings for 'spamcheck'
	if (empty($wassup_options->wassup_spamcheck)) {
		$wassup_options->wassup_spamcheck = "0";
		//#set wassup_spamcheck=1 if either wassup_refspam=1 or wassup_spam=1
		if ( $wassup_options->wassup_spam == "1" || $wassup_options->wassup_refspam == "1" ) { 
			$wassup_options->wassup_spamcheck = "1";
		}
	}
	if (empty($wassup_options->wassup_table)) {
		$wassup_options->wassup_table = $wassup_table;
	}
	//wassup_cache for caching of charts and geoip data
	//...wassup_cache automatically disabled for pre-1.8 wassup users
	if (!empty($wassup_options->wassup_version) && version_compare($wassup_options->wassup_version,'1.8','<')) {
		$wassup_options->wassup_cache = 0;	//disabled
	}
	// Save settings before table create/upgrade...
	$wassup_options->saveSettings();

	//#Install/upgrade tables in "upgrade.php" module
	if (file_exists(WASSUPDIR.'/lib/upgrade.php')) {
		require_once(WASSUPDIR.'/lib/upgrade.php');
	} else {
		echo "file: ".WASSUPDIR.'/lib/upgrade.php does not exist!';
		exit(1);
	}
	$wsuccess = wassup_tableInstaller();
	//double-check that main table was installed
	if ($wsuccess){ 
		$wassup_options->wassup_alert_message = "Wassup $wassupversion: ".__("Database created/upgraded successfully","wassup"); //debug
	} else {
		$wassup_options->wassup_alert_message = "Wassup $wassupversion: ".__("An error occured during the upgrade. WassUp table structure may not have been updated properly.","wassup"); //debug
	}
	if ($wpdb->get_var("SHOW TABLES LIKE '{$wassup_table}'") == $wassup_table) { 
		//Reset 'dbengine' MySQL setting with each upgrade...because host server settings can change
		if (!empty($wassup_options->wassup_version)) {	//upgrade only
			$wassup_options->wassup_dbengine = $wassup_options->getMySQLsetting('engine');
		}
		//turn off wassup_cache if meta table does not exist
		if ($wassup_options->wassup_cache == 1) {
			if ($wpdb->get_var("SHOW TABLES LIKE \'$wassup_meta_table\'") != $wassup_meta_table) { 
				$wassup_options->wassup_cache = 0;
			}
		}

		//show warning when 'WP_CACHE' constant is set
		if (wassup_compatCheck("WP_CACHE") == true) {
			$wassup_options->wassup_alert_message = '<strong style="color:#c00;padding:5px;">'.__("WassUp cannot generate accurate statistics with page caching enabled.","wassup")." ".__("If your cache plugin stores whole Wordpress pages/posts as HTML documents, then WassUp won't run properly. Please deactivate your cache plugin and remove \"WP_CACHE\" from \"wp_config.php\".","wassup").'</strong>';
		}
		//TODO: Show warning when 'wp_footer()' does not exist in active theme template (non-cache setups only)

		//#Since v1.7: put current version# in options after update
		$wassup_options->wassup_version = $wassupversion;

		$wassup_options->wassup_active = 1;  //start recording
		$wassup_options->saveSettings();

	} else {
		//main table not created - exit with error
		delete_option('wassup_settings');
		if (function_exists('deactivate_plugins')) {
			deactivate_plugins(__FILE__);
		}
		echo '<strong style="color:#c00;padding:5px;">'.__("An error occured during WassUp table install","wassup").'</strong>'.". <br/>debug: return code: ".(int)$wsuccess." <br/>wassup table: $wassup_table &nbsp; meta table: $wassup_meta_table";
		exit(1);	//exit with error
	}
} //end function

//set global variables 
$wassup_options = new wassupOptions; 

/**
 * Stop recording visitor hits, if 'wassup_uninstall' option is set, 
 *   completely remove all wassup tables and options from Wordpress (when 
 *   plugin is deactivated). -Helene D. 2008-02-26
 * New v1.8: no Wassup functions or classes are used during 'uninstall'
 */
function wassup_uninstall() {
	global $wpdb, $wp_version;

	$wassup_settings = get_option('wassup_settings');
	//first, stop recording
	if (!empty($wassup_settings['wassup_active'])) {
		$wassup_settings['wassup_active'] = "0";
		update_option('wassup_settings', $wassup_settings);
	}
	//remove wassup tables and options
	if (!empty($wassup_settings['wassup_uninstall']) || !is_array($wassup_settings)) {
		//remove wassup widgets from wordpress ?
		remove_action("widgets_init", "wassup_widget_init");
		if (version_compare($wp_version, '2.7', '<')) {
			remove_action('activity_box_end', 'wassupDashChart');
		} else {
			remove_action('wp_dashboard_setup', 'wassup_add_dashboard_widgets');
		}
		//purge wassup tables- WARNING: this is a permanent erase!!
		$wassup_table = (empty($wassup_settings['wassup_table'])?$wpdb->prefix.'wassup': $wassup_settings['wassup_table']);
		$table_tmp_name = $wassup_table."_tmp";
		$table_meta_name = $wassup_table."_meta";
		//$wpdb->query("DROP TABLE IF EXISTS $wassup_table"); //incorrectly causes an activation error in Wordpress
		//$wpdb->query("DROP TABLE IF EXISTS $table_tmp_name"); //incorrectly causes an activation error in Wordpress
		mysql_query("DROP TABLE IF EXISTS $table_meta_name");
		mysql_query("DROP TABLE IF EXISTS $table_tmp_name");
		mysql_query("DROP TABLE IF EXISTS $wassup_table");

		delete_option('wassup_settings');
	}
} //end function wassup_uninstall

/**
 * Output javascript in page head for wassup tracking 
 * @param none 
 * @return none;
 */
function wassup_head() {
	global $wassup_options, $wassupversion, $wscreen_res, $current_user;

	//if ($wassup_options->wassup_active == "1") { //redundant, in hook
	//Since v.1.8: removed meta tag to reduce plugin bloat
	//print '<meta name="wassup-version" content="'.$wassupversion.'" />'."\n";
	//add screen resolution javascript to blog header
	if ($wscreen_res == "" && isset($_COOKIE['wassup_screen_res'])) {
		$wscreen_res = attribute_escape(trim($_COOKIE['wassup_screen_res']));
		if ($wscreen_res == "x") $wscreen_res = "";
	}
	if (empty($wscreen_res) && isset($_SERVER['HTTP_UA_PIXELS'])) {
		//resolution in IE/IEMobile header sometimes
		$wscreen_res = str_replace('X',' x ',$_SERVER['HTTP_UA_PIXELS']);
	}
	if (empty($wscreen_res) && isset($_COOKIE['wassup'])) {
		$cookie_data = explode('::',attribute_escape(base64_decode(urldecode($_COOKIE['wassup']))));
		$wscreen_res=(!empty($cookie_data[2]))?$cookie_data[2]:"";
	}
	//Get visitor's screen resolution using javascript and a cookie.
	// - Added here so javascript code is placed in document <head> 
	//   to store this client-side only variable in a cookie that PHP
	//   can read.  -Helene D. 2009-01-19 ?>
<script type="text/javascript">
//<![CDATA[
	var screen_res = "<?php echo $wscreen_res; ?>"; 
<?php
	if (empty($wscreen_res) && !isset($_COOKIE['wassup_screen_res'])) { ?>
	function writeCookie(name,value,hours) {
		var the_cookie = name+"="+escape(value)+"; expires=";
		var expires = "";
		hours=hours+0; //convert to number
		if (hours > 0) { //0==expires on browser close
			var cdate = new Date();
			cdate.setTime(cdate.getTime()+(hours*60*60*1000));
			expires = expires+cdate.toGMTString();
		} <?php
		if (defined('COOKIE_DOMAIN')) {
			$cookiedomain = COOKIE_DOMAIN;
			$cookiepath = "/";
		} else {
			$cookieurl = parse_url(get_option('home'));
			$cookiedomain = preg_replace('/^www\./','',$cookieurl['host']);
			$cookiepath = $cookieurl['path'];
		}
		echo "\n\t\t".'document.cookie = the_cookie+expires+"; path='.$cookiepath.'; domain='.$cookiedomain.'";'."\n"; ?>
	}
	screen_res = screen.width+" x "+screen.height;
	if (screen_res==" x ") screen_res = window.screen.width+" x "+window.screen.height;
	if (screen_res==" x ") screen_res = screen.availWidth+" x "+screen.availHeight;
	if (screen_res!=" x ") { 
		writeCookie("wassup_screen_res",screen_res,"48"); //keep 2 days
	} else {
		screen_res = "";
	}
<?php
	} //end if !isset('wassup_screen_res')
?>
//]]>
</script><?php
	echo "\n";
	//} // end if wassup_active == "1"
} //end function wassup_head

//# Wassup init hook actions performed before headers are sent: 
//#   -Load jquery AJAX library and dependent javascripts for admin menus
//#   -Load language/localization files for admin menus and widget
//#   -Set 'wassup' cookie for new visitor hits when wassup is active
function wassup_init() {
	global $wp_version, $wassup_options;

	//block any obvious sql injection attempts involving WassUp -Helene D. 2009-06-16
	$request_uri = $_SERVER['REQUEST_URI'];
	if (!$request_uri) $request_uri = $_SERVER['SCRIPT_NAME']; // IIS
	if (stristr($request_uri,'wassup')!==FALSE || (isset($_SERVER['HTTP_REFERER']) && stristr($_SERVER['HTTP_REFERER'],'wassup')!==FALSE)) {
		if (preg_match('/[&?].+\=(\-(1|9)+|.*(select|update|delete|alter|drop|union|create)[ %&].*(?:from)?.*wp_\w+)/i',str_replace(array('\\','&#92;','"','%22','&#34;','&quot','&#39;','\'','`','&#96;'),'',$request_uri))>0) {
   			header("HTTP/1.1 403 Forbidden");
			wp_die('Illegal request - Permission Denied!');
		} elseif (preg_match('/(<|&lt;|&#60;|%3C)script[^a-z0-9]/i',$_SERVER['REQUEST_URI'])>0) {
   			header("HTTP/1.1 403 Forbidden");
			wp_die('Illegal request - Permission Denied!');
		}
	}

	//### Add wassup scripts to Wassup Admin pages...
	if (is_admin()) {
		if (!empty($_GET['page']) && stristr($_GET['page'],'wassup') !== FALSE) {
			//NOTE: jquery, ui.tabs, and thickbox built into Wordpress since 2.7
			if (file_exists(WASSUPDIR.'/js/thickbox/thickbox.js')) {
				wp_deregister_script('thickbox');
			}
			if (version_compare($wp_version, '2.7', '<') && file_exists(WASSUPDIR.'/js/jquery.js') ) {
				if (function_exists('wp_deregister_script')) {
					wp_deregister_script('jquery');	
				}
				// the safe way to load jquery into WP
				wp_register_script('jquery', WASSUPURL.'/js/jquery.min.js',FALSE,'1.6.2'); 
			} 
			if ($_GET['page'] == "wassup-spia") {
				//the safe way to load a jquery dependent script
				wp_enqueue_script('spia', WASSUPURL.'/js/spia.js', array('jquery'), '1.4');
			} elseif($_GET['page'] == "wassup-options") {
				//if (version_compare($wp_version, '2.7', '<')) {
					wp_enqueue_script('jqueryui', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js', array('jquery'), '1.8.16');
				//} else {
				//	wp_enqueue_script('ui.core', array('jquery'));
				//	wp_enqueue_script('ui.tabs', array('jquery'));
				//}
			} 
			//the safe way to load a jquery dependent script
			if (file_exists(WASSUPDIR.'/js/thickbox/thickbox.js')) {
				wp_enqueue_script('thickbox', WASSUPURL.'/js/thickbox/thickbox.js', array('jquery'), '3');
			}
		}
		//Loading language file...
		//Doesn't work if the plugin file has its own directory.
		//Let's make it our way... load_plugin_textdomain() searches only in the wp-content/plugins dir.
		$currentLocale = get_locale();
		if(!empty($currentLocale)) {
			$moFile = dirname(__FILE__) . "/language/" . $currentLocale . ".mo";
			if(@file_exists($moFile) && is_readable($moFile)) load_textdomain('wassup', $moFile);
		}
	} //if is_admin
	//Set Wassup cookie for visitor hits before headers are sent
	if (!empty($wassup_options->wassup_active)) {
		wassupPrepend();
	}
} // end function wassup_init

//### Wassup Admin functions
//For improved WassUp performance, restrict admin hooks and 
// admin functions to admin pages only
if (is_admin()) {

//Add the wassup stylesheet and other javascripts...
function add_wassup_css() {
	global $wassup_options, $wdebug_mode;

	$plugin_page = $_GET['page'];
	if (stristr($plugin_page,'wassup') !== FALSE) { $plugin_page="wassup"; }
	//Add css and javascript to wassup menu pages only...
	if ($plugin_page == "wassup") {
		//assign a value to whash, if none
		if (empty($wassup_options->whash)) {
			$wassup_options->whash = $wassup_options->get_wp_hash();
			$wassup_options->saveSettings();
		}
		//preassign "GET" parameters for "action.php" in "action_param"
		$action_param='&whash='.$wassup_options->whash;
		if ($wdebug_mode) {
			$action_param .= '&debug_mode=true';
		}
		//Important Note: In WordPress 2.6+ "/wp-content/" can be 
		//  located outside of Wordpress' install directory. In 
		//  this configuration, "action.php" will not run without 
		//  the additional GET parameter, "wpabspath=ABSPATH"
		if (defined('WP_CONTENT_DIR') && strpos(WP_CONTENT_DIR,ABSPATH)===FALSE) {
			//  wpabspath is encoded to hide real directory 
			//  path from users and to improve security
			$action_param .= '&wpabspath='.urlencode(base64_encode(ABSPATH));
		}

		//print the css stylesheet and javascripts
		echo "\n".'<link rel="stylesheet" href="'.WASSUPURL.'/css/wassup.css'.'" type="text/css" />'."\n";
		//thickbox built into Wordpress since 2.7
		echo "\n".'<script type="text/javascript">var tb_pathToImage = "'.WASSUPURL.'/js/thickbox/loadingAnimation.gif";</script>';
		echo "\n".'<link rel="stylesheet" href="'.WASSUPURL.'/js/thickbox/thickbox.css'.'" type="text/css" />';

		if ($_GET['page'] != "wassup-options" AND $_GET['page'] != "wassup-spia") {
?>
<script type='text/javascript'>
  //<![CDATA[
  //  var selftimerID = 0;
  function selfRefresh(){
 	location.href='?<?php print $_SERVER['QUERY_STRING']; ?>';
  }
  //  selftimerID = setTimeout('selfRefresh()', <?php print ($wassup_options->wassup_refresh * 60000)+2000; ?>);
  //]]>
</script><?php
			//Since 1.8.2: restrict refresh to range 0-180 minutes (3 hrs)
			$wrefresh = 0;
 			if (!is_numeric($wassup_options->wassup_refresh) || $wassup_options->wassup_refresh < 0	 || $wassup_options->wassup_refresh > 180) {
				$wrefresh = 3; //3 minutes default;
			} else {
				$wrefresh = (int) $wassup_options->wassup_refresh;
			} 
 			//always refresh wassup-online page every 1-3 mins
			if ($_GET['page'] == "wassup-online" && ($wrefresh > 3 || $wrefresh < 1)) {
				$wrefresh = 3;
			}

			//don't add refresh timer javascript if refresh==0
			if ($wrefresh > 0) { 
				echo "\n"; ?>
<script type='text/javascript'>
  //<![CDATA[
  var selftimerID = 0;
  var _countDowncontainer="0";
  var _currentSeconds=0;
  var paused = " *<?php _e('paused','wassup'); ?>* ";
  var tickerID = 0;
  function ActivateCountDown(strContainerID, initialValue) {
  	_countDowncontainer = document.getElementById(strContainerID);
  	SetCountdownText(initialValue);
  	tickerID = window.setInterval("CountDownTick()", 1000);
  }
  function CountDownTick() {
  	if (_currentSeconds > 0) {		//don't tick below zero
    		SetCountdownText(_currentSeconds-1);
  	} else {
		clearInterval(tickerID);	//stop ticker when reach 0
		tickerID = 0;
  	}
  }
  function SetCountdownText(seconds) {
  	//store:
  	_currentSeconds = seconds;
  	//build text:
  	var strText = AddZero(seconds);
  	//apply:
  	if (_countDowncontainer) {	//prevents error in "Options" submenu
  		_countDowncontainer.innerHTML = strText;
  	}
  }
  function AddZero(num) {
  	return ((num >= "0")&&(num < 10))?"0"+num:num+"";
  }
  selftimerID = setTimeout('selfRefresh()', <?php print ($wrefresh * 60000)+2000; ?>);
  //]]>
</script>
<script type="text/javascript">
  //<![CDATA[
  window.onload=WindowLoad;
  function WindowLoad(event) {
  	ActivateCountDown("CountDownPanel", <?php print ($wrefresh * 60); ?>);
  }
  //]]>
</script>
<?php			} //end if $wrefresh > 0
?>
<script type="text/javascript">
//<![CDATA[
jQuery(document).ready(function($){
	$("a.showhide").click(function(){
	   var id = $(this).attr('id');
	   $("div.navi" + id).toggle("slow");
	   return false;
	});
	$("a.toggleagent").click(function(){
	   var id = $(this).attr('id');
	   $("div.naviagent" + id).slideToggle("slow");
	   return false;
	});

	//show larger icons on mouse-over
	$("img.delete-icon").mouseover(function() {
		$(this).attr("src","<?php echo WASSUPURL.'/img/b_delete2.png'; ?>"); 
	}).mouseout(function() {
		$(this).attr("src","<?php echo WASSUPURL.'/img/b_delete.png'; ?>");
	}); 
	$("img.table-icon").mouseover(function() {
		$(this).attr("src","<?php echo WASSUPURL.'/img/b_select2.png'; ?>"); 
	}).mouseout(function() {
		$(this).attr("src","<?php echo WASSUPURL.'/img/b_select.png'; ?>");
	});

	$("a.deleteID").click(function(){
		var id = $(this).attr('id');
		//highlight the record being deleted
		$("div#delID" + id).css("background-color","#ffcaaa");
		$("div#delID" + id).find(".sum-nav").css("background-image","none");
		$.ajax({
		  url: "<?php echo WASSUPURL.'/lib/action.php?action=deleteID'.$action_param; ?>&id=" + id,
		  async: false,
  		  success: function(html){
		  	if (html == "") 
		  		$("div#delID" + id).fadeOut("slow");
			else 
  				$("div#delID" + id).find('p.delbut').append("<br/><br/><small style='color:#404;font-weight:bold;text-align:right;float:right;'> <nobr><?php _e('Sorry, delete failed!','wassup'); ?></nobr> " + html + "</small>");
			},
		  error: function (XMLHttpReq, txtStatus, errThrown) {
			  $("div#delID" + id).find('p.delbut').append("<br/><br/><small style='color:#404;font-weight:bold;text-align:right;float:right;'> <nobr><?php _e('Sorry, delete failed!','wassup'); ?></nobr> " + txtStatus + ": " + errThrown + "</small>");
		  	}
		});
		return false;
	});

	$("a.show-search").toggle(function(){ <?php
		if (empty($_GET['search'])) { 
			echo "\n"; ?>
	   $("div.search-ip").slideDown("slow");
	   $("a.show-search").html("<?php _e('Hide Search', 'wassup'); ?>");
	},function() {
	   $("div.search-ip").slideUp("slow");
	   $("a.show-search").html("<?php _e('Search', 'wassup'); ?>");
	   return false; <?php	
		} else {
			echo "\n"; ?>
	   $("div.search-ip").slideUp("slow");
	   $("a.show-search").html("<?php _e('Search', 'wassup'); ?>");
	},function() {
	   $("div.search-ip").slideDown("slow");
	   $("a.show-search").html("<?php _e('Hide Search', 'wassup'); ?>");
	   return false; <?php	
		}
		echo "\n"; ?>
	});
	/*

	// Since v1.8.3: deleted hide/show "top stats" javascript because it is not used

	*/
	$("a.toggle-all").toggle(function() {
	   $("div.togglenavi").slideDown("slow");
	   $("a.toggle-all").html("<?php _e('Collapse All', 'wassup'); ?>");
	},function() {
	   $("div.togglenavi").slideUp("slow");
	   $("a.toggle-all").html("<?php _e('Expand All', 'wassup'); ?>");
	   return false;
	});
	$("a.toggle-allcrono").toggle(function() {
	   $("div.togglecrono").slideUp("slow");
	   $("a.toggle-allcrono").html("<?php _e('Expand Chronology', 'wassup'); ?>");
 	},function() {
	   $("div.togglecrono").slideDown("slow");
	   $("a.toggle-allcrono").html("<?php _e('Collapse Chronology', 'wassup'); ?>");
	   return false;
	});
<?php 			
	//Since v1.8.2: don't add timer click function when wrefresh==0
	if ($wrefresh > 0) {  ?>
	$("#CountDownPanel").click(function(){	//Pause|Resume countdown
	   var timeleft = _currentSeconds*1000;
	   if (tickerID != 0) {
	   	clearInterval(tickerID);
	   	clearTimeout(selftimerID);
		tickerID = 0;
		$(this).css('color','#999').html(paused);
	   } else {
	   	if (_currentSeconds < 1) timeleft = 1000;
	   	selftimerID = setTimeout('selfRefresh()', timeleft);
	   	tickerID = window.setInterval("CountDownTick()", 1000);
		$(this).css('color','#555');
	   }
	});
<?php 	} //end if $wrefresh > 0 (2nd)
?>
}); //end jQuery(document).ready
//]]>
</script>
<?php } //end if page != wassup-options

if ($_GET['page'] == "wassup-options") {
        //#Current active tabs are indentified after page reload with 
        //#  either $_GET['tab']=N or $_POST['submit-optionsN'] where 
        //#  N=tab number. The tab is then activated directly in 
        //#  "settings.php" with <li class="ui-tabs-selected">
//<link rel="stylesheet" href="WASSUPURL.'/css/ui.tabs.css'" type="text/css" />
echo "\n"; ?>
<link href='http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/themes/base/jquery-ui.css' rel='stylesheet' type='text/css' />
<script type="text/javascript">
  //<![CDATA[
  jQuery(document).ready(function($) {
	  var $tabs = $('#tabcontainer').tabs();
	  $('.submit-opt').click(function(){
		$(this).css("background-color", "#d71");
	  });
	  $('.default-opt').click(function(){
		$(this).css("background-color", "#d71");
	  });
  });
  //]]>
</script>
<?php
} elseif ($_GET['page'] == "wassup-spia") {
	//## Filter detail lists by visitor type...
	if (isset($_GET['spiatype'])) {
		$spytype = attribute_escape($_GET['spiatype']);
		$wassup_options->wassup_default_spy_type = $spytype;
		$wassup_options->saveSettings(); //save changes 
	} elseif (isset($wassup_options->wassup_default_spy_type) && $wassup_options->wassup_default_spy_type != '') {
		$spytype = $wassup_options->wassup_default_spy_type;
	} else {
		$spytype="everything";
	}
?>
<script type="text/javascript">
  //<![CDATA[
  jQuery(document).ready(function($){
  	$('#spyContainer > div:gt(4)').fadeEachDown(); // initial fade
  	$('#spyContainer').spy({ 
  		limit: 12, 
  		fadeLast: 5, 
		ajax: <?php echo "'".WASSUPURL."/lib/action.php?action=spia&spiatype=".$spytype.$action_param."',\n";
		if (!empty($wassup_options->wassup_spy_speed) && is_numeric($wassup_options->wassup_spy_speed)) {
			echo "\t\ttimeout: ".$wassup_options->wassup_spy_speed;
		} else {
			echo "\t\ttimeout: 5000";
		} ?>,
  		'timestamp': myTimestamp, 
		fadeInSpeed: 1100 });

	$('#spy-pause').click(function(){
		$(this).css("background-color", "#ebb");
		$("#spy-play").css("background-color", "#eae9e9"); <?php
		if (!empty($wassup_options->wassup_geoip_map)) {
			echo "\n"; ?>
		$("div#map").css({"opacity": "0.6", "background": "none"}); <?php
		} ?>
	});
	$('#spy-play').click(function(){
		$(this).css("background-color", "#cdc"); 
		$("#spy-pause").css("background-color", "#eae9e9");<?php
		if (!empty($wassup_options->wassup_geoip_map)) {
			echo "\n"; ?>
		$("div#map").css("opacity", "1"); <?php
		} ?>
	});
  });
	
  function myTimestamp() {
  	var d = new Date();
  	var timestamp = d.getFullYear() + '-' + pad(d.getMonth()) + '-' + pad(d.getDate());
  	timestamp += ' ';
  	timestamp += pad(d.getHours()) + ':' + pad(d.getMinutes()) + ':' + pad(d.getSeconds());
  	return timestamp;
  }

  // pad ensures the date looks like 2006-09-13 rather than 2006-9-13
  function pad(n) {
  	n = n.toString();
  	return (n.length == 1 ? '0' + n : n);
  }

  //]]>
</script>
<?php } //end if page == "wassup-spia"
} //end if plugin_page == "wassup"
} //end function add_wassup_css()

//put WassUp in the top-level admin menu and add submenus....
function wassup_add_pages() {
	global $wassup_options, $wp_version;

	$userlevel = $wassup_options->wassup_userlevel;
	if (empty($userlevel)) { $userlevel = 8; }
	// add the default submenu first (important!)...
	add_menu_page('Wassup', 'WassUp', $userlevel, WASSUPFOLDER, 'Wassup');
	add_submenu_page(WASSUPFOLDER, __('Visitor Details', 'wassup'), __('Visitor Details', 'wassup'), $userlevel, WASSUPFOLDER); //<-- WASSUPFOLDER needed here for directory names that include a version number...
	add_submenu_page(WASSUPFOLDER, __('Spy Visitors', 'wassup'), __('SPY Visitors', 'wassup'), $userlevel, 'wassup-spia', 'WassUp');
	add_submenu_page(WASSUPFOLDER, __('Current Visitors Online', 'wassup'), __('Current Visitors Online', 'wassup'), $userlevel, 'wassup-online', 'WassUp');
	//only admins can change WassUp options, so userlevel is always 8
	add_submenu_page(WASSUPFOLDER, __('Options', 'wassup'), __('Options', 'wassup'), 8, 'wassup-options', 'WassUp');

	//add Wassup Stats submenu on WP2.7+ dashboard menu
	//add "settings" to action links on "plugins" page
	if (version_compare($wp_version, '2.5', '>=')) {
		if (version_compare($wp_version, '2.7', '>=')) {
			add_submenu_page('index.php', __('WassUp Stats'), __('WassUp Stats'), $userlevel, 'wassup-stats', 'WassUp');

			add_filter("plugin_action_links_".WASSUPFOLDER."/wassup.php", 'wassupPluginLinks', -10, 2);	//WP 2.7+ filter
		} else {
			add_filter('plugin_action_links', 'wassupPluginLinks', -10, 2);	//WP 2.5+ filter
		}
	}
} //end function wassup_add_pages

/**
 * Wassup's hook function for Wordpress plugin links: appends 'settings' 
 *  link (wassup-options) to list of action links on "Plugins" page
 * @param (2) array, string
 * @return array
 * @since v1.8
 */
function wassupPluginLinks($links, $file) {
	if ($file == WASSUPFOLDER."/wassup.php") {
		$links[] = '<a href="'.admin_url('admin.php?page=wassup-options').'">'.__("Settings").'</a>';
	}
	return $links;
} // end function wassupPluginLinks

/**
 * Wassup's admin page manager - displays WassUp admin pages 
 */
function WassUp() {
	global $wpdb, $wp_version, $current_user, $user_level, $wassupversion, $wassup_options, $wdebug_mode;
	
	// Start getting time of execution to debug SQL query
	$starttime = microtime_float();

	//#debug...
	if ($wdebug_mode) {
		$mode_reset=ini_get('display_errors');
		//error_reporting(E_ALL | E_STRICT);	//debug, E_STRICT=php5 only
		error_reporting(E_ALL);	//debug
		ini_set('display_errors','On');	//debug
		echo "\n<!-- *WassUp DEBUG On-->\n";
		echo "<!-- *normal setting: display_errors=$mode_reset -->\n";
	}
	if ( !ini_get('safe_mode')) {	//extend php script timeout length
		@set_time_limit(2*60); 	//  ...to 2 minutes
	}
	//for generating page link urls....
	$wpurl =  get_bloginfo('wpurl');
	$blogurl =  get_bloginfo('home');
	$wassup_table = (!empty($wassup_options->wassup_table)? $wassup_options->wassup_table: $wpdb->prefix . "wassup");
	$table_tmp_name = $wassup_table."_tmp";

	//"action_param" are preassigned "GET" parameters used for "action.php" external/ajax calls like "top ten" 
	$action_param='&whash='.$wassup_options->whash;
	if ($wdebug_mode) {
		$action_param .= '&debug_mode=true';
	}
	//wpabspath param required for non-standard wp-content directory location
	if (defined('WP_CONTENT_DIR') && strpos(WP_CONTENT_DIR,ABSPATH)===FALSE) {
		$action_param .= '&wpabspath='.urlencode(base64_encode(ABSPATH));
	}
	$wassup_options->loadSettings();	//needed in case "update_option is run elsewhere in wassup (widget)
	//user_level needed for admin user checking
	if (empty($user_level) || !is_numeric($user_level)) {
		get_currentuserinfo();
	}

	// RUN THE SAVE/RESET FORM OPTIONS 
	// Processed here so that any resulting "admin_message" or errors 
	// will display with page
	//DELETE NOW options...
	$admin_message="";
	$affected_recs=0;
	if (!empty($_POST['delete_manual']) && $_POST['delete_manual'] !== "never") {
		$delete_condition = ""; 
		$to_date = current_time("timestamp");
		$from_date = @strtotime($_POST['delete_manual'], $to_date);
		if (is_numeric($from_date) && $from_date < $to_date) {
			$delete_condition = "`timestamp`<'$from_date'";
			if ($_POST['delete_filter_manual'] =="spider"){
				$delete_condition .= " AND spider!=''";
			} elseif ($_POST['delete_filter_manual'] =="spam"){
				$delete_condition .= " AND spam>0";
			}
			$wpdb->query("DELETE FROM $wassup_table WHERE $delete_condition");
			$affected_recs = $wpdb->rows_affected + 0;
			//$wpdb->query("OPTIMIZE TABLE $wassup_table"); //table already optimized daily
		}
		if ($affected_recs > 0) {
			$admin_message = $affected_recs." ".__("records deleted successfully","wassup")."." ;
		} else {
			$admin_message = __("Nothing to delete!","wassup")."." ;
		}
		$wassup_options->saveSettings();
	}
	if (!empty($_POST['delete_auto']) && $_POST['delete_auto'] !== "never") {
		$delete_condition = ""; 
		$to_date = current_time("timestamp");
		$from_date = @strtotime($_POST['delete_auto'], $to_date);
		if (is_numeric($from_date)) {
			$wassup_options->delete_auto = $_POST['delete_auto'];
			$wassup_options->delete_filter = $_POST['delete_filter'];
			$wassup_options->saveSettings();
			if (isset($_POST['delete_now']) && $from_date < $to_date) {
				$delete_condition = "`timestamp`<'$from_date'";
				if ($_POST['delete_filter'] =="spider"){
					$delete_condition .= " AND spider!=''";
				} elseif ($_POST['delete_filter'] =="spam"){
					$delete_condition .= " AND spam>0";
				}
				$wpdb->query("DELETE FROM $wassup_table WHERE $delete_condition");
				$affected_recs = $wpdb->rows_affected + 0;
				if ($affected_recs > 0) {
					$admin_message = $affected_recs." ".__("records deleted successfully","wassup")."." ;
				} else {
					$admin_message = __("Nothing to delete!","wassup")."." ;
				}
			} else {
				$admin_message = __("Wassup options updated successfully","wassup")."." ;
			} //end if delete_now
		} //end if numeric
	} //end if delete_auto
	if (!empty($_POST['wassup_empty'])) {
		$wpdb->query("DELETE FROM $wassup_table");
		if ($affected_recs > 0) {
			$affected_recs = $wpdb->rows_affected + 0;
			$admin_message = $affected_recs." ".__("records deleted successfully","wassup")."." ;
		} else {
			$admin_message = __("Nothing to delete!","wassup")."." ;
		}
		//TODO: "Optimize" operation locks table so it must finish in background to prevent browser close/timeout from interrupting the release of the lock, making it permanent.
		//if ($affected_recs > 1000) {
		//	$wpdb->query("OPTIMIZE TABLE $wassup_table");
		//}
		$wassup_options->saveSettings();
	}
	if (!isset($_POST['delete_now'])) {
	if (isset($_POST['submit-options']) || 
	    isset($_POST['submit-options2']) || 
	    isset($_POST['submit-options3'])) {
		if (!empty($_POST['wassup_remind_flag'])) {
			$wassup_options->wassup_remind_flag = $_POST['wassup_remind_flag'];
			if (!empty($_POST['wassup_remind_mb']) ) {
				$wassup_options->wassup_remind_mb = $_POST['wassup_remind_mb'];
			} else {
				$wassup_options->wassup_remind_mb = 10;
			}
		}
		$wassup_options->wassup_active = $_POST['wassup_active'];
		$wassup_options->wassup_chart_type = $_POST['wassup_chart_type'];
		if ((int)$_POST['wassup_chart_type'] == 0) {	//no chart
			$wassup_options->wassup_chart = "0";
		}
		$wassup_options->wassup_loggedin = $_POST['wassup_loggedin'];
		$wassup_options->wassup_admin = $_POST['wassup_admin'];
		$wassup_options->wassup_spider = $_POST['wassup_spider'];
		$wassup_options->wassup_attack = $_POST['wassup_attack'];
		$wassup_options->wassup_spamcheck = $_POST['wassup_spamcheck'];
		$wassup_options->wassup_spam = $_POST['wassup_spam'];
		$wassup_options->wassup_refspam = $_POST['wassup_refspam'];
		$wassup_options->wassup_hack = $_POST['wassup_hack'];
		$wassup_options->wassup_exclude = attribute_escape($_POST['wassup_exclude']);
		$wassup_options->wassup_exclude_url = attribute_escape($_POST['wassup_exclude_url']);
		$wassup_options->wassup_exclude_user = attribute_escape($_POST['wassup_exclude_user']);
		$wassup_options->delete_auto = $_POST['delete_auto'];
		if (isset($_POST['delete_filter'])) {
			$wassup_options->delete_filter = $_POST['delete_filter'];
		}
		$wassup_options->wassup_screen_res = $_POST['wassup_screen_res'];
		//Since v1.8.2: validate wassup_refresh input value
		if (is_numeric($_POST['wassup_refresh']) && $_POST['wassup_refresh']>=0 && $_POST['wassup_refresh']<=180) { 
			$wassup_options->wassup_refresh = (int)$_POST['wassup_refresh'];
		}

		$wassup_options->wassup_userlevel = (int)$_POST['wassup_userlevel'];
		$wassup_options->wassup_dashboard_chart = $_POST['wassup_dashboard_chart'];
		$wassup_options->wassup_geoip_map = $_POST['wassup_geoip_map'];
		if (!empty($_POST['wassup_googlemaps_key'])) {	//don't clear geoip key
			$wassup_options->wassup_googlemaps_key = $_POST['wassup_googlemaps_key'];
		}
		if (!empty($_POST['wassup_spy_speed']) && is_numeric($_POST['wassup_spy_speed']) && (int)$_POST['wassup_spy_speed'] >1000) {
			$wassup_options->wassup_spy_speed = (int)$_POST['wassup_spy_speed'];
		}
		$wassup_options->wassup_time_format = $_POST['wassup_time_format'];
		$wassup_options->wassup_time_period = $_POST['wassup_time_period'];
		$wassup_options->wassup_default_type = $_POST['wassup_default_type'];
		$wassup_options->wassup_default_limit = $_POST['wassup_default_limit'];
		//New in v1.8.3: 1) toplimit input for top stats (top10) 
		// list size and 2) top_nospider input to exclude spiders 
		// from all top stats
		$top_ten = array("toplimit" => (isset($_POST['toplimit'])? (int)$_POST['toplimit']:"10"),
				"topsearch" => $_POST['topsearch'],
				"topreferrer" => $_POST['topreferrer'],
				"toprequest" => $_POST['toprequest'],
				"topbrowser" => $_POST['topbrowser'],
				"topos" => $_POST['topos'],
				"toplocale" => (isset($_POST['toplocale'])?$_POST['toplocale']:"0"),
				"topvisitor" => (isset($_POST['topvisitor'])?$_POST['topvisitor']:"0"),
				"toppostid" => (isset($_POST['toppostid'])?$_POST['toppostid']:"0"),
				"topreferrer_exclude" => $_POST['topreferrer_exclude'],
				"top_nospider" => (isset($_POST['top_nospider'])?$_POST['top_nospider']:"0"));
		$wassup_options->wassup_top10 = attribute_escape(serialize($top_ten));
		$wassup_options->wassup_cache = (!empty($_POST['wassup_cache'])?"1":"0");
		if ($wassup_options->saveSettings()) {
			$admin_message = __("Wassup options updated successfully","wassup")."." ;
		}
	} elseif (isset($_POST['submit-options4'])) {	//uninstall checkbox
		if (!empty($_POST['wassup_uninstall'])) {
			$wassup_options->wassup_uninstall = $_POST['wassup_uninstall'];
		} else {
			$wassup_options->wassup_uninstall = "0";
		}
		if ($wassup_options->wassup_uninstall == "1") {
			$wassup_options->wassup_active = "0"; //disable recording now
		}
		if ($wassup_options->saveSettings()) {
			$admin_message = __("Wassup uninstall option updated successfully","wassup")."." ;
		}
	} elseif (isset($_POST['submit-spam'])) {
		$wassup_options->wassup_spamcheck = $_POST['wassup_spamcheck'];
		$wassup_options->wassup_spam = $_POST['wassup_spam'];
		$wassup_options->wassup_refspam = $_POST['wassup_refspam'];
		$wassup_options->wassup_hack = $_POST['wassup_hack'];
		if ($wassup_options->saveSettings()) {
			$admin_message = __("Wassup spam options updated successfully","wassup")."." ;
		}
	} elseif (isset($_POST['reset-to-default'])) {
		$wassup_options->loadDefaults();
		if ($wassup_options->saveSettings()) {
			$admin_message = __("Wassup options updated successfully","wassup")."." ;
		}
	}
	} //end if !delete_now

	//#sets current tab style for Wassup admin submenu?
	$class_spy="";
	$class_opt="";
	$class_ol="";
	$class_sub="";
	$expcol = '
	<table width="100%"><tr>
		<td align="left" class="legend"><a href="#" class="toggle-all boxed">'.__('Expand All','wassup').'</a></td>
	</tr></table>';
	if ($_GET['page'] == "wassup-spia") {
		$class_spy='current';
		$wassuppage="wassup-spy";
	} elseif ($_GET['page'] == "wassup-options") {
		$class_opt='current';
		$wassuppage=$_GET['page'];
	} elseif ($_GET['page'] == "wassup-online") {
		$class_ol="current";
		$wassuppage=$_GET['page'];
	} else {
		$class_sub="current";
		$wassuppage="wassup";
		$expcol = '
	<table width="100%" class="toggle"><tr>
		<td align="left" class="legend"><a href="#" class="toggle-all boxed">'.__('Expand All','wassup').'</a></td>
		<td align="right" class="legend"><a href="#" class="toggle-allcrono boxed">'.__('Collapse Chronology','wassup').'</a></td>
	</tr></table>';
	}

	//for stringShortener calculated values and max-width...-Helene D. 11/27/07, 12/6/07
	if (!empty($wassup_options->wassup_screen_res)) {
		$screen_res_size = (int) $wassup_options->wassup_screen_res;
	} else { 
		$screen_res_size = 670;
	}
	$max_char_len = ($screen_res_size)/10;
	$screen_res_size = $screen_res_size+20; //for wrap margins...
	$wrapstyle = "style='margin:0 auto; padding:0 15px; max-width:".$screen_res_size."px;'";

	//for wassup chart size
	$res = (int) $wassup_options->wassup_screen_res;
	if (empty($res)) $res=620;
	elseif ($res < 800) $res=620;
	elseif ($res < 1024) $res=740;
	else $res=1000; //1000 is Google api's max chart width 

	//Some Wordpress 2.7-specific style adjustments
	if (version_compare($wp_version, '2.7', '>=')) { 
		//set smaller chart size and screen_res to make room for new sidebar in WP2.7+
		$screen_res_size = $screen_res_size-160;
		$max_char_len = $max_char_len-16;
		if ((int)$wassup_options->wassup_screen_res <1200) {
			$res = $res-120;
		}
		$wrapstyle = "";

	//Restore horizontal menus in Wordpress 2.7 by adding WassUp 
	//   submenus to context menus- for easier menu navigation (no need
	//   to scroll down to see menus). Tested in ie6, ie7, ff1.5, ff2,
	//   ff3, safari 1-3. -Helene D. 2009-03-09
	echo "\n"; ?>
	<ul id="wassup-menu">
		<li class="wassup-menu-link <?php echo $class_opt; ?>"><?php
		//only administrators can view "wassup-options"
		if ($user_level >= 8) {
			echo '<a href="'.admin_url("admin.php?page=wassup-options").'">'.__('Options','wassup').'</a>';
		} else {
			_e('Options','wassup');
		} ?></li>

		<li class="wassup-menu-link <?php echo $class_ol; ?>"><a href="<?php echo admin_url('admin.php?page=wassup-online'); ?>"><?php _e('Current Visitors Online','wassup'); ?></a></li>
		<li class="wassup-menu-link <?php echo $class_spy; ?>"><a href="<?php echo admin_url('admin.php?page=wassup-spia'); ?>"><?php _e('SPY Visitors','wassup'); ?></a></li>
		<li class="wassup-menu-link <?php echo $class_sub; ?>"><a href="?page=<?php echo WASSUPFOLDER; ?>"><?php _e('Visitor Details','wassup'); ?></a></li>
	</ul><div style="clear:right;"></div>
<?php	} //end if version_compare(2.7)

	//#display an admin message or an alert. This must be above "wrap"
	//# div, but below wassup menus -Helene D. 2008-2-26, 2009-3-08
	if (!empty($admin_message)) {
		$wassup_options->showMessage($admin_message);
	} elseif (!empty($wassup_options->wassup_alert_message)) {
		$wassup_options->showMessage();
		//#show alert message only once, so remove it here...
		$wassup_options->wassup_alert_message = "";
		$wassup_options->saveSettings();
	} elseif ($wassup_options->wassup_active != "1") {
		//display as a system message when not recording...
	    	$admin_message = __("Warning","wassup").": WassUp ".__("is NOT recording new statistics","wassup").". ".__('To collect data you must check "Enable/Disable recording" in "Options: Statistics Recording" tab','wassup');
		$wassup_options->showMessage($admin_message);
	} ?>
	<div id="wassup-wrap" class="wrap <?php echo $wassuppage; ?>">
		<div id="icon-plugins" class="icon32 wassup-icon"></div>
<?php

	// HERE IS THE VISITORS ONLINE VIEW
	if ($_GET['page'] == "wassup-online") { ?>
		<h2>WassUp - <?php _e("Current Visitors Online", "wassup"); ?></h2>
		<p class="legend"><?php echo __("Legend", "wassup").': &nbsp; <span class="box-log">&nbsp;&nbsp;</span> '.__("Logged-in Users", "wassup").' &nbsp; <span class="box-aut">&nbsp;&nbsp;</span> '.__("Comment Authors", "wassup").' &nbsp; <span class="box-spider">&nbsp;&nbsp;</span> '.__("Spiders/bots", "wassup"); ?></p><br />
		<?php
		$to_date = current_time('timestamp');
		$from_date = $to_date - 3*60;	//-3 minutes from timestamp
		//$currenttot = $wpdb->get_var("SELECT COUNT(DISTINCT wassup_id) as currenttot FROM $table_tmp_name WHERE `timestamp` BETWEEN $from_date AND $to_date");	//redundant
		$currenttot = 0;
		$qryC = $wpdb->get_results("SELECT `id`, wassup_id, count(wassup_id) as numurl, max(`timestamp`) as max_timestamp, `ip`, searchengine, urlrequested, `referrer`, spider, `username`, comment_author FROM $table_tmp_name WHERE `timestamp` > $from_date GROUP BY wassup_id ORDER BY max_timestamp DESC");
		if (!empty($qryC) && is_array($qryC)) {
			$currenttot = count($qryC);
		} ?>
		<p class='legend'><?php echo __("Visitors online", "wassup").": <strong>".$currenttot."</strong>"; ?></p>
		<div class='main-tabs'><?php print $expcol;
		if ($currenttot > 0) {
		foreach ($qryC as $cv) {
			if ($wassup_options->wassup_time_format == 24) {
			    $timed = gmdate("H:i:s", $cv->max_timestamp);
			} else {
			    $timed = gmdate("h:i:s a", $cv->max_timestamp);
			}
			$ip_proxy = strpos($cv->ip,",");
			//if proxy, get 2nd ip...
			if ($ip_proxy !== false) {
				$ip = substr($cv->ip,(int)$ip_proxy+1);
			} else { 
				$ip = $cv->ip;
			}
			if ($cv->referrer != '') {
			if ($cv->searchengine != "" || stristr($cv->referrer,$wpurl)!=$cv->referrer) { 
				if ($cv->searchengine == "") {
					$referrer = '<a href="'.wCleanURL($cv->referrer).'" target=_"BLANK"><span style="font-weight: bold;">'.stringShortener("{$cv->referrer}", round($max_char_len*.8,0)).'</span></a>';
				} else {
					$referrer = '<a href="'.wCleanURL($cv->referrer).'" target=_"BLANK">'.stringShortener("{$cv->referrer}", round($max_char_len*.9,0)).'</a>';
				}
			} else { 
				$referrer = __("From your blog", "wassup");
			} 
		} else { 
			$referrer = __("Direct hit", "wassup"); 
		} 
		$numurl = $cv->numurl;
?>
		<div class="sum-rec">
		<div class="sum-nav-spy">
			<div class="sum-box">
				<span class="sum-box-ip"><?php if ($numurl >= 2) { ?><a  href="#" class="showhide" id="<?php echo $cv->id ?>"><?php print $ip; ?></a><?php } else { ?><?php print $ip; ?><?php } ?></span>
			</div>
			<div class="sum-det">
				<span class="det1"><?php
			if (strstr($cv->urlrequested,'[404]')) {  //no link for 404 page
				print stringShortener($cv->urlrequested, round($max_char_len*.9,0)+5);
			} else {
				print '<a href="'.wAddSiteurl("{$cv->urlrequested}").'" target="_BLANK">';
				print stringShortener("{$cv->urlrequested}", round($max_char_len*.9,0)).'</a>';
			} ?> </span>
				<span class="det2"><strong><?php print $timed; ?> - </strong><?php print $referrer ?></span>
			</div>
		</div>
<?php 		// User is logged in or is a comment's author
		if ($cv->username != "" OR $cv->comment_author != "") {
			if ($cv->username != "") {
				$Ousername = '<li class="users"><span class="indent-li-agent">'.__("LOGGED IN USER", "wassup").': <strong>'.$cv->username.'</strong></span></li>'; 
				$Ocomment_author = '<li class="users"><span class="indent-li-agent">'.__("COMMENT AUTHOR", "wassup").': <strong>'.$cv->comment_author.'</strong></span></li>'; 
				$unclass = "userslogged";
			} elseif ($cv->comment_author != "") {
				$Ocomment_author = '<li class="users"><span class="indent-li-agent">'.__("COMMENT AUTHOR", "wassup").': <strong>'.$cv->comment_author.'</strong></span></li>'; 
				$unclass = "users";
			}
?>
		<ul class="<?php print $unclass; ?>">
			<?php print $Ousername; ?>
			<?php print $Ocomment_author; ?>
		</ul>
<?php		} 
		if ($numurl >1) { ?>
		<div style="display: none;" class="detail-data togglenavi navi<?php echo $cv->id ?>">
			<ul class="url"><?php 
			$qryCD = $wpdb->get_results("SELECT `timestamp`, urlrequested FROM $table_tmp_name WHERE wassup_id='".$cv->wassup_id."' ORDER BY `timestamp`");
			$i=1;
			foreach ($qryCD as $cd) {
				if ($wassup_options->wassup_time_format == 24) {
					$time2 = gmdate("H:i:s", $cd->timestamp);
				} else {
					$time2 = gmdate("h:i:s a", $cd->timestamp);
				}
				$num = ($i&1);
				$char_len = round($max_char_len*.9,0);
				if ($num == 0) $classodd = "urlodd"; else  $classodd = "url"; ?>
			<li class="<?php print $classodd; ?> navi<?php echo $cv->id ?>">
				<span class="indent-li"><?php print $time2; ?> - <?php
				if (strstr($cd->urlrequested,'[404]')) {  //no link for 404 page
					print stringShortener($cd->urlrequested, $char_len);
				} else {
					print '<a href="'.wAddSiteurl("{$cd->urlrequested}").'" target="_BLANK">';
					print stringShortener("{$cd->urlrequested}", $char_len).'</a>'."\n";
				} ?>
				</span>
			</li>
<?php
				$i++;
			} //end foreach qryCD ?>
			</ul>
		</div>
<?php		} //end if numurl
?>
		<p class="sum-footer"></p>
		</div><?php
		} //end foreach qryC

		echo $expcol;
		} //end if currenttot
?>
	</div><!-- /main-tabs -->

<?php	// HERE IS THE SPY MODE VIEW
	} elseif ($_GET['page'] == "wassup-spia") { ?>
	<h2>WassUp - <?php _e("SPY Visitors", "wassup"); ?></h2>
	<div style="width:100%; margin:15px 3px; padding:5px; height:40px; clear:both;">
		<p class="legend" style="padding:2px 0 0 5px; margin:0;"><?php echo __("Legend", "wassup").': &nbsp; <span class="box-log">&nbsp;&nbsp;</span> '.__("Logged-in Users", "wassup").' &nbsp; <span class="box-aut">&nbsp;&nbsp;</span> '.__("Comments Authors", "wassup").' &nbsp; <span class="box-spider">&nbsp;&nbsp;</span> '.__("Spiders/bots", "wassup"); ?></p>
		<span id="spy-pause"><a href="#?" onclick="return pauseSpy();"><?php _e("Pause", "wassup"); ?></a></span>
		<span id="spy-play"><a href="#?" onclick="return playSpy();"><?php _e("Play", "wassup"); ?></a></span>
		<!-- <span class="separator">|</span> -->
		<span style="font-size:11px; text-align:right; float:right;"><?php _e('Spy items by','wassup'); ?>: <select name="navi" style="font-size: 11px;" onChange="window.location.href=this.options[this.selectedIndex].value;"><?php
                //## selectable filter by type of record (wassup_default_spy_type)
		if (isset($_GET['spiatype'])) {
			$spytype = attribute_escape($_GET['spiatype']);
		} elseif ($wassup_options->wassup_default_spy_type != '') {
			$spytype = $wassup_options->wassup_default_spy_type;
		} else {
			$spytype="everything";
		}
                $selected=$spytype;
                $optionargs="?page=wassup-spia&spiatype=";
                $wassup_options->showFormOptions("wassup_default_spy_type","$selected","$optionargs"); ?>
                </select>
                &nbsp; </span>
	</div>

<?php // GEO IP Map
		if ($wassup_options->wassup_geoip_map == 1 AND $wassup_options->wassup_googlemaps_key != "") { ?>
	<div id="placeholder">
	<div id="map" style="width:<?php echo (int)($screen_res_size*0.95); ?>px; height:270px;"></div>
	</div>
	<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?php echo $wassup_options->wassup_googlemaps_key; ?>" type="text/javascript"></script>
	<script type="text/javascript">
	//<![CDATA[
		if (GBrowserIsCompatible()) { 
			// Display the map, with some controls and set the initial location 
			var map = new GMap2(document.getElementById("map"));
			map.addControl(new GSmallMapControl());
			map.addControl(new GMapTypeControl());
			//map.enableScrollWheelZoom();
			map.setCenter(new GLatLng(0,0),3);
			//map.setMapType(G_HYBRID_MAP);
		}
		// display a warning if the browser was not compatible
		else {
			alert("Sorry, the Google Maps API is not compatible with this browser");
		}
	//]]>
	</script><?php
		} //end if geoip_map 
?>
	<div id="spyContainer"><?php
		//display last few hits here. rest will be added by spia.js
		$to_date = current_time('timestamp');
		$from_date = ($to_date - 12*(60*60)); //display last 10 visits in 12 hours...
		wassup_spiaView($from_date,0,$spytype,$wassup_table); ?>
		<p style="height:2px;clear:both;"></p>
	</div><!-- /spyContainer -->
	<br />

<?php	// HERE IS THE OPTIONS VIEW
	} elseif($_GET['page'] == "wassup-options") { ?>
		<h2>WassUp - <?php _e('Options','wassup'); ?></h2>
		<p><?php _e('You can add a sidebar Widget with some useful statistics information by activating "WassUp Widget" in the','wassup'); ?>
		<a href="<?php echo admin_url('widgets.php'); ?>"><?php _e('Widgets menu','wassup'); ?></a>
		</p><?php
		if (!function_exists('wassup_optionsView')) {
			include_once(WASSUPDIR.'/lib/settings.php');
		}
		wassup_optionsView(); ?>

<?php	// HERE IS THE MAIN/DETAILS VIEW
	} else {
		?>
		<h2>WassUp - <?php _e("Latest hits", "wassup"); ?></h2><?php 
		if ($wassup_options->wassup_active != 1) { ?>
			<p style="color:red; font-weight:bold;"><?php _e("WassUp recording is disabled", "wassup"); ?></p><?php
		} 
		//## GET parameters that change options settings
		if (isset($_GET['wchart']) || isset($_GET['wmark'])) { 
			if (isset($_GET['wchart'])) { // [0|1] only
			if ($_GET['wchart'] == 0) {
				$wassup_options->wassup_chart = 0;
			} elseif ($_GET['wchart'] == 1) {
				$wassup_options->wassup_chart = 1;
			}
			}
			if (isset($_GET['wmark'])) { // [0|1] only
			if ($_GET['wmark'] == 0) {
                		$wassup_options->wmark = "0";
				$wassup_options->wip = "";
			} elseif ($_GET['wmark'] == 1 && isset($_GET['wip'])) {
				$wassup_options->wmark = "1";
				$wassup_options->wip = attribute_escape($_GET['wip']);
			}
			}
			$wassup_options->saveSettings();
		}

		//## GET params that filter detail display
		$stickyFilters=""; //filters that remain in effect after page reloads
		//
		//## Filter detail list by date range...
		$to_date = current_time("timestamp");	//wordpress time function
		if (isset($_GET['last']) && is_numeric($_GET['last'])) { 
			$wlast = $_GET['last'];
			$stickyFilters.='&last='.$wlast;
		} else {
			$wlast = $wassup_options->wassup_time_period; 
		}
		if ($wlast == 0) {
			$from_date = "0";	//all time
		} else {
			$from_date = $to_date - (int)(($wlast*24)*3600);
			//extend start date to within a rounded time	
			if ($wlast < .25) { 	//start on 1 minute
				$from_date = ((int)($from_date/60))*60;
			} elseif ($wlast < 7) {
				$from_date = ((int)($from_date/300))*300;
			} elseif ($wlast < 30) {
				$from_date = ((int)($from_date/1800))*1800;
			} elseif ($wlast < 365) {
				$from_date = ((int)($from_date/86400))*86400;
			} else {
				$from_date = ((int)($from_date/604800))*604800;
			}
		}

		//## Filter detail lists by visitor type...
		if (isset($_GET['type'])) {
			$wtype = attribute_escape($_GET['type']);
			$stickyFilters.='&type='.$wtype;
		} else {
			$wtype = $wassup_options->wassup_default_type;
		}
		if (!empty($wtype) && $wtype != 'everything') {
			$wwhereis=$wassup_options->getKeyOptions("wassup_default_type","sql",$wtype);
		} else {
			$wtype="";
			$wwhereis="";
		}
		//## Filter detail lists by a specific page and number
		//#  of items per page...
		$witems = 10;	//default
		if (isset($_GET['limit']) && is_numeric($_GET['limit'])) {
			//$witems = htmlentities(attribute_escape($_GET['limit'])); 
			$witems = (int)$_GET['limit']; 
		} elseif ($wassup_options->wassup_default_limit != '') {
			$witems = $wassup_options->wassup_default_limit;
		}
		if ((int)$witems < 1 ) { $witems = 10; }
		//# current page selections
		if (isset($_GET['pages']) && is_numeric($_GET['pages'])) {
			$wpages = (int)$_GET['pages'];
		} else {
			$wpages = 1;
		}
		if ( $wpages > 1 ) {
			$wlimit = " LIMIT ".(($wpages-1)*$witems).",$witems";
		} else {
			$wlimit = " LIMIT $witems";
		}

		//## Filter detail lists by a searched item
                if (!empty($_GET['search'])) { 
                        $wsearch = attribute_escape(strip_tags(html_entity_decode($_GET['search'])));
			$stickyFilters.='&search='.$wsearch;
                } else {
                        $wsearch = "";
                }

                // DELETE EVERY RECORD MARKED BY IP
		//#  Delete limited to selected date range only. -Helene D. 3/4/08.
		if (!empty($_GET['deleteMARKED']) && $wassup_options->wmark == "1" && !empty($_GET['dip'])) {
                        $del_count = $wpdb->get_var("SELECT COUNT(ip) as deleted FROM $wassup_table WHERE ip='".attribute_escape($_GET['dip'])."' AND `timestamp` BETWEEN $from_date AND $to_date");
                        if (method_exists($wpdb,'prepare')) {
                                $wpdb->query($wpdb->prepare("DELETE FROM $wassup_table WHERE ip='%s' AND `timestamp` BETWEEN %s AND %s", $_GET['dip'], $from_date, $to_date));
                        } else {
                                $wpdb->query("DELETE FROM $wassup_table WHERE ip='".attribute_escape($_GET['dip'])."' AND `timestamp` BETWEEN $from_date AND $to_date");
                        }
                        $rec_count = $wpdb->get_var("SELECT COUNT(ip) as deleted FROM $wassup_table WHERE ip='".attribute_escape($_GET['dip'])."' AND `timestamp` BETWEEN $from_date AND $to_date");	//double-check deletions
			$rec_deleted = ($del_count - $rec_count)." ".__('records deleted','wassup');
			$wassup_options->showMessage($rec_deleted);
                        //echo '<p><strong>'.$rec_deleted.' '.__('records deleted','wassup').'</strong></p>';
			//reset wmark/deleteMarked after delete
                        $wassup_options->wmark = "0";
                        $wassup_options->wip = null;
                        $wassup_options->saveSettings();
                } //end if deleteMARKED
		//to prevent browser timeouts, send <!--heartbeat--> output
		echo "<!--heartbeat-->\n";

		// Instantiate class to count items
		$wTot = New WassupItems($wassup_table,$from_date,$to_date,$wwhereis,$wlimit);
		$wTot->whereis = $wwhereis;
		$wTot->Limit = $wlimit;
		$wTot->WpUrl = $wpurl;
		echo "<!--heartbeat-->\n";

		// MAIN QUERY
		$wmain = $wTot->calc_tot("main", $wsearch);
		echo "<!--heartbeat-->\n";
		$witemstot = $wTot->calc_tot("count", $wsearch, null, "DISTINCT");
		echo "<!--heartbeat-->\n";
		$wpagestot = $wTot->calc_tot("count", $wsearch);
		echo "<!--heartbeat-->\n";
		$wspamtot = $wTot->calc_tot("count", $wsearch, "AND spam>0");
		// Check if some records was marked
		if ($wassup_options->wmark == "1") {
			$markedtot = $wTot->calc_tot("count", $wsearch, "AND ip = '".$wassup_options->wip."'", "DISTINCT");
		}
		echo "<!--heartbeat-->\n";
		// Check if some records were searched
		if (!empty($wsearch)) {
			$searchtot = $wTot->calc_tot("count", $wsearch, null, "DISTINCT");
		}
		//#  remove any delete request from $_SERVER['QUERY_STRING'] 
		//#  clear non-sticky filter parameters before applying new filters 
		if (isset($_GET['deleteMARKED']) && isset($_GET['dip'])) {
			$remove_it= array('&deleteMARKED='.$_GET['deleteMARKED'],'&wmark=1','&dip='.$_GET['dip']);
		} else {
			$remove_it = array();
		}
		if (isset($_GET['wchart'])) {
			$remove_it[] = '&wchart='.$_GET['wchart'];
		}
		if (!empty($remove_it)) {
			$URLQuery = str_replace($remove_it,"",$_SERVER['QUERY_STRING']);
			$_SERVER['QUERY_STRING'] = $URLQuery; //in case of auto refresh
		} else {
			$URLQuery = $_SERVER['QUERY_STRING'];
		} ?>
		<form><table width="100%" style="margin:15px 3px"><tr>
		<td align="left" width="25"><?php
		//chart options
		if ($wassup_options->wassup_chart == "1") {  ?>
			<a href="?<?php echo attribute_escape($URLQuery.'&wchart=0'); ?>" style="text-decoration:none;">
			<img src="<?php echo WASSUPURL.'/img/chart_delete.png" style="padding:0px 6px 0 0;" alt="'.__('hide chart','wassup').'" title="'.__('Hide the chart','wassup'); ?>" /></a><?php 
		} else { ?>
			<a href="?<?php echo attribute_escape($URLQuery.'&wchart=1'); ?>" style="text-decoration:none;">
			<img src="<?php echo WASSUPURL.'/img/chart_add.png" style="padding:0px 6px 0 0;" alt="'.__('show chart','wassup').'" title="'.__('Show the chart','wassup'); ?>" /></a><?php
		} ?></td>
		<td class="legend" align="left"><span class="separator">|</span>
		<?php
		//## Show selectable detail filters...
		//selectable filter by date range
		if (isset($_GET['last']) && isset($_GET['pages'])) {
			$new_last = str_replace(array("&last=".$_GET['last'], "&pages=".$_GET['pages']),"", $URLQuery);
		} elseif (isset($_GET['last'])) {
			$new_last = str_replace("&last=".$_GET['last'],"", $URLQuery);
		} else {
			$new_last = $URLQuery;
		}
		_e('Details for the last','wassup'); ?>:
		<select style="font-size: 11px;" name="last" onChange="window.location.href=this.options[this.selectedIndex].value;"><?php 
		$optionargs="?".attribute_escape($new_last."&last=");
		$wassup_options->showFormOptions("wassup_time_period","$wlast","$optionargs"); ?>
		</select></td>
		<td class="legend" align="right"><?php _e('Items per page','wassup'); ?>: <select name="navi" style="font-size: 11px;" onChange="window.location.href=this.options[this.selectedIndex].value;"><?php
                //selectable filter by number of items on page (default_limit)
		if (isset($_GET['limit'])) {
			$new_limit = attribute_escape(str_replace("&limit=".$_GET['limit'], "", html_entity_decode($URLQuery)));
		} else { 
			$new_limit = $URLQuery;
		}
                $selected=$witems;
                $optionargs="?".$new_limit."&limit=";
		$wassup_options->showFormOptions("wassup_default_limit","$selected","$optionargs"); ?>
		</select><span class="separator">|</span>
		<?php _e('Filter items by','wassup'); ?>: <select style="font-size: 11px;" name="type" onChange="window.location.href=this.options[this.selectedIndex].value;">
                <?php
                //selectable filter by type of record (wassup_default_type)
                $selected=$wtype;
		$filter_args=str_replace("&type=$wtype","",$stickyFilters);
		$optionargs="?page=".WASSUPFOLDER.$filter_args."&type=";
                $wassup_options->showFormOptions("wassup_default_type","$selected","$optionargs"); ?>
                </select></td>
                </tr>
                </table></form><?php
		// Print Site Usage ?>
        <div class='centered'>
                <div id='usage'>
                        <ul>
                        <li><span style="border-bottom:2px solid #0077CC;"><?php echo $witemstot; ?></span> <?php _e('Visits','wassup'); ?></li>
                        <li><span style="border-bottom:2px dashed #FF6D06;"><?php echo $wpagestot; ?></span> <?php _e('Pageviews','wassup'); ?></li>
                        <li><span><?php echo @number_format(($wpagestot/$witemstot), 2); ?></span> <?php _e('Pages/Visits','wassup'); ?></li><?php
		// Print spam usage only if enabled
		if ($wassup_options->wassup_spamcheck == 1) { ?>
			<li><span> <a href="#TB_inline?width=400&inlineId=hiddenspam" class="thickbox"><?php echo $wspamtot; ?><span class="plaintext">(<?php echo @number_format(($wspamtot*100/$wpagestot), 1); ?>%)</span></a></span> <?php _e('Spams','wassup'); ?></li><?php
		} ?>
			</ul><br/>
			<div id="placeholder" align="center"></div>
		</div>
	</div><?php 
		// Page breakdown
		// paginate only when total records > items per page
		if ($witemstot > $witems) {
			$p=new wassup_pagination();
			$p->items($witemstot);
			$p->limit($witems);
			$p->currentPage($wpages);
			$p->target("admin.php?page=".WASSUPFOLDER."&limit=$witems&type=$wtype&last=$wlast&search=$wsearch");
			echo "<!--heartbeat-->\n";
			$p->calculate();
			$p->adjacents(5);
		}

		// hidden spam options
	?><div id="hiddenspam" style="display:none;">
		<h2><?php _e('Spam Options','wassup'); ?></h2>
		<form action="" method="post">
		<p><input type="checkbox" name="wassup_spamcheck" value="1" <?php if($wassup_options->wassup_spamcheck == 1 ) print "CHECKED"; ?> /> <strong><?php _e('Enable/Disable Spam Check on Records','wassup'); ?></strong></p>
		<p style="padding-left:30px;"><input type="checkbox" name="wassup_spam" value="1" <?php if($wassup_options->wassup_spam == 1) print "CHECKED"; ?> /> <?php _e('Record Akismet comment spam attempts','wassup'); ?></p>
		<p style="padding-left:30px;"><input type="checkbox" name="wassup_refspam" value="1" <?php if($wassup_options->wassup_refspam == 1) print "CHECKED"; ?> /> <?php _e('Record referrer spam attempts','wassup'); ?></p>
		<p style="padding-left:30px;"><input type="checkbox" name="wassup_hack" value="1" <?php if($wassup_options->wassup_hack == 1) print "CHECKED"; ?> /> <?php _e("Record admin break-in/hacker attempts", "wassup") ?></p>
		<p style="padding-left:0;"><input type="submit" name="submit-spam" value="<?php _e('Save Settings','wassup'); ?>" /></p>
		</form>
	</div> <!-- /hiddenspam -->
	<table width="100%"><tr>
	<td align="left" width="28">
		<a href="#" onclick='selfRefresh()'><img src="<?php echo WASSUPURL; ?>/img/reload.png" id="refresh" alt="refresh screen" title="Refresh screen" /></a></td>
	<td class="legend" align="left"><?php 
		echo __('Auto refresh in','wassup').'&nbsp;<span id="CountDownPanel">00</span>&nbsp;'.__('seconds','wassup');
		echo '<br/>';
		// Marked items - Refresh
		if ($wassup_options->wmark == 1) {
			echo '&nbsp; <a href="?'.attribute_escape($URLQuery.'&search='.$wassup_options->wip).'" title="'.__('Filter by marked IP','wassup').'"> '.__('Show marked items','wassup').' (<strong>'.$markedtot.'</strong> '.__("total").')</a> ';
		} ?></td>
	<td align="right" class="legend">
		<a href="<?php echo wCleanURL(WASSUPURL.'/lib/action.php?action=topten&from_date='.$from_date.'&to_date='.$to_date.$action_param.'&width='.($res+250).'&height=440','','url'); 
		if ($wdebug_mode) echo '" target="_blank';
		else echo '" class="thickbox';
		?>" title="Wassup <?php _e('Top Stats for','wassup');
		$wdformat = get_option("date_format");
		if (($to_date - $from_date) > 24*60*60) {
			echo ": ".gmdate("$wdformat",$from_date)." - ".gmdate("$wdformat",$to_date);
		} else {
			echo ": ".gmdate("$wdformat H:00",$from_date)." - ".gmdate("$wdformat H:00",$to_date);
		} ?>"><?php _e('Show Top Stats','wassup'); ?></a>
		<span class="separator">|</span> 
		<a href="#" class='show-search'><?php 
			if (!empty($wsearch)) {
				_e('Hide Search','wassup'); 
			} else {
				_e('Search','wassup'); 
			} ?></a></td>
	</tr>
	<tr><td align="left" class="legend" colspan="2"><?php
		if (!empty($wsearch)) { 
			echo " &nbsp; <strong>".(int)$searchtot."</strong> ".__('Matches found for search','wassup').": <strong>$wsearch</strong>";
		} else { 
			echo "<br/>";
		} ?>
	</td>
	<td align="right" class="legend">
		<div class="search-ip" <?php if (empty($wsearch)) echo 'style="display: none;"'; ?>>
		<form action="" method="get">
		<input type="hidden" name="page" value="<?php echo WASSUPFOLDER; ?>" /><?php
		if (!empty($stickyFilters)) {
			$wfilterargs=wGetQueryPairs($stickyFilters);
			if (!empty($wfilterargs)) {
				foreach ($wfilterargs AS $wfilter) {
					$wfilterval=explode('=',$wfilter);
					if (!empty($wfilterval[0]) && $wfilterval[0]!= 'type') {
						echo "\n"; ?>
						<input type="hidden" name="<?php echo $wfilterval[0].'" value="'.$wfilterval[1]; ?>" /><?php
					}
				}
			}
		}
		echo "\n"; ?>
		<input type="text" size="25" name="search" value="<?php if ($wsearch != "") print attribute_escape($wsearch); ?>" /><input type="submit" name="submit-search" value="search" />
		</form>
		</div> <!-- /search-ip -->
	</td>
	</tr></table>
	<div class='main-tabs'><?php
	print $expcol;
        //# Show Page numbers/Links...
	if ($witemstot > $witems) {
		echo '<div id="pag" align="center">'.$p->show()."</div>\n";
        }
	//# Detailed List of Wassup Records...
	if ($witemstot >0 && count($wmain) >0) {
	foreach ($wmain as $rk) {
		$timestampF = $rk->max_timestamp;
		$dateF = gmdate("d M Y", $timestampF);
		if ($wassup_options->wassup_time_format == 24) {
			$datetimeF = gmdate('Y-m-d H:i:s', $timestampF);
			$timeF = gmdate("H:i:s", $timestampF);
		} else {
			$datetimeF = gmdate('Y-m-d h:i:s a', $timestampF);
			$timeF = gmdate("h:i:s a", $timestampF);
		}
		//$ip = @explode(",", $rk->ip);
		$ip_proxy = strpos($rk->ip,",");
		//if proxy, get 2nd ip...
		if ($ip_proxy !== false) {
			$ip = trim(substr($rk->ip,(int)$ip_proxy+1));
			if (empty($ip) || $ip == "unknown" || $ip == "127.0.0.1") {
				//if bad 2nd ip, use proxy ip
				$ip = substr($rk->ip,0,$ip_proxy-1);
			}
		} else { 
			$ip = $rk->ip;
		}
		if ($rk->hostname != "") $hostname = $rk->hostname; 
		else $hostname = "unknown";
		//$numurl = $wpdb->get_var("SELECT COUNT(DISTINCT id) as numurl FROM $wassup_table WHERE wassup_id='".$rk->wassup_id."'");
		$numurl = (int) $rk->page_hits;
		echo "\n";
?>
	<div id="delID<?php echo $rk->wassup_id; ?>" class="sum-rec <?php if ($wassup_options->wmark == 1 AND $wassup_options->wip == $ip) echo 'sum-mark'; ?>"> <?php
		// Visitor Record - raw data (hidden)
		$raw_div="raw-".substr($rk->wassup_id,0,25).rand(0,99);
		echo "\n"; ?>
                <div id="<?php echo $raw_div; ?>" style="display:none; padding-top:7px;" >
                <h2><?php _e("Raw data","wassup"); ?>:</h2>
                <style type="text/css">.raw { color: #542; padding-left:5px; }</style>
                <ul style="list-style-type:none;padding:20px 0 0 30px;">
		<li><?php echo __("Visit type","wassup").': <span class="raw">';
                if ($rk->username != "") { 
			echo __("Logged-in user","wassup").' - '.$rk->username;
			$unclass = "sum-box-log";
		} elseif ($rk->spam == "3") { 
                	_e("Spammer/Hacker","wassup");
			$unclass = "sum-box-spider";
		} elseif (!empty($rk->spam)) { 
                	_e("Spammer","wassup");
			$unclass = "sum-box-spider";
		} elseif ($rk->comment_author != "") { 
                	echo __("Comment author","wassup").' - '.$rk->comment_author;
			$unclass = "sum-box-aut";
		} elseif ($rk->feed != "") { 
			echo __("Feed","wassup").' - '.$rk->feed;
			$unclass = "sum-box-spider";
		} elseif ($rk->spider != "") { 
			echo __("Spider","wassup").' - '.$rk->spider;
			$unclass = "sum-box-spider";
		} else {
			 _e("Regular visitor","wassup");
			$unclass = "";
		}
		echo '</span>'; ?></li>
		<li><?php echo __("IP","wassup").': <span class="raw">'.$rk->ip.'</span>'; ?></li>
		<li><?php echo __("Hostname","wassup").': <span class="raw">'.$hostname.'</span>'; ?></li>
		<li><?php echo __("Url Requested","wassup").': <span class="raw">'.attribute_escape(htmlspecialchars(html_entity_decode($rk->urlrequested))).'</span>'; ?></li><?php
		if (!empty($rk->url_wpid) && is_numeric($rk->url_wpid)) {
			$p_title=$wpdb->get_var("SELECT `post_title` from {$wpdb->prefix}posts WHERE `ID` = {$rk->url_wpid}");
			echo "\n"; ?>
		<li style="text-indent:10px;"><?php echo __("Post/Page ID","wassup").': <span class="raw">'.$rk->url_wpid.'</span>'; ?></li>
		<li style="text-indent:10px;"><?php echo __("Post/Page Title","wassup").': <span class="raw">'.$p_title.'</span>'; ?></li><?php
		}
		echo "\n"; ?>
		<li><?php echo __("Referrer","wassup").': <span class="raw">'.attribute_escape(urldecode($rk->referrer)).'</span>'; ?></li><?php
		if ($rk->search != "") { ?>
		<li><?php echo __("Search Engine","wassup").': <span class="raw">'.$rk->searchengine.'</span> &nbsp; &nbsp; ';
		echo __("Search","wassup").': <span class="raw">'.$rk->search.'</span> &nbsp; &nbsp; '; 
		echo __("Page","wassup").': <span class="raw">'.$rk->searchpage.'</span>';?></li><?php
		} ?>
		<li><?php echo __("User Agent","wassup").': <span class="raw">'.attribute_escape(htmlspecialchars(html_entity_decode($rk->agent))).'</span>'; ?></li><?php
		if (empty($rk->spider) || $rk->browser != "") { 
			echo "\n"; ?>
		<li><?php echo __("Browser","wassup").': <span class="raw">'.$rk->browser.'</span>'; ?></li>
		<li><?php echo __("OS","wassup").': <span class="raw">'.$rk->os.'</span>'; ?></li><?php
		} elseif ($rk->os != "") { ?>
		<li><?php echo __("OS","wassup").': <span class="raw">'.$rk->os.'</span>'; ?></li><?php
		}
		if ($rk->language != "") { ?>
			<li><?php echo __("Locale/Language","wassup").': <span class="raw">'.$rk->language.'</span>'; ?></li><?php
		}
		if ($rk->screen_res != "") { ?>
		<li><?php echo __("Screen Resolution","wassup").': <span class="raw">'.$rk->screen_res.'</span>'; ?></li><?php
		} ?>
		<li><?php echo 'Wassup ID'.': <span class="raw">'.$rk->wassup_id.'</span>'; ?></li>
		<li><?php
		if ($numurl > 1) _e("End timestamp","wassup");
		else _e("Timestamp","wassup");
		echo ': <span class="raw">'.$datetimeF.' ( '.$rk->max_timestamp.' )</span>'; ?></li>
		</ul>
		</div> <!-- raw-wassup_id -->

		<?php //Visitor Record - detail listing
		if ($rk->referrer != '') {
			if ($rk->searchengine != "" || stristr($rk->referrer,$wpurl)!=$rk->referrer) { 
				if ($rk->searchengine == "") {
				$referrer = '<a href="'.wCleanURL($rk->referrer).'" target="_BLANK"><span style="font-weight: bold;">'.stringShortener($rk->referrer, round($max_char_len*.8,0)).'</span></a>';
				} else {
				$referrer = '<a href="'.wCleanURL($rk->referrer).'" target="_BLANK">'.stringShortener($rk->referrer, round($max_char_len*.9,0)).'</a>';
				}
			} else { 
                        $referrer = __('From your blog','wassup');
                        }
                } else { 
                        $referrer = __('Direct hit','wassup');
		} ?>
                <div class="<?php if ($wassup_options->wmark == 1 AND $wassup_options->wip == $ip) echo "sum-nav-mark"; else echo "sum-nav"; ?>">

			<p class="delbut"><?php
		// Mark/Unmark IP
                if ($wassup_options->wmark == 1 AND $wassup_options->wip ==  $ip) { ?>
			<a href="?<?php echo attribute_escape($URLQuery.'&deleteMARKED=1&dip='.$ip); ?>" style="text-decoration:none;" class="deleteIP"><img class="delete-icon" src="<?php echo WASSUPURL.'/img/cross.png" alt="'.__('delete','wassup').'" title="'.__('Delete ALL marked records with this IP','wassup'); ?>" /></a>
			<a href="?<?php echo attribute_escape($URLQuery.'&wmark=0'); ?>" style="text-decoration:none;"><img class="unmark-icon" src="<?php echo WASSUPURL.'/img/error_delete.png" alt="'.__('unmark','wassup').'" title="'.__('UnMark IP','wassup'); ?>" /></a><?php
		} else { ?>
			<a href="#" class="deleteID" id="<?php echo $rk->wassup_id ?>" style="text-decoration:none;"><img class="delete-icon" src="<?php echo WASSUPURL.'/img/b_delete.png" alt="'.__('delete','wassup').'" title="'.__('Delete this record','wassup'); ?>" /></a>
			<a href="?<?php echo attribute_escape($URLQuery.'&wmark=1&wip='.$ip); ?>" style="text-decoration:none;"><img class="mark-icon" src="<?php echo WASSUPURL.'/img/error_add.png" alt="'.__('mark','wassup').'" title="'.__('Mark IP','wassup'); ?>" /></a><?php
		} ?>
			<a href="#TB_inline?height=400&width=<?php echo $res.'&inlineId='.$raw_div; ?>" class="thickbox"><img class="table-icon" src="<?php echo WASSUPURL.'/img/b_select.png" alt="'.__('show raw table','wassup').'" title="'.__('Show the items as raw table','wassup'); ?>" /></a>
			</p>
			<div class="sum-box">
				<span class="sum-box-ip <?php echo $unclass ?>"><?php
			if ($numurl > 1) { ?><a  href="#" class="showhide" id="<?php echo $rk->id ?>"><?php print $ip; ?></a><?php
			} else { print $ip; } ?></span>
				<span class="sum-date"><?php print $datetimeF; ?></span>
			</div>
			<div class="sum-det">
				<span class="det1"><?php
			if (strstr($rk->urlrequested,'[404]')) {  //no link for 404 page
				print stringShortener($rk->urlrequested, round($max_char_len*.8,0)+5);
			} else {
				print '<a href="'.wAddSiteurl($rk->urlrequested).'" target="_BLANK">';
				print stringShortener($rk->urlrequested, round($max_char_len*.8,0)).'</a>';
			} ?></span>
				<span class="det2"><strong><?php _e('Referrer','wassup'); ?>: </strong><?php print $referrer; ?><br /><strong><?php _e('Hostname','wassup'); ?>:</strong> <?php echo $hostname; ?></span>
			</div>
		</div> <!-- /sum-nav -->
		<div class="detail-data">
			<?php 
			// Referer is search engine
			if ($rk->searchengine != "") {
			if (stristr($rk->searchengine,"images")!==FALSE || stristr($rk->referrer,'&imgurl=')!==FALSE) {
				$seclass = 'searchimage'; 
				$page = (number_format(($rk->searchpage / 19), 0) * 18); 
				$Apagenum = explode(".", number_format(($rk->searchpage / 19), 1));
				$pagenum = ($Apagenum[0] + 1);
				$url = parse_url($rk->referrer); 
				$ref = $url['scheme']."://".$url['host']."/images?q=".str_replace(' ', '+', $rk->search)."&start=".$page;
			} else {
				$seclass = '';
				$pagenum = $rk->searchpage;
				$ref = $rk->referrer;
			} ?>
			<ul class="searcheng <?php print $seclass; ?>">
			<li class="searcheng"><span class="indent-li-agent"><?php _e('SEARCH ENGINE','wassup'); ?>: <strong><?php print $rk->searchengine." (".__("page","wassup").": $pagenum)"; ?></strong></span></li>
			<li class="searcheng"><?php _e("KEYWORDS","wassup"); ?>: <strong><a href="<?php print wCleanURL($ref);  ?>" target="_BLANK"><?php print stringShortener($rk->search, round($max_char_len*.52,0)); ?></a></strong></li>
			</ul>
<?php 			}
			$Ocomment_author = "";
			$unclass = "users";
			// User is logged in, is an administrator, and/or is a comment author
			if ($rk->username != "") {
				$utype = __("LOGGED IN USER","wassup");
				$unclass = "userslogged";
				//check for administrator
				$udata=get_userdatabylogin($rk->username);
				if (!empty($udata->user_level) && $udata->user_level > 7) {
					$utype = __("ADMINISTRATOR","wassup");
					$unclass .= " adminlogged";
				}
				$Ocomment_author = '<li class="users"><span class="indent-li-agent">'.$utype.': <strong>'.$rk->username.'</strong></span></li>';
			}
			if ($rk->comment_author != "") {
				$Ocomment_author .= '<li class="users"><span class="indent-li-agent">'.__("COMMENT AUTHOR","wassup").': <strong>'.$rk->comment_author.'</strong></span></li>';
			}
			if (!empty($Ocomment_author)) { ?>
			<ul class="<?php print $unclass; ?>">
				<?php print $Ocomment_author; ?>
			</ul>
			<?php
			}
			// Referer is a Spider or Bot
			if ($rk->spider != "") {
			if ($rk->feed != "") { ?>
			<ul class="spider feed">
			<li class="feed"><span class="indent-li-agent"><?php _e('FEEDREADER','wassup'); ?>: <strong><a href="#" class="toggleagent" id="<?php echo $rk->id; ?>"><?php print $rk->spider; ?></a></strong></span></li>
<?php				if (is_numeric($rk->feed)) { ?>
			<li class="feed"><span class="indent-li-agent"><?php _e('SUBSCRIBER(S)','wassup'); ?>: <strong><?php print (int)$rk->feed; ?></strong></span></li>
<?php				} ?>
			</ul>
<?php 
			} else { ?>
			<ul class="spider">
			<li class="spider"><span class="indent-li-agent"><?php _e('SPIDER','wassup'); ?>: <strong><a href="#" class="toggleagent" id="<?php echo $rk->id; ?>"><?php print $rk->spider; ?></a></strong></span></li>
			</ul>
<?php			}
			} //end if spider
			// Referer is a SPAM
			if ($rk->spam > 0 && $rk->spam < 3) { ?>
			<ul class="spam">
			<li class="spam"><span class="indent-li-agent"><?php
				echo '<strong>'.__("Probably SPAM!","wassup").'</strong>'; 
				if ($rk->spam==2) { echo ' ('.__("Referer Spam","wassup").')'; }
				elseif (!empty($wassup_options->spam)) { 
					echo ' (Akismet '.__("Spam","wassup").')';
				} else {
					echo ' ('.__("Comment Spam","wassup").')';
				} ?> </span></li>
			</ul><?php
			} elseif ($rk->spam == 3) { ?>
                        <ul class="spam">
			<li class="spam"><span class="indent-li-agent">
			<?php _e("Probably hack attempt!","wassup"); ?></span></li>
			</ul><?php 
			}
			//hidden user agent string
			?><div style="display: none;" class="togglenavi naviagent<?php echo $rk->id ?>"><ul class="useragent">
				<li class="useragent"><?php _e('User Agent','wassup'); ?>: <strong><?php print $rk->agent; ?></strong></li>
			</ul></div>
			<?php
			// User os/browser/language
			if ($rk->spider == "" AND ($rk->os != "" OR $rk->browser != "")) {
				echo "\n"; ?>
			<ul class="agent">
			<li class="agent"><span class="indent-li-agent"><?php
				if ($rk->language != "") { ?>
				<img src="<?php echo WASSUPURL.'/img/flags/'.strtolower($rk->language).'.png'.'" alt="'.strtolower($rk->language).'" title="'.__("Language","wassup").': '.strtolower($rk->language); ?>" />&nbsp; <?php
				}
				_e("OS","wassup"); ?>: <strong><a href="#" class="toggleagent" id="<?php echo $rk->id; ?>"><?php print $rk->os; ?></a></strong></span></li>
			<li class="agent"><?php _e("BROWSER","wassup"); ?>: <strong><a href="#" class="toggleagent" id="<?php echo $rk->id; ?>"><?php print $rk->browser; ?></a></strong></li><?php 
				if ($rk->screen_res != "") { ?>
			<li class="agent"><?php _e("RESOLUTION","wassup"); ?>: <strong><?php print $rk->screen_res; ?></strong></li><?php
				} ?>
			</ul><?php
			}
			echo "\n"; ?>
			<div style="display: visible;" class="togglecrono navi<?php echo $rk->id ?>">
			<ul class="url"><?php 
		if ($numurl > 1) {
			//Important Note: list of urls visited is affected by browsers like Safari 4 which hits a page from both the user window and from it's "top sites" page, creating multiple duplicate records with distinct id's...
			//$qryCD = $wpdb->get_results("SELECT `timestamp`, urlrequested FROM $wassup_table WHERE wassup_id='".$rk->wassup_id."' ORDER BY `timestamp`");	//duplicates possible
			$qryCD = $wpdb->get_results("SELECT DISTINCT `timestamp`, urlrequested, agent FROM $wassup_table WHERE wassup_id='".$rk->wassup_id."' ORDER BY `timestamp`");	//no duplication, unless agent is differnt
			//$qryCD = $wpdb->get_results("SELECT `id`, `timestamp`, urlrequested FROM $wassup_table WHERE wassup_id='".$rk->wassup_id."' ORDER BY `id`");	//id is sequential, so sort order == visit order...UPDATE: may not be in visit order because 'insert delayed' could make `id` out of sync with `timestamp`
			$i=1;
			$char_len = round($max_char_len*.92,0);
			foreach ($qryCD as $cd) {	
				if ($wassup_options->wassup_time_format == 24) {
					$time2 = '&nbsp;&nbsp; '.gmdate("H:i:s", $cd->timestamp);
				} else {
					$time2 = gmdate("h:i:s a", $cd->timestamp);
				}
				$num = ($i&1);
				if ($num == 0) $classodd = "urlodd"; 
				else  $classodd = "url";
				if ($i < $numurl || $rk->urlrequested != $cd->urlrequested) { ?>
			<li class="<?php echo $classodd.' navi'.$rk->id; ?>"><span class="indent-li-nav"><?php echo $time2.' ->';
				if (strstr($cd->urlrequested,'[404]')) {  //no link for 404 page
					print stringShortener($cd->urlrequested, $char_len);
				} else {
					print '<a href="'.wAddSiteurl($cd->urlrequested).'" target="_BLANK">';
					print stringShortener($cd->urlrequested, $char_len).'</a>';
				} ?></span>
			</li><?php
				}
				$i++;
			} //end foreach qryCD
		} ?>
			</ul>
			</div><!-- /url -->
		</div><!-- /detail-data -->
		<p class="sum-footer"></p>
	</div><!-- /delID... -->
	<?php
	} //end foreach qry
	print $expcol; //moved
	} //end if witemstot > 0
	if ($witemstot > $witems) {
		print '<div align="center">'.$p->show().'</div><br />'."\n";
	} ?>
	</div><!-- /main-tabs --><?php
	// Print Google chart last to speed up detail display
	if (!empty($wassup_options->wassup_chart) || (!empty($_GET['chart']) && "1" == attribute_escape($_GET['chart']))) {
		$chart_type = ($wassup_options->wassup_chart_type >0)? $wassup_options->wassup_chart_type: "2";
		//show Google!Charts image
		if ($wpagestot > 12) {
			$chartwidth=$res;
			//let browser resize chart for small screens
			if ((int)$wassup_options->wassup_screen_res <800){
				$chartwidth=640;
			}
			$chart_url = $wTot->TheChart($wlast, $chartwidth, "180", $wsearch, $chart_type, "bg,s,dedade|c,lg,90,edffff,0,dedade,0.8", "page", $wtype);
			$html='<img src="'.$chart_url.'" alt="'.__("Graph of visitor hits","wassup").'" class="chart" width="'.$res.'" />';
		} else {
			$html='<p style="padding-top:10px;">'.__("Too few records to print chart","wassup").'...</p>';
		} 
	} else {
		$html='<p style="padding-top:10px">&nbsp;</p>';
	} //end if wassup_chart==1
?>
	<script type="text/javascript">
	//<![CDATA[
		var newhtml='<?php echo $html; ?>';
		jQuery('div#placeholder').html(newhtml).css("background-image","none");
	//]]>
	</script><?php

	} //end MAIN/DETAILS VIEW 

	// End calculating execution time of script
	$totaltime = sprintf("%8.8s",(microtime_float() - $starttime)); ?>
	<p><small>WassUp ver: <?php echo $wassupversion.' <span class="separator">|</span> '.__("Check the official","wassup").' <a href="http://www.wpwp.org" target="_BLANK">WassUp</a> '.__("page for updates, bug reports and your hints to improve it","wassup").' <span class="separator">|</span> <a href="http://trac.wpwp.org/wiki/Documentation" title="Wassup '.__("User Guide documentation","wassup").'">Wassup '.__("User Guide documentation","wassup").'</a>'; ?>
	<nobr><span class="separator">|</span> <?php echo __('Exec time','wassup').": $totaltime"; ?></nobr></small></p>
	<?php 
	if ($wdebug_mode) {
		//display MySQL errors/warnings in admin menus - for debug
		$wpdb->print_error();	//debug

		//restore normal mode
		@ini_set('display_errors',$mode_reset);	//turn off debug
	}
	?>
	</div>	<!-- end wrap --> 
<?php 
} //end function Wassup

//Since v.1.8: createTable/upgradeTable functions moved to 'upgrade.php'
//  module and are loaded during install/upgrade/uninstall only to keep WassUp fast.
} //end if is_admin

//### Wassup Tracking functions
//Set Wassup_id and cookie (before headers sent)
function wassupPrepend() {
	global $wassup_options, $current_user, $user_level, $wscreen_res, $wassup_cookie_value, $wdebug_mode;

	//reload wassup_options in case changed elsewhere (by admin)
	$wassup_options = new wassupOptions;

	//wassup must be active for tracking to begin
	if (empty($wassup_options->wassup_active)) {	//do nothing
		return;
	}
	$wassup_id = "";
	$session_timeout = 1;
	$wscreen_res = "";
	$cookieIP = "";
	$cookieHost = "";
	$cookieUser = "";
	$wassup_cookie_value="";
	$wassup_dbtask=array();

	//### Check for cookies in case this is an ongoing visit
	//#visitor tracking with "cookie"...
	if (isset($_COOKIE['wassup'])) {
		$wassup_cookie_value = $_COOKIE['wassup'];
		$cookie_data = explode('::',attribute_escape(base64_decode(urldecode($_COOKIE['wassup']))));
		$wassup_id = $cookie_data[0];
		if (!empty($cookie_data[1])) { 
			$wassup_timer = $cookie_data[1];
			$session_timeout = ((int)$wassup_timer - (int)time());
		}
		if (!empty($cookie_data[2])) $wscreen_res = $cookie_data[2];
		if (!empty($cookie_data[3])) {
			$cookieIP = $cookie_data[3];
			if (!empty($cookie_data[4])) {
				$cookieHost = $cookie_data[4];
			}
		}
		//new in v1.8.3: username in wassup cookie
		if (!empty($cookie_data[5])) {
			$cookieUser = $cookie_data[5];
		}
	}
	//set screen resolution value from cookie or browser header data, if any
	if (empty($wscreen_res)) {
		if (isset($_COOKIE['wassup_screen_res'])) {
			$wscreen_res = attribute_escape(trim($_COOKIE['wassup_screen_res']));
			if ($wscreen_res == "x") $wscreen_res="";
		} 
		if (empty($wscreen_res) && isset($_SERVER['HTTP_UA_PIXELS'])) {
			//resolution in IE/IEMobile header sometimes
			$wscreen_res = str_replace('X',' x ',$_SERVER['HTTP_UA_PIXELS']);
		}
		//if (empty($wscreen_res) && isset($_GET['wscr'])) {
		//	$wscreen_res = $_GET['wscr'];
		//} 
	}

	//First exclusion control is for admin user
	//if (empty($current_user->user_login)) { 
		get_currentuserinfo();	//sets $current_user, $user_xx 
	//}
	//sometimes current_user is empty during wordpress init so use "cookieUser" variable sometimes
	$logged_user = (!empty($current_user->user_login)? $current_user->user_login: $cookieUser);
	//exclude valid wordpress admin page visits and admin hits
	if (($wassup_options->wassup_admin == "1" || $user_level < 8) && (!is_admin() || empty($logged_user))) {

		//write wassup cookie for new visits, visit timeout (45 mins) or empty screen_res
		if (empty($wassup_id) || $session_timeout < 1 || (empty($cookie_data[2]) && !empty($wscreen_res))) {
			$ipAddress = "";
			$proxy = "";
			$hostname = "";
			$IP="";
			//#### Get the visitor's details from http header
			if (isset($_SERVER["REMOTE_ADDR"])) {
				$ipAddress = $_SERVER["REMOTE_ADDR"];
				if(isset($_SERVER['HTTP_CLIENT_IP']) && $ipAddress != $_SERVER["HTTP_CLIENT_IP"]) {
					$proxy = $_SERVER["HTTP_CLIENT_IP"];
				} elseif(isset($_SERVER['HTTP_X_REAL_IP']) && $ipAddress != $_SERVER["HTTP_X_REAL_IP"]) {
					$proxy = $_SERVER["HTTP_X_REAL_IP"];
				}
			} elseif(isset($_SERVER['HTTP_CLIENT_IP'])){
				$ipAddress = $_SERVER["HTTP_CLIENT_IP"];
			} elseif(isset($_SERVER['HTTP_X_REAL_IP'])) {
				$ipAddress = $_SERVER["HTTP_X_REAL_IP"];
			}
			if (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){
				if ($ipAddress != $_SERVER["HTTP_X_FORWARDED_FOR"]) {
					$proxy = $ipAddress;
				}
				//in case of multiple forwarding
				$IP = wValidIP($_SERVER["HTTP_X_FORWARDED_FOR"]);
			} else {
			 	$IP = wValidIP($ipAddress);
			}
			//TODO Doublecheck
			if (empty($IP) && !empty($proxy)) {
				$IP = wValidIP($proxy);
				if($ipAddress == $proxy ) {
				if(isset($_SERVER['HTTP_CLIENT_IP'])){
					$proxy = $_SERVER["HTTP_CLIENT_IP"];
				} elseif(isset($_SERVER['HTTP_X_REAL_IP'])){
					$proxy =$_SERVER['HTTP_X_REAL_IP'];
				} else {
					$proxy = "";
				}
				} else {
					$proxy = "";
				}
			}
			if (empty($IP) && empty($proxy)) {
				if (isset($_SERVER['HTTP_CLIENT_IP']))	{
					$IP = wValidIP($_SERVER["HTTP_CLIENT_IP"]);
				} elseif(isset($_SERVER['HTTP_X_REAL_IP'])){
					$IP = wValidIP($_SERVER['HTTP_X_REAL_IP']);
				}
			}
			if (!empty($cookieIP) && $cookieIP == $IP) {
				$hostname = $cookieHost;
			} elseif (!empty($IP)) {
				$hostname = @gethostbyaddr($IP);
				//exclude dummy addresses...
				if (empty($hostname) || $hostname == "unknown" || $hostname == "localhost.localdomain" || preg_match("/[a-z0-9\.]+\.local$/",$hostname)>0) {
					if (!empty($proxy)) {
						$IP = wValidIP($proxy);
						if (!empty($IP)) {
							$hostname = @gethostbyaddr($IP);
						}
						$proxy = "";
					}
				}
				if (!empty($proxy) && wValidIP($proxy) && $proxy != $IP) {
					$ipAddress = $proxy.",".$IP;
				} else {
					$ipAddress = $IP;
				}
			} elseif (!empty($cookieIP)) {
				$IP = $cookieIP;
				$hostname = $cookieHost;
			} else {
				$IP = $ipAddress;
			} //end if !empty(IP)
			if (empty($hostname) && !empty($cookieHost)) {
				$hostname = $cookieHost;
			}
			if (empty($IP)) { $IP = $ipAddress; }
			if (empty($hostname)) { $hostname = "unknown"; }
			$userAgent = (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
			$tempUA = $userAgent;
			$templen = strlen($userAgent);
			if ($templen == 0) $tempUA = "Unknown Spider";
			elseif ($templen<10) {
				$tempUA = $userAgent." $templen is too small";
			}
		//# Create a new id for this visit from a combination
		//#  of date/hour/min/ip/loggeduser/useragent/hostname.
		//#  It is not unique so that multiple visits from the 
		//#  same ip/userAgent within a 30 minute-period, can be 
		//#  tracked, even when session/cookies is disabled. 
			$temp_id = sprintf("%-060.60s", date('YmdH').str_replace(array(' ','http://','www.','/','.','\'','"',"\\",'$','-','&','+','_',';',',','>','<',':','#','*','%','!','@',')','(',), '', intval(date('i')/30).substr(strrev($IP),2).strrev($logged_user).strrev($tempUA).intval(date('i')/30)).date('HdmY').strrev($hostname).$templen.rand());

			//#assign new wassup id from "temp_id" 
			$wassup_id = md5($temp_id);
			$wassup_timer=((int)time() + 2700); //use 45 minutes timer

			//put the cookie in the oven and set the timer...
			//this must be done before headers sent
			if (defined('COOKIE_DOMAIN')) {
				$cookiedomain = preg_replace('#(https?\://)?(www\.)?#','',strtolower(COOKIE_DOMAIN));
				$cookiepath = "/";
			} else {
				$cookieurl = parse_url(get_option('home'));
				$cookiedomain = preg_replace('/^www\./i','',$cookieurl['host']);
				$cookiepath = $cookieurl['path'];
			}
			$expire = time()+3000;	//expire based on utc timestamp, not on Wordpress time
			$cookie_data = implode('::',array("$wassup_id","$wassup_timer","$wscreen_res","$IP","$hostname","$logged_user"));
			$wassup_cookie_value = urlencode(base64_encode($cookie_data));
			setcookie("wassup", "$wassup_cookie_value", $expire, $cookiepath, $cookiedomain);
			unset($temp_id, $tempUA, $templen);
		} //end if empty(wassup_id)

		//place wassup tag and javascript in document head
		add_action('wp_head', 'wassup_head', 10);
		//track visitor hits: 
		//  use 'send_headers' hook to track media, downloads, and feed hits
		if (preg_match("/(\.(gif|ico|jpe?g|png|svg|tiff|asp|cgi|css|doc|html?|js|jar|jsp|rdf|rtf|pdf|ppt|psd|txt|xls|xlt|xml|flv|mov|mpg|mp4|mp3|ogg|swf|wmv|Z|gz|zip)$)|[=\/](feed|atom)/i", $_SERVER['REQUEST_URI'])>0) {
			add_action('send_headers', 'wassupAppend');
		} else {
			//track 404 hit when it is 1st visit record
			if (is_404()) $req_code = 404;
			else $req_code = (isset($_SERVER['REDIRECT_STATUS'])?(int)$_SERVER['REDIRECT_STATUS']:200);
			if ($req_code != 200) {
				add_action('send_headers', 'wassupAppend', 15);
			} else {
			//...1-priority so runs before other shutdown actions
				add_action('shutdown','wassupAppend',1);
			}
		}
	} else {
		//successful login, retroactively undo "hack attempt" label
		//wassup timer is additional check in case successful hack
		if (!empty($wassup_id) && $session_timeout > 2500) { //TODO
			$wassup_table = (!empty($wassup_options->wassup_table)?$wassup_options->wassup_table:$wpdb->prefix."wassup");
			//queue the update because of "delayed insert"
			$wassup_dbtask[] = "UPDATE $wassup_table SET `spam`='0' WHERE `wassup_id`='$wassup_id' AND `spam`=3 ";
			wassup_scheduled_dbtask($wassup_dbtask);
		}
	} //end if wassup_admin && !is_admin
} //end function wassupPrepend

//Track visitors and save record in wassup table, after page is displayed
function wassupAppend() {
	global $wpdb, $wassup_options, $current_user, $user_level, $wassupversion, $wassup_cookie_value, $wscreen_res, $wdebug_mode;

	if ($wassup_options->wassup_active == 0) {	//do nothing
		return;
	}
	@ignore_user_abort(1); // finish script in background if visitor aborts

	if ($wdebug_mode) {	//#debug...
		//debug mode must be off for media and non-html requests 
		if (preg_match("/(\.(gif|ico|jpe?g|png|tiff|css|doc|js|jar|rdf|rtf|pdf|ppt|psd|txt|xls|xlt|xml|flv|mov|mpg|mp4|mp3|ogg|swf|wmv|Z|gz|zip)$)|[=\/](feed|atom)/i", $_SERVER['REQUEST_URI'])>0) {
			$wdebug_mode=false;
		} elseif (is_feed()) {
			$wdebug_mode=false;
		} else {
			$mode_reset=ini_get('display_errors');
			$debug_reset=$wdebug_mode;
			error_reporting(E_ALL);	//debug, E_STRICT=php5 only
			ini_set('display_errors','On');	//debug
			//Debug: Output open comment tag to hide PHP errors from visitors
			echo "\n<!-- *WassUp DEBUG On\n";   //hide errors
			$debug_output= "\n".date('H:i:s.u').' WassUp DEBUG On'."\n";
		}
	} else {
		//do only fatal error reporting
		// note: this won't work if PHP in safe mode
		$errlvl = @error_reporting();
		if (!empty($errlvl)) {
			@error_reporting(E_ERROR);
		}
	} //end if $wdebug_mode

	$wpurl = get_bloginfo('wpurl');
	$blogurl = get_option('home');
	$wassup_table = (!empty($wassup_options->wassup_table)?$wassup_options->wassup_table:$wpdb->prefix."wassup");
	$table_tmp_name = $wassup_table . "_tmp";
	$table_cache = $wassup_table."_meta";
	$wassup_rec = "";
	$wassup_dbtask=array();	//for scheduled db operations

	if (is_single() || is_page()) $post_ID = get_the_id();
	else $post_ID=0;
	//if (empty($current_user->user_login)) { 
		get_currentuserinfo();	//gets $current_user, $user_xx 
	//}
	$logged_user = (!empty($current_user->user_login)? $current_user->user_login: "");
	$urlRequested = $_SERVER['REQUEST_URI'];
	$hackercheck = false;
	$spam=0;
	if (empty($logged_user) && $wassup_options->wassup_hack == "1") {
		//no hack checks on css or image requests
		if (preg_match('/\.(css|jpe?g|gif|png|tiff)$/i',$_SERVER['REQUEST_URI'])==0) {
			$hackercheck = true;
		}
	}
	$timestamp  = current_time("timestamp"); //Add a timestamp to visit

	//# First exclusion control is for admin user
	if ($wassup_options->wassup_admin == "1" || $user_level < 8) {

	//# TODO?: Exclude wp-cron utility hits
	//if (stristr($urlRequested,"/wp-cron.php?doing_wp_cron")===FALSE || (!empty($_SERVER['SERVER_ADDR']) && $_SERVER['SERVER_ADDR'] != $_SERVER['REMOTE_ADDR'])) {

	//# Record non-admin page visits and or hack attempts
	if ((!is_admin() && stristr($urlRequested,"/wp-admin/")===FALSE && stristr($urlRequested,"/wp-includes/")===FALSE) || $hackercheck) {
		//Get post/page id, if any
		if (is_single() || is_page()) { $post_ID = get_the_id(); }
		else { $post_ID=0; }
		//TODO: get wordpress category-id/tag-id tag pages

		//# Exclude users and urls on exclusion list... 
		$exclude_visit = false;
		if (!empty($wassup_options->wassup_exclude_user) && !empty($logged_user)) {
			$exclude_list = explode(",", $wassup_options->wassup_exclude_user);
			foreach ($exclude_list as $exclude_user) {
				if ($exclude_user == $logged_user) {
					$exclude_visit = true;
					break 1;
				}
			}
		}
		//TODO: exclude page requests by post_id
		if (!empty($wassup_options->wassup_exclude_url) && !$exclude_visit) {
			$exclude_list = explode(",", $wassup_options->wassup_exclude_url);
			$pagerequest=strtolower(remove_query_arg('wscr',$urlRequested));
			foreach ($exclude_list as $exclude_url) {
				$exclude_page = strtolower($exclude_url);
				$i = strlen($exclude_url);
				if ($pagerequest == $exclude_page || substr($pagerequest,0,$i) == $exclude_page) {
					$exclude_visit = true;
					break 1;
				} elseif ("$pagerequest" == "{$blogurl}$exclude_page") {
					$exclude_visit = true;
					break 1;
				} elseif ("{$blogurl}$pagerequest" == "$exclude_page") {
					$exclude_visit = true;
					break 1;
				}
			}
		} //end if wassup_exclude_url

	//exclusion control by specific username/url
	if (!$exclude_visit) {
		$wassup_id = "";
		$cookieIP = "";
		$cookieHost = "";
		//check for wassup cookie and read contents
		if (empty($wassup_cookie_value) && isset($_COOKIE['wassup'])) {
			$wassup_cookie_value = $_COOKIE['wassup'];
		}
		if (!empty($wassup_cookie_value)) {
			$cookie_data = attribute_escape(base64_decode(urldecode($wassup_cookie_value)));
			$wassup_cookie = explode('::',$cookie_data);
			$wassup_id = $wassup_cookie[0];
			if (!empty($wassup_cookie[2])) { 
				$wscreen_res = $wassup_cookie[2];
			}
			if (!empty($wassup_cookie[3])) {
				$cookieIP = $wassup_cookie[3];
				if (!empty($wassup_cookie[4])) {
					$cookieHost = $wassup_cookie[4];
				}
			}
		}
		//### set screen resolution value from cookie or browser header data, if any
		if (empty($wscreen_res)) {
			if (isset($_COOKIE['wassup_screen_res'])) {
				$wscreen_res = attribute_escape(trim($_COOKIE['wassup_screen_res']));
				if ($wscreen_res == "x") $wscreen_res = "";
			} 
			if (empty($wscreen_res) && isset($_SERVER['HTTP_UA_PIXELS'])) {
				//resolution in IE/IEMobile header sometimes
				$wscreen_res = str_replace('X',' x ',attribute_escape($_SERVER['HTTP_UA_PIXELS']));
			}
		}
		//#### Get the visitor's details from http header...
		$ipAddress = "";
		$hostname = "";
		if (isset($_SERVER["REMOTE_ADDR"])) {
			$ipAddress = $_SERVER["REMOTE_ADDR"];
			$IP = "";
			if (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){
				$proxy = $ipAddress;
				//in case of multiple forwarding
				$IP = wValidIP($_SERVER["HTTP_X_FORWARDED_FOR"]);
				if ($IP) {
				if ($cookieIP == $IP) {
					$hostname = $cookieHost;
				} else {
					$hostname = @gethostbyaddr($IP);
					//exclude dummy addresses...
					if (!empty($hostname) && $hostname != "unknown" && $hostname != "localhost.localdomain") {
					if (!wValidIP($proxy)) {
						$ipAddress = $IP;
					} else {
						$ipAddress = $proxy.",".$IP;
					}
					} else {
						$hostname = "";
					}
				}
				} //end if IP
			}
			if (empty($IP) || empty($hostname)) {
				$IP = $_SERVER["REMOTE_ADDR"];
				if (wValidIP($IP)) {
					if ($cookieIP == $IP) {
						$hostname = $cookieHost;
					} else {
						$hostname = @gethostbyaddr($IP);
					}
				} elseif (!empty($cookieIP)) {
					$IP=$cookieIP;
					$hostname=$cookieHost;
				}
			}
		} //end if REMOTE_ADDR
		if (empty($IP)) { $IP = $ipAddress; }
		if (empty($hostname)) { $hostname = "unknown"; }

		//'referrer' must be cleaned when added to table, or used 
		// in a query on database, or when displayed to screen...
		// NOT before...otherwise tests for search engines and 
		//  search phrase will fail.
		$referrer = (isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER']: '');
    		$userAgent = (isset($_SERVER['HTTP_USER_AGENT']) ? rtrim($_SERVER['HTTP_USER_AGENT']) : '');
		if (strlen($userAgent) > 255) {
			$userAgent=substr(str_replace(array('  ','%20%20','++'),array(' ','%20','+'),$userAgent),0,255);
		}
    		$language = (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? attribute_escape($_SERVER['HTTP_ACCEPT_LANGUAGE']) : '');
    		$comment_user = (isset($_COOKIE['comment_author_'.COOKIEHASH]) ? utf8_encode($_COOKIE['comment_author_'.COOKIEHASH]) : '');

	//### Additional recording exclusion controls...
	//# Exclude IPs on exclusion list... 
	if (empty($wassup_options->wassup_exclude) || empty($IP) ||
	     strstr($wassup_options->wassup_exclude,$IP) == FALSE) {
	
	//if (stristr($urlRequested,"favicon.ico") === FALSE) {		//moved
	//# Exclude requests for themes and plugins from recordings
	if (stristr($urlRequested,"/wp-content/plugins") === FALSE || stristr($urlRequested,"forum") !== FALSE || $hackercheck) {
	if (stristr($urlRequested,"/wp-content/themes") === FALSE || stristr($urlRequested,"comment") !== FALSE) {
		
	//# Exclude user-selected options
	if ($wassup_options->wassup_loggedin == 1 || !is_user_logged_in()) {
	if ($wassup_options->wassup_attack == 1 || stristr($userAgent,"libwww-perl") === FALSE ) {
		//# Check for 404 requests before duplicate check, then 
		// track them for first hit or for hack attempts only
		if (is_404()) { $req_code = 404; }
		else { $req_code = (isset($_SERVER['REDIRECT_STATUS'])?(int)$_SERVER['REDIRECT_STATUS']:200); }
		if ($req_code != 200) {
			$urlRequested = "[$req_code] ".$_SERVER['REQUEST_URI'];
		}
	//#===================================================
	//### Start recording visit....
	$browser = "";
	$os = "";
	$spider = "";
	$feed = "";
	//Work-around for cookie rejection:
	//#assign a new wassup id and use it in dup check
	if (empty($wassup_id)) {
		//# Create a new id for this visit from a combination
		//#  of date/hour/min/ip/loggeduser/useragent/hostname.
		//#  It is not unique so that multiple visits from the 
		//#  same ip/userAgent within a 30 minute-period, can be 
		//#  tracked, even when session/cookies are disabled. 
		$tempUA = $userAgent;
		$templen = strlen($userAgent);
		if ($templen == 0) $tempUA = "Unknown Spider";
		elseif ($templen<10) {
			$tempUA = $userAgent." $templen is too small";
		}
		$temp_id = sprintf("%-060.60s", date('YmdH').str_replace(array(' ','http://','www.','/','.','\'','"',"\\",'$','-','&','+','_',';',',','>','<',':','#','*','%','!','@',')','(',), '', intval(date('i')/30).substr(strrev($IP),2).strrev($logged_user).strrev($tempUA).intval(date('i')/30)).date('HdmY').strrev($hostname).$templen.rand());
		$wassup_id = md5($temp_id);
		//echo "temp_id=$temp_id\n";	//debug
		unset($temp_id, $tempUA, $templen);	//to free memory
	}
	//# Check for duplicates, previous spam check, and screen res.
	// and get previous settings to prevent redundant checks on same
	// visitor.  Dup==same wassup_id, same URL, and timestamp <180 secs
	$dup_urlrequest=0;
	$wpageviews=0;
	$spamresult=0;
	$wpdb->query("SET wait_timeout = 7"); //don't wait for slow responses
	$recent_hit = $wpdb->get_results("SELECT wassup_id, urlrequested, spam, screen_res, `timestamp`, browser, spider, os, feed, `language`, `agent`, `referrer` FROM ".$table_tmp_name." WHERE wassup_id='".$wassup_id."' AND `timestamp` >".($timestamp-180)." ORDER BY `timestamp` DESC");
	if (!empty($recent_hit)) {
		$wpageviews=count($recent_hit);
		//check 1st record only
		//record is dup if same url (with 'wscr' removed) and same user-agent
		if ($recent_hit[0]->urlrequested == $urlRequested || $recent_hit[0]->urlrequested == remove_query_arg('wscr',$urlRequested) || $recent_hit[0]->urlrequested == "[404] $urlRequested") {
			if ($recent_hit[0]->agent == $userAgent || empty($recent_hit[0]->agent)) {
				$dup_urlrequest=1;
			}
		} elseif (preg_match("/\.(gif|ico|jpe?g|png|tiff)$/", $_SERVER['REQUEST_URI']) >0) {
			//exclude images/photos only after confirmation of other valid page hit by visitor
			$dup_urlrequest=1;
		}

		//retrieve previous spam check results
		$spamresult = $recent_hit[0]->spam;

		//check for screen resolution and update, if not previously recorded
		//...queue the update because of "delayed insert"
		if (empty($recent_hit[0]->screen_res) && !empty($wscreen_res)) {
			$wassup_dbtask[] = "UPDATE $wassup_table SET `screen_res`='$wscreen_res' WHERE `wassup_id`='$wassup_id' AND `screen_res`='' ";
		}

		//get previously recorded settings for this visitor to
		//  avoid redundant tests
		if ($dup_urlrequest == 0) {
			if (empty($wscreen_res) && !empty($recent_hit[0]->screen_res)) {
				$wscreen_res = $recent_hit[0]->screen_res;
			}
			if ($spam == 0 && (int)$spamresult >0 ) {
				$spam = $spamresult;
			}
			if ($recent_hit[0]->agent == $userAgent || empty($userAgent)) {
				$browser = $recent_hit[0]->browser;
				$spider = $recent_hit[0]->spider;
				$os = $recent_hit[0]->os;
				if (empty($language)) {
					$language = $recent_hit[0]->language;
				}
				//feed reader only if this page is feed
				if (!empty($recent_hit[0]->feed) && is_feed()) {
					$feed = $recent_hit[0]->feed;
				}
			}
		}
		// Detect disguised spiders and harvesters by checking for
		//  excessive pageviews (threshold: 8+ views in < 16 secs)
		if ($wpageviews >7 && empty($spider)) {
			$pageurls = array();
			$visitstart = $recent_hit[7]->timestamp;
			if (($timestamp - $recent_hit[7]->timestamp) < 16) {
				$is_spider=true;
				$pageurls[]="$urlRequested";
				//a spider is unlikely to hit same page 2+ times
				foreach ($recent_hit AS $w_pgview) {
					if (stristr($w_pgview->urlrequested,"robots.txt")!==false) {
						$is_spider = true;
						break;
					} elseif (in_array($w_pgview->urlrequested,$pageurls)) {
						$is_spider = false;
						break;
					} else {
						$pageurls[] = $w_pgview->urlrequested;
					}
				}
				if ($is_spider) {
					$spider = __("Unknown Spider","wassup");
					$wassup_dbtask[] = "UPDATE $wassup_table SET `spider`='$spider' WHERE `wassup_id`='$wassup_id' AND `spider`=''";
				}
			}
		}
	} //end if recent_hit
	if ($wdebug_mode) {	//debug
		if (!empty($recent_hit)) {
			echo "<br />\nRecent visit data found in wassup_tmp:\n"; //debug
			print_r($recent_hit); //debug
			echo "\n";
			$debug_output .="\n\n".date('H:i:s.u').' Recent data lookup results: $recent_hit='.serialize($recent_hit)."\n";
			if ($dup_urlrequest == 1) {
				echo "\nDuplicate record!\n";
			}
			if ($recent_hit[0]->agent != $userAgent) {
				echo "\nUser Agents NOT Identical:";
				echo "\n\tCurrent user agent: ".$userAgent;
				echo "\n\tPrevious user agent:".$recent_hit[0]->agent."\n";
			}
		} else {
			echo "<br />\nNo Recent visit data found in wassup_tmp.\n"; //debug
		}
	}
	$wpdb->query("SET wait_timeout = 60");

	//don't record 404 unless 1st visit or hack attempt
	if ($req_code == 200 || empty($recent_hit) || ($hackercheck && (stristr($urlRequested,"/wp-")!==FALSE || preg_match('/\.(php|ini|aspx?|dll|cgi)|(\.\.\/\.\.\/|root[^a-z0-9\-_]|[^a-z0-9\-_]passw|\=admin[^a-z0-9\-_]|\=\-\d+|(bin|etc)\/)/i',$urlRequested)>0))) {
		//identify hackers 
		if ($req_code != 200 && preg_match('#(([a-z0-9_\*\-\#\,]+\.php[456]?)|\.(?:cgi|aspx?)|[\*\,\'"]|\=\-1$)#i',$urlRequested,$matches)>0) {
			//visitors requesting non-existent server-side scripts are up to no good
			if (empty($matches[2]) || $matches[2]!= "index.php") {
				$spam = 3;
			}
		} elseif (preg_match('#\.\./\.\./(etc/passwd|\.\./\.\./)#i',$urlRequested)>0 || preg_match('#[\[&\?/](dir|document_root\]?|id|page|thisdir)\=https?\://#i',$urlRequested)>0) { 
			//anyone trying to access root files, password or ids are up to no good
			$spam = 3;
		} elseif (wIsAttack()) {
			$spam = 3;
		}
		//retroactively update record for hack attempt
		if ($spam == "3" && $spamresult == "0" && !empty($recent_hit)) {
			$wassup_dbtask[] = "UPDATE $wassup_table SET `spam`='3' WHERE `wassup_id`='$wassup_id' AND `spam`='0' ";
		}

	//# Exclude duplicates and avoid redundant checks for multi-page visitors
	if ($dup_urlrequest == 0) {
		//##### Extract useful visit information from http header..
		//#Identify user-agent...
		if (empty($browser) && empty($spider)) {
			$ua = new UADetector();
			if (!empty($ua->name)) {
			if ($ua->agenttype == "B") {
				$browser = $ua->name;
				if (!empty($ua->version)) { 
					$browser .= " ".wMajorVersion($ua->version);
					if (strstr($ua->version,"Mobile")!==false) {
						$browser .= " Mobile";
					}
				}
			} else {
				$spider = $ua->name;
				if ($ua->agenttype == "F") {
					if (!empty($ua->subscribers)) {
						$feed = $ua->subscribers;
					} else {
						$feed = $spider;
					}
				} elseif ($ua->agenttype == "H" || $ua->agenttype == "S") {	//it's a script injection bot|spammer
					if ($spam == "0") { $spam = 3; }
				}
			} //end else agenttype
				$os = $ua->os;
				if (!empty($ua->resolution)) {
					$wscreen_res = (preg_match('/^\d+x\d+$/',$ua->resolution)>0)?str_replace('x',' x ',$ua->resolution):$ua->resolution;
				}
				if (!empty($ua->language) && empty($language)) {
					$language=$ua->language;
				}
				if ($wdebug_mode) $debug_output .= "\n".date('H:i:s.u').' UAdetecter results: $ua='.serialize($ua)."\n";
			} //end if $ua->name

			$agent = (!empty($browser)?$browser:$spider);

			//#Identify agent with wGetBrowser
 			if ((empty($agent) || stristr($agent,'unknown')!==false) && !empty($userAgent)) {
				list($browser,$os) = wGetBrowser($userAgent);
				if (!empty($browser)) $agent= $browser;
				if ($wdebug_mode) $debug_output .= "\n".date('H:i:s.u').' wGetBrowser results: $browser='.$browser.'  $os='.$os."\n";
			}

			//# Some spiders, such as Yahoo and MSN, don't 
			//  always give a unique useragent. Test against 
			//  a list of known hostnames/IP to identify these
			//  spiders. -Helene D.
			$spider_hosts='/^((65\.55|207\.46)\.\d{3}.\d{1,3}|.*\.crawl\.yahoo\.net|ycar\d+\.mobile\.[a-z0-9]{3}\.yahoo\.com|msnbot.*\.search\.msn\.com|crawl[0-9\-]+\.googlebot\.com|(crawl(?:er)?|spider|robot)\-?\d*\..*)$/';
			//#Identify spiders and feeds with wGetSpider...
			if (empty($agent) || stristr($agent,'unknown')!==false || preg_match($spider_hosts,$hostname)>0 ) {
				list($spider,$spidertype,$feed) = wGetSpider($userAgent,$hostname,$browser);
				if ($wdebug_mode) $debug_output .= "\n".date('H:i:s.u').' wGetSpider results: $spider='.$spider.'  $spidertype='.$spidertype.' $feed='.$feed."\n";
				//it's a browser
				if ($spidertype == "B" && $urlRequested != "/robots.txt") { 
					if (empty($browser)) $browser = $spider;
					$spider = "";
					$feed = "";

				//it's a script injection bot|spammer
				} elseif ($spidertype == "H" || $spidertype == "S") {
					if ($spam == "0") $spam = 3;
				}

			//#Identify spiders and feed with wGetSpider...
			} elseif(!empty($userAgent) && (strlen($agent)<5 || strstr($agent,'N/A') || ($agent == $browser && (empty($os) || preg_match("#\s?([a-z]+(?:bot|crawler|spider|reader|agent))[^a-z]#i",$userAgent)>0 || strstr($urlRequested,"robots.txt")!==FALSE || is_feed())))) {
				list($spider,$spidertype,$feed) = wGetSpider($userAgent,$hostname,$browser);
				if ($wdebug_mode) $debug_output .= "\n".date('H:i:s.u').' wGetSpider results: $spider='.$spider.'  $spidertype='.$spidertype.' $feed='.$feed."\n";
				//it's a browser
				if ($spidertype == "B" && $urlRequested != "/robots.txt") { 
					if (empty($browser)) $browser = $spider;
					$spider = "";
					$feed = "";
				} elseif ($spidertype == "H" || $spidertype == "S") {	//it's a script injection bot|spammer
					if ($spam == "0") $spam = 3;
				}

			//no userAgent == spider
 			} elseif (empty($userAgent) && empty($agent)) {
				$spider = __("Unknown Spider","wassup");
			} 

		} //end if empty(browser) && empty(spider)

		//if 1st request is "robots.txt" then this is a bot
		if (empty($spider) && strstr($urlRequested,"robots.txt")!==FALSE && empty($recent_hit)) {
			$spider = __("Unknown Spider","wassup");

		//empty userAgent is a bot
		} elseif (empty($spider) && empty($browser) && empty($userAgent)) {
			$spider = __("Unknown Spider","wassup");
		}

	//spider exclusion control
	//# Spider exclusion control moved to avoid unneeded tests
	if ($wassup_options->wassup_spider == 1 || $spider == '') {

		//# some valid spiders to exclude from spam check below
		$goodbot = false;
		if ($hostname!="" && !empty($spider) && preg_match('#^(googlebot|bingbot|msnbot|yahoo\!\sslurp|technorati)#i',$spider)>0 && preg_match('#\.(googlebot|live|msn|yahoo|technorati)\.(com|net)$#i',$hostname)>0){
			$goodbot = true;
		}

        //do spam exclusion controls, unless disabled in wassup_spamcheck
	if ($wassup_options->wassup_spamcheck == 1 && $spam == 0 && !$goodbot) {
		$spamComment = New wassup_checkComment;

		//### 1st Check for referrer spam...faster, if positive
		if ($wassup_options->wassup_refspam == 1 && !empty($referrer)) {
			//#...skip if referrer is own blog
			if (stristr($referrer,$wpurl)===FALSE && stristr($referrer,$blogurl)===FALSE && !$wdebug_mode) {
			// Do a control if it is Referrer Spam
			//check if referrer is a previous comment spammer
			if ($spamComment->isRefSpam($referrer)>0) {
				$spam = 2;
			//check referer against a list of known spammers
			} else {
				if ($wdebug_mode) {
			 		$isspam = wGetSpamRef($referrer,$hostname);
				} else {
			 		$isspam = @wGetSpamRef($referrer,$hostname);
				}
				if ($isspam) $spam = 2;
			}
			}
		}
        	
		//### Check for comment spammers...
		//# No spam check on known bots (google, yahoo,...) unless
		//#  there is a comment or forum page request...
		if ($spam == 0 && (empty($spider) || stristr($urlRequested,"comment")!== FALSE || stristr($urlRequested,"forum")!== FALSE  || !empty($comment_user))) { 

		//if (isset($spamresult) && stristr($urlRequested,"comment") === FALSE && stristr($urlRequested,"forum") === FALSE && empty($comment_user) && empty($_POST['comment'])) {
		//	$spam = $spamresult;	//redundant - set in dup check

			//check for previous spammer detected by anti-spam plugin
			$spammerIP = $spamComment->isSpammer($IP); //TODO: IP or ipAddress?
			if ($spammerIP > 0) {	//is previous comment spam
				$spam = 1;
			}
			//set as spam if both URL and referrer are "comment" and browser is obsolete or Opera
			if ($spam== 0 && $wassup_options->wassup_spam==1 && stristr($urlRequested,"comment")!== FALSE && stristr($referrer,"#comment")!==FALSE && (stristr($browser,"opera")!==FALSE || preg_match('/^(AOL|Netscape|IE)\s[1-6]$/',$browser)>0)) {
				$spam=1;
			}
			//#lastly check for comment spammers using Akismet API
			// Note: Akismet spam test may cause "header already sent" errors with "send_headers" hook in some Wordpress configurations
			if ($spam == 0 && $wassup_options->wassup_spam == 1 && stristr($urlRequested,"comment")!== FALSE && stristr($urlRequested,"/comments/feed/")== FALSE) {
				$akismet_key = get_option('wordpress_api_key');
				$akismet_class = dirname(__FILE__).'/lib/akismet.class.php';
			if (!empty($akismet_key) && file_exists($akismet_class)) {
				// load array with comment data 
				$comment_user_email = (!empty($_COOKIE['comment_author_email_'.COOKIEHASH])? utf8_encode($_COOKIE['comment_author_email_'.COOKIEHASH]):"");
				$comment_user_url = (!empty($_COOKIE['comment_author_url_'.COOKIEHASH])? utf8_encode($_COOKIE['comment_author_url_'.COOKIEHASH]):"");
				$Acomment = array(
					'author' => $comment_user,
					'email' => $comment_user_email,
					'website' => $comment_user_url,
					'body' => (isset($_POST["comment"])? $_POST["comment"]:""),
					'permalink' => $urlRequested,
					'user_ip' => $ipAddress,
					'user_agent' => $userAgent);

				// instantiate an instance of the class 
				if (!class_exists('Akismet')) {
					include_once($akismet_class);
				}
				$akismet = new Akismet($wpurl, $akismet_key, $Acomment);
				// Check if it's spam
				if ( $akismet->isSpam() && !$akismet->errorsExist()) {
					$spam = 1;
				}
			} //end if !empty(akismet_key)
			} //end if wassup_spam

			//retroactively update visitor's hits as spam, in case late detection
			if (!empty($recent_hit) && !empty($spam) && $spamresult==0) {
				//queue the update...
				$wassup_dbtask[]="UPDATE $wassup_table SET `spam`='".$spam."' WHERE `wassup_id`='".$wassup_id."' AND `spam`='0' ";
			}

		} //end if spam == 0
	} //end if wassup_spamcheck == 1

	//## Final exclusion control is spam...
	if ($spam == 0 OR ($wassup_options->wassup_spam == 1 AND $spam == 1) OR ($wassup_options->wassup_refspam == 1 AND $spam == 2) OR ($wassup_options->wassup_hack == 1 AND $spam == 3)) {
	if (stristr($urlRequested,"/wp-content/plugins/")===FALSE OR $spam == 3) {
		//###More user/referrer details for recording
		//#get language/locale info from hostname or referrer data
		$language = wGetLocale($language,$hostname,$referrer);

		//# get search engine and search keywords from referrer 
		$searchengine="";
		$search_phrase="";
		$searchpage="";
		$searchcountry="";
		//don't check own blog for search engine data
		if (!empty($referrer) && $spam == "0" && stristr($referrer,$blogurl)!=$referrer && !$wdebug_mode) {
			//if ($wdebug_mode) {	//debug
			//	echo '<br />\n$Referrer="'.$referrer.'" is NOT own site: '.$blogurl.'. Checking for search engine data...'."\n"; //debug
			//}
			//get GET type search results, ex: search=x
			if (strpos($referrer,'=')!==false) {
				list($searchengine,$search_phrase,$searchpage,$searchlang,$searchlocale)=explode("|",wGetSE($referrer));
				if ($search_phrase != '') {
					$sedomain = parse_url($referrer);
					$searchdomain = $sedomain['host'];
				}
			}
			//get other types of search results, ex: search/x
			if ($search_phrase == '') {
				$se=seReferer($referrer);
				if (!empty($se['Query']))  {
					$search_phrase = $se['Query'];
					$searchpage = $se['Pos'];
					$searchdomain = $se['Se'];
				} else {
					$searchengine = "";
				}
			}
			if ($search_phrase != '')  {
			if (!empty($searchengine)) {
				if (stristr($searchengine,"images")===FALSE && stristr($referrer,'&imgurl=')===FALSE) {
				// 2011-04-18: "page" parameter is now used on referrer string for Google Images et al.
				if (preg_match('#page[=/](\d+)#i',$referrer,$pcs)>0) {
					if ($searchpage != $pcs[1]) {
						$searchpage = $pcs[1];
					}
				} else {
	  	 		// NOTE: Position retrieved in Google Images is 
	   			// the position number of image NOT page rank position like web search
	   				$searchpage=(int)($searchpage/10)+1;
	   			}
				}
				//append country code to search engine name
				if (preg_match('/(\.([a-z]{2})$|^([a-z]{2})\.)/i',$searchdomain,$match)) {
	   				if (!empty($match[2])) {
	   				    	$searchcountry = $match[2];
	   				} elseif (!empty($match[3])) {
	   				    	$searchcountry = $match[3];
					}
					if (!empty($searchcountry) && $searchcountry != "us") {
						$searchengine .= " ".strtoupper($searchcountry);
					}
				}
			} else {
				$searchengine = $searchdomain;
			}
			//use search engine country code as locale
			$searchlocale = trim($searchlocale);
			if (!empty($searchlocale)) {
				$language = $searchlocale;
			} elseif (!empty($searchcountry) && ($language == "us" || empty($language))) {
				$language=$searchcountry;
			}
		} //end if search_phrase
		} //end if (!empty($referrer)
		if ($searchpage == "") {
			$searchpage = 0;
		}

		// #Record visit in wassup tables...
		// #create record to add to wassup tables...	
		$wassup_rec = array('wassup_id'=>$wassup_id, 
				'timestamp'=>$timestamp, 
				'ip'=>$ipAddress, 
				'hostname'=>$hostname, 
				'urlrequested'=>$urlRequested, 
				'agent'=>$userAgent,
				'referrer'=>$referrer, 
				'search'=>$search_phrase,
				'searchpage'=>$searchpage,
				'searchengine'=>$searchengine,
				'os'=>$os, 
				'browser'=>$browser, 
				'language'=>$language, 
				'screen_res'=>$wscreen_res, 
				'spider'=>$spider, 
				'feed'=>$feed, 
				'username'=>$logged_user, 
				'comment_author'=>$comment_user, 
				'spam'=>$spam,
				'url_wpid'=>$post_ID);

		// Insert the record into the db
		insert_into_wp($wassup_table, $wassup_rec);
		// Insert the record into the wassup_tmp table too
		insert_into_wp($table_tmp_name, $wassup_rec);
		// Delete records older then 3 minutes
		if (((int)$timestamp)%17 == 0 ) { 
			$wassup_dbtask[] = "DELETE FROM $table_tmp_name WHERE `timestamp`<'".($timestamp - 3*60)."' ";
		}

        } //end if !wp-content/plugins
        } //end if $spam == 0

        } //end if wassup_spider
	} //end if dup_urlrequest == 0
        } //end if !is_404

        } //end if wassup_attack
        } //end if wassup_loggedin

        } //end if !themes
        } //end if !plugins
	//} //end if !favicon

	} //end if wassup_exclude
	} //end if !exclude_visit
	} //end if !is_admin
	//} //TODO end if wp-cron.php?doing_wp_cron===FALSE
	} //end if loggeduser_level
	
	//### Notify admin if alert is set and wassup table > alert
	if ($wassup_options->wassup_remind_flag == 1) {
	   // check database size ~every 5 minutes to keep wassup fast...
	   if ( (time())%299 == 0 ) {
		$table_status = $wpdb->get_results("SHOW TABLE STATUS LIKE '$wassup_table'");
		foreach ($table_status as $fstatus) {
			$data_lenght = $fstatus->Data_length;
		}
		$tusage = ($data_lenght/1024/1024);
		if ($tusage > $wassup_options->wassup_remind_mb) {
			$recipient = get_bloginfo('admin_email');
			$sender = get_bloginfo('name').' <wassup_noreply@'.parse_url($blogurl,PHP_URL_HOST).'>';
                        $subject = "[ALERT]".__('WassUp Plugin table has reached maximum size!','wassup');
                        $message = __('Hi','wassup').",\n".__('you have received this email because your WassUp Database table at your Wordpress blog','wassup')." ($wpurl) ".__('has reached the maximum value you set in the options menu','wassup')." (".$wassup_options->wassup_remind_mb." Mb).\n\n";
                        $message .= __('This is only a reminder, please take the actions you want in the WassUp options menu','wassup')." (".get_bloginfo('url')."/wp-admin/admin.php?page=wassup-options).\n\n".__('This alert now will be removed and you will be able to set a new one','wassup').".\n\n";
                        $message .= __('Thank you for using WassUp plugin. Check if there is a new version available here:','wassup')." http://wordpress.org/extend/plugins/wassup/\n\n".__('Have a nice day!','wassup')."\n";
                        mail($recipient, $subject, $message, "From: $sender");
                        $wassup_options->wassup_remind_flag = 2;
                        $wassup_options->saveSettings();
		}
	   }
	} //if wassup_remind_flag

	//automatic database cleanup tasks...do every few visits
	if (((int)$timestamp)%119 == 0) {
		$n = count($wassup_dbtask);
		//### Purge old records from wassup table
		//Don't combine purge with other database tasks
		if (!empty($wassup_options->delete_auto) && $wassup_options->delete_auto!="never" && $n==0) {
			//use visit timestamp not current time for delete
			$delete_from = @strtotime($wassup_options->delete_auto, $timestamp);
			$delete_condition = ""; 
			if (is_numeric($delete_from) && $delete_from < $timestamp) {
				$delete_condition = "`timestamp`<'$delete_from'";
				if (!empty($wassup_options->delete_filter)) {
					if ($wassup_options->delete_filter =="spider"){
						$delete_condition .= " AND spider!=''";
					} elseif ($wassup_options->delete_filter =="spam"){
						$delete_condition .= " AND spam>0";
					}
				}
				//#check before doing delete as it locks the table...
				if ((int)$wpdb->get_var("SELECT COUNT(id) FROM $wassup_table WHERE $delete_condition") > 0) {
					$wassup_dbtask[] = "DELETE FROM $wassup_table WHERE $delete_condition";
				}
			}
		}
		//### clean up wassup_meta cached records that have expired
		if (!empty($wassup_options->wassup_cache)) {
			$expire_stamp = time() - 3600;
			$wassup_dbtask[] = "DELETE FROM $table_cache WHERE `meta_expire`>'0' AND `meta_expire`<'$expire_stamp'";
		}//end if delete_auto

		//### Optimize table once a day
		if ($timestamp > ($wassup_options->wassup_optimize+24*3600) && count($wassup_dbtask)==0) {
			$wassup_options->wassup_optimize = current_time('timestamp');
                        $wassup_options->saveSettings();
			$wassup_dbtask[] = "OPTIMIZE TABLE $wassup_table ";
		}
	}

	//perform scheduled database tasks 
	if (count($wassup_dbtask)>0) {
		if ($wdebug_mode) {
			$debug_output.="\n"."Performing Scheduled tasks:".serialize($wassup_dbtask)."\n";
		}
		wassup_scheduled_dbtask($wassup_dbtask);
	}
	if ($wdebug_mode) {
		if (!empty($wassup_rec)) {
			echo "<br />\nWassUp record data:\n";
			print_r($wassup_rec); //debug
			echo "<br />\n*** Visit recorded ***\n"; //debug
		} else {
			echo "<br />\n*** Visit was NOT recorded! ***\n"; //debug
		}
		echo "<br />\n--> \n";	//close comment tag to hide debug data from visitors 
		//restore normal mode
		@ini_set('display_errors',$mode_reset);
	}
} //end function wassupAppend()

// Function to insert the item into the db
function insert_into_wp($wTable, $wassup_rec) {
	global $wpdb, $wassup_options;

	$wassup_table = $wassup_options->wassup_table;
	$wassup_tmp_table = $wassup_table . "_tmp";
	$delayed="";	//for delayed insert

	//check that wassup_rec is valid associative array
	if (is_array($wassup_rec) && !empty($wassup_rec['wassup_id'])) {
		if ($wTable == $wassup_table && !empty($wassup_options->wassup_dbengine) && stristr($wassup_options->wassup_dbengine,"isam")!==false) {
			$delayed="DELAYED";	//for delayed insert
		}
		//double-check that table exists to avoid errors displaying on blog page
        	if ($wpdb->get_var("SHOW TABLES LIKE '$wTable'") == $wTable) {

		//sanitize mySQL insert statement to prevent SQL injection
		if (method_exists($wpdb,'insert') && $delayed == "") { 	//WP 2.5+
			$result = $wpdb->insert($wTable,$wassup_rec);
		} elseif (method_exists($wpdb,'prepare')) {
			$insert = $wpdb->prepare("INSERT $delayed INTO $wTable (wassup_id, `timestamp`, ip, hostname, urlrequested, agent, referrer, search, searchpage, os, browser, language, screen_res, searchengine, spider, feed, username, comment_author, spam, url_wpid) VALUES ( %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
	   			$wassup_rec['wassup_id'],
		   		$wassup_rec['timestamp'],
		   		$wassup_rec['ip'],
	   			$wassup_rec['hostname'],
		   		$wassup_rec['urlrequested'], 
		   		$wassup_rec['agent'],
	   			$wassup_rec['referrer'],
		   		$wassup_rec['search'],
		   		$wassup_rec['searchpage'], 
	   			$wassup_rec['os'],
		   		$wassup_rec['browser'], 
		   		$wassup_rec['language'], 
	   			$wassup_rec['screen_res'], 
		   		$wassup_rec['searchengine'],
		   		$wassup_rec['spider'], 
	   			$wassup_rec['feed'], 
		   		$wassup_rec['username'], 
		   		$wassup_rec['comment_author'], 
	   			$wassup_rec['spam'],
	   			$wassup_rec['url_wpid']);
			$result = $wpdb->query($insert);
                } else { 
			$insert = sprintf("INSERT $delayed INTO $wTable (wassup_id, `timestamp`, ip, hostname, urlrequested, agent, referrer, search, searchpage, os, browser, language, screen_res, searchengine, spider, feed, username, comment_author, spam, url_wpid) VALUES ( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )",
	   			wSanitizeData($wassup_rec['wassup_id']),
		   		wSanitizeData($wassup_rec['timestamp']),
		   		wSanitizeData($wassup_rec['ip']),
	   			wSanitizeData($wassup_rec['hostname']),
		   		wSanitizeData($wassup_rec['urlrequested']), 
		   		wSanitizeData($wassup_rec['agent']),
	   			wSanitizeData($wassup_rec['referrer']),
		   		wSanitizeData($wassup_rec['search']),
		   		wSanitizeData($wassup_rec['searchpage']), 
	   			wSanitizeData($wassup_rec['os']),
		   		wSanitizeData($wassup_rec['browser']), 
		   		wSanitizeData($wassup_rec['language']), 
	   			wSanitizeData($wassup_rec['screen_res']), 
		   		wSanitizeData($wassup_rec['searchengine']),
		   		wSanitizeData($wassup_rec['spider']), 
	   			wSanitizeData($wassup_rec['feed']), 
		   		wSanitizeData($wassup_rec['username']), 
		   		wSanitizeData($wassup_rec['comment_author']), 
	   			wSanitizeData($wassup_rec['spam']),
	   			wSanitizeData($wassup_rec['url_wpid']));
			$result = $wpdb->query($insert);
		} 
		} //end if table exists
	} //end if is_array
} //end function insert_into_wp

//clean up data for insertion into mySQL to prevent SQL injection attacks
// Use as alternative to "wpdb::prepare" (Wordpress <2.3)
function wSanitizeData($var, $quotes=false) {
	global $wpdb;
	if (is_string($var)) {	//clean strings
		//sanitize urls with "clean_url" wordpress function
		$varstr = stripslashes($var);
		if (strstr($varstr, '://')!==false) {
			$varstr = clean_url($var,'','db');
			if (empty($varstr)) {	//oops, clean_url chomp
				$varstr = attribute_escape(stripslashes($var));
			}
		} else {
			$varstr = attribute_escape($varstr);
		}
 		if ($quotes) {
			$var = "'". $varstr ."'";
		} else {
			$var=$varstr;
		}
	} elseif (is_bool($var) && $quotes) {   //convert boolean variables to binary boolean
		$var = ($var) ? 1 : 0;
	} elseif (is_null($var) && $quotes) {   //convert null variables to SQL NULL
		$var = "NULL";
	}
	//note numeric values do not need to be sanitized
	return $var;
} //end wSanitizeData

/**
 * break up a full or partial url into key=value pairs
 * @access public
 * @return array
 */
function wGetQueryPairs($urlstring){
	$wreturn = array();
	if (!empty($urlstring)) {
		$wtab=parse_url($urlstring);
		if (key_exists("query",$wtab)){
			$query=$wtab["query"];
			$wreturn=explode("&",$query);
		} else {
			$wreturn=explode("&",$urlstring); //partial url
		}
	}
	return $wreturn;
} //end wGetQueryPairs

/**
 * Find search engine referrals from lesser-known search engines or from
 *  engines that use a url-format (versus GET) for search query
 */
function seReferer($ref = false) {
	$SeReferer = (is_string($ref) ? $ref : mb_convert_encoding(strip_tags($_SERVER['HTTP_REFERER']), "HTML-ENTITIES", "auto"));
	if ($SeReferer == "") { return false; }

	//Check against Google, Yahoo, MSN, Ask and others
	if(preg_match("#^https?://([^/]+).*[&\?](prev|q|p|s|search|searchfor|as_q|as_epq|query|keywords|term|encquery)=([^&]+)#i",$SeReferer,$pcs) > 0){
		$SeDomain = trim(strtolower($pcs[1]));
		if ($pcs[2] == "encquery") { 
			$SeQuery = " *".__("encrypted search","wassup")."* ";
		} else { 
			$SeQuery = $pcs[3];
		}

	//Check for search engines that show query as a url with 'search' and keywords in path (ex: Dogpile.com)
	} elseif (preg_match("#^https?://([^/]+).*/(results|search)/web/([^/]+)/(\d+)?#i", $SeReferer,$pcs)>0){
		$SeDomain = trim(strtolower($pcs[1]));
		$SeQuery = $pcs[3];
		if (!empty($pcs[4])) {
			$sePos = (int)$pcs[4];
		}
	//Check for search engines that show query as a url with 'search' in domain and keywords in path (ex: twitnitsearch.appspot.com)
	} elseif (preg_match("#^https?://([a-z0-9_\-\.]*(search)(?:[a-z0-9_\-\.]*\.(?:[a-z]{2,4})))/([^/]+)(?:[a-z_\-=/]+)?/(\d+)?#i",$SeReferer."/",$pcs)>0) {
		$SeDomain = trim(strtolower($pcs[1]));
		$SeQuery = $pcs[3];
		if (!empty($pcs[4])) {
			$sePos = (int)$pcs[4];
		}
	}
	unset ($pcs);

	//-- We have a query
	if(isset($SeQuery)){ 
		// Multiple URLDecode Trick to fix DogPile %XXXX Encodes
		if (strstr($SeQuery,'%')) {
			$OldQ=$SeQuery;
			$SeQuery=urldecode($SeQuery);
			while($SeQuery != $OldQ){
				$OldQ=$SeQuery;
				$SeQuery=urldecode($SeQuery);
			}
		}
		if (!isset($SePos)) { 
			if (preg_match("#[&\?](start|startpage|b|cd|first|stq|pi|page)[=/](\d+)#i",$SeReferer,$pcs)) {
				$SePos = $pcs[2];
			} else {
				$SePos = 1;
			}
			unset ($pcs);
		}
    		$searchdata=array("Se"=>$SeDomain, "Query"=>$SeQuery,
				  "Pos"=>$SePos, "Referer"=>$SeReferer);
	} else {
		$searchdata=false;
	}
	return $searchdata;
} //end function seReferrer

/**
 * Parse referrer string for match from a list of known search engines and,
 *  if found, return an array containing engine name, search keywords, 
 *  results page#, and language.
 * Note:
 *  To distinguish "images", "mobile", or other types of searches from
 *  regular text searches, the "images" and "mobile" domains must be
 *  listed above the "text" domain in search engines array.
 *
 *  New or obscure search engines, search engines with a URL-formatted
 *  referrer string, and any search engine not listed here, are identified
 *  by another function, "SeReferrer()".
 *
 * @access public
 * @param string
 * @return array
 */
function wGetSE($referrer = null){
	$key = null;
	$search_phrase="";
	$searchpage="";
	$searchengine="";
	$searchlang="";
	$selocale="";
	$blogurl = preg_replace('#(https?\://)?(www\.)?#','',strtolower(get_option('home')));
	//list of well known search engines. 
	//  Structure: "SE Name|SE Domain(partial+unique)|query_key|page_key|language_key|locale|"
	$lines = array(
		"Google Images|images.google.|prev|start|hl||",  //obsolete
		"Google Images|/imgres?imgurl=|prev|start|hl||", 
		"Google Images|.google.com/images?|q|cd|hl||",
		"Google Mobile|google.com/m/|q|cd|hl||", 
		"Google|www.google.|q|cd|hl||",
		"Google|www.google.|as_q|start|hl||",	//advanced query
		"Yahoo! Images|images.search.yahoo.com|p||||", 
		"Yahoo! Mobile|m.search.yahoo.|p||||", 
		"Yahoo! Mobile|search.yahoo.com/mobile/|p||||", 
		"Yahoo!|search.yahoo.|p||||", 
		"Yahoo!|answers.yahoo.com|p||||", 
		"Bing Mobile|m.bing.com|q|first|||", 
		"Bing Images|.bing.com/images/|q|first|||", 
		"Bing|.bing.com|q|first|||", 
		"MSN|search.msn.|q|first|||", 
		"Windows Live|search.live.com|q|first|||",
		"100Links|100links.supereva.it|q|||it|",
		"2020Search|.2020search.com|st||||",
		"abcsearch.com|abcsearch.com|terms||||",
		"ABC Sok|verden.abcsok.no|q|||no|",
		"Alice|search.alice.it|qs|||it|", 
		"Altavista|.altavista.com|q||||",
		"Altavista|.altavista.com|aqa||||",	//advanced query
		"Alexa|alexa.com|q||||","Alltheweb|alltheweb.com|q||||",
		"Aol|.aol.|query||||",
		"Aol|aolrecherches.aol.fr|query|||fr|",
		"Arianna|arianna.libero.it|query|||it|",
		"Ask|ask.com|ask||||","Ask|ask.com|q||||",
		"Atlas|search.atlas.cz|q|||cz|",
		"Beedly INT|beedly.us|q||||",
		"Bing Cache|cc.bingj.com|q|first|||", 
		"bluewin|bluewin.ch|query|||ch|", 
		"Centrum|search.centrum.cz|q|||cz|",
		"Clarence|search.clarence.com|q||||",
		"Conduit|search.conduit.com|q||||",
		"DMOZ|search.dmoz.org|search||||", 
		"Dogpile|dogpile.com|q||||",
		"earthlink.net|earthlink.net|q||||",
		"Excite|excite.|q||||",
		"Gazzetta|search.gazzetta.it|q|||it|",
		"Godago|.godago.com|keywords||||",
		"Good Search|goodsearch.com|Keywords||||", 
		"Google Blog|blogsearch.google.|as_q|start|||", 
		"Google Blog|blogsearch.google.|q|start|||",
		"Google Groups|groups.google.|q|start|||", 
		"Google Groups|groups.google.|q|start|||", 
		"Google Translate|translate.google.|prev|start|hl||",
		"Google Translate|translate.googleusercontent.com|prev|start|hl||",
		"Google Cache|http://64.233.1|q|cd|hl||", 
		"Google Cache|http://72.14.|q|cd|hl||", 
		"Google Cache|http://74.125.|q|cd|hl||", 
		"Google Cache|http://209.85.|q|cd|hl||", 
		"Google Cache|.googleusercontent.com|q|cd|hl||",
		"Google IPv6|ipv6.google.|q|cd|hl||",
		"HotBot|hotbot.|query||||",
		"ICQ Search|.icq.com|q||||",
		"Il Trovatore|.iltrovatore.it|q|||it|",
		"Incredimail|.incredimail.com|q||||",
		"ItaliaPuntoNet|italiapuntonet.net|search||||",
		"ixquick|ixquick.com|query||||", 
		"Jyxo|jyxo.1188.cz|q|||cz|",
		"Jumpy|.mediaset.it|searchWord|||it|",
		"Kataweb|kataweb.it|q|||it|", 
		"Kvasir|kvasir.no|searchExpr|||no|", 
		"Lycos|.lycos.it|query|||it|",
		"Lycos|lycos.|q||||",
		"My Search|mysearch.com|searchfor||||",
		"My Way|mysearch.myway.com|searchfor||||",
		"Metacrawler|metacrawler.|q||||", 
		"Metager|metager.de|eingabe|||de|",
		"Netscape Search|search.netscape.com|query||||",
		"Overture|overture.com|Keywords||||",
		"OpenDir|.opendir.cz|cohledas|||cz|",
		"PagineGialle|paginegialle.it|qs|||it|",
		"Picsearch|.picsearch.com|q||||",
		"Search|.search.com|q||||", 
		"Search|.search.it|srctxt|||it|",
		"Seznam|.seznam.cz|q|||cz|", 
		"Start.no|start.no|q||||", 
		"StartNow|search.startnow.|q||||",
		"Supereva|supereva.it|q|||it|",
		"Teoma|teoma.com|q||||",
		"T-Online|suche.t-online.de|q|||de|",
		"Tiscali|search-dyn.tiscali.|key||||",
		"Tiscali|.tiscali.|query||||",
		"Virgilio|.virgilio.it|qs|||it|",
		"Voil|voila.fr|kw|||fr|",
		"Web|.web.de|su|||de|", 
		"Yahoo! Mobile|m.yahoo.com|p||||",
		"Yahoo! Mobile|m2.yahoo.com|p||||",
		"Yahoo!|.yahoo.com|p||||", 
		"Yandex|yandex.ru|text|||ru|",
		"Yippy|search.yippy.com|query||||",
		"Zoohoo|.zoohoo.cz|q|||cz|", 
		"Zoznam|.zoznam.sk|s|||sk|",
		"|...|q||||",	//dummy record to prevent "SK" being appended to search domains not on this list
	);
	foreach($lines as $line_num => $se) {
		list($nome,$domain,$key,$page,$lang,$selocale)=explode("|",$se);
		//match on both domain and key..
		if (strpos($domain,'http') === false) {
			$se_regex='/^https?\:\/\/[a-z0-9\.\-]*'.preg_quote($domain,'/').'.*[&\?]'.$key.'\=([^&]+)/i';
		} else {
			$se_regex='/^'.preg_quote($domain,'/').'.*[&\?]'.$key.'\=([^&]+)/i';
		}
		$se = preg_match($se_regex,$referrer,$match);
		if (!$se && strpos($referrer,$domain)!==false && strpos(urldecode($referrer),$key.'=')!==false) {
			$se=preg_match($se_regex,urldecode($referrer),$match);
		}
		if ($se) {	// found it!
			$searchengine = $nome;
			$search_phrase = "";
			$svariables=array();
			// Google Images or Google Translate needs additional processing of search phrase after 'prev='
			if ($nome == "Google Images" || $nome == "Google Translate") {
				//'prev' is an encoded substring containing actual "q" query, so use html_entity_decode to show [&?] in url substring
				$svariables = wGetQueryPairs(html_entity_decode(preg_replace('#/\w+\?#i','', urldecode($match[1]))));
				$key='q';	//q is actual search key
			} elseif ($nome == "Google Cache") {
				$n = strpos($match[1],$blogurl);
				if ($n !== false) {
				//blogurl in search phrase: cache of own site
					$search_phrase = attribute_escape(urldecode(substr($match[1],$n+strlen($blogurl))));
					$svariables = wGetQueryPairs($referrer);
				} elseif (strpos($referrer,$blogurl)!==false && preg_match('/\&prev\=([^&]+)/',$referrer,$match)!==false) {
					//NOTE: 'prev=' requires html_entity_decode to show [&?] in url substring
					$svariables = wGetQueryPairs(html_entity_decode(preg_replace('#/\w+\?#i','', urldecode($match[1]))));
				} else {
				//no blogurl in search phrase: cache of an external site with referrer link
					$searchengine = "";
					$referrer = "";
				}
			} else {
				//distinguish google mobile from google
				if ($nome == "Google" && strstr($referrer,'/m/search?')!==false) {
					$nome = "Google Mobile";
				}
				$search_phrase = attribute_escape(urldecode($match[1]));
				$svariables = wGetQueryPairs($referrer);
			}
			//retrieve search engine parameters
			$i = count($svariables);
			while($i--){
				$tab=explode("=",$svariables[$i]);
				if($tab[0] == $key && empty($search_phrase)){
					$search_phrase=attribute_escape($tab[1]);
				} else {
					if (!empty($page) && $page == $tab[0] && is_numeric($tab[1])) {
						$searchpage = $tab[1];
					}
					if (!empty($lang) && $lang == $tab[0]) {
						$searchlang = attribute_escape($tab[1]);
					}
					//Indentify locale via Google search's new parameter, 'gl'
					if (strstr($nome,'Google')!==false && $tab[0] == "gl" && !empty($tab[1])) {
						$selocale = attribute_escape($tab[1]);
					}
				}
			} //end while
			break 1;
		} elseif (strstr($referrer,$domain)!==false) {
			$searchengine = $nome;
		} //end if preg_match
	} //end foreach
	//search engine or key is not in list, so check for search phrase instead
	if (empty($search_phrase) && !empty($referrer)) {
		//unset($nome,$domain,$key,$page,$lang);

	//Check for general search phrases
	if (preg_match("#^https?://([^/]+).*[&?](q|search|searchfor|as_q|as_epq|query|keywords?|term|text|encquery)=([^&]+)#i",$referrer,$pcs) > 0) {
		if (empty($searchengine)) {
			$searchengine = trim(strtolower($pcs[1]));
		}
		if ($pcs[2] == "encquery") { 
			$search_phrase = " *".__("encrypted search","wassup")."* ";
		} else { 
			$search_phrase = $pcs[3];
		}

	//Check separately for queries that use nonstandard search variable
	// to avoid retrieving values like "p=parameter" when "q=query" exists
	} elseif(preg_match("#^https?://([^/]+).*(?:results|search|query).*[&?](aq|as|p|su|s|kw|k|qo|qp|qs|string)=([^&]+)#i",$referrer,$pcs) > 0) {
		if (empty($searchengine)) {
			$searchengine = trim(strtolower($pcs[1]));
		}
		$search_phrase = $pcs[3];
	}
	} //end if empty(search_phrase)

	//do a separate check for page number, if not found above
	if (!empty($search_phrase)) {
		if (empty($searchpage) && preg_match("#[&\?](start|startpage|b|cd|first|stq|p|pi|page)[=/](\d+)#i",$referrer,$pcs)>0) {
			$searchpage = $pcs[2];
		}
	}
	return ($searchengine."|".$search_phrase."|".$searchpage."|".$searchlang."|".$selocale."|");
} //end wGetSE

//extract browser and platform info from a user agent string and
// return the values in an array: 0->browser 1->os. -Helene D. 6/7/08.
function wGetBrowser($agent="") {
	if (empty($agent)) { $agent = $_SERVER['HTTP_USER_AGENT']; }
	$browsercap = array();
	$browscapbrowser = "";
	$browser = "";
	$os = "";
	//check PHP browscap data for browser and platform, when available
	if (ini_get("browscap") != "" ) {
		$browsercap = get_browser($agent,true);
		if (!empty($browsercap['platform'])) {
		if (stristr($browsercap['platform'],"unknown") === false) {
			$os = $browsercap['platform'];
			if (!empty($browsercap['browser'])) {
				$browser = $browsercap['browser'];
			} elseif (!empty($browsercap['parent'])) {
				$browser = $browsercap['parent'];
			}
			if (!empty($browser) && !empty($browsercap['version'])) {
				$browser = $browser." ".wMajorVersion($browsercap['version']);
			}
		}
		}
		//reject generic browscap browsers (ex: mozilla, default)
		if (preg_match('/^(mozilla|default|unknown)/i',$browser) > 0) {
			$browscapbrowser = "$browser";	//save just in case
			$browser = "";
		}
	}
	$os = trim($os); 
	$browser = trim($browser);

	//use Detector class when browscap is missing or browser is unknown
	if ( $os == "" || $browser == "") {
		$dip = &new Detector("", $agent);
		$browser =  trim($dip->browser." ".wMajorVersion($dip->browser_version));
		if ($dip->os != "" && $dip->os != "N/A") {
			$os = trim($dip->os." ".$dip->os_version);
		}

		//use saved browscap info, if Detector had no browser result
		if (!empty($browscapbrowser) && ($browser == "" || $browser == "N/A")) {
			$browser = $browscapbrowser;
		}
	}
	return array($browser,$os);
} //end function wGetBrowser

//return a major version # from a version string argument
function wMajorVersion($versionstring) {
	$version=0;
	if (!empty($versionstring)) {
		$n = strpos($versionstring,'.');
		if ($n >0) {
			$version= (int) substr($versionstring,0,$n);
		}
		if ($n == 0 || $version == 0) {
			$p = strpos($versionstring,'.',$n+1);
			if ($p) $version= substr($versionstring,0,$p);
		}
	}
	if ($version > 0) {
		return $version;
	} else {
		return $versionstring;
	}
}

//extract spider information from a user agent string and return an array
// with values: (name, type=[R|B|F|H|L|S|V], feed subscribers)
// All spider types:  R=robot,  B=browser/downloader,  F=feed reader, 
//	H=hacker/spoofer/injection bot,  L=Link checker/sitemap generator, 
//	S=Spammer/email harvester,  V=css/html Validator
function wGetSpider($agent="",$hostname="", $browser=""){
	if (empty($agent)) { $agent = $_SERVER['HTTP_USER_AGENT']; }
	$ua = rtrim($agent);
	if (empty($ua)) {	//nothing to do...
		return false;
	} 
	$spiderdata=false;
	$crawler = "";
	$feed = "";
	$os = "";
	//## Identify obvious script injection bots 
	if (stristr($ua,'location.href')!==FALSE) {
		$crawlertype = "H";
		$crawler = "Script Injection bot";
	} elseif (preg_match('/(<|&lt;|&#60;)a(\s|%20|&#32;|\+)href/i',$ua)>0) {
		$crawlertype = "H";
		$crawler = "Script Injection bot";
	} elseif (preg_match('/(<|&lt;|&#60;)script/i',$ua)>0) {
		$crawlertype = "H";
		$crawler = "Script Injection bot";
	} elseif (preg_match('/select.*(\s|%20|\+|%#32;)from(\s|%20|\+|%#32;)wp_/i',$ua)>0) {
		$crawlertype = "H";
		$crawler = "Script Injection bot";

	//## check for crawlers that identify themselves clearly in their
	//#  user agent string with words like bot, spider, and crawler
	} elseif (!empty($ua) && preg_match("#(\w+[ \-_]?(bot|spider|crawler|reader|seeker))[0-9/ -:_.;\)]#",$ua,$matches) > 0) {
		$crawler = $matches[1];
		$crawlertype="R";

	} elseif (!empty($hostname)) {
		//## check for crawlers that mis-identify themselves as a 
		//#  browser but come from a known crawler domain - the 
		//#  most common of these are MSN (ie6,win2k3), and Yahoo!
		if (substr($hostname,-16) == ".crawl.yahoo.net" || (substr($hostname,-10)==".yahoo.com" && substr($hostname,0,3)=="ycar")) {
			if (stristr($ua,"Slurp")) {
				$crawler = "Yahoo! Slurp";
				$crawlertype="R";
			} elseif (stristr($ua,"mobile")) {
				$crawler = "Yahoo! Mobile";
				$crawlertype="R";
			} else {
				$crawler = "Yahoo!";
				$crawlertype="R";
			}
		} elseif (substr($_SERVER["REMOTE_ADDR"],0,6) == "65.55." || substr($_SERVER["REMOTE_ADDR"],0,7) == "207.46.") {
			$crawler = "MSNBot";
			$crawlertype="R";
		} elseif (substr($hostname,-8) == ".msn.com" && strpos($hostname,"msnbot")!== FALSE) {
			$crawler = "MSNBot";
			$crawlertype="R";

		//googlebot mobile can show as browser, sometimes
		} elseif (substr($hostname,-14) == ".googlebot.com") {
			if (stristr($ua,"mobile")) {
				$crawler = "Googlebot-Mobile";
				$crawlertype="R";
			} else {
				$crawler = "Googlebot";
				$crawlertype="R";
			}

		//} elseif (!empty($browser) && preg_match("#([a-z0-9_]*(bot|crawl|reader|seeker|spider\.|feed|indexer|parser))]#",$ua,$matches) > 0) {
		//## TODO: check for crawlers that claim to be browsers but
		//#  their hostname says otherwise
		}
	}

	if (empty($crawler) && ini_get("browscap") != "" ) {
		//## check browscap data for crawler info., when available
		$browsercap = get_browser($ua,true);
		//if no platform(os), assume crawler...
		if (!empty($browsercap['platform'])) {
			if ( $browsercap['platform'] != "unknown") {
				$os = $browsercap['platform'];
			}
		}
		if (!empty($browsercap['crawler']) || !empty($browsercap['stripper']) || $os == "") {
			if (!empty($browsercap['browser'])) {
				$crawler = $browsercap['browser'];
			} elseif (!empty($browsercap['parent'])) {
				$crawler = $browsercap['parent'];
			}
			if (!empty($crawler) && !empty($browsercap['version'])) {
				$crawler = $crawler." ".$browsercap['version'];
			}
		}
		//reject unknown browscap crawlers (ex: default)
		if (preg_match('/^(default|unknown|robot)/i',$crawler) > 0) {
			$crawler = "";
		}
	}

	//get crawler info. from a known list of bots and feedreaders that
	// don't list their names first in UA string.
	//Note: spaces are removed from UA string for the bot comparison
	$crawler = trim($crawler);
	if (empty($crawler)) {
		$uagent=str_replace(" ","",$ua);
		$key = null;
		// array format: "Spider Name|UserAgent keywords (no spaces)| Spider type (R=robot, B=Browser/downloader, F=feedreader, H=hacker, L=Link checker, M=siteMap generator, S=Spammer/email harvester, V=CSS/Html validator)
		$lines = array("Googlebot|Googlebot/|R|", 
			"Yahoo!|Yahoo! Slurp|R|",
			"FeedBurner|FeedBurner|F|",
			"AboutUsBot|AboutUsBot/|R|", 
			"80bot|80legs.com|R|", 
			"Aggrevator|Aggrevator/|F|", 
			"AlestiFeedBot|AlestiFeedBot||", 
			"Alexa|ia_archiver|R|", "AltaVista|Scooter-|R|", 
			"AltaVista|Scooter/|R|", "AltaVista|Scooter_|R|", 
			"AMZNKAssocBot|AMZNKAssocBot/|R|",
			"AppleSyndication|AppleSyndication/|F|",
			"Apple-PubSub|Apple-PubSub/|F|",
			"Ask.com/Teoma|AskJeeves/Teoma)|R|",
			"Ask Jeeves/Teoma|ask.com|R|",
			"AskJeeves|AskJeeves|R|", 
			"Baiduspider|www.baidu.com/search/spider|R|",
			"BlogBot|BlogBot/|F|", "Bloglines|Bloglines/|F|",
			"Blogslive|Blogslive|F|",
			"BlogsNowBot|BlogsNowBot|F|",
			"BlogPulseLive|BlogPulseLive|F|",
			"IceRocket BlogSearch|icerocket.com|F|",
			"Charlotte|Charlotte/|R|", 
			"Xyleme|cosmos/0.|R|", "cURL|curl/|R|",
			"Daumoa|Daumoa-feedfetcher|F|",
			"Daumoa|DAUMOA|R|",
			"Daumoa|.daum.net|R|",
			"Die|die-kraehe.de|R|", 
			"Diggit!|Digger/|R|", 
			"disco/Nutch|disco/Nutch|R|",
			"DotBot|DotBot/|R|",
			"Emacs-w3|Emacs-w3/v||", 
			"ananzi|EMC||", 
			"EnaBot|EnaBot||", 
			"esculapio|esculapio/||", "Esther|esther||", 
			"everyfeed-spider|everyfeed-spider|F|", 
			"Evliya|Evliya||", "nzexplorer|explorersearch||", 
			"eZ publish Validator|eZpublishLinkValidator||",
			"FacebookExternalHit|facebook.com/externalhit|R|",
			"FastCrawler|FastCrawler|R|", 
			"FDSE|FDSErobot|R|", 
			"Feed::Find|Feed::Find||",
			"FeedDemon|FeedDemon/|F|",
			"FeedHub FeedFetcher|FeedHub|F|", 
			"Feedreader|Feedreader|F|", 
			"Feedshow|Feedshow|F|", 
			"Feedster|Feedster|F|",
			"FeedTools|feedtools|F|",
			"Feedfetcher-Google|Feedfetcher-google|F|", 
			"Felix|FelixIDE/||", 
			"FetchRover|ESIRover||", 
			"fido|fido/||", 
			"Fish|Fish-Search-Robot||", "Fouineur|Fouineur||", 
			"Freecrawl|Freecrawl|R|", 
			"FriendFeedBot|FriendFeedBot/|F|",
			"FunnelWeb|FunnelWeb-||", 
			"gammaSpider|gammaSpider||", "gazz|gazz/||", 
			"GCreep|gcreep/||", 
			"GetRight|GetRight|R|", 
			"GetURL|GetURL.re||", "Golem|Golem/||", 
			"Google Images|Googlebot-Image|R|",
			"Google AdSense|Mediapartners-Google|R|", 
			"Google Desktop|GoogleDesktop|F|", 
			"Google Web Preview|GoogleWebPreview|R|",
			"GreatNews|GreatNews|F|", 
			"Gregarius|Gregarius/|F|",
			"Gromit|Gromit/||", 
			"gsinfobot|gsinfobot||", 
			"Gulliver|Gulliver/||", "Gulper|Gulper||", 
			"GurujiBot|GurujiBot||", 
			"havIndex|havIndex/||",
			"heritrix|heritrix/||", "HI|AITCSRobot/||",
			"HKU|HKU||", "Hometown|Hometown||", 
			"HostTracker|host-tracker.com/|R|",
			"ht://Dig|htdig/|R|", "HTMLgobble|HTMLgobble||", 
			"Hyper-Decontextualizer|Hyper||", 
			"iajaBot|iajaBot/||", 
			"IBM_Planetwide|IBM_Planetwide,||", 
			"ichiro|ichiro||", 
			"Popular|gestaltIconoclast/||", 
			"Ingrid|INGRID/||", "Imagelock|Imagelock||", 
			"IncyWincy|IncyWincy/||", 
			"Informant|Informant||", 
			"InfoSeek|InfoSeek||", 
			"InfoSpiders|InfoSpiders/||", 
			"Inspector|inspectorwww/||", 
			"IntelliAgent|IAGENT/||", 
			"ISC Systems iRc Search|ISCSystemsiRcSearch||", 
			"Israeli-search|IsraeliSearch/||", 
			"IRLIRLbot/|IRLIRLbot||",
			"Italian Blog Rankings|blogbabel|F|", 
			"Jakarta|Jakarta||", "Java|Java/||", 
			"JBot|JBot||", 
			"JCrawler|JCrawler/||", 
			"JoBo|JoBo||", "Jobot|Jobot/||", 
			"JoeBot|JoeBot/||",
			"JumpStation|jumpstation||", 
			"image.kapsi.net|image.kapsi.net/|R|", 
			"kalooga/kalooga|kalooga/kalooga||", 
			"Katipo|Katipo/||", 
			"KDD-Explorer|KDD-Explorer/||", 
			"KIT-Fireball|KIT-Fireball/||", 
			"KindOpener|KindOpener||", "kinjabot|kinjabot||", 
			"KO_Yappo_Robot|yappo.com/info/robot.html||", 
			"Krugle|Krugle||", 
			"LabelGrabber|LabelGrab/||",
			"Larbin|larbin_||",
			"libwww-perl|libwww-perl||", 
			"lilina|Lilina||", 
			"Link|Linkidator/||", "LinkWalker|LinkWalker|L|", 
			"LiteFinder|LiteFinder||", 
			"logo.gif|logo.gif||", "LookSmart|grub-client||",
			"Lsearch/sondeur|Lsearch/sondeur||", 
			"Lycos|Lycos/||", 
			"Magpie|Magpie/||", "MagpieRSS|MagpieRSS|F|", 
			"Mail.ru|Mail.ru||", 
			"marvin/infoseek|marvin/infoseek||", 
			"Mattie|M/3.||", "MediaFox|MediaFox/||", 
			"Megite2.0|Megite.com||", 
			"NEC-MeshExplorer|NEC-MeshExplorer||", 
			"MindCrawler|MindCrawler||", 
			"Missigua Locator|Missigua Locator||", 
			"MJ12bot|MJ12bot|R|", "mnoGoSearch|UdmSearch||", 
			"MOMspider|MOMspider/||", "Monster|Monster/v||", 
			"Moreover|Moreoverbot||", "Motor|Motor/||", 
			"MSNBot|MSNBOT/|R|", "MSN|msnbot.|R|",
			"MSRBOT|MSRBOT|R|", "Muninn|Muninn/||", 
			"Muscat|MuscatFerret/||", 
			"Mwd.Search|MwdSearch/||", 
			"MyBlogLog|Yahoo!MyBlogLogAPIClient|F|",
			"Naver|NaverBot||",
			"Naver|Cowbot||",
			"NDSpider|NDSpider/||", 
			"Nederland.zoek|Nederland.zoek||", 
			"NetCarta|NetCarta||", 
			"NetMechanic|NetMechanic||", 
			"NetScoop|NetScoop/||", 
			"NetNewsWire|NetNewsWire||", 
			"NewsAlloy|NewsAlloy||",
			"newscan-online|newscan-online/||", 
			"NewsGatorOnline|NewsGatorOnline||", 
			"Exalead NG|NG/|R|", 
			"NHSE|NHSEWalker/||", "Nomad|Nomad-V||", 
			"Nutch/Nutch|Nutch/Nutch||", 
			"ObjectsSearch|ObjectsSearch/||", 
			"Occam|Occam/||", 
			"Openfind|Openfind||", 
			"OpiDig|OpiDig||", 
			"Orb|Orbsearch/||", 
			"OSSE Scanner|OSSEScanner||", 
			"OWPBot|OWPBot||", 
			"Pack|PackRat/||", "ParaSite|ParaSite/||", 
			"Patric|Patric/||", 
			"PECL::HTTP|PECL::HTTP||", 
			"PerlCrawler|PerlCrawler/||", 
			"Phantom|Duppies||", "PhpDig|phpdig/||", 
			"PiltdownMan|PiltdownMan/||", 
			"Pimptrain.com's|Pimptrain||", 
			"Pioneer|Pioneer||", 
			"Portal|PortalJuice.com/||", "PGP|PGP-KA/||", 
			"PlumtreeWebAccessor|PlumtreeWebAccessor/||", 
			"Poppi|Poppi/||", "PortalB|PortalBSpider/||", 
			"psbot|psbot/|R|", 
			"R6_CommentReade|R6_CommentReade||", 
			"R6_FeedFetcher|R6_FeedFetcher|F|", 
			"radianrss|RadianRSS||", 
			"Raven|Raven-v||", 
			"relevantNOISE|relevantnoise.com||",
			"Resume|Resume||", "RoadHouse|RHCS/||", 
			"RixBot|RixBot||",
			"Robbie|Robbie/||", "RoboCrawl|RoboCrawl||", 
			"RoboFox|Robofox||",
			"Robozilla|Robozilla/||", 
			"Rojo|rojo1|F|", 
			"Roverbot|Roverbot||", 
			"RssBandit|RssBandit||", 
			"RSSMicro|RSSMicro.com|F|",
			"Ruby|Rfeedfinder||", 
			"RuLeS|RuLeS/||", 
			"Runnk RSS aggregator|Runnk||", 
			"SafetyNet|SafetyNet||", 
			"Sage|(Sage)|F|",
			"SBIder|sitesell.com|R|", 
			"Scooter|Scooter/||", 
			"ScoutJet|ScoutJet||",
			"SearchProcess|searchprocess/||", 
			"Seekbot|seekbot.net|R|", 
			"SimplePie|SimplePie/|F|", 
			"Sitemap Generator|SitemapGenerator||", 
			"Senrigan|Senrigan/||", 
			"SeznamBot|SeznamBot/|R|",
			"SeznamScreenshotator|SeznamScreenshotator/|R|",
			"SG-Scout|SG-Scout||", "Shai'Hulud|Shai'Hulud||", 
			"Simmany|SimBot/||", 
			"SiteTech-Rover|SiteTech-Rover||", 
			"shelob|shelob||", 
			"Sleek|Sleek||", 
			"Slurp|.inktomi.com/slurp.html|R|",
			"Snapbot|.snap.com|R|", 
			"SnapPreviewBot|SnapPreviewBot|R|",
			"Smart|ESISmartSpider/||", 
			"Snooper|Snooper/b97_01||", "Solbot|Solbot/||", 
			"Sphere Scout|SphereScout|R|",
			"Sphere|sphere.com|R|",
			"spider_monkey|mouse.house/||",
			"SpiderBot|SpiderBot/||", 
			"Spiderline|spiderline/||",
			"SpiderView(tm)|SpiderView||", 
			"SragentRssCrawler|SragentRssCrawler|F|",
			"Site|ssearcher100||",
			"StackRambler|StackRambler||", 
			"Strategic Board Bot|StrategicBoardBot||", 
			"Suke|suke/||", 
			"SummizeFeedReader|SummizeFeedReader|F|", 
			"suntek|suntek/||", 
			"SurveyBot|SurveyBot||", 
			"Sygol|.sygol.com||", 
			"Syndic8|Syndic8|F|", 
			"TACH|TACH||", "Tarantula|Tarantula/||",
			"tarspider|tarspider||", "Tcl|dlw3robot/||", 
			"TechBOT|TechBOT||", "Technorati|Technoratibot||",
			"Teemer|Teemer||", "Templeton|Templeton/||",
			"TitIn|TitIn/||", "TITAN|TITAN/||", 
			"Twiceler|.cuil.com/twiceler/|R|",
			"Twiceler|.cuill.com/twiceler/|R|",
			"Twingly|twingly.com|R|",
			"UCSD|UCSD-Crawler||", "UdmSearch|UdmSearch/||",
			"UniversalFeedParser|UniversalFeedParser|F|", 
			"UptimeBot|uptimebot||", 
			"URL_Spider|URL_Spider_Pro/|R|", 
			"VadixBot|VadixBot||", "Valkyrie|Valkyrie/||", 
			"Verticrawl|Verticrawlbot||", 
			"Victoria|Victoria/||", 
			"vision-search|vision-search/||", 
			"void-bot|void-bot/||", "Voila|VoilaBot||",
			"Voyager|.kosmix.com/html/crawler|R|",
			"VWbot|VWbot_K/||", 
			"W3C_Validator|W3C_Validator/|V|",
			"w3m|w3m/|B|", "W3M2|W3M2/||", "w3mir|w3mir/||", 
			"w@pSpider|w@pSpider/||", 
			"WallPaper|CrawlPaper/||",
			"WebCatcher|WebCatcher/||", 
			"webCollage|webcollage/|R|", 
			"webCollage|collage.cgi/|R|", 
			"WebCopier|WebCopierv|R|",
			"WebFetch|WebFetch|R|", "WebFetch|webfetch/|R|", 
			"WebMirror|webmirror/||", 
			"webLyzard|webLyzard||", "Weblog|wlm-||", 
			"WebReaper|webreaper.net|R|", 
			"WebVac|webvac/||", "webwalk|webwalk||", 
			"WebWalker|WebWalker/||", 
			"WebWatch|WebWatch||", 
			"WebStolperer|WOLP/||", 
			"WebThumb|WebThumb/|R|", 
			"Wells Search II|WellsSearchII||", 
			"Wget|Wget/||",
			"whatUseek|whatUseek_winona/||", 
			"whiteiexpres/Nutch|whiteiexpres/Nutch||",
			"wikioblogs|wikioblogs||", 
			"WikioFeedBot|WikioFeedBot||", 
			"WikioPxyFeedBo|WikioPxyFeedBo||",
			"Wild|Hazel's||", 
			"Wired|wired-digital-newsbot/||", 
			"Wordpress Pingback/Trackback|Wordpress||", 
			"WWWC|WWWC/||", 
			"XGET|XGET/||", 
			"yacybot|yacybot||",
			"Yahoo FeedSeeker|YahooFeedSeeker|F|",
			"Yahoo MMAudVid|Yahoo-MMAudVid/|R|",
			"Yahoo MMCrawler|Yahoo-MMCrawler/|R|",
			"Yahoo!SearchMonkey|Yahoo!SearchMonkey|R|",
			"YahooSeeker|YahooSeeker/|R|",
			"YoudaoBot|YoudaoBot|R|", 
			"Tailrank|spinn3r.com/robot|R|",
			"Tailrank|tailrank.com/robot|R|",
			"Yandex|Yandex|R|",
			"Yesup|yesup||",
			"Internet|User-Agent:||",
			"Robot|Robot||", "Spider|spider||");
		foreach($lines as $line_num => $spider) {
			list($nome,$key,$crawlertype)=explode("|",$spider);
			if ($key != "") {
				if(strstr($uagent,$key)===FALSE) { 
					continue; 
				} else { 
					$crawler = trim($nome);
					if (!empty($crawlertype) && $crawlertype == "F") {
						$feed = $crawler;
					}
					break 1;
				}
			}
		}
	} // end if crawler

	//If crawler not on list, use first word in useragent for crawler name
	if (empty($crawler)) { 
		//Assume first word in useragent is crawler name
		if (preg_match("/^(\w+)[\/ \-\:_\.;]/",$ua,$matches) > 0) {
			if (strlen($matches[1])>1 && $matches[1]!="Mozilla") { 
				$crawler = $matches[1];
			}
		}
		/* //Use browser name for crawler as last resort
		if (empty($crawler) && !empty($browser)) { 
			$crawler = $browser;
		} */
	}
	//#do a feed check and get feed subcribers, if available
	if (preg_match("/([0-9]{1,10})\s?subscriber/i",$ua,$subscriber) > 0) {
		// It's a feedreader with some subscribers
		$feed = $subscriber[1];
		if (empty($crawler) && empty($browser)) {
			$crawler = "Feed Reader";
			$crawlertype="F";
		}
	} elseif (empty($feed) && (is_feed() || preg_match("/(feed|rss)/i",$ua)>0)) {
		if (!empty($crawler)) { 
			$feed = $crawler;
		} elseif (empty($browser)) {
			$crawler = "Feed Reader";
			$feed = "feed reader";
		}
		$crawlertype="F";
	} //end else preg_match subscriber

	//check for spoofers of Google/Yahoo crawlers...
	if ($hostname!="") {
		if (preg_match('/^(googlebot|yahoo\!\ slurp)/i',$crawler)>0 && preg_match('/\.(googlebot|yahoo)\./i',$hostname)==0){
			$crawler = "Spoofer bot";
			$crawlertype = "H";
		}
	} //end if hostname
	$spiderdata=array($crawler,$crawlertype,trim($feed));

	return $spiderdata;
} //end function wGetSpider

//#get the visitor locale/language
function wGetLocale($language="",$hostname="",$referrer="") {
	global $wpdb, $wassup_options, $wdebug_mode;
	$clocale="";
	$country="";
	$language = trim(strtolower($language));
	//change language code to 2-digits
	if (strlen($language) >2) {
	   	$langarray = @explode("-", $language);
	   	$langarray = @explode(",", $langarray[1]);
		list($language) = @explode(";", $langarray[0]);
	}
	//#1st check for a cached geoip record's country code TODO
	/*
	if (!empty($wassup_options->wassup_cache)) {
		$wassup_cache = $wassup_options->wassup_table."_meta";
		//retrieve record from table...
		$geoip = $wpdb->query('SELECT `meta_value` from $wassup_cache WHERE...
		//assign locale from country code...
		$clocale=$geoip['country_code'];
		//change UK to GB for consistency in Wassup
		if ($language == "uk") $language = "gb";
	}
	 */
	if ($clocale == "") {
	  //#use 2-digit top-level domains (country) for language, if any
	  if (!empty($hostname) && preg_match("/\.[a-z]{2}$/i", $hostname) > 0) {
		$country = strtolower(substr($hostname,-2));
		//ignore domains commonly used for media
		if ($country == "tv" || $country == "fm") $country="";
		if ($country != "") {
			if (empty($language)) $language=$country;
			elseif ($language=="us" || $language=="en") {
				$language = $country;
			}
		}
	  }

	  //major USA-only ISP hosts who always have "us" as language code
	  if (!empty($hostname) && preg_match('/(\.[a-z]{2}\.comcast\.net|\.verizon\.net|\.windstream\.net)$/',$hostname,$matches)>0) {
		$language = "us";

	  //#use country code in referrer search string for language
	  } elseif (!empty($referrer) && ($language=="" || $language=="us" || $language=="en")) {
		//google referrer syntax: google.com[.country],hl=language
		if (preg_match('/\.google\.(?:com?\.([a-z]{2})|([a-z]{2})|com)\/[a-z]+.*(?:[&\?]hl\=([a-z]{2})\-?(\w{2})?)/i',$referrer,$matches)>0) {
			if (!empty($matches[1])) {
				$country = strtolower($matches[1]);
			} elseif (!empty($matches[2])) {
				$country = strtolower($matches[2]);
			} elseif (!empty($matches[4])) {
				$country = strtolower($matches[4]);
			} elseif (!empty($matches[3])) {
				$country = strtolower($matches[3]);
			}
			if (!empty($country)) $language = $country;
		}
		unset ($matches);
	  }

	  //Make language code consistent with country code
	  if ($language == "en") {	//default to "US" if "en"
		$language = "us";
	  } elseif ($language == "uk") {	//change UK to GB
		$language = "gb";
	  } elseif ($language == "ja") {	//change JA to JP
		$language = "jp";
	  } elseif ($language == "ko") {	//change KO to KR
		$language = "kr";
	  } elseif ($language == "da") {	//change DA to DK
		$language = "dk";
	  } elseif ($language == "ur") {	//Urdu 
		$language = "in";	//could be India or Pakistan
	  } elseif ($language == "he" || $language == "iw") {	//Hebrew (iso) 
		$language = "il";	//Israel
	  } 

	  if (!empty($language) && preg_match("/^[a-z]{2}$/",$language)>0){
		$clocale = $language;
	  }
	} //end if $clocale==""
	return $clocale;
} //end function wGetLocale

/**
 * Check referrer host (or visitor hostname) against a list of known 
 * referrer spammers and return "true" if match or if faked
 * @param string (2)
 * @return boolean
 */
function wGetSpamRef($referrer,$hostname="") {
	global $wdebug_mode;
	$referrer=attribute_escape(strip_tags(str_replace(" ","",html_entity_decode($referrer))));
	$badhost=false;
        //$key = null;
	$referrer_host = "";
	$referrer_path = "";

	if (empty($referrer) && !empty($hostname)) {
		$referrer_host = $hostname;
		$hostname="";
	} elseif (!empty($referrer)) {
		$rurl = parse_url(strtolower($referrer));
		if (isset($rurl['host'])) {
			$referrer_host = $rurl['host'];
			//$referrer_path = $rurl['path'];
			$thissite = parse_url(get_option('home'));
			//exclude current site as referrer
			if (isset($thissite['host']) && $referrer_host == $thissite['host']) {
				$referrer_host = "";
			//New in v1.8.3: check the path|query part of url for spammers
			} else {
				//rss.xml|sitemap.txt in referrer is faked
				if (preg_match('#.+/(rss\.xml|sitemap\.txt)$#',$referrer)>0) {
					$badhost=true;
				//membership|user id in referrer is faked
				} elseif (preg_match('#.+[^a-z0-9]((?:show)?user|u)\=\d+$#',$referrer)>0) {
					$badhost=true;
				//youtube video in referrer is faked
				} elseif (preg_match('#(\.|/)youtube\.com/watch\?v\=.+#"',$referrer)>0) {
					$badhost=true;
				//some facebook links in referrer are faked
				} elseif (preg_match('#(\.|/)facebook\.com\/ASeaOfSins$#"',$referrer)>0) {
					$badhost=true;
				}
			}
		} else {	//faked referrer string
			$badhost=true;
		}
		//#a shortened URL is likely FAKED referrer string!
		if (!$badhost && !empty($referrer_host)) {
			$url_shorteners = array('bit.ly', 'cli.gs', 
				'goo.gl', 'is.gd', 'ow.ly',
				'shorturl.com', 'snurl.com', 'su.pr',
				'tinyurl.com','tr.im');
			if(in_array($referrer_host,$url_shorteners)) {
				$badhost=true;
			}
		}
	} //end elseif
	if (empty($referrer_host) || $badhost) return $badhost;
	
	if ($wdebug_mode) echo "\$referrer_host = $referrer_host.\n";
	//compare against a list of recent referer spammers
	$lines = array(	'123666123\.com',
			'209\.29\.25\.180',
			'[a-z]+19[0-9]{2}\.co\.cc',
			'aerhaethjsry\.com',
			'aimtrust\.com',
			'american\-insurance\-companies\.com',
			'amipregnantquizzes\.com',
			'all\-lasik\-centers\.com',
			'allmymovies\.biz',
			'articlemarketingrobots\.org',
			'baby\-kleidung\.runashop\.com',
			'bayanbag\.tk',
			'beachvitality\.com',
			'bloggingtomakemoney\.net',
			'blueberryvitamin\.com',
			'bumphero\.com',
			'canadapharm\.atwebpages\.com',
			'candy\.com',
			'carartexpert\.com',
			'celebritydietdoctor\.com',
			'celebrity\-?diets\.(com|org|net|info|biz)',
			'[a-z0-9]+\.cheapchocolatesale\.com',
			'chocolate\.com',
			'clients\.your\-server\.de',
			'couplesresortsonline\.com',
			'creditcardsinformation\.info',
			'.*\.css\-build\.info',
			'h\-1.+\.cssgroup\.lv',
			'.*dietplan\.com',
			'dogcareinsurancetips\.sosblog\.com',
			'dollhouserugs\.com',
			'dreamworksdentalcenter\.com',
			'duunot\.eu',
			'\.ewebesales\.net',
			'exactinsurance\.info',
			'find1friend\.com',
			'freefarmvillesecrets\.info',
			'frenchforbeginnerssite\.com',
			'gameskillinggames\.net',
			'gardenactivities\.webnode\.com',
			'globalringtones\.net',
			'gossipchips\.com',
			'gskstudio\.com',
			'hearcam\.org',
			'hearthealth\-hpe\.org',
			'highheelsale\.com',
			'homebasedaffiliatemarketingbusiness\.com',
			'hosting37\d{2}\.com/',
			'howgrowtall\.(com|info)',
			'insurancebinder\.info',
			'internetserviceteam\.com',
			'intl\-alliance\.com',
			'it\.n\-able\.com',
			'justanimal\.com',
			'justbazaar\.com',
			'knowledgehubdata\.com',
			'koreanracinggirls\.com',
			'lacomunidad\.elpais\.com',
			'lactoseintolerancesymptoms\.net',
			'liquiddiet[a-z\-]*\.com',
			'locksmith[a-z\-]+\.org',
			'lockyourpicz\.com',
			'luia\.ru',
			'mydirtyhobbycom\.de',
			'myhealthcare\.com',
			'nextcars\.net',
			'odcadide\.iinaa\.net',
			'oma\-chat\-live\.de',
			'onlinemarketpromo\.com',
			'pacificstore\.com',
			'pc\-games\-10\.de',
			'peter\-sun\-scams\.com',
			'pharmondo\.com',
			'pinky\-vs\-cherokee\.com',
			'pinkyxxx\.org',
			'[a-z]+\.pixnet\.net',
			'play\-mp3\.com',
			'poker\-review\.tk',
			'pornobesto\.ru',
			'21[89]\-124\-182\-64\.cust\.propagation\.net',
			'prosperent\-adsense\-alternative\.blogspot\.com',
			'ragedownloads\.info',
			'red\-black\.ru',
			'[a-z\-]*ringtone\.net',
			'rufights\.com',
			'seoindiawizard\.com',
			'sexcam\-girls\.at',
			'singlesvacationspackages\.com',
			'sitetalk\-revolution\.com',
			'smartforexsignal\.com',
			'socratestheme\.me',
			'stableincomeplan\.blogspot\.com',
			'staphinfectionpictures\.org',
			'static\.theplanet\.com',
			'[a-z]+\-[a-z]+\-symptoms\.com',
			'thebestweddingparty\.com',
			'thik\-chik\.com',
			'thisweekendsmovies\.com',
			'unassigned\.psychz\.net',
			'ultrabait\.biz',
			'[a-z\-\.]+vigra\-buy\.info',
			'vitamin\-d\-deficiency\-symptoms\.com',
			'vpn\-privacy\.org',
			'watchstock\.com',
			'web\-promotion\-services\.net',
			'wh\-tech\.com',
			'wholesalelobster\.ca',
			'wholesalelivelobster\.com',
			'wineaccessories\-winegifts\.com',
			'writeagoodcoverletter\.com',
			'writeagoodresume\.net',
			'yeastinfectionsymptomstreatments\.com'
			);
	foreach($lines as $spammer) {
		if (!empty($spammer)) {
		if(preg_match("#^{$spammer}\$#",$referrer_host)>0) {
			// found it!
			$badhost=true;
			break 1;
		} elseif(!empty($hostname) && preg_match('#(^|\.){$spammer}\$#i',$hostname)>0) {
			$badhost=true;
			break 1;
		}
		}
	}
	//#Assume any referrer name similar to "viagra/zanax/.."
	//#  is spam and mark as such...
	if (!$badhost) {
		$lines = array(	"allegra", "ambien", "ativan", "blackjack",
			"bukakke", "casino","cialis","ciallis", "celebrex",
			"cumdripping", "cumeating", "cumfilled",
			"cumpussy", "cumsucking", "cumswapping",
			"diazepam", "diflucan", "drippingcum", "eatingcum",
			"enhancement", "finasteride", "fioricet",
			"gabapentin", "gangbang", "highprofitclub",
			"hydrocodone", "krankenversicherung", "lamisil",
			"latinonakedgirl", "levitra", "libido", "lipitor",
			"lortab", "melatonin", "meridia", "NetCaptor",
			"orgy-", "phentemine", "phentermine", "propecia",
			"proscar", "pussycum", "sildenafil", "snowballing",
			"suckingcum", "swappingcum", "swingers",
			"tadalafil", "tigerspice", "tramadol", "ultram-",
			"valium", "valtrex", "viagra", "viagara","vicodin",
			"xanax", "xenical", "xxx-",
			"zoloft", "zovirax", "zanax"
			);
		foreach ($lines as $badreferrer) {
			if (strstr($referrer_host, $badreferrer) !== FALSE) { 
				$badhost=true;
				break 1;
			}
		}
	}
	//#check for a customized spammer list...
	if (!$badhost) {
		$badhostfile= WASSUPDIR.'/badhosts.txt';
		if (preg_match('/\.[a-z]{2}$/',$referrer_host)>0) {
			$badhostfile= WASSUPDIR.'/badhosts-intl.txt';
		}
		if (file_exists($badhostfile)) {
			$lines = file($badhostfile,FILE_IGNORE_NEW_LINES);
		foreach($lines as $spammer) { 
			if (!empty($spammer)) {
			if (preg_match("#(^|\.){$spammer}\$#",$referrer_host)>0) {
                          	// found it!
			  	$badhost=true;
			  	break 1;
			} elseif(!empty($hostname) && preg_match("#(^|\.){$spammer}\$#i",$hostname)>0) {
				$badhost=true;
				break 1;
			}
			}
		}
		}
	}
	return $badhost;
} //end function wGetSpamRef()

//return 1st valid IP address in a comma-separated list of IP addresses
//  -Helene D. 2009-03-01
function wValidIP($multiIP) {
	//in case of multiple forwarding
	$ips = explode(",",$multiIP);
	$goodIP = "";
	//look through forwarded list for a good IP
	foreach ($ips as $IP) {
		//exclude dummy IPv4 addresses...
		$ipaddr = trim($IP);
		if (!empty($ipaddr) && $ipaddr != "unknown" && $ipaddr != "0.0.0.0" && $ipaddr != "127.0.0.1" && substr($ipaddr,0,8) != "192.168." && substr($ipaddr,0,3) != "10." && substr($ipaddr,0,4) != "172.") {
			$goodIP = $ipaddr;
			break 1;
		}
	}
	if (!empty($goodIP)) { return $goodIP; }
	else { return false; }
} //end function wValidIP

function export_wassup() {
	global $wpdb, $wassup_options;

	if (empty($wassup_options->wassup_table)) {
		$wassup_table = $wpdb->prefix . "wassup";
	} else {
		$wassup_table = $wassup_options->wassup_table;
	}
	$filename = 'wassup.' . gmdate('Y-m-d') . '.sql';

	//# check for records before exporting...
	$numrecords = $wpdb->get_var("SELECT COUNT(wassup_id) FROM $wassup_table");
	if ( $numrecords > 0 ) {
		if ($numrecords > 10000) {
		//...could take a long time, so run in background in case browser times out
			ignore_user_abort(1);
		}
		$exportdata=backup_table("$wassup_table");

	if ($exportdata) {
	//TODO: use compressed file transfer when zlib available...
	do_action('export_wassup');
	header('Content-Description: File Transfer');
	header("Content-Disposition: attachment; filename=$filename");
	header('Content-Type: text/plain charset=' . get_option('blog_charset'), true);

	// Function is below
	//backup_table($wassup_table);
	echo $exportdata;

	die(); 	//sends output and flushes buffer
	}
} //end if numrecords > 0
} //end function export_wassup()

/**
* Taken partially from wp-db-backup plugin
* Alain Wolf, Zurich - Switzerland
* Website: http://www.ilfilosofo.com/blog/wp-db-backup/
* @param string $table
* @param string $segment
* @return void
*/
function backup_table($table, $segment = 'none') {
	global $wpdb, $wassup_options;
	define('ROWS_PER_SEGMENT', 100);

	$table_structure = $wpdb->get_results("DESCRIBE $table");
	if (! $table_structure) {
		$wassup_options->wassup_alert_message = __('Error getting table details','wassup') . ": $table";
		$wassup_options->saveSettings();
		return FALSE;
	}

	if(($segment == 'none') || ($segment == 0)) {
		// Add SQL statement to drop existing table
		$sql .= "\n\n";
		$sql .= "#\n";
		$sql .= "# " . sprintf(__('Delete any existing table %s','wassup'),$table) . "\n";
		$sql .= "#\n";
		$sql .= "\n";
		$sql .= "#\n";
		$sql .= "# Uncomment if you need\n";
		$sql .= "#DROP TABLE IF EXISTS " . $table . ";\n";
		
		// Table structure
		// Comment in SQL-file
		$sql .= "\n\n";
		$sql .= "#\n";
		$sql .= "# " . sprintf(__('Table structure of table %s','wassup'),$table) . "\n";
		$sql .= "#\n";
		$sql .= "\n";
		$sql .= "#\n";
		$sql .= "# Uncomment if you need\n";
		
		$create_table = $wpdb->get_results("SHOW CREATE TABLE $table", ARRAY_N);
		if (FALSE === $create_table) {
			$err_msg = sprintf(__('Error with SHOW CREATE TABLE for %s.','wassup'), $table);
			$wassup_options->wassup_alert_message = $err_msg;
			$wassup_options->saveSettings();
			$sql .= "#\n# $err_msg\n#\n";
		}
		$sql .= $create_table[0][1] . ' ;';
		
		if (FALSE === $table_structure) {
			$err_msg = sprintf(__('Error getting table structure of %s','wassup'), $table);
			$wassup_options->wassup_alert_message = $err_msg;
			$wassup_options->saveSettings();
			$sql .= "#\n# $err_msg\n#\n";
		}
	
		// Comment in SQL-file
		$sql .= "\n\n";
		$sql .= "#\n";
		$sql .= '# ' . sprintf(__('Data contents of table %s','wassup'),$table) . "\n";
		$sql .= "#\n";
	}
	
	if(($segment == 'none') || ($segment >= 0)) {
		$defs = array();
		$ints = array();
		foreach ($table_structure as $struct) {
			if ( (0 === strpos($struct->Type, 'tinyint')) ||
				(0 === strpos(strtolower($struct->Type), 'smallint')) ||
				(0 === strpos(strtolower($struct->Type), 'mediumint')) ||
				(0 === strpos(strtolower($struct->Type), 'int')) ||
				(0 === strpos(strtolower($struct->Type), 'bigint')) ||
				(0 === strpos(strtolower($struct->Type), 'timestamp')) ) {
					$defs[strtolower($struct->Field)] = $struct->Default;
					$ints[strtolower($struct->Field)] = "1";
			}
		}
		
		// Batch by $row_inc
		
		if($segment == 'none') {
			$row_start = 0;
			$row_inc = ROWS_PER_SEGMENT;
		} else {
			$row_start = $segment * ROWS_PER_SEGMENT;
			$row_inc = ROWS_PER_SEGMENT;
		}
		do {	
			//Extend php and mysql wait timeout to 15 minutes
			$timeout=15*60;
			if ( !ini_get('safe_mode')) @set_time_limit($timeout);
			$wpdb->query("SET wait_timeout = $timeout");
			$table_data = $wpdb->get_results("SELECT * FROM $table LIMIT {$row_start}, {$row_inc}", ARRAY_A);

			$entries = 'INSERT INTO ' . $table . ' VALUES (';	
			//    \x08\\x09, not required
			$search = array("\x00", "\x0a", "\x0d", "\x1a");
			$replace = array('\0', '\n', '\r', '\Z');
			if($table_data) {
				foreach ($table_data as $row) {
					$values = array();
					foreach ($row as $key => $value) {
						if ($ints[strtolower($key)]) {
							// make sure there are no blank spots in the insert syntax,
							// yet try to avoid quotation marks around integers
							$value = ( '' === $value) ? $defs[strtolower($key)] : $value;
							$values[] = ( '' === $value ) ? "''" : $value;
						} else {
							$values[] = "'" . str_replace($search, $replace, addslashes($value)) . "'";
						}
					}
					$sql .= " \n" . $entries . implode(', ', $values) . ') ;';
				}
				$row_start += $row_inc;
			}
		} while((count($table_data) > 0) and ($segment=='none'));
		//reset mysql wait timeout to 1 minute
		$wpdb->query("SET wait_timeout = 60");
	}
	
	if(($segment == 'none') || ($segment < 0)) {
		// Create footer/closing comment in SQL-file
		$sql .= "\n";
		$sql .= "#\n";
		$sql .= "# " . sprintf(__('End of data contents of table %s','wp-db-backup'),$table) . "\n";
		$sql .= "# --------------------------------------------------------\n";
		$sql .= "\n";
	}
	return $sql;
} // end backup_table()

if (!function_exists('microtime_float')) {
function microtime_float() {	//replicates microtime(true) from PHP5
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}
}

// hook function to put a timestamp in page footer for page caching test
function wassup_foot() {
	global $wassup_options, $wassupversion, $wscreen_res, $wdebug_mode;

	//Since 1.8.2: separate 'wassup_screen_res' cookie in footer for
	// IE users because Internet Explorer does not report screen height
	// or width until after it begins to render the document body.
	if (empty($wscreen_res) && !isset($_COOKIE['wassup_screen_res'])) {
		echo "\n"; ?>
<!--[if IE]>
<script language=javascript>
//<![CDATA[
	if (screen_res=="") {
		screen_res = screen.width + " x " + screen.height;
	}
	if (screen_res!=" x ") {
		var cdate = new Date();
		cdate.setTime(cdate.getTime()+(48*60*60*1000));
		var cexpires = cdate.toGMTString();
		//var the_cookie = "wassup_screen_res="+escape(screen_res)+"; expires="+cexpires;
		document.cookie = "wassup_screen_res=" + escape(screen_res)+ "; path=/; domain=" + document.domain;

	}
//]]>
</script>
<![endif]--><?php
	} //end if !isset('wassup_screen_res')

	//Output a comment with a current timestamp to verify that page is not cached (i.e. visit is being recorded).
	echo "<!--\n<p class=\"small\"> WassUp $wassupversion ".__("timestamp","wassup").": ".date('Y-m-d h:i:sA T')." (".gmdate('h:iA',time()+(get_option('gmt_offset')*3600)).")<br />\n";
	echo __("If above timestamp is not current time, this page is cached","wassup").".</p> -->\n";
} //end wassup_foot

/**
 * Perform db operations on wassup tables
 *  -Helene D. 2010-04-27
 * @param array
 * @return none
 * @author Helene D.
 * @since version 1.8
 */
function wassup_scheduled_dbtask($dbtask) {
	global $wpdb;
	if (!empty($dbtask)) {
		if (!is_array($dbtask)) $db_tasks=unserialize($dbtask);
		else $db_tasks=$dbtask;
		if (!is_array($db_tasks))  $db_tasks = array($db_tasks);
		//some db operations can be slow on large tables, so extend
		// script execution time
		if ( !ini_get('safe_mode')) { // run script up to 0.5 hour
			@set_time_limit(60*30);
		}
		foreach ($db_tasks as $db_sql) {
			if (strstr($db_sql,' WHERE ')!==false) {
				$wpdb->query($db_sql);
			} elseif (strstr($db_sql,'OPTIMIZE TABLE ')!==false) {
				$wpdb->query($db_sql);
			}
		}
	}
} //end function wassup_scheduled_dbtask


// Security functions
/**
 * Check for obvious signs of script injection and hack attempts -Helene D. 2010-02-01
 * @param none
 * @return boolean
 * @author Helene D.
 * @since version 1.8
 */
function wIsAttack() {
	$is_attack=false;
	if (preg_match('/["\';<>\$]/',$_SERVER['REQUEST_URI'])>0) {
		$is_attack=true;
	} elseif (preg_match('/[&?].+\=(\-(1|9)+|.*(select|update|delete|alter|drop|union|create)[ %&].*(?:from)?.*wp_\w+)/i',str_replace(array('\\','&#92;','"','%22','&#34;','&quot','&#39;','\'','`','&#96;'),'',$_SERVER['REQUEST_URI']))>0) {
		$is_attack=true;
	} elseif (preg_match('/((<|&lt;|&#60;|%3C)script[^a-z0-9])|((\.{1,2}\/){3,})|(\=\.\.\/)/i',$_SERVER['REQUEST_URI'])>0) {
		$is_attack=true;
	} elseif (preg_match('/[^a-z_\-](dir|file|href|img|location|path|src|document_root.?)\=/i',$_SERVER['REQUEST_URI'])>0) {
		$is_attack=true;
	} elseif (preg_match('/[\.\/](ini|exe|cmd|aspx?|bin|etc)/i',$_SERVER['REQUEST_URI'])>0) {
		$is_attack=true;
	//} elseif (preg_match('/(document|function|script|window|cookie)/i',$_SERVER['REQUEST_URI'])>0) {
	//	$is_attack=true;
	}
	return $is_attack;
} //end function wIsAttack

// START initializing Widget
function wassup_widget_init() {

        if ( !function_exists('register_sidebar_widget') )
                return;

function wassup_widget($wargs) {
	global $wpdb;
	extract($wargs);
	$wassup_settings = get_option('wassup_settings');
	$wpurl =  get_bloginfo('wpurl');
	$blogurl =  get_bloginfo('home');
	$wassup_table = $wassup_settings['wassup_table'];
	$table_tmp_name = $wassup_table . "_tmp";
	if ($wassup_settings['wassup_widget_title'] != "") $title = $wassup_settings['wassup_widget_title']; else $title = "Visitors Online";
	if ($wassup_settings['wassup_widget_ulclass'] != "") $ulclass = $wassup_settings['wassup_widget_ulclass']; else $ulclass = "links";
	if ($wassup_settings['wassup_widget_chars'] != "") $chars = $wassup_settings['wassup_widget_chars']; else $chars = "18";
	if ($wassup_settings['wassup_widget_searchlimit'] != "") $searchlimit = $wassup_settings['wassup_widget_searchlimit']; else $searchlimit = "5";
	if ($wassup_settings['wassup_widget_reflimit'] != "") $reflimit = $wassup_settings['wassup_widget_reflimit']; else $reflimit = "5";
	if ($wassup_settings['wassup_widget_topbrlimit'] != "") $topbrlimit = $wassup_settings['wassup_widget_topbrlimit']; else $topbrlimit = "5";
	if ($wassup_settings['wassup_widget_toposlimit'] != "") $toposlimit = $wassup_settings['wassup_widget_toposlimit']; else $toposlimit = "5";
	
	print $before_widget;

	//show stats only when WassUp is active
	if (empty($wassup_settings['wassup_active'])) {
		print $before_title . $title . $after_title;
		print "<ul class='$ulclass'><li>".__("No Data","wassup")."</li>\n";
		print "<span style='font-size:6pt; text-align:center;'>".__("powered by", "wassup")." <a href='http://www.wpwp.org/' title='WassUp - ".__("Real Time Visitors Tracking","wassup")."'>WassUp</a></span></ul>";

	} else {	//Wassup is recording (active)
		$to_date = current_time('timestamp');
		$from_date = strtotime('-3 minutes', $to_date);

	// Widget Latest Searches
	if ($wassup_settings['wassup_widget_search'] == 1) {
	$query_det = $wpdb->get_results("SELECT search, referrer FROM $table_tmp_name WHERE search!='' GROUP BY search ORDER BY `timestamp` DESC LIMIT ".attribute_escape($searchlimit)."");
	if (count($query_det) > 0) {
		print "$before_title ".__('Last searched terms','wassup')." $after_title";
		print "<ul class='$ulclass'>";
		foreach ($query_det as $sref) {
			print "<li>- <a href='".wCleanURL($sref->referrer)."' target='_blank' rel='nofollow'>".stringShortener($sref->search, $chars)."</a></li>";
		}
		print "</ul>";
	}
	}

	// Widget Latest Referers
	if ($wassup_settings['wassup_widget_ref'] == 1) {
	$query_ref = $wpdb->get_results("SELECT referrer FROM $table_tmp_name WHERE searchengine='' AND referrer!='' AND referrer NOT LIKE '$wpurl%' GROUP BY referrer ORDER BY `timestamp` DESC LIMIT ".attribute_escape($reflimit)."");
	if (count($query_ref) > 0) {
		print "$before_title ".__('Last referers','wassup')." $after_title";
		print "<ul class='$ulclass'>";
		foreach ($query_ref as $eref) {
			print "<li>- <a href='".wCleanURL($eref->referrer)."' target='_blank' rel='nofollow'>".stringShortener(preg_replace('#https?://#i','',$eref->referrer), $chars)."</a></li>";
		}
		print "</ul>";
	}
	}

	$wstart = (int)(current_time('timestamp') - 30.4*86400); //1 month in seconds
	// Widget TOP Browsers
	if ($wassup_settings['wassup_widget_topbr'] == 1) {
		$top_period = "'`timestamp` > $wstart'";	//one month
		$top_limit = attribute_escape($topbrlimit);
		$top_results =  wGetStats("browser",$top_limit,$top_period);
		if (count($top_results) > 0) {
			print "$before_title ".__('Top Browsers','wassup')." $after_title";
			print "<ul class='$ulclass'>";
			foreach ($top_results as $wtop) {
				print "<li>- ".stringShortener($wtop->top_item, $chars)."</li>";
			}
			print "</ul>";
		}
	}

	// Widget TOP OSes
	if ($wassup_settings['wassup_widget_topos'] == 1) {
		$top_period = "'`timestamp` > $wstart'";	//one month
		$top_limit = attribute_escape($toposlimit);
		$top_results =  wGetStats("os",$top_limit,$top_period);
		if (count($top_results) > 0) {
			print "$before_title ".__('Top OS','wassup')." $after_title";
			print "<ul class='$ulclass'>";
			foreach ($top_results as $wtop) {
				print "<li>- ".stringShortener($wtop->top_item, $chars)."</li>";
			}
			print "</ul>";
		}
	}

	// Widget Visitors Online
	$TotWid = New WassupItems($table_tmp_name,$from_date,$to_date);

	$currenttot = $TotWid->calc_tot("count", null, null, "DISTINCT");
	$currentlogged = $TotWid->calc_tot("count", null, "AND  username!=''", "DISTINCT");
	$currentauth = $TotWid->calc_tot("count", null, "AND  comment_author!='' AND username=''", "DISTINCT");

        print $before_title . $title . $after_title;
        print "<ul class='$ulclass'>";
        if ((int)$currenttot < 10) $currenttot = "0".$currenttot;
        print "<li><strong style='padding:0 4px 0 4px;background:#ddd;color:#777'>".$currenttot."</strong> ".__('visitor(s) online','wassup')."</li>";
        if ((int)$currentlogged > 0 AND $wassup_settings['wassup_widget_loggedin'] == 1) {
        if ((int)$currentlogged < 10) $currentlogged = "0".$currentlogged;
                print "<li><strong style='padding:0 4px 0 4px;background:#e7f1c8;color:#777'>".$currentlogged."</strong> ".__('logged-in user(s)','wassup')."</li>";
        }
        if ((int)$currentauth > 0 AND $wassup_settings['wassup_widget_comauth'] == 1) {
        if ((int)$currentauth < 10) $currentauth = "0".$currentauth;
                print "<li><strong style='padding:0 4px 0 4px;background:#fbf9d3;color:#777'>".$currentauth."</strong> ".__('comment author(s)','wassup')."</li>";
	}
	print "<li style='font-size:6pt; color:#bbb;'>".__("powered by", "wassup")." <a style='color:#777;' href='http://www.wpwp.org' title='WassUp - ".__("Real Time Visitors Tracking","wassup")."'>WassUp</a></li>";
	print "</ul>";

	} //end if empty(wassup_active)

	print $after_widget;
} //end function wassup_widget

	//User-selectable widget options
	function wassup_widget_control() {
		global $wpdb;
		$wassup_settings = get_option('wassup_settings');

		//save widget form input
		if (isset($_POST['wassup-submit'])) {
			$wassup_settings['wassup_widget_title'] = attribute_escape($_POST['widget_title']);
			$wassup_settings['wassup_widget_ulclass'] = attribute_escape($_POST['widget_ulclass']);
			if (is_numeric($_POST['widget_chars'])) {
				$wassup_settings['wassup_widget_chars'] = $_POST['widget_chars'];
			}
			$wassup_settings['wassup_widget_loggedin'] = $_POST['widget_loggedin'];
			$wassup_settings['wassup_widget_comauth'] = $_POST['widget_comauth'];
			$wassup_settings['wassup_widget_search'] = $_POST['widget_search'];
			if ((int)$_POST['widget_searchlimit']>0) {
				$wassup_settings['wassup_widget_searchlimit'] = (int)$_POST['widget_searchlimit'];
			} elseif (empty($wassup_settings['wassup_widget_searchlimit'])) {
				$wassup_settings['wassup_widget_searchlimit'] = 5;
			}
			$wassup_settings['wassup_widget_ref'] = $_POST['widget_ref'];
			if ((int)$_POST['widget_reflimit']>0) {
				$wassup_settings['wassup_widget_reflimit'] = (int)$_POST['widget_reflimit'];
			} elseif (empty($wassup_settings['wassup_widget_reflimit'])) {
				$wassup_settings['wassup_widget_reflimit'] = 5;
			}
			$wassup_settings['wassup_widget_topbr'] = $_POST['widget_topbr'];
			if ((int)$_POST['widget_topbrlimit']>0) {
				$wassup_settings['wassup_widget_topbrlimit'] = (int)$_POST['widget_topbrlimit'];
			}
			$wassup_settings['wassup_widget_topos'] = $_POST['widget_topos'];
			if ((int)$_POST['widget_toposlimit']>0) {
				$wassup_settings['wassup_widget_toposlimit'] = (int)$_POST['widget_toposlimit'];
			}

			if (empty($wassup_settings['wassup_userlevel'])) {
				$wassup_settings['wassup_userlevel'] = 8;
			}
			if (empty($wassup_settings['wassup_refresh'])) {
				$wassup_settings['wassup_refresh'] = 3;
			}
			//save the new widget selections
			update_option('wassup_settings', $wassup_settings); 
		} //end if _POST[submit]

		//widget selection form 
		$title = (isset($wassup_settings['wassup_widget_title']))? attribute_escape($wassup_settings['wassup_widget_title']): "Visitors Online";
		$ulclass = (isset($wassup_settings['wassup_widget_ulclass']))? attribute_escape($wassup_settings['wassup_widget_ulclass']): "links";
		$chars = (!empty($wassup_settings['wassup_widget_chars'])) ? (int) $wassup_settings['wassup_widget_chars']: 18;
		$searchlimit = (!empty($wassup_settings['wassup_widget_searchlimit'])) ? (int)$wassup_settings['wassup_widget_searchlimit']: 5;
		$reflimit = (!empty($wassup_settings['wassup_widget_reflimit'])) ? (int)$wassup_settings['wassup_widget_reflimit']: 5;
		$topbrlimit = (!empty($wassup_settings['wassup_widget_topbrlimit'])) ? (int)$wassup_settings['wassup_widget_topbrlimit']: 5;
		$toposlimit = (!empty($wassup_settings['wassup_widget_toposlimit'])) ? (int)$wassup_settings['wassup_widget_toposlimit']: 5;
		?>
		<div>
		<p style="align:left;">
		<label for="widget_title"><nobr><?php _e("Title","wassup"); ?>: 
			<input style="width:200px;background-color:#ddd;" type="text" name="widget_title" id="widget_title" value="<?php echo $title; ?>" /></nobr>
			<nobr> &nbsp; <small>(<?php _e("default \"Visitors Online\"", "wassup") ?>)</small></nobr>
		</label></p>
		<p style="align:left;">
		<label for="widget_ulclass"><nobr><?php _e("Stylesheet class for &lt;ul&gt; attribute","wassup"); ?>:
			<input style="width:100px;background-color:#ddd;" type="text" name="widget_ulclass" id="widget_ulclass" value="<?php echo $ulclass; ?>" /></nobr>
		<nobr> &nbsp; <small>(<?php _e("default \"links\"","wassup"); ?>)</small></nobr>
		</label></p>
		<p style="align:left;">
		<label for="widget_chars"><nobr><?php _e("Number of characters to display from left","wassup"); ?>? 
			<input style="width:50px;background-color:#ddd;" type="text" name="widget_chars" id="widget_chars" value="<?php echo $chars; ?>" /></nobr>
			<br/><nobr> &nbsp; <small>(<?php _e("For template compatibility - default 18", "wassup"); ?>)</small></nobr>
		</label></p>
		<p style="align:left;">
		<label for="widget_loggedin">
			<nobr><input type="checkbox" name="widget_loggedin" id="widget_loggedin" value="1" <?php if (!empty($wassup_settings['wassup_widget_loggedin'])) { echo "CHECKED"; } ?> />
			<?php _e("Show number of logged-in users online","wassup"); ?></nobr> 
			<br/><span style="padding-left:25px;"><nobr><small>(<?php _e("Stats recording must be enabled in WassUp options", "wassup"); ?>)</small></nobr></span>
			<!-- (<?php _e("default Yes", "wassup"); ?>) -->
		</label></p>
		<p style="align:left;">
		<label for="widget_comauth">
			<nobr><input type="checkbox" name="widget_comauth" id="widget_comauth" value="1" <?php if (!empty($wassup_settings['wassup_widget_comauth'])) { echo "CHECKED"; } ?> />
			<?php _e("Show number of comment authors online", "wassup"); ?></nobr>
			<!-- (<?php _e("default Yes", "wassup"); ?>) -->
		</label></p>
		<p style="align:left;">
		<label for="widget_search">
			<nobr><input type="checkbox" name="widget_search" id="widget_search" value="1" <?php if (!empty($wassup_settings['wassup_widget_search'])) { echo "CHECKED"; } ?> />
			<?php _e("Show latest searches","wassup"); ?></nobr>
			<!-- (<?php _e("default Yes", "wassup"); ?>) -->
		</label>
		<label for="widget_searchlimit"><span style="padding-left:25px;line-height:1.1em;display:block;">
			<nobr><?php _e("How many searches?","wassup"); ?>
			<input style="width:40px;background-color:#ddd;" name="widget_searchlimit" id="widget_searchlimit" value="<?php echo $searchlimit; ?>" /></nobr> 
			<small>(<?php _e("default 5", "wassup"); ?>)</small></span>
		</label>
		</p>
		<p style="align:left;">
		<label for="widget_ref">
			<nobr><input type="checkbox" name="widget_ref" id="widget_ref" value="1" <?php if (!empty($wassup_settings['wassup_widget_ref'])) { echo "CHECKED"; } ?> />
			<?php _e("Show latest external referrers", "wassup"); ?></nobr>
			<!-- (<?php _e("default Yes", "wassup"); ?>) -->
		</label>
		<label for="widget_reflimit"><span style="padding-left:25px;line-height:1.1em;display:block;">
			<nobr><?php _e("How many referrers?","wassup"); ?>
			<input style="width:40px;background-color:#ddd;" name="widget_reflimit" id="widget_reflimit" value="<?php echo $reflimit; ?>" /></nobr>
			<small>(<?php _e("default 5", "wassup"); ?>)</small></span>
		</label>
		</p>
		<p style="align:left;">
		<label for="widget_topbr">
			<nobr><input type="checkbox" name="widget_topbr" id="widget_topbr" value="1" <?php if (!empty($wassup_settings['wassup_widget_topbr'])) { echo "CHECKED"; } ?> />
			<?php _e("Show top browsers","wassup"); ?> <small>(<?php _e("default No","wassup"); ?>)</small></nobr>
			 <span style="padding-left:25px;"><small><nobr>(<?php _e("Enabling it could slow down blog)", "wassup"); ?></nobr></small>
		</label>
		<label for="widget_topbrlimit"><span style="padding-left:25px;line-height:1.1em;display:block;">
			<nobr><?php _e("How many browsers?","wassup"); ?>
			<input style="width:40px;background-color:#ddd;" name="widget_topbrlimit" id="widget_topbrlimit" value="<?php echo $topbrlimit; ?>" /></nobr>
			<small>(<?php _e("default 5", "wassup"); ?>)</small></span>
		</label>
		</p>
		<p style="align:left;">
		<label for="widget_topos">
			<nobr><input type="checkbox" name="widget_topos" id="widget_topos" value="1" <?php if (!empty($wassup_settings['wassup_widget_topos'])) { echo "CHECKED"; } ?> />
			<?php _e("Show top operating systems","wassup"); ?> <small>(<?php _e("default No","wassup"); ?>)</small></nobr>
			 <span style="padding-left:25px;"><small><nobr>(<?php _e("Enabling it could slow down blog)", "wassup"); ?></nobr></small>
		</label>
		<label for="widget_toposlimit"><span style="padding-left:25px;line-height:1.1em;display:block;">
			<nobr><?php _e("How many operating systems?","wassup"); ?>
			<input style="width:40px;background-color:#ddd;" name="widget_toposlimit" id="widget_toposlimit" value="<?php echo $toposlimit; ?>" /></nobr>
			<small>(<?php _e("default 5", "wassup"); ?>)</small></span>
		</label>
		</p>
		<p style="text-align:left;"><input type="hidden" name="wassup-submit" id="wassup-submit" value="1" /></p>
		</div>
	<?php
	} //end function wassup_widget_control

	if(function_exists('register_sidebar_widget')) {
		register_sidebar_widget(__('Wassup Widget'), 'wassup_widget'); 
		register_widget_control(array('Wassup Widget', 'widgets'), 'wassup_widget_control', 500, 440);
	}
} //end function wassup_widgit_init

function wassup_sidebar($before_widget='', $after_widget='', $before_title='', $after_title='', $wtitle='', $wulclass='', $wchars='', $wsearch='', $wsearchlimit='', $wref='', $wreflimit='', $wtopbr='', $wtopbrlimit='', $wtopos='', $wtoposlimit='') {
	global $wpdb;
	$wpurl =  get_bloginfo('wpurl');
	$blogurl =  get_bloginfo('home');
	$wassup_settings = get_option('wassup_settings');
	$wassup_table = $wassup_settings['wassup_table'];
	$table_tmp_name = $wassup_table . "_tmp";
	if ($wtitle != "") $title = $wtitle; else $title = "Visitors Online";
	if ($wulclass != "") $ulclass = $wulclass; else $ulclass = "links";
	if ($wchars != "") $chars = $wchars; else $chars = "18";
	if ($wsearchlimit != "") $searchlimit = $wsearchlimit; else $searchlimit = "5";
	if ($wreflimit != "") $reflimit = $wreflimit; else $reflimit = "5";
	if ($wtopbrlimit != "") $topbrlimit = $wtopbrlimit; else $topbrlimit = "5";
	if ($wtoposlimit != "") $toposlimit = $wtoposlimit; else $toposlimit = "5";
	$to_date = current_time('timestamp');
	$from_date = strtotime('-3 minutes', $to_date);

	print $before_widget;

	//show stats only when WassUp is active
	if (empty($wassup_settings['wassup_active'])) {
		print $before_title . $title . $after_title;
		print "<ul class='$ulclass'><li>".__("No Data","wassup")."</li>\n";
		print "<span style='font-size:6pt; text-align:center;'>".__("powered by", "wassup")." <a href='http://www.wpwp.org/' title='WassUp - ".__("Real Time Visitors Tracking","wassup")."'>WassUp</a></span></ul>";

	} else {	//Wassup is recording (active)


	if ($wsearch == 1) {
	$query_det = $wpdb->get_results("SELECT search, referrer FROM $table_tmp_name WHERE search!='' GROUP BY search ORDER BY `timestamp` DESC LIMIT $searchlimit");
	if (count($query_det) > 0) {
		print "$before_title Last searched terms $after_title";
		print "<ul class='$ulclass'>";
		foreach ($query_det as $sref) {
			print "<li>- <a href='".attribute_escape($sref->referrer)."' target='_blank' rel='nofollow'>".stringShortener(attribute_escape($sref->search), $chars)."</a></li>";
		}
		print "</ul>";
	}
	}

	if ($wref == 1) {
	$query_ref = $wpdb->get_results("SELECT referrer FROM $table_tmp_name WHERE searchengine='' AND referrer!='' AND referrer NOT LIKE '$wpurl%' GROUP BY referrer ORDER BY `timestamp` DESC LIMIT $reflimit");
	if (count($query_ref) > 0) {
		print "$before_title Last referers $after_title";
		print "<ul class='$ulclass'>";
		foreach ($query_ref as $eref) {
			print "<li>- <a href='".attribute_escape($eref->referrer)."' target='_blank' rel='nofollow'>".stringShortener(preg_replace("#https?://#", "", attribute_escape($eref->referrer)), $chars)."</a></li>";
		}
		print "</ul>";
	}
	}

	$wstart = (int)(current_time('timestamp') - 30.4*86400); //1 month in seconds
	if ($wtopbr == 1) {
		$top_period = "'`timestamp` > $wstart'";	//one month
		$top_limit = attribute_escape($topbrlimit);
		$top_results =  wGetStats("browser",$top_limit,$top_period);
		if (count($top_results) > 0) {
			print "$before_title ".__('Top Browsers','wassup')." $after_title";
			print "<ul class='$ulclass'>";
			foreach ($top_results as $wtop) {
				print "<li>- ".stringShortener($wtop->top_item, $chars)."</li>";
			}
			print "</ul>";
		}
	}

	if ($wtopos == 1) {
		$top_period = "'`timestamp` > $wstart'";	//one month
		$top_limit = attribute_escape($toposlimit);
		$top_results =  wGetStats("os",$top_limit,$top_period);
		if (count($top_results) > 0) {
			print "$before_title ".__('Top OS','wassup')." $after_title";
			print "<ul class='$ulclass'>";
			foreach ($top_results as $wtop) {
				print "<li>- ".stringShortener($wtop->top_item, $chars)."</li>";
			}
			print "</ul>";
		}
	}

	// Visitors Online
	$TotWid = New WassupItems($table_tmp_name,$from_date,$to_date);

	$currenttot = $TotWid->calc_tot("count", null, null, "DISTINCT");
	$currentlogged = $TotWid->calc_tot("count", null, "AND  username!=''", "DISTINCT");
	$currentauth = $TotWid->calc_tot("count", null, "AND  comment_author!=''' AND username=''", "DISTINCT");

	print $before_title . $title . $after_title;
	print "<ul class='$ulclass'>";
	if ((int)$currenttot < 10) $currenttot = "0".$currenttot;
	print "<li><strong style='padding:0 4px 0 4px;background:#ddd;color:#777'>".$currenttot."</strong> visitor(s) online</li>";
	if ((int)$currentlogged > 0 AND $wassup_settings['wassup_widget_loggedin'] == 1) {
	if ((int)$currentlogged < 10) $currentlogged = "0".$currentlogged;
		print "<li><strong style='padding:0 4px 0 4px;background:#e7f1c8;color:#777'>".$currentlogged."</strong> logged-in user(s)</li>";
	}
	if ((int)$currentauth > 0 AND $wassup_settings['wassup_widget_comauth'] == 1) {

	if ((int)$currentauth < 10) $currentauth = "0".$currentauth;
		print "<li><strong style='padding:0 4px 0 4px;background:#fbf9d3;color:#777'>".$currentauth."</strong> comment author(s)</li>";
	}
	print "<li style='font-size:6pt; color:#bbb;'>".__("powered by", "wassup")." <a style='color:#777;' href='http://www.wpwp.org/' title='WassUp - Real Time Visitors Tracking'>WassUp</a></li>";
	print "</ul>";

	} //end if !empty(wassup_active)

	print $after_widget;
} //end function wassup_sidebar

// function to print out a chart's preview in the dashboard for WP < 2.7 //moved
function wassupDashChart() {
	global $wpdb, $wassup_options;
	$wassup_table = $wassup_options->wassup_table;
	if ($wassup_options->wassup_dashboard_chart == 1) {
		$chart_type = ($wassup_options->wassup_chart_type >0)? $wassup_options->wassup_chart_type: "2";
		$to_date = current_time("timestamp");
		$Chart = New WassupItems($wassup_table,"",$to_date);
        	$chart_url = $Chart->TheChart(1, "400", "125", "", $chart_type, "bg,s,efebef|c,lg,90,edffff,0,efebef,0.8", "dashboard"); ?>
	<h3>WassUp <?php _e('Stats','wassup'); ?> <cite><a href="admin.php?page=<?php echo WASSUPFOLDER; ?>"><?php _e('More','wassup'); ?> &raquo;</a></cite></h3>
	<div id="placeholder" align="left">
		<img src="<?php echo $chart_url; ?>" alt="WassUp <?php _e('visitor stats chart','wassup'); ?>"/>
	</div>
<?php	}
} //end function wassupDashChart

// Create functions to output the contents of Dashboard Widget in WP 2.7+
function wassup_dashboard_widget_function() {
	global $wpdb, $wassup_options, $user_level;

	$wassup_table = $wassup_options->wassup_table;
	$table_tmp_name = $wassup_table."_tmp";
	$to_date = current_time("timestamp");
	$chart_type = ($wassup_options->wassup_chart_type >0)? $wassup_options->wassup_chart_type: "2";
	$res = ((int)$wassup_options->wassup_screen_res-160)/2;
	$Chart = New WassupItems($wassup_table,"",$to_date);
	$chart_url = $Chart->TheChart(1, $res, "180", "", $chart_type, "bg,s,efebef|c,lg,90,edffff,0,eae9e9,0.8", "dashboard"); 
	$max_char_len= 40;
	$wpurl = get_bloginfo('wpurl');
	echo '
	<style>
	#placeholder { 
		margin:-10px !important;
		padding:2px 0 5px;
		background: #eae9e9 url("'.WASSUPFOLDER.'/img/bg_wrap.png") repeat;
		font-size:11px;
	}
	#wassup_dashboard_widget .wassup_dash_box {
		margin: 0;
		padding: 5px 10px;
		text-align:left;
	}
	#wassup_dashboard_widget .wassup_dash_box p {
		margin: 4px 0 8px 0;
		padding: 0 0 8px 20px;
		text-indent: -20px;
		font-weight: normal;
		border-bottom: 1px solid #dfdfdf;
	}
	#wassup_dashboard_widget h5 {
		border-top: 2px solid #dfdfdf;
		margin: 0;
		padding: 15px 5px 10px 10px;
		font-size:12px;
	}
	#wassup_dashboard_widget h5 strong {
		font-size:24px;
		margin: 0 10px 0 0;
		padding:2px 10px 2px 10px;
		background-color:#aabbff;
		border:1px solid #dfdfdf;
	}
	#placeholder p { margin:0; padding:0 2px 10px; overflow:auto; }
	#placeholder p img { margin:0; padding:0 10px 0 0; }
	#placeholder cite { 
		padding: 0 5px; 
		font-size: 0.8em;
		position:absolute;
		right: 20px;
		top: -15px;
	}
</style>
'; ?>
	<div id="placeholder">
		<cite><a href="admin.php?page=<?php echo WASSUPFOLDER; ?>"><?php _e('More Stats','wassup'); ?> &raquo;</a></cite>
		<p id="wassup_dashchart" align="center"><img src="<?php echo $chart_url; ?>" alt="[img: WassUp <?php _e('visitor stats chart','wassup'); ?>]"/></p><?php
		$from_date = $to_date - 3*60; //-3 minutes from timestamp
		$currenttot = 0;
		$qryC = $wpdb->get_results("SELECT `id`, wassup_id, max(timestamp) as max_timestamp, `ip`, urlrequested, `referrer`, searchengine, spider, `username`, comment_author, spam FROM $table_tmp_name WHERE `timestamp` > $from_date GROUP BY wassup_id ORDER BY max_timestamp DESC");
		if (!empty($qryC) && is_array($qryC)) {
			$currenttot = count($qryC);
		}

		if ($currenttot > 0) { ?>
			<h5><?php echo '<strong>'.$currenttot."</strong>".__("Visitors online", "wassup"); ?></h5><?php
			//show visit info for approved users only
			get_currentuserinfo();
			if (empty($user_level) || $wassup_options->wassup_userlevel <= $user_level ) { ?>
			<div class='wassup_dash_box'><?php
			foreach ($qryC as $cv) {
				if ($wassup_options->wassup_time_format == 24) {
					$timed = gmdate("H:i:s", $cv->max_timestamp);
				} else {
					$timed = gmdate("h:i:s a", $cv->max_timestamp);
				}
				$ip_proxy = strpos($cv->ip,",");
				//if proxy, get 2nd ip...
				if ($ip_proxy !== false) {
					$ip = substr($cv->ip,(int)$ip_proxy+1);
				} else { 
					$ip = $cv->ip;
				}
				if ($cv->referrer != '') {
					if ($cv->searchengine != "" || stristr($cv->referrer,$wpurl)!=$cv->referrer) { 
					if ($cv->searchengine == "") {
						$referrer = '<a href="'.wCleanURL($cv->referrer).'" target=_"BLANK"><span style="font-weight: bold;">'.stringShortener("{$cv->referrer}", round($max_char_len*.8,0)).'</span></a>';
					} elseif (empty($cv->spam)) {
						$referrer = '<a href="'.wCleanURL($cv->referrer).'" target=_"BLANK">'.stringShortener("{$cv->referrer}", round($max_char_len*.9,0)).'</a>';
					} else {
						$referrer = __("Direct hit", "wassup"); 
					}
					} else { 
						$referrer = __("From your blog", "wassup"); 
					} 
				} else { 
					$referrer = __("Direct hit", "wassup"); 
				} 
		 		// User is logged in or is a comment's author
				if ($cv->username != "" OR $cv->comment_author != "") {
					if ($cv->username != "") {
						$Ousername[] = $cv->username; 
						$Ocomment_author[] = $cv->comment_author; 
					} elseif ($cv->comment_author != "") {
						$Ocomment_author[] = $cv->comment_author; 
					}
				}

				if (strstr($cv->urlrequested,"[404]")) {  //no link for 404 page
					$requrl = stringShortener($cv->urlrequested, round($max_char_len*.9,0)+5);
				} else {
					$requrl = '<a href="'.wAddSiteurl("{$cv->urlrequested}").'" target="_BLANK">';
					$requrl .= stringShortener("{$cv->urlrequested}", round($max_char_len*.9,0)).'</a>';
				} ?>
				<p><strong><?php print $timed; ?></strong> - <?php echo $ip; ?> - <?php print $requrl ?><br /><?php echo __("Referrer", "wassup"); ?>: <?php echo $referrer; ?></p><?php		 
			} //end foreach qryC
?>
			</div><?php
			if (count($Ousername) > 0) { ?>
			<div class="wassup_dash_box"><p><?php
				echo __('Registered users','wassup').': '.implode(',', $Ousername); ?></p></div><?php
			} elseif (count($Ocomment_author) > 0) { ?>
			<div class="wassup_dash_box"><p><?php
				echo __('Comment authors','wassup').': '.implode(",", $Ocomment_author); ?></p></div><?php
			}
			} //end if user_level
		} //end if currenttot
?>
		</div><?php
} //end wassup_dashboard_widget_function

// Create the function use in the action hook
function wassup_add_dashboard_widgets() {
	wp_add_dashboard_widget('wassup_dashboard_widget', 'WassUp Summary', 'wassup_dashboard_widget_function');	
}

//##Load Wassup functions into document head, contents, and admin menus using Wordpress hooks
function wassup_loader() {
	global $wp_version, $wassup_options;

	//## Wassup Admin hooks and filters
	if (is_admin()) {
		register_deactivation_hook(__FILE__, 'wassup_uninstall');

		//add hooks for wassup admin header functions
		add_action('admin_head', 'add_wassup_css');
		add_action('admin_menu', 'wassup_add_pages');

		// add dashboard widget hook when WassUp is active
		if (!empty($wassup_options->wassup_dashboard_chart) && !empty($wassup_options->wassup_active)) {
			if (version_compare($wp_version, '2.7', '<')) {
				add_action('activity_box_end', 'wassupDashChart');
			} else {
				// Hook into the 'wp_dashboard_setup' action to register our other functions
				add_action('wp_dashboard_setup', 'wassup_add_dashboard_widgets' );
			}
		}
	} //end if is_admin

	//## non-admin and visitor tracking hooks
	if (!empty($wassup_options->wassup_active)) {
		add_action("widgets_init", "wassup_widget_init");
		//add_action('wp_head', 'wassup_head'); //now in wassupPrepend
		add_action('wp_footer', 'wassup_foot');
	} //end if wassup_active
} //end function wassup_loader

//### Add hooks after functions have been defined
//# General hooks
register_activation_hook(__FILE__, 'wassup_install'); 
//'init' hook for actions required before http headers are sent 
add_action('init', 'wassup_init');	//<==wassupAppend hook added here 
//hooks for actions after headers are sent (output-related)
add_action('plugins_loaded', 'wassup_loader'); 
?>
