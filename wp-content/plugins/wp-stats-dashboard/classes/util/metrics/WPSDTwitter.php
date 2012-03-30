<?php
/**
 * WPSDTwitter
 *
 * @author dligthart <info@daveligthart.com>
 * @version 0.5
 * @package wp-stats-dashboard
 */
class WPSDTwitter extends WPSDStats {

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
	 * (default value: '')
	 * 
	 * @var string
	 * @access public
	 */
	var $address = '';
	/**
	 * un
	 * 
	 * @var mixed
	 * @access public
	 */
	var $un;
	
	/**
	 * WPSDTwitter function.
	 * 
	 * @access public
	 * @param string $username. (default: '')
	 * @return void
	 */
	function WPSDTwitter($username = '') {
		
		parent::WPSDStats();
		
		if('' == $username) { 
		
			$form = new WPSDAdminConfigForm();
		
			$this->un = trim($form->getWpsdTwitterUn());
		
		} else {
			
			$this->un = $username;
		}
		
		if('' != $this->un) {
			
			$this->address = 'http://twitter.com/' . $this->un; 
			
			if($this->is_cache_outdated('twitter', $this->un)) {
				
				$api_url_request = "https://api.twitter.com/1/users/lookup.json?screen_name={$this->un}&include_entities=true";
				
				$this->xml = str_replace(',', '', $this->fetchDataRemote($api_url_request));
				
				$this->set();
				
				$this->updated_cache('twitter', $this->un);
				
			} else {
				
				$this->set_cached();
			}
		}
	}
	
	/**
	 * isEnabled function.
	 * 
	 * @access public
	 * @return void
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
		
		//Set followers.
		$this->values['f'] = $this->get_count('followers_count');
		
		$this->update_cache('twitter_f_' . $this->un, $this->values['f']);
		
		// Set following.
		$this->values['fi'] = $this->get_count('friends_count');
		
		$this->update_cache('twitter_fi_' . $this->un, $this->values['fi']);
		
		// Set list count.
		$this->values['l'] = $this->get_count('listed_count');
		
		$this->update_cache('twitter_l_' . $this->un, $this->values['l']);
		
		// Set tweet count.
		$this->values['t'] = $this->get_count('statuses_count');
		
		$this->update_cache('twitter_t_' . $this->un, $this->values['t']);

	}
	
	/**
	 * get_count function.
	 * 
	 * @access public
	 * @param mixed $type
	 * @return integer
	 */
	function get_count($type) {
							
		$matches = array();
		
		preg_match("/{$type}\":(\d+)/", $this->xml, $matches);
		
		return number_format($matches[1]);
	}
	
	/**
	 * set_cached function.
	 * 
	 * @access public
	 * @return void
	 */
	function set_cached() {
		$this->values['f'] = $this->get_cached('twitter_f_' . $this->un);
		$this->values['l'] = $this->get_cached('twitter_l_' . $this->un);
		$this->values['fi'] = $this->get_cached('twitter_fi_' . $this->un);
		$this->values['t'] = $this->get_cached('twitter_t_' . $this->un);
	}
	
	/**
	 * get function.
	 * 
	 * @access public
	 * @param mixed $value
	 * @return void
	 */
	function get($value){
		return (isset($this->values[$value]) ? $this->values[$value] : '0');
	}

	/**
	 * Get rank.
	 * @return integer
	 * @access public
	 */
	function getFollowers() {
		return $this->get('f');
	}
	
	/**
	 * getFollowing function.
	 * 
	 * @access public
	 * @return void
	 */
	function getFollowing() {
		return $this->get('fi');
	}
	
	/**
	 * getLists function.
	 * 
	 * @access public
	 * @return void
	 */
	function getLists() {
		return $this->get('l');
	}
	
	/**
	 * getTweets function.
	 * 
	 * @access public
	 * @return void
	 */
	function getTweets() {
		return $this->get('t');
	}
	
	/**
	 * getRatio function.
	 * 
	 * @access public
	 * @return float
	 */
	function getRatio() {
		
		$followers = $this->getFollowers();

		$following  = $this->getFollowing();
					
		if($followers > 0) { 
						
			return round($following / $followers, 2);
		} 
				
		return 1;
	}

	/**
	 * getAddress function.
	 * 
	 * @access public
	 * @return string
	 */
	function getAddress() {
		return $this->address;
	}
	
	/**
	 * getUsername function.
	 * 
	 * @access public
	 * @return string
	 */
	function getUsername() {		
		return $this->un;
	}
}
?>