<?php
/**
 * WPSDPlaxo.
 * 
 * @author dligthart <info@daveligthart.com>
 * @version 0.1
 * @package wp-stats-dashboard
 */
class WPSDPlaxo extends WPSDStats {

	var $xml;
	var $values;
	var $address = '';
	var $uri = '';
	
	/**
	 * WPSDPlaxo function.
	 * 
	 * @access public
	 * @return void
	 */
	function WPSDPlaxo() {
	
		parent::WPSDStats();
		
		$form = new WPSDAdminConfigForm();
		
		$this->uri = trim($form->getWpsdPlaxoUri());
		
		if('' != $this->uri) {
			
			$this->address = $this->uri;
			
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
	 * @return unknown_type
	 */
	function isEnabled() {
		return ('' != $this->uri);
	}
	
	/**
	 * Set data.
	 */
	function set() {
		
		preg_match('@<span class="connections">([0-9]+) connections</span>@si', $this->xml, $matches);
		
		//Set Connection Count.
		$this->values['count'] = number_format($matches[1]);
		
		$this->set_cache('plaxo_c', $this->values['count']);
	}
	
	/**
	 * Set cached.
	 * 
	 * @return unknown_type
	 */
	function set_cached() {
	
		$this->values['count'] = $this->get_cache('plaxo_c');
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
	 * Get connection count.
	 * 
	 * @return integer connections
	 * @access public
	 */
	function getConnectionCount() {
		return $this->get('count');
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