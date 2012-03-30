<?php
/**
 * WPSDSiteValue.
 * @author dligthart <info@daveligthart.com>
 * @version 0.2
 * @package wp-stats-dashboard
 */
class WPSDSiteValue extends WPSDStats {
	
	/**
	 * value
	 * 
	 * @var mixed
	 * @access public
	 */
	var $value;
	
	/**
	 * WPSDSiteValue function.
	 * 
	 * @access public
	 * @param int $age_years. (default: 1)
	 * @param int $inbound. (default: 1)
	 * @param float $cpm. (default: 1.2)
	 * @return void
	 */
	function WPSDSiteValue($age_years = 1, $inbound = 1, $cpm = 1.2) {
		
		parent::WPSDStats();
		
		if($this->isOutdated()) {
			
			$totalhits = $this->getTotalHits();
			
			$rev_month = (($totalhits / $age_years / 12) / 1000) * $cpm;
			
			$this->value = round(($rev_month * $inbound * 12), 2);
			
			if(null == $this->value) $this->value = '-';
			
			$this->set_cache('site_value', $this->value);
			
			$this->set_cache('site_total_views', $totalhits);
			
		} else {
			
			$this->value = $this->get_cache('site_value');	
		}
	}
	
	/**
	 * getValue function.
	 * 
	 * @access public
	 * @return void
	 */
	function getValue() {
		
		return $this->value;
	}
	
	/**
	 * getTotalHits function.
	 * 
	 * @access public
	 * @return void
	 */
	function getTotalHits() {
		
		return get_option('wpsd_alltime_hits');
	}
}
?>