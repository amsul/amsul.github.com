<?php
/**
 * WPSDLinkedIn.
 * 
 * @author dligthart <info@daveligthart.com>
 * @version 0.2
 * @package wp-stats-dashboard
 */
class WPSDLinkedIn extends WPSDStats {

	var $xml;
	var $values;
	var $address = '';
	var $un;
	var $address_company = '';
	var $un_company;
	
	/**
	 * WPSDLinkedIn.
	 * 
	 * @param string $domain
	 * @param boolean $curl
	 */
	function WPSDLinkedIn($curl = false) {
		parent::WPSDStats();
		
		$form = new WPSDAdminConfigForm();
		
		$this->un = trim($form->getWpsdLinkedInUn());
		
		$this->un_company = trim($form->getWpsdLinkedInCompanyUn());
		
		if('' != $this->un_company) {
			
			$this->address_company = 'http://www.linkedin.com/company/' . $this->un_company;
		}
		
		if('' != $this->un) {
			
			$this->address = 'http://www.linkedin.com/in/' . $this->un; 
			
			if($this->isOutdated()) {
			
				$this->set();
				
			} else {
				
				$this->set_cached();
			}
		}
	}
	
	/**
	 * Is enabled.
	 * @return unknown_type
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
	
		$this->xml = $this->fetchDataRemote($this->address);
		
		//Set Connection Count.
		$this->values['count'] = $this->get_count('connection-count', $this->xml, 1);
		
		//$this->values['rc'] = $this->get_count('recommendation-count r[0-9]+', $this->xml, 1);
		
		$this->set_cache('linkedin_c', $this->values['count']);
		
		if('' != $this->address_company) {
			
			$result = $this->fetchDataRemote($this->address_company);
			
		//	$this->values['network_count'] = '';
			
			preg_match('@New Hires \(([0-9]+)\)@', $result, $matches);
			
			$this->values['new_hires'] = number_format($matches[1]);
			
			$this->set_cache('linkedin_nh', $this->values['new_hires']);
			
		//	$this->values['empl'] = '';

		}
	}
	
	/**
	 * Get count.
	 * 
	 * @param unknown_type $type
	 * @param unknown_type $data
	 * @param unknown_type $match_index
	 * @return string
	 */
	function get_count($type, $data, $match_index = 1) {
		preg_match("@<strong>([0-9]+)</strong>  connections@si", $data, $matches);				
		return number_format($matches[$match_index]);
	}
	
	/**
	 * Set cached.
	 * 
	 * @return unknown_type
	 */
	function set_cached() {
		$this->values['count'] = $this->get_cache('linkedin_c');
		$this->values['rc'] = $this->get_cache('linkedin_rc');		
		$this->values['new_hires'] = $this->get_cache('linkedin_nh');
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
	 * getCompanyNewHires function.
	 * 
	 * @access public
	 * @return integer
	 */
	public function getCompanyNewHires() {
		
		return $this->get('new_hires');
	}
	
	/**
	 * Get connection count.
	 * 
	 * @return integer connections
	 * @access public
	 */
	function getConnectionCount() {
		return $this->get('count');
	}
	
	/**
	 * Get recommends.
	 * 
	 * @return integer recommends
	 * @access public
	 */
	function getRecommends() {
		return $this->get('rc');
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
	 * getCompanyAddress function.
	 * 
	 * @access public
	 * @return string
	 */
	function getCompanyAddress() {
		return $this->address_company;
	}
	
	/**
	 * getCompanyStatsAddress function.
	 * 
	 * @access public
	 * @return string
	 */
	function getCompanyStatsAddress() {
		return $this->address_company . '/statistics';
	}
}
?>