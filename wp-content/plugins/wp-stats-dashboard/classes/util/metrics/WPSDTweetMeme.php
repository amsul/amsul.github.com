<?php
/**
 * WPSDTweetMeme.
 * @author dligthart <info@daveligthart.com>
 * @version 0.2
 * @package wp-stats-dashboard
 */
class WPSDTweetMeme extends WPSDStats {

	var $xml;
	var $values;
	var $address = '';
	var $address2 = '';
	/**
	 * WPSDTweetMeme.
	 * @param string $domain
	 * @param boolean $curl
	 */
	function WPSDTweetMeme($domain, $curl = false) {
		
		parent::WPSDStats();
		
		$this->address = 'http://api.tweetmeme.com/url_info.xml?url=' . $domain;
		
		$domain = $this->getHost(parse_url($domain));

		$domain = str_replace('www.', '', $domain);
		
		$this->address2 = 'http://tweetmeme.com/search?q=' . $domain;
		
		if($this->isOutdated()) {
			
			$this->xml = $this->fetchDataRemote($this->address);
		
			$this->set();
			
		} 
		else {
			
			$this->set_cached();
		}
	}
	
	/**
	 * Set data.
	 */
	function set() {
		
		preg_match("@<url_count>(.*?)</url_count>@si", $this->xml, $matches);
	
		$this->values['count'] = number_format($matches[1]);
		
		preg_match("@<tm_link>(.*?)</tm_link>@si", $this->xml, $matches2);
		
		$this->address2 = $matches2[1];
		
		$this->set_cache('tweets', $this->values['count']);
		
		$this->set_cache('tm_addr', $this->address2);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	function set_cached() {
		
		$this->values['count'] = $this->get_cache('tweets');
		
		$this->address2 = $this->get_cache('tm_addr');
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
	 * Get rank.
	 * @return rank
	 * @access public
	 */
	function getCount() {
		return $this->get('count');
	}

	/**
	 * Get address.
	 * @return address
	 * @access public
	 */
	function getAddress() {
		return $this->address2;
	}
}
?>