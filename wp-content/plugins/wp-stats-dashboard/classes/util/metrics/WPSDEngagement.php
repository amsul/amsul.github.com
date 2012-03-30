<?php
/**
 * WPSDEngagement.
 * @author dligthart <info@daveligthart.com>
 * @version 0.2
 * @package wp-stats-dashboard
 */
class WPSDEngagement extends WPSDStats {

	function WPSDEngagement() {
		parent::WPSDStats();	
	}

	function getEngagement(){
		global $wpdb;
	
		if($this->isOutdated()) {
			
			$numcomms = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->comments} WHERE comment_approved = '1'");

			if (0 < $numcomms) $numcomms = number_format($numcomms);
			
			$this->set_cache('engagement', $numcomms);
		} 
		else {
			
			$numcomms = $this->get_cache('engagement');	
		}
		
		return $numcomms;
	}
	
	function getPingback() {
		global $wpdb;
		
		if($this->isOutdated()) {
				
			foreach ( array('', 'pingback', 'trackback') as $type ) { 
	            $count = $wpdb->get_var("SELECT COUNT( comment_ID )FROM {$wpdb->posts} 
	            LEFT JOIN {$wpdb->comments} ON ( comment_post_ID = ID AND comment_approved = '1' AND comment_type='{$type}' ) 
	            WHERE post_status = 'publish' GROUP BY ID"); 

	            $this->set_cache('engagement_' . $type, $count);
			}
		} 
		
		return $this->get_cache('engagement_pingback');
	}
	
	function getTrackback() {
		
		return $this->get_cache('engagement_trackback');		
	}
}
?>