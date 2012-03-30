<?php
/**
 * WPSDFriendFeed.
 * 
 * @author dligthart <info@daveligthart.com>
 * @version 0.1
 * @package wp-stats-dashboard
 */
class WPSDFriendFeed extends WPSDStats {

	var $xml;
	var $values;
	var $address = '';
	var $un;
	
	/**
	 * WPSDFriendFeed.
	 * 
	 * @param boolean $curl
	 */
	function WPSDFriendFeed() {
		
		parent::WPSDStats();
		
		$form = new WPSDAdminConfigForm();
		
		$this->un = trim($form->getWpsdFriendFeedUn());
		
		if('' != $this->un) {
			
			$this->address = 'http://friendfeed.com/' . $this->un; 
			
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

		$this->values['subscribers'] = $this->get_count('subscribers', $this->xml);

		$this->set_cache('friendfeed_subscrib', $this->values['subscribers']);
		
		$this->values['subscriptions'] = $this->get_count('subscriptions', $this->xml);

		$this->set_cache('friendfeed_subscrip', $this->values['subscriptions']);
		
		$this->values['comments'] = $this->get_count('comments', $this->xml);

		$this->set_cache('friendfeed_comments', $this->values['comments']);
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
		
		preg_match("@<span class=\"num\">([0-9]+)</span> {$type}</a>@si", $data, $matches);
		
	//	print_r($matches);
		
		return number_format($matches[1]);
	}
		
	/**
	 * Set cached.
	 * @access private
	 */
	function set_cached() {
		
		$this->values['subscribers'] = $this->get_cache('friendfeed_subscrib');
		
		$this->values['subscriptions'] = $this->get_cache('friendfeed_subscrip');
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
	 * Get Friends.
	 * 
	 * @return integer subscribers
	 * @access public 
	 */
	function getSubscribers() {
		
		return $this->get('subscribers');
	}
	
	/**
	 * Get Subscriptions.
	 * 
	 * @return integer number of subscriptions
	 */
	function getSubscriptions() {
		
		return $this->get('subscriptions');
	}
	
	/**
	 * Get comments.
	 * 
	 * @return integer number of comments
	 */
	function getComments() {
		
		return $this->get('comments');
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