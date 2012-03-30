<?php
/**
 * WPSDGoogleBackLinks.
 * @author dligthart <info@daveligthart.com>
 * @version 0.2
 * @package wp-stats-dashboard
 */
class WPSDGoogleBackLinks extends WPSDStats {

	var $xml;
	var $values;
	var $address = '';
	var $address2 = '';
	var $egosearch = '';
	
	/**
	 * WPSDGoogleBackLinks.
	 * @param string $domain
	 * @param boolean $curl
	 */
	function WPSDGoogleBackLinks($domain, $curl = false) {
		
		parent::WPSDStats();

		$domain = $this->getNormalizedUrl($domain);
		
		$form = new WPSDAdminConfigForm();
		
		$this->egosearch = str_replace(' ', '+', $form->getWpsdEgoSearch());
		
		if($this->isOutdated() && '' != $domain) {
					
			$this->values['rank'] = $this->getBackLinks($domain);
			
			$this->set_cache('google_bl', $this->values['rank']);
			
			$this->values['blogsearch_backlinks'] = $this->getBlogSearchBacklinks($domain);
			
			$this->set_cache('google_bs_bl', $this->values['blogsearch_backlinks']);
			
			$this->values['ego'] = $this->getEgoSearch($this->egosearch);
			
			$this->set_cache('google_ego', $this->values['ego']);
			
		} else {
			
			$this->values['rank'] = $this->get_cache('google_bl');
			
			$this->values['blogsearch_backlinks'] = $this->get_cache('google_bs_bl');
			
			$this->values['ego'] = $this->get_cache('google_ego');
		}
	}

	/**
	 * Set data.
	 */
	function set() {
		
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
	 * @return integer rank
	 * @access public
	 */
	function getRank() {
		return $this->get('rank');
	}
	
	/**
	 * getBacklinks function.
	 * 
	 * @access public
	 * @return integer backlinks
	 */
	function getBsBacklinks() {
		
		return $this->get('blogsearch_backlinks');
	}
	
	/**
	 * getEgoSearchResults function.
	 * 
	 * @access public
	 * @return void
	 */
	function getEgoSearchResults() {
		
		return $this->get('ego');
	}	
	
	/**
	 * getEgoSearchAddress function.
	 * 
	 * @access public
	 * @param mixed $name
	 * @return void
	 */
	function getEgoSearchAddress() {
		
		$name = $this->egosearch;
		
	    return "http://www.google.com/search?hl=en&lr=&ie=UTF-8&q=%22{$name}%22&filter=0";      	
	}
	
	/**
	 * getBlogSearchAddress function.
	 * 
	 * @access public
	 * @return void
	 */
	function getBlogSearchAddress() {
	
		$uri = $this->getNormalizedUrl(get_bloginfo('url'));
		
		$uri = trim(eregi_replace('http://', '', $uri)); $uri = trim(eregi_replace('http', '', $uri));

	    $this->address2 = 'http://blogsearch.google.com/blogsearch?hl=en&num=10&c2coff=1&lr=lang_en&safe=active&oi=spell&ie=UTF-8&q=' . $uri;	    
	  	
		return $this->address2;
	}
	
	/**
	 * Get address.
	 * @return address
	 * @access public
	 */
	function getAddress() {
		
		$uri = $this->getNormalizedUrl(get_bloginfo('url'));
		
		$uri = trim(eregi_replace('http://', '', $uri)); $uri = trim(eregi_replace('http', '', $uri));

	   	return 'http://www.google.com/search?hl=en&lr=&ie=UTF-8&q=link:'.$uri.'&filter=0';
	}
	
	/**
	 * getBlogSearchBackLinks function.
	 * 
	 * @access private
	 * @param mixed $uri
	 * @return integer
	 */
	function getBlogSearchBackLinks($uri) {
	
		if('' == $uri) return 0;
	
		$uri = trim(eregi_replace('http://', '', $uri)); $uri = trim(eregi_replace('http', '', $uri));

	    $this->address2 = 'http://www.google.com/search?hl=en&lr=lang_en&ie=UTF-8&tbm=blg&c2coff=1&safe=active&q=' . $uri;	    
	  
	    $data = $this->fetchDataRemote($this->address2, false, 1, true);
	    
	   	preg_match("@<div id=resultStats>About (.*?) results@i", $data, $r);	
	    
	    return ($r[1]) ? $r[1] : '0';
	
	}
	
	/**
	 * getEgoSearch function.
	 * 
	 * @access public
	 * @param mixed $name
	 * @return integer
	 */		
	function getEgoSearch($name) {
	    
	    if('' == $name) return 0;
	    
	    $data = $this->fetchDataRemote($this->getEgoSearchAddress($name), false, 1, true);
	    
	    preg_match("@<div id=resultStats>About (.*?) results@i", $data, $r);		
	    
	    return ($r[1]) ? $r[1] : '0';
	}
	
	/**
	 * getBackLinks function.
	 * 
	 * @access public
	 * @param mixed $uri
	 * @return integer
	 */
	function getBackLinks($uri) {
	
		if('' == $uri) return 0;
	    
		$uri = trim(eregi_replace('http://', '', $uri)); $uri = trim(eregi_replace('http', '', $uri));

	    $this->address = 'http://www.google.com/search?hl=en&lr=&ie=UTF-8&q=link:'.$uri.'&filter=0';
	    
	    $data = $this->fetchDataRemote($this->address, false, 1, true);
	    
	    preg_match("@<div id=resultStats>About (.*?) results@i", $data, $r);		
	    
	    return ($r[1]) ? $r[1] : '0';
	}
}
?>