<?php
/**
 * Trend graph data.
 *
 * @author dligthart <info@daveligthart.com>
 * @version 0.2
 * @package wp-stats-dashboard
 * @subpackage view
 */

include_once(realpath(dirname(__FILE__) . '/../../../../..') . '/wp-load.php'); // load wordpress context.

// Check user.
$user = wp_get_current_user();

// Only for admin.
if($user->caps['administrator'] || wpsd_has_access()) {

	$form = new WPSDAdminConfigForm();

	if($form->getWpsdWidgetTrends()) {

		$dao = new WPSDTrendsDao();

		$factory = new WPSDStatsFactory();

		$trends_type = $form->getWpsdTrendsType();

		if(null == $trends_type) $trends_type = $factory->pagerank;

		if(isset($_REQUEST['type'])) {

			$trends_type = $_REQUEST['type'];
		}

		$rows = $dao->getStats($trends_type);

		$set = array();

		if(is_array($rows)) {

			foreach($rows as $row) {

				$set[$row->wpsd_trends_date] = $row->wpsd_trends_stats;
			}
		}


		if (null != $set) {

			foreach ($set as $date => $value) {

				$dataSet[$date] = number_format($value, 0, '.', '');
			}
		}

		$dataSet = array_reverse($dataSet, true);

		$count = count($dataSet);
		$labels = '';
		$values = '';
		$tips = '';
		$i = 0;

		foreach ($dataSet as $date => $data) {
			if ($firstDate == "")
				$firstDate = strtotime($date);

			$dayDiff = round((strtotime($date)-$firstDate) / (60 * 60 * 24));

			if ($dayDiff > $maxDay)
				$maxDay = $dayDiff;

			$labels .= date("d", strtotime($date));

			if($i < $count -1) {
				$labels .= ',';
				$tips .= ',';
			}

			$i++;
		}

		$i = 0;
		foreach ($dataSet as $date => $data) {
			if ($firstDate == "")
				$firstDate = strtotime($date);

			$dayDiff = round((strtotime($date)-$firstDate) / (60 * 60 * 24));

			if ($dayDiff > $maxDay)
				$maxDay = $dayDiff;

			$values .= $data;

			if($i < $count -1) {
				$values .= ',';
			}

			$i++;
		}
		?>&x_label_style=8,#1a1a1a,2,1,#e6f0ff&
&x_axis_steps=1&
&y_ticks=5,10,5&
&x_labels=<?php echo $labels; ?>&
&y_min=0&
&y_max=<?php echo max($dataSet); ?>&
&bg_colour=#f9f9f9&
&x_axis_colour=#1a1a1a&
&x_grid_colour=#e6f0ff&
&y_axis_colour=#1a1a1a&
&y_grid_colour=#e6f0ff&
&inner_background=#ffffff,#e2e2e2,90&
&tool_tip=%23x_label%23%3Cbr%3E%23key%23%3A+%23val%23%3Cbr%3E%23tip%23&
&line_hollow=3,14568a,Trend,12,4&
&values=<?php echo $values; ?>&
&tool_tips_set=<?php echo $tips; ?>
<?php
	}
}
?>