<?php
/**
 * Collection of core functions.
 *
 * @package    Wordspop
 * @subpackge  Wordspop_Framework
 * @copyright  Copyright (c) 2010-2011 Wordspop
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU GPL version 2
 * @since      1.0-beta1
 */

/**
 * Get the header template.
 *
 * This function shits the wp locate_template() which only look TEMPLATEPATH/STYLESHEETPATH
 * but not from get_theme_root()!
 *
 * @since 1.0-beta3
 */
function wpop_get_header( $name = null ) {
    do_action( 'get_header', $name );

    $reldir = wpop_relative_theme_dir();

    $templates = array();
    if ( isset( $name ) ) {
        $templates[] = "{$reldir}/header-{$name}.php";
    }

    $templates[] = "{$reldir}/header.php";

    // Load the template!
    locate_template( $templates, true );
} // e:wpop_get_header()

/**
 * Get the footer template.
 *
 * This function is effective for the mobile mode.
 *
 * @since 1.0-beta3
 */
function wpop_get_footer( $name = null ) {
    do_action( 'get_footer', $name );

    $reldir = wpop_relative_theme_dir();

    $templates = array();
    if ( isset( $name ) ) {
        $templates[] = "{$reldir}/footer-{$name}.php";
    }

    $templates[] = "{$reldir}/footer.php";
    
    locate_template( $templates, true );
} // e:wpop_get_footer()

/**
 * Get theme option
 *
 * @since 1.0-beta1
 */
function wpop_get_option( $name, $echo = false ) {
    if ( !class_exists( 'WPop_Theme' ) ) {
        return false;
    }

    if ( $echo ) {
        echo WPop_Theme::getOptions( $name );
    } else {
        return WPop_Theme::getOptions( $name );
    }
} // e:wpop_get_option()

/**
 * Day options list
 *
 * @since 1.0-beta1
 */
function wpop_days_options() {
    $options = array();
    for ( $i = 1; $i <= 31; $i++ ) {
      $options[$i] = $i;
    }
    return $options;
} // e:wpop_days_options()

/**
 * Month options list
 *
 * @since 1.0-beta1
 */
function wpop_months_options() {
    $options = array();
    for ( $i = 1; $i <= 12; $i++ ) {
        $options[$i] = strftime( '%B', mktime( 0, 0, 0, $i, 1, 1970 ) );
    }

    return $options;
} // e:wpop_months_options()

/**
 * Posts options list
 *
 * @since 1.0-beta1
 */
function wpop_posts_options( $type = 'post' ) {
    $the_query = new WP_Query( sprintf( 'post_type=%s&nopaging=1', $type ) );
    $options = array();
    while ( $the_query->have_posts() ) {
        $the_query->the_post();
        $options[ get_the_ID() ] = get_the_title();
    }
    return $options;
} // e:wpop_posts_options()

/**
 * Pages options list
 *
 * @since 1.0-beta1
 */
function wpop_pages_options() {
    return wpop_posts_options( 'page' );
} // e:wpop_pages_options()

/**
 * Categories options list
 *
 * @since 1.0-beta1
 */
function wpop_categories_options() {
    $cats = get_categories( 'hide_empty=0' );
    $options = array();
    foreach( $cats as $cat ) {
        $options[ $cat->cat_ID ] = $cat->name;
    }
    return $options;
} // e:wpop_categories_options()

/**
 * Tags options list
 *
 * @since 1.0-beta1
 */
function wpop_tags_options() {
    $tags = get_tags( 'hide_empty=0' );
    $options = array();
    foreach( $tags as $tag ) {
        $options[ $tag->term_id ] = $tag->name;
    }
    return $options;
} // e:wpop_tags_options()

/**
 * Taxonomies options list
 *
 * @since 1.0-beta6
 */
function wpop_taxonomies_options( $taxonomies ) {
    if ( !taxonomy_exists( $taxonomies ) ) {
        return;
    }

    $terms = get_terms( $taxonomies, 'hide_empty=0' );
    $options = array();
    foreach( $terms as $term ) {
        $options[ $term->term_id ] = $term->name;
    }
    return $options;
} // e:wpop_taxonomies_options()

/**
 * Slides options list
 *
 * @since 1.0-beta6
 */
function wpop_slides_options() {
    return wpop_taxonomies_options( 'slide' );
} // e:wpop_slides_options()

/**
 * Presentations options list
 *
 * @since 1.0-beta6
 */
function wpop_presentations_options() {
    return wpop_taxonomies_options( 'presentation' );
} // e:wpop_presentations_options()

/**
 * The Wordspop website link.
 *
 * @since 1.0-beta5
 */
function wpop_the_sitelink( $echo = true) {
    $link = sprintf(
        '<a id="wordspop" href="http://wordspop.com/" title="%s" rel="external"><img src="%s/images/wordspop.png" width="70" height="20" alt="Wordspop"></a>',
        __( 'Visit the Wordspop', WPOP_THEME_SLUG ),
        WPOP_ASSETS
    );
    
    if ( $echo ) {
        echo $link;
    } else {
        return $link;
    }
} // e:wpop_the_sitelink()

/**
 * Get the valid link to switch from mobile version to regular version.
 *
 * @since 1.0-beta6
 */
function wpop_mobile_to_regular_link() {
    
} // e:wpop_mobile_to_regular_link()

/**
 * Find out whether if the current page is login page or not.
 *
 * @since 1.0-beta5
 */
function is_login() {
    if ( in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ) ) ) {
        return true;
    }
    return false;
} // e:wpop_is_login()

/**
 * Output raw content.
 *
 * @since 1.0-beta6
 */
function wpop_the_raw( $content ) {
    return '$raw$' . $content . '$-raw$';
} // e:wpop_the_raw()

/**
 * Output oped content.
 *
 * @since 1.0-beta6
 */
function wpop_the_rawop( $content ) {
    return wpop_the_raw( '$op$' . $content . '$-op$' );
} // e:wpop_the_rawop()

/**
 * Get relative directory of theme.
 *
 * @since 1.0-beta6
 */
function wpop_relative_theme_dir() {
    return str_replace( TEMPLATEPATH, '', get_template_directory() );
} // e:wpop_relative_theme_dir()

/**
 * Fix the supplied templates according to theme dir.
 *
 * @param array Templates.
 *
 * @since 1.0-beta6
 */
function wpop_templates( $templates ) {
    settype( $templates, 'array' );
    $reldir = wpop_relative_theme_dir();
    foreach( $templates as $i => $template ) {
        $templates[$i] =  $reldir . '/' . str_replace( $reldir, '', $template );
    }

    return $templates;
} // e:wpop_templates()

/**
 * Same function with wp locate_template except the template has been corrected.
 *
 * @param string|array $template_names Template file(s) to search for, in order.
 * @param bool $load If true the template file will be loaded if it is found.
 * @param bool $require_once Whether to require_once or require. Default true. Has no effect if $load is false.
 * @return string The template filename if one is located.
 *
 * @since 1.0-beta6
 */
function wpop_locate_template( $template_names, $load = false, $require_once = true ) {
    return locate_template( wpop_templates( $template_names ), $load, $require_once );
} // e:wpop_locate_template()

/**
 * Get mobile agent.
 *
 * @return mixed Mobile agent or false if it was not on mobile mode.
 * @since 1.0-beta6
 */
function wpop_get_mobile() {
    // WPOP_MOBILE_AGENT only available if mobile device agent detected
    if ( !defined( 'WPOP_MOBILE_AGENT' ) ) {
        return false;
    }

    return WPOP_MOBILE_AGENT;
} // e:wpop_get_mobile()

/**
 * Get relative root directory of mobile template.
 *
 * @return string
 * @since 1.0-beta6
 */
function wpop_get_mobile_root() {
    return 'mobile' . DS . wpop_get_mobile();
} // e:wpop_get_mobile_root()

/**
 * Get the scheme currently use.
 *
 * @return string
 * @since 1.0-beta6
 */
function wpop_get_scheme() {
    return WPop_Theme::getOptions( 'wpop_scheme' );
} // e:wpop_get_scheme()

/**
 * Get scheme directory root.
 *
 * @return string
 * @since 1.0-beta6
 */
function wpop_get_scheme_root() {
    $scheme = wpop_get_scheme();
    if ( $scheme === false || $scheme === 'default' ) {
        $dir =  get_template_directory();
        return str_replace( '/' . wpop_get_mobile_root(), '', $dir );
    }
    
    return WPOP_THEME_SCHEMES . DS . $scheme;
} // e:wpop_get_scheme_root()

/**
 * Get scheme root uri.
 *
 * @return string
 * @since 1.0-beta6
 */
function wpop_get_scheme_root_uri() {
    $scheme = wpop_get_scheme();
    if ( $scheme === false || $scheme === 'default' ) {
        $uri = get_template_directory_uri();
        return str_replace( '/' . wpop_get_mobile_root(), '', $uri );
    }

    $uri = get_template_directory_uri() . '/schemes/' . $scheme;
    $uri = str_replace( '/' . wpop_get_mobile_root(), '', $uri );

    return $uri;
} // e:wpop_get_scheme_root_uri()

/**
 * Get scheme stylesheet directory.
 *
 * @return string
 * @since 1.0-beta6
 */
function wpop_get_scheme_stylesheet_directory() {
    $dir = wpop_get_scheme_root();
    if ( WPop::isMobile() ) {
        $dir .= DS . wpop_get_mobile_root();
    }
    return $dir;
} // e:wpop_get_scheme_stylesheet_directory()

/**
 * Get scheme stylesheet uri.
 *
 * @return string
 * @since 1.0-beta6
 */
function wpop_get_scheme_stylesheet_directory_uri() {
    $uri = wpop_get_scheme_root_uri();
    if ( WPop::isMobile() ) {
        $uri .= DS . wpop_get_mobile_root();
    }
    return $uri;
} // e:wpop_get_scheme_stylesheet_directory_uri()

/**
 * Get scheme stylesheet path.
 *
 * @return string
 * @since 1.0-beta6
 */
function wpop_get_scheme_stylesheet() {
    return wpop_get_scheme_stylesheet_directory() . '/style.css';
} // e:wpop_get_scheme_stylesheet()

/**
 * Get stylesheet uri.
 *
 * @return string
 * @since 1.0-beta6
 */
function wpop_get_scheme_stylesheet_uri() {
    return wpop_get_scheme_stylesheet_directory_uri() . '/style.css';
} // e:wpop_get_scheme_stylesheet_uri()


/**
 * Make excerpt.
 *
 * @return string
 * @since 1.0-beta6
 */
function wpop_excerpt( $text, $size = 20, $filter = '') {
    $blah = explode( ' ', strip_tags( $text ) );
    if ( count( $blah ) > $size ) {
        $k = $size;
        $use_dotdotdot = 1;
    } else {
        $k = count( $blah );
        $use_dotdotdot = 0;
    }
    $excerpt = '';
    for ( $i = 0; $i < $k; $i++ ) {
        $excerpt .= $blah[$i] . ' ';
    }
    $excerpt .= ( $use_dotdotdot ) ? '...' : '';

    if ( $filter ) {
      return apply_filters( $filter, $excerpt );
    }
    
    return $excerpt;

}