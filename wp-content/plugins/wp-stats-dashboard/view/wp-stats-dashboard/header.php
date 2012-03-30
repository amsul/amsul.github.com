<!-- WP-Stats-Dashboard <?php echo $version; ?> by Dave Ligthart http://daveligthart.com -->
<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($) { 
	var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
	var data = { 
		action: 'wpsd_metrics', 
		_ajax_nonce: "<?php echo $nonce; ?>", 
		timeout: 3000
	};
	jQuery.post(ajaxurl, data, function(response) {	jQuery('#wpsd-metrics-data').html(response); });
});
/* ]]> */
</script>
<style type="text/css">
#wpsd-overview-table img {
	width:23px; 
	margin-right:5px; 
	vertical-align:middle;
}
</style>