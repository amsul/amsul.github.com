<?php
/**
 * WPSDPeerIndex.
 * @author dligthart <info@daveligthart.com>
 * @version 0.1
 * @package wp-stats-dashboard
 */
class WPSDPeerIndex extends WPSDStats {

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
	 * WPSDPeerIndex function.
	 * 
	 * @access public
	 * @param string $username. (default: '')
	 * @return void
	 */
	function WPSDPeerIndex($username = '') {
		
		parent::WPSDStats();
		
		if('' == $username) { 
		
			$form = new WPSDAdminConfigForm();
		
			$this->un = trim($form->getWpsdTwitterUn());
		
		} else {
			
			$this->un = $username;
		}
		
		if('' != $this->un) {
			
			$this->address = 'http://www.peerindex.com/' . $this->un; 
			
			if($this->is_cache_outdated('peerindex', $this->un)) {
				
				$this->xml = str_replace(',', '', $this->fetchDataRemote($this->address, false, 1, true));

				$this->set();
				
				$this->updated_cache('peerindex', $this->un);
				
			} else {
				
				$this->set_cached();
			}
		}
	}
	
	/**
	 * isEnabled function.
	 * 
	 * @access public
	 * @return void
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
		
		$this->values['score'] = $this->get_count();
		
		$this->update_cache('peerindex_' . $this->un, $this->values['score']);
	}
	
	/**
	 * get_count function.
	 * 
	 * @access public
	 * @return void
	 */
	function get_count() {
							
		$matches = array();
		
		preg_match("/<dd>(\d+)<\/dd>/", $this->xml, $matches);
		
		return number_format($matches[1]);
	}
	
	/**
	 * set_cached function.
	 * 
	 * @access public
	 * @return void
	 */
	function set_cached() {
	
		$this->values['score'] = $this->get_cached('peerindex_' . $this->un);
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
	 * getScore function.
	 * 
	 * @access public
	 * @return integer
	 */
	function getScore() {
		return $this->get('score');
	}
	
	/**
	 * Get address.
	 * @return address
	 * @access public
	 */
	function getAddress() {
		return $this->address;
	}
	
	/**
	 * getUsername function.
	 * 
	 * @access public
	 * @return string
	 */
	function getUsername() {
		
		return $this->un;
	}
}
?>