<?php
/**
 * WPSDSiteAge.
 * @author dligthart <info@daveligthart.com>
 * @version 0.3
 * @package wp-stats-dashboard
 */
class WPSDSiteAge extends WPSDStats {

	var $xml;
	var $values;
	var $address = '';

	/**
	 * WPSDSiteAge.
	 * 
	 * @param string $domain
	 * @param boolean $curl
	 */
	function WPSDSiteAge($domain, $curl = false) {
		
		parent::WPSDStats();
		
		$url = preg_replace("/^(http:\/\/)*(www.)*/is", "",  $this->getHost(parse_url($domain)) );

		$url = preg_replace("/\/.*$/is" , "" ,$url);
					
		$this->address = 'http://reports.internic.net/cgi/whois?whois_nic='.$url.'&type=domain';

		if($this->isOutdated() && '' != $url) {
			
			$this->xml = $this->fetchDataRemote($this->address);

			$this->set();
			
		} else {
			
			$this->set_cached();
		}
	}

	/**
	 * Set data.
	 */
	function set() {

		preg_match("@Creation Date:(.*?)Expiration Date@si", $this->xml, $matches);

		$creation_date = date('Y-m-d', strtotime($matches[1]));

		$a = strtotime($creation_date);
		
		$b = strtotime("now");
		
		$years = ( intval( ( $b - $a) / 86400) +1 ) / 365;

		if(null == $years) $years = 1;

		$this->values['age'] = $years;
		
		$this->set_cache('age', $years);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	function set_cached() {
		$this->values['age'] = $this->get_cache('age');
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
	function getAge() {
		return $this->get('age');
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