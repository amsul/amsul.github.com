<?php
/**
 * WPSDBitly.
 * @author dligthart <info@daveligthart.com>
 * @version 0.2
 * @package wp-stats-dashboard
 */
class WPSDBitly extends WPSDStats {

	var $xml;
	var $values;
	var $address = '';
	var $key = 'R_e925ad28d2620b74307b092ecf3363fe';
	var $un = 'wpsd';
	var $request_shorten_url;
	var $request_clicks_url; 
	
	/**
	 * WPSDBitly.
	 * @param string $domain
	 * @param boolean $curl
	 */
	function WPSDBitly($domain = '', $curl = false) {
		
		parent::WPSDStats();
		
		if('' != $domain) {
			
			$form = new WPSDAdminConfigForm();
			
			if('' != trim($form->getWpsdBitlyUn())) {
				
				$this->un = trim($form->getWpsdBitlyUn());
				
				$this->key = trim($form->getWpsdBitlyKey());
			}
			
			$this->request_shorten_url = 'http://api.bit.ly/v3/shorten?login='. $this->un. '&apiKey=' . $this->key. '&uri='.urlencode($domain).'&format=xml';

			if($this->isOutdated() && '' != $this->un) {
				
				$this->set();
				
			} else {
				
				$this->set_cached();
			}
		}
	}
	
	/**
	 * Shorten.
	 * 
	 * @return unknown_type
	 */
	function shorten() {
		
		$s = $this->get_cache('bitly_s');
		
		if('' == $s) {
			
			$data = $this->fetchDataRemote($this->request_shorten_url);
			
			$s = $this->get_match('url', $data, 1);
			
			$this->set_cache('bitly_s', $s);
		} 
		
		return $s;
	}
	
	/**
	 * Set clicks.
	 * 
	 * @return unknown_type
	 */
	function setClicks() {
		
		$this->request_clicks_url = 'http://api.bit.ly/v3/clicks?shortUrl=' . $this->getShortUrl() . '&login=' . $this->un . '&apiKey=' . $this->key . '&format=xml'; //&hash=j3

		$data = $this->fetchDataRemote($this->request_clicks_url);
		
		$c = number_format($this->get_match('user_clicks', $data, 1));
			
		$this->set_cache('bitly_c', $c);

		return $c;
	}
	
	/**
	 * Is enabled.
	 * @return unknown_type
	 */
	function isEnabled() {
		return ('' != $this->un);
	}
	
	/**
	 * Set data.
	 */
	function set() {
		
		$this->values['short'] = $this->shorten();
		
		$this->values['clicks'] = $this->setClicks();
		
	}
	
	/**
	 * Get match.
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
	 * 
	 * @return unknown_type
	 */
	function set_cached() {
		
		$this->values['short'] = $this->get_cache('bitly_s');
		
		$this->values['clicks'] = $this->get_cache('bitly_c');
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
	 * Get short url.
	 * 
	 * @return string url
	 */
	function getShortUrl() {
			
		return $this->values['short'];
	}
	
	/**
	 * Get clicks.
	 * 
	 * return integer clicks.
	 */
	function getClicks() {
		
		return $this->values['clicks'];
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