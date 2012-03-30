<?php
/**
 * Wordspop Framework
 *
 * @package    Wordspop
 * @subpackge  Wordspop_Framework
 * @copyright  Copyright (c) 2010-2011 Wordspop
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU GPL version 2
 * @since      1.0-beta1
 */

/**
 * @see WPop_Config
 */
require_once 'wpop_config.php';

/**
 * @see WPop_Theme
 */
require_once 'wpop_theme.php';

/**
 * @see WPop_Migration
 */
require_once 'wpop_migration.php';

/**
 * @see WPop_Widget
 */
require_once 'wpop_widget.php';

/**
 * @see WPop_Mobile
 */
require_once 'wpop_mobile.php';

/**
 * @see WPop_Utils
 */
require_once 'wpop_utils.php';

/**
 * @see WPop_API
 */
require_once 'wpop_api.php';

/**
 * @see WPop_Slider
 */
require_once 'wpop_selector.php';

/**
 * @see WPop_Shortcode
 */
require_once 'wpop_shortcode.php';

/**
 * @see WPop_UI
 */
require_once 'wpop_ui.php';

/**
 * @see WPop_Slider
 */
require_once 'wpop_slider.php';

/**
 * Core class.
 *
 * @package    Wordspop
 * @subpackge  Wordspop_Framework
 * @copyright  Copyright (c) 2010-2011 Wordspop
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU GPL version 2
 * @author     Firman Wandayandi <firmanw@wordspop.com>
 * @since      1.0-beta1
 */
class WPop
{
    /**
     * Whether is mobile mode or regular.
     *
     * @var bool
     * @since 1.0-beta6
     */
    private static $_isMobile = false;

    /**
     * Initialization
     *
     * @since 1.0-beta1
     */
    public static function init()
    {
        // Read the theme metadata first
        self::_readMeta();

        // Update theme info
        self::_updateThemeInfo();

        // Detect the mobile mode
        self::_detectMobile();

        // Create the theme instance
        $theme = WPop_Theme::instance();
        $theme->init();

        if ( is_admin() ) {
            self::_checkMigration( $theme );
            self::_checkUpdates( $theme );
        }

        // Create the menu
        add_action( 'admin_menu', array( 'WPop', 'createMenu') );

        // Create a admin bar menu (WordPress 3.1 or later)
        if ( version_compare( get_bloginfo( 'version' ), '3.1', '>=' ) ) {
            // Put it after the updates menu (80)
            add_action( 'admin_bar_menu', array( 'WPop', 'createAdminBarMenu'), 90 );
        }

        // Calls the setup routine
        add_action( 'init', array( 'WPop', 'setup' ) );

        // Initialize the shortcode handler
        WPop_Shortcode::instance();
    }

    /**
     * Read the theme meta and define some theme constant.
     *
     * @since 1.0-beta5
     */
    private static function _readMeta()
    {
        $headers = array(
            'name'        => 'Theme Name',
            'version'     => 'Version'
        );

        $meta = get_file_data( TEMPLATEPATH . '/style.css', $headers, 'theme' );

        if ( !defined( 'WPOP_THEME_NAME' ) ) {
            define( 'WPOP_THEME_NAME', $meta['name'] );
        }

        if ( !defined( 'WPOP_THEME_SLUG' ) ) {
            define( 'WPOP_THEME_SLUG', WPop_Utils::slugify( WPOP_THEME_NAME, '-' ) );
        }
        
        if ( !defined( 'WPOP_THEME_ID' ) ) {
            define( 'WPOP_THEME_ID', WPop_Utils::slugify( WPOP_THEME_NAME ) );
        }

        if ( !defined( 'WPOP_THEME_VERSION' ) ) {
            define( 'WPOP_THEME_VERSION', $meta[ 'version' ] );
        }
    }

    /**
     * Detect the mobile device mode.
     *
     * @since 1.0-beta5
     */
    private static function _detectMobile()
    {
        if ( is_admin() ) {

            if ( isset( $_GET['page'] ) && $_GET['page'] == WPOP_THEME_SLUG . '-mobile' ) {
                self::$_isMobile = true;
            } else if ( !empty( $_POST ) && isset( $_POST['data'] ) ) { // Probably this is an ajax request
                parse_str( $_POST['data'], $ajax_data );
                if ( isset( $ajax_data['_wp_http_referer'] ) ) {
                    $url = parse_url( $ajax_data['_wp_http_referer'] );
                    if ( isset( $url['query'] ) && preg_match( '/page=[a-z0-9_-]+mobile/i', $url['query'] ) ) {
                        self::$_isMobile = true;
                    }
                }
            }

        } else {

            if ( isset( $_GET[ 'full' ] ) ) {
                $scheme = 'http';
                if ( isset( $_SERVER[ 'HTTPS' ] ) && $_SERVER[ 'HTTPS' ] == 'on' ) {
                    $scheme = 'https';
                }

                $url = parse_url( $_SERVER[ 'HTTP_HOST' ] . $_SERVER[ 'REQUEST_URI' ] );

                setcookie( 'full', 1, 0, '/' );
                header( 'Location: ' . $scheme . '://' .  $url[ 'path' ] );
                exit;
            }

            $mobile = WPop_Mobile::instance(); // Detect whether user is mobile or not
            if ( $mobile->isMobile() ) {
                self::$_isMobile = true;
                self::_mobileInit();
                
                // Disable admin bar
                if ( function_exists( 'show_admin_bar' ) ) {
                    show_admin_bar( false );
                }

                if ( !is_admin() && is_dir( WPOP_THEME_MOBILE ) ) {
                    if ( !isset( $_COOKIE[ 'full' ] ) && !isset( $_GET['full'] ) ) {
                        //add_action('template_redirect', array('WPop', 'loadMobileTemplate'));
                    }
                }
            }

        }
    }

    /**
     * Find the whether is mobile or not.
     *
     * @return bool
     * @since 1.0-beta6
     */
    public static function isMobile()
    {
        return self::$_isMobile;
    }

    /**
     * Initialization on mobile mode.
     *
     * @since 1.0-beta6
     */
    public function _mobileInit()
    {
        $mobile = WPop_Mobile::instance();
        $agents = $mobile->agents();
        if ( !in_array( 'mobile', $agents ) ) {
            $agents[] = 'default';
        }

        $template_dir = get_template_directory();

        // Creating possible template according to user agent
        $templates = array();
        foreach ( $agents as $agent ) {
            $templates[$agent] = $template_dir . DS . 'mobile' . DS . $agent . DS . 'index.php';
        }

        foreach ( $templates as $agent => $file ) {
            if ( file_exists( $file)  ) {
                define( 'WPOP_MOBILE_AGENT', $agent );
                return;
            }
        }
    }

    /**
     * Check the whether theme requires the migration or not
     *
     * @since 1.0-beta1
     */
    private static function _checkMigration()
    {
        $info = self::getOption('theme');
        if ( $info['id'] == WPOP_THEME_ID && version_compare( $info['version'], WPOP_THEME_VERSION, 'lt' ) == -1 ) {
            $classfile = WPOP_THEME_CLASSES . DS . 'migration.php';
            if ( file_exists( $classfile ) ) {
                include_once $classfile;
                if ( class_exists( 'Migration' ) ) {
                    $theme = WPop_Theme::instance();
                    $querystring = 'page=' . WPOP_THEME_SLUG;
                    $theme->notification(
                        __( 'The previous settings of theme detected, would you like to migrate the settings to this install?', WPOP_THEME_SLUG ) . ' ' .
                        sprintf('<a href="?%1$s&migrate=1">%2$s</a> / <a href="?%1$s&migrate=0">%3$s</a>', $querystring, __( 'Yes', WPOP_THEME_SLUG ), __( 'No', WPOP_THEME_SLUG ) )
                    );
                }
            }
        }
    }

    /**
     * Check the theme update
     *
     * @since 1.0-beta3
     */
    private static function _checkUpdates()
    {
        $theme = WPop_Theme::instance();
        
        // Check for updates
        $api = WPop_API::instance();
        add_filter( 'pre_set_site_transient_update_themes', array( $api, 'getThemeUpdates' ) );
        add_filter( 'pre_set_transient_update_themes', array( $api, 'getThemeUpdates' ) );
        add_filter( 'site_transient_update_themes', array( $api, 'getThemeUpdates' ) );
        add_filter( 'transient_update_themes', array( $api, 'getThemeUpdates' ) );

        // Show notification if updates available
        $wpop_updates = get_site_transient( 'wpop_updates' );
        if ( is_object( $wpop_updates ) && isset( $wpop_updates->response ) ) {
            $slug = basename( TEMPLATEPATH );
            if ( isset( $wpop_updates->response[$slug] ) ) {
                $info = $wpop_updates->response[$slug];
                if ( version_compare( WPOP_THEME_VERSION, $info['new_version'], 'lt' ) ) {
                    $theme_link = sprintf( '<a href="http://wordspop.com/themes/%s">%s</a> %s', WPOP_THEME_SLUG, WPOP_THEME_NAME, $info['new_version'] );
                    if ( $info['license'] == 'Free' ) {
                        $message = $theme_link .' ' . __( 'is available!', WPOP_THEME_SLUG ) . ' ' .
                                   sprintf( '<a href="%supdate-core.php">' . __( 'Please update now' ) . '</a>.<br />', get_admin_url() );
                    } else {
                        $message = $theme_link .' ' . __( 'is available! Please follow the instruction how to update.', WPOP_THEME_SLUG );
                    }
                    $theme->notification( $message );
                }
            }
        }
    }

    /**
     * Update or add theme info.
     *
     * @param bool $force Force update?
     *
     * @since 1.0-beta6
     */
    private static function _updateThemeInfo( $force = false)
    {
        $info = self::getOption('theme');
        if ( $info === false || $force ) {
            $info = array(
                'theme'   => WPOP_THEME_NAME,
                'slug'    => WPOP_THEME_SLUG,
                'id'      => WPOP_THEME_ID,
                'version' => WPOP_THEME_VERSION
            );
            self::saveOption('theme', $info);
        }
    }
    
    /**
     * Get option
     *
     * @param   string  $name                 Option name
     * @param   mixed   $default  (optional)  Default value
     * @return  mixed
     * @since   1.0-beta1
     */
    public static function getOption( $name, $default = false )
    {
        return get_option( sprintf( 'wpop_%s', WPop_Utils::namify( $name, '_' ) ), $default );
    }

    /**
     * Save the option
     *
     * @access  public
     * @param   mixed   $name   Option name
     * @param   mixed   $value  Value
     * @since   1.0-beta1
     */
    public static function saveOption( $name, $value )
    {
        return update_option( sprintf( 'wpop_%s', WPop_Utils::namify( $name, '_' ) ), stripslashes_deep( $value ) );
    }

    /**
     * Setup routine
     * @since   1.0-beta1
     */
    public static function setup()
    {
        self::_registerPostTypes();

        // Plugins
        wp_register_script( 'jquery-dump', WPOP_ASSETS . '/js/plugins/jquery.dump.js', array( 'jquery' ), WPOP_VERSION );
        wp_register_script( 'jquery-hoverintent', WPOP_ASSETS . '/js/plugins/jquery.hoverIntent.min.js', array( 'jquery' ), WPOP_VERSION );
        wp_register_script( 'jquery-easing', WPOP_ASSETS . '/js/plugins/jquery.easing.pack.js', array( 'jquery' ), WPOP_VERSION );
        wp_register_script( 'jquery-cycle', WPOP_ASSETS . '/js/plugins/jquery.cycle.all.min.js', array( 'jquery' ), WPOP_VERSION );
        wp_register_script( 'jquery-cutetime', WPOP_ASSETS . '/js/plugins/jquery.cuteTime.min.js', array( 'jquery' ), WPOP_VERSION );
        wp_register_script( 'jquery-colorpicker', WPOP_ASSETS . '/js/plugins/colorpicker.min.js', array( 'jquery' ), WPOP_VERSION );
        
        // All-in-one plugins pack
        wp_register_script( 'jquery-plugins', WPOP_ASSETS . '/js/jquery.plugins.min.js', array( 'jquery' ), WPOP_VERSION );
        
        // Wordspop core
        wp_register_script( 'wpop-core', WPOP_ASSETS . '/wpop.js', array( 'jquery', 'jquery-plugins', 'jquery-colorpicker' ), WPOP_VERSION );
        wp_register_script( 'wpop-core-min', WPOP_ASSETS . '/wpop.min.js', array( 'jquery', 'jquery-plugins', 'jquery-colorpicker' ), WPOP_VERSION );
        
        // Extras
        wp_register_script( 'wpop-extras', WPOP_ASSETS . '/js/extras.js', false, WPOP_VERSION );
        wp_register_script( 'wpop-extras-min', WPOP_ASSETS . '/js/extras.min.js', false, WPOP_VERSION );
        wp_register_script( 'google-maps', 'http://maps.googleapis.com/maps/api/js?sensor=false' );
        
        // Shortcodes
        wp_register_script( 'wpop-shortcodes',  WPOP_ASSETS . '/js/shortcodes.js', array( 'jquery', 'google-maps' ), WPOP_VERSION );
        wp_register_script( 'wpop-shortcodes-min',  WPOP_ASSETS . '/js/shortcodes.min.js', array( 'jquery', 'google-maps' ), WPOP_VERSION );

        if ( defined( 'WP_DEBUG' ) && WP_DEBUG && !is_login() ) {
            wp_enqueue_script( 'jquery-dump' );
        }

        if ( is_admin() ) {
            wp_enqueue_style( 'thickbox' ); // Thickbox
            wp_enqueue_style( 'widgets' ); // Widgets
            wp_enqueue_style( 'wpop', WPOP_ASSETS . '/wpop.css' ); // Wordspop admin

            wp_enqueue_script( 'media-upload' ); // Media uploader
            wp_enqueue_script( 'thickbox' ); // Thickbox
            wp_enqueue_script( 'jquery-ui-draggable' ); // jQuery Draggable
            wp_enqueue_script( 'jquery-ui-droppable' ); // jQuery Droppable
            wp_enqueue_script( 'jquery-ui-sortable' ); // jQuery Droppable
            wp_enqueue_script( 'jquery-colorpicker' ); // jQuery Colorpicker
            wp_enqueue_script( 'jquery-plugins' ); // All-in-one jQuery plugins pack

            // Wordspop
            if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                wp_enqueue_script( 'wpop-core' );
            } else {
                wp_enqueue_script( 'wpop-core-min' );
            }
        } else {
            if ( defined( 'WP_DEBUG' ) && WP_DEBUG && !is_login() ) {
                wp_enqueue_script( 'wpop-extras' );
                wp_enqueue_script( 'wpop-shortcodes' );
            } else {
                wp_enqueue_script( 'wpop-extras-min' );
                wp_enqueue_script( 'wpop-shortcodes-min' );
            }
        }
    }

    /**
     * Register the bundled custom post types.
     *
     * @since   1.0-beta5
     */
    private function _registerPostTypes()
    {
        // Register the internal use post type
        register_post_type( 'wpop_internal', array(
            'labels'            => array(
                'name' => 'Wordspop Internal'
            ),
            'public'            => true,
            'show_ui'           => false,
            'rewrite'           => false,
            'query_var'         => false,
            'show_in_nav_menus' => false
        ));
        
        $config = WPop_Config::instance();

        // Slide support
        if ( current_theme_supports( 'slide' ) ) {
            // Slide item post type
            $args = array(
                'labels'              => array(
                    'name'                => _x( 'Slides', 'post type general name', WPOP_THEME_SLUG ),
                    'singular_name'       => _x( 'Slide', 'post type singular name', WPOP_THEME_SLUG ),
                    'add_new'             => _x( 'Add New', 'slide', WPOP_THEME_SLUG ),
                    'all_items'           => __( 'All Slides', WPOP_THEME_SLUG ),
                    'add_new_item'        => __( 'Add New Slide', WPOP_THEME_SLUG ),
                    'edit_item'           => __( 'Edit Slide', WPOP_THEME_SLUG ),
                    'new_item'            => __( 'New Slide', WPOP_THEME_SLUG ),
                    'view_item'           => __( 'View Slide', WPOP_THEME_SLUG ),
                    'search_items'        => __( 'Search Slides', WPOP_THEME_SLUG ),
                    'not_found'           => __( 'No Slides found.', WPOP_THEME_SLUG ),
                    'not_found_in_trash'  => __( 'No Slides found in Trash.', WPOP_THEME_SLUG ), 
                    'parent_item_colon'   => '',
                    'menu_name'           => __( 'Slides', WPOP_THEME_SLUG )
                ),
                'public'              => false,
                'publicly_queryable'  => true,
                'exclude_from_search' => true,
                'show_ui'             => true, 
                'query_var'           => true,
                'rewrite'             => array( 'slug' => 'slide' ),
                'menu_icon'           => WPOP_ASSETS . '/images/menu-icon-slides.png',
                'menu_position'       => null,
                'supports'            => array( 'title', 'editor', 'thumbnail', 'page-attributes' )
            ); 
            register_post_type( 'slide', $args);
            
            // Slide taxonomy
            $labels = array(
                'name'              => _x( 'Presentations', 'taxonomy general name', WPOP_THEME_SLUG ),
                'singular_name'     => _x( 'Presentation', 'taxonomy singular name', WPOP_THEME_SLUG ),
                'search_items'      => __( 'Search Presentations', WPOP_THEME_SLUG ),
                'all_items'         => __( 'All Presentations', WPOP_THEME_SLUG ),
                'parent_item'       => __( 'Parent Presentation', WPOP_THEME_SLUG ),
                'parent_item_colon' => __( 'Parent Presentation:', WPOP_THEME_SLUG ),
                'edit_item'         => __( 'Edit Presentation', WPOP_THEME_SLUG ), 
                'update_item'       => __( 'Update Presentation', WPOP_THEME_SLUG ),
                'add_new_item'      => __( 'Add New Presentation', WPOP_THEME_SLUG ),
                'new_item_name'     => __( 'New Presentation', WPOP_THEME_SLUG ),
                'menu_name'         => __( 'Presentations', WPOP_THEME_SLUG ),
            );

            register_taxonomy( 'presentation', 'slide', array(
                'hierarchical'  => true,
                'labels'        => $labels,
                'show_ui'       => true,
                'query_var'     => true,
                'public'        => true,
                'rewrite'       => array( 'slug' => 'presentation' ),
            ));
        }

        // Portfolio support
        if ( current_theme_supports( 'portfolio' ) ) {
            // Portfolio item post type
            $args = array(
                'labels'              => array(
                    'name'                => _x( 'Portfolio Items', 'post type general name', WPOP_THEME_SLUG ),
                    'singular_name'       => _x( 'Portfolio Item', 'post type singular name', WPOP_THEME_SLUG ),
                    'add_new'             => _x( 'Add New', 'slide', WPOP_THEME_SLUG ),
                    'all_items'           => __( 'All Porfolio Items', WPOP_THEME_SLUG ),
                    'add_new_item'        => __( 'Add New Portfolio Item', WPOP_THEME_SLUG ),
                    'edit_item'           => __( 'Edit Portfolio Item', WPOP_THEME_SLUG ),
                    'new_item'            => __( 'New Portfolio Item', WPOP_THEME_SLUG ),
                    'view_item'           => __( 'View Portfolio Item', WPOP_THEME_SLUG ),
                    'search_items'        => __( 'Search Portfolio Items', WPOP_THEME_SLUG ),
                    'not_found'           => __( 'No Portfolio Items found.', WPOP_THEME_SLUG ),
                    'not_found_in_trash'  => __( 'No Portfolio Items found in Trash.', WPOP_THEME_SLUG ), 
                    'parent_item_colon'   => '',
                    'menu_name'           => __( 'Portfolio', WPOP_THEME_SLUG )
                ),
                'public'              => true,
                'publicly_queryable'  => true,
                'exclude_from_search' => true,
                'show_ui'             => true, 
                'query_var'           => true,
                'rewrite'             => true,
                'menu_icon'           => WPOP_ASSETS . '/images/menu-icon-portfolio.png',
                'menu_position'       => null,
                'supports'            => array( 'title', 'editor', 'thumbnail', 'comments', 'excerpt', 'custom-fields' )
            ); 
            register_post_type( 'portfolio', $args);

            // Portfolio type taxonomy
            $labels = array(
                'name'              => _x( 'Porfolio Categories', 'taxonomy general name', WPOP_THEME_SLUG ),
                'singular_name'     => _x( 'Portfolio Category', 'taxonomy singular name', WPOP_THEME_SLUG ),
                'search_items'      => __( 'Search Portfolio Categories', WPOP_THEME_SLUG ),
                'all_items'         => __( 'All Portfolio Categories', WPOP_THEME_SLUG ),
                'parent_item'       => __( 'Parent Category', WPOP_THEME_SLUG ),
                'parent_item_colon' => __( 'Parent Category:', WPOP_THEME_SLUG ),
                'edit_item'         => __( 'Edit Portfolio Category', WPOP_THEME_SLUG ), 
                'update_item'       => __( 'Update Portfolio Category', WPOP_THEME_SLUG ),
                'add_new_item'      => __( 'Add New Portfolio Category', WPOP_THEME_SLUG ),
                'new_item_name'     => __( 'New Portfolio Category', WPOP_THEME_SLUG ),
                'menu_name'         => __( 'Categories', WPOP_THEME_SLUG ),
            );

            register_taxonomy( 'portfolio_category', 'portfolio', array(
                'hierarchical'  => true,
                'labels'        => $labels,
                'show_ui'       => true,
                'query_var'     => true,
                'public'        => true,
                'rewrite'       => array( 'slug' => 'portfolio-category' ),
            ));
        }

        if ( current_theme_supports( 'feedback' ) ) {
            $args = array(
                'labels'              => array(
                    'name'                => _x( 'Feedback', 'post type general name', WPOP_THEME_SLUG ),
                    'singular_name'       => _x( 'Feedback Item', 'post type singular name', WPOP_THEME_SLUG ),
                    'add_new'             => _x( 'Add New', 'slide', WPOP_THEME_SLUG ),
                    'all_items'           => __( 'All Feedback', WPOP_THEME_SLUG ),
                    'add_new_item'        => __( 'Add New Feedback Item', WPOP_THEME_SLUG ),
                    'edit_item'           => __( 'Edit Feedback Item', WPOP_THEME_SLUG ),
                    'new_item'            => __( 'New Feedback Item', WPOP_THEME_SLUG ),
                    'view_item'           => __( 'View Feedback Item', WPOP_THEME_SLUG ),
                    'search_items'        => __( 'Search Feedback Items', WPOP_THEME_SLUG ),
                    'not_found'           => __( 'No items found.', WPOP_THEME_SLUG ),
                    'not_found_in_trash'  => __( 'No items found in Trash.', WPOP_THEME_SLUG ), 
                    'parent_item_colon'   => ''
                ),
                'public'              => false,
                'publicly_queryable'  => true,
                'exclude_from_search' => true,
                'show_ui'             => true, 
                'query_var'           => true,
                'rewrite'             => true,
                'menu_icon'           => WPOP_ASSETS . '/images/menu-icon-feedback.png',
                'menu_position'       => null,
                'supports'            => array( 'title', 'editor', 'thumbnail' )
            ); 
            register_post_type( 'feedback', $args);
        }
    }

    /**
     * Get internal post.
     *
     * Internal post is a silent post to make able to fire the media uploader and have the attachments anywhere.
     *
     * @return  integer
     * @since   1.0-beta1
     */
    public static function getInternalPost( $token = 'internal' )
    {
        global $wpdb;

        $token = preg_replace( '/^wpop-/', '', WPop_Utils::namify( $token, '-', false ) );

        $params = array(
            'post_type'       => 'wpop_internal',
            'post_name'       => 'wpop-' . $token,
        );

        $query = "SELECT ID FROM {$wpdb->posts} WHERE post_parent = 0";
        foreach ( $params as $column => $value ) {
            $query .= " AND {$column} = '{$value}'";
        }
        $query .= ' LIMIT 1';

        $post = $wpdb->get_row( $query) ;
        if ( count( $post ) ) {
            return $post->ID;
        }

        $params['post_status'] = 'draft';
        $params['comment_status'] = 'closed';
        $params['ping_status'] = 'closed';
        $params['post_title'] = 'Wordspop - ' . ucwords( trim( preg_replace( '/[-_\[\]]+/', ' ', $token ) ) );
        return wp_insert_post( $params );
    }

    /**
     * Call a callback
     *
     * Safely call a callback which is automatically load the script if needed and call function if exists.
     *
     * @param   string  $callback A string of function name or a callback
     * @return  mixed
     * @since   1.0-beta1
     */
    public static function call( $callback )
    {
        // only accept callback and string value
        if ( !is_callable( $callback ) && !is_string( $callback ) ) {
            wp_die(' Invalid paramater for WPop::callback()', 'WPop' );
        }

        // Get the arguments except the callback
        $args = func_get_args();
        array_shift( $args );

        if ( is_callable( $callback ) ) {
            return call_user_func_array( $callback, $args );
        } else if ( is_string( $callback ) && !function_exists( $callback ) ) {
            if ( !file_exists( WPOP_THEME_FUNCTIONS . DS . $callback . '.php' ) ) {
                if ( !file_exists( WPOP_FUNCTIONS . DS . $callback . '.php' ) ) {
                    // Gave up, no such callback!
                    wp_die( 'Callback "' . $callback . '" not found' );
                } else {
                    // Found in theme functions directory
                    require_once WPOP_FUNCTIONS. DS . $callback . '.php';
                }
            } else {
                // Found in framework function directory
                require_once WPOP_THEME_FUNCTIONS . DS . $callback . '.php';
            }

            return call_user_func_array( $callback, $args );
        }
    }

    /**
     * Hook: admin_menu
     *
     * Creates the admin menu.
     *
     * @since   1.0-beta1
     */
    public static function createMenu()
    {
        global $menu;
        
        $theme = WPop_Theme::instance();

        // Create a new separator
        if ( version_compare( get_bloginfo( 'version' ), '2.9', '>=' ) ) {
            //$menu['58.995'] = array( '', 'manage_options', 'separator-wpop', '', 'wp-menu-separator' );
        }

        // Theme settings submenu
        add_submenu_page( 'themes.php', sprintf( '%s ' . __( 'Options', WPOP_THEME_SLUG ), WPOP_THEME_NAME ), __( 'Theme Options', WPOP_THEME_SLUG ), 'edit_theme_options', WPOP_THEME_SLUG, array( $theme, 'displayOptions' ) );
        
        // Mobile settings
        if ( WPop_Theme::haveMobile() ) {
            add_submenu_page( 'themes.php', sprintf( '%s ' . __( 'Mobile Options', WPOP_THEME_SLUG ), WPOP_THEME_NAME ), __( 'Mobile Options', WPOP_THEME_SLUG ), 'edit_theme_options', WPOP_THEME_SLUG . '-mobile', array( $theme, 'displayOptions' ) );
        }

        // Wordspop top level menu
        add_menu_page ( 'Wordspop', 'Wordspop', 'read', 'wpop', 'wpop_page_themes', WPOP_ASSETS . '/images/menu-icon-wordspop.png', 81);

        // Add compose slide menu
        if ( current_theme_supports( 'slide' ) ) {
            add_submenu_page( 'edit.php?post_type=slide', __( 'Slides Composer', WPOP_THEME_SLUG ), __( 'Composer', WPOP_THEME_SLUG ), 'edit_posts', 'slides_composer', 'wpop_page_slides_composer' );
        }
    }
    
    /**
     * Hook: admin_bar_menu
     *
     * Creates the admin bar menu.
     *
     * @since   Version: 1.0.0-beta3
     */
    public static function createAdminBarMenu()
    {
        global $wp_admin_bar;

        // Theme options menu
        if ( current_user_can('edit_theme_options') ) {
            $wp_admin_bar->add_menu( array(
                'parent'  => 'appearance',
                'id'      => WPOP_THEME_SLUG . '-theme-options',
                'title'   => __( 'Theme Options', WPOP_THEME_SLUG ),
                'href'    => get_admin_url( null, 'admin.php?page=' . WPOP_THEME_SLUG )
            ));

            // Mobile settings
            if ( WPop_Theme::haveMobile() ) {
                $wp_admin_bar->add_menu( array(
                    'parent' => 'appearance',
                    'id'  => WPOP_THEME_SLUG . '-mobile-options',
                    'title' => __( 'Mobile Options', WPOP_THEME_SLUG ),
                    'href'  => get_admin_url( null, 'admin.php?page=' . WPOP_THEME_SLUG . '-mobile' )
                ));
            }
        }

        // Wordspop related menu
        $wp_admin_bar->add_menu( array(
            'id'      => 'wordspop-menu',
            'title'   => 'Wordspop',
            'href'    => get_admin_url( null, 'admin.php?page=wpop' )
        ));
        $wp_admin_bar->add_menu( array(
            'parent'  => 'wordspop-menu',
            'id'      => 'wordspop-themes',
            'title'   => __( 'Themes', WPOP_THEME_SLUG ),
            'href'    => get_admin_url( null, 'admin.php?page=wpop' )
        ));
        $wp_admin_bar->add_menu( array(
            'parent'  => 'wordspop-menu',
            'id'      => 'wordspop-website',
            'title'   => __( 'Website', WPOP_THEME_SLUG ),
            'href'    => 'http://wordspop.com/'
        ));
        $wp_admin_bar->add_menu( array(
            'parent'  => 'wordspop-menu',
            'id'      => 'wordspop-forum',
            'title'   => __( 'Support Forum', WPOP_THEME_SLUG ),
            'href'    => 'http://forum.wordspop.com/'
        ));
    }

    /**
     * Show debug message.
     *
     * @param string $log Message.
     *
     * @since   1.0-beta1
     */
    public static function debug( $log )
    {
        if ( defined( 'WPOP_DEBUG' ) && WPOP_DEBUG ) {
            echo "{$log}<br />\n";
        }
    }

    /**
     * Migrate routine.
     *
     * @param bool $process Process migrate or not.
     *
     * @since 1.0-beta6
     */
    public static function migrate( $process )
    {
        if ( !$process ) {
            self::_updateThemeInfo( true );
            $theme = WPop_Theme::instance();
            $theme->notification( __( 'Migration ignored. Have fun!', WPOP_THEME_SLUG ) );
            return;
        }

        $classfile = WPOP_THEME_CLASSES . DS . 'migration.php';
        $migration = new Migration;
        $migration->migrate();
    }
}
