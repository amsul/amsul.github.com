<?php
/**
 * Graph data.
 * 
 * @author dligthart
 * @version 0.3
 * @package wp-stats-dashboard
 */

include_once(realpath(dirname(__FILE__) . '/../../../../..') . '/wp-load.php'); // load wordpress context.

// Check user.
$user = wp_get_current_user();

// Only for admin.
if($user->caps['administrator'] || wpsd_has_access()) {
	
	//$chart_type = get_option('wpsd_type');

	// Get chart data.
	echo wpsd_get_chart_data();
}
?>