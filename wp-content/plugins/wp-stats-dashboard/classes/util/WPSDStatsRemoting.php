<?php
/**
 * WPSDRemoting class.
 * @author daveligthart.com
 * @version 0.1
 * @package wp-stats-dashboard
 * @subpackage util
 */
class WPSDStatsRemoting {

	/**
	 * _blogId
	 * 
	 * @var mixed
	 * @access public
	 */
	var $_blogId = 0;
	/**
	 * _serviceUrl
	 * 
	 * (default value: 'http://dashboard.wordpress.com/wp-admin/index.php?page=stats&blog=%d')
	 * 
	 * @var string
	 * @access public
	 */
	var $_serviceUrl = 'http://dashboard.wordpress.com/wp-admin/index.php?page=stats&unit=1&blog=%s';
	/**
	 * _statsData
	 * 
	 * (default value: array())
	 * 
	 * @var array
	 * @access public
	 */
	var $_statsData = array();
	/**
	 * _processed
	 * 
	 * (default value: false)
	 * 
	 * @var bool
	 * @access public
	 */
	var $_processed = false;
	/**
	 * _debug
	 * 
	 * (default value: false)
	 * 
	 * @var bool
	 * @access public
	 */
	var $_debug = false;
	
	/**
	 * WPSDStatsRemoting function.
	 * 
	 * @access public
	 * @return void
	 */
	function WPSDStatsRemoting($blogId) {
		
		if(!$blogId) { 
			
			$this->_blogId = get_option('wpsd_blog_id');
		}
		else {
			
			$this->_blogId = $blogId;
		}
	}
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @param mixed $blogId
	 * @return void
	 */
	function __construct($blogId) {
		
		$this->WPSDStatsRemoting($blogId);
	}
	
	/**
	 * connectToService function.
	 * 
	 * @access public
	 * @return void
	 */
	function connectToService() {
		
		wpsd_login();
	}
	/**
	 * getServiceUrl function.
	 * 
	 * @access public
	 * @return void
	 */
	function getServiceUrl() {
		
		return sprintf($this->_serviceUrl, $this->_blogId);
	}
	/**
	 * getRemoteData function.
	 * 
	 * @access public
	 * @param mixed $url
	 * @return void
	 */
	function getRemoteData($url) {
	
		$this->connectToService();
			
		$ch = curl_init();
	
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_COOKIEFILE,  wpsd_get_cookie());
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.2.8) Gecko/20100727 Firefox/3.6.8");
		curl_setopt($ch, CURLOPT_REFERER, get_bloginfo('wpurl') . '/wp-admin/index.php?page=stats');
		curl_setopt($ch, CURLOPT_URL, $url);
	
		$result = curl_exec($ch);
	
		curl_close ($ch);
	
		return $result;
	}
	
	/**
	 * splitData function.
	 * 
	 * @access public
	 * @param mixed $result
	 * @param mixed $key
	 * @return void
	 */
	function splitData($result, $key) {
			
		preg_match_all('/<h4><strong>Pageviews:<\/strong> (.*?)<\/h4>/s', $result, $temp);
			
		if(null != $temp && !empty($temp[1][0])) {
			
			$this->_statsData['views'][$key] = $temp[1][0];
		}
		
		$pattern = '<div .*?class="statsdiv">(.*?)<\/div>';
				
		preg_match_all('/'.$pattern.'/s', $result, $matches);
							
		if(null != $matches && is_array($matches)) { 		
							
			foreach($matches[1] as $row) {

				if(null != $row) {
					
					$m = array();
	
					if(stristr($row, 'referrers')) {

						$this->processMatches(
							$this->getMatches(
								'<td class="label"><a href=\'.*?\'>(.*?)<\/a><\/td><td class="views">(.*?)<\/td>', $row),
								'referrers', $key);
																
					}
					elseif(stristr($row, 'top posts')) {

						$this->processMatches(
							$this->getMatches(
								'target=\'_blank\'>(.*?)<\/a><\/span><\/td>
			<td class="more"><a href=\'.*?\'><img src=".*?" alt=".*?" \/><\/a><\/td><td class="views">(.*?)<\/td>', $row),
								'posts', $key);
					
					}
					elseif(stristr($row, 'search engine terms')) {
					
						$this->processMatches(
							$this->getMatches(
								'<td class="label">(.*?)<\/td><td class="views">(.*?)<\/td>', $row),
								'search_engine_terms', $key);
					
					}
					elseif(stristr($row, 'clicks')) {
					
						$this->processMatches(
							$this->getMatches(
								'<td class="label"><a href=\'.*?\'>(.*?)<\/a><\/td><td class="views">(.*?)<\/td>', $row),
								'clicks', $key);
					
					}
				}
			}
		}
		
		$this->dumpStatsData();
		
		return true;
	}
	
	/**
	 * dumpRawData function.
	 * 
	 * @access public
	 * @param mixed $data
	 * @return void
	 */
	function dumpRawData($data) {
		
		if($this->_debug)
			print_r($data);
	}
	
	/**
	 * dumpStatsData function.
	 * 
	 * @access public
	 * @return void
	 */
	function dumpStatsData() {
		
		if($this->_debug)
			print_r($this->_statsData);
	}
	
	/**
	 * processMatches function.
	 * 
	 * @access public
	 * @param mixed $matches
	 * @param mixed $key
	 * @return mixed
	 */
	function processMatches($matches, $key, $key2 = '') {
		
		if(!empty($key) && is_array($matches)) {
			
			if(isset($matches[1]) && isset($matches[2])) {
				
				$set = array();
				
				for($i=0; $i < count($matches[1]); $i++) {
					
					$val1 = trim($matches[1][$i]);
					$val2 = trim($matches[2][$i]);
					
					if(null == $val1 || '' == $val1) {
						
						$val1 = 'empty';
					}
					
					if(null == $val2 || '' == $val2) {
						
						$val2 = 0;
					}
										
					array_push($set, (array($val1 => $val2)));
				}
				
				if(!empty($key2)) {
					
					$this->_statsData[$key][$key2] = $set;
					
				} else { 
					
					$this->_statsData[$key] = $set;
				}
				
				$this->_processed = true;
				
				return $this->_statsData[$key];
			}
		}		
		
		return false;
	}
	
	/**
	 * getMatches function.
	 * 
	 * @access public
	 * @param mixed $pattern
	 * @param mixed $row
	 * @return void
	 */
	function getMatches($pattern, $row) {
		
		if(!empty($pattern)) {
					
			preg_match_all('/' . $pattern . '/s', $row, $m);
					
			return $m;
		}
		
		return false;
	}
	
	/**
	 * getViewsByDate function.
	 * 
	 * @access public
	 * @param mixed $date
	 * @return void
	 */
	function getViewsByDate($date) {
		
		if($this->updateStatsData($date)) { 
		
			return $this->_statsData['views'];
		}
		
		return array(-1);
	}
	
	/**
	 * updateStatsData function.
	 * 
	 * @access public
	 * @param string $date. (default: '')
	 * @return boolean
	 */
	function updateStatsData($date = '') {
	
		if(empty($date)) {
			
			 $date = date('Y-m-d');
		} 
		
		if(!$this->_processed) { 
			
			return $this->splitData(
					$this->getRemoteData(
						sprintf('%s&day=%s', $this->getServiceUrl(), $date)), $date);
		}
		
		return false;
	}
	
	/**
	 * getReferrersByDate function.
	 * 
	 * @access public
	 * @param mixed $date
	 * @return void
	 */
	function getReferrersByDate($date) {
				
		if($this->updateStatsData($date)) { 
		
			return $this->_statsData['referrers'];
		}
		
		return array(-1);
	}
	/**
	 * getSearchEngineTermsByDate function.
	 * 
	 * @access public
	 * @param mixed $date
	 * @return void
	 */
	function getSearchEngineTermsByDate($date) {
		
		if($this->updateStatsData($date)) { 
		
			return $this->_statsData['search_engine_terms'];
		}
		
		return array(-1);
	}
	/**
	 * getClicksByDate function.
	 * 
	 * @access public
	 * @param mixed $date
	 * @return void
	 */
	function getClicksByDate($date) {
		
		if($this->updateStatsData($date)) { 
		
			return $this->_statsData['clicks'];
		}
		
		return array(-1);
	}
	/**
	 * getPostsByDate function.
	 * 
	 * @access public
	 * @param mixed $date
	 * @return void
	 */
	function getPostsByDate($date) {
		
		if($this->updateStatsData($date)) { 
		
			return $this->_statsData['posts'];
		}
		
		return array(-1);
	}	
	
	/**
	 * getViewsByYearAndMonth function.
	 * 
	 * @access public
	 * @return void
	 */
	function getViewsByYearAndMonth() {
	
		$result = $this->getRemoteData(sprintf('http://dashboard.wordpress.com/wp-admin/index.php?page=stats&view=table&blog=%d', $this->_blogId));
		
		return $this->parseTable(str_replace(',', '', $result));	
	}
	
	/**
	 * parseTable function.
	 * 
	 * @access public
	 * @param mixed $html
	 * @return array
	 */
	function parseTable($html)	
	{
  		// Find the table
  		preg_match("/<table.*?>.*?<\/[\s]*table>/s", $html, $table_html);
  		
  		// Get title for each row
  		preg_match_all("/<th>(.*?)<\/[\s]*th>/", $table_html[0], $matches);
  		$row_headers = $matches[1];
  		
  		// Iterate each row
  		preg_match_all("/<tr.*?>(.*?)<\/[\s]*tr>/s", $table_html[0], $matches);
  		
  		$table = array();
  		
  		$years = array();
  		
  		foreach($matches[1] as $row_html) {
  		
  			if($row_html) { 
  			  		  		
  		  		preg_match_all("/<th>(.*?)<\/[\s]*th>/", $row_html, $th_matches);
  		  		  		
  		  		$year = $th_matches[1][0];
  		  		
  		  		$years[$year] = $year;
  		  		  		
  		  		preg_match_all("/<td.*?>(.*?)<\/[\s]*td>/", $row_html, $td_matches);
  		  	
  		  		$row = array();
  		  	
  		  		for($i=0; $i<count($td_matches[1]); $i++) {
  		  			  		  			
  		   	 			$td = trim(strip_tags(html_entity_decode($td_matches[1][$i])));
  		   				
  		   				if(null == $td) $td = 0;
  		   				
  		   				$row[$year][$row_headers[$i]] = $td;
 						
  		  		}
  		
  		  		if(count($row) > 0) {
  		  		
					array_pop($row[$year]);
  		    	
  		    		$table[] = $row;
  		    	}
  			}
  		}
  		
  		return $table;
	} 		
}
?>