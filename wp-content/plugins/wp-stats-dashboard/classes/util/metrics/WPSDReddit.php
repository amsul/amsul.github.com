<?php
/**
 * WPSDReddit.
 * @author dligthart <info@daveligthart.com>
 * @version 0.2
 * @package wp-stats-dashboard
 */
class WPSDReddit extends WPSDStats {

	var $xml;
	var $xml2;
	var $values;
	var $address = '';
	var $domain = '';
	var $profile_address = '';
	/**
	 * WPSDReddit.
	 * @param string $domain
	 * @param boolean $curl
	 */
	function WPSDReddit($domain, $curl = false) {	
		
		parent::WPSDStats();

		$domain = $this->getNormalizedUrl($domain);
		
		$this->address = "http://www.reddit.com/api/info.json?url=" . $domain;
		
		$form = new WPSDAdminConfigForm();
		
		$username = $form->getWpsdRedditUn();
		
		$this->profile_address = "http://www.reddit.com/user/{$username}/";
		
		$this->domain = $domain;
		
		if($this->isOutdated() && '' != $domain) {
			
			$this->xml = $this->fetchDataRemote($this->address);
			
			$this->xml2 = $this->fetchDataRemote($this->profile_address);
			
			$this->set();
			
		} else {
			
			$this->set_cached();
		}
	}

	/**
	 * set function.
	 * 
	 * @access public
	 * @return void
	 */
	function set() {
	
		$json = new Services_JSON();

		$data = $json->decode($this->xml);
		
		$temp = $data->data->children[0];
		
		$score = $temp->data->score;
		
		$eng = $temp->data->num_comments;
				
		$this->values['score'] = $score;
		
		$this->values['eng'] = $eng;
		
		$this->set_cache('reddit_score', $score);
		
		$this->set_cache('reddit_eng', $eng);
		
		preg_match('@<span class="karma">([0-9]+)</span>@', $this->xml2, $matches);
		
		$this->values['karma'] = $matches[1];
		
		$this->set_cache('reddit_karma', $this->values['karma']);
		
	}
	
	/**
	 * set_cached function.
	 * 
	 * @access public
	 * @return void
	 */
	function set_cached() {
		$this->values['score'] = $this->get_cache('reddit_score');
		$this->values['eng'] = $this->get_cache('reddit_eng');
		$this->values['karma'] = $this->get_cache('reddit_karma');
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
	 * 
	 * @return integer rank
	 * @access public
	 */
	function getRank() {
		return $this->get('score');
	}
	
	/**
	 * Get comments.
	 * 
	 * @return integer comments
	 * @accces public
	 */
	function getComments() {
		return $this->get('eng');
	}
	
	/**
	 * getKarma function.
	 * 
	 * @access public
	 * @return void
	 */
	function getKarma() {
		return $this->get('karma');
	}

	/**
	 * Get address.
	 * 
	 * @return string address
	 * @access public
	 */
	function getAddress() {
		return $this->address;
	}
	
	/**
	 * getProfileAddress function.
	 * 
	 * @access public
	 * @return void
	 */
	function getProfileAddress() {
		return $this->profile_address;
	}
	
	/**
	 * Get home address.
	 * 
	 * @return string url
	 * @access public
	 */
	function getHomeAddress() {
		$domain = str_replace('http://', '', $this->domain);
		$domain = str_replace('https://', '', $domain);
		return 'http://www.reddit.com/' . $domain;
	}
}
?>