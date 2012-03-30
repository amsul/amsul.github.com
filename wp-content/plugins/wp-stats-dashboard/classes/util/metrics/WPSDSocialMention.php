<?php
/**
 * WPSDSocialMention.
 *
 * @author dligthart <info@daveligthart.com>
 * @version 0.1
 * @package wp-stats-dashboard
 */
class WPSDSocialMention extends WPSDStats {

	var $xml;
	var $values;
	var $address = '';

	/**
	 * WPSDSocialMention.
	 *
	 * @param string  $domain
	 * @param boolean $curl
	 */
	function WPSDSocialMention($curl = false) {

		parent::WPSDStats();

		$this->address = 'http://socialmention.com/search?q=' . urlencode(get_bloginfo('url')) . '&t=all&btnG=Search';

		if($this->isOutdated()) {

			$this->set();

		} else {

			$this->set_cached();
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

		$data = strip_tags($this->fetchDataRemote($this->address, true, 1, true));
				
		//Set followers.
		$this->values['mentions'] = $this->get_count('mentions', $data);

		$this->set_cache('socialmention_mentions', $this->values['mentions']);
	}

	/**
	 * Get count.
	 *
	 * @param unknown $type
	 * @param unknown $data
	 * @return integer
	 */
	function get_count($type, $data) {

		preg_match("@([0-9]+) {$type}@", $data, $matches);

		return number_format($matches[1]);
	}

	/**
	 * set_cached function.
	 *
	 * @access public
	 * @return void
	 */
	function set_cached() {

		$this->values['mentions'] = $this->get_cache('socialmention_mentions');
	}

	/**
	 * Get data.
	 *
	 * @return value
	 * @access protected
	 */
	function get($value){
		return (isset($this->values[$value]) ? $this->values[$value] : '0');
	}


	/**
	 * getMentions function.
	 *
	 * @access public
	 * @return integer number of mentions
	 */
	function getMentions() {
		return $this->get('mentions');
	}

	/**
	 * Get address.
	 *
	 * @return address
	 * @access public
	 */
	function getAddress() {
		return $this->address;
	}
}
?>