<?php
/**
 * WPSDAmplify.
 *
 * @author dligthart <info@daveligthart.com>
 * @version 0.3
 * @package wp-stats-dashboard
 */
class WPSDAmplify extends WPSDStats {

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

		$this->un = trim($form->getWpsdAmplifyUn());

		if('' != $this->un) {

			$this->address = 'http://'.$this->un.'.amplify.com';
				
			if($this->isOutdated()) {

				$this->xml = $this->fetchDataRemote($this->address, false, 3, false);

				$this->set();

			} else {

				$this->set_cached();
			}
		}
	}
	
	/**
	 * WPSDAmplify function.
	 * 
	 * @access public
	 * @return void
	 */
	function WPSDAmplify() {

		$this->__construct();
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

		preg_match_all("@0px;\">([0-9]+)<@", $this->xml, $matches);
	
	//	print_r($matches);

		$this->values['followers'] = number_format($matches[1][0]);
			
		$this->set_cache('amplify_f', $this->values['followers']);
		
		$this->values['sources'] = number_format($matches[1][1]);
			
		$this->set_cache('amplify_s', $this->values['sources']);

		$this->values['posts'] = number_format($matches[1][2]);
			
		$this->set_cache('amplify_p', $this->values['posts']);
		
	}

	/**
	 * Set cached.
	 */
	function set_cached() {

		$this->values['followers'] = $this->get_cache('amplify_f');

		$this->values['posts'] = $this->get_cache('amplify_p');
		
		$this->values['sources'] = $this->get_cache('amplify_s');
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
	 * Get followers
	 *
	 * @return integer number of followers
	 * @access public
	 */
	function getFollowers() {
		return $this->get('followers');
	}

	/**
	 * Get number of posts.
	 *
	 * @return integer posts
	 * @access public
	 */
	function getPosts() {
		return $this->get('posts');
	}

	/**
	 * Get number of sources.
	 * 
	 * @return integer sources
	 * @access public
	 */
	function getSources() {
		return $this->get('sources');
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