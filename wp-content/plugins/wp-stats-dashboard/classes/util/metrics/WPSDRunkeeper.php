<?php
/**
 * WPSDRunkeeper.
 *
 * @author dligthart <info@daveligthart.com>
 * @version 0.1
 * @package wp-stats-dashboard
 */
class WPSDRunkeeper extends WPSDStats {

	var $xml;
	var $values;
	var $address = '';
	var $un;

	/**
	 * WPSDRunkeeper.
	 *
	 * @param boolean $curl
	 */
	function WPSDRunkeeper($curl = false) {

		parent::WPSDStats();

		$form = new WPSDAdminConfigForm();

		$this->un = trim($form->getWpsdRunkeeperUn());

		if('' != $this->un) {

			$this->address = 'http://runkeeper.com/user/'.$this->un.'/profile';
				
			if($this->isOutdated()) {

				$this->xml = $this->fetchDataRemote($this->address);

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

		$this->values['total'] = $this->get_count('totalActivities', $this->xml);
			
		$this->set_cache('runkeeper_total', $this->values['total']);
		
		$this->values['distance'] = $this->get_count('totalDistance', $this->xml);
			
		$this->set_cache('runkeeper_distance', $this->values['distance']);
		
		$this->values['calories'] = $this->get_count('totalCalories', $this->xml);
			
		$this->set_cache('runkeeper_calories', $this->values['calories']);
	}

	/**
	 * Get count.
	 *
	 * @param $type
	 * @param $data
	 * @return unknown_type
	 */
	function get_count($type, $data) {

		preg_match("@<div class=\"profileStatsCell\" id=\"{$type}\">(.*?)</div>@si", $data, $matches);

		//strip out of the HTML element from the matches
		$raw=str_replace(',','',strip_tags(trim($matches[1])));

		// Remove whitespace.
		$raw = preg_replace( "{[ \t]+}", '', $raw);

		//geting the the rank values out of the string
		preg_match("/([0-9]+)/", $raw, $matches);

		return number_format($matches[1]);
	}

	/**
	 * Set cached.
	 */
	function set_cached() {

		$this->values['total'] = $this->get_cache('runkeeper_total');

		$this->values['distance'] = $this->get_cache('runkeeper_distance');

		$this->values['calories'] = $this->get_cache('runkeeper_calories');
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
	 * Get total activities.
	 *
	 * @return integer total activities
	 * @access public
	 */
	function getTotal() {
		return $this->get('total');
	}

	/**
	 * Get distance.
	 *
	 * @return float distance
	 */
	function getDistance() {
		return $this->get('distance');
	}

	/**
	 * Get calories.
	 *
	 * @return float calories
	 */
	function getCalories() {
		return $this->get('calories');
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