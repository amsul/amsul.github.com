<?php
/**
 * WPSDFoursquare.
 *
 * @author dligthart <info@daveligthart.com>
 * @version 0.5
 * @package wp-stats-dashboard
 */
class WPSDFoursquare extends WPSDStats {
	
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
	 * WPSDFoursquare function.
	 * 
	 * @access public
	 * @param bool $curl. (default: false)
	 * @return void
	 */
	function WPSDFoursquare($curl = false) {
		
		parent::WPSDStats();
		
		$form = new WPSDAdminConfigForm();
		
		$this->un = trim($form->getWpsdFoursquareUn());
		
		if('' != $this->un) {
					
			$this->address = 'https://foursquare.com/' . $this->un;
			
			//echo $this->address;
			
			if($this->isOutdated()) {
				
				$this->xml = $this->fetchDataRemote($this->address, false, 1, true);
				
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
		
		// Checkins 
		
		// Days out
		
		// Things done
		
		preg_match_all('@>([0-9]+)</h2>@', str_replace(',','', $this->xml), $matches);
				
	//	preg_match("@DAYS OUT <strong class=\"notranslate\" style=\"left:7px\">([0-9]+)@si", $this->xml, $matches);
		
		$this->values['do'] = number_format($matches[1][1]);
		
	//	preg_match("@CHECK-INS<strong class=\"notranslate\">([0-9]+)@si", $this->xml, $matches);
		
		$this->values['ci'] = number_format($matches[1][0]);
		
		$this->values['td'] = number_format($matches[1][2]);
		
		$this->set_cache('foursquare_do', $this->values['do']);
		
		$this->set_cache('foursquare_ci', $this->values['ci']);
		
		$this->set_cache('foursquare_td', $this->values['td']);
		
		/*preg_match('@Friends <span class=\"notranslate\">\(([0-9]+) total\)@', $this->xml, $matches);
		
		$this->values['fr'] = $matches[1];
		
		$this->set_cache('foursquare_fr', $this->values['fr']);
		
		preg_match('@Mayorships <span class=\"notranslate\">\(([0-9]+)\)@', $this->xml, $matches);
		
		$this->values['ms'] = $matches[1];
		
		$this->set_cache('foursquare_ms', $this->values['ms']);
		
		preg_match('@Badges <span class=\"notranslate\">\(([0-9]+)\)@', $this->xml, $matches);
		
		$this->values['bg'] = $matches[1];
		
		$this->set_cache('foursquare_bg', $this->values['bg']);	*/			
	}
	
	/**
	 * set_cached function.
	 * 
	 * @access public
	 * @return void
	 */
	function set_cached() {

		//$this->values['no'] = $this->get_cache('foursquare_no');
		
		$this->values['do'] = $this->get_cache('foursquare_do');
		$this->values['ci'] = $this->get_cache('foursquare_ci');
		$this->values['td'] = $this->get_cache('foursquare_td'); 
		
		//$this->values['fr'] = $this->get_cache('foursquare_fr');
		//$this->values['ms'] = $this->get_cache('foursquare_ms');
		//$this->values['bg'] = $this->get_cache('foursquare_bg');
	}
	
	/**
	 * get function.
	 * 
	 * @access public
	 * @param mixed $value
	 * @return string
	 */
	function get($value){
		return (isset($this->values[$value]) ? $this->values[$value] : '0');
	}

	/**
	 * getTotalDaysOut function.
	 * 
	 * @access public
	 * @return integer
	 */
	function getTotalDaysOut() {
	
		return $this->get('do');
	}
	
	/**
	 * getTotalNightsOut function.
	 * 
	 * @access public
	 * @return integer
	 */
	function getTotalNightsOut() {
	
		return $this->get('no');
	}
	
	/**
	 * getTotalCheckins function.
	 * 
	 * @access public
	 * @return integer
	 */
	function getTotalCheckins() {
	
		return $this->get('ci');
	}
	
	/**
	 * getFriends function.
	 * 
	 * @access public
	 * @return integer
	 */
	function getFriends() {
		
		return $this->get('fr');
	}
	
	/**
	 * getMayorships function.
	 * 
	 * @access public
	 * @return integer
	 */
	function getMayorships() {
		
		return $this->get('ms');
	}
	
	/**
	 * getBadges function.
	 * 
	 * @access public
	 * @return integer
	 */
	function getBadges() {
		
		return $this->get('bg');
	}
	
	/**
	 * getThingsDone function.
	 * 
	 * @access public
	 * @return integer
	 */
	function getThingsDone() {
		
		return $this->get('td');
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