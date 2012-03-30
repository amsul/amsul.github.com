<?php
/**
 * WPSDBattlenet.
 * @author dligthart <info@daveligthart.com>
 * @version 0.1
 * @package wp-stats-dashboard
 */
class WPSDBattlenet extends WPSDStats {

	var $xml;
	var $values;
	var $address;
	var $uri;
	
	/**
	 * WPSDBattlenet.
	 * @param string $domain
	 * @param boolean $curl
	 */
	function WPSDBattlenet($curl = false) {
		
		parent::WPSDStats();
		
		$form = new WPSDAdminConfigForm();
		
		$this->uri = trim($form->getWpsdBattlenetUri());
		
		if('' != $this->uri) {
			
			$this->address = $this->uri;
			
			if($this->isOutdated()) {
				
				$this->xml = $this->fetchDataRemote($this->address, true, 1, true, 'int-SC2=1; perm=1; Domain=battle.net; Path=/');
			
				//echo $this->address;
			
				//var_dump($this->xml);
				
				$this->set();
				
			} else {
				
				$this->set_cached();
			}
		}
	}
	
	function isEnabled() {
		
		return ('' != $this->uri);
	}
	
	/**
	 * Set data.
	 */
	function set() {
		
		preg_match('@<h3>([0-9]+)</h3>@si', $this->xml, $matches);
		
		$rank = $matches[1];
		
		preg_match_all('@<span>([0-9]+)</span>@si', $this->xml, $matches);
		
		//print_r($matches);
		
		// Set rank.
		$this->values['rank'] = $rank;
				
		$this->set_cache('bn_rank', $this->values['rank']);
		
		$this->values['lg'] = $matches[0][0]; //league games.
		
		$this->values['cg'] = $matches[0][1]; //Custom Games.
		
		$this->values['ca'] = $matches[0][2]; //Co-Op vs AI.
		
		$this->values['ffa'] = $matches[0][3]; //FFA.
		
		$this->set_cache('bn_lg', $this->values['lg']);
		
		$this->set_cache('bn_cg', $this->values['cg']);
		
		$this->set_cache('bn_ca', $this->values['ca']);
		
		$this->set_cache('bn_ffa', $this->values['ffa']);
		 
		// Set wins.
		/*$this->values['wins'] = $this->get_wins($this->xml);
		
		$this->set_cache('bn_wins', $this->values['wins']);
		
		// Set games.
		$this->values['games'] = $this->get_games($this->xml);
		
		$this->set_cache('bn_games', $this->values['games']); */
	}
	
	/**
	 * Get stat count.
	 * 
	 * @param string $data
	 * @return string number
	 */
	function get_rank($data) {
		
		preg_match("@<span>\#([0-9]+)</span>@si", $data, $matches);
		
		return number_format($matches[1]);
	}
	
	/**
	 * Get count.
	 * 
	 * @param $type
	 * @param $data
	 * @return unknown_type
	 */
	function get_wins($data) {

		preg_match("@<span class=\"totals\">([0-9])+ Wins</span>@si", $data, $matches);
					
		return number_format($matches[1]);
	}
	
	/**
	 * get_games function.
	 * 
	 * @access public
	 * @param mixed $data
	 * @return void
	 */
	function get_games($data) {

		preg_match("@<span class=\"totals\">([0-9])+ Games</span>@si", $data, $matches);
					
		return number_format($matches[1]);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	function set_cached() {
	
		$this->values['rank'] = $this->get_cache('bn_rank');
		
		$this->values['lg'] = $this->get_cache('bn_lg');
		
		$this->values['cg'] = $this->get_cache('bn_cg');
		
		$this->values['ca'] = $this->get_cache('bn_ca');
		
		$this->values['ffa'] = $this->get_cache('bn_ffa');
				
		/*$this->values['wins'] = $this->get_cache('bn_wins');
		
		$this->values['games'] = $this->get_cache('bn_games');*/
	}
	
	/**
	 * Get data.
	 
	 * @return integer value
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
	 * getGames function.
	 * 
	 * @access public
	 * @return void
	 */
	function getGames() {
	
		return $this->get('lg');
	}
	
	/**
	 * getCustomGames function.
	 * 
	 * @access public
	 * @return void
	 */
	function getCustomGames() {
		
		return $this->get('cg');
	}
	
	/**
	 * getCoop function.
	 * 
	 * @access public
	 * @return void
	 */
	function getCoop() {
		
		return $this->get('ca');
	}
	
	/**
	 * getFFA function.
	 * 
	 * @access public
	 * @return void
	 */
	function getFFA() {
		
		return $this->get('ffa');
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