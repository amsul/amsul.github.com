<?php
	$icon = wpsd_sanitize($icon);
	$url = wpsd_sanitize($url);
	$name = wpsd_sanitize($name);
	$type = wpsd_sanitize($type);
	$code = wpsd_sanitize($code);
?>
<style type="text/css">
.wpsd-profile-type {
	margin:5px;
	width:97%;
}
.wpsd-profile-icon {
	margin:5px;
}
.wpsd-profile-code {
	margin:5px;
	font-weight:bold;
}
</style>

<div class="wpsd-profile-type">

	<span class="wpsd-profile-icon">
		<?php echo '<img src="'. WPSD_PLUGIN_URL . '/resources/images/icons/'.$icon.'.png" alt="'.$icon.' icon" />'; ?>
	</span>
	
	<?php $cb_checked = ' checked="checked"'; $cb_id = 'wpsd-profile-checkbox-' . $type; ?>
	
	<input type="checkbox" id="<?php echo $cb_id; ?>" name="<?php echo $cb_id; ?>"<?php if($cb_value) echo $cb_checked; ?>/>

	<a href="<?php echo $url; ?>" target="_blank" title="<?php echo $name; echo ' profile'; ?>"><span class="wpsd-profile-name"><?php echo $name; ?></span></a>
	
	<input type="text" name="wpsd-profile-input-<?php echo $type; ?>" id="wpsd-profile-input-<?php echo $type; ?>" size="32" value="<?php if('200' == $code){ echo $username; } ?>" />
	
	<span class="wpsd-profile-code"><?php echo $code; ?></span>
	
</div>