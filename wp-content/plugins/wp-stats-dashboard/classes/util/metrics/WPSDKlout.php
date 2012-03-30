<?php 
/**
 * WPSDKlout.
 * @author dligthart <info@daveligthart.com>
 * @version 0.3
 * @package wp-stats-dashboard
 */

if(!class_exists('Services_JSON')) {
	include_once(realpath(dirname(__FILE__).'/..') . '/Services_JSON.php');
}

class WPSDKlout extends WPSDStats {

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
	 * home
	 * 
	 * (default value: 'http://klout.com/')
	 * 
	 * @var string
	 * @access public
	 */
	var $home = 'http://klout.com/';
	/**
	 * un
	 * 
	 * (default value: '')
	 * 
	 * @var string
	 * @access public
	 */	
	var $un = '';
	
	/**
	 * WPSDKlout function.
	 * 
	 * @access public
	 * @param string $username. (default: '')
	 * @return void
	 */
	function WPSDKlout($username = '') {
		
		parent::WPSDStats();	
		
		$this->loadData($username);
	}
	
	/**
	 * loadData function.
	 * 
	 * @access public
	 * @param string $username. (default: '')
	 * @return void
	 */
	function loadData($username = '') {
		
		if('' == $username) { 		
		
			$form = new WPSDAdminConfigForm();
		
			$this->un = trim($form->getWpsdTwitterUn());
		} 
		else {
			
			$this->un = $username;
		}
		
		$request = 'http://api.klout.com/1/klout.json?key=ztfmf92wg98k8s5pyv49x3kv&users=' . $this->un;
		
		$this->address = 'http://klout.com/profile/summary/'.$this->un.'/';
								
		if($this->is_cache_outdated('klout_cache', $this->un)) {
			
			$this->xml = $this->fetchDataRemote($request);
			
			$this->set();
			
			$this->updated_cache('klout_cache', $this->un);
		} 
		else {
			
			$this->set_cached();
		}
	}
	
	/**
	 * isKloutOutdated function.
	 * 
	 * @access public
	 * @return void
	 */
	function isKloutOutdated() {
		
		$arr = get_option('wpsd_klout_cache_dates');
		
		if(is_array($arr) && isset($arr[$this->un])) {
			
			if($arr[$this->un] == date('Y-m-d')) {
				
				return false;
			}
		} 
		
		return true;
	}
	
	/**
	 * kloutCacheUpdated function.
	 * 
	 * @access public
	 * @return void
	 */
	function kloutCacheUpdated() {
		
		$arr = get_option('wpsd_klout_cache_dates');
		
		if(!is_array($arr)) {
		
			$arr = array();
		}
		
		$arr[$this->un] = date('Y-m-d');
			
		update_option('wpsd_klout_cache_dates', $arr);
	}

	/**
	 * Json decode.
	 * @param string $content
	 * @param boolean $assoc
	 * @return json decoded string
	 * @access protected
	 */
	function jsonDecode($content, $assoc = FALSE){
		if ($assoc) {
			
	    	$json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
	    	
	    } else {
	   		
	    	$json = new Services_JSON;
	   	}
	   	
	   	return $json->decode($content);
	}
	
	/**
	 * Set user.
	 * 
	 * @return unknown_type
	 */
	function setUser() {
			
		if('' != $this->un) {
		
			$ret = $this->jsonDecode( 
				$this->fetchDataRemote('http://api.klout.com/1/users/show.json?key=ztfmf92wg98k8s5pyv49x3kv&users='.$this->un)
			, 1);
			
			$this->values['type'] = $ret['users'][0]['score']['kclass'];
			
			$this->values['desc'] = $ret['users'][0]['score']['kclass_description'];
			
			$this->update_cache('kl_type_' . $this->un, $this->values['type']);
			
			$this->update_cache('kl_desc_' . $this->un, $this->values['desc']);
		} 
	}
	
	/**
	 * Set data.
	 * @access protected
	 */
	function set() {
		
		// Set score.
		
		$ret = $this->jsonDecode($this->xml, 1);
		
		$rank = number_format($ret['users'][0]['kscore'], 2);
	
		$this->values['rank'] = ($rank !='')?$rank:'0';

		$this->update_cache('kl_score_' . $this->un, $this->values['rank']);
		
		// Set user type, desc.
		
		$this->setUser();
	}
	
	/**
	 * Set get cached data.
	 * 
	 * @return unknown_type
	 */
	function set_cached() {
		
		$this->values['rank'] = $this->get_cached('kl_score_' . $this->un);
		
		$this->values['type'] = $this->get_cached('kl_type_' . $this->un);
		
		$this->values['desc'] = $this->get_cached('kl_desc_' . $this->un);
	}
	
	/**
	 * Get integer.
	 * 
	 * @return value
	 * @access protected
	 */
	function get($value){
		return (isset($this->values[$value]) ? $this->values[$value] : '0');
	}

	/**
	 * Get rank.
	 * 
	 * @return integer rank
	 * @access public
	 */
	function getRank() {
		return $this->get('rank');
	}
	
	/**
	 * Get description.
	 * 
	 * @return string description
	 * @access public
	 */
	function getDescription() {
		return $this->values['desc'];
	}
	
	/**
	 * Get description.
	 * 
	 * @return string description
	 * @access public
	 */
	function getDesc() {
		return $this->getDescription();
	}
	
	/**
	 * Get type.
	 * 
	 * @return unknown_type
	 */
	function getType() {
		return $this->values['type'];
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