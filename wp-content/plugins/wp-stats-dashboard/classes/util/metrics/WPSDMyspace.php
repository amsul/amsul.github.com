<?php
/**
 * WPSDMyspace.
 * @author dligthart <info@daveligthart.com>
 * @version 0.3
 * @package wp-stats-dashboard
 */
class WPSDMyspace extends WPSDStats {

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
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	function __construct() {
		
		parent::__construct();
		
		$form = new WPSDAdminConfigForm();
		
		$this->un = trim($form->getWpsdMyspaceUn());
		
		if('' != $this->un) {
			
			$this->address = 'http://www.myspace.com/' . $this->un; 
			
			if($this->isOutdated()) {
				
				$this->xml = str_replace(',', '', $this->fetchDataRemote($this->address, false, 1, true));
				
				$this->set();
				
			} else {
				
				$this->set_cached();
			}
		}
	}
	
	/**
	 * WPSDMyspace function.
	 * 
	 * @access public
	 * @return void
	 */
	function WPSDMyspace() {
		$this->__construct();
	}
	
	/**
	 * isEnabled function.
	 * 
	 * @access public
	 * @return boolean
	 */
	function isEnabled() {
		return ('' != $this->un);
	}
	
	/**
	 * set function.
	 * 
	 * @access public
	 * @return void
	 */
	function set() {
		
		$this->values['friends'] = $this->get_count('count', $this->xml);
		
		$this->set_cache('myspace_f', $this->values['friends']);
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

		preg_match("@([0-9]+)\)<\/h3>@", $data, $matches);
			
		return number_format($matches[1]);
	}
	
	/**
	 * set_cached function.
	 * 
	 * @access public
	 * @return void
	 */
	function set_cached() {
		
		$this->values['friends'] = $this->get_cache('myspace_f');
	}
	
	/**
	 * Get data.
	 * 
	 * @return integer
	 * @access protected
	 */
	function get($value){
		return (isset($this->values[$value]) ? $this->values[$value] : '0');
	}
	
	/**
	 * Get friend count.
	 * 
	 * @return integer friends
	 * @access public
	 */
	function getFriends() {
		return $this->get('friends');
	}
	
	/**
	 * Get address.
	 * @return string
	 * @access public
	 */
	function getAddress() {
		return $this->address;
	}
}
?>