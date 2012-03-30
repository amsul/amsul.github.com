<?php
/**
 * WPSDEmpireAvenue.
 * 
 * @author dligthart <info@daveligthart.com>
 * @version 0.1
 * @package wp-stats-dashboard
 */
class WPSDEmpireAvenue extends WPSDStats {

	var $xml;
	var $values;
	var $address = '';
	var $un = '';
	var $pw = '';
	var $key = 'b8d2a6dc3724f39441766a0559f6c944293c82f8654b8a7708';
	var $auth;
	
	/**
	 * WPSDEmpireAvenue.
	 * 
	 * @param boolean $curl
	 */
	function WPSDEmpireAvenue($curl = false) {
		
		parent::WPSDStats();
		
		$form = new WPSDAdminConfigForm();
		
		$this->un = trim($form->getWpsdEaveUn());
		
		$this->pw = trim($form->getWpsdEavePw());
		
		$this->auth = "?apikey={$this->key}&username={$this->un}&password={$this->pw}";
				
		if('' != $this->un && '' != $this->pw) {
		
			if($this->isOutdated()) {
				
				$this->xml = $this->fetchDataRemote($this->getServiceUri('info'));	
				
				$this->set();
				
			} else {
				
				$this->set_cached();
			} 
		}
	}
	
	/**
	 * Get highest share price.
	 * 
	 * @return integer share price
	 * @access public
	 */
	function getSharePriceHighest() {
		return 0;
	}
	
	/**
	 * Get latest stock price.
	 * 
	 * @return string price
	 */
	function getLastTrade() {
		
		return $this->get('last_trade');	
	}
	
	/**
	 * Get share holders count.
	 * 
	 * @return integer number of shareholders
	 * @access public
	 */
	function getShareHolders() {
		
		return $this->get('shareholders');
	}
	
	/**
	 * Get service uri.
	 * 
	 * @param string $type
	 * @return string
	 * @access private
	 */
	function getServiceUri($type) {
		
		$url = '';
		
		switch($type) {
			
			case 'info':
				
				$url = 'https://api.empireavenue.com/profile/info' . $this->auth;
				
				break;
			
			case 'rankings':
				
				$url = 'https://api.empireavenue.com/profile/rankings' . $this->auth;
				
				break;
		}
		
		return $url;
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
		
		$this->values['last_trade'] = $this->getCount('last_trade');
		
		$this->set_cache('eave_last_trade', $this->values['last_trade']);
		
		$this->values['shareholders'] = $this->getCount('shareholders_count');

		$this->set_cache('eave_shareholders', $this->values['shareholders']);
	}
	
	/**
	 * Get count.
	 * 
	 * @param string $type
	 * @return integer count
	 * @access private
	 */
	function getCount($type) {
	
		preg_match_all("@\"{$type}\":\"(.*?)\",@si", $this->xml, $matches);
		
		//print_r($matches);
		
		return $matches[1][0];
	}
	
	/**
	 * Set cached.
	 */
	function set_cached() {
		
		$this->values['last_trade'] = $this->get_cache('eave_last_trade');
		
		$this->values['shareholders'] = $this->get_cache('eave_shareholders');
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
	 * Get address.
	 * @return address
	 * @access public
	 */
	function getAddress() {
		return 'http://www.empireavenue.com';
	}
}
?>