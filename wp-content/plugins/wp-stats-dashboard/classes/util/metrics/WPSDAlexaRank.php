<?php
/**
 * WPSDAlexaRank. Ranking on Alexa.
 * @author dligthart <info@daveligthart.com>
 * @version 0.1
 * @package wp-stats-dashboard
 */
class WPSDAlexaRank extends WPSDStats {

	/**
	 * xml
	 * 
	 * @var mixed
	 * @access public
	 */
	var $xml;
	/**
	 * values
	 * 
	 * @var mixed
	 * @access public
	 */
	var $values;
	/**
	 * address
	 * 
	 * (default value: 'http://data.alexa.com/data?cli=10&dat=snbamz&url=')
	 * 
	 * @var string
	 * @access public
	 */
	var $address = 'http://data.alexa.com/data?cli=10&dat=snbamz&url=';

	/**
	 * WPSDAlexaRank.
	 * @param string $domain
	 * @param boolean $curl
	 */
	function WPSDAlexaRank($domain, $curl = false) {
		
		parent::WPSDStats();
		
		$domain = $this->getNormalizedUrl($domain);
		
		if($this->isOutdated() && '' != $domain) {
	
			$this->xml = $this->fetchDataRemote($this->address . $domain);
					
			$this->set();
		} 
		else {
			
			$this->set_cached();
		}	
	}

	/**
	 * set function.
	 * 
	 * @access public
	 * @return void
	 */
	function set() {
		$this->values['rank'] = (preg_match('/POPULARITY URL="[a-z0-9\\-\\.\\/]{1,}" TEXT="([0-9]{1,12})"/',$this->xml,$regs) ? number_format($regs[1]) : 0);
		$this->values['reach'] = (preg_match('/REACH RANK="([0-9]{1,12})"/',$this->xml,$regs) ? number_format($regs[1]) : 0);
		$this->values['linksin'] = (preg_match('/LINKSIN NUM="([0-9]{1,12})"/',$this->xml,$regs) ? number_format($regs[1]) : 0);
		
		$this->set_cache('alexa_rank', $this->values['rank']);
		$this->set_cache('alexa_reach', $this->values['reach']);
		$this->set_cache('alexa_linksin', $this->values['linksin']);
	}
	
	/**
	 * set_cached function.
	 * 
	 * @access public
	 * @return void
	 */
	function set_cached() {
		$this->values['rank'] = $this->get_cache('alexa_rank');
		$this->values['reach'] = $this->get_cache('alexa_reach');
		$this->values['linksin'] = $this->get_cache('alexa_linksin');
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
	 * Get reach.
	 * @return reach
	 * @access public
	 */
	function getReach() {
		return $this->get('reach');
	}

	/**
	 * Get Links in.
	 * @return linksin
	 * @access public
	 */
	function getLinksIn() {
		return $this->get('linksin');
	}	
}
?>