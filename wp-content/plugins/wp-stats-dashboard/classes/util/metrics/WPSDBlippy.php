<?php
/**
 * WPSDBlippy.
 * 
 * @author dligthart <info@daveligthart.com>
 * @version 0.2
 * @package wp-stats-dashboard
 */
class WPSDBlippy extends WPSDStats {

	var $xml;
	var $values;
	var $address = '';
	var $un;
	
	/**
	 * WPSDBlippy.
	 * 
	 * @param boolean $curl
	 */
	function WPSDBlippy($curl = false) {
		
		parent::WPSDStats();
		
		$form = new WPSDAdminConfigForm();
		
		$this->un = trim($form->getWpsdBlippyUn());
		
		if('' != $this->un) {
					
			$this->address = 'http://blippy.com/' . $this->un;
				
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
	 * Set data.
	 */
	function set() {

		/*preg_match_all("@<span class=\"number\">([0-9]+)</span>@si", $this->xml, $matches);
		
		if(null != $matches) {
			
			//print_r($matches);
			
			$this->values['purchases'] =  $matches[1][0]; 
			$this->values['reviewed_purchases'] =  $matches[1][1]; 
			$this->values['awesomes'] =  $matches[1][2]; 
			$this->values['funnys'] =  $matches[1][3]; 
			$this->values['informatives'] =  $matches[1][4]; 
			$this->values['omgwtfs'] =  $matches[1][5]; 
		
			$this->set_cache('blippy_purchases', $this->values['purchases']);
			$this->set_cache('blippy_rpurchases', $this->values['reviewed_purchases']);
			$this->set_cache('blippy_awesomes', $this->values['awesomes']);
			$this->set_cache('blippy_funnys', $this->values['funnys']);
			$this->set_cache('blippy_informatives', $this->values['informatives']);
			$this->set_cache('blippy_omgwtfs', $this->values['omgwtfs']); */
			
			/*$this->values['followers'] =  $matches[1][0]; //$this->getCount('Followers', $this->xml);
		
			$this->values['following'] =  $matches[1][1]; //$this->getCount('Following', $this->xml);
		
			$this->set_cache('blippy_followers', $this->values['followers']);
		
			$this->set_cache('blippy_following', $this->values['following']);	
			
			$this->values['purchases'] = $matches[1][2];//$this->getCount('Purchases', $this->xml);
		
			$this->values['accounts'] =  $matches[1][3]; //$this->getCount('Accounts', $this->xml);

			$this->set_cache('blippy_purchases', $this->values['purchases']);
		
			$this->set_cache('blippy_accounts', $this->values['accounts']);*/
		//}

		/*preg_match("@<h4 class=\"blue\">Following &mdash; ([0-9]+)</h4>@si", $this->xml, $matches);
		
		$this->values['following'] = number_format($matches[1]);
		
		$this->set_cache('blippy_following', $this->values['following']); */
		
		
		preg_match("@([0-9]+) reviews@si", $this->xml, $matches);
		
		$this->values['reviews'] = $matches[1];
		
		$this->set_cache('blippy_reviews', $this->values['reviews']);

	}
		
	/**
	 * Set cached.
	 */
	function set_cached() {
		/*$this->values['purchases'] = $this->get_cache('blippy_purchases');
		$this->values['accounts'] =  $this->get_cache('blippy_accounts');
		$this->values['followers'] = $this->get_cache('blippy_followers');
		$this->values['following'] = $this->get_cache('blippy_following');*/
		
		/*$this->values['purchases'] =  $this->get_cache('blippy_purchases');
		$this->values['reviewed_purchases'] =  $this->get_cache('blippy_rpurchases');
		$this->values['awesomes'] =  $this->get_cache('blippy_awesomes');
		$this->values['funnys'] =  $this->get_cache('blippy_funnys');
		$this->values['informatives'] = $this->get_cache('blippy_informatives');
		$this->values['omgwtfs'] =  $this->get_cache('blippy_omgwtfs');		
		$this->values['following'] = $this->get_cache('blippy_following');*/
		
		$this->values['reviews'] = $this->get_cache('blippy_reviews');
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
	 * getReviews function.
	 * 
	 * @access public
	 * @return void
	 */
	function getReviews() {
		return $this->get('reviews');
	}
	
	/**
	 * Get purchases.
	 * 
	 * @return integer purchases
	 */
	function getPurchases() {
		return $this->get('purchases');
	}
	
	/**
	 * Get reviewed purchases.
	 * 
	 * @return integer reviewed purchases
	 */
	function getReviewedPurchases() {
		return $this->get('reviewed_purchases');
	}
	
	/**
	 * Get awesomes.
	 * 
	 * @return integer awesomes
	 */
	function getAwesomes() {
		return $this->get('awesomes');
	}
	
	/**
	 * Get funnys.
	 * 
	 * @return integer funnys
	 */
	function getFunnys() {
		return $this->get('funnys');
	}
	
	/**
	 * Get informatives.
	 * 
	 * @return integer informatives
	 */
	function getInformatives() {
		return $this->get('informatives');
	}
	
	/**
	 * Ow my god what the f*ks.
	 * 
	 * @return integer omgwtfs
	 */
	function getOmgwtfs() {
		return $this->get('omgwtfs');
	}
	
	/**
	 * @deprecated 
	 * @return integer accounts
	 */
	function getAccounts() {
		return $this->get('accounts');
	}
	
	/**
	 * @deprecated
	 * @return integer followers
	 */
	function getFollowers() {
		return $this->get('followers');
	}
	
	/**
	 * Get following.
	 * 
	 * @return integer following
	 */
	function getFollowing() {
		return $this->get('following');
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