<?php
/**
 * WPSDArchive.
 * @author dligthart <info@daveligthart.com>
 * @version 0.2
 * @package wp-stats-dashboard
 */
class WPSDArchive extends WPSDStats {

	/**
	 * xml
	 * 
	 * @var mixed
	 * @access public
	 */
	var $xml;
	/**
	 * values
	 * 
	 * @var mixed
	 * @access public
	 */
	var $values;
	/**
	 * address
	 * 
	 * (default value: '')
	 * 
	 * @var string
	 * @access public
	 */
	var $address = '';

	/**
	 * WPSDArchive function.
	 * 
	 * @access public
	 * @return void
	 */
	function WPSDArchive() {	
		
		parent::WPSDStats();
		
		$domain = get_bloginfo('url');	
		
		$domain = 'http://www.daveligthart.com';
		
		$domain = str_replace('www.','',$this->getHost(parse_url($domain)));	
					
		//$this->address = "http://web.archive.org/web/*/{$domain}";
		
		$this->address = "http://wayback.archive.org/web/*/{$domain}";
		
		if($this->isOutdated() && '' != $domain) {
			
			$this->xml = $this->fetchDataRemote($this->address);
						
			$this->set();
		} 
		else {

			$this->set_cached();
		}
	}
		
	/**
	 * set function.
	 * 
	 * @access public
	 * @return void
	 */
	function set() {
	
		preg_match_all("@<strong>(.*) times@", $this->xml, $matches);
							
		$this->values['results'] = $matches[1][0];
				
		$this->set_cache('archive_results', $this->values['results'] );
	}
	
	/**
	 * set_cached function.
	 * 
	 * @access public
	 * @return void
	 */
	function set_cached() {
		
		$this->values['results'] = $this->get_cache('archive_results');
	}

	/**
	 * 
	 * @param unknown_type $value
	 * @return unknown_type
	 */
	function get($value){
		return (isset($this->values[$value]) ? $this->values[$value] : '0');
	}

	/**
	 * getResults function.
	 * 
	 * @access public
	 * @return integer number of archives
	 */
	function getResults() {
	
		return $this->get('results');
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