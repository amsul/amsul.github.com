<?php
/**
 * WPSDNewsVine.
 * @author dligthart <info@daveligthart.com>
 * @version 0.2
 * @package wp-stats-dashboard
 */
class WPSDNewsVine extends WPSDStats {

	var $xml;
	var $values;
	var $address = '';
	var $un;
	
	/**
	 * WPSDNewsVine.
	 * 
	 * @param boolean $curl
	 */
	function WPSDNewsVine() {
		parent::WPSDStats();
		
		$form = new WPSDAdminConfigForm();
		
		$this->un = trim($form->getWpsdNewsVineUn());
		
		if('' != $this->un) {
			
			$this->address = 'http://'.$this->un.'.newsvine.com/'; 
			
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
		
		$this->values['articles'] = $this->get_count('Articles Posted', $this->xml);
		
		$this->set_cache('newsvine_a', $this->values['articles']);
	
		$this->values['links'] = $this->get_count('Links Seeded', $this->xml);

		$this->set_cache('newsvine_l', $this->values['links']);

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

		preg_match("@{$type}: ([0-9]+)@si", $data, $matches);
	
		return number_format($matches[1]);
	}
	
	/**
	 * Set cached.
	 */
	function set_cached() {
		$this->values['articles'] = $this->get_cache('newsvine_a');
		$this->values['links'] = $this->get_cache('newsvine_l');
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
	 * Get articles.
	 * 
	 * @return integer articlecount
	 * @access public
	 */
	function getArticles() {
		return $this->get('articles');
	}
	
	/**
	 * Get links.
	 * 
	 * @return integer linkcount
	 * @access public 
	 */
	function getLinks() {
		return $this->get('links');
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