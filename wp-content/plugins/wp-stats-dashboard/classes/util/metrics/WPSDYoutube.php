<?php
/**
 * WPSDYoutube.
 *
 * @author dligthart <info@daveligthart.com>
 * @version 0.4
 * @package wp-stats-dashboard
 */
class WPSDYoutube extends WPSDStats {

	/**
	 * xml
	 * 
	 * @var string
	 * @access public
	 */
	var $xml;
	/**
	 * values
	 * 
	 * @var array
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
	 * @var string
	 * @access public
	 */
	var $un = '';
	
	/**
	 * WPSDYoutube function.
	 * 
	 * @access public
	 * @return void
	 */
	function WPSDYoutube() {
		
		parent::WPSDStats();
		
		$form = new WPSDAdminConfigForm();
		
		$this->un = trim($form->getWpsdYoutubeUn());
		
		if('' != $this->un) {
			
			$this->address = 'http://www.youtube.com/' . $this->un; 
			
			if($this->isOutdated()) {
				
				$this->xml = str_replace('.','', str_replace(',', '', $this->fetchDataRemote($this->address)));
						
				$this->set();
				
			} else {
				
				$this->set_cached();
			}
		}
	}
	
	/**
	 * isEnabled function.
	 * 
	 * @access public
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
		
		preg_match_all('@stat-value">([0-9]+)@', $this->xml, $matches);
		
		if(null != $matches) { 
			
			$this->values['views'] = $matches[1][1];
		
			$this->values['subscribers'] = $matches[1][0];
		
			$this->set_cache('youtube_v', $this->values['views']);
			
			$this->set_cache('youtube_s' , $this->values['subscribers']);
		}
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
	
		$this->values['views'] = $this->get_cache('youtube_v');
	
		$this->values['subscribers'] = $this->get_cache('youtube_s');
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
	 * Get subscribers.
	 * 
	 * @return integer subscribers
	 * @access public
	 */
	function getSubscribers() {
		return $this->get('subscribers');
	}
	
	/**
	 * Get views.
	 * 
	 * @return integer views
	 * @access public
	 */
	function getViews() {
		return $this->get('views');
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