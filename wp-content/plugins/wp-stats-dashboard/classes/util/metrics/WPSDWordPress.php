<?php
/**
 * WPSDWordPress.
 * @author dligthart <info@daveligthart.com>
 * @version 0.1
 * @package wp-stats-dashboard
 */
class WPSDWordPress extends WPSDStats {

	var $xml;
	var $values;
	var $address = 'http://wordpress.com/wp-admin/admin-ajax.php?cookie&action=wpcom_load_tab&template=likes';
	var $un;
	var $pw;
	var $cookie;
	
	/**
	 * WPSDWordPress.
	 */
	function WPSDWordPress() {
		
		parent::WPSDStats();
		
		$form = new WPSDAdminConfigForm();
		
		$this->un = trim($form->getWpsdUn());
		
		$this->pw = trim($form->getWpsdPw());
		
		if('' != $this->un && '' != $this->pw) {
					
			if($this->isOutdated()) {
				
				if($this->login()) {
					
					$this->xml = $this->get_data();	
				}
							
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
		
		$this->values['posts'] = $this->get_count('posts', $this->xml);
		
		$this->values['blogs'] = $this->get_count('blogs', $this->xml);
		
		$this->set_cache('wordpress_like_posts', $this->values['posts']);
		
		$this->set_cache('wordpress_like_blogs', $this->values['blogs']);
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

		preg_match("@ ([0-9]+) {$type}@si", $data, $matches);
			
		return number_format($matches[1]);
	}
	
	/**
	 * Set cached.
	 */
	function set_cached() {
		
		$this->values['posts'] = $this->get_cache('wordpress_like_posts');
		
		$this->values['blogs'] = $this->get_cache('wordpress_like_blogs');
	}
	
	/**
	 * Get data.
	 * @return value
	 * @access protected
	 */
	function get($value){
		return (isset($this->values[$value]) ? $this->values[$value] : '0');
	}
	
	function getLikePosts() {
		return $this->get('posts');
	}
	
	function getLikeBlogs() {
		return $this->get('blogs');
	}
	
	/**
	 * Get address.
	 * @return address
	 * @access public
	 */
	function getAddress() {		
		return 'http://wordpress.com/likes';
	}
	
	
	function login() {
		
		$blog_id = get_option('wpsd_blog_id');

		$cookie_filename = md5($blog_id);
		
		$cache_path = wpsd_get_cache_path();
		
		$cache_dir = $cache_path . 'wp-stats-dashboard';
		
		$url = 'https://dashboard.wordpress.com/wp-login.php';
	
		$this->cookie = $cache_dir . '/' . $cookie_filename;
		
		// Login and set cookie.
		
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);
		curl_setopt($ch, CURLOPT_REFERER, get_bloginfo('wpurl') . '/wp-admin/index.php?page=stats');
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
		curl_setopt($ch, CURLOPT_POSTFIELDS, "log={$this->un}&pwd={$this->pw}&testcookie=1");
	
		curl_exec($ch);	// Login.
				
		curl_close ($ch); // close connection.
		
		unset($ch); // clear channel.
		
		return true;	
	}
	
	function get_data() {
		
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
		curl_setopt($ch, CURLOPT_URL, $this->address);
		
		return curl_exec ($ch);
	}
}
?>