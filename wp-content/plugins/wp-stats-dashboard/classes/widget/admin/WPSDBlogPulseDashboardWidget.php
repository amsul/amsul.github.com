<?php
/**
 * WPSDBlogPulseDashboardWidget.
 *
 * Display graph from BlogPulse.com.
 *
 * @author dligthart <info@daveligthart.com>
 * @version 0.1
 * @package wp-stats-dashboard
 */
class WPSDBlogPulseDashboardWidget{

	/**
	 * Constructor.
	 * @access public
	 */
	function WPSDBlogPulseDashboardWidget() {
		add_action( 'wp_dashboard_setup', array(&$this, 'register_widget') );
		add_filter( 'wp_dashboard_widgets', array(&$this, 'add_widget') );
	}

	/**
	 * Register widget.
	 * @access protected
	 */
	function register_widget() {
		wp_register_sidebar_widget('wpsd_blogpulse', __('Stats - BlogPulse.com', 'wpsd'),
			array(&$this, 'widget'),
			array(
			'all_link' => '',
			'feed_link' => '',
			'edit_link' => 'options.php' )
		);
	}

	/**
	 * Add widget.
	 * @param array $widgets
	 * @access protected
	 */
	function add_widget( $widgets ) {
		global $wp_registered_widgets;

		if ( !isset($wp_registered_widgets['wpsd_blogpulse']) ) return $widgets;

		array_splice( $widgets, 2, 0,'wpsd_blogpulse');

		return $widgets;
	}

	/**
	 * Widget.
	 */
	function widget($args = array()) {
		if (is_array($args))
			extract( $args, EXTR_SKIP );
		
		$loading_img = '<img src="'. WPSD_PLUGIN_URL .'/resources/images/ajax-loader.gif" alt="'.__('loading', 'wpsd').'" width="24" height="24" />';
		
		echo $before_widget.$before_title.$widget_name.$after_title;

		echo '<div id="wpsd_blogpulse_inner">&nbsp;' . $loading_img .'</div>';
		
		echo $after_widget;
	}
}
?>