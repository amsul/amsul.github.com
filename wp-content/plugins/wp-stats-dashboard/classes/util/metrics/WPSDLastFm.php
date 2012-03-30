<?php
/**
 * WPSDLastFm.
 * @author dligthart <info@daveligthart.com>
 * @version 0.5
 * @package wp-stats-dashboard
 */
class WPSDLastFm extends WPSDStats {

	var $xml;
	var $values;
	var $address = 'http://www.last.fm/user/';
	var $api_uri = 'http://ws.audioscrobbler.com/2.0/';
	var $key = '68e5fce9766a4c0d36ee1a2c54ed554a';
	var $user = '';
	
	/**
	 * WPSDLastFm.
	 * @param string $domain
	 * @param boolean $curl
	 */
	function WPSDLastFm() {
		
		parent::WPSDStats();
			
		$form = new WPSDAdminConfigForm();
			
		$this->user = trim($form->getWpsdLastFmUn());
		
		$this->address .= $this->user;
				
		if($this->isOutdated() && '' != $this->user) {
					
			$this->xml = $this->fetchDataRemote($this->address, false, 1 , true);

			$this->set();

		} else {

			$this->set_cached();
		}

	}
	
	/**
	 * Set friends.
	 * 
	 * @return false|integer count
	 */
 	function setFriends() {
		
		$data = $this->getStats($this->user, 'user.getfriends', $this->key);
		
		if($data) {
			
			$c = number_format($this->get_match_attr('total', $data, 1));
				
			$this->set_cache('lastfm_f', $c);
	
			return $c;
		}
		
		return 0;
	}
	
	/**
	 * Get stats.
	 * 
	 * @param unknown_type $user
	 * @param unknown_type $method
	 * @param unknown_type $key
	 * @return string data
	 */
	function getStats($user, $method, $key) {

		if('' == $user || '' == $method || '' == $key) return false;
		
		return $this->fetchDataRemote($this->api_uri . '?method='.$method.'&user='.$user.'&api_key='.$key);
	}
	
	/**
	 * Set data.
	 */
	function set() {
		
		$plays = $this->get_match_plays();
		
		$this->values['friends'] = $this->setFriends();
			
		$this->values['shout'] = number_format($this->get_match('shout', $this->xml));
		
		$this->values['plays'] = $plays; //($this->get_match('tracks played', $this->xml)
		
		$this->values['playlists'] = number_format($this->get_match('Playlists', $this->xml));
		
		$this->values['lovedtracks'] = number_format($this->get_match('Loved Tracks', $this->xml));
		
		$this->values['posts'] = number_format($this->get_match('Posts', $this->xml));
		
		$this->set_cache('lastfm_shout', $this->values['shout']);
		
		$this->set_cache('lastfm_plays', $this->values['plays']);
		
		$this->set_cache('lastfm_playlists', $this->values['playlists']);
		
		$this->set_cache('lastfm_loved', $this->values['lovedtracks']);
		
		$this->set_cache('lastfm_posts', $this->values['posts']);
	}
	
	/**
	 * get_match_plays function.
	 * 
	 * @access public
	 * @return void
	 */
	function get_match_plays() {
	
		preg_match_all("@<span class=\"flip\">([0-9]+)</span>@", $this->xml, $matches);
		
		$ret = '';
		
		if(null != $matches[1]) { 
			
			foreach($matches[1] as $e) {
				
				if(null != $e) {
				
					$ret .= $e;
				}
			}
		}
		
		return $ret;	
	}
	
	/**
	 * Get match attr.
	 * 
	 * @param $type
	 * @param $data
	 * @param $match_index
	 * @return string match
	 * @access protected
	 */
	function get_match_attr($type, $data, $match_index = 1) {
		
		preg_match("@{$type}=\"([0-9]+)\"@s", $data, $matches);

		return $matches[$match_index];
	}
	
	/**
	 * Get match tag.
	 * 
	 * @param $type
	 * @param $data
	 * @param $match_index
	 * @return string match
	 * @access protected
	 */
	function get_match($type, $data, $match_index = 1) {
			
		preg_match("@([0-9]+) {$type}@", $data, $matches);

		return $matches[$match_index];
	}

	/**
	 * Set cached.
	 */
	function set_cached() {

		$this->values['friends'] = $this->get_cache('lastfm_f');
		
		$this->values['shout'] =  $this->get_cache('lastfm_shout');
		
		$this->values['plays'] =   $this->get_cache('lastfm_plays');
		
		$this->values['playlists'] =  $this->get_cache('lastfm_playlists');
		
		$this->values['lovedtracks'] =  $this->get_cache('lastfm_loved');
		
		$this->values['posts'] =  $this->get_cache('lastfm_posts');
	}

	/**
	 * Get data.
	 * 
	 * @return string value
	 * @access protected
	 */
	function get_number($value){

		return (isset($this->values[$value]) ? $this->values[$value] : '0');
	}
	
	
	function getFriends() {
			
		return $this->values['friends'];
	}
	
	function getShout() {
		
		return $this->get('shout');
	}
	
	function getPlaylists() {
		
		return $this->get('playlists');
	}
	
	function getLovedTracks() {
		
		return $this->get('lovedtracks');
	}
	
	function getPlays() {
		
		return $this->get('plays');
	}
	
	function getPosts() {
		
		return $this->get('posts');
	}
	
	/**
	 * Get data.
	 * @return value
	 * @access protected
	 */
	function get($value) {
		
		return (isset($this->values[$value]) ? $this->values[$value] : '0');
	}
	
	/**
	 * Get address.
	 * 
	 * @return string address
	 * @access public
	 */
	function getAddress() {
		
		return $this->address;
	}
}
?>