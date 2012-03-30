<?php
/**
 * WPSDScore.
 * @author dligthart <info@daveligthart.com>
 * @version 0.1
 * @package wp-stats-dashboard
 */
class WPSDScore extends WPSDStats {

	/**
	 * userId
	 * 
	 * @var integer
	 * @access public
	 */
	var $userId;
	/**
	 * score
	 * 
	 * @var float
	 * @access public
	 */
	var $score;
	
	/**
	 * WPSDScore function.
	 * 
	 * @access public
	 * @param mixed $userId
	 * @return void
	 */
	function WPSDScore($userId) {
		
		parent::WPSDStats();
			
		$this->userId = $userId;
				
		if($this->userId) {
			
			if($this->is_cache_outdated('wpsdscore', $this->userId)) {
				
				$this->set();
				
				$this->updated_cache('wpsdscore', $this->userId);
				
			} else {
				
				$this->set_cached();
			}
		}
	}
		
	/**
	 * set function.
	 * 
	 * @access public
	 * @return void
	 */
	function set() {
				
		if($this->userId) { 		
			
			$this->update_cache('wpsd_score_' . $this->userId, $score);
		}
	}
	
	/**
	 * set_cached function.
	 * 
	 * @access public
	 * @return void
	 */
	function set_cached() {
		
		if($this->userId) { 
			
			$this->score = $this->get_cached('wpsd_score_' . $this->userId);
		}
	}
	
	/**
	 * getScore function.
	 * 
	 * @access public
	 * @return void
	 */
	function getScore() {
		
		return $this->score;
	}	
}
?>