<?php
/**
 * WPSDDiigo.
 * @author dligthart <info@daveligthart.com>
 * @version 0.2
 * @package wp-stats-dashboard
 */
class WPSDDiigo extends WPSDStats {

	var $xml;
	var $values;
	var $address = '';
	var $un;
	
	/**
	 * WPSDDiigo.
	 * 
	 * @param boolean $curl
	 */
	function WPSDDiigo($curl = false) {
		
		parent::WPSDStats();
		
		$form = new WPSDAdminConfigForm();
		
		$this->un = trim($form->getWpsdDiigoUn());
		
		if('' != $this->un) {
			
			$this->address = 'http://www.diigo.com/profile/' . $this->un; 
			
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
		
		//Set public bookmark count.
		$this->values['bookmarks'] = $this->get_count('/user', $this->xml);
		
		$this->set_cache('diigo_b', $this->values['bookmarks']);
	
		$this->values['friends'] = $this->get_count('/friends', $this->xml);

		$this->set_cache('diigo_fr', $this->values['friends']);
		
		$this->values['followers'] = $this->get_count('/friends/follower', $this->xml);
		
		$this->set_cache('diigo_f', $this->values['followers']);
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

		preg_match("@<a href=\"{$type}/{$this->un}\"><strong>([0-9]+)</strong>@si", $data, $matches);
	
		return number_format($matches[1]);
	}
	
	/**
	 * Set cached.
	 */
	function set_cached() {
		$this->values['bookmarks'] = $this->get_cache('diigo_b');
		$this->values['friends'] = $this->get_cache('diigo_fr');
		$this->values['followers'] = $this->get_cache('diigo_f');
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
	 * Get bookmarks.
	 * 
	 * @return integer bookmarks
	 * @access public
	 */
	function getBookmarks() {
		return $this->get('bookmarks');
	}
	
	/**
	 * Get Friends.
	 * 
	 * @return integer friends
	 * @access public 
	 */
	function getFriends() {
		return $this->get('friends');
	}
	
	/**
	 * Get followers.
	 * 
	 * @return integer followers
	 * @access public
	 */
	function getFollowers() {
		return $this->get('followers');
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