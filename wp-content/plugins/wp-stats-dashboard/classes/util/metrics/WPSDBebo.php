<?php
/**
 * WPSDBebo.
 * TODO: implement when profiles are public.
 * @author dligthart <info@daveligthart.com>
 * @version 0.1
 * @package wp-stats-dashboard
 */
class WPSDBebo extends WPSDStats {

	var $xml;
	var $values;
	var $address = '';
	var $un;
	
	/**
	 * WPSDBebo.
	 * 
	 * @param boolean $curl
	 */
	function WPSDBebo($curl = false) {
		
		parent::WPSDStats();
		
		$form = new WPSDAdminConfigForm();
		
		$this->un = trim($form->getWpsdBeboUn());
		
		if('' != $this->un) {
		
			$this->address = 'http://www.bebo.com/' . $this->un;
			
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
	
		$this->values['contacts'] = $this->get_count('contacts', $this->xml);
		
		$this->set_cache('bebo_c', $this->values['contacts']);
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

		preg_match("@{$type} \(([0-9]+)\)</h2>@si", $data, $matches);
	
		return number_format($matches[1]);
	}
	
	/**
	 * Set cached.
	 */
	function set_cached() {
		$this->values['contacts'] = $this->get_cache('bebo_c');
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
	 * Get contacts.
	 * 
	 * @return integer contacts
	 * @access public
	 */
	function getContacts() {
		return $this->get('contacts');
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