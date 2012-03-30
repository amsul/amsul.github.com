<?php
/**
 * WPSDIdentica.
 * @author dligthart <info@daveligthart.com>
 * @version 0.2
 * @package wp-stats-dashboard
 */
class WPSDIdentica extends WPSDStats {

	var $xml;
	var $values;
	var $address = '';
	var $un;
	
	/**
	 * WPSDIdentica function.
	 * 
	 * @access public
	 * @return void
	 */
	function WPSDIdentica() {
		
		parent::WPSDStats();
		
		$form = new WPSDAdminConfigForm();
		
		$this->un = trim($form->getWpsdIdenticaUn());
		
		if('' != $this->un) {
			
			$this->address = 'http://identi.ca/' . $this->un; 
			
			if($this->isOutdated()) {
				
				$this->xml = $this->fetchDataRemote($this->address);
			
				$this->set();
				
			} else {
				
				$this->set_cached();
			}
		}
	}
	
	/**
	 * Is enabled.
	 * 
	 * @return boolean
	 */
	function isEnabled() {
		return ('' != $this->un);
	}
	
	/**
	 * set function.
	 * 
	 * @access public
	 * @return void
	 */
	function set() {
		
		preg_match_all('@([0-9]+)</h2>@', $this->xml, $matches);
				
		$this->values['subscriptions'] = $matches[1][0];
		
		$this->values['subscribers'] = $matches[1][1];
		
	//	print_r($matches);
				
		$this->set_cache('identica_subscrip', $this->values['subscriptions']);
		
		$this->set_cache('identica_subscrib', $this->values['subscribers']);
		
	}
	
	/**
	 * set_cached function.
	 * 
	 * @access public
	 * @return void
	 */
	function set_cached() {
	
		$this->values['subscriptions'] = $this->get_cache('identica_subscrip');
		
		$this->values['subscribers'] = $this->get_cache('identica_subscrib');
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
	 * getSubscriptions function.
	 * 
	 * @access public
	 * @return integer number of subscriptions
	 */
	function getSubscriptions() {
		return $this->get('subscriptions');
	}
	
	/**
	 * getSubscribers function.
	 * 
	 * @access public
	 * @return integer number of subscribers
	 */
	function getSubscribers() {
		return $this->get('subscribers');
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