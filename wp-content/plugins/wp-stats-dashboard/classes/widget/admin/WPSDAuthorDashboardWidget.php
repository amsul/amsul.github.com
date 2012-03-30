<?php
/**
 * WPSDAuthorDashboardWidget.
 *
 * @author dligthart <info@daveligthart.com>
 * @version 0.1
 * @package wp-stats-dashboard
 */
class WPSDAuthorDashboardWidget{

	/**
	 * WPSDAuthorDashboardWidget function.
	 * 
	 * @access public
	 * @return void
	 */
	function WPSDAuthorDashboardWidget() {
		
		add_action( 'wp_dashboard_setup', array(&$this, 'register_widget') );
		
		add_filter( 'wp_dashboard_widgets', array(&$this, 'add_widget') );
	}

	/**
	 * register_widget function.
	 * 
	 * @access public
	 * @return void
	 */
	function register_widget() {
		
		wp_register_sidebar_widget('wpsd_author', __('Stats - Top 5 Authors', 'wpsd') ,
			array(&$this, 'widget'),
			array(
			//'all_link' => '',
			//'feed_link' => '',
			'edit_link' => 'options.php' )
		);
	}

	/**
	 * add_widget function.
	 * 
	 * @access public
	 * @param mixed $widgets
	 * @return void
	 */
	function add_widget( $widgets ) {
		global $wp_registered_widgets;

		if ( !isset($wp_registered_widgets['wpsd_author']) ) return $widgets;

		array_splice( $widgets, 2, 0, 'wpsd_author');

		return $widgets;
	}

	/**
	 * widget function.
	 * 
	 * @access public
	 * @param array $args. (default: array())
	 * @return void
	 */
	function widget($args = array()) {
		if (is_array($args))
			extract( $args, EXTR_SKIP );
		
		$loading_img = '<img src="'. WPSD_PLUGIN_URL .'/resources/images/ajax-loader.gif" alt="'.__('loading', 'wpsd').'" width="24" height="24" />';
		
		echo $before_widget.$before_title.$widget_name.$after_title;

		echo '<div id="wpsd_author_inner">&nbsp;' . $loading_img . '</div>';

		echo $after_widget;
	}
}
?>