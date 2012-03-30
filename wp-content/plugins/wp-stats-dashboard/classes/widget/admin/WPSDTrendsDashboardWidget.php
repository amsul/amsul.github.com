<?php
/**
 * Trends Dashboard Widget.
 * @author dligthart <info@daveligthart.com>
 * @version 0.1
 * @package wp-stats-dashboard
 */
class WPSDTrendsDashboardWidget extends WPSDAdminWidget{

	/**
	 * Constructor.
	 * @access public
	 */
	function WPSDTrendsDashboardWidget() {
		
		$this->plugin_name = 'wp-stats-dashboard';
		
		$this->plugin_base = dirname(__FILE__) . '/../../..';
		
		add_action( 'wp_dashboard_setup', array(&$this, 'register_widget') );
		
		add_filter( 'wp_dashboard_widgets', array(&$this, 'add_widget') );
	}

	/**
	 * Register widget.
	 * @access protected
	 */
	function register_widget() {
		
		wp_register_sidebar_widget('wpsd_trends', 'Stats - Trends',
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

		if ( !isset($wp_registered_widgets['wpsd_trends']) ) return $widgets;

		array_splice( $widgets, 2, 0,'wpsd_trends');

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

		if($form->getWpsdWidgetTrends()) {
		
			$dao = new WPSDTrendsDao();
			
			$factory = new WPSDStatsFactory();
			
			$trends_type = $form->getWpsdTrendsType();
			
			if(null == $trends_type) $trends_type = $factory->pagerank;
			
			$rows = $dao->getStats($trends_type);
			
			$data = array();
			
			if(is_array($rows)) {
					
				foreach($rows as $row) {
					
					$data[$row->wpsd_trends_date] = $row->wpsd_trends_stats;
				}		
			}
			
			// Default is pagerank.
			$this->render_admin('admin_trend', array('set'=>$data, 'label'=>'days', 'form'=>$form));
		}
		else {

			_e('Widget disabled check', 'wpsd'); echo ' <a href="' . wpsd_get_settings_url() .'" title="wp-stats-dashboard settings" target="_self">'.__('settings', 'wpsd') . '</a>';
		}

		echo $after_widget;
	}
}
?>