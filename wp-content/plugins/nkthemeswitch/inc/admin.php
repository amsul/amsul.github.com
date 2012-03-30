<?php

/**
 * add admin page
 */
function nkthemeswitch_add_pages() {
	$page = add_options_page( __( 'Theme Switch', 'nkthemeswitch' ), __( 'Theme Switch', 'nkthemeswitch' ), 10, 'nkthemeswitch', 'nkthemeswitch_options_page' );
	add_action( 'admin_head-' . $page, 'nkthemeswitch_css_admin' );

	// Add icon
	add_filter( 'ozh_adminmenu_icon_nkthemeswitch', 'nkthemeswitch_icon' );
}

/**
 * Return admin menu icon
 *
 * @return string path to icon
 *
 * @since 0.6.0.2
 */
function nkthemeswitch_icon() {
	global $nkthemeswitch;
	return $nkthemeswitch['url'] . 'pic/arrow_switch.png';
}

/**
 * Load admin CSS style
 *
 * @since 0.5.0
 *
 * @todo check if this is correct
 */
function nkthemeswitch_css_admin() {
	global $nkthemeswitch; ?>
	<link rel="stylesheet" href="<?php echo $nkthemeswitch['url'] . 'css/admin.css?v=0.2.2' ?>" type="text/css" media="all" /> <?php
}

/**
 * Plugin activation hook
 *
 * Migrate config if necessary
 */
function nkthemeswitch_install() {
	if ( !get_option( 'nkthemeswitch' ) && !get_option( 'nkthemeswitch_level' ) ) {
		$option = nkthemeswitch_reset();
		update_option( 'nkthemeswitch', $option );
		return;
	}
	if ( get_option( 'nkthemeswitch_level' ) ) {
		$option = nkthemeswitch_migrate_0_5_0();
		update_option( 'nkthemeswitch', $option );
	}

	$option = get_option( 'nkthemeswitch' );
	if ( $option['version'] == '0.5.0') {
		$option = nkthemeswitch_migrate_0_6_0();
		update_option( 'nkthemeswitch', $option );
	}
}

/**
 * Clean up when uninstalled, delete settings from db
 */
function nkthemeswitch_uninstall() {
	delete_option( 'nkthemeswitch' );
}

/**
 * Migrate from old option schema
 *
 * @return array the migrated settings
 *
 * @since 0.5.0
 */
function nkthemeswitch_migrate_0_5_0() {
	$option	= nkthemeswitch_reset();

	$option['config']['theme']			= get_option( 'nkthemeswitch_theme' );
	$option['config']['preview']		= get_option( 'nkthemeswitch_preview' );
	$option['config']['passkey']		= get_option( 'nkthemeswitch_passkey' );

	foreach ( array( 'theme', 'preview', 'passkey', 'level', 'widgetlevel' ) as $opt ) {
		delete_option( "nkthemeswitch_$opt" );
	}

	return $option;
}

/**
 * Migrate from old option schema
 *
 * @return array the migrated settings
 *
 * @since 0.6.0
 */
function nkthemeswitch_migrate_0_6_0() {
	$old_option = get_option( 'nkthemeswitch' );
	$new_option	= nkthemeswitch_reset();

	// Don't stop preview links from working, keep passkey
	$new_option['config']['passkey'] = $old_option['config']['passkey'];
	if ( $old_option['config']['preview'] == 'Enable' ) {
		$new_option['config']['audience'] == PREVIEW;
	}

	// It wasn't possible to deactivate switching perviously, so enable it
	$new_option['config']['active'] == true;

	// It wasn't possible to deactivate admin switching perviously, so enable it
	$new_option['config']['admin_too'] == true;

	return $new_option;
}

/**
 * Reset settings
 *
 * @return array the default settings
 *
 * @since 0.5.0
 */
function nkthemeswitch_reset() {
	$default = array(
		'version' => '0.7.2',
		'config' => array(
			'active'			=> 'Disable', // do anything? 
			'exclude'			=> 'Disable', // exclude default + classic
			'preview'			=> 'Disable', // allow preview links?
			'uselist'			=> 'Disable', // use list format
			'passkey'			=> nkthemeswitch_passkey(),
			'admin_too'			=> 'Disable',
			'audience'			=> ONLY_ADMIN,
			'audience_widget'	=> ONLY_ADMIN,
			'audiences'			=> array(
				ONLY_ADMIN,
				PREVIEW,
				WORLD,
				// I'm not sure this can work at all. Are we maybe too late in
				// the WordPress process when we know if on page etc.
				//'Only on pages',
				//'Anywhere but on pages',
				//'Only on posts',
				//'Anywhere but on posts',
				// Doesn't work on my server, no time to look into it. See
				// themeswitch.php
				// Should be easy to configure
				//'Internet Explorer <= 8',
				//'Internet Explorer <= 7',
				//'Internet Explorer <= 6',
			),
			'audiences_widget'	=> array(
				ONLY_ADMIN,
				WORLD,
			),
			'cookie_timeout'	=> 300,
			'title'				=> '',
		),
	);
	return $default;
}

/**
 * Prints an <input>
 *
 * @param string $name form select name
 * @param string $value the value
 */
function nkthemeswitch_form_input( $name, $value ) { ?>
	<input type="text" name="<?php echo $name ?>" value="<?php echo $value ?>" size="10" /> <?php
}

/**
 * Generate a random pass key
 *
 * @return integer random value
 */
function nkthemeswitch_passkey() {
	return uniqid( mt_rand() );
}

function nkthemeswitch_options_page() { ?>
	<div id="nkuttler" class="wrap" > <?php
		$option = get_option( 'nkthemeswitch' );
		#echo '<pre>'; var_dump( $option ); echo '</pre>';
		if ( isset( $_POST['config'] ) ) {
			#function_exists( 'check_admin_referer ') ? check_admin_referer( 'nkthemeswitch' ) : null;
			$nonce = $_REQUEST['_wpnonce'];
			if ( !wp_verify_nonce( $nonce, 'nkthemeswitch-config' ) ) die( 'Security check' ); 

			$config = $_POST['config'];
			$option['config'] = array_merge( ( array ) $option['config'], $config );
			update_option( 'nkthemeswitch', $option );
		}
		elseif ( isset( $_POST['pass'] ) && $_POST['pass'] == 'reset' ) {
			#function_exists( 'check_admin_referer ') ? check_admin_referer( 'nkthemeswitch' ) : null;
			$nonce = $_REQUEST['_wpnonce'];
			if ( !wp_verify_nonce( $nonce, 'nkthemeswitch-reset' ) ) die( 'Security check' ); 
			$option = nkthemeswitch_reset();
			update_option( 'nkthemeswitch', $option );
		}
		?>
	
		<h2><?php _e( 'Theme Switch', 'nkthemeswitch' ) ?></h2> <?php 
		require_once( 'nkuttler.php' );
		nkuttler0_2_2_links( 'nkthemeswitch' ) ?>
		<p>
			<?php _e( 'This plugin has three basic configurations:', 'nkthemeswitch' ) ?>
		</p>
		<ol>
			<li><strong><?php _e( 'Only admin', 'nkthemeswitch' ); ?></strong>: <?php _e( 'This is useful for theme developers and to preview themes on live sites.', 'nkthemeswitch' ) ?></li>
			<li><strong><?php _e( "Only with passkey", 'nkthemeswitch' ); ?></strong>: <?php _e( 'Send your clients preview links. I recommend to change the passkey from time to time to stop old links from working. Once the passkey has been accepted by the plugin the client could guess other working template names in theory.', 'nkthemeswitch' ) ?></li>
			<li><strong><?php _e( 'Everybody', 'nkthemeswitch' ); ?></strong>: <?php _e( 'Theme switching for everybody. You want to add the widget and to modify the widget permissions accordingly.', 'nkthemeswitch' ) ?></li>
		</ol>

		<p>
			<?php _e( 'The <strong>cookie expire</strong> determines how long the theme switch stays in effect once it was requested. The default is five minutes, you probably want to change that.' , 'nkthemeswitch' ) ?>
		</p>

		<p>
			<?php _e( 'You can also add a cloud of theme switching links to any post or page by using the <tt>[nkthemeswitch]</tt> shortcode. Use <tt>[nkthemeswitch mode=screenshot]</tt> to display the screenshots instead of names. The shortcode adds a link to the plugin homepage to your footer.' , 'nkthemeswitch' ) ?>
		</p>
			
		<h2><?php _e( 'Configuration', 'nkthemeswitch') ?></h2>
		<form action="" method="post">
			<?php function_exists( 'wp_nonce_field' ) ? wp_nonce_field( 'nkthemeswitch-config' ) : null; ?>
			<table class="form-table" id="clearnone" >
				<tr>
					<th>
						<label>
							<?php _e( 'Enable theme switching', 'nkthemeswitch' )  ?>
						</label>
					</th>
					<td>
						<?php nkthemeswitch_form_select( 'config[active]', $option['config']['active'], array( 'Disable', 'Enable' ) ); ?>
					</td>
				</tr> <?php
	
				if ( $option['config']['active'] == 'Enable' ) { ?>
					<tr>
						<th>
							<label>
								<?php _e( 'Enable theme switching in the admin area (for theme options)', 'nkthemeswitch' )  ?>
							</label>
						</th>
						<td>
							<?php nkthemeswitch_form_select( 'config[admin_too]', $option['config']['admin_too'], array('Enable', 'Disable') ); ?>
						</td>
					</tr>
	
					<tr>
						<th>
							<label>
								<?php _e( 'Who can switch themes', 'nkthemeswitch' )  ?>
							</label>
						</th>
						<td>
							<?php nkthemeswitch_form_select( 'config[audience]', $option['config']['audience'], $option['config']['audiences'] ) ?>
						</td>
					</tr>
		
					<tr>
						<th>
							<label>
								<?php _e( 'Who can see the widget', 'nkthemeswitch' )  ?>
							</label>
						</th>
						<td>
							<?php nkthemeswitch_form_select( 'config[audience_widget]', $option['config']['audience_widget'], $option['config']['audiences_widget'] ) ?>
						</td>
					</tr>

					<tr>
						<th>
							<label>
								<?php _e( 'Widget title', 'nkthemeswitch' ) ?>
							</label>
						</th>
						<td>
							<?php nkthemeswitch_form_input ( 'config[widget_title]', $option['config']['widget_title'] ) ?>
						</td>
					</tr>
		
					<tr>
						<th>
							<label>
								<?php _e( 'Use unordered list for the theme cloud? The default is a flat display', 'nkthemeswitch' ) ?>
							</label>
						</th>
						<td>
							<?php nkthemeswitch_form_select( 'config[uselist]', $option['config']['uselist'], array( 'Disable', 'Enable' ) ) ?>
						</td>
					</tr>
		
		
					<tr>
						<th>
							<label>
								<?php _e( 'Passkey for theme preview links', 'nkthemeswitch' ) ?>
							</label>
						</th>
						<td>
							<?php nkthemeswitch_form_select( 'config[passkey]', $option['config']['passkey'], array( $option['config']['passkey'], nkthemeswitch_passkey() ) ) ?>
						</td>
					</tr>
		
					<tr>
						<th>
							<label>
								<?php _e( 'Cookie expire (seconds)', 'nkthemeswitch' ) ?>
							</label>
						</th>
						<td>
							<?php nkthemeswitch_form_input ( 'config[cookie_timeout]', $option['config']['cookie_timeout'] ) ?>
						</td>
					</tr>

					<tr>
						<th>
							<label>
								<?php _e( 'Exclude default themes', 'nkthemeswitch' ) ?>
							</label>
						</th>
						<td>
							<?php nkthemeswitch_form_select( 'config[exclude]', $option['config']['exclude'], array( 'Disable', 'Enable' ) ) ?>
						</td>
					</tr> <?php
				} ?>
			</table>

			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'nkthemeswitch' ) ?>">
			</p>

		</form> <?php

		if ( $option['config']['active'] == 'Enable' ) { ?>
			<p><?php
				$theme = get_current_theme();
				printf ( __( "The default theme is <strong>%s</strong>.", 'nkthemeswitch' ), $theme ) ?>
			</p> <?php
		}

		if ( $option['config']['audience'] == PREVIEW && $option['config']['active'] == 'Enable' ) { ?>
			<h3><?php _e( 'Preview links for your customers', 'nkthemeswitch' ) ?></h3>
			<div class="nkthemeswitch-admin"> <?php
				echo nkthemeswitch_cloud( $option, '', true ); ?>
			</div> <?php
		}
			
		if ( $option['config']['active'] == 'Enable' ) { ?>
			<h3><?php _e( 'Open theme in new window', 'nkthemeswitch' ) ?></h3>
			<div class="nkthemeswitch-admin"> <?php
				echo nkthemeswitch_cloud( $option, '_blank', false ); ?>
			</div> <?php
		} ?>

		<h2><?php _e( 'Reset', 'nkthemeswitch' ) ?></h2>
		<form action="" method="post">
			<?php function_exists( 'wp_nonce_field' ) ? wp_nonce_field( 'nkthemeswitch-reset' ) : null; ?>
			<input type="hidden" name="pass" value="reset" />
			<p class="submit">
				<input type="submit" class="button-secondary" value="<?php _e( 'Reset Settings', 'nkthemeswitch' ) ?>">
			</p>
		</form>
	</div> <?php
}

/**
 * Load Translations
 */
function nkthemeswitch_load_translation_file() {
	$plugin_path = plugin_basename( dirname( __FILE__ ) . '/../translations' );
	load_plugin_textdomain( 'nkthemeswitch', '', $plugin_path );
}



?>
