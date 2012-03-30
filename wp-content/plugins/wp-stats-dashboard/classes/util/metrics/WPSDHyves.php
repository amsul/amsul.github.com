<?php
/**
 * WPSDHyves.
 * @author dligthart <info@daveligthart.com>
 * @version 0.1
 * @package wp-stats-dashboard
 */
class WPSDHyves extends WPSDStats {

	var $xml;
	var $values;
	var $address = '';
	var $un;
	
	/**
	 * WPSDHyves.
	 * 
	 * @param boolean $curl
	 */
	function WPSDHyves($curl = false) {
		
		parent::WPSDStats();
		
		$form = new WPSDAdminConfigForm();
		
		$this->un = trim($form->getWpsdHyvesUn());
		
		if('' != $this->un) {
			
			$this->address = 'http://'.$this->un.'.hyves.nl/profile/';
			
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

		$this->values['friends'] = $this->get_count('', $this->xml);

		$this->set_cache('hyves_fr', $this->values['friends']);
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
		
		preg_match("@<small>\(([0-9]+)\)</small>@si", $data, $matches);
		
		return number_format($matches[1]);
	}
		
	/**
	 * Set cached.
	 */
	function set_cached() {
		$this->values['friends'] = $this->get_cache('hyves_fr');
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
	 * Get Friends.
	 * 
	 * @return integer friends
	 * @access public 
	 */
	function getFriends() {
		return $this->get('friends');
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