<?php

if(!class_exists('WPSDKlout')) { 

	include_once(dirname(__FILE__) . '/../WPSDKlout.php');
}

if(!class_exists('WPSDTwitter')) {
	
	include_once(dirname(__FILE__) . '/../WPSDTwitter.php');
}
 
/**
 * WPSDUserMetrics class.
 * 
 * @extends WPSDStats
 * @author daveligthart.com
 * @version 0.6
 * @package wp-stats-dashboard
 * @subpackage metrics
 */
class WPSDUserMetrics extends WPSDStats {

	/**
	 * _comments_placed
	 * 
	 * @var integer
	 * @access private
	 */
	var $_comments_placed = 0;
	/**
	 * _comments_received
	 * 
	 * @var integer
	 * @access private
	 */	
	var $_comments_received = 0;
	/**
	 * _post_count
	 * 
	 * @var integer
	 * @access private
	 */
	var $_post_count = 0;
	/**
	 * _user_id
	 * 
	 * @var integer
	 * @access private
	 */
	var $_user_id;
	/**
	 * _klout_score
	 * 
	 * @var float
	 * @access public
	 */
	var $_klout_score = 0;
	/**
	 * _twitter_ratio
	 * 
	 * @var float
	 * @access public
	 */
	var $_twitter_ratio = 0;
	
	/**
	 * _wpsd_score
	 * 
	 * @var float
	 * @access public
	 */
	var $_wpsd_score = 0;
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @param mixed $user_id
	 * @return void
	 */
	function __construct($user_id) {
		
		parent::__construct();
		
		$this->_user_id = $user_id;
		
		$this->loadData();
	}
	
	/**
	 * WPSDUserMetrics function.
	 * 
	 * @access public
	 * @param integer $user_id
	 * @return void
	 */
	function WPSDUserMetrics($user_id) {
		
		$this->__construct($user_id);
	}
	
	/**
	 * loadData function.
	 * 
	 * @access public
	 * @return void
	 */
	function loadData() {
		
		global $wpdb;
	
		if($this->_user_id > 0) { 
						
			$this->setAuthorMetrics();
		}
	}
	
	/**
	 * setAuthorMetrics function.
	 * 
	 * @access public
	 * @return void
	 */
	function setAuthorMetrics() {
		
		global $wpdb;
		
		$this->_post_count = get_usernumposts($this->_user_id);
					
		$this->_comments_received = $wpdb->get_var("
					SELECT COUNT(*) FROM {$wpdb->comments} WHERE comment_approved = 1 
						AND user_id <> {$this->_user_id}
						AND comment_post_ID IN (
							SELECT ID FROM {$wpdb->posts} WHERE post_status = 'publish' AND post_author = {$this->_user_id}
				)");	
				
		$this->_comments_placed = $wpdb->get_var(" 
					SELECT COUNT(*) FROM {$wpdb->comments} WHERE comment_approved = 1 AND user_id = {$this->_user_id}
				");	
				
		@$this->calculateKloutScore();
		
		@$this->calculateTwitterRatio();
		
		@$this->calculateWpsdScore();
	}
	
	/**
	 * calcuulateKloutScore function.
	 * 
	 * @access public
	 * @return void
	 */
	function calculateKloutScore() {
		
		$klout_username = esc_attr(get_the_author_meta( 'wpsd_user_klout', $this->_user_id ) );
				
		if('' != $klout_username) { 
					
			$klout = new WPSDKlout($klout_username);
				
			$this->_klout_score = $klout->getRank();
		}
	}
	
	/**
	 * calculateTwitterRation function.
	 * 
	 * @access public
	 * @return void
	 */
	function calculateTwitterRatio() {
		
		$twitter_username = esc_attr(get_the_author_meta( 'wpsd_user_twitter', $this->_user_id ) );
				
		if('' != $twitter_username) {
					
			$t = new WPSDTwitter($twitter_username);
			
			$this->_twitter_ratio = $t->getRatio();
		}
	}
	
	/**
	 * calculateWpsdScore function.
	 * 
	 * @access public
	 * @return void
	 */
	function calculateWpsdScore() {
		global $wpdb; 
		
		$score_matrix = array(
			'twitter_ratio' => array($this->_twitter_ratio, 10),
			'klout_score' => array($this->_klout_score, 30),
			'post_count'	=> array($this->_post_count, 40),
			'comments_received' => array($this->_comments_received, 20)
		);
		
		$count_posts = wp_count_posts();

		$published_posts = $count_posts->publish;
		
		$total_comments = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->comments} WHERE comment_approved = '1'");
		
		$this->_wpsd_score = 0;
		
		foreach($score_matrix as $key => $metric) {
		
			switch($key) { 			
				case 'twitter_ratio':
				    if($metric[0] >=1) $metric[0] = 1;					
					$n =  ((1 - $metric[0]) * 100) * ($metric[1] / 100);		
					$this->_wpsd_score += $n;					
					//echo $n; echo '<br/>';
				break;				
				case 'klout_score':
					$n = $metric[0] * ($metric[1] / 100);					
					$this->_wpsd_score += $n;
					//echo $n; echo '<br/>';
				break;				
				case 'post_count':
					$n = ($metric[0] / $count_posts) * ($metric[1] /100);
					$this->_wpsd_score += $n;
					//echo $n; echo '<br/>';
				break;				
				case 'comments_received':
					$n = ($metric[0] / $total_comments) * ($metric[1] / 100);
					$this->_wpsd_score += $n;
					//echo $n; echo '<br/>';
				break;	
			}
		}	
		
		//$this->_wpsd_score = $this->_wpsd_score;
	
		/*
		0.5 => (0=100, 1=0)
		13	=> (0…100)
		50 	=> (published_posts=100, (post_count / publish_posts) * 100)
		80  => (total_comments=100, (comments_received / total_comments) * 100) 
		*/	
	}
	
	/**
	 * getCommentsPlaced function.
	 * 
	 * @access public
	 * @return integer
	 */
	function getCommentsPlaced() {
		
		return $this->_comments_placed;
	}
	
	/**
	 * getCommentsReceived function.
	 * 
	 * @access public
	 * @return integer
	 */
	function getCommentsReceived() {
		
		return $this->_comments_received;
	}
	
	/**
	 * getPostCount function.
	 * 
	 * @access public
	 * @return integer
	 */
	function getPostCount() {
	
		return $this->_post_count;
	}
	
	/**
	 * getKloutScore function.
	 * 
	 * @access public
	 * @return float
	 */
	function getKloutScore() {
		
		return $this->_klout_score;
	}
	
	/**
	 * getTwitterRatio function.
	 * 
	 * @access public
	 * @return float
	 */
	function getTwitterRatio() {
		
		return $this->_twitter_ratio;
	}
	
	/**
	 * getWpsdScore function.
	 * 
	 * @access public
	 * @return float
	 */
	function getWpsdScore() {
		
		return $this->_wpsd_score;
	}
}
?>