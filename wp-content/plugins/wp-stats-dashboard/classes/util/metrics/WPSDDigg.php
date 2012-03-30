<?php
/**
 * WPSDDigg.
 * @author dligthart <info@daveligthart.com>
 * @version 0.3
 * @package wp-stats-dashboard
 */
class WPSDDigg extends WPSDStats {
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
	 * WPSDDigg.
	 * @param string $domain
	 * @param boolean $curl
	 */
	function WPSDDigg() {
		
		parent::WPSDStats();
		
		$form = new WPSDAdminConfigForm();
		
		$this->un = trim($form->getWpsdDiggUn());
		
		if('' != $this->un) {
					
			$this->address = 'http://digg.com/' . $this->un;
				
			if($this->isOutdated()) {
				
				$this->xml = $this->fetchDataRemote($this->address);
				
				$this->set();
				
				$this->set_results();
				
			} else {
				
				$this->set_cached();
			}
		}
	}
	
	/**
	 * set_results function.
	 * 
	 * @access public
	 * @return void
	 */
	function set_results() {
	
		$url = get_bloginfo('url');
		
		$data = $this->fetchDataRemote("http://digg.com/search?q=site:{$url}");
		
		preg_match_all('@<dt>([0-9]+)</dt>@', $data, $matches);
		
		//var_dump($matches);
		
		$this->values['results'] = $matches[1][0];
		
		$this->set_cache('digg_results', $this->values['results']);
	}
	
	/**
	 * set function.
	 * 
	 * @access public
	 * @return void
	 */
	function set() {
		
		preg_match_all("@<strong>([0-9]+)</strong>@i", $this->xml, $matches);
		
		$this->values['diggs'] = $matches[1][0];
		$this->values['comments'] = $matches[1][1];
		$this->values['submissions'] = $matches[1][2];		
		$this->values['followers'] = $matches[1][3];
		$this->values['following'] = $matches[1][4];
		$this->values['daily_diggs'] = $matches[1][5];		
		$this->values['daily_submissions'] = $matches[1][6];
		$this->values['pop_stories'] = $matches[1][7];
		$this->values['pop_ratio'] = $matches[1][8];
		
		$this->set_cache('digg_followers', $this->values['followers']);
		$this->set_cache('digg_following', $this->values['following']);
		$this->set_cache('digg_diggs', $this->values['diggs']);
		$this->set_cache('digg_comments', $this->values['comments']);
	}
	
	/**
	 * set_cached function.
	 * 
	 * @access public
	 * @return void
	 */	
	function set_cached() {
		$this->values['followers'] = $this->get_cache('digg_followers');
		$this->values['following'] = $this->get_cache('digg_following');
		$this->values['diggs'] = $this->get_cache('digg_diggs');
		$this->values['comments'] = $this->get_cache('digg_comments');
		$this->values['results'] = $this->get_cache('digg_results');
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
	 * Get followers.
	 * 
	 * @return unknown_type
	 */
	function getFollowers() {
		
		return $this->values['followers'];
	}
	
	/**
	 * Get following.
	 * 
	 * @return unknown_type
	 */
	function getFollowing() {
		
		return $this->values['following'];
	}
	
	/**
	 * Get diggs.
	 * 
	 * @return unknown_type
	 */
	function getDiggs() {
		
		return $this->values['diggs'];
	}
	
	/**
	 * Get comments.
	 * 
	 * @return unknown_type
	 */
	function getComments() {
		
		return $this->values['comments'];
	}
	
	/**
	 * getResults function.
	 * 
	 * @access public
	 * @return integer number of results
	 */
	function getResults() {
		
		return $this->values['results'];
	}
	
	/**
	 * Get rank.
	 * @return rank
	 * @access public
	 * @deprecated since digg 2.0
	 */
	function getRank() {
		return $this->get('rank');
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