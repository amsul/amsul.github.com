<?php 
		printf('
		
		<style type="text/css">
		.metric_label {  } 
		.metric_green { float:right; display:block; background-color:green;  padding-left:10px; padding-right:10px; color:#fff; font-weight:bold; } 
		.metric_red {  float:right; display:block; background-color:red;  padding-left:10px; padding-right:10px; color:#fff; font-weight:bold; }
		.wpsd_toggle_twitter_search_contents, .wpsd_toggle_contents { display:none; padding:0px 10px 0px 35px; }
		.wpsd_toggle_twitter_search { }
		.wpsd_toggle {

			background:url("%1$s") no-repeat top left;
			line-height:32px;
			display:block;
			cursor:pointer;
			vertical-align:bottom;
		}
		.wpsd_toggle span { margin-left:35px; }
		</style>
		
		<script type="text/javascript">
			var ts = false;
			jQuery(".wpsd_toggle").click(
				function() {
					jQuery(this).find(".wpsd_toggle_contents").hide();
					if(ts == false){
						jQuery(this).find(".wpsd_toggle_contents").show();
						jQuery(this).css("background-image", "url(%2$s)");
						ts = true;
					} else {
						jQuery(this).find(".wpsd_toggle_contents").hide();						
						jQuery(this).css("background-image", "url(%1$s)");
						ts = false;
					}
				}
			);
		</script>
		
		', 
		WPSD_PLUGIN_URL . '/resources/images/toggle-plus.png',
		WPSD_PLUGIN_URL . '/resources/images/toggle-minus.png');
?>