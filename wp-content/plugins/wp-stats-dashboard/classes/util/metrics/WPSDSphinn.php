<?php
/**
 * WPSDSphinn.
 * @author dligthart <info@daveligthart.com>
 * @version 0.1
 * @package wp-stats-dashboard
 */
class WPSDSphinn extends WPSDStats {

	var $xml;
	var $values;
	var $address = '';
	var $un;
	
	/**
	 * WPSDSphinn.
	 */
	function WPSDSphinn() {
		parent::WPSDStats();
		
		$form = new WPSDAdminConfigForm();
		
		$this->un = trim($form->getWpsdSphinnUn());
		
		if('' != $this->un) {
			
			$this->address = 'http://sphinn.com/user/' . $this->un; 
			
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
		
		$this->values['topics'] = $this->get_count('Topics Submitted:', $this->xml);
		
		$this->set_cache('sphinn_t', $this->values['topics']);
		
		$this->values['comments'] = $this->get_count('Comments Made:', $this->xml);
		
		$this->set_cache('sphinn_c', $this->values['comments']);
		
		$this->values['hot'] = $this->get_count('Topics Gone Hot:', $this->xml);
		
		$this->set_cache('sphinn_h', $this->values['hot']);
		
		$this->values['cast'] = $this->get_count('Sphinns Cast:', $this->xml);
		
		$this->set_cache('sphinn_ct', $this->values['cast']);
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
		
		preg_match("@<strong>{$type}</strong></td>(.*?)([0-9]+)</td>@si", $data, $matches);
		
	//	print_r($matches);
		
		return number_format($matches[2]);
	}
	
	/**
	 * Set cached.
	 */
	function set_cached() {
	
		$this->values['topics'] = $this->get_cache('sphinn_t');
			
		$this->values['comments'] = $this->get_cache('sphinn_c');
				
		$this->values['hot'] = $this->get_cache('sphinn_h');
		
		$this->values['cast'] = $this->get_cache('sphinn_ct');
	}
	
	/**
	 * Get data.
	 * 
	 * @return value
	 * @access protected
	 */
	function get($value){
		return (isset($this->values[$value]) ? $this->values[$value] : '0');
	}
	
	/**
	 * Get topic count.
	 * 
	 * @return integer topics
	 * @access public
	 */
	function getTopics() {
		return $this->get('topics');
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	function getComments() {
		return $this->get('comments');
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	function getHot() {
		return $this->get('hot');	
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	function getCast() {
		return $this->get('cast');
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