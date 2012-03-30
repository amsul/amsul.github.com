<?php
/**
 * WPSDSociety.
 *
 * @author dligthart <info@daveligthart.com>
 * @version 0.1
 * @package wp-stats-dashboard
 */
class WPSDSociety extends WPSDStats {

	var $xml;
	var $values;
	var $address = '';
	var $un;
	
	function WPSDSociety() {

		parent::WPSDStats();

		$form = new WPSDAdminConfigForm();

		$this->un = trim($form->getWpsdSocietyUn());

		if('' != $this->un) {

			$this->address = 'http://www.society.me/' . $this->un;
				
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

		$matches = array();

		preg_match_all("@<b>([0-9]+)</b>@i", $this->xml, $matches);
	
		//print_r($matches);

		$this->values['followers'] = number_format($matches[1][0]);
			
		$this->set_cache('society_followers', $this->values['followers']);
		
		$this->values['following'] = number_format($matches[1][1]);
			
		$this->set_cache('society_following', $this->values['following']);

		$this->values['likes'] = number_format($matches[1][2]);
			
		$this->set_cache('society_likes', $this->values['likes']);
		
		$this->values['answered'] = number_format($matches[1][3]);
			
		$this->set_cache('society_answered', $this->values['answered']);
	}

	/**
	 * Set cached.
	 */
	function set_cached() {

		$this->values['followers'] = $this->get_cache('society_followers');

		$this->values['following'] = $this->get_cache('society_following');
		
		$this->values['likes'] = $this->get_cache('society_likes');
		
		$this->values['answered'] = $this->get_cache('society_answered');
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
	 * Get followers
	 *
	 * @return integer number of followers
	 * @access public
	 */
	function getFollowers() {
		return $this->get('followers');
	}
	
	/**
	 * Get following
	 *
	 * @return integer number of people you follow
	 * @access public
	 */
	function getFollowing() {
		return $this->get('following');
	}

	/**
	 * Get number of posts.
	 *
	 * @return integer posts
	 * @access public
	 */
	function getLikes() {
		return $this->get('likes');
	}

	/**
	 * Get number of answers given.
	 * 
	 * @return integer answers
	 * @access public
	 */
	function getAnswered() {
		return $this->get('answered');
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