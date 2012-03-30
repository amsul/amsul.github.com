<?php
/**
 * WPSDMylikes.
 * 
 * @author dligthart <info@daveligthart.com>
 * @version 0.1
 * @package wp-stats-dashboard
 */
class WPSDMylikes extends WPSDStats {

	var $xml;
	var $values;
	var $address = '';
	var $un;
	
	/**
	 * WPSDMylikes.
	 * @param string $domain
	 * @param boolean $curl
	 */
	function WPSDMylikes($curl = false) {
		
		parent::WPSDStats();
		
		$form = new WPSDAdminConfigForm();
		
		$this->un = trim($form->getWpsdMylikesUn());
		
		if('' != $this->un) {
			
			$this->address = 'http://mylikes.com/' . $this->un; 
			
			if($this->isOutdated()) {
				
				$this->xml = $this->fetchDataRemote($this->address);
				
				$this->set();
				
			} else {
				
				$this->set_cached();
			}
		}
	}
	
	/**
	 * 
	 * Is enabled.
	 */
	function isEnabled() {
		
		return ('' != $this->un);
	}
	
	/**
	 * Set data.
	 * @access private
	 */
	function set() {
	
		$this->values['likes'] = $this->get_count('like', $this->xml);
		
		$this->set_cache('mylikes_likes', $this->values['likes']);
		
		$this->values['flikes'] = $this->get_count('item', $this->xml);
		
		$this->set_cache('mylikes_flikes', $this->values['flikes']);
		
		$this->values['comments'] = $this->get_count('comment', $this->xml);
		
		$this->set_cache('mylikes_comments', $this->values['comments']);
		
		$this->values['followers'] = $this->get_count('person', $this->xml);
		
		$this->set_cache('mylikes_followers', $this->values['followers']);
		
		$this->values['influence'] = $this->get_influence_score($this->xml);
		
		$this->set_cache('mylikes_influence', $this->values['influence']);
		
	}

	/**
	 * Get count.
	 * 
	 * @param $type
	 * @param $data
	 * @return unknown_type
	 * @access private
	 */
	function get_count($type, $data) {
			
		preg_match("@([0-9]+) {$type}@si", $data, $matches);
	
		return number_format($matches[1]);
	}
	
	/**
	 * 
	 * Get influence score.
	 * @return integer score
	 * @access private
	 */
	function get_influence_score($data) {
		
		preg_match("@Influence score: ([0-9]+)@si", $data, $matches);
	
		return number_format($matches[1]);
	}
	
	/**
	 * 
	 * Get data from cache.
	 */
	function set_cached() {
		$this->values['likes'] = $this->get_cache('mylikes_likes');
		$this->values['flikes'] = $this->get_cache('mylikes_flikes');
		$this->values['comments'] = $this->get_cache('mylikes_comments');
		$this->values['followers'] = $this->get_cache('mylikes_followers');
		$this->values['influence'] = $this->get_cache('mylikes_influence');
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
	function getFollowers() {
		return $this->get('followers');
	}
	
	/**
	 * Get likes
	 * @return integer likes
	 */
	function getLikes() {
		return $this->get('likes');
	}
	
	/**
	 * Get first likes.
	 * @return integer first likes
	 */
	function getFlikes() {
		return $this->get('flikes');
	}
	
	/**
	 * Get comments.
	 * @return integer comments
	 */
	function getComments() {
		return $this->get('comments');
	}
	
	/**
	 * 
	 * Get influence score.
	 * @return integer score
	 */
	function getInfluence() {
		return $this->get('influence');
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