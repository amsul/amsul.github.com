<?php
/**
 * @package WassUp
 * @subpackage wassup.class.php module
 */
//global $wpdb;
if (!class_exists('wassupOptions')) {
/**
 * wassupOptions - A PHP 4+ Class for Wassup plugin option settings
 *   Contains variables and methods used to set or change wassup 
 *   settings in Wordpress' wp_options table and to output those
 *   values for use in forms. 
 * @author: Helene Duncker. 2/24/08, 6/21/09, 2009-11-15, 2011-04-17
 */
class wassupOptions {
	/* general/detail settings */
	//var $wassup_debug_mode = "0";
	var $wassup_refresh = "3";	
	var $wassup_userlevel = "8";
	var $wassup_screen_res = "800";	
	var $wassup_default_type = "";	
	var $wassup_default_spy_type = "";
	var $wassup_default_limit = "10";
	var $wassup_top10 ;	//array containg top stats preferences
	var $wassup_dashboard_chart;	
	var $wassup_geoip_map;	
	var $wassup_googlemaps_key;
	var $wassup_spy_speed = "5000";	//New in 1.8.3
	var $wassup_time_format;
	var $wassup_time_period;	//since 1.8 - visitor details default range

	/* recording settings */
	var $wassup_active = "1";	
	var $wassup_loggedin = "1";
	var $wassup_admin = "1";	//since 1.7 - record administrators
	var $wassup_spider = "1";
	var $wassup_attack = "1";
	var $wassup_hack = "1";	
	var $wassup_exclude;	
	var $wassup_exclude_url;
	var $wassup_exclude_user;	//since 1.7 - for exclusion by username

	/* spam settings */
	var $wassup_spamcheck;
        var $wassup_spam;
        var $wassup_refspam;

	/* table/file management settings */
	//var $wassup_savepath;	//deprecated
	var $delete_auto;
	var $delete_filter;	//since 1.8 - auto delete select records
	var $wassup_remind_mb;
	var $wassup_remind_flag;
	var $wassup_uninstall;	//for complete uninstall of wassup
	var $wassup_optimize;	//for optimize table once a day
	var $wassup_version;	//since 1.7 - revision# for wassup updates
	var $wassup_table;	//since 1.7.2 - WassUp table name
	var $wassup_dbengine;	//since 1.7.2 - MySQL table engine type
	var $wassup_cache;	//since 1.8 - use wassup_meta table as cache

	/* chart display settings */
	var $wassup_chart;
	var $wassup_chart_type;

	/* widget settings */
	var $wassup_widget_title;
	var $wassup_widget_ulclass;
	var $wassup_widget_loggedin;
	var $wassup_widget_comauth;
	var $wassup_widget_search;
	var $wassup_widget_searchlimit;
	var $wassup_widget_ref;
	var $wassup_widget_reflimit;
	var $wassup_widget_topbr;
	var $wassup_widget_topbrlimit;
	var $wassup_widget_topos;
	var $wassup_widget_toposlimit;
	var $wassup_widget_chars;

	/* temporary action settings */
	var $wassup_alert_message;	//to display alerts
	var $wmark;
	var $wip;
	var $whash = "";	//wp_hash value used by action.php

	/* Constructor */
	function wassupoptions() {
		//# initialize class variables with current options 
		//# or with defaults if none
		$this->loadSettings();
	}

	/* Methods */
	function loadDefaults() {
		$defaults = $this->defaultSettings();
		$this->loadSettings($defaults);
	}

	/**
	 * get the default values for all 'wassup_settings'
	 * @access public
	 * @param none
	 * @return array
	 */
	function defaultSettings($dsetting="") {
		global $wpdb;
		$top10 = array(	"toplimit"=>"10",	//new in 1.8.3 - top stats list size
				"topsearch"=>"1",
				"topreferrer"=>"1",
				"toprequest"=>"1",
				"topbrowser"=>"1",
				"topos"=>"1",
				"toplocale"=>"0",
				"topvisitor"=>"0",
				"toppostid"=>"0",	//new in v1.8.3 - top article by post-id
				"topreferrer_exclude"=>"",
				"top_nospider"=>"0");	//new in v1.8.3 - exclude spiders from top stats
		$defaults = array(
			'wassup_active'		=>"1",
			'wassup_loggedin'	=>"1",
			'wassup_admin'		=>"1",
			'wassup_spider'		=>"1",
			'wassup_attack'		=>"1",
			'wassup_hack'		=>"1",
			'wassup_spamcheck'	=>"1",
        		'wassup_spam'		=>"1",
        		'wassup_refspam'	=>"1",
			'wassup_exclude'	=>"",
			'wassup_exclude_url'	=>"",
			'wassup_exclude_user'	=>"",
			'wassup_cache'		=>"1",
			'wassup_chart'		=>"1",
			'wassup_chart_type'	=>"2",
			'delete_auto'		=>"never",
        		'delete_filter'		=>"",
			'wassup_remind_mb'	=>"0",
			'wassup_remind_flag'	=>"0",
			'wassup_refresh'	=>"3",
			'wassup_userlevel'	=>"8",
			'wassup_screen_res'	=>"800",
			'wassup_default_type'	=>"everything",
			'wassup_default_spy_type'=>"everything",
			'wassup_default_limit'	=>"10",
			'wassup_dashboard_chart'=>"0",
			'wassup_geoip_map'	=>"0",
			'wassup_googlemaps_key'	=>"",
			'wassup_spy_speed'	=>"5000",
			'wassup_time_format'	=>"24",
			'wassup_time_period'	=>"1",
			'wassup_widget_title'	=>"Visitors Online",
			'wassup_widget_ulclass'	=>"links",
			'wassup_widget_loggedin'=>"1",
			'wassup_widget_comauth'	=>"1",
			'wassup_widget_search'	=>"1",
			'wassup_widget_searchlimit' =>"5",
			'wassup_widget_ref'	=>"1",
			'wassup_widget_reflimit'=>"5",
			'wassup_widget_topbr'	=>"1",
			'wassup_widget_topbrlimit'=>"5",
			'wassup_widget_topos'	=>"1",
			'wassup_widget_toposlimit'=>"5",
			'wassup_widget_chars'	=>"18",
			'wassup_alert_message'	=>"",
			'wassup_uninstall'	=>"0",
			'wassup_optimize'	=>current_time('timestamp'),
			'wassup_top10'	=>attribute_escape(serialize($top10)),
//					"topsearch"=>"1", - moved
//					"topreferrer"=>"1", - moved
//					"toprequest"=>"1", - moved
//					"topbrowser"=>"1", - moved
//					"topos"=>"1", - moved
//					"toplocale"=>"0", - moved
//					"topvisitor"=>"0", - moved
//					"topfeed"=>"0", - moved
//					"topcrawler"=>"0", - moved
//					"topreferrer_exclude"=>""))), - moved
			'whash' 	=>$this->get_wp_hash(),
			'wassup_version'=>"",
			'wassup_table'  =>$wpdb->prefix . "wassup",
			'wassup_dbengine'=>$this->getMySQLsetting("engine"));

		//never discard google maps api key with "reset-to-default"
		if (!empty($this->wassup_googlemaps_key)) {
			$defaults['wassup_googlemaps_key']= $this->wassup_googlemaps_key;
		}
		//never discard wassup_version' or 'wassup_table' with "reset-to-default"
		if (!empty($this->wassup_version)) {
			$defaults['wassup_version']= $this->wassup_version;
		}
		if (!empty($this->wassup_table)) {
			$defaults['wassup_table']= $this->wassup_table;
		}
		//New in 1.8.3: return a single default value when function argument given
		if (!empty($dsetting)) {
			if ($dsetting == "top10" || $dsetting == "wassup_top10" || $dsetting == "top_stats") {
				return ($top10);
			} elseif (!empty($defaults[$dsetting])) {
				return ($defaults[$dsetting]);
			} else {
				return (null);
			}
		} else { 
			return($defaults);
		};
	} //end defaultSettings

	//#Load class variables with array parameter or current options
	function loadSettings($settings=array()) {
		//# load class variables with settings parameter or load
		//#   wp_options if no parameter
		if (empty($settings) || count($settings) == 0) {
			$settings = $this->getSettings();
		}
		if (!empty($settings) && is_array($settings)) {
			$this->options2class($settings);
		} else {
			return false;
		}
		return true;
	} //end loadSettings

	/**
	 * Retrieve wassup settings from 'wp_options' plus...include new 
	 *  settings (with defaults) and omit deprecated settings.
	 * @access public (TODO: should be static)
	 * @param none
	 * @return array
	 * @since version 1.8
	 */
	function getSettings() {
		$current_opts = get_option('wassup_settings');
		$default_opts = $this->defaultSettings();
		$settings = array();
		if (!empty($current_opts) && is_array($current_opts)) {
			foreach ($default_opts as $skey => $defaultvalue) {
			   if (array_key_exists($skey,$current_opts)) {
			   	$settings[$skey] = $current_opts[$skey];
			   } else {
			   	$settings[$skey] = $defaultvalue;
			   }
			} //end foreach
		} else {
			$settings = $default_opts;
		}
		return $settings;
	} //end getSettings
	
	//#Save class variables to the Wordpress options table
	function saveSettings() {
		//#  convert class variables into an array and save using
		//#  Wordpress functions, "update_option" or "add_option"
		//#convert class into array...
		$settings_array = array();
		$obj = $this;
		foreach (array_keys(get_class_vars(get_class($obj))) as $k){
			if (is_array($obj->$k)) {
				//serialize any arrays within $obj
				if (count($obj->$k)>0) {
					$settings_array[$k] = attribute_escape(serialize($obj->$k));
				} else {
					$settings_array[$k] = "";
				}
			} else {
				$settings_array[$k] = "{$obj->$k}";
			}
		}
		//#save array to options table...
		$options_check = get_option('wassup_settings');
		if (!empty($options_check)) {
			update_option('wassup_settings', $settings_array);
		} else {
			add_option('wassup_settings', $settings_array, 'Options for WassUp');
		}
		return true;
	}

	function deleteSettings() {
		//#delete the contents of the options table...
		delete_option('wassup_settings');
	}

	//#Return an array containing all possible values of $key, a class 
	//#  variable name or the name of an input field. For use in form 
	//#  validation, etc.
	function getKeyOptions($key,$meta="",$selected="") {
		$key_options = array();
		$key_options_meta = array();
		$key_options_sql = array();
		$key_default = "";	//default value
		switch ($key) {
		case "wassup_screen_res":
			//"Options" setting
			$key_options = array("640","800","1024","1200");
			$key_options_meta = array("&nbsp;640",
				"&nbsp;800",
				"1024",
				"1200");
			$key_default = "800";
			break;
		case "wassup_userlevel":
			//"Options" setting
			$key_options = array("8","6","2");
			$key_options_meta = array(
				__("Administrator"),
				'&nbsp;'.__("Editor"),
				'&nbsp;'.__("Author"));
			break;
		case "wassup_chart_type":
			//"Options" setting
			$key_options = array("0","1","2");
			$key_options_meta = array(
				__("None - don't show chart","wassup"),
				__("One - 2 lines chart 1 axis","wassup"),
				__("Two - 2 lines chart 2 axes","wassup"));
			$key_default = "1";
			break;
		case "wassup_default_type":
		case "wassup_default_spy_type":
			//"Options" setting, Report and chart option
			$siteurl = get_option('home');
			$url = parse_url($siteurl);
			$sitedomain = $url['host'];

			$key_options = array("everything", 
				"spider", "nospider",
				"spam", "nospam",
				"nospamspider",
				"loggedin",
				"comauthor",
				"searchengine",
				"referrer");
			$key_options_meta = array(__("Everything","wassup"),
				__("Spider","wassup"),
				__("No spider","wassup"),
				__("Spam","wassup"),
				__("No Spam","wassup"),
				__("No Spam, No Spider","wassup"),
				__("Users logged in","wassup"),
				__("Comment authors","wassup"),
				__("Referrer from search engine","wassup"),
				__("Referrer from ext link","wassup"));
			$key_options_sql = array("",
				" AND spider!=''",
				" AND spider=''",
				" AND spam>0",
				" AND spam=0",
				" AND spam=0 AND spider=''",
				" AND username!=''",
				" AND comment_author!=''",
				" AND searchengine!='' AND search!=''",
				" AND referrer!='' AND referrer NOT LIKE 'http://".$sitedomain."%' AND referrer NOT LIKE 'http://www.".$sitedomain."%'"
				);
			break;
		case "wassup_default_limit":
			//"Options" setting, report and chart option
			$key_options = array("10","20","50","100");
			$key_options_meta = array("&nbsp;10",
				"&nbsp;20",
				"&nbsp;50",
				"100");
			break;
		case "delete_auto":
			//"Options" settings
			$key_options = array("never", 
					"-1 day",
					"-1 week", 
					"-2 weeks", 
					"-1 month", 
					"-3 months",
					"-6 months",
					"-1 year");
			$key_options_meta = array(
				__("Don't delete anything","wassup"),
				__("24 hours","wassup"),
				__("7 days","wassup"),
				__("2 weeks","wassup"),
				__("1 month","wassup"),
				__("3 months","wassup"),
				__("6 months","wassup"),
				__("1 year","wassup"));
			break;
		case "delete_filter":
			$key_options = array("all", "spider", "spam");
			$key_options_meta = array(
				__("All"),__("Spider"),__("Spam"));
			break;
		case "sort_group":
			//TODO add to dislay options in Main/details screen
			$key_options = array("IP","URL");
			$key_options_meta = array(
				__("IP Address","wassup"),
				__("URL Request","wassup"));
			break;
		case "wassup_time_period": 
			//"Details" report and chart option
			$key_options = array(".05",".25","1","7","14","30","90","180","365","0");
			$key_options_meta = array(
				__("1 hour"),
				__("6 hours"),
				__("24 hours"),
				__("7 days"),
				__("2 weeks"),
				__("1 month"),
				__("3 months"),
				__("6 months"),
				__("1 year"),
				__("all time"));
			$key_default = "2";
			break;
		default: 	//enable/disable is default
			$key_options =  array("1","0");
			$key_options_meta =  array("Enable","Disable");
		} //end switch
		if ($key_default == "") {
			$key_default = $key_options[0];
		}
		$return = "";
		if ($meta == "meta") {
			//return 1 item
			if ($selected!="") {
				$key = array_search($selected,$key_options);
				if ($key) {
					$return=$key_options_meta[$key];
				} elseif (!is_numeric($key_default)) {
					$key = array_search($key_default,$key_options);
					$return=$key_options_meta[$key];
				} else {
					$return=$key_options_meta[$key_default];
				}
			//return array of items
			} else {

				$return=$key_options_meta;
			}
			return $return;
		} elseif ($meta == "default") {
			return $key_default;
		} elseif ($meta == "sql") {
			if (!empty($key_options_sql)) {
			if ($selected!="") {
			//return 1 item
				$key = array_search($selected,$key_options);
				if ($key) {
					$return=$key_options_sql[$key];
				} elseif (!is_numeric($key_default)) {
					$key = array_search($key_default,$key_options);
					$return=$key_options_sql[$key];
				} else {
					$return=$key_options_sql[$key_default];
				}
			} else {
			//return array of items
				$return=$key_options_sql;
			}
			}
			return $return;
		} else {
			return $key_options;
		}
	} //end getKeyOptions

	//#generates <options> tags for use in a <select> form.  
	//#   $itemkey must a class variable name or a "key" name from 
	//#   'getKeyOptions' above.
	function showFormOptions ($itemkey,$selected="",$optionargs="") {
		$form_items =$this->getKeyOptions($itemkey);
		if (count($form_items) > 0) {
			$form_items_meta = $this->getKeyOptions($itemkey,"meta");
			if ($selected == "") { 
				if (isset($this->$itemkey)) {
					$selected = $this->$itemkey;
				} else { 
					$selected = $form_items[0];
				}
			}
			foreach ($form_items as $k => $option_item) {
	        		echo "\n\t\t".'<option value="'.$optionargs.$option_item.'"';
	        		if ($selected == $option_item) { echo ' SELECTED>'; }
				else { echo '>'; }
				echo $form_items_meta[$k].'&nbsp;&nbsp;</option>';
			}
		}
	} //end showFormOptions


//	//#Sets the class variable, wassup_savepath, with the given 
//	//#  value $savepath
//	function setSavepath($savepath="") {
//		$savepath = rtrim($savepath,"/");
//		$blogurl = rtrim(get_bloginfo('home'),"/");
//		if (!empty($savepath)) {
//			//remove site URL from path in case user entered it
//			if (strpos($savepath, $blogurl) === 0) {
//				$tmppath=substr($savepath,strlen($blogurl)+1);
//			} elseif (strpos($savepath,'/') === 0 && !$this->isWritableFolder($savepath)) {
//				$tmppath=substr($savepath,1);
//			} elseif (strpos($savepath,'./') === 0 ) {
//				$tmppath=substr($savepath,2);
//			} else { 
//				$tmppath = $savepath;
//			}
//			//append website root or home directory to relative paths...
//			if (preg_match('/^[a-zA-Z]/',$tmppath) > 0 || strpos($tmppath,'../') === 0) {
//				if (!empty($_ENV['DOCUMENT_ROOT'])) {
//					$tmppath = rtrim($_ENV['DOCUMENT_ROOT'],'/').'/'.$tmppath;
//				} elseif (!empty($_ENV['HOME'])) {
//					$tmppath = rtrim($_ENV['HOME'],'/').'/'.$tmppath;
//				}
//				if ($this->isWritableFolder($tmppath)) {
//					$savepath = $tmppath;
//				}
//			} 
//		}
//		$this->wassup_savepath = $savepath;
//	}

	/**
	 * Return true if the given directory path exists and is writable
	 * @access static
	 * @param string
	 * @return boolean
	 * @author Helene D.
	 */
	function isWritableFolder($folderpath="") {
		$folderpath=trim($folderpath);	//remove white spaces
		if (!empty($folderpath) && strpos($folderpath,'http://') !== 0 ) {
			if (file_exists($folderpath)) { 
				$testfile = rtrim($folderpath,"/")."/temp".time().'.txt';
				//#check that the directory is writable...
				if (@touch($testfile)) { unlink($testfile); }
				else { return false; }
			} else {
				return false;
			}
		} else {
			return false;
		}
		return true;
	}

	/**
	 * Return a MySQL system variable value or '' if variable is not set
	 * @access public
	 * @param string
	 * @return string
	 * @since 1.7.2
	 */
	function getMySQLsetting($mysql_var) {
		global $wpdb;
		$mysql_value = false;
		//default mysql_var request is "engine"
		if (empty($mysql_var) || $mysql_var == "engine") {
			$table_status = $wpdb->get_results("SHOW TABLE STATUS LIKE '{$this->wassup_table}'");
			foreach ($table_status as $fstatus) {
				if (isset($fstatus->Engine)) {
					$mysql_value = $fstatus->Engine;
					break 1;
				} elseif (isset($fstatus->Type)) {
					$mysql_value = $fstatus->Type;
					break 1;
				}
			}

		//get the timezone 
		} elseif ($mysql_var == "timezone") {
			$sql_timezone = false;
			$sql_sys_timezone="";
			$sql_vars = $wpdb->get_results("SHOW VARIABLES LIKE '%zone'");
			foreach ($sql_vars as $col) {
				if ($col->Variable_name == "system_time_zone") {
					$sql_sys_timezone = $col->Value;
				} elseif ($col->Variable_name == "time_zone") {
					$sql_timezone = $col->Value;
				} elseif ($col->Variable_name == "timezone") {
					$sql_timezone = $col->Value;
				}
			}
			if ($sql_timezone == "SYSTEM" || empty($sql_timezone)) {
				$host_timezone = $this->getHostTimezone();
				if (!empty($host_timezone)) {
					$sql_timezone=$host_timezone;
				} else {
					$sql_timezone = $sql_sys_timezone;
				}
			}
			if (!empty($sql_timezone)) {
				$mysql_value=$sql_timezone;
			}

		//get timezone offset for today's date.
		} elseif ($mysql_var == "tzoffset") {
			$tzoffset = $this->getTimezoneOffset();
			//Change offset from seconds into MySQL "[+-]hh:mm" format
			$mysql_value = $this->formatTimezoneOffset($tzoffset);

		//get a variable with exact parameter name
		} else { 
			$sql_vars = $wpdb->get_results("SHOW VARIABLES LIKE '{$mysql_var}'");
			if (!empty($sql_vars)) {
			foreach ($sql_vars as $col) {
				if ($col->Variable_name == $mysql_var) {
					$mysql_value = $col->Value;
					break 1;
				}
			}
			}
		}
		return $mysql_value;
	} //end function getMySQLsetting

	/**
	 * return MySQL timezone offset
	 * @access public
	 * @param none
	 * @return string
	 * @since 1.8
	 */
	function getTimezoneOffset() {
		global $wpdb;
		//calculate mysql timezone offset by converting MySQL's 
		//  NOW() to a timestamp and subtracting UTC current time
		//  from it. Note: conversion to timestamp is affected by
		//  PHP TZ setting, so remove that offset from calculation
		$tzoffset=false;
		$mysql_now = $wpdb->get_var("SELECT NOW() AS mysql_now");
		$now = ((int)(time()/1800))*1800; //truncate to 1/2 hour
		if ($mysql_now!="") {
			if (function_exists('date_timestamp_get')) {
				$mysql_dt = new DateTime($mysql_now);
				$mysql_time = $mysql_dt->getTimestamp();
				$adjust = $mysql_dt->getOffset();
			} else {
				$mysql_time = strtotime($mysql_now);
				$adjust = (int)date('Z');
			}
			$tzoffset = ((int)($mysql_time/1800))*1800 - $now;
			if (is_numeric($adjust)) $tzoffset += $adjust;
		}
		return $tzoffset;
	} //end getTimezoneOffset

	/**
	 * get timezone (and offset) directly the host server 
	 * @access static
	 * @param none
	 * @return array (timezone *string, offset *string)
	 * @since 1.8
	 */
	function getHostTimezone($getoffset=false) {
		$hostTimezone = __("Unknown");
		$nix_server="";
		//run *nix 'date' command to get offset from host server system
		//'date' function for timezone not supported on Windows
		if (defined('PHP_OS') && strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
			$nix_server = PHP_OS;
		} else {
			if (!empty($_SERVER['SERVER_SOFTWARE'])) {
				$php_os = $_SERVER['SERVER_SOFTWARE'];
			} elseif (function_exists('apache_get_version')) { 
				$php_os = apache_get_version();
			}
			if (preg_match('/(nix|bsd|os\s?x|ux|darwin|sun)/i',$php_os)>0) {
				$nix_server = $php_os;
			}
		}
		if (!empty($nix_server)) {
			if ($getoffset) {
				$hostTZ = @exec('date +"%Z|%z"');
				//in case exec is disabled
				if (!empty($hostTZ) && strpos($hostTZ,'|')!==false) {
					$hostTimezone = explode('|',$hostTZ);
					$hostTimezone[1] = substr($hostTimezone[1],0,3);
				}
			} else {
				$hostTZ = @exec('date +"%Z"');
				//in case exec is disabled
				if (!empty($hostTZ)) {
					$hostTimezone = $hostTZ;
				}
			}
		}
		return $hostTimezone;
	} //end getHostTimezone

	/**
	 * Change offset from seconds or hours into MySQL "[+-]hh:mm" format
	 * @access static
	 * @param string
	 * @return string
	 * @since 1.8
	 */
	function formatTimezoneOffset($offset=false) {
		$tzoffset = false;
		if (preg_match('/^[\-+]?[0-9\.]+$/',$offset)>0) { //must be a number
			//convert seconds to hours:min
			$n=false;
			if ($offset > 12 || $offset < -12) {
				$noffset = $offset/3600;
			} else {
				$noffset = $offset;
			}
			$n = strpos($noffset,'.');
			if ($n !== false) {
				$offset_hrs = substr($noffset,0,$n);
				$offset_min = (int)substr($noffset,$n+1)*6;
			} else {
				$offset_hrs = $noffset;
				$offset_min = 0;
			}
			if ($offset < 0) {
				$tzoffset = sprintf("%d:%02d",$offset_hrs, $offset_min);
			} else {
				$tzoffset = "+".sprintf("%d:%02d",$offset_hrs, $offset_min);
			}
		} elseif (preg_match('/^([\-+])?(\d{1,2})?\:(\d{2})/',$offset,$match)>0) {
			if (empty($match[2])) $match[2] = "0";
			if (!empty($match[1]) && $match[1]=="-") {
				$tzoffset = "-".sprintf("%d:%02d",$match[2], $match[3]);
			} else {
				$tzoffset = "+".sprintf("%d:%02d",$match[2], $match[3]);
			}
		}
		return $tzoffset;
	} //end formatTimezoneOffset

	//#Set a wp_hash value and return it
	function get_wp_hash($hashkey="") {
		$wassuphash = "";
		if (function_exists('wp_hash')) { 
			if (empty($hashkey)) {
				if (defined('SECRET_KEY')) { 
					$hashkey = SECRET_KEY;
				} else { 
					$hashkey = "wassup";
				}
			}
			$wassuphash = wp_hash($hashkey);
		}
		return $wassuphash;
	} //end function get_wp_hash

	/**
	 * Convert associative array to this class object
	 * @access private
	 * @param associative-array
	 * @return boolean
	 * @since 1.8
	 */
	function options2class($options_array) { 
		if (!empty($options_array) && is_array($options_array)) {
		foreach ($options_array as $o_key => $o_value) {
			if (isset($this->$o_key)) { //returns false for null values
				$this->$o_key = $o_value;
			} elseif (function_exists('property_exists')) {	//PHP 5.1+ function
				if (property_exists($this,$o_key)) {
					$this->$o_key = $o_value;
				}
			} elseif (array_key_exists($o_key,$this)) { //valid for objects in PHP 4.0.7 thru 5.2 only
				$this->$o_key = $o_value;
			}
		} //end foreach
		} else {
			return false;
		}
		return true;
	} //end options2class

	//#show a system message in Wassup Admin menus
	function showMessage($message="") {
		if (empty($message) && !empty($this->wassup_alert_message)) {
			$message = $this->wassup_alert_message;
		}
		//#check for error message/notice message
		if (stristr($message,"error") !== FALSE || stristr($message,"problem") !== FALSE) {
			echo '<div id="wassup-error" class="fade error" style="color:#d00;padding:10px;">'.$message;
			//print_r($this); // #debug
			echo '</div>'."\n";
		} else {
			echo '<div id="wassup-message" class="fade updated" style="color:#040;padding:10px;">'.$message;
			//print_r($this); // #debug
			echo '</div>'."\n";
		}
	} //end showMessage

	function showError($message="") {
		$this->showMessage($message);
	}
} //end class wassupOptions
} //end if !class_exists
?>
