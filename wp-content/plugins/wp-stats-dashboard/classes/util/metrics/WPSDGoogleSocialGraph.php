<?php
/**
 * WPSDGoogleSocialGraph.
 * @author dligthart <info@daveligthart.com>
 * @version 0.3
 * @copyright Copyright &copy; 2009, dligthart
 * @package wp-stats-dashboard
 */

if(!class_exists('Services_JSON')) {
	include_once(realpath(dirname(__FILE__).'/..') . '/Services_JSON.php');
}

class WPSDGoogleSocialGraph extends WPSDStats {

	var $host = 'http://socialgraph.apis.google.com';
	var $opt_keys = array('edi','edo','fme','pretty','callback','sgn');
	var $default_options = array('edi' => TRUE, 'edo' => TRUE,'fme' => TRUE, 'pretty' => 0, 'callback' => NULL, 'sgn' => NULL);
	var $urls = array();

	var $default_relations = array('friend','contact','acquaintance','met','co-worker','colleague','co-resident','neighbor');

	/**
	 * WPSDGoogleSocialGraph.
	 * @param array $urls
	 * @access public
	 */
	function WPSDGoogleSocialGraph($urls = array()) {
		
		parent::WPSDStats();
		
		$this->urls = $urls;
	}

	/**
	 * Json decode.
	 * @param string $content
	 * @param boolean $assoc
	 * @return json decoded string
	 * @access protected
	 */
	function jsonDecode($content, $assoc = FALSE){
		if ($assoc) {
	    	$json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
	    } else {
	   		$json = new Services_JSON;
	   	}
	   	return $json->decode($content);
	}

	/**
	 * Json encode.
	 * @param string $content
	 * @return encoded json
	 * @access protected
	 */
	function jsonEncode($content) {
		$json = new Services_JSON;
		return $json->encode($content);
	}

	/**
	 * Query.
	 * @param array $options
	 * @access protected
	 */
	function query($options = array()) {
		if (count($options) == 0){
			$options = $this->default_options;
		}

		if (is_string($this->urls)){
			$qs['q'] = urlencode($this->urls);
		}

		if (is_array($this->urls)) {
			foreach ($this->urls as $k => $v) {
				$qs_array[] = urlencode($v);
			}
			$qs['q'] = implode(",",$qs_array);
		}

		$query = array_merge($qs, $options);

		$query_string = '';
		foreach ($query as $k=>$v) {
			$query_string[] = $k."=".$v;
		}

		$query_string = implode("&",$query_string);

		$path = "/lookup?";

		$res = $this->get_data_curl($this->host.$path.$query_string);

		return $this->jsonDecode($res, 1);
	}

	/**
	 * Referred to as. "Followers".
	 * @param array $relationships
	 * @return referenced by
	 * @access public
	 */
	function referred_to_as($relationship = array()) {
		return $this->relationship_map($relationship,'nodes_referenced_by');
	}

	/**
	 * Refers to as. "Following".
	 * @param array $relationships array("me") | array("friend")
	 * @return nodes referenced
	 * @access public
	 */
	function refers_to_as($relationship = array()) {
		return $this->relationship_map($relationship,'nodes_referenced');
	}

	/**
	 * Mutual references as. "Followers".
	 * @param array $relationships
	 * @return references
	 * @access public
	 */
	function mutual_reference_as($relationship = array()) {
		return array_unique(array_merge($this->relationship_map($relationship,'nodes_referenced_by')));
	}

	/**
	 * Relationship mapping.
	 * @param array $relationships
	 * @param string $key
	 * @access protected
	 */
	function relationship_map($relationships, $key) {
		$results = $this->query();
		$nodes = $results['nodes'];
		$nodeRet = array();
		if(null != $nodes && is_array($nodes)) {
			foreach ($nodes as $url => $refs) {
				$relations = $refs[$key];			
				if(null != $relations && is_array($relations)) {
					foreach ($relations as $relUrl => $val) {
						$types = $val['types'];
						foreach ($relationships as $r) {
							if (in_array($r,$types)) {
								$nodeRet[] = $relUrl;
							}
						}
					}
				}
			}
		}
		return array_unique($nodeRet);
	}

	/**
	 * Array equal.
	 * @param string $a
	 * @param string $b
	 * @access protected
	 */
	function array_equal($a, $b) {
		return (is_array($a) && is_array($b) && array_diff($a, $b) === array_diff($b, $a));
	}

	/**
	 * Get data curl.
	 * @param string $address url
	 * @return result
	 * @access protected
	 */
	function get_data_curl($address) {
		$res = '';
		if(function_exists('curl_init')) {
			$url = $address;
		
			$ch = curl_init();
			$timeout = 5;
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
			$res = curl_exec($ch);
			curl_close($ch);
		
		}
		return $res;
	}

	/**
	 * Number of nodes pointing out.
	 * @return node count
	 * @access public
	 */
	function getNodeOutCount() {
		
		if($this->isOutdated()) {
			
			$oc = count($this->refers_to_as($this->default_relations));
			
			$this->set_cache('gsg_oc', $oc);
		}
		
		return $this->get_cache('gsg_oc');
	}

	/**
	 * Number of nodes pointing in.
	 * @return node count
	 * @access public
	 */
	function getNodeInCount() {
		
		if($this->isOutdated()) {

			$ic = count($this->referred_to_as($this->default_relations));
			
			$this->set_cache('gsg_ic', $ic);
		}
		
		return $this->get_cache('gsg_ic');
	}

	/**
	 * Print nodes out.
	 * @access public
	 */
	function printNodesOut() {
		$nodes = $this->refers_to_as($this->default_relations);
		$this->printNodes($nodes);
	}

	/**
	 * Print nodes out.
	 * @access public
	 */
	function printNodesIn() {
		$nodes = $this->referred_to_as($this->default_relations);
		$this->printNodes($nodes);
	}
	/**
	 * Print nodes.
	 * @param array $nodes
	 * @access protected
	 */
	function printNodes($nodes) {
		if(is_array($nodes) && count($nodes) > 0) {
			echo '<ul class="wpsd_xfn_list">';
			foreach($nodes as $node) {
				echo '<li><a href="'.$node.'" target="_blank" title="xfn node">' . $node . '</a></li>';
			}
			echo '</ul>';
		}
	}
	/**
	 * Get home address.
	 * @return url
	 * @access public
	 */
	function getHomeAddress() {
		return 'http://socialgraph-resources.googlecode.com/svn/trunk/samples/findyours.html?q=' . get_bloginfo('url');
	}
}
/*
$test = new WPSDGoogleSocialGraph(array('http://www.daveligthart.com'));

echo 'in: ' . $test->getNodeInCount() . ' | out: ' . $test->getNodeOutCount();

echo '<h2>IN</h2>';
$test->printNodesIn();

echo '<h2>OUT</h2>';
$test->printNodesOut();

echo '<br/>';

echo '<p>A:';
print_r($test->refers_to_as(array('friend')));
print_r($test->referred_to_as(array('friend')));
print_r($test->mutual_reference_as(array('friend')));
echo '</p>';

echo '<p>B';
print_r($test->refers_to_as(array('me')));
print_r($test->referred_to_as(array('me')));
print_r($test->mutual_reference_as(array('me')));
echo '</p>';

//print_r($test->query());
*/
?>