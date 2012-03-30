<?php
/**
 * WPSDDashboardWidget.
 * @author dligthart <info@daveligthart.com>
 * @version 0.2
 * @package wp-stats-dashboard
 */
class WPSDDashboardWidget extends WPSDAdminWidget {

	/**
	 * Constructor.
	 */
	function WPSDDashboardWidget() {
		global $wp_version;

		$this->plugin_name = 'wp-stats-dashboard';
		$this->plugin_base = dirname(__FILE__) . '/../../..';

		if($wp_version >= 2.7) {
			add_action( 'wp_dashboard_setup', array(&$this, 'register_widget') );
			add_filter( 'wp_dashboard_widgets', array(&$this, 'add_widget') );
		}
	}
	/**
	 * Register widget.
	 * @access protected
	 */
	function register_widget() {
		global $wp_version;
		
		if($wp_version >= 2.7) {
			
			wp_add_dashboard_widget('wpstatsdashboard_widget', __('Stats - Views per day', 'wpsd'),
			array(&$this, 'widget'));

			// Globalize the metaboxes array, this holds all the widgets for wp-admin

			global $wp_meta_boxes;

			// Get the regular dashboard widgets array
			// (which has our new widget already but at the end)

			$b = $wp_meta_boxes['dashboard']['normal']['core'];

			// Backup and delete our new dashbaord widget from the end of the array

			$a = array('wpstatsdashboard_widget' => $b['wpstatsdashboard_widget']);

			unset($b['wpstatsdashboard_widget']);

			// Merge the two arrays together so our widget is at the beginning

			$sorted_dashboard = array_merge($a, $b);

			// Save the sorted array back into the original metaboxes

			$wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;

		} else {
			wp_register_sidebar_widget('wpstatsdashboard_widget', 'Stats - Views per day',
			array(&$this, 'widget'),
			array(
			'all_link' => 'options-general.php?page=wp-stats-dashboard',
			'feed_link' => 'options-general.php?page=wp-stats-dashboard',
			'edit_link' => 'options-general.php?page=wp-stats-dashboard' )
			);
		}
	}
	/**
	 * Add widget.
	 * @param array $widgets
	 * @access protected
	 */
	function add_widget( $widgets ) {
		global $wp_registered_widgets;
		if ( !isset($wp_registered_widgets['wpstatsdashboard_widget']) ) return $widgets;
		array_splice( $widgets, 2, 0, 'wpstatsdashboard_widget' );
		return $widgets;
	}
	/**
	 * Widget.
	 */
	function widget($args = array()) {
		if (is_array($args))
		extract( $args, EXTR_SKIP );

		echo $before_widget.$before_title.$widget_name.$after_title;

		$this->render_admin('admin_dashboard', array('plugin_name'=>'wp-stats-dashboard'));

		echo $after_widget;
	}
}
?>