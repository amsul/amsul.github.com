<?php
/**
 * WPSDYahooBuzz.
 * @author dligthart <info@daveligthart.com>
 * @version 0.1
 * @deprecated services no longer exists.
 * @package wp-stats-dashboard
 */
class WPSDYahooBuzz extends WPSDStats {
	
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
	 * uri
	 * 
	 * @var mixed
	 * @access public
	 */
	var $uri;
	
	/**
	 * WPSDYahooBuzz function.
	 * 
	 * @access public
	 * @return void
	 */
	function WPSDYahooBuzz() {
		
		parent::WPSDStats();
		
		$form = new WPSDAdminConfigForm();
		
		$this->uri = trim($form->getWpsdYahooBuzzUri());
		
		if('' != $this->uri) {
					
			$this->address = $this->uri;
				
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
		return ('' != $this->uri);
	}
	
	/**
	 * set function.
	 * 
	 * @access public
	 * @return void
	 */
	function set() {
	
		$this->xml = str_replace(',','',$this->xml);
		 
		preg_match_all('@<td class="value">([0-9]+)</td>@si', $this->xml, $matches);
		
	//	print_r($matches);
		
		$this->values['first_buzzer'] = number_format($matches[1][0]);
		
		$this->values['buzzed_up'] = number_format($matches[1][1]);
		
		$this->values['buzzed_down'] = number_format($matches[1][2]);
		
		$this->values['comments'] = number_format($matches[1][3]);
		
		$this->set_cache('yahoobuzz_fb', $this->values['first_buzzer']);
		
		$this->set_cache('yahoobuzz_bu', $this->values['buzzed_up']);

		$this->set_cache('yahoobuzz_bd', $this->values['buzzed_down']);
		
		$this->set_cache('yahoobuzz_c', $this->values['comments']);
	}
	
	/**
	 * set_cached function.
	 * 
	 * @access public
	 * @return void
	 */
	function set_cached() {
			
		$this->values['first_buzzer'] = number_format($this->get_cache('yahoobuzz_fb'));
		
		$this->values['buzzed_up'] =  number_format($this->get_cache('yahoobuzz_bu'));
		
		$this->values['buzzed_down'] =  number_format($this->get_cache('yahoobuzz_bd'));
		
		$this->values['comments'] =  number_format($this->get_cache('yahoobuzz_c'));	
	}
	
	/**
	 * get function.
	 * 
	 * @access public
	 * @param mixed $value
	 * @return void
	 */
	function get($value){
		
		return (isset($this->values[$value]) ? $this->values[$value] : '0');
	}
	
	/**
	 * getFirstBuzzer function.
	 * 
	 * @access public
	 * @return void
	 */
	function getFirstBuzzer() {
		
		return $this->values['first_buzzer'];
	}
	
	/**
	 * getBuzzedUp function.
	 * 
	 * @access public
	 * @return void
	 */
	function getBuzzedUp() {
		
		return $this->values['buzzed_up'];
	}
	
	/**
	 * getBuzzedDown function.
	 * 
	 * @access public
	 * @return void
	 */
	function getBuzzedDown() {
		
		return $this->values['buzzed_down'];
	}
	
	/**
	 * getComments function.
	 * 
	 * @access public
	 * @return void
	 */
	function getComments() {
		
		return $this->values['comments'];
	}	
	
	/**
	 * getAddress function.
	 * 
	 * @access public
	 * @return void
	 */
	function getAddress() {
		
		return $this->address;
	}
}
?>