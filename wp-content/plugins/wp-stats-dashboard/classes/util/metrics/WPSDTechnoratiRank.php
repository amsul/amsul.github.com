<?php
/**
 * WPSDTechnoratiRank. Ranking on Technorati.
 * @author dligthart <info@daveligthart.com>
 * @version 0.3
 * @package wp-stats-dashboard
 */
class WPSDTechnoratiRank extends WPSDStats {

	var $xml;
	var $values;
	var $address = '';

	/**
	 * WPSDTechnoratiRank.
	 * @param string $domain
	 * @param boolean $curl
	 */
	function WPSDTechnoratiRank($domain, $curl = false) {	
		
		parent::WPSDStats();

		//$this->address = 'http://api.technorati.com/bloginfo?key=&url=' . $domain;

		$domain = $this->getHost(parse_url($domain));
		
		$this->address = 'http://technorati.com/blogs/' . $domain;
		
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
		
		//get the html code of the URL
		$html_values=$this->xml;

		//get the string within <div class="rank"></div>
		preg_match("@Technorati Authority:(.*?)</strong>@si", $html_values, $matches);

		//strip out of the HTML element from the matches
		$rankvalue=strip_tags(trim($matches[1]));

		//geting the the rank values out of the string
		//preg_match("/(\d+(,\d+)?(,\d+)?)/", $rankvalue, $matches);

		//Set the rank.
		$this->values['rank'] = number_format($rankvalue); //$matches[0];
		
		$this->set_cache('technorati_rank', $this->values['rank']);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	function set_cached() {
		
		$this->values['rank'] = ($this->get_cache('technorati_rank')) ? $this->get_cache('technorati_rank') : '0';
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
	 * Get address.
	 * @return address
	 * @access public
	 */
	function getAddress() {
		return $this->address;
	}
}
?>