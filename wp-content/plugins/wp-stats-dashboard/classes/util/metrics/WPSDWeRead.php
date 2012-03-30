<?php
/**
 * WPSDWeRead.
 *
 * @author dligthart <info@daveligthart.com>
 * @version 0.1
 * @package wp-stats-dashboard
 */
class WPSDWeRead extends WPSDStats {

	var $xml;
	var $values;
	var $address = '';
	var $id;

	/**
	 * WPSDWeRead.
	 *
	 * @param boolean $curl
	 */
	function WPSDWeRead($curl = false) {

		parent::WPSDStats();

		$form = new WPSDAdminConfigForm();

		$this->id = trim($form->getWpsdWeReadId());

		if('' != $this->id) {

			$this->address = 'http://weread.com/profile/'.str_replace(' ', '+', $this->id) . '/';
				
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
		return ('' != $this->id);
	}

	/**
	 * Set data.
	 */
	function set() {

		$this->values['books'] = $this->get_count('Books', $this->xml);
			
		$this->set_cache('weread_books', $this->values['books']);
		
		$this->values['reviews'] = $this->get_count('Reviews', $this->xml);
			
		$this->set_cache('weread_reviews', $this->values['reviews']);
		
		$this->values['ratings'] = $this->get_count('Ratings', $this->xml);
			
		$this->set_cache('weread_ratings', $this->values['ratings']);
	}

	/**
	 * Get count.
	 *
	 * @param $type
	 * @param $data
	 * @return unknown_type
	 */
	function get_count($type, $data) {

		preg_match("@([0-9]+) {$type}</a>@", $data, $matches);

		return number_format($matches[1]);
	}

	/**
	 * Set cached.
	 */
	function set_cached() {

		$this->values['books'] = $this->get_cache('weread_books');

		$this->values['reviews'] = $this->get_cache('weread_reviews');

		$this->values['ratings'] = $this->get_cache('weread_ratings');
	}

	/**
	 * Get data.
	 * @return value
	 * @access protected
	 */
	function get($value){
		return (isset($this->values[$value]) ? $this->values[$value] : '0');
	}

	
	function getBooks() {
		return $this->get('books');
	}

	
	function getReviews() {
		return $this->get('reviews');
	}

	
	function getRatings() {
		return $this->get('ratings');
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