<?php
/**
 * @package WassUP
 * @subpackage action.php module
 */
/**
 * action.php -- perform an (ajax) action for WassUp admin and reports
 */
//immediately block any attempt to hack WordPress via WassUp
//  -Helene D. 2009-04-04
$is_attack=false;
if (preg_match('/["\';<>\$\\\*]/',$_SERVER['REQUEST_URI'])>0) {
	$is_attack=true;
} elseif (preg_match('/(\.+\/){3,}/',$_SERVER['REQUEST_URI'])>0) {
	$is_attack=true;
} elseif (preg_match('/(&lt;|&#60;|%3C)/',$_SERVER['REQUEST_URI'])>0) {
	$is_attack=true;
} elseif (preg_match('#[^a-z_/\-](select|delete|update|alter|drop|create|union|\-1|\-9+)[^a-z_/]#i',$_SERVER['REQUEST_URI'])>0) {
	$is_attack=true;
} elseif (preg_match('/[^a-z_\-](dir|file|href|img|location|path|page|src|thisdir|document_root.?)\=/i',$_SERVER['REQUEST_URI'])>0) {
	$is_attack=true;
} elseif (preg_match('/[\.\/](aspx?|bin|dll|cgi|cmd|etc|exe|ini|jsp)/i',$_SERVER['REQUEST_URI'])>0) {
	$is_attack=true;
} elseif (preg_match('/(document|function|script|window|cookie)/i',$_SERVER['REQUEST_URI'])>0) {
	$is_attack=true;
} 

if ($is_attack) {
   	header("HTTP/1.1 403 Forbidden");
	die('Illegal request - Permission Denied!');
}

//security check#2: check that hash exists
if (!isset($_GET['whash'])) {	//hash required
	header("HTTP/1.1 403 Forbidden");
	die('Missing or invalid parameter - Permission Denied!');
} 

//#check for required files and include them
if (!function_exists('get_bloginfo')) {
	//IMPORTANT NOTE: As of WordPress 2.6+ "/wp-content/" can be in a
	//  different location from the Wordpress install directory (i.e. 
	//  not a subdirectory). This configuration requires an additional 
	//  GET parameter "wpabspath=ABSPATH" for "action.php" to run.
	//-Helene D. 2009-04-04
	if (!empty($_GET['wpabspath'])) {
		$wpabspath=attribute_escape(base64_decode(urldecode($_GET['wpabspath'])));
	} elseif (defined('ABSPATH')) {
		$wpabspath=ABSPATH;
	} 
	if (empty($wpabspath) || !is_dir($wpabspath)) {
		$file = preg_replace('/\\\\/', '/', __FILE__);
		$wpabspath=substr($file,0,strpos($file, '/wp-content/')+1);
	}
	if (file_exists($wpabspath. 'wp-config.php')) {
        	include_once($wpabspath.'wp-config.php');
	} elseif (file_exists($wpabspath. '../wp-config.php')) { //since WP2.6
        	include_once($wpabspath.'../wp-config.php');
	} else {
		//Note: localization functions, _e() and __(), are not used
		// here because they would not be defined if this error 
		// occurred
		echo '<span style="color:red;">Action.php ERROR: path not found, '.$wpabspath.'</span>';
		die();
	}
}

//security check#3: check that user is logged in (can be faked)
$logged_user = wp_get_current_user();
$validuser = (!empty($logged_user->user_login)? true: false);
//#only logged-in users are allowed to run this script -Helene D.
if (!$validuser) {
	header("HTTP/1.1 403 Forbidden");
	$wassup_error=__('Login required. Permission denied!','wassup');
	wp_die($wassup_error);
}

//security check#4: check hash value
$hashfail = true;
if (isset($_GET['whash'])) {
	$wassup_settings = get_option('wassup_settings');
	if ($_GET['whash'] == $wassup_settings['whash'] || $_GET['whash'] == attribute_escape($wassup_settings['whash'])) {
		$hashfail = false;
	}
}

//#perform an "action" and display the results, if any
if (!$hashfail) {
	//#set required variables
	$wpurl =  get_bloginfo('wpurl');
	$table_name = (!empty($wassup_settings['wassup_table']))? $wassup_settings['wassup_table'] : $wpdb->prefix . "wassup";
	$table_tmp_name = $table_name . "_tmp";
	if (!defined('WASSUPFOLDER')) {
		define('WASSUPFOLDER', dirname(dirname(__FILE__)));
	}
	if (!defined('WASSUPURL')) {
		//flexible "wp-content" paths since WordPress 2.6
		if (function_exists('plugins_url')) { 	//Wordpress 2.6+ function
			$wassupurl = plugins_url(WASSUPFOLDER);
		} elseif (defined('WP_CONTENT_URL') && defined('WP_CONTENT_DIR') && strpos(WP_CONTENT_DIR,ABSPATH)===FALSE) {
			$wassupurl = rtrim(WP_CONTENT_URL,"/")."/plugins/".WASSUPFOLDER;
		} else {
			$wassupurl = $wpurl."/wp-content/plugins/".WASSUPFOLDER;
		}
		define('WASSUPURL',$wassupurl);
		unset ($wassupurl);	//to free memory
	}

	$wdebug_mode=false;	//debug set below
	//echo "Debug: Starting action.php from directory ".dirname(__FILE__).".  ABSPATH=".$wpabspath.".<br />\n"; //debug

	// ### Separate "delete" action because it has no output
	// ACTION: DELETE ON THE FLY FROM VISITOR DETAILS VIEW
	if ($_GET['action'] == "deleteID") {
		if (!empty($_GET['id'])) {
			if (method_exists($wpdb,'prepare')) {
				$wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE wassup_id='%s'", $_GET['id']));
			} else {
				$wpdb->query("DELETE FROM $table_name WHERE wassup_id='".attribute_escape($_GET['id'])."'");
			}
		} else {
			echo "Error: Missing wassup_id parameter";
		}
		exit();
	} //end if action==deleteID

	// ### Begin actions that have output...
	if (!empty($_GET['debug_mode'])) {
		$wdebug_mode=true;
		$mode_reset=ini_get('display_errors');
		error_reporting(E_ALL);	//debug, E_STRICT=php5 only
		ini_set('display_errors','On');	//debug
		echo "\n<!-- *WassUp DEBUG On-->\n";
		echo "<!-- *normal setting: display_errors=$mode_reset -->\n";
		if (function_exists('profiler_beginSection')) {
			profiler_beginSection('(Tot)Action.php');
		}
	}
	#load wassup core functions
	if (!function_exists('stringShortener')) {
		if (file_exists(dirname(__FILE__). '/main.php')) {
			include_once(dirname(__FILE__). '/main.php');
		} else {
			echo '<span style="font-color:red;">Action.php '.__("ERROR: file not found","wassup").', '.dirname(__FILE__).'/main.php</span>';
			exit();
		}
	}
	//#retrieve command-line arguments
	if (isset($_GET['to_date']) && is_numeric($_GET['to_date'])) {
		$to_date = (int)$_GET['to_date'];
	} else {
		$to_date = current_time('timestamp');
	}
	if (isset($_GET['from_date']) && is_numeric($_GET['from_date'])) {
		$from_date = (int)$_GET['from_date'];
	} else {
		$from_date = ($to_date - 180);	//3 minutes
	}

	if (isset($_GET['width']) && is_numeric($_GET['width'])) {
		$max_char_len = (int)($_GET['width'])/10;
	}
	if (isset($_GET['rows']) && is_numeric($_GET['rows'])) {
		$rows = (int)$_GET['rows'];
	}
	//#check that $to_date is a number
	if (!is_numeric($to_date)) { //bad date sent
		echo '<span style="color:red;">Action.php '.__("ERROR: bad date","wassup").', '.$to_date.'</span>';
		exit();
	}

	//force browser to disable caching so action.php works as an ajax request
	nocache_headers();
	//#perform an action and display output
?>
<html>
<head>
	<link rel="stylesheet" href="<?php echo WASSUPURL; ?>/css/wassup.css" type="text/css" />
</head>
<body>
<?php
	// ACTION: RUN SPY VIEW
	if ($_GET['action'] == "spia") {
		if (empty($rows)) { $rows = 0; }
		if (!empty($_GET['spiatype'])) $spytype=attribute_escape($_GET['spiatype']);
		else $spytype=$wassup_settings['wassup_default_spy_type'];
		$from_spydate=current_time('timestamp')-10;
		wassup_spiaView($from_spydate,$rows,$spytype);

	// ACTION: SUMMARY PIE CHART - TODO
	} elseif ($_GET['action'] == "piechart") {
		// Prepare Pie Chart
		$wTot = New WassupItems($table_name,$from_date,$to_date);
		$items_pie[] = $wTot->calc_tot("count", $search, "AND spam>0", "DISTINCT");
		$items_pie[] = $wTot->calc_tot("count", $search, "AND searchengine!='' AND spam=0", "DISTINCT");
		$items_pie[] = $wTot->calc_tot("count", $search, "AND searchengine='' AND referrer NOT LIKE '%".$this->WpUrl."%' AND referrer!='' AND spam=0", "DISTINCT");
		$items_pie[] = $wTot->calc_tot("count", $search, "AND searchengine='' AND (referrer LIKE '%".$this->WpUrl."%' OR referrer='') AND spam=0", "DISTINCT"); ?>
		<div style="text-align: center"><img src="http://chart.apis.google.com/chart?cht=p3&amp;chco=0000ff&amp;chs=600x300&amp;chl=Spam|Search%20Engine|Referrer|Direct&amp;chd=<?php Gchart_data($items_pie, null, null, null, 'pie'); ?>" /></div>

	<?php
	// ACTION: LINE CHART - TODO
	//} elseif ($_GET['action'] == "chart") {
	//	$chart = WassupItems::theChart($from_date,$to_date,$search);

	// ACTION: DISPLAY RAW RECORDS - no longer used (deprecated)
	//} elseif ($_GET['action'] == "displayraw") {

	// ACTION: SHOW TOP TEN
	} elseif ($_GET['action'] == "topten") {
		$top_limit=0;	//use default setting
		if ($wdebug_mode) {
			$title='WassUp '.__('Top Stats for Period','wassup');
			$wdformat = get_option("date_format");
			if (($to_date - $from_date) > 24*60*60) {
				$title .= ": ".gmdate("$wdformat",$from_date)." - ".gmdate("$wdformat",$to_date);
			} else {
				$title .= ": ".gmdate("$wdformat H:00",$from_date)." - ".gmdate("$wdformat H:00",$to_date);
			}
		} else {
			$title=false;
		}
		wassup_top10view($from_date, $to_date, $max_char_len, $top_limit,$title);
	// ACTION: DISPLAY GEOGRAPHIC AND WHOIS DETAILS	- TODO
	} else {
		echo '<span style="color:red;">Action.php '.__("ERROR: Missing or unknown parameters","wassup").', action='.attribute_escape($_GET["action"]).'</span>';
	}  
	if ($wdebug_mode) {
		if (function_exists('profiler_endSection')) {
			profiler_endSection('(Tot)Action.php');
			profiler_printResults();
		}
		//$wpdb->print_error();	//debug
		ini_set('display_errors',$mode_reset);	//turn off debug
	}?>
</body></html>	
	<?php
} else {
	echo '<span style="color:red;">Action.php '.__("ERROR: Nothing to do here","wassup").'</span>';
} //end if !$hashfail
?>
