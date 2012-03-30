<?php
/**
 * @package WassUP
 * @subpackage upgrade.php module
 */
/**
 * Contains functions to create and update the tables required for WassUp
 *   install and upgrade. These functions are loaded only when plugin is 
 *   activated through the 'wassup_install' hook function.
 * @since version 1.8
 * @author Helene D.
 */

/**
 * Table install manager function that calls either 'wCreateTable' or 
 * 'wUpdateTable' depending on whether this is a new install or an upgrade.
 * @param none
 * @return boolean
 */ 
function wassup_tableInstaller() {
	global $wpdb, $wassupversion, $wassup_options;

	//#wassup table names
	$wassup_table = (!empty($wassup_options->wassup_table)? $wassup_options->wassup_table : $wpdb->prefix . "wassup");
	$wassup_tmp_table = $wassup_table."_tmp";
	$wassup_meta_table = $wassup_table."_meta";
	$wcharset=true;
	$wsuccess=false;
	//$wpdb->show_errors = true;

	//CREATE/UPGRADE table
	if ($wpdb->get_var("SHOW TABLES LIKE '{$wassup_table}'") == $wassup_table) { 
		$wcharset=false;
		$wsuccess=wUpdateTable(); //<== wassup_tmp is added here, if missing
	} else {
		if (wCreateTable()) {	//1st attempt
			$wcharset=true;
		}
		//2nd attempt: no character set in table
		if ($wpdb->get_var("SHOW TABLES LIKE '{$wassup_table}'") != $wassup_table) { 
			$wcharset=false;
			wCreateTable("",$wcharset);
		}

	}

	//check that install was successful, issue warnings, start tracker
	if ($wsuccess || $wpdb->get_var("SHOW TABLES LIKE '{$wassup_table}' ") == $wassup_table) {
		$wsuccess=true;
		//double-check that temp and meta were created
		if ($wpdb->get_var("SHOW TABLES LIKE '{$wassup_tmp_table}' ") != $wassup_tmp_table) {
			wCreateTable($wassup_tmp_table,$wcharset);
		}
		if ($wpdb->get_var("SHOW TABLES LIKE '{$wassup_meta_table}' ") != $wassup_meta_table) {
			wCreateTable($wassup_meta_table,$wcharset);
		}
	}
	//$wpdb->show_errors = false;
	return($wsuccess);
} //#end function wassup_tableInstaller

/**
 * Create or upgrade wassup tables using Wordpress' 'dbDelta' function and
 * include a first record for new wp_wassup table.
 * @param (2) string,boolean
 * @return boolean
 */
function wCreateTable($wtable="",$withcharset=true) {
	global $wpdb, $current_user, $wp_version, $wassupversion, $wassup_options;

	$wassup_table = (!empty($wassup_options->wassup_table))? $wassup_options->wassup_table: $wpdb->prefix . "wassup";
	$wassup_tmp_table = $wassup_table."_tmp";
	$wassup_meta_table = $wassup_table."_meta";
	if (empty($wtable)) $wtable_name = $wassup_table;
	else $wtable_name = $wtable;
	$is_new_table = false;

	//table builds should not be interrupted, so run in background in case of browser timeout
	ignore_user_abort(1);

	//use Wordpress' "dbDelta" to create and update table structure
	if (!function_exists('dbDelta')) {
	if (file_exists(ABSPATH . 'wp-admin/includes/upgrade.php')) {
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	} else {	//deprecated since 2.5
        	require_once( ABSPATH.'wp-admin/upgrade-functions.php');
	}
	}
	//...Set default character set and collation (on new table)
	$charset_collate = '';
   	//Add charset on new table only
	if ($wpdb->get_var("SHOW TABLES LIKE '{$wtable_name}'") == $wtable_name) {
		$withcharset = false;
	} else {
		$is_new_table = true;
	}

	//#don't do charset/collation when < MySQL 4.1 or when DB_CHARSET is undefined
	//Note: it is possible that table default charset !== WP database charset on preexisting MySQL database and tables (from WP2.3 or less) because old charsets persist after upgrades
	if ($withcharset && version_compare(mysql_get_server_info(),'4.1.0','>') && defined('DB_CHARSET') && !empty($wpdb->charset)) {
		$charset_collate = 'DEFAULT CHARACTER SET '.$wpdb->charset;
		//add collate only when charset is specified
		if (!empty($wpdb->collate)) {
			$charset_collate .= ' COLLATE '.$wpdb->collate;
		}
	}

	//wassup table structure
	if ($wtable_name == $wassup_table || $wtable_name == $wassup_tmp_table) {
		$sql_createtable = "CREATE TABLE $wtable_name (
  `id` mediumint(9) unsigned NOT NULL auto_increment,
  `wassup_id` varchar(60) NOT NULL,
  `timestamp` varchar(20) NOT NULL,
  `ip` varchar(50) default NULL,
  `hostname` varchar(150) default NULL,
  `urlrequested` text,
  `agent` varchar(255) default NULL,
  `referrer` text,
  `search` varchar(255) default NULL,
  `searchpage` int(11) unsigned default '0',
  `os` varchar(15) default NULL,
  `browser` varchar(50) default NULL,
  `language` varchar(5) default NULL,
  `screen_res` varchar(15) default NULL,
  `searchengine` varchar(25) default NULL,
  `spider` varchar(50) default NULL,
  `feed` varchar(50) default NULL,
  `username` varchar(50) default NULL,
  `comment_author` varchar(50) default NULL,
  `spam` varchar(5) default '0',
  `url_wpid` varchar(50) default NULL,
  UNIQUE KEY `id` (`id`),
  KEY `idx_wassup` (`wassup_id`(32),`timestamp`),
  INDEX (`os`),
  INDEX (`browser`),
  INDEX `timestamp` (`timestamp`)) {$charset_collate};";
	//Note: index (username,ip) was removed because of problems with
	//  non-romanic language display
	//Since v1.8: increased ip width to 50 for ipv6 support

	//...Include a first record if new table (not temp table)
	$sql_firstrecord = '';
	if ($wtable_name == $wassup_table && $is_new_table) {
		if (!class_exists('UADetector'))
			include_once (dirname(__FILE__) . '/lib/uadetector.class.php');
		$ua = new UADetector;
		if (empty($current_user->user_login)) get_currentuserinfo();
		$logged_user = (!empty($current_user->user_login)? $current_user->user_login: "");
		if (isset($_COOKIE['wassup_screen_res'])) {
			$screen_res = attribute_escape(trim($_COOKIE['wassup_screen_res']));
			if ($screen_res == "x") $screen_res = "";
		}
		$currentLocale = get_locale();
		$locale = preg_replace('/^[a-z]{2}_/','',strtolower($currentLocale));
		$sql_firstrecord = sprintf("INSERT INTO $wassup_table (`wassup_id`, `timestamp`, `ip`, `hostname`, `urlrequested`, `agent`, `referrer`, `search`, `searchpage`, `os`, `browser`, `language`, `screen_res`, `searchengine`, `spider`, `feed`, `username`, `comment_author`, `spam`) VALUES ('%032s','%s','%s','%s','%s','%s','%s','','','%s','%s','%s','%s','','','','%s','','0')",
			1, current_time('timestamp'),
			'127.0.0.1', 'localhost', 
			'[404] '.__('Welcome to WassUP','wassup'), 
			$ua->agent . ' WassUp/'.$wassupversion.' (http://www.wpwp.org)', 
			'http://www.wpwp.org', $ua->os, 
			trim($ua->name.' '.$ua->majorVersion($ua->version)),
			$locale, $screen_res, $logged_user);
	} // end if wassup && is_new_table

	//...create/upgrade wassup table

	//New in 1.8.3: Don't use Wordpress' "dbdelta" function on
	//  pre-existing "wp_wassup" table because "dbDelta" fails to 
	//  upgrade wp_wassup's large table structure in Wordpress 3.1+ 
	//  (throws MySQL ALTER TABLE error). 
	if ($wtable_name != $wassup_table) {
		$result = dbDelta($sql_createtable);
	} elseif (!empty($sql_firstrecord)) {
		$result = dbDelta(array($sql_createtable,$sql_firstrecord));
	} 
	//$wpdb->print_error(); //debug

	//...return 'true' if table created successfully, false otherwise
	$retvalue=true;
	if ($wpdb->get_var("SHOW TABLES LIKE '{$wtable_name}'") != $wtable_name) {

		$retvalue=false;
	} else {
		if ($wtable == "" && version_compare(mysql_get_server_info(),'4.1.0','>')) {
			//'CREATE TABLE LIKE' syntax not supported in MySQL 4.1 or less
			$wupgrade = dbDelta("CREATE TABLE $wassup_tmp_table LIKE {$wassup_table}");
			//$wpdb->print_error(); //debug
		}
	}
	} //end if wassup_table
	
	//Since v1.8: "meta" table to extend wassup. Used as a temporary
	// data cache and can be used to capture additional visitor data
	if ($wtable == "") $wtable_name = $wassup_meta_table;
	if ($wtable_name == $wassup_meta_table) {

	// Wassup Meta Table Structure:
	// `wassup_key` can be either a foreign key for wp_wassup (contains
	//  	data from an indexed column) or can be text, ex: for geoip 
	//	it would contain the wp_wassup key, `ip`.
	// `meta_key` is an abbreviation descriptive of the value stored, 
	//  	ex: 'geoip','chart'.
	// `meta_value` is the value stored. Can be text, number or a 
	//  	serialized array.
	// `meta_expire` is a timestamp that is an expiration date in unix
	//  	timestamp format...for temporary/cache-only records.
	$sql_create_meta = "CREATE TABLE `$wassup_meta_table` (
  `meta_id` integer(15) unsigned auto_increment,
  `wassup_key` varchar(150) NOT NULL,
  `meta_key` varchar(80) NOT NULL,
  `meta_value` longtext,
  `meta_expire` integer(10) unsigned default '0',
  UNIQUE KEY meta_id (`meta_id`),
  INDEX (`wassup_key`),
  INDEX (`meta_key`)) {$charset_collate}; ";

	//create/upgrade wassup table
	$result = dbDelta($sql_create_meta);
	//$wpdb->print_error(); //debug

	//...return 'false' if table was not created 
	if ($wtable == $wassup_meta_table && $wpdb->get_var("SHOW TABLES LIKE '{$wtable_name}'") != $wtable_name) {
		$retvalue=false;
	}
	} //end if wassup_meta_table 

	return($retvalue);
	//#TODO: 
	// 1. create stored procedure that selects records by timestamp
	// 2. create table views, 'wassup_hourly', 'wassup_weekly', 'wassup_monthly' as subsets of 'wp_wassup' for hourly hits, daily hits, weekly hits...
	//    Note: views are only available in MySQL 5.0.1+
} //end function wCreateTable

/**
 * Upgrade existing wassup tables and row content, drop and recreate 
 * 'wassup_tmp' table, and optimize 'wp_wassup' table by dropping and 
 * rebuilding all indices except 'id'. 
 * Note: Wordpress' 'dbDelta' function is used (in "wCreateTable") to 
 * upgrade individual columns and to recreate dropped indices.
 * @param none
 * @return boolean
 */
function wUpdateTable() {
	global $wpdb, $wassup_options, $wassupversion;

	$wassup_table = (!empty($wassup_options->wassup_table))? $wassup_options->wassup_table: $wpdb->prefix . "wassup";
	$wassup_tmp_table = $wassup_table."_tmp";
	$wassup_meta_table = $wassup_table."_meta";
	$wsuccess=true;

	//queue table content upgrades
	//For upgrade to v1.8: fix incorrect OS "win2008" (="win7")
	$upgrade_sql = array();
	if (!empty($wassup_options->wassup_version) && version_compare($wassup_options->wassup_version,"1.8","<")){
		$upd_timestamp = strtotime("1 January 2009");
		$upgrade_sql[] = "UPDATE $wassup_table SET `os`='win7' WHERE `os`='win2008' AND `timestamp`>$upd_timestamp;";
		$upgrade_sql[] = "UPDATE $wassup_table SET `os`='win7 x64' WHERE `os`='win2008 x64' AND `timestamp`>$upd_timestamp;";
	}

	//increase mysql session timeout to 3 minutes for upgrade 
	$wpdb->query("SET wait_timeout = 180");

	//do table content upgrade - after table upgrade to avoid lockouts
	//if (!empty($upgrade_sql)) {
	//	wassup_scheduled_dbtask($upgrade_sql);
	//}

	// For all upgrades: Drop and re-create all indices except 'id' and
	// 'meta_id' on 'wp_wassup' and 'wp_wassup_meta' tables
	$wtables = array("$wassup_table", "$wassup_meta_table");
	foreach ($wtables AS $wtbl) {
		//# get list of all wassup indices
		$wqryresult = mysql_query("SHOW INDEX FROM `{$wtbl}`");
		if ($wqryresult) { 
			$wrow_count = mysql_num_rows($wqryresult); 
		} else {
			$wrow_count = 0;
		}
		//# get the names of all indices
		$idx_names = array();
		$prev_key = "";	//names listed multiples times per columns in key
		if ($wrow_count > 1) {
		while ($wrow = mysql_fetch_array($wqryresult,MYSQL_ASSOC)) {
			if ($wrow["Column_name"]!= "id" && $wrow["Column_name"]!= "meta_id" && $wrow["Key_name"]!= $prev_key) {
				$idx_names[] = $wrow["Key_name"];
			}
			$prev_key = $wrow["Key_name"];
		} //end while
		} //end if wrow_count
		mysql_free_result($wqryresult);
		//# drop all the indices in $idx_names and drop temp table...
		//drop indices
		foreach ($idx_names AS $idx_drop) {
			mysql_query("DROP INDEX $idx_drop ON `{$wtbl}`");
		}
		unset ($wrow, $wrow_count, $prev_key, $idx_names);
	}

	//NOTE: All column updates replaced by single call to 'wcreateTable' function which uses 'dbDelta' to update table structure

	////## For all upgrades - redundant code
	////# Drop and re-create all indices except 'id'
	////Get list of all wp_wassup indices...
	////$idx_cols = $wpdb->get_col("SHOW INDEX FROM $wassup_table","Column_name"); //doesn't work
	//$qryresult = mysql_query("SHOW INDEX FROM {$wassup_table}");
	//if ($qryresult) { 
	//	$row_count = mysql_num_rows($qryresult); 
	//} else {
	//	$row_count = 0;
	//}
	////store names of all indices in an array...
	//$idx_names = array();
	//$prev_key = "";	//names listed multiples times per columns in key
	//if ($row_count > 1) {
	//	while ($row = mysql_fetch_array($qryresult,MYSQL_ASSOC)) {
	//		if ($row["Column_name"] != "id" && $row["Key_name"] != $prev_key) {
	//			$idx_names[] = $row["Key_name"];
	//		}
	//		$prev_key = $row["Key_name"];
	//	} //end while
	//} //end if row_count
	//mysql_free_result($qryresult);
	////drop indices in array...
	//foreach ($idx_names AS $idx_drop) {
	//	mysql_query("DROP INDEX $idx_drop ON {$wassup_table}");
	//}
	// drop wp_wassup_tmp table...
	//$wpdb->query("DROP TABLE IF EXISTS $table_tmp_name"); //incorrectly causes an activation error in Wordpress
	mysql_query("DROP TABLE IF EXISTS `{$wassup_tmp_table}`"); 

	//call 'wCreateTable' to update tables structure and rebuild indices using wordpress' 'dbdelta' function...
	//NOTE: 'wcreateTable' no longer upgrades "wp_wassup" table since v.1.8.3
	$wsuccess=wCreateTable();

	//New in 1.8.3: 'wCreateTable' and 'dbdelta' workaround code
	// Since Wordpress 3.1, Wordpress' "dbDelta" function fails to 
	// upgrade "wp_wassup" table structure (throws MySQL ALTER TABLE 
	// error). To work around this problem, restored column-by-column 
	// upgrades from earlier versions of WassUp (pre 1.7) and restored 
	// manual index rebuilds.

	//upgrades for version 1.7 or less
	if (empty($wassup_options->wassup_version) OR version_compare($wassup_options->wassup_version,"1.7","<")) {
		// Upgrade from version < 1.3.9 - add 'spam' field to wassup table
		if ($wpdb->get_var("SHOW COLUMNS FROM $wassup_table LIKE 'spam'") == "") {
			$wpdb->query("ALTER TABLE {$wassup_table} ADD COLUMN spam VARCHAR(5) DEFAULT '0'");
		}

		// Upgrade from version <= 1.5.1 - increase 'wassup_id' size
		$wassup_col = $wpdb->get_results("SHOW COLUMNS FROM $wassup_table LIKE 'wassup_id'");
		foreach ($wassup_col as $wID) {
			if ($wID->Type != "varchar(60)") {
				$wpdb->query("ALTER TABLE {$wassup_table} CHANGE COLUMN wassup_id wassup_id varchar(60) DEFAULT NULL");
			}
		}
		// - increase size of 'searchengine' and 'spider' fields
		$col_size = $wpdb->get_results("SHOW COLUMNS FROM $wassup_table LIKE 'searchengine'");
		foreach ($col_size as $wCol) {
			if ($wCol->Type != "varchar(25)") {
				$wpdb->query("ALTER TABLE {$wassup_table} CHANGE COLUMN searchengine searchengine varchar(25) DEFAULT NULL");
			}
		}
		$col_size = $wpdb->get_results("SHOW COLUMNS FROM $wassup_table LIKE 'spider'");
		foreach ($col_size as $wCol) {
			if ($wCol->Type != "varchar(50)") {
				$wpdb->query("ALTER TABLE {$wassup_table} CHANGE COLUMN spider spider varchar(50) DEFAULT NULL");
			}
		}
	} //end if wassup_version
	//upgrades for versions 1.7 thru 1.8.2
	if (empty($wassup_options->wassup_version) OR version_compare($wassup_options->wassup_version,"1.8.3","<")) {
		//Since v1.8: add new field 'url_wpid' for post_id tracking
		if ($wpdb->get_var("SHOW COLUMNS FROM $wassup_table LIKE 'url_wpid'") == "") {
			$wpdb->query("ALTER TABLE {$wassup_table} ADD COLUMN url_wpid varchar(50) DEFAULT NULL");
		}
		//Since v1.8: increase size of 'ip' field for IPv6 addresses
		$wpdb->query("ALTER TABLE {$wassup_table} CHANGE COLUMN ip `ip` varchar(50) DEFAULT NULL");
	}
	//TODO: show an upgrade warning error when table structure is not updated
	//rebuild indices on 'wp_wassup' - this also optimizes
	//...could take a long time, so run in background in case of timeout
	ignore_user_abort(1);
	$wpdb->query("ALTER TABLE {$wassup_table} ADD KEY idx_wassup (wassup_id(32),`timestamp`)");
	$wpdb->query("ALTER TABLE {$wassup_table} ADD INDEX (os)");
	$wpdb->query("ALTER TABLE {$wassup_table} ADD INDEX (browser)");
	$wpdb->query("ALTER TABLE {$wassup_table} ADD INDEX (`timestamp`)");

	//build secondary tables, in case upgrade failed in 'wCreateTable'
	if ($wpdb->get_var("SHOW TABLES LIKE '{$wassup_tmp_table}'") != $wassup_tmp_table) { 
		wCreateTable($wassup_tmp_table,false);
	}
	if ($wpdb->get_var("SHOW TABLES LIKE '{$wassup_meta_table}'") != $wassup_meta_table) { 
		wCreateTable($wassup_meta_table,false);
	}

	if ($wsuccess || $wpdb->get_var("SHOW TABLES LIKE '{$wassup_table}'") == $wassup_table) { 
		$wsuccess=true;
	}

	//lastly do table content upgrade, if any
	if ($wsuccess) {
		if (!empty($upgrade_sql)) {
			wassup_scheduled_dbtask($upgrade_sql);
		}
	}
	return($wsuccess);
} //end function wUpdateTable

/**
 * Install function to inspect Wordpress for certain settings that must
 *  exist (or not exist) for WassUp to run properly.
 *  ...currently only "wp_footer" function and WP_CACHE are checked.
 * @param string
 * @return boolean
 * @since v1.8
 */
function wassup_compatCheck($item_to_check) {
	global $wassup_options;
	//wp-footer: test for wp_footer() function in file 'footer.php'
	if ($item_to_check == "wp_footer") {
		$result=true;
		$footer_file =  STYLESHEETPATH."/footer.php";
		if (!file_exists($footer_file)) $footer_file = TEMPLATEPATH."/footer.php";
		if (file_exists($footer_file)) {
			$footer = file_get_contents($footer_file);
			//Note: if 'wp_footer()' is commented-out in template code, it will still match as true in test below
			if (stristr($footer,'wp_footer(')!==false || stristr($footer,'wp_footer (')!==false) $result=true;
			else $result=false;
		} else {
			$result=false;
		}
	} elseif ($item_to_check == "WP_CACHE") {
		$result=false;
		if (defined('WP_CACHE') && WP_CACHE!==false && trim(WP_CACHE)!=="") {
			$result=true;
		}
	} else {
		$result=true; //default
	}
	return $result;
} //end wInstall_check
