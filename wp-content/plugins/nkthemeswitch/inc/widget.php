<?php

/**
 * Register the widget
 */

function nkthemeswitch_widget_init() {
	register_sidebar_widget( __( 'Theme Switch' ), 'widget_nkthemeswitch' );
}

/**
 * The widget
 *
 * Since 0.6.0 this uses a lot of code of Ryan's plugin, see themeswitch.php
 */
function widget_nkthemeswitch( $args ) {
	$option = get_option( 'nkthemeswitch' );

	// Hide if necessary
	if ( $option['config']['active'] != 'Enable' ) return;
	if ( ( $option['config']['audience_widget'] == ONLY_ADMIN ) && !current_user_can('switch_themes') ) return;

	extract( $args );

	// todo does this do anything? check 2.8 widget api too..
	$ts = $before_widget;
	if ( $option['config']['widget_title'] != '' ) {
		$ts .= $before_title;
		$ts .= $option['config']['widget_title'];
		$ts .= $after_title;
	}

	// see themeswitch.php
	if (
		defined( 'WP_ALLOW_MULTISITE' ) && WP_ALLOW_MULTISITE == true
		&& defined( 'NKTHEMESWITCH_LOAD_MS' ) && NKTHEMESWITCH_LOAD_MS == true
		&& version_compare( get_bloginfo( 'version' ), '3.0', '>=' )
	) {
		require_once( 'ms.php' );
		$themes = get_allowed_themes();
	}
	else
		$themes = get_themes();

	$default_theme = get_current_theme();

	if ( count( $themes ) > 1 ) {
		$theme_names = array_keys( $themes );
		natcasesort( $theme_names );

		$link = $_SERVER['REQUEST_URI'];

		$schema = parse_url( $link ); // wp seems to re-define this. wtf?
		if ( isset ( $schema['query'] ) ) {
			$link .= '&amp;';
		}
		else {
			$link .= '?';
		}

		$ts .= '<ul id="nkthemeswitch">' . "\n";		
		$ts .= '<li>'."\n" . '	<select name="themeswitcher" onchange="location.href=\'' . $link . 'theme=\' + this.options[this.selectedIndex].value;">'."\n"	;

		foreach ( $theme_names as $theme_name ) {
			// Skip unpublished themes.
			if ( isset( $themes[$theme_name]['Status'] ) && $themes[$theme_name]['Status'] != 'publish' )
				continue;
				
			if ( $option['config']['exclude'] == 'Enable' ) {
				if ( $theme_name == 'WordPress Classic' || $theme_name == 'WordPress Default' || $theme_name == 'Twenty Ten' ) {
					continue;
				}
			}
			if ( ( !empty( $_COOKIE['nkthemeswitch' . COOKIEHASH] ) && $_COOKIE['nkthemeswitch' . COOKIEHASH] == $theme_name )
					|| ( empty( $_COOKIE['nkthemeswitch' . COOKIEHASH] ) && ( $theme_name == $default_theme ) ) ) {
				$ts .= '		<option value="'.$theme_name.'" selected="selected">' . substr( htmlspecialchars($theme_name), 0, 40 ) . '</option>'."\n" ;
			}	else {
				$ts .= '		<option value="'.$theme_name.'">' . substr( htmlspecialchars($theme_name), 0, 40 ) . '</option>'."\n" ;
			}				
		}
		$ts .= '	</select>'."\n" . '</li>'."\n" ;
		$ts .= '</ul>';
		$ts .= '<noscript><input type="submit" value="' . __( 'Theme Switch', 'nkthemeswitch' ) . '" /></noscript>';
	}
	$ts .= $after_widget;
	echo $ts;
}

?>
