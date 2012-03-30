<?php
/**
 * WPSDGetGlue.
 * 
 * @author dligthart <info@daveligthart.com>
 * @version 0.1
 * @package wp-stats-dashboard
 */
class WPSDGetGlue extends WPSDStats {

	var $xml;
	var $values;
	var $address = '';
	var $un;
	
	/**
	 * WPSDGetGlue.
	 * 
	 * @param boolean $curl
	 */
	function WPSDGetGlue() {
		
		parent::WPSDStats();
		
		$form = new WPSDAdminConfigForm();
		
		$this->un = trim($form->getWpsdGetGlueUn());
		
		if('' != $this->un) {
			
			//http://getglue.com/daveligthart
			
			$this->address = 'http://getglue.com/' . $this->un; 
			
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
	 * @access private
	 */
	function isEnabled() {
		return ('' != $this->un);
	}
	
	/**
	 * Set data.
	 * @access private
	 */
	function set() {

		$this->values['checkins'] = $this->get_count('checkins', $this->xml);

		$this->set_cache('getglue_checkins', $this->values['checkins']);
		
		$this->values['likes'] = $this->get_count('likes', $this->xml);

		$this->set_cache('getglue_likes', $this->values['likes']);
		
		$this->values['reviews'] = $this->get_count('reviews', $this->xml);

		$this->set_cache('getglue_reviews', $this->values['reviews']);
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

		preg_match("@<span class=\"{$type} count\">([0-9]+)</span>@si", $data, $matches);
		
		return number_format($matches[1]);
	}
		
	/**
	 * Set cached.
	 * @access private
	 */
	function set_cached() {
		
		$this->values['checkins'] = $this->get_cache('getglue_checkins');
		
		$this->values['likes'] = $this->get_cache('getglue_likes');
		
		$this->values['reviews'] = $this->get_cache('getglue_reviews');
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
	 * Get checkins.
	 * 
	 * @return integer checkins
	 * @access public 
	 */
	function getCheckins() {
		
		return $this->get('checkins');
	}
	
	/**
	 * Get likes.
	 * 
	 * @return integer number of likes
	 */
	function getLikes() {
		
		return $this->get('likes');
	}
	
	/**
	 * Get reviews.
	 * 
	 * @return integer number of reviews
	 */
	function getReviews() {
		
		return $this->get('reviews');
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