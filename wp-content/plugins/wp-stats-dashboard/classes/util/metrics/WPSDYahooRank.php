<?php
/**
 * WPSDYahooRank. Ranking based on number of incoming links.
 * @author dligthart <info@daveligthart.com>
 * @version 0.4
 * @package wp-stats-dashboard
 */
class WPSDYahooRank extends WPSDStats {

	var $xml;
	var $values;
	// deprecated
	/*var $address = 'http://search.yahooapis.com/SiteExplorerService/V1/inlinkData?appid=c16bCwrV34GNSmI05no_7uRU89ME_fvJ8G8958jsdclgZl8MJHCebcluFX6RsHj0&output=php&query=';*/
	var $address = '';
	var $home = 'http://siteexplorer.search.yahoo.com/search?p=%s&bwm=i&fr=sfp';
	var $domain  = '';

	/**
	 * WPSDTechnoratiRank.
	 * @param string $domain
	 * @param boolean $curl
	 */
	function WPSDYahooRank($domain, $curl = false) {
		parent::WPSDStats();
		
		//$this->address .= $domain;
	
		$this->domain = $domain;
		
		if($this->isOutdated()) {
			
			$url = sprintf($this->home, str_replace('http://', '', $this->domain));
			
			$this->address = $url;
						
			$this->xml = str_replace(',', '', strip_tags($this->fetchDataRemote($url, true, 2, false)));
			
			//echo $this->xml;
			
			$this->set();
			
		} else {
			
			$this->set_cached();
		}
	}

	/**
	 * Set data.
	 * @access protected
	 */
	function set() {
		
		/*if(isset($this->xml['ResultSet']['totalResultsAvailable'])) {
		
			$linksin = $this->xml['ResultSet']['totalResultsAvailable'];
			
			$this->values['linksin'] = ($linksin != '')?$linksin:0;
			
			$this->set_cache('yahoo_linksin', $this->values['linksin']);
		} 
		else {
			
			$this->values['linksin'] = 0;
			
			$this->set_cache('yahoo_linksin', '0');
		}*/
			
		preg_match("@Inlinks\s\(([0-9]+)\)@", $this->xml, $matches);
		
		$linksin = number_format($matches[1]);
			
		$this->values['linksin'] = $linksin;
			
		$this->set_cache('yahoo_linksin', $this->values['linksin']);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	function set_cached() {
		$this->values['linksin'] = $this->get_cache('yahoo_linksin');
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
	function getRank() {
		return $this->get('rank');
	}

	/**
	 * Get Links in.
	 * @return linksin
	 * @access public
	 */
	function getLinksIn() {
		return $this->get('linksin');
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
	 * Get compete.com home address.
	 * @return url
	 * @access public
	 */
	function getHomeAddress() {
		return $this->home . $this->domain;
	}
}
?>