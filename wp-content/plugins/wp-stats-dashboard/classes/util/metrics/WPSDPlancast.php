<?php
/**
 * WPSDPlancast.
 * @author dligthart <info@daveligthart.com>
 * @version 0.2
 * @package wp-stats-dashboard
 */
class WPSDPlancast extends WPSDStats {

	var $xml;
	var $values;
	var $address = '';
	var $un;
	
	/**
	 * WPSDPlancast.
	 */
	function WPSDPlancast() {
		parent::WPSDStats();
		
		$form = new WPSDAdminConfigForm();
		
		$this->un = trim($form->getWpsdPlancastUn());
		
		if('' != $this->un) {
			
			$this->address = 'http://plancast.com/' . $this->un; 
			
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
	 * Set data.
	 */
	function set() {
		
		$this->values['subscribers'] = $this->get_count('subscribers_count', $this->xml);
		
		$this->set_cache('plancast_f', $this->values['subscribers']);
		
		$this->values['subscriptions'] = $this->get_count('subscriptions_count', $this->xml);
		
		$this->set_cache('plancast_s', $this->values['subscriptions']);
	}
	
	/**
	 * Get count.
	 * 
	 * @param $type
	 * @param $data
	 * @return integer count
	 * @access protected
	 */
	function get_count($type, $data) {

		preg_match("@id=\"{$type}\">([0-9]+)</span>@si", $data, $matches);
					
		return number_format($matches[1]);
	}
	
	/**
	 * Set cached.
	 */
	function set_cached() {
		
		$this->values['subscribers'] = $this->get_cache('plancast_f');
		
		$this->values['subscriptions'] = $this->get_cache('plancast_s');
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
	 * Get subscribers.
	 * @return integer count
	 */
	function getSubscribers() {
		return $this->get('subscribers');
	}
	
	/**
	 * Get subscriptions.
	 * @return integer count
	 */
	function getSubscriptions() {
		return $this->get('subscriptions');
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