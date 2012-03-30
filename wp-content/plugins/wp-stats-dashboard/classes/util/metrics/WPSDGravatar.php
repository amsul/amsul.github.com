<?php
/**
 * WPSDGravatar.
 *
 * @author dligthart <info@daveligthart.com>
 * @version 0.1
 * @package wp-stats-dashboard
 */
class WPSDGravatar extends WPSDStats {

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
	 * address2
	 * 
	 * (default value: '')
	 * 
	 * @var string
	 * @access public
	 */
	var $address2 = '';
	/**
	 * userhash
	 * 
	 * (default value: '')
	 * 
	 * @var string
	 * @access public
	 */
	var $userhash  = '';
	
	/**
	 * WPSDGravatar.
	 * 
	 * @param boolean $curl
	 */
	function WPSDGravatar() {
		
		parent::WPSDStats();
			
		$current_user = wp_get_current_user();
					
		if(null != $current_user) {
			
			$this->userhash = md5($current_user->user_email);
		}
		
		$this->address = "http://en.gravatar.com/{$this->userhash}"; 
		
		$this->address2 = "http://gravatar.com/avatar/{$this->userhash}";

		if('' != $this->address) {
					
			if($this->isOutdated()) {
		
				$this->xml = $this->fetchDataRemote($this->address . '.json');
						
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
		
		return true;
	}
	
	/**
	 * Set data.
	 */
	function set() {
		
		$json = new Services_JSON;
		
		$obj = $json->decode($this->xml);
		
		//var_dump($obj);
		
		if($obj) {
		
			$o = $obj->entry[0];
			
			$this->values['location'] = $o->currentLocation;		
			
			$this->values['name'] = $o->name->formatted;
		}
		
		$this->set_cache('gravatar_location', $this->values['location']);
		
		$this->set_cache('gravatar_name', $this->values['name']);
	}

	
	/**
	 * Set cached.
	 */
	function set_cached() {
	
		$this->values['location'] = $this->get_cache('gravatar_location');
		
		$this->values['name'] = $this->get_cache('gravatar_name');
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
	 * Get address.
	 * @return address
	 * @access public
	 */
	function getAddress() {
		
		return $this->address;
	}
	
	/**
	 * getAvatar function.
	 * 
	 * @access public
	 * @return void
	 */
	function getAvatar() {
		
		return $this->address2;
	}
	
	/**
	 * getCurrentLocation function.
	 * 
	 * @access public
	 * @return string geo location
	 */
	function getLocation() {
		
		return $this->values['location'];
	}
	
	/**
	 * getFullName function.
	 * 
	 * @access public
	 * @return string name
	 */
	function getFullName() {
		
		return $this->values['name'];
	}
	
	/**
	 * getHash function.
	 * 
	 * @access public
	 * @return void
	 */
	function getHash() {
		
		return $this->userhash;
	}
}
?>