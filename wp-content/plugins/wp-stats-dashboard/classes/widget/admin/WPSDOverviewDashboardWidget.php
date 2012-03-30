<?php
/**
 * WPSDOverviewDashboardWidget. General Stats.
 * @author dligthart <info@daveligthart.com>
 * @version 0.3
 * @package wp-stats-dashboard
 */
class WPSDOverviewDashboardWidget{

	/**
	 * Constructor.
	 * @access public
	 */
	function WPSDOverviewDashboardWidget() {
		
		add_action( 'wp_dashboard_setup', array(&$this, 'register_widget') );
		
		add_filter( 'wp_dashboard_widgets', array(&$this, 'add_widget') );
	}

	/**
	 * Register widget.
	 * @access protected
	 */
	function register_widget() {
		
		wp_register_sidebar_widget('wpsd_overview', __('Stats - Social Media Metrics', 'wpsd'),
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

		if ( !isset($wp_registered_widgets['wpsd_overview']) ) return $widgets;

		array_splice( $widgets, 2, 0, 'wpsd_overview');

		return $widgets;
	}

	/**
	 * Widget.
	 */
	function widget($args = array()) {
		if (is_array($args))
			extract( $args, EXTR_SKIP );

		echo $before_widget.$before_title.$widget_name.$after_title;

		$form = new WPSDAdminConfigForm();

		if($form->getWpsdWidgetOverview()){
						
			$result =  wpsd_read_cache();

			echo '<!-- WP-Stats-Dashboard - START General Stats -->';
		
			if(defined('WPSD_PLUGIN_URL')){
				
				echo '<div align="center" id="wpsd-loading"> <img src="'. WPSD_PLUGIN_URL .'/resources/images/ajax-loader.gif" alt="' . __('loading', 'wpsd') . '" width="24" height="24" /></div>';
				
				echo '<div id="wpsd-stats-ranking"></div>';
				
			} else {

				_e('WPSD_PLUGIN_URL not defined', 'wpsd');
			}
										
			// Get domain.
			$domain_url = get_bloginfo('url');
			
			// Get shortened url.
			$s = get_option('wpsd_bitly_s');		
			if('' != $s) {
			
				$domain_url = $s;
			}
				
			echo '<br/><input type="button" value="' . __('Reload', 'wpsd') . '" class="button-primary" id="btn_wpsd_reload_2" />';
						
			echo '<!-- WP-Stats-Dashboard - STOP General Stats-->';
							
		} else {
			_e('Widget disabled check', 'wpsd'); echo ' <a href="' . wpsd_get_settings_url() .'" title="wp-stats-dashboard settings" target="_self">'.__('settings', 'wpsd') . '</a>';
		}
		echo $after_widget;
	}
}
?>