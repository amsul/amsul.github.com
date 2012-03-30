<?php
/**
 * WPSDEducoPark.
 * @author dligthart <info@daveligthart.com>
 * @version 0.1
 * @package wp-stats-dashboard
 */
class WPSDEducoPark extends WPSDStats {
	
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
	 * WPSDEducoPark function.
	 * 
	 * @access public
	 * @return void
	 */
	function WPSDEducoPark() {
		
		parent::WPSDStats();
		
		$form = new WPSDAdminConfigForm();
		
		$this->un = trim($form->getWpsdEducoParkUn());
		
		if('' != $this->un) {
					
			$this->address = 'http://www.educopark.com/people/view/' . $this->un;
				
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
	 * set function.
	 * 
	 * @access public
	 * @return void
	 */
	function set() {
		
		$this->values['l'] = number_format($this->getMatch('Life Lessons'));
				
		$this->values['lt'] = number_format($this->getMatch('Life Talk'));
		
		$this->values['b'] = number_format($this->getMatch('Books'));
		
		$this->set_cache('educopark_l', $this->values['l']);
		
		$this->set_cache('educopark_lt', $this->values['lt']);		
		
		$this->set_cache('educopark_b', $this->values['b']);
	}
	
	/**
	 * getMatch function.
	 * 
	 * @access public
	 * @param mixed $tag
	 * @return void
	 */
	function getMatch($tag){
		
		preg_match("@([0-9]+) {$tag}@si", $this->xml, $matches);
		
		return $matches[1];
	}
	
	/**
	 * set_cached function.
	 * 
	 * @access public
	 * @return void
	 */
	function set_cached() {
	
		$this->values['l'] = $this->get_cache('educopark_l');
	
		$this->values['lt'] = $this->get_cache('educopark_lt');
		
		$this->values['b'] = $this->get_cache('educopark_b');
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
	 * getLessons function.
	 * 
	 * @access public
	 * @return void
	 */
	function getLessons() {
		
		return $this->values['l'];
	}
	
	/**
	 * getTalks function.
	 * 
	 * @access public
	 * @return void
	 */
	function getTalks() {
		
		return $this->values['lt'];
	}	
	
	function getBooks() {
		
		return $this->values['b'];
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