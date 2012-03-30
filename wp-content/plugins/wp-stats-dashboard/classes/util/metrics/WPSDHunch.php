<?php
/**
 * 
 * WPSDHunch.  
 * 
 * @author dligthart <info@daveligthart.com>
 * @version 0.1
 * @package wp-stats-dashboard
 */
class WPSDHunch extends WPSDStats {

	
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
	 * WPSDHunch function.
	 * 
	 * @access public
	 * @return void
	 */
	function WPSDHunch() {
		
		parent::WPSDStats();
		
		$form = new WPSDAdminConfigForm();

		$this->un = trim($form->getWpsdHunchUn());
		
		$this->address = "http://hunch.com/{$this->un}/";
		
		if($this->isOutdated()) {
				
			$this->xml = ($this->fetchDataRemote($this->address));

			$this->set();
			
		} else {
				
			$this->set_cached();
		} 
	}
	
	/**
	 * Set data.
	 */
	function set() {
		
		$matches = array();
		
		preg_match_all("@([0-9]+)<\/strong>@", $this->xml, $matches);
		
	//	print_r($matches);
		
		if(isset($matches[1][0])) {

			$this->values['recommendation'] = number_format($matches[1][0]);
			$this->values['saved']	= number_format($matches[1][1]);
			$this->values['followers'] = number_format($matches[1][2]);
			$this->values['following'] = number_format($matches[1][3]);
			
			$this->set_cache('hunch_s', $this->values['saved']);
			$this->set_cache('hunch_r', $this->values['recommendation']);
			$this->set_cache('hunch_fs', $this->values['followers']);	
			$this->set_cache('hunch_fg', $this->values['following']);	
		}
	}

	/**
	 * Set cached.
	 */
	function set_cached() {
		$this->values['recommendation'] = $this->get_cache('hunch_r');
		$this->values['saved'] = $this->get_cache('hunch_s');
		$this->values['followers'] = $this->get_cache('hunch_fs');
		$this->values['following'] = $this->get_cache('hunch_fg');
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
	 * getRecommendation function.
	 * 
	 * @access public
	 * @return integer
	 */
	function getRecommendation() {
		return $this->get('recommendation');
	}
	
	/**
	 * getSaved function.
	 * 
	 * @access public
	 * @return integer
	 */
	function getSaved() {
		return $this->get('saved');
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