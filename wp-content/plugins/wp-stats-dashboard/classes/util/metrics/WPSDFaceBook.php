<?php
/**
 * WPSDFaceBook.
 *
 * @author dligthart <info@daveligthart.com>
 * @version 0.3
 * @package wp-stats-dashboard
 */
class WPSDFaceBook extends WPSDStats {

	var $xml;
	var $values;
	var $address = 'http://www.facebook.com/';
	var $key; //= '44cffec61cf9b71febf87795e799fa97';
	var $secret;// = 'b7d7568d819886925e2812e57138b0b4';
	var $user = '';
	var $api = null;
	//var $graph_api_url = 'https://graph.facebook.com/?ids=';
	var $graph_api_url = 'https://graph.facebook.com/';
	
	var $username;
	var $password;
	
	var $fql_like_url = 'https://api.facebook.com/method/fql.query?query=select%20%20like_count,%20total_count,%20share_count,%20click_count%20from%20link_stat%20where%20url=';
	
	/**
	 * WPSDFaceBook.
	 */
	function WPSDFaceBook()
	{

		parent::WPSDStats();

		$form = new WPSDAdminConfigForm();

		$this->user = $form->getWpsdFacebookUn();
		
		//$this->username = $form->getWpsdFacebookLogin();
		
		//$this->password = $form->getWpsdFacebookPassword();
		
		//echo $this->username; 
		//echo $this->password;

		$this->address .= $this->user;

		if ($this->key && $this->secret) {

			require_once dirname(__FILE__) . '/../api/facebook/php4_client/facebook.php';

			$this->api = new WPSDFacebookAPI($this->key, $this->secret);
		}
		
		//$this->set();
	
		if ($this->isOutdated()) {

			$this->set();

		} else {

			$this->set_cached();
		}
	}

	/**
	 * Set friends.
	 *
	 * @return false|integer count
	 */
	function setFanCount()
	{

		if ($this->key && $this->secret) {

			$page_id = $this->user; //'5281959998'; // use graph to get page id.

			$data = $this->fetchDataRemote($this->graph_api_url . $page_id);

			preg_match('#"link": "(.*?)"#si', $data, $matches);

			$link = $matches[1];

			$fql = 'select fan_count from page where page_id = '.$page_id.';';

			$result = $this->api->api_client->fql_query($fql);

			// var_dump($result['fql_query_response']['page']);

			$c = @$result['fql_query_response']['page']['fan_count'];
		}
		else {

			if ($this->user) {

				$data = $this->fetchDataRemote($this->graph_api_url . $this->user);
				
				$this->xml = $data;
				
				$type = 'likes';

				preg_match("#\"{$type}\": ([0-9]+)#si", $data, $matches);

				$c = $matches[1];

				preg_match('#"link": "(.*?)"#si', $data, $matches);

				$link = $matches[1];
			}
		}

		if (!$c) $c = 0;

		$this->set_cache('facebook_f', $c);

		$this->set_cache('facebook_link', $link);

		return $c;
	}
	
	/**
	 * getFacebookLikeStats function.
	 * 
	 * @access public
	 * @param string $url (default: '')
	 * @return string xml data
	 */
	function getLikeStats($url = '') {
		
		if(!$url)
			$url = get_bloginfo('url');
				
		$data = str_replace('.', '', str_replace(',', '', $this->fetchDataRemote("{$this->fql_like_url}%22{$url}%22")));
		
		if(null == $data) return false;
		
		preg_match_all('@_count>([0-9]+)<\/@si', $data, $matches);
				
		if(isset($matches[1])) {
			
			$metrics['like']  = $matches[1][0];
			$metrics['total'] = $matches[1][1];
			$metrics['share'] = $matches[1][2];
			$metrics['click'] = $matches[1][3];
		}
				
		return $metrics;
	}
	
	/**
	 * getProfileUrl function.
	 * 
	 * @access public
	 * @param mixed $data
	 * @return void
	 */
	function getProfileUrl($data) {
		
		preg_match('@href="(.*?)" id="navAccountName"@si', $data, $matches);
		
		return $matches[1];
	}	
	
	
	/**
	 * getFriendCount function.
	 * 
	 * @access public
	 * @param mixed $data
	 * @return void
	 */
	function getFriendCount($data) {
		
		preg_match('@([0-9]+) friends@si', $data, $matches);
				
		return number_format($matches[1]);
	
	}

	/**
	 * getFacebookHtml function.
	 * 
	 * @access public
	 * @param mixed $username
	 * @param mixed $password
	 * @param string $url. (default: 'http://www.facebook.com')
	 * @return void
	 */
	function getFacebookHtml($username, $password, $url = 'http://www.facebook.com')
	{

		// access to facebook home page (to get the cookies)
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_ENCODING, "");
		curl_setopt($curl, CURLOPT_COOKIEJAR, wpsd_get_cache_dir() . 'cookies_facebook.cookie');
		curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.2) Gecko/20100115 Firefox/3.6 (.NET CLR 3.5.30729)");
		$curlData = curl_exec($curl);
		curl_close($curl);

		// do get some parameters for login to facebook
		$charsetTest = substr($curlData, strpos($curlData, "name=\"charset_test\""));
		$charsetTest = substr($charsetTest, strpos($charsetTest, "value=") + 7);
		$charsetTest = substr($charsetTest, 0, strpos($charsetTest, "\""));

		$locale = substr($curlData, strpos($curlData, "name=\"locale\""));
		$locale = substr($locale, strpos($locale, "value=") + 7);
		$locale = substr($locale, 0, strpos($locale, "\""));

		$lsd = substr($curlData, strpos($curlData, "name=\"locale\""));
		$lsd = substr($lsd, strpos($lsd, "value=") + 7);
		$lsd = substr($lsd, 0, strpos($lsd, "\""));

		// do login to facebook
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, "https://login.facebook.com/login.php?login_attempt=1");
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_POSTFIELDS, "charset_test=" . $charsetTest . "&locale=" . $locale . "&non_com_login=&email=" . $username . "&pass=" . $password . "&charset_test=" . $charsetTest . "&lsd=" . $lsd);
		curl_setopt($curl, CURLOPT_ENCODING, "");
		curl_setopt($curl, CURLOPT_COOKIEFILE, wpsd_get_cache_dir()  . 'cookies_facebook.cookie');
		curl_setopt($curl, CURLOPT_COOKIEJAR, wpsd_get_cache_dir()  . 'cookies_facebook.cookie');
		curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.2) Gecko/20100115 Firefox/3.6 (.NET CLR 3.5.30729)");
		$curlData = curl_exec($curl);
		curl_close($curl);

		return $curlData;
	}
	
	/**
	 * setMonthlyActiveUsers function.
	 * 
	 * @access private
	 * @return void
	 */
	function setMonthlyActiveUsers() {
		
		preg_match('@fbInsightsTinyStatisticNumber\">([0-9]+)<@', $this->xml, $matches);

		return $matches[1];
	}

	/**
	 * Set data.
	 */
	function set() {

		$this->values['fans'] = $this->setFanCount();
		
		$this->values['like_metrics'] =  $this->getLikeStats();
		
		$this->set_cache('facebook_like_metrics', $this->values['like_metrics']);
				
	/*	$this->values['monthly_active'] = $this->setMonthlyActiveUsers();
		
		$this->set_cache('facebook_monthly_active', $this->values['montly_active']); */
	}

	/**
	 * Set cached.
	 */
	function set_cached()
	{

		$this->values['fans'] = $this->get_cache('facebook_f');

		$this->values['link'] = $this->get_cache('facebook_link');
		
		$this->values['like_metrics'] = $this->get_cache('facebook_like_metrics');
		
		//$this->values['monthly_active'] = $this->get_cache('facebook_monthly_active');
	}

	/**
	 * Get data.
	 *
	 * @return value
	 * @access protected
	 */
	function get_number($value)
	{

		return isset($this->values[$value]) ? $this->values[$value] : '0';
	}

	/**
	 * Get Fans.
	 *
	 * @return integer fans
	 */
	function getFans()
	{

		return $this->values['fans'];
	}
	
	/**
	 * getMonthlyActiveFans function.
	 * 
	 * @access public
	 * @return integer active fans
	 */
	function getMonthlyActiveFans() {
		
		return $this->values['monthly_active'];
	}
	
	/**
	 * getLikeMetrics function.
	 * 
	 * @access public
	 * @return array
	 */
	function getLikeMetrics() {
		
		return $this->values['like_metrics'];
	}

	/**
	 * Get address.
	 *
	 * @return address
	 * @access public
	 */
	function getAddress()
	{

		return $this->get_cache('facebook_link');
	}
}
?>