<?php
/**
 * WPSDBacktype.
 * @author dligthart <info@daveligthart.com>
 * @version 0.3
 * @package wp-stats-dashboard
 */
class WPSDBacktype extends WPSDStats {
	
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
	 * address_backtweets
	 * 
	 * (default value: '')
	 * 
	 * @var string
	 * @access public
	 */
	var $address_backtweets = '';
	
	/**
	 * WPSDBacktype.
	 * @param string $domain
	 * @param boolean $curl
	 */
	function WPSDBacktype($domain, $curl = false) {	
		
		parent::WPSDStats();
		
		$domain = $this->getHost(parse_url($domain));
			
		$this->address = "http://www.backtype.com/domain/{$domain}";
		
		$form = new WPSDAdminConfigForm();
		
		$twitter_un = trim($form->getWpsdTwitterUn());
		
		if('' != $twitter_un) {
			
			$this->address_backtweets = "http://backtweets.com/user/{$twitter_un}";
		}
		
		if($this->isOutdated() && '' != $domain) {
						
			$this->set();
		} 
		else {

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
	
		// Set backtype.
			
		$data = $this->fetchDataRemote($this->address);

		preg_match("@([0-9]+) Comments</a>@", $data, $matches);

		$c = strip_tags(trim($matches[1]));
		
		preg_match("@([0-9]+) Tweets</a>@", $data, $matches);
		
		$t = strip_tags(trim($matches[1]));
		
		$this->values['comments'] = $c;
		
		$this->values['tweets'] = $t;

		$this->set_cache('backtype_comments', $this->values['comments'] );
		
		$this->set_cache('backtype_tweets', $this->values['tweets']);
		
		// Set backtweets.
		if('' != $this->address_backtweets) {
			
			$data = $this->fetchDataRemote($this->address_backtweets);
			
			preg_match("@([0-9]+)</em> Score@", $data, $matches);
			
			$this->values['btscore'] = $matches[1];
			
			$this->set_cache('backtweet_score', $this->values['btscore']);
		}
	}

	/**
	 * set_cached function.
	 * 
	 * @access public
	 * @return void
	 */
	function set_cached() {
		
		$this->values['comments'] = $this->get_cache('backtype_comments');
		
		$this->values['tweets'] = $this->get_cache('backtype_tweets');
		
		$this->values['btscore'] = $this->get_cache('backtweet_score');
	}

	/**
	 * get function.
	 * 
	 * @access public
	 * @param mixed $value
	 * @return mixed
	 */
	function get($value){
		return (isset($this->values[$value]) ? $this->values[$value] : '0');
	}

	/**
	 * Get comments.
	 * 
	 * @return integer
	 * @access public
	 */
	function getComments() {
		return $this->get('comments');
	}
	
	/**
	 * Get tweets.
	 * @return integer
	 * @access public 
	 */
	function getTweets() {
		return $this->get('tweets');
	}

	/**
	 * getScore function.
	 * 
	 * @access public
	 * @return integer
	 */
	function getScore() {
		return $this->get('btscore');
	}
	
	/**
	 * Get address.
	 * @return string address
	 * @access public
	 */
	function getAddress() {
		return $this->address;
	}
	
	/**
	 * getHomeAddress function.
	 * 
	 * @access public
	 * @return string
	 */
	function getHomeAddress() {
		return $this->address;
	}
	
	/**
	 * getBackTweetsAddress function.
	 * 
	 * @access public
	 * @return string
	 */
	function getBackTweetsAddress() {
		return $this->address_backtweets;
	}
}
?>