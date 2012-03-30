<?php
/**
 * WPSD99Designs.
 *
 * @author dligthart <info@daveligthart.com>
 * @version 0.1
 * @package wp-stats-dashboard
 */
class WPSD99Designs extends WPSDStats {

	var $xml;
	var $values;
	var $address = '';
	var $un;

	/**
	 * WPSD99Designs.
	 *
	 * @param boolean $curl
	 */
	function WPSD99Designs($curl = false) {

		parent::WPSDStats();

		$form = new WPSDAdminConfigForm();

		$this->un = trim($form->getWpsd99DesignsUn());

		if('' != $this->un) {

			$this->address = 'http://99designs.com/people/'. $this->un;
				
			if($this->isOutdated()) {

				$this->xml = $this->fetchDataRemote($this->address, false, 1, true);

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
		
		$matches = array();

		preg_match_all("@<dd>([0-9]+)</dd>@", $this->xml, $matches);
	
		$this->values['contests'] = number_format($matches[1][0]);
			
		$this->set_cache('99designs_contests', $this->values['contests']);
		
		$this->values['active'] = number_format($matches[1][1]);
			
		$this->set_cache('99designs_active', $this->values['active']);

		$this->values['awarded'] = number_format($matches[1][2]);
			
		$this->set_cache('99designs_awarded', $this->values['awarded']); 
		
	}

	/**
	 * Set cached.
	 */
	function set_cached() {

		$this->values['contests'] = $this->get_cache('99designs_contests');

		$this->values['active'] = $this->get_cache('99designs_active');
		
		$this->values['awarded'] = $this->get_cache('99designs_awarded'); 
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
	 * Get contests.
	 *
	 * @return integer number contests held
	 * @access public
	 */
	function getContests() {
		return $this->get('contests');
	}
	
	/**
	 * getActive function.
	 * 
	 * @access public
	 * @return void
	 */
	function getActive() {
		return $this->get('active');
	}
	
	/**
	 * getAwarded function.
	 * 
	 * @access public
	 * @return void
	 */
	function getAwarded() {
		return $this->get('awarded');
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