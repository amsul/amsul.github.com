<?php
/**
 * WPSDDeliousRank. Number of incoming delicous bookmarks.
 * @author dligthart <info@daveligthart.com>
 * @version 0.4
 * @package wp-stats-dashboard
*/
class WPSDDeliciousRank extends WPSDStats {

	/**
	 * result
	 * 
	 * @var mixed
	 * @access public
	 */
	var $result;
	
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
	 * (default value: 'http://feeds.delicious.com/v2/json/urlinfo/data?hash=')
	 * 
	 * @var string
	 * @access public
	 */
	var $address = 'http://www.delicious.com/search?p='; //= 'http://feeds.delicious.com/v2/json/urlinfo/data?hash=';
	
	/**
	 * address2
	 * 
	 * (default value: '')
	 * 
	 * @var string
	 * @access public
	 */
	var $address2 = '';
	
	/**
	 * domain
	 * 
	 * (default value: '')
	 * 
	 * @var string
	 * @access public
	 */
	var $domain =  '';
	
	/**
	 * xml
	 * 
	 * (default value: '')
	 * 
	 * @var string
	 * @access public
	 */
	var $xml = '';
	
	/**
	 * xml2
	 * 
	 * (default value: '')
	 * 
	 * @var string
	 * @access public
	 */
	var $xml2 = '';
	
	/**
	 * WPSDDeliciousRank function.
	 * 
	 * @access public
	 * @param mixed $domain
	 * @param bool $curl. (default: false)
	 * @return void
	 */
	function WPSDDeliciousRank($domain, $curl = false) {
		
		parent::WPSDStats();
		
		$this->domain = $domain;

		if(substr($domain, strlen($domain)-1) != '/') {
			
			$domain .= '/';
		}

		$this->domain = md5($domain);
		
		$form = new WPSDAdminConfigForm();
		
		$search = urlencode(trim($form->getWpsdDeliciousSearch()));
		
		$this->address2 = trim($form->getWpsdDeliciousUri());
		
		if('' != $search) {
										
			$this->address .= "{$search}&chk=&fr=del_icio_us&lc=1&atags=&rtags=&context=all";
			
			//echo $this->address;
				
			if($this->isOutdated()) {
				
				$this->xml = $this->fetchDataRemote($this->address,false,1,true);
				
				$this->result = $this->get_data_curl();
				
				$this->xml2 = $this->fetchDataRemote($this->address2,false,1,true);
				
				$this->set();
				
			} else {
				
				$this->set_cached();
			}
		}
		
	}

	/**
	 * get_data function.
	 * 
	 * @access public
	 * @return void
	 */
	function get_data() {
		
		$url = $this->address;
		
		$result = file_get_contents($url);
		
		return $result;
	}

	/**
	 * set function.
	 * 
	 * @access public
	 * @return void
	 */
	function set() {
	
		$this->values['linksin'] = number_format($this->result->total_posts);
		
		$this->set_cache('del_rank', $this->values['linksin']);
		
		preg_match('@<strong>([0-9]+) Results</strong>@', $this->xml, $matches);
	
		$this->values['results'] = number_format($matches[1]);
		
		$this->set_cache('delicious_results', $this->values['results']);
		
		preg_match('@Saved ([0-9]+) times@', $this->xml2, $matches);
		
		$this->values['url_results'] = number_format($matches[1]);
		
		$this->set_cache('delicious_url_res', $this->values['url_results']);
	}
	
	/**
	 * set_cached function.
	 * 
	 * @access public
	 * @return void
	 */
	/*function set_cached() {
	
		$this->values['linksin'] = $this->get_cache('del_rank');
	}*/
	
	/**
	 * set_cached function.
	 * 
	 * @access public
	 * @return void
	 */
	function set_cached() {
		
		$this->values['results'] = $this->get_cache('delicious_results');
		
		$this->values['linksin'] = $this->get_cache('del_rank');
		
		$this->values['url_results'] = $this->get_cache('delicious_url_res');
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
	 * getLinksIn function.
	 * 
	 * @access public
	 * @return string number of links
	 */
	function getLinksIn() {
	
		return $this->get('linksin');
	}
	
	/**
	 * getResultCount function.
	 * 
	 * @access public
	 * @return string number of results
	 */
	function getResultCount() {
	
		return $this->get('results');
	}

	/**
	 * getUrlResultCount function.
	 * 
	 * @access public
	 * @return string number of results
	 */
	function getUrlResultCount() {
		
		return $this->get('url_results');
	}
	
	/**
	 * Get home address.
	 * @return url
	 * @access public
	 * @deprecated
	 */
	function getHomeAddress() {
	
		$domain = str_replace('http://', '', $this->domain);
	
		$domain = str_replace('https://', '', $domain);
	
		$domain = str_replace('www.', '', $domain);
	
		return 'http://delicious.com/search?p='.$domain.'&u=&chk=&context=&fr=del_icio_us&lc=0';
	}
	
	/**
	 * getAddress function.
	 * 
	 * @access public
	 * @return string url
	 */
	function getAddress() {
		
		return $this->address;
	}

	/**
	 * Get data by curl.
	 * @param string $domain
	 * @access public
	 * @deprecated
	 */
	function get_data_curl() {
		$res = '';
		if(function_exists('curl_init')) {
			
			$url = 'http://feeds.delicious.com/v2/json/urlinfo/data?hash=' . $this->domain;
			$ch = curl_init();
			$timeout = 5;
			curl_setopt($ch,CURLOPT_URL, $url);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, $timeout);
			$temp = curl_exec($ch);
			curl_close($ch);

			$json = new Services_JSON();
			$result = $json->decode($temp);

			if($result != null && is_array($result)) {
				$res = $result[0];
			}
		}
		return $res;
	}
}
?>