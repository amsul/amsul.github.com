<div class="wrap nosubsub">

<?php if(function_exists('screen_icon')) {screen_icon('edit');} ?>

<h2><?php echo wp_specialchars( __('WPSD - RPC Client', 'wpsd')); ?></h2>

<p><?php _e('For testing purposes only.', 'wpsd');?></p>

<table width="100%"> 

<tr>
	<td> 
	
<table style="width: 97%; display:inline; float:left;">
	<!-- getKey -->
	<tbody style="background:#ccc;">
	<tr>
		<td valign="top" style="padding: 5px;"><label
			for="txt_rpc_get_key_username"><?php _e('getKey username', 'wpsd'); ?></label> <br />
		<input type="text" id="txt_rpc_get_key_username" name="txt_rpc_get_key_username" value=""/> <br/>
		<label
			for="txt_rpc_get_key_usernamed"><?php _e('getKey password', 'wpsd'); ?></label> <br />
		<input type="text" id="txt_rpc_get_key_password" name="txt_rpc_get_key_password" value=""/><br/>

		<input type="button" name="btn_rpc_get_key"
			id="btn_rpc_get_key" class="button" value="<?php _e('Get Key', 'wpsd'); ?>" />
		
		<br/>
		<label
			for="txt_rpc_get_stats_key"><?php _e('getStats key', 'wpsd'); ?></label> <br />
			
			<input type="text" id="txt_rpc_get_stats_key" name="txt_rpc_get_stats_key" value=""/> <br/>
		</td>
	</tr>	
	</tbody>
		
	<!-- getStatsByDate -->
	<tbody style="background:green; color:white;">
	<tr>
		<td valign="top" style="padding: 5px;">
		
		<label
			for="txt_rpc_get_stats_type"><?php _e('getStats type', 'wpsd'); ?></label> <br />
			
			<input type="text" id="txt_rpc_get_stats_type" name="txt_rpc_get_stats_type" value=""/> <br/>
		<label
			for="txt_rpc_get_stats_by_date_type"><?php _e('getStatsByDate type Y-m-d', 'wpsd'); ?></label> <br />
			
			<input type="text" id="txt_rpc_get_stats_by_date_type" name="txt_rpc_get_stats_by_date_type" value="<?php echo date('Y-m-d'); ?>"/> <br/>
			
			<input type="button" name="btn_rpc_get_stats_by_date"
			id="btn_rpc_get_stats_by_date" class="button" value="<?php _e('Get Stats By Date', 'wpsd'); ?>" />
			
						<input type="button" name="btn_rpc_get_stats"
			id="btn_rpc_get_stats" class="button" value="<?php _e('Get Stats', 'wpsd'); ?>" />
		</td>
	</tr>
	</tbody>
	
	<!-- getMetrics -->
	<tbody style="background:blue; color:white;">
	<tr>
		<td valign="top" style="padding: 5px;">
			<label><?php _e('getMetrics', 'wpsd'); ?></label> <br />
			<input type="button" name="btn_rpc_get_metrics"
			id="btn_rpc_get_metrics" class="button" value="<?php _e('Get Metrics', 'wpsd'); ?>" />
		</td>

	</tr>	
	</tbody>
	
	<!-- getStatsByYearAndMonth-->
	<tbody  style="background:yellow; color:#000;">
	<tr>
		<td valign="top" style="padding: 5px;">
		<label><?php _e('getStatsByYearAndMonth', 'wpsd'); ?></label> <br />

		<input type="button" name="btn_rpc_get_metrics"
			id="btn_rpc_get_stats_by_year_and_month" class="button" value="<?php _e('Get Stats By Year And Month', 'wpsd'); ?>" />
		</td>

	</tr>	
	</tbody>
	
	<!-- getStatsByDateRange -->
	<tbody style="background:red; color:white;"y>
	<tr>
		<td valign="top" style="padding: 5px;">
		
		<label><?php _e('getStatsByDateRange', 'wpsd'); ?></label> <br />
		
		<?php _e('Type', 'wpsd'); ?>
		<br/>
		<input type="text" id="txt_rpc_get_stats_by_date_range_type" name="txt_rpc_get_stats_by_date_range_type" value="1"/> <br/>
		
		<?php _e('Date From', 'wpsd'); ?>
		<br/>
		<input type="text" id="txt_rpc_get_stats_by_date_range_from" name="txt_rpc_get_stats_by_date_range_from" value=""/> <br/>
		
		<?php _e('Date To', 'wpsd'); ?>
		<br/>
		<input type="text" id="txt_rpc_get_stats_by_date_range_to" name="txt_rpc_get_stats_by_date_range_to" value="<?php echo date('Y-m-d') ;?>"/> <br/>
		
		<br/>
		<input type="button" name="btn_rpc_get_stats_by_date_range"
			id="btn_rpc_get_stats_by_date_range" class="button" value="<?php _e('Get Stats By Date Range', 'wpsd'); ?>" />
		</td>
	</tr>	
	</tbody>	
</table>

</td>
<td valign="top">

	Output<br/>
	
	<textarea cols="85" rows="30" id="output"></textarea>
	
	<br/>
	
	<input type="button" name="btn_rpc_clear_cache"
			id="btn_rpc_clear_cache" class="button" value="<?php _e('Clear Server Cache', 'wpsd'); ?>" />
</td>
</tr>
</table>

</div>
<script type="text/javascript" language="javascript">
/* <![CDATA[ */
function wpsd_rpc_get_key() {
	
	var data = {
		action:'wpsd_rpc_get_key',
		username:jQuery('#txt_rpc_get_key_username').val(),
		password:jQuery('#txt_rpc_get_key_password').val()
	};	
	
	jQuery.post(ajaxurl, data, function(response) {			
		
		jQuery('#output').html(response);	
		
		jQuery('#txt_rpc_get_stats_key').val(response);
	});
};

function wpsd_rpc_get_stats() {

	var data = {
		action:'wpsd_rpc_get_stats',
		key:jQuery('#txt_rpc_get_stats_key').val(),
		type:jQuery('#txt_rpc_get_stats_type').val()
	};	
	
	jQuery.post(ajaxurl, data, function(response) {			
		jQuery('#output').html(response);	
	});
};

function wpsd_rpc_get_stats_by_date() {

	var data = {
		action:'wpsd_rpc_get_stats_by_date',
		key:jQuery('#txt_rpc_get_stats_key').val(),
		type:jQuery('#txt_rpc_get_stats_type').val(),
		date:jQuery('#txt_rpc_get_stats_by_date_type').val()
	};	
	
	jQuery.post(ajaxurl, data, function(response) {		
		jQuery('#output').html(response);	
	});
};

function wpsd_rpc_get_metrics() {

	var data = {
		action:'wpsd_rpc_get_metrics',
		key:jQuery('#txt_rpc_get_stats_key').val()
	};	
	
	jQuery.post(ajaxurl, data, function(response) {
		jQuery('#output').html(response);	
	});
};

function wpsd_rpc_get_stats_by_date_range() {

	var data = {
		action:'wpsd_rpc_get_stats_by_date_range',
		key:jQuery('#txt_rpc_get_stats_key').val(),
		type:jQuery('#txt_rpc_get_stats_by_date_range_type').val(),
		from_date:jQuery('#txt_rpc_get_stats_by_date_range_from').val(),
		to_date:jQuery('#txt_rpc_get_stats_by_date_range_to').val()
	};	
	
	jQuery.post(ajaxurl, data, function(response) {		
		jQuery('#output').html(response);	
	});
};

function wpsd_rpc_clear_cache() {

	var data = {
		action:'wpsd_rpc_clear_cache',
		key:jQuery('#txt_rpc_get_stats_key').val()
	};	
	
	jQuery.post(ajaxurl, data, function(response) {		
		alert('Cache cleared');	
	});
};

function wpsd_rpc_get_stats_by_year_and_month() {

	var data = {
		action:'wpsd_rpc_get_stats_by_year_and_month',
		key:jQuery('#txt_rpc_get_stats_key').val()
	};	
	
	jQuery.post(ajaxurl, data, function(response) {		
		jQuery('#output').html(response);	
	});
};

function wpsd_rpc_get_version() {

	var data = {
		action:'wpsd_rpc_get_version'		
	};	
	
	jQuery.post(ajaxurl, data, function(response) {		
		jQuery('#output').html(response);	
	});
};


jQuery(document).ready(function(){
	
	jQuery('#btn_rpc_get_key').click(function() {
		wpsd_rpc_get_key();
	});
	
	jQuery('#btn_rpc_get_stats').click(function(){
		wpsd_rpc_get_stats();
	});	
	
	jQuery('#btn_rpc_get_stats_by_date').click(function(){
		wpsd_rpc_get_stats_by_date();
	});
	
	jQuery('#btn_rpc_get_metrics').click(function(){
		wpsd_rpc_get_metrics();
	});
	
	jQuery('#btn_rpc_get_stats_by_date_range').click(function(){
		wpsd_rpc_get_stats_by_date_range();
	});
	
	jQuery('#btn_rpc_get_stats_by_year_and_month').click(function(){
		wpsd_rpc_get_stats_by_year_and_month();
	});

	jQuery('#btn_rpc_clear_cache').click(function() {		
		wpsd_rpc_clear_cache();
	});
	
	wpsd_rpc_get_version();	
});
/* ]]> */
</script>

<style type="text/css">
 #output { 
 	background: #000; 
 	color:#00f72b; 
 	padding:15px; 
 	font-family: Menlo, monospace;
 	font-size: 0.8em;
 	border:2px solid #fff;
 }
</style>