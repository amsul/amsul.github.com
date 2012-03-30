<?php
/**
 * WPSDW3Validator.
 * @author dligthart <info@daveligthart.com>
 * @version 0.1
 * @package wp-stats-dashboard
 */
class WPSDW3Validator extends WPSDStats {

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
	 * WPSDW3Validator.
	 */
	function WPSDW3Validator() {	
		
		parent::WPSDStats();
		
		$domain = get_bloginfo('url');			
					
		$this->address = "http://validator.w3.org/check?uri={$domain}";
			
		if($this->isOutdated() && '' != $domain) {
			
			$this->xml = $this->fetchDataRemote($this->address, false, 1, true);
			
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

		preg_match("@([0-9]+) Errors@", $this->xml, $matches);
		
		$c = $matches[1];
			
		$this->values['errors'] = $c;
				
		$this->set_cache('w3c_errors', $this->values['errors'] );
	}
	
	/**
	 * set_cached function.
	 * 
	 * @access public
	 * @return void
	 */
	function set_cached() {
		
		$this->values['errors'] = $this->get_cache('w3c_errors');
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
	 * getVisit function.
	 * 
	 * @access public
	 * @return string last date visted
	 */
	function getErrors() {
	
		return $this->get('errors');
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