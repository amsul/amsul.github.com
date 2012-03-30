<?php
/*
Plugin Name: Theme Switch
Plugin URI: http://www.nkuttler.de/nkthemeswitch/
Author: Nicolas Kuttler
Author URI: http://www.nkuttler.de/
Description: Use a different wordpress theme for logged in user. Nice for theme development.
Version: 0.9.0.3
Text Domain: nkthemeswitch
*/

/**
 * Files
 * inc/admin.php		admin stuff
 * inc/widget.php		the widget
 * inc/themeswitch.php	always runs on frontend
 */

define( 'ONLY_ADMIN', 'Only admin' );
define( 'PREVIEW', 'Only with passkey' );
define( 'WORLD', 'Everybody' );

/**
 * Check who we are and do stuff
 *
 * @since 0.5.0
 */
function nkthemeswitch_load() {
	// http://codex.wordpress.org/Determining_Plugin_and_Content_Directories
	// Pre-2.6 compatibility
	if ( ! defined( 'WP_CONTENT_URL' ) )
	      define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
	if ( ! defined( 'WP_CONTENT_DIR' ) )
	      define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
	if ( ! defined( 'WP_PLUGIN_URL' ) )
	      define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
	if ( ! defined( 'WP_PLUGIN_DIR' ) )
	      define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );
	global $nkthemeswitch;
	$nkthemeswitch = array(
		'path' => WP_PLUGIN_DIR . '/' . str_replace( basename( __FILE__ ), "", plugin_basename( __FILE__ ) ),
		'url' => WP_PLUGIN_URL . '/' . str_replace( basename( __FILE__ ), "", plugin_basename( __FILE__ ) ),
	);

	// stuff we need to load always to make the passkey work
	// todo it doesn't seem to be possible to check if a user is logged in this early, load widget anyway
	require_once( 'inc/themeswitch.php' );
	require_once( 'inc/widget.php' );
	// todo can't this be done later?
	nkthemeswitch_widget_init();
	add_shortcode( 'nkthemeswitch', 'nkthemeswitch_shortcode' );

	if ( is_admin() ) {
		require_once( 'inc/admin.php' );

		add_action( 'init', 'nkthemeswitch_load_translation_file' );

		register_uninstall_hook( __FILE__, 'nkthemeswitch_uninstall' );

		add_action( 'admin_menu', 'nkthemeswitch_add_pages' );
	}
}
add_action( 'plugins_loaded', 'nkthemeswitch_load' );

/**
 * Install hook
 *
 * @since 0.7.0
 */
function nkthemeswitch_do_install() {
	require_once( 'inc/admin.php' );
	nkthemeswitch_install();
}
register_activation_hook( __FILE__, 'nkthemeswitch_do_install' );

?>
