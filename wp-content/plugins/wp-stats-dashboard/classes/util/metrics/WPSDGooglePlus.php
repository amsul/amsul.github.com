<?php
/**
 * 
 * WPSDGooglePlus.  
 * 
 * @author dligthart <info@daveligthart.com>
 * @version 0.4
 * @package wp-stats-dashboard
 */
class WPSDGooglePlus extends WPSDStats {

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
	var $address = 'http://www.circlecount.com/p/%s';
	
	/**
	 * un
	 * 
	 * @var mixed
	 * @access public
	 */
	var $un;
	
	/**
	 * WPSDGooglePlus function.
	 * 
	 * @access public
	 * @return void
	 */
	function WPSDGooglePlus() {
		
		parent::WPSDStats();
		
		//$id = '107127911590007452165'; // debug.
		
		$form = new WPSDAdminConfigForm();

		$this->un = trim($form->getWpsdGooglePlusUn());
		
		$id = $this->un;
					
		if($this->isOutdated()) {
				
			$this->xml = strip_tags($this->fetchDataRemote(sprintf($this->address, $id), true, 1, true));
			
			preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $this->xml);
							
			$this->set();
			
		} else {
				
			$this->set_cached();
		} 
		
		$this->address = "https://plus.google.com/{$id}/posts";
	}
	
	/**
	 * set function.
	 * 
	 * @access public
	 * @return void
	 */
	function set() {
		
		$matches = array();
		
		preg_match_all('@Followers: ([0-9]+)@', $this->xml, $matches);
		
		if(isset($matches[1][0])) {
			
			$this->values['followers'] = number_format($matches[1][0]);
						
			$this->set_cache('google_plus_fs', $this->values['followers']);	
			
		}
		
		preg_match_all('@Following: ([0-9]+)@', $this->xml, $matches);
		
		if(isset($matches[1][0])) {
			
			$this->values['following'] = number_format($matches[1][0]);
		
			$this->set_cache('google_plus_fg', $this->values['following']);	
		}
	}
	
	/**
	 * set_cached function.
	 * 
	 * @access public
	 * @return void
	 */
	function set_cached() {
		
		$this->values['followers'] = $this->get_cache('google_plus_fs');
		
		$this->values['following'] = $this->get_cache('google_plus_fg');
	}
	
	/**
	 * get function.
	 * 
	 * @access protected
	 * @param mixed $value
	 * @return string
	 */
	function get($value){
		return (isset($this->values[$value]) ? $this->values[$value] : '0');
	}

	/**
	 * Get the number of followers.
	 * 
	 * @return integer 
	 * @access public
	 */
	function getFollowers() {
		return $this->get('followers');
	}
	
	/**
	 * getFollowing function.
	 * 
	 * @access public
	 * @return integer
	 */
	function getFollowing() {
		return $this->get('following');
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