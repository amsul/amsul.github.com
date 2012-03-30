<?php
/**
 * WPSDBlogPulse.
 * @author dligthart <info@daveligthart.com>
 * @version 0.2
 * @package wp-stats-dashboard
 */
class WPSDBlogPulse extends WPSDStats {
	/**
	 * xml
	 * 
	 * @var mixed
	 * @access public
	 */
	var $xml;
	/**
	 * xml2
	 * 
	 * @var mixed
	 * @access public
	 */
	var $xml2;
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
	 * domain
	 * 
	 * (default value: '')
	 * 
	 * @var string
	 * @access public
	 */
	var $domain = '';
	
	/**
	 * profile_address
	 * 
	 * (default value: '')
	 * 
	 * @var string
	 * @access public
	 */
	var $profile_address = '';
	
	/**
	 * conversation_count
	 * 
	 * @var mixed
	 * @access public
	 */
	var $conversation_count = 0;
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @param string $domain. (default: '')
	 * @return void
	 */
	function __construct($domain = '') {
		
		parent::__construct();
		
		if('' != $domain) { 
		
			$domain = $this->getNormalizedUrl($domain);
		
			$this->address = "http://www.blogpulse.com/search?query=" . $domain;
		
			$this->domain = $domain;
		
			if($this->isOutdated() && '' != $domain) {
			
				$this->xml = $this->fetchDataRemote($this->address, false, 1, true);
			
				$this->set();
			
			} else {
			
				$this->set_cached();
			}
		}
	}
	
	/**
	 * WPSDBlogPulse function.
	 * 
	 * @access public
	 * @param string $domain. (default: '')
	 * @return void
	 */
	function WPSDBlogPulse($domain = '') {	
		
		$this->__construct($domain);
	}
	
	/**
	 * getGraphImgUrl function.
	 * 
	 * @access public
	 * @return void
	 */
	function getGraphImgUrl() {
		
		$domain = $this->getNormalizedUrl(get_bloginfo('url'));
		
		$res = $this->fetchDataRemote('http://www.blogpulse.com/trend?query1='.$domain.'&label1=&query2=&label2=&query3=&label3=&days=180&x=34&y=9', false, 1, true);	
		
		preg_match('@/graphs/(.+)\.png@', $res, $matches);
		
	//	print_r($matches);
		
		return "http://www.blogpulse.com/graphs/{$matches[1]}.png";
	}
	
	/**
	 * getConversations function.
	 * 
	 * @access public
	 * @return string html
	 */
	function getConversations() {
				
		$domain = get_bloginfo('url');
		
		$url = "http://blogpulse.com/conversation?query=&link={$domain}&max_results=200&start_date=20100101&Submit.x=18&Submit.y=11&Submit=Submit";
		
		//echo $url;
		
		$res = $this->fetchDataRemote($url, false, 1, true);	 // , false, 1, true
		
	//	echo $res;
		
		$out = '';
		
		preg_match_all('@href="(.*?)">@si', $res, $matches);
		
		$i = 0;
		
		if(null != $matches && null != $matches[1])  {
			
			$out .='<ol>';
			
			foreach($matches[1] as $m) { 
				
				if(null != $m && stristr($m, 'http') && !stristr($m, 'nielsen') && !stristr($m, 'nmincite') && !stristr($m, 'linksmall')) { 
					
					$out .= '<li><a href="'.$m.'" target="_blank">' . $m . '</a></li>';
					
					$i++;
				}			
			}
			
			$out .= '</ol>';
		}
		
		$this->conversation_count = $i;
		
		$this->set_cache('blogpulse_conversations', $this->conversation_count);
		
		return $out;
	}

	/**
	 * set function.
	 * 
	 * @access public
	 * @return void
	 */
	function set() {
			
		preg_match('@([0-9]+) messages@', $this->xml, $matches);
		
		$this->values['results'] = $matches[1];
		
		$this->set_cache('blogpulse_results', $this->values['results']);
		
	}
	
	/**
	 * set_cached function.
	 * 
	 * @access public
	 * @return void
	 */
	function set_cached() {
	
		$this->values['results'] = $this->get_cache('blogpulse_results');
		
		$this->conversation_count = $this->get_cache('blogpulse_conversations');
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
	 * getResults function.
	 * 
	 * @access public
	 * @return integer count
	 */
	function getResults() {
		return $this->get('results');
	}
	
	/**
	 * getConversationCount function.
	 * 
	 * @access public
	 * @return integer count
	 */
	function getConversationCount() {
		return $this->conversation_count;
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
	
	/**
	 * getProfileAddress function.
	 * 
	 * @access public
	 * @return void
	 */
	function getProfileAddress() {
		return $this->profile_address;
	}
}
?>