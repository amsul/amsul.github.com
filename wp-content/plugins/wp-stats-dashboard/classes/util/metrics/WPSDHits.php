<?php 
/**
 * WPSDHits.
 * @author dligthart <info@daveligthart.com>
 * @version 0.2
 * @package wp-stats-dashboard
 */
class WPSDHits extends WPSDStats {
	
	/**
	 * WPSDHits.
	 */
	function WPSDHits() {
			
		parent::WPSDStats();
		
		if($this->isOutdated()) {
			
			$this->set();
		}
		else {
			
			$this->set_cached();
		}
	}
	
	/**
	 * Get total hits.
	 * 
	 * @access private
	 * @return integer total hits
	 */
	function getTotalHits() {
				
		$arr = wpsd_get_chart_xy();
		
		if(null == $arr) return 0;
		
		$res = $arr[1][count($arr[1]) - 1];
		
		if(null == $res) return 0;
		
		return $res;
	}
	
	/**
	 * Set data.
	 */
	function set() {
		
		$this->values['views'] = $this->getTotalHits();
		
		$this->values['alltime_views'] = wpsd_get_all_time_views();
				
		$this->set_cache('total_hits', $this->values['views']);
		
		$this->set_cache('alltime_hits', $this->values['alltime_views']);
		
	}
	
	/**
	 * Set cached.
	 */
	function set_cached() {
		
		$this->values['views'] = $this->get_cache('total_hits');
		
		$this->values['alltime_views'] = $this->get_cache('alltime_hits');
	}
	
	/**
	 * Get data.
	 * @return integer value
	 * @access protected
	 */
	function get($value){
		
		return (isset($this->values[$value]) ? $this->values[$value] : '0');
	}

	/**
	 * Get views.
	 * 
	 * return integer views
	 * @access public
	 */
	function getViews() {
		
		return $this->get('views');
	}
	
	/**
	 * getViewsAllTime function.
	 * 
	 * @access public
	 * @return integer all time views.
	 */
	function getViewsAllTime() {
		
		return $this->get('alltime_views');
	}
}
?>