<?php
/**
 * WPSDFlickr.
 * @author dligthart <info@daveligthart.com>
 * @version 0.3
 * @package wp-stats-dashboard
 */
class WPSDFlickr extends WPSDStats {

	var $xml;
	var $values;
	var $address = '';
	var $api_uri = 'http://api.flickr.com/services/rest/?method=';
	var $key = '52123af8cdf75d105c54dd47acf0edfe';
	var $user_id; 
	
	/**
	 * WPSDFlickr.
	 * @param string $domain
	 * @param boolean $curl
	 */
	function WPSDFlickr() {
		
		parent::WPSDStats();
					
		$form = new WPSDAdminConfigForm();
			
		$this->address = 'http://www.flickr.com/photos/' . trim($form->getWpsdFlickrUsername());
		
		if($this->isOutdated() && '' != $this->address) {

			$this->set();

		} else {

			$this->set_cached();
		}
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	function setUserId() {
		
		$method = $this->api_uri . 'flickr.urls.lookupUser&api_key='.$this->key.'&url=' .$this->address. '&auth_token=&api_sig=';		
			
		$data = $this->fetchDataRemote($method);
				
		$id = $this->get_match_attr('id', $data, 1);
		
		if(null != $id) {

			$this->set_cache('flickr_id', $id);
		}

		return $id;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	function setViews() {
		
		$method = $this->api_uri . 'flickr.people.getInfo&api_key='.$this->key.'&user_id='.$this->user_id.'&auth_token=&api_sig=';		
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	function setCount() {
		
		$method = $this->api_uri . 'flickr.people.getInfo&api_key='.$this->key.'&user_id='.$this->user_id; //'&auth_token=&api_sig=';		
				
		$data = $this->fetchDataRemote($method);
		
		$c = number_format($this->get_match('count', $data, 1));
			
		$this->set_cache('flickr_c', $c);

		return $c;
	}
	
	/**
	 * Set data.
	 */
	function set() {
		
		$this->user_id = $this->setUserId();
		
		$this->values['count'] = $this->setCount();

		//$this->values['views'] = $this->setViews();

	}
	
	/**
	 * Get match attr.
	 * 
	 * @param $type
	 * @param $data
	 * @param $match_index
	 * @return unknown_type
	 */
	function get_match_attr($type, $data, $match_index = 1) {
		
		preg_match("@{$type}=\"(.+)\"@si", $data, $matches);

		return $matches[$match_index];
	}
	
	/**
	 * Get match tag.
	 * 
	 * @param $type
	 * @param $data
	 * @param $match_index
	 * @return unknown_type
	 */
	function get_match($type, $data, $match_index = 1) {
			
		preg_match("@<{$type}>(.*?)</{$type}>@si", $data, $matches);

		return $matches[$match_index];
	}

	/**
	 * Set cached.
	 */
	function set_cached() {

		$this->values['count'] = $this->get_cache('flickr_c');

		$this->values['views'] = $this->get_cache('flickr_v');
	}

	/**
	 * Get data.
	 * @return value
	 * @access protected
	 */
	function get_number($value){

		return (isset($this->values[$value]) ? $this->values[$value] : '0');
	}
	
	/**
	 * Get count.
	 * 
	 * @return integer count
	 */
	function getCount() {
			
		return $this->values['count'];
	}
	
	/**
	 * Get Views
	 * 
	 * @return integer views
	 */
	function getViews() {

		return $this->values['views'];
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