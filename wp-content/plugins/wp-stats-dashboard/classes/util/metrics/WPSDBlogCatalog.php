<?php
/**
 * WPSDBlogCatalog. Ranking on blogcatelog.com.
 * @author dligthart <info@daveligthart.com>
 * @version 0.2
 * @package wp-stats-dashboard
 */
class WPSDBlogCatalog extends WPSDStats {

	var $xml;
	var $values;
	var $address = '';
	var $home = 'http://blogcatalog.com/';
	var $domain  = '';
	var $un;
	
	/**
	 * WPSDBlogCatalog.
	 * 
	 * @param $domain
	 * @param $curl
	 * @return unknown_type
	 */
	function WPSDBlogCatalog($domain, $curl = false) {
		
		parent::WPSDStats();
		
	///	$this->domain = $this->getNormalizedUrl($domain);

		//$this->address = "http://api.blogcatalog.com/bloginfo?bcwsid=yK7v16bA8r&url={$this->domain}";
		
		$form = new WPSDAdminConfigForm();

		$this->un = $form->getWpsdBlogCatalogUn();

		$this->address = 'http://www.blogcatalog.com/user/' . $this->un;;
		
		if($this->isOutdated()) {
			
			$this->xml = $this->fetchDataRemote($this->address);
			
			$this->set();
			
		} else {
			
			$this->set_cached();
		}
	}

	/**
	 * Set data.
	 * @access protected
	 */
	function set() {
		
		preg_match_all('@div class="button_count">([0-9]+)</div>@si', $this->xml, $matches);
		
		//print_r($matches);
		
		$this->values['blog'] = $matches[1][0];
		
		$this->values['following'] = $matches[1][1];
		
		$this->values['followers'] = $matches[1][2];
		
		$this->values['reading'] = $matches[1][3];
		
		$this->values['discussions'] = $matches[1][4];
		
		$this->set_cache('bc_blog', $this->values['blog']);
		
		$this->set_cache('bc_following', $this->values['following']);
		
		$this->set_cache('bc_followers', $this->values['followers']);
		
		$this->set_cache('bc_reading', $this->values['reading']);
		
		$this->set_cache('bc_discussions', $this->values['discussions']);
				
		/*$rank = $this->getTagValue('rank', $this->xml);
		$this->values['rank'] = ($rank !='')?$rank:'0';

		$views = $this->getTagValue('views', $this->xml);
		$this->values['views'] = ($views != '')?$views: '0';
		
		$hits = $this->getTagValue('hits', $this->xml);
		$this->values['hits'] = ($hits != '')?$hits:'0';
		
		$this->values['home'] = $this->getTagValue('bcurl', $this->xml);
		
		if('' == $this->values['home']) {

			$this->values['home'] = $this->home;
		}
		
		$this->set_cache('bc_rank', $this->values['rank']);
		$this->set_cache('bc_views', $this->values['views']);
		$this->set_cache('bc_hits', $this->values['hits']);
		$this->set_cache('bc_home', $this->values['home']);*/
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	function set_cached() {
		/*$this->values['rank'] = $this->get_cache('bc_rank');
		$this->values['views'] = $this->get_cache('bc_views');
		$this->values['hits'] = $this->get_cache('bc_hits');
		$this->values['home'] = $this->get_cache('bc_home');*/
		
		$this->values['blog'] = $this->get_cache('bc_blog');
		
		$this->values['following'] = $this->get_cache('bc_following');
		
		$this->values['followers'] = $this->get_cache('bc_followers');
		
		$this->values['reading'] = $this->get_cache('bc_reading');
		
		$this->values['discussions'] = $this->get_cache('bc_discussions');
	}
	
	/**
	 * Get tag value.
	 * @param string $tagname
	 * @access protected
	 */
	function getTagValue($tag, $xml) {
		$preg = "|<$tag>(.*?)</$tag>|s";
		preg_match_all($preg, $xml, $tags);
		return trim($tags[1][0]);
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
	 * getBlog function.
	 * 
	 * @access public
	 * @return void
	 */
	function getBlog() {
		return $this->get('blog');
	}
	
	/**
	 * getFollowers function.
	 * 
	 * @access public
	 * @return void
	 */
	function getFollowers() {
		return $this->get('followers');
	}
	
	/**
	 * getFollowing function.
	 * 
	 * @access public
	 * @return void
	 */
	function getFollowing() {
		return $this->get('following');
	}
	
	/**
	 * getRead function.
	 * 
	 * @access public
	 * @return void
	 */
	function getReading() {
		return $this->get('reading');
	}
	
	/**
	 * getDiscussions function.
	 * 
	 * @access public
	 * @return void
	 */
	function getDiscussions(){
		return $this->get('discussions');
	}

	/**
	 * Get rank.
	 * @return rank
	 * @access public
	 * @deprecated
	 */
	function getRank() {
		return $this->get('rank');
	}
	
	/**
	 * Get views.
	 * 
	 * @return integer views
	 * @access public
	 * @deprecated
	 */
	function getViews() {
		return $this->get('views');
	}
	
	/**
	 * Get hits.
	 * 
	 * @return integer hits
	 * @access public
	 * @deprecated
	 */
	function getHits() {
		return $this->get('hits');
	}

	/**
	 * Get address.
	 * 
	 * @return address
	 * @access public
	 */
	function getAddress() {
		return $this->address;
	}

	/**
	 * Get compete.com home address.
	 * @return url
	 * @access public
	 */
	function getHomeAddress() {
		return $this->getAddress();
	}
}
?>