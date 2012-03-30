<!-- WP-Stats-Dashboard <?php echo $version; ?> by Dave Ligthart http://daveligthart.com -->
<!-- daily views -->
<script type="text/javascript">
/* <![CDATA[ */
var wpsd_flashvars = {
  data: "<?php bloginfo('wpurl'); ?>/wp-content/plugins/wp-stats-dashboard/view/wp-stats-dashboard/graph.php?<?php echo '_nonce='; echo $nonce; ?>"
};
var wpsd_params = {
  menu: "true",
  scale: "default",
  allowfullscreen: "false",
  allowscriptaccess: "never",
  wmode: "transparent",
  salign: "TL",
  quality: "high",
  play: "true"
};
var wpsd_attributes = {
  id: "my_wpsd_chart",
  name: "my_wpsd_chart"
};

swfobject.embedSWF("<?php bloginfo('wpurl');?>/wp-content/plugins/wp-stats-dashboard/resources/swf/open-flash-chart.swf", "wpsd-mini-graph", "100%", "150", "9.0.0","expressInstall.swf", wpsd_flashvars, wpsd_params, wpsd_attributes);
/* ]]> */
</script><!-- /WP-Stats-Dashboard -->