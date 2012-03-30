<?php 
/**
 * Trends.
 * @author dligthart
 */
?>

<fieldset>
<legend style="font-size: 11px;"><?php _e('Select trend', 'wpsd'); ?></legend>
<?php 

if(isset($_REQUEST['wpsd_trends_type'])) {
	$wpsd_trends_type = $_REQUEST['wpsd_trends_type']; 
} else {
	$wpsd_trends_type = $form->getWpsdTrendsType();
}
$onchange = 'onchange="javascript:wpsd_reload_trend(this.value);"';
?>
<?php include('blocks/select-trend.php'); ?>
</fieldset>

<br/>
<div id="wpsd_trend">
	<?php /* include('admin_ajax_trend_visualize.php'); */ ?>
</div>