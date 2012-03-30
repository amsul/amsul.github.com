<?php
/**
 * WPSDDisqus.
 * @author dligthart <info@daveligthart.com>
 * @version 0.1
 * @package wp-stats-dashboard
 */
class WPSDDisqus extends WPSDStats {

	var $xml;
	var $values;
	var $address = '';
	var $un;
	
	/**
	 * WPSDDisqus.
	 * 
	 * @param boolean $curl
	 */
	function WPSDDisqus($curl = false) {
		
		parent::WPSDStats();
		
		$form = new WPSDAdminConfigForm();
		
		$this->un = trim($form->getWpsdDisqusUn());
		
		if('' != $this->un) {
					
			$this->address = 'http://disqus.com/' . $this->un . '/';
				
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
		
		preg_match("@([0-9]+) Total Comments@si", $this->xml, $matches);
		
		$this->values['cp'] = number_format($matches[1]);
		
		preg_match("@([0-9]+) Likes received@si", $this->xml, $matches);
		
		$this->values['pr'] = number_format($matches[1]);
		
		$this->set_cache('disqus_cp', $this->values['cp']);
		
		$this->set_cache('disqus_pr', $this->values['pr']);
				
	}
	
	/**
	 * Set cached.
	 */
	function set_cached() {
		$this->values['cp'] = $this->get_cache('disqus_cp');
		$this->values['pr'] = $this->get_cache('disqus_pr');
	}
	
	/**
	 * Get data.
	 * @return value
	 * @access protected
	 */
	function get($value){
		return (isset($this->values[$value]) ? $this->values[$value] : '0');
	}

	function getComments() {
		return $this->get('cp');
	}
	
	function getPoints() {
		return $this->get('pr');
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