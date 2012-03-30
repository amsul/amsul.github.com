<?php
/**
 * Copy all the stuff from wp-admin we need.
 * WordPress is so beautifully consistent.
 * get_themes() is in wp-includes/
 * get_allowed_themes() is in wp-admin/
 */

/**
 * Get the allowed themes for the current blog.
 *
 * @since 3.0.0
 *
 * @uses get_themes()
 * @uses current_theme_info()
 * @uses get_site_allowed_themes()
 * @uses wpmu_get_blog_allowedthemes
 *
 * @return array $themes Array of allowed themes.
 */
if ( !function_exists( 'get_allowed_themes' ) ) {
	function get_allowed_themes() {
		if ( !is_multisite() )
			return get_themes();

		$themes = get_themes();
		$ct = current_theme_info();
		$allowed_themes = apply_filters("allowed_themes", get_site_allowed_themes() );
		if ( $allowed_themes == false )
			$allowed_themes = array();

		$blog_allowed_themes = wpmu_get_blog_allowedthemes();
		if ( is_array( $blog_allowed_themes ) )
			$allowed_themes = array_merge( $allowed_themes, $blog_allowed_themes );

		if ( isset( $allowed_themes[ esc_html( $ct->stylesheet ) ] ) == false )
			$allowed_themes[ esc_html( $ct->stylesheet ) ] = true;

		reset( $themes );
		foreach ( $themes as $key => $theme ) {
			if ( isset( $allowed_themes[ esc_html( $theme[ 'Stylesheet' ] ) ] ) == false )
				unset( $themes[ $key ] );
		}
		reset( $themes );

		return $themes;
	}
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @return unknown
 */
if ( !function_exists( 'current_theme_info' ) ) {
	function current_theme_info() {
		$themes = get_themes();
		$current_theme = get_current_theme();
		if ( ! isset( $themes[$current_theme] ) ) {
			delete_option( 'current_theme' );
			$current_theme = get_current_theme();
		}
		$ct->name = $current_theme;
		$ct->title = $themes[$current_theme]['Title'];
		$ct->version = $themes[$current_theme]['Version'];
		$ct->parent_theme = $themes[$current_theme]['Parent Theme'];
		$ct->template_dir = $themes[$current_theme]['Template Dir'];
		$ct->stylesheet_dir = $themes[$current_theme]['Stylesheet Dir'];
		$ct->template = $themes[$current_theme]['Template'];
		$ct->stylesheet = $themes[$current_theme]['Stylesheet'];
		$ct->screenshot = $themes[$current_theme]['Screenshot'];
		$ct->description = $themes[$current_theme]['Description'];
		$ct->author = $themes[$current_theme]['Author'];
		$ct->tags = $themes[$current_theme]['Tags'];
		$ct->theme_root = $themes[$current_theme]['Theme Root'];
		$ct->theme_root_uri = $themes[$current_theme]['Theme Root URI'];
		return $ct;
	}
}

/**
 * Another undocumented core function we need
 */
if ( !function_exists( 'get_site_allowed_themes' ) ) {
	function get_site_allowed_themes() {
		$themes = get_themes();
		$allowed_themes = get_site_option( 'allowedthemes' );
		if ( !is_array( $allowed_themes ) || empty( $allowed_themes ) ) {
			$allowed_themes = get_site_option( 'allowed_themes' ); // convert old allowed_themes format
			if ( !is_array( $allowed_themes ) ) {
				$allowed_themes = array();
			} else {
				foreach( (array) $themes as $key => $theme ) {
					$theme_key = esc_html( $theme['Stylesheet'] );
					if ( isset( $allowed_themes[ $key ] ) == true ) {
						$allowedthemes[ $theme_key ] = 1;
					}
				}
				$allowed_themes = $allowedthemes;
			}
		}
		return $allowed_themes;
	}
}

/**
 * Another undocumented core function we need
 */
if ( !function_exists( 'wpmu_get_blog_allowedthemes' ) ) {
	function wpmu_get_blog_allowedthemes( $blog_id = 0 ) {
		$themes = get_themes();

		if ( $blog_id != 0 )
			switch_to_blog( $blog_id );

		$blog_allowed_themes = get_option( 'allowedthemes' );
		if ( !is_array( $blog_allowed_themes ) || empty( $blog_allowed_themes ) ) { // convert old allowed_themes to new allowedthemes
			$blog_allowed_themes = get_option( 'allowed_themes' );

			if ( is_array( $blog_allowed_themes ) ) {
				foreach( (array) $themes as $key => $theme ) {
					$theme_key = esc_html( $theme['Stylesheet'] );
					if ( isset( $blog_allowed_themes[$key] ) == true ) {
						$blog_allowedthemes[$theme_key] = 1;
					}
				}
				$blog_allowed_themes = $blog_allowedthemes;
				add_option( 'allowedthemes', $blog_allowed_themes );
				delete_option( 'allowed_themes' );
			}
		}

		if ( $blog_id != 0 )
			restore_current_blog();

		return $blog_allowed_themes;
	}
}
