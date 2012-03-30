<?php

/**
 * Since 0.6.0:
 * contains code from Ryan Boren's Theme Switcher plugin. His website is at
 * http://boren.nu/
 *
 * His source says:
 *
 * Adapted from Alex King's style switcher.
 * http://www.alexking.org/software/wordpress/
 */

/**
 * Check if switch was requested + store cookie
 *
 * @since 0.6.0
 */
function nkthemeswitch_check_cookie() {
	if ( !empty( $_GET['theme'] ) ) {
		$option = get_option( 'nkthemeswitch' );
		$expire = time() + (int) $option['config']['cookie_timeout'];

		// refresh cookie on every switch
		setcookie( 'nkthemeswitch' . COOKIEHASH, stripslashes( $_GET['theme'] ), $expire, COOKIEPATH );

		// passkey has to be passed again
		if ( !empty( $_GET['passkey'] ) ) {
			setcookie( 'nkthemeswitch_passkey' . COOKIEHASH, $_GET['passkey'], $expire, COOKIEPATH );
		}

		// Force reload to see switched theme
		// Strip theme GET parameter (and everything behind it) to avoid loop
		 
		$redirect = $_SERVER['REQUEST_URI'];
		$redirect = preg_replace( '/.theme=.*/', '', $redirect );

		if ( function_exists( 'wp_redirect' ) ) {
			wp_redirect( $redirect );
		}
		else {
			header( 'Location: '. $redirect );
		}

		exit;
	}
}

/**
 * The core. Switch theme if cookie present + permission granted.
 *
 * @param string $default we get the standard theme's directory and return
 * whatever directory we want
 *
 * @return string the theme ( directory )
 *
 * @since 0.5.0
 *
 * Cookies can't be trusted
 */
function nkthemeswitch( $current, $pass = 'Template' ) {
	if ( isset( $_COOKIE['nkthemeswitch' . COOKIEHASH] ) ) {
		$theme = $_COOKIE['nkthemeswitch' . COOKIEHASH];
	}
	else return $current;

	$option = get_option( 'nkthemeswitch' );

	// only if cookie
	if ( !empty( $theme ) ) {

		// not in the admin area unless configured
		if ( is_admin() && $option['config']['admin_too'] == 'Enable' || !is_admin() ) {
	
			// Enabled at all?
			if ( $option['config']['active'] != 'Enable' ) return $current;
	
			// Enough permissions?
			if ( nkthemeswitch_check_permissions( $option ) ) {
				$theme = get_theme( $theme );
				
				// valid theme?
				if ( !empty( $theme ) ) {

					// Don't let people peek at unpublished themes.
					if ( isset( $theme['Status'] ) && $theme['Status'] != 'publish' ) return $current;		
	
					return $theme[$pass];
				}
			}
		}
	}
	return $current;
}
/**
 * Helpers
 *
 * @since 0.6.0
 */
function nkthemeswitch_template( $current ) {
	return nkthemeswitch( $current, 'Template' );
}

function nkthemeswitch_stylesheet( $current ) {
	return nkthemeswitch( $current, 'Stylesheet' );
}

/**
 * Check if some custom theme switch rules apply
 *
 * @param array $config plugin config
 *
 * @since 0.5.0
 */
function nkthemeswitch_check_permissions( $option ) {
	switch ( $option['config']['audience'] ) {
		case ONLY_ADMIN:
			if ( current_user_can( 'switch_themes' ) ) return true;
			break;
		case PREVIEW:
			if ( current_user_can( 'switch_themes' ) ) return true; // admin too
			if ( $_COOKIE['nkthemeswitch_passkey' . COOKIEHASH ] == $option['config']['passkey'] ) {
				return true;
			}
			break;
		case WORLD:
			return true;
			break;
	}
	return false;
}

/**
 * Draw a cloud of all themes. Based on Ryan's code.
 *
 * @param string $target a target
 * @param boolean $passkey passkey
 * @param boolean $screenshot show screenshot instead of name?
 * @param boolean $addname add the name to screenshots
 *
 * @since 0.6.0
 */
function nkthemeswitch_cloud( $target = '_blank', $passkey = false, $screenshot = false, $addname = false ) {
	$option = get_option('nkthemeswitch');
	if (
		defined( 'WP_ALLOW_MULTISITE' ) && WP_ALLOW_MULTISITE == true
		&& defined( 'NKTHEMESWITCH_LOAD_MS' ) && NKTHEMESWITCH_LOAD_MS == true
		&& version_compare( get_bloginfo( 'version' ), '3.0', '>=' )
	) {
		// I really don't want to read through those files just to determine if it's safe to include them...
		//$themephp	= ABSPATH . '/wp-admin/includes/theme.php';
		//$msphp		= ABSPATH . '/wp-admin/includes/ms.php';
		//if ( file_exists( $themephp ) && file_exists( $msphp ) ) {
		//	require_once( $themephp );
		//	require_once( $msphp );
		//}
		require_once( 'ms.php' );
		$themes = get_allowed_themes();
	}
	else
		$themes = get_themes();
	$theme_names = array_keys($themes);
	natcasesort($theme_names);
	$root = get_theme_root_uri();
	$r = '';

	if ( $option['config']['uselist'] == 'Enable' ) {
		$listopen		= '<ul>';
		$listclose		= '</ul>';
		$listitemopen	= '<li>'; 
		$listitemclose	= '</li>';
	}

	$r .= $listopen;

	foreach ($theme_names as $theme_name) {
		if ( isset( $themes[$theme_name]['Status'] ) && $themes[$theme_name]['Status'] != 'publish' )
			continue;
		$r .= $listitemopen;
		if ( $option['config']['exclude'] == 'Enable' ) {
			if ( $theme_name == 'WordPress Classic' || $theme_name == 'WordPress Default' || $theme_name == 'Twenty Ten' ) {
				continue;
			}
		}

		if ( is_admin() ) {
			$url = get_bloginfo( 'url' );
		}
		else {
			$url = get_permalink();
		}

		$schema = parse_url( $url ); // wp seems to re-define this. wtf?
		if ( isset ( $schema['query'] ) ) {
			$url .= '&amp;';
		}
		else {
			$url .= '?';
		}

		$url .= 'theme=' . urlencode( $theme_name );
		if ( $passkey ) {
			$url .= '&passkey=' . $option['config']['passkey'];
		} 

		$r .= '<a target="' . $target . '" class="nkthemeswitch" href="';
		$r .= $url;
		$r .= '">';

		$imagefound = false;
		if ( $screenshot && $themes[$theme_name]['Screenshot'] ) {
			$imagefound = true;
			$r .= '<img src="' . $root . '/' . $themes[$theme_name]['Stylesheet'] . '/' . $themes[$theme_name]['Screenshot'] .'" alt="" />';
		}
		else {
			$r .= htmlspecialchars( $theme_name );
		}
		if ( $screenshot && $addname && $imagefound ) {
			$r .= "<span>";
			$r .= htmlspecialchars( $theme_name );
			$r .= "</span>";
		}
		$r .= "</a>\n";
		$r .= $listitemclose;
	}

	$r .= $listclose;

	return $r;
} 

/**
 * Create a shortcode for link cloud. Defaults to show theme names.
 *
 * @since 0.6.0
 */
function nkthemeswitch_shortcode( $atts ) {
	extract( shortcode_atts( array(
		'mode' => 'text',
		'addname' => '',
	), $atts ) );

	if ( $mode == 'screenshot' )
		$screenshot = true;
	if ( $addname == 'yes' || $addname == 'true' )
		$addname = true;

	add_action( 'wp_footer', 'nkthemeswitch_homelink' );
	$before = "<div class=\"nkthemeswitch-cloud-$mode\" >";
	$after = '</div>';
	$option = get_option( 'nkthemeswitch' );
	$cloud = nkthemeswitch_cloud( '', false, $screenshot, $addname );
	return $before . $cloud . $after;
}

/**
 * Home link, only displayed if shortcode is used. Not configurable, coding
 * frenzy over...
 *
 * @since 0.6.0
 */
function nkthemeswitch_homelink() { ?>
	<?php printf( __( "<a href=\"%s\">WordPress Theme Switch Plugin</a>", 'nkthemeswitch' ), 'http://www.nkuttler.de/wordpress/theme-switch-and-preview-plugin/' ) ?> <?php
}

/**
 * Prints a <select> and the <option>s
 * Needed for admin and widget
 *
 * @param string $name form select name
 * @param string $choice the chosen one
 * @param array $choices possible choices
 */
function nkthemeswitch_form_select( $name, $selected, $choices ) { ?>
	<select name="<?php echo $name; ?>"><?php
		foreach ( $choices as $option ) {
			if ( $option == $selected ) {
				echo "<option value=\"$option\" selected>" . __( $option, 'nkthemeswitch' ) . "</option>\n";
			}
			else {
				echo "<option value=\"$option\" >" . __( $option, 'nkthemeswitch' ) . "</option>\n";
			}
		} ?>
	</select> <?php
}

if ( !is_admin() )
	nkthemeswitch_check_cookie();

add_filter( 'template', 'nkthemeswitch_template' );
add_filter( 'stylesheet', 'nkthemeswitch_stylesheet' );
