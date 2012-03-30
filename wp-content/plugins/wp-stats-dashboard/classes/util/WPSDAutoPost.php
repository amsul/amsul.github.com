<?php
/**
 * WPSDAutoPost class.
 * @author dligthart <info@daveligthart.com>
 * @package wp-stats-dashboard
 * @subpackage util
 * @version 0.2
 */
class WPSDAutoPost {
	
	/**
	 * _amplifyEmail
	 * 
	 * (default value: '')
	 * 
	 * @var string
	 * @access private
	 */
	var $_amplifyEmail = '';
	
	/**
	 * _posterousEmail
	 * 
	 * (default value: '')
	 * 
	 * @var string
	 * @access private
	 */
	var $_posterousEmail = '';
	
	/**
	 * WPSDAutoPost function.
	 * 
	 * @access public
	 * @return void
	 */
	function WPSDAutoPost() {
		
	}
	
	/**
	 * setAmplifyEmail function.
	 * 
	 * @access public
	 * @param mixed $amplifyEmail
	 * @return void
	 */
	function setAmplifyEmail($amplifyEmail) {
		
		$this->_amplifyEmail = $amplifyEmail;
	}
	
	/**
	 * setPosterousEmail function.
	 * 
	 * @access public
	 * @param mixed $posterousEmail
	 * @return void
	 */
	function setPosterousEmail($posterousEmail) {
		
		$this->_posterousEmail = $posterousEmail;
	}
	
	/**
	 * post function.
	 * 
	 * @access public
	 * @param mixed $title
	 * @param mixed $content
	 * @param string $email (default: '')
	 * @return void
	 */
	function post($title, $content, $email = '') {
		
		$success = array('amplify' => false, 'posterous' => false);
		
		if($this->_amplifyEmail) {
			
   			//return wp_mail($this->_amplifyEmail, $title, $content);
   			
   			$success['amplify'] = mail($this->_amplifyEmail, $title, $content);
		}
		
		if($this->_posterousEmail) {
			
			if($email) $email = "-f{$email}";
						
			$success['posterous'] = mail($this->_posterousEmail, $title, $content, null, $email);
		}
		
		return $success;
	}
}
?>