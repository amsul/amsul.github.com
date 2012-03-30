<?php
/**
 * WPSDBing.
 * @author dligthart <info@daveligthart.com>
 * @version 0.4
 * @package wp-stats-dashboard
 */
class WPSDBing extends WPSDStats {

	var $xml;
	var $values;
	var $address = '';

	/**
	 * WPSDBing.
	 * @param string $domain
	 * @param boolean $curl
	 */
	function WPSDBing($domain, $curl = false) {
		
		parent::WPSDStats();
		
		$this->address = 'http://www.bing.com/search?q=site:'.$domain.'&go=&form=QBLH&filt=all&setplang=en-US'; 

		if($this->isOutdated()) {
			
			$this->xml = str_replace(',', '.', $this->fetchDataRemote($this->address, false, 1 ,true));
	
			$this->set();
			
		} else {
			
			$this->set_cached();
		}
	}
	
	/**
	 * Set data.
	 */
	function set() {
		
		preg_match("@([0-9]+) results@", $this->xml, $matches);

		$this->values['rank'] = number_format($matches[1]);
		
		$this->set_cache('bing_r', $this->values['rank']);
		
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	function set_cached() {
		$this->values['rank'] = $this->get_cache('bing_r');
	}
	
	/**
	 * Get data.
	 * @return value
	 * @access protected
	 */
	function get($value){
		return (isset($this->values[$value]) ? $this->values[$value] : '0');
	}

	/**
	 * Get rank.
	 * @return rank
	 * @access public
	 */
	function getRank() {
		return $this->get('rank');
	}

	/**
	 * Get address.
	 * @return address
	 * @access public
	 */
	function getAddress() {
		return $this->address;
	}
}
?>