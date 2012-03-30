<?php
/**
 * Trends Dao.
 * 
 * @author dligthart <info@daveligthart.com>
 * @package wp-stats-dashboard
 * @subpackage dao
 * @version 0.2
 */
class WPSDTrendsDao {
	
	var $tablename;
	
	/**
	 * WPSDTrendsDao function.
	 * 
	 * @access public
	 * @return void
	 */
	function WPSDTrendsDao() {
		
		global $wpdb;
		
		$db_ver = get_option('wpsd_db_version');
		
		$this->tablename = $wpdb->prefix . 'wpsd_trends'; 
	}
	
	/**
	 * Insert or update.
	 * 
	 * @param $type
	 * @param $stats
	 * @return integer id
	 */
	function update($type, $stats) {
		global $wpdb;

		$date = date('Y-m-d 00:00:00');
		
		$id = $wpdb->get_var("SELECT wpsd_trends_id as id FROM {$this->tablename} where wpsd_trends_type = {$type} and wpsd_trends_date = '{$date}'");
		
		$cur_stats = $wpdb->get_var("SELECT wpsd_trends_stats as stats FROM {$this->tablename} where wpsd_trends_type = {$type} and wpsd_trends_date = '{$date}'");

		if($id <=0) {
		
			$this->insertStats($stats, $type, $date);	
		}
		else {
			
			if($stats > 0 && $cur_stats < $stats) { 
			
				$this->updateStats($id, $stats, $type, $date);			
			}
		}
		
		return $id;
	}
	
	/**
	 * Insert stats by type and date.
	 * 
	 * @param $stats
	 * @param $type
	 * @param $date
	 * @return integer new id
	 */
	function insertStats($stats = 0, $type = 0, $date = null ) {
		global $wpdb;
		
		if($stats > -1 && $type > -1 && $date != null) {

			$wpdb->insert( $this->tablename, 
				array( 
					'wpsd_trends_type'  => $type, 
					'wpsd_trends_date'  => $date, 
					'wpsd_trends_stats' => $stats ), 
				array( '%d', '%s', '%d'));
				
			return $wpdb->insert_id;
		}
		
		return false;
	}
	
	/**
	 * Update stats by id.
	 * 
	 * @param $id
	 * @param $stats
	 * @param $type
	 * @param $date
	 * @return unknown_type
	 */
	function updateStats($id = 0, $stats = 0, $type = 0, $date = null ) {
		global $wpdb;
		
		if($id > 0 && $stats > -1 && $type > -1 && $date != null) {
						
			$wpdb->update( $this->tablename, 
					array( 
						'wpsd_trends_type'  => $type, 
						'wpsd_trends_date'  => $date, 
						'wpsd_trends_stats' => $stats ),
					array('wpsd_trends_id' => $id), 
					array( '%d', '%s', '%s')
			);
			
			return true;	
		}
		
		return false;
	}
	
	/**
	 * getStats function.
	 * 
	 * @access public
	 * @param mixed $type
	 * @return void
	 */
	function getStats($type) {
		global $wpdb;
		
		$rows = false;
		
		if($type > -1) {

			$rows = $wpdb->get_results( "SELECT * FROM {$this->tablename} where wpsd_trends_type = {$type} order by wpsd_trends_date desc limit 30" );
		}
		
		return $rows;
	}
}
?>