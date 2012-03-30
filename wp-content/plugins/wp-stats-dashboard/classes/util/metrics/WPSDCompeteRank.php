<?php
/**
 * WPSDCompeteRank. Ranking on compete.com.
 * @author dligthart <info@daveligthart.com>
 * @version 0.2
 * @package wp-stats-dashboard
 */
class WPSDCompeteRank extends WPSDStats {

	var $xml;
	var $values;
	var $address = '';
	var $home = 'http://snapshot.compete.com/';
	var $domain  = '';
	/**
	 * WPSDTechnoratiRank.
	 * @param string $domain
	 * @param boolean $curl
	 */
	function WPSDCompeteRank($domain, $curl = false) {
		
		parent::WPSDStats();
		
		//$this->domain = $this->getNormalizedUrl($domain);
		
		$parseUrl = parse_url(trim($domain));
		
		$this->domain = $this->getHost($parseUrl);
			
	//	$this->address = "http://api.compete.com/fast-cgi/MI?d={$this->domain}&ver=3&apikey=f4fqrkzqbn5upk375ru3fv35&size=large";
		
		//echo $this->domain;
		
		$this->address = 'http://apps.compete.com/sites/'.$this->domain.'/trended/rank/?apikey=67b79113e4e67d3fdb2b5efd9805045e';
			
		if($this->isOutdated() && '' != $this->domain) {
			
			$this->xml = $this->fetchDataRemote($this->address);
			
			$this->set();
		} 
		else {
			
			$this->set_cached();
		}
	}

	/**
	 * Set data.
	 * @access protected
	 */
	function set() {
	
		$json = new Services_JSON();
		
		$result = $json->decode($this->xml);
		
		$rank = $result->data->trends->rank[count($result->data->trends->rank) - 1];
		
		$rank = $rank->value;
		
		//$rank = $this->getTagValue('ranking', $this->xml);
		$this->values['rank'] = ($rank != '')?$rank:0;

	//	$people = $this->getTagValue('count', $this->xml);
	//	$this->values['linksin'] = ($people != '')?$people:0;
		
		$this->set_cache('compete_rank', $this->values['rank']);
	//	$this->set_cache('compete_linksin', $this->values['linksin']);
	}
	
	/**
	 * Set cached.
	 * 
	 * @return unknown_type
	 */
	function set_cached() {
		
		$this->values['rank'] = $this->get_cache('compete_rank');
		
		if(!is_string($this->values['rank'])) $this->values['rank'] = '0';
		
		//$this->values['linksin'] = $this->get_cache('compete_linksin');
	}
	
	/**
	 * Get tag value.
	 * @param string $tagname
	 * @access protected
	 */
	function getTagValue($tag, $xml) {
		$preg = "|<$tag>(.*?)</$tag>|s";
		preg_match_all($preg, $xml, $tags);
		return trim($tags[1][0]);
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
		return number_format($this->get('rank'));
	}

	/**
	 * Get Links in.
	 * @return linksin
	 * @deprecated 
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
		$domain = str_replace('http://', '', $this->domain);
		$domain = str_replace('https://', '', $domain);
		$domain = str_replace('www.', '', $domain);
		return $this->home . $domain;
	}
}
?>