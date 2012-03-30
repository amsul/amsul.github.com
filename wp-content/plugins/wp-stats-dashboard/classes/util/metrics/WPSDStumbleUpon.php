<?php
/**
 * WPSDStumbleUpon.
 * @author dligthart <info@daveligthart.com>
 * @version 0.3
 * @package wp-stats-dashboard
 */
class WPSDStumbleUpon extends WPSDStats {
	
	/**
	 * xml
	 * 
	 * @var mixed
	 * @access public
	 */
	var $xml;
	
	/**
	 * xml2
	 * 
	 * @var mixed
	 * @access public
	 */
	var $xml2;
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
	 * profile_address
	 * 
	 * @var mixed
	 * @access public
	 */
	var $profile_address;
	
	/**
	 * WPSDStumbleUpon function.
	 * 
	 * @access public
	 * @param mixed $domain
	 * @param bool $curl. (default: false)
	 * @return void
	 */
	function WPSDStumbleUpon($domain, $curl = false) {
		
		parent::WPSDStats();

		$domain = $this->getHost(parse_url($domain));
		
		$domain = str_replace('/', '', $domain);
		
		$domain = str_replace('www.', '', $domain);
			
		$this->address = 'http://www.stumbleupon.com/url/' . $domain . '/';
		
		$form = new WPSDAdminConfigForm();
		
		$this->un = trim($form->getWpsdStumbleUponUn());

		$this->profile_address = 'http://www.stumbleupon.com/stumbler/' . $this->un . '/';
		
		if($this->isOutdated()) {
			
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

		$html_values = $this->xml;

		$str = '<div class="views">
						<a href="'.$this->address . '">
							<span>([0-9.]+)</span> views						</a>
					</div>';
		
		preg_match('@' . $str . '@si', $html_values, $matches);
				
		$this->values['views'] = number_format($matches[1]);
				
		$this->set_cache('su', $this->values['views']);
		
		preg_match('@([0-9]+) followers@', $this->xml2, $matches);
		
		$this->values['followers'] = $matches[1];
		
		$this->set_cache('su_followers', $this->values['followers']);	
		
		preg_match('@([0-9]+) following@', $this->xml2, $matches);
		
		$this->values['following'] = $matches[1];
		
		$this->set_cache('su_following', $this->values['following']);	

	}
	
	/**
	 * set_cached function.
	 * 
	 * @access public
	 * @return void
	 */
	function set_cached() {
		
		$this->values['views'] = $this->get_cache('su');
		
		$this->values['followers'] = $this->get_cache('su_followers');
		
		$this->values['following'] = $this->get_cache('su_following');
	}
	
	/**
	 * Get data.
	 * 
	 * @return integer value
	 * @access protected
	 */
	function get($value){
	
		return (isset($this->values[$value]) ? $this->values[$value] : '0');
	}

	/**
	 * Get views.
	 * 
	 * @return integer views
	 * @access public
	 */
	function getViews() {
	
		return $this->get('views');
	}
	
	/**
	 * getFollowers function.
	 * 
	 * @access public
	 * @return void
	 */
	function getFollowers() {
		
		return $this->get('followers');
	}
	
	/**
	 * getFollowing function.
	 * 
	 * @access public
	 * @return void
	 */
	function getFollowing() {
		
		return $this->get('following');
	}

	/**
	 * getAddress function.
	 * 
	 * @access public
	 * @return void
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
}
?>