<?php
/**
 * WPSDPosterous.
 * @author dligthart <info@daveligthart.com>
 * @version 0.2
 * @package wp-stats-dashboard
 */
class WPSDPosterous extends WPSDStats {

	var $xml;
	var $values;
	var $address = '';
	var $un;
	
	/**
	 * WPSDPosterous.
	 */
	function WPSDPosterous() {
		
		parent::WPSDStats();
		
		$form = new WPSDAdminConfigForm();
		
		$this->un = trim($form->getWpsdPosterousUn());
		
		if('' != $this->un) {
			
			$this->address = 'http://posterous.com/people/subscribers/' . $this->un; //;http://'.$this->un .'.posterous.com';
			
			if($this->isOutdated()) {
				
				$this->xml = $this->fetchDataRemote($this->address);
				
				$this->set();
				
			} else {
				
				$this->set_cached();
			}
		}
	}
	
	/**
	 * Is enabled.
	 * 
	 * @return boolean
	 */
	function isEnabled() {
		return ('' != $this->un);
	}
	
	/**
	 * Set data.
	 */
	function set() {
		
		//$this->values['subscribers'] = $this->get_count('posterousHeaderItemValue', $this->xml);
		
		preg_match('@has ([0-9]+)@', $this->xml, $matches);
		
		$this->values['subscribers'] = number_format($matches[1]);
		
		$this->set_cache('posterous_s', $this->values['subscribers']);
	}
	
	/**
	 * Get count.
	 * 
	 * @param $type
	 * @param $data
	 * @return integer count
	 * @access protected
	 */
	function get_count($type, $data) {
		
		preg_match("@class=\"{$type}\">([0-9]+)</div>@si", $data, $matches);
				
		return number_format($matches[1]);
	}
	
	/**
	 * Set cached.
	 */
	function set_cached() {
		
		$this->values['subscribers'] = $this->get_cache('posterous_s');
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
	 * Get subscribers.
	 * @return integer count
	 */
	function getSubscribers() {
		return $this->get('subscribers');
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