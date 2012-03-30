<?php
/**
 * WPSDQuora.
 *
 * @author dligthart <info@daveligthart.com>
 * @version 0.3
 * @package wp-stats-dashboard
 */
class WPSDQuora extends WPSDStats {
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
	 * WPSDQuora.
	 */
	function WPSDQuora() {

		parent::WPSDStats();

		$form = new WPSDAdminConfigForm();

		$this->un = trim($form->getWpsdQuoraUn());

		if('' != $this->un) {

			$this->address = 'http://www.quora.com/'. $this->un;
				
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
		
		$matches = array();

		preg_match_all("@<span class=\"light normal\">([0-9]+)@", $this->xml, $matches);
		
	//	var_dump($matches);
		
		$this->values['followers'] = number_format($matches[1][0]);
			
		$this->set_cache('quora_followers', $this->values['followers']);
		
		$this->values['following'] = number_format($matches[1][1]);
			
		$this->set_cache('quora_following', $this->values['following']);

		//$this->values['mentions'] = number_format($matches[1][2]);
			
		//$this->set_cache('quora_mentions', $this->values['mentions']); 
		
	}

	/**
	 * Set cached.
	 */
	function set_cached() {

		$this->values['followers'] = $this->get_cache('quora_followers');

		$this->values['following'] = $this->get_cache('quora_following');
		
		//$this->values['mentions'] = $this->get_cache('quora_mentions'); 
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
	 * getMentions function.
	 * 
	 * @access public
	 * @return void
	 */
	function getMentions() {
		return $this->get('mentions');
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