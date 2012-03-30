<?php
/**
 * Dashboard widget.
 * @author dligthart <info@daveligthart.com>
 * @version 0.3
 * @package wp-stats-dashboard
 */
global $wp_version;

$cache_path = wpsd_get_cache_path();

$un = get_option('wpsd_un');

$pw = get_option('wpsd_pw');

$user = wp_get_current_user(); // stats are only for administrators.
 
if( ($user->caps['administrator'] || wpsd_has_access())
	&& '' != $un 
	&& '' != $pw 
	&& file_exists($cache_path) 
	&& is_writable($cache_path)): 
?>

	<div id="wpsd_chart_container">
			<a href="http://www.adobe.com/go/getflashplayer">
				<img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" />
			</a>
	</div>
		
<?php else: ?>

<?php _e('check', 'wpsd');?> <a href="<?php echo wpsd_get_settings_url(); ?>" title="wp-stats-dashboard settings" target="_self"><?php _e('settings', 'wpsd'); ?></a>

<?php endif; ?>