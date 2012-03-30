<?php
/**
 * WPSDBaseForm.
 * @author Dave Ligthart <info@daveligthart.com>
 * @version 0.1
 * @package wp-stats-dashboard
 */
class WPSDBaseForm {

	// Exclude vars from save.
	var $exclude_vars = array();

	/**
	 * BaseForm.
	 * __construct()
	 */
	function WPSDBaseForm(){
	}

	/**
	 * Fetch html form identifier html.
	 * @return String html
	 * @access public
	 */
	function htmlFormId(){
		return "<input type=\"hidden\" name=\"form_name\" id=\"form_name\" value=\"".get_class($this)."\" />\n";
	}

	/**
	 * Is valid form?
	 * @return boolean is valid
	 * @access public
	 */
	function isValidForm(){
		return ($_POST["form_name"] == get_class($this));
	}

	/**
	 * Set form values.
	 * @return	boolean succes
	 * @access protected
	 */
	function setFormValues(){
		/**
		 * Check if the right form is submitted.
		 */
		if(!empty($_POST) && $this->isValidForm()){
			foreach($_POST as $key=>$value){
				$this->$key = $value;
			}
			return true;
		}
		return false;
	}
	/**
	 * Exclude vars from save.
	 * @param array $names variable names
	 * @access protected
	 */
	function setExcludeVars($names = array()){
		$this->exclude_vars = $names;
	}

	/**
	 * Get excluded vars.
	 * @return array excluded vars
	 * @access protected
	 */
	function getExcludeVars(){
		return $this->exclude_vars;
	}

	/**
	 * Get class vars.
	 * @return	array	class variables with values
	 * @access protected
	 */
	function getClassVars(){
		$class_vars = get_object_vars($this);
		return $class_vars;
	}

	/**
	 * Save form values to database.
	 * @param integer $post_id
	 * @access protected
	 */
	function save($post_id){
		if($post_id > 0){
			$class_vars = $this->getClassVars();
			foreach($class_vars as $key=>$value){
				if(!in_array($key, $this->exclude_vars)) {
					if(!update_post_meta($post_id, $key, $value)){
						// if failed add the meta.
						if(!add_post_meta($post_id, $key, $value)){
							//echo " failed";
						}
					}
				}
			}
		}
	}

	/**
	 * Delete post.
	 * @param integer $post_id
	 * @return Object post
	 */
	function delete($post_id){
		if($post_id != "" && $post_id > 0)
			return wp_delete_post($post_id);
	}

	/**
	 * Load form values from database.
	 * @param integer $post_id
	 * @access protected
	 */
	function load($post_id){
		if($post_id > 0){
			$class_vars = $this->getClassVars();
			foreach($class_vars as $key=>$value){
				$this->$key = $this->loadItem($post_id, $key);
			}
		}
	}

	/**
	 * Load single form value.
	 * @param	integer	$post_id
	 * @param	integer $key
	 * @access protected
	 */
	function loadItem($post_id, $key){
		return get_post_meta($post_id, $key, true);
	}

	/**
	 * Save options.
	 */
	function saveOptions(){
		if(!empty($_POST)){
			$class_vars = $this->getClassVars();
			foreach($class_vars as $key=>$value){
				if($key != "" && !in_array($key, $this->exclude_vars)) {
					if(!update_option($key, $value)){
						if(!add_option($key, $value)){
							//echo "failed to add option!";
						}
					}
				}
			}
		}
	}

	/**
	 * Load options.
	 */
	function loadOptions(){
		$class_vars = $this->getClassVars();
		foreach($class_vars as $key=>$value){
			$this->$key = $this->loadOption($key);
		}
	}

	/**
	 * Load option.
	 * @param String $key option key
	 * @return String option value
	 */
	function loadOption($key){
		if('' != $key) {	
			$value = get_option($key); 
			if(null != $value) {
				return stripslashes($value);
			}	
		}
	}
}
?>