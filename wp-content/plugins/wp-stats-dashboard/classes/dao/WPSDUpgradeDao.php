<?php
/**
 * Upgrade DAO.
 *
 * @author dligthart <info@daveligthart.com>
 * @package wp-stats-dashboard
 * @subpackage dao
 * @version 0.2
 */
class WPSDUpgradeDao {

	/**
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	function __construct() {
	
	}
	
	/**
	 * WPSDUpgradeDao function. Constructor.
	 * 
	 * @access public
	 * @return void
	 */
	function WPSDUpgradeDao() {
		
	}
	
	/**
	 * install function. Install database.
	 * 
	 * @access public
	 * @return array
	 */
	function install() {
		
		global $wpdb;
		
		global $wp_version;

		$forUpdate = array();

		if($this->isInstalled()) {
			
			$this->upgrade();

		} else {

			if (version_compare($wp_version, '2.3', '>='))	{
				
				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				
			} else {
				
				require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
			}

			// add charset & collate like wp core
			$charset_collate = '';
			
			if ( version_compare(mysql_get_server_info(), '4.1.0', '>=') ) {
				
				if ( ! empty($wpdb->charset) )
					$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
				
				if ( ! empty($wpdb->collate) )
					$charset_collate .= " COLLATE $wpdb->collate";
			}

			$tn = $wpdb->prefix . 'wpsd_trends';

			$create_wpsd_trends = 'CREATE TABLE '. $tn . '(
  wpsd_trends_id BIGINT NOT NULL AUTO_INCREMENT ,
  wpsd_trends_type INT NULL ,
  wpsd_trends_date DATETIME NULL ,
  wpsd_trends_stats FLOAT NULL ,
  PRIMARY KEY (wpsd_trends_id) )'. $charset_collate . '; ';
		
			$queries = $create_wpsd_trends;
			
			$forUpdate = dbDelta($queries);
				
			if(defined('WPSD_DB_VERSION')){

				add_option('wpsd_db_version', WPSD_DB_VERSION);
			}
		}

		return $forUpdate;
	}
	
	/**
	 * upgrade function. Upgrade database.
	 * 
	 * @access public
	 * @return void
	 */
	function upgrade() {
		global $wpdb;
		global $wp_version;

		if(defined('WPSD_DB_VERSION')) {

			$installed_ver = get_option('wpsd_db_version');
		
			if('0.1' == $installed_ver) {
				
				$old = 'wpsd_trends';
			
				$new = $wpdb->prefix . 'wpsd_trends';
			
				$sql = "RENAME TABLE {$old} TO {$new}";
				
				$wpdb->query($sql);
				
				$this->updateDBVersion();
			}
		}
	}
	
	/**
	 * isInstalled function. Check if installed.
	 * 
	 * @access public
	 * @return boolean
	 */
	function isInstalled() {
		global $wpdb;
		global $wp_version;
		
		$plugin_table_name = $wpdb->prefix . 'wpsd_trends';
		
		if('0.1' == get_option('wpsd_db_version')) {
			
			$plugin_table_name = 'wpsd_trends';
		}
			
		return ($wpdb->get_var('SHOW TABLES LIKE "'.$plugin_table_name.'" ') == $plugin_table_name);
	}

	/**
	 * updateDBVersion function. Update Database Version.
	 * 
	 * @access public
	 * @return void
	 */
	function updateDBVersion() {
		
		if(defined('WPSD_DB_VERSION')){

			update_option("wpsd_db_version", WPSD_DB_VERSION);
		}
	}
}
?>