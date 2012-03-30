<?php
/**
 * Graph data.
 * 
 * @author dligthart
 * @version 0.2
 * @package wp-stats-dashboard
 */

include_once(realpath(dirname(__FILE__) . '/../../../../..') . '/wp-load.php'); // load wordpress context.

if(isset($_REQUEST['_nonce'])) { 
	
	// Get chart data.
	
	if ( is_active_widget(false, false, 'wpsdminigraphwidget', true) ) { // if users allows widget, the data is readable.
		
		echo wpsd_get_chart_data();
	}
}
?>