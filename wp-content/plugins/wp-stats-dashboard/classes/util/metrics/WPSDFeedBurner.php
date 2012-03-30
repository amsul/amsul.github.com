<?php
/**
 * WPSDFeedBurner.
 * @author dligthart <info@daveligthart.com>
 * @version 0.2
 * @package wp-stats-dashboard
 */
class WPSDFeedBurner extends WPSDStats {

	var $xml;
	var $values;
	var $address = '';
	var $api_uri = 'http://feedburner.google.com/api/awareness/1.0/GetFeedData?uri=';

	/**
	 * WPSDFeedBurner.
	 * @param string $domain
	 * @param boolean $curl
	 */
	function WPSDFeedBurner() {
		parent::WPSDStats();
			
		$form = new WPSDAdminConfigForm();
			
		$this->address = $form->getWpsdFeedburnerUri();
			
		if($this->isOutdated() && '' != $this->address) {

			$this->set();

		} else {

			$this->set_cached();
		}

	}

	function setHits() {
		
		$uri = $this->api_uri . $this->address;
		
		$data = $this->fetchDataRemote($uri);

		$c = number_format($this->get_match_attr('hits', $data, 1));
			
		$this->set_cache('feedburner_h', $c);

		return $c;
	}
	
	function setCirculation() {
		
		$uri = $this->api_uri . $this->address;
		
		$data = $this->fetchDataRemote($uri);

		$c = number_format($this->get_match_attr('circulation', $data, 1));
			
		$this->set_cache('feedburner_c', $c);

		return $c;
	}

	/**
	 * Set data.
	 */
	function set() {

		$this->values['hits'] = $this->setHits();

		$this->values['circulation'] = $this->setCirculation();

	}
	
	/**
	 * Get match attr.
	 * 
	 * @param $type
	 * @param $data
	 * @param $match_index
	 * @return unknown_type
	 */
	function get_match_attr($type, $data, $match_index = 1) {
		
		preg_match("@{$type}=\"([0-9]+)\"@si", $data, $matches);

		return $matches[$match_index];
	}
	
	/**
	 * Get match tag.
	 * 
	 * @param $type
	 * @param $data
	 * @param $match_index
	 * @return unknown_type
	 */
	function get_match($type, $data, $match_index = 1) {
			
		preg_match("@<{$type}>(.*?)</{$type}>@si", $data, $matches);

		return $matches[$match_index];
	}

	/**
	 * Set cached.
	 */
	function set_cached() {

		$this->values['hits'] = $this->get_cache('feedburner_h');

		$this->values['circulation'] = $this->get_cache('feedburner_c');
	}

	/**
	 * Get data.
	 * @return value
	 * @access protected
	 */
	function get_number($value){
		return (isset($this->values[$value]) ? $this->values[$value] : '0');
	}
	
	/**
	 * Get hits.
	 * 
	 * @return integer hits
	 */
	function getHits() {		
		return $this->values['hits'];
	}
	
	/**
	 * Get circulation.
	 * 
	 * @return integer circulation
	 */
	function getCirculation() {
		return $this->values['circulation'];
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