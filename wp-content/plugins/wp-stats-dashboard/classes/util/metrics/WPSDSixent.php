<?php
/**
 * WPSDSixent.
 * TODO: sixent doesn't have any public metrics. You need to login first.
 * @author dligthart <info@daveligthart.com>
 * @version 0.1
 * @package wp-stats-dashboard
 */
class WPSDSixent extends WPSDStats {

	var $xml;
	var $values;
	var $address = '';
	var $un;
	
	/**
	 * WPSDSixent.
	 * 
	 * @param boolean $curl
	 */
	function WPSDSixent($curl = false) {
		
		parent::WPSDStats();
		
		$form = new WPSDAdminConfigForm();
		
		$this->un = trim($form->getWpsdSixentUn());
		
		if('' != $this->un) {
			
			$this->address = 'http://'.$this->un.'.sixent.com/contacts';
			
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
	
		$this->values['contacts'] = $this->get_count('', $this->xml);
		
		$this->set_cache('sixent_c', $this->values['contacts']);
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

		preg_match("@rm-page_contact_count'\).innerHTML = '([0-9]+) Contacts'@si", $data, $matches);
	
		return number_format($matches[1]);
	}
	
	/**
	 * Set cached.
	 */
	function set_cached() {
		$this->values['contacts'] = $this->get_cache('sixent_c');
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