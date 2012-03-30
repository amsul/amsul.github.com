<?php
/**
 * WPSDVimeo.
 * @author dligthart <info@daveligthart.com>
 * @version 0.3
 * @package wp-stats-dashboard
 */
class WPSDVimeo extends WPSDStats {

	var $xml;
	var $values;
	var $address = '';
	var $un;
	
	/**
	 * WPSDVimeo function.
	 * 
	 * @access public
	 * @return void
	 */
	function WPSDVimeo() {
		
		parent::WPSDStats();
		
		$form = new WPSDAdminConfigForm();
		
		$this->un = trim($form->getWpsdVimeoUn());
		
		if('' != $this->un) {
			
			$this->address = 'http://vimeo.com/' . $this->un; 
			
			if($this->isOutdated()) {
				
				$this->xml = $this->fetchDataRemote($this->address, true, 1, true);
					
				//echo $this->xml;	
					
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
	 * set function.
	 * 
	 * @access public
	 * @return void
	 */
	function set() {
		
		preg_match_all('@<var>([0-9]+)</var>@si', $this->xml, $matches);
	
		$this->values['videos'] = $matches[1][0];
		
		$this->values['likes'] = $matches[1][1];
		
		$this->values['contacts'] = $matches[1][2];
		
		$this->set_cache('vimeo_v', $this->values['videos']);
		
		$this->set_cache('vimeo_l', $this->values['likes']);
		
		$this->set_cache('vimeo_c' , $this->values['contacts']);
	}
	
	/**
	 * Get count.
	 * 
	 * @param $type
	 * @param $data
	 * @return integer count
	 * @access protected
	 */
	function get_count($type, $data) {

		preg_match("@id=\"{$type}\">([0-9]+)</div>@si", $data, $matches);
	
		return number_format($matches[1]);
	}
	
	/**
	 * set_cached function.
	 * 
	 * @access public
	 * @return void
	 */
	function set_cached() {
		$this->values['videos'] = $this->get_cache('vimeo_v');
		$this->values['likes'] = $this->get_cache('vimeo_l');
		$this->values['contacts'] = $this->get_cache('vimeo_c');
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
	 * getVideos function.
	 * 
	 * @access public
	 * @return integer
	 */
	function getVideos() {
		return $this->get('videos');
	}
	
	/**
	 * getLikes function.
	 * 
	 * @access public
	 * @return integer
	 */
	function getLikes() {
		return $this->get('likes');
	}
	
	/**
	 * getContacts function.
	 * 
	 * @access public
	 * @return integer
	 */
	function getContacts() {
		return $this->get('contacts');
	}
	
	/**
	 * getGroups function.
	 * 
	 * @access public
	 * @return integer
	 */
	function getGroups() {
		return $this->get('groups');
	}
	
	/**
	 * getChannels function.
	 * 
	 * @access public
	 * @return integer
	 */
	function getChannels() {
		return $this->get('channels');
	}
	
	/**
	 * getAlbums function.
	 * 
	 * @access public
	 * @return integer
	 */
	function getAlbums() {
		return $this->get('albums');
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