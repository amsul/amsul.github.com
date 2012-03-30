<?php
/**
 * Head include.
 * @author dligthart <info@daveligthart.com>
 * @version 1.0
 * @package wp-stats-dashboard
 */

$config = new WPSDAdminConfigForm();

if( (stristr( $_SERVER['REQUEST_URI'] , '/wp-admin/') 
		&& !isset($_REQUEST['page']) 
		&& !$config->getWpsdDisableWidgets() 
		&& (!stristr( $_SERVER['REQUEST_URI'] , '.php') || stristr( $_SERVER['REQUEST_URI'] , 'index.php') )
	) 
	|| ('wpsd' == @$_REQUEST['page'] 
	|| 'wpsd_profile_finder' == @$_REQUEST['page'])) {
?>
<script type="text/javascript" language="javascript">
/* <![CDATA[ */

function wpsd_load_stats() {
	wpsd_loading();
	<?php if(defined('WPSD_PLUGIN_URL')): ?>
	jQuery("#wpsd-stats-ranking").load("<?php echo  WPSD_PLUGIN_URL . '/classes/ajax.php'; ?>",
		function (responseText, textStatus, XMLHttpRequest) {
			wpsd_loaded();
		}
	);
	<?php endif; ?>
};

function wpsd_load_stats_refresh() {
	wpsd_loading();
	<?php if(defined('WPSD_PLUGIN_URL')): ?>
	jQuery("#wpsd-stats-ranking").html('');
	jQuery("#wpsd-stats-ranking").load("<?php echo  WPSD_PLUGIN_URL . '/classes/ajax.php?wpsd_update_cache=true'; ?>",
		function (responseText, textStatus, XMLHttpRequest) {
			wpsd_loaded();
		}
	);
	<?php endif; ?>
};

jQuery(document).ready(function(){
	 
	wpsd_load_stats();
		
	jQuery('#btn_wpsd_reload_2').click(function() {	
		wpsd_load_stats_refresh();
	});
	
	jQuery('#btn_rpc_get_key').click(function() {
		wpsd_rpc_get_key();
	});
	
	jQuery('#btn_rpc_get_stats').click(function(){
		wpsd_rpc_get_stats();
	});
	
	wpsd_load_clicks();
	wpsd_load_postviews();
	wpsd_load_referers();
	wpsd_load_searchterms();
	wpsd_load_compete();
	wpsd_load_optimize();
	wpsd_load_authors();
});

var wpsd_flashvars = {
  data: "../wp-content/plugins/wp-stats-dashboard/view/admin/graph.php"
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

swfobject.embedSWF("<?php bloginfo('wpurl');?>/wp-content/plugins/wp-stats-dashboard/resources/swf/open-flash-chart.swf", 
	"wpsd_chart_container", 
	"100%", 
	"150", 
	"9.0.0",
	"expressInstall.swf", 
	wpsd_flashvars, 
	wpsd_params, 
	wpsd_attributes);

var wpsd_flashvars_t = {
  data: "../wp-content/plugins/wp-stats-dashboard/view/admin/graph_trend.php"
};

var wpsd_params_t = {
  menu: "true",
  scale: "default",
  allowfullscreen: "false",
  allowscriptaccess: "always",
  wmode: "transparent",
  salign: "TL",
  quality: "high",
  play: "true"
};
var wpsd_attributes_t = {
  id: "my_wpsd_trend_chart",
  name: "my_wpsd_trend_chart"
};

swfobject.embedSWF("<?php bloginfo('wpurl');?>/wp-content/plugins/wp-stats-dashboard/resources/swf/open-flash-chart.swf", 
	"wpsd_trend", 
	"100%", 
	"150", 
	"9.0.0",
	"expressInstall.swf", 
	wpsd_flashvars_t, 
	wpsd_params_t, 
	wpsd_attributes_t);

function wpsd_reload_trend(type) {
	tmp = findSWF("my_wpsd_trend_chart");
 	tmp.reload("../wp-content/plugins/wp-stats-dashboard/view/admin/graph_trend.php?type="+type);
}

function findSWF(movieName) {
  if (navigator.appName.indexOf("Microsoft")!= -1) {
    return window["ie_" + movieName];
  } else {
    return document[movieName];
  }
}

/* ]]> */
</script>
<?php } ?>