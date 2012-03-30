<?php
/**
 * WPSDWPPlugin.
 * @author Dave Ligthart <info@daveligthart.com>
 * @version 0.3
 * @package wp-stats-dashboard
 */
class WPSDWPPlugin {

	/**
	 * Plugin name.
	 * @var string
	 **/
	var $plugin_name;

	/**
	 * Plugin 'view' directory.
	 * @var string Directory
	 **/
	var $plugin_base;

	/**
	 * Register your plugin with a name and base directory.  This <strong>must</strong> be called once.
	 *
	 * @param string $name Name of your plugin.  Is used to determine the plugin locale domain
	 * @param string $base Directory containing the plugin's 'view' files.
	 * @return void
	 **/
	function register_plugin ($name, $base) {
		
		$this->plugin_base = rtrim (dirname ($base), '/');
		
		$this->plugin_name = $name;
	
		// Here we manually fudge the plugin locale as WP doesnt allow many options
		$locale = get_locale();
				
		if (empty($locale) || '' == trim($locale)) { 
			
			$locale = 'en_US';
		}
		
		$name = 'wpsd';
		
		$mofile = $this->plugin_base . "/locale/{$locale}.mo";

		load_textdomain ($name, $mofile);
	}


	/**
	 * Register a WordPress action and map it back to the calling object
	 *
	 * @param string $action Name of the action
	 * @param string $function Function name (optional)
	 * @param int $priority WordPress priority (optional)
	 * @param int $accepted_args Number of arguments the function accepts (optional)
	 * @return void
	 */
	function add_action ($action, $function = '', $priority = 10, $accepted_args = 1) {
	
		add_action ($action, array (&$this, $function == '' ? $action : $function), $priority, $accepted_args);
	}


	/**
	 * Register a WordPress filter and map it back to the calling object
	 *
	 * @param string $action Name of the action
	 * @param string $function Function name (optional)
	 * @param int $priority WordPress priority (optional)
	 * @param int $accepted_args Number of arguments the function accepts (optional)
	 * @return void
	 */
	function add_filter ($filter, $function = '', $priority = 10, $accepted_args = 1) {
	
		add_filter ($filter, array (&$this, $function == '' ? $filter : $function), $priority, $accepted_args);
	}


	/**
	 * Renders an admin section of display code
	 *
	 * @param string $ug_name Name of the admin file (without extension)
	 * @param string $array Array of variable name=>value that is available to the display code (optional)
	 * @return void
	 */
	function render_admin ($ug_name, $ug_vars = array (), $action = null) {
		
		global $plugin_base;
		
		foreach ($ug_vars AS $key => $val)
			$$key = $val;

		if (file_exists ("{$this->plugin_base}/view/admin/$ug_name.php"))
			include ("{$this->plugin_base}/view/admin/$ug_name.php");
		else
			echo "<p>Rendering of admin template {$this->plugin_base}/view/admin/$ug_name.php failed</p>";
	}

	/**
	 * Renders a section of user display code.  The code is first checked for in the current theme display directory
	 * before defaulting to the plugin
	 *
	 * @param string $ug_name Name of the admin file (without extension)
	 * @param string $array Array of variable name=>value that is available to the display code (optional)
	 * @return void
	 */
	function render ($ug_name, $ug_vars = array (), $action = null) {
		foreach ($ug_vars AS $key => $val)
			$$key = $val;

		if (file_exists (TEMPLATEPATH."/view/{$this->plugin_name}/$ug_name.php"))
			include (TEMPLATEPATH."/view/{$this->plugin_name}/$ug_name.php");
		else if (file_exists ("{$this->plugin_base}/view/{$this->plugin_name}/$ug_name.php"))
			include ("{$this->plugin_base}/view/{$this->plugin_name}/$ug_name.php");
		else
			echo "<p>Rendering of template $ug_name.php failed</p>";
	}

	/**
	 * Renders a section of user display code.  The code is first checked for in the current theme display directory
	 * before defaulting to the plugin
	 *
	 * @param string $ug_name Name of the admin file (without extension)
	 * @param string $array Array of variable name=>value that is available to the display code (optional)
	 * @return void
	 */
	function capture($ug_name, $ug_vars = array ()) {
		ob_start ();
		$this->render ($ug_name, $ug_vars);
		$output = ob_get_contents ();
		ob_end_clean ();
		return $output;
	}


	/**
	 * Captures an admin section of display code
	 *
	 * @param string $ug_name Name of the admin file (without extension)
	 * @param string $array Array of variable name=>value that is available to the display code (optional)
	 * @return string Captured code
	 */
	function capture_admin($ug_name, $ug_vars = array ()) {
		ob_start ();
		$this->render_admin ($ug_name, $ug_vars);
		$output = ob_get_contents ();
		ob_end_clean ();
		return $output;
	}

	/**
	 * Get the plugin's base directory
	 *
	 * @return string Base directory
	 */
	function dir() {
	
		return $this->plugin_base;
	}


	/**
	 * Get a URL to the plugin.  Useful for specifying JS and CSS files
	 *
	 * For example, <img src="<?php echo $this->url () ?>/myimage.png"/>
	 *
	 * @return string URL
	 */
	function url() {
		$url = substr ($this->plugin_base, strlen (realpath (ABSPATH)));
		if (DIRECTORY_SEPARATOR != '/')
			$url = str_replace (DIRECTORY_SEPARATOR, '/', $url);
		$url = get_bloginfo ('wpurl')."/".$url;

		// Do an SSL check - only works on Apache
		if (isset ($_SERVER['HTTPS']))
			$url = str_replace ('http://', 'https://', $url);
		return $url;
	}

	/**
	 * getAction function.
	 * 
	 * @access public
	 * @return void
	 */
	function getAction(){
		// Extract URL
	 	$url = $this->getActionUrl();
		$sub = isset ($_GET['sub']) ? $_GET['sub'] : '';
		return $sub;
	}
	
	/**
	 * getActionUrl function.
	 * 
	 * @access public
	 * @return void
	 */
	function getActionUrl(){
		$url = explode ('&', $_SERVER['REQUEST_URI']);
	  	$url = $url[0];
	  	return $url;
	}

	/**
	 * Display a standard error message (using CSS ID 'message' and classes 'fade' and 'error)
	 * @param string $message Message to display
	 * @return void
	 * @access private
	 */
	function render_error ($message)
	{
	?>
		<div class="fade error" id="message">
		 <p><?php echo $message ?></p>
		</div>
	<?php
	}


	/**
	 * Display a standard notice (using CSS ID 'message' and class 'updated').
	 * Note that the notice can be made to automatically disappear, and can be removed
	 * by clicking on it.
	 *
	 * @param string $message Message to display
	 * @param int $timeout Number of seconds to automatically remove the message (optional)
	 * @return void
	 * @access private
	 */
	function render_message ($message, $timeout = 0)
	{
?>
		<div class="updated" id="message" onclick="Element.remove ('message')">
		 <p><?php echo $message ?></p>
		<?php if ($timeout > 0) : ?>
		<script type="text/javascript" charset="utf-8">
			new PeriodicalExecuter (function (pe)
			{
				pe.stop ();
				Effect.Fade ('message');
			}, <?php echo $timeout ?>);
		</script>
		<?php endif; ?>
		<script type="text/javascript" charset="utf-8">
			new Effect.Pulsate ('message', { pulses: 4, duration: 5, from: 0.1});
		</script>
		</div>

<?php
	}
}
?>