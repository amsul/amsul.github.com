<?php
/**
 * WPSDNetlog.
 * @author dligthart <info@daveligthart.com>
 * @version 0.2
 * @package wp-stats-dashboard
 */
class WPSDNetlog extends WPSDStats {

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
	 * WPSDNetlog function.
	 * 
	 * @access public
	 * @return void
	 */
	function WPSDNetlog() {
		
		parent::WPSDStats();
		
		$form = new WPSDAdminConfigForm();
		
		$this->un = trim($form->getWpsdNetlogUn());
		
		if('' != $this->un) {
			
			$this->address = 'http://en.netlog.com/' . $this->un . '/';
			
			if($this->isOutdated()) {
				
				$this->xml = $this->fetchDataRemote($this->address);
				
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
		
		preg_match_all('@skinCounter big">([0-9]+)@', $this->xml, $matches);
		
		//print_r($matches);
		
		$this->values['friends'] = $matches[1][0];
				
		$this->values['guestbook'] = $matches[1][1];
		
		$this->values['pictures'] = $matches[1][2];
		
		$this->values['blog'] = $matches[1][3];
				
		$this->set_cache('netlog_friends', $this->values['friends']);
		
	//	$this->set_cache('netlog_groups', $this->values['groups']);
		
		$this->set_cache('netlog_guestbook', $this->values['guestbook']);
		
		$this->set_cache('netlog_pictures', $this->values['pictures']);
		
		$this->set_cache('netlog_blog', $this->values['blog']);
		
	//	$this->set_cache('netlog_links', $this->values['links']);
		
		preg_match('@([0-9]+) visitors@', $this->xml, $matches);
		
		$this->values['visitors'] = $matches[1];
		
		$this->set_cache('netlog_visitors', $this->values['visitors']);
	}
	
	/**
	 * set_cached function.
	 * 
	 * @access public
	 * @return void
	 */
	function set_cached() {
		
		$this->values['friends'] = $this->get_cache('netlog_friends');
		
		$this->values['groups'] = $this->get_cache('netlog_groups');
		
		$this->values['guestbook'] = $this->get_cache('netlog_guestbook');
		
		$this->values['pictures'] = $this->get_cache('netlog_pictures');
		
		$this->values['blog'] = $this->get_cache('netlog_blog');
		
		$this->values['links'] = $this->get_cache('netlog_links');
		
		$this->values['visitors'] = $this->get_cache('netlog_visitors');
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
	 * getFriends function.
	 * 
	 * @access public
	 * @return integer
	 */
	function getFriends() {
		return $this->get('friends');
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
	 * getGuestbook function.
	 * 
	 * @access public
	 * @return integer
	 */
	function getGuestbook() {
		return $this->get('guestbook');
	}
	
	/**
	 * getPictures function.
	 * 
	 * @access public
	 * @return integer
	 */
	function getPictures() {
		return $this->get('pictures');
	}
	
	/**
	 * getBlog function.
	 * 
	 * @access public
	 * @return integer
	 */
	function getBlog() {
		return $this->get('blog');
	}
	
	/**
	 * getLinks function.
	 * 
	 * @access public
	 * @return integer
	 */
	function getLinks() {
		return $this->get('links');
	}
	
	/**
	 * getVisitors function.
	 * 
	 * @access public
	 * @return integer
	 */
	function getVisitors() {
		return $this->get('visitors');
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