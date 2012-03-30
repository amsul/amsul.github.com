<?php
/**
 * Wordspop Framework
 *
 * @category   Wordspop
 * @package    WPop
 * @copyright  Copyright (c) 2010-2011 Wordspop
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU GPL version 2
 * @version    $Id:$
 */

// WPop core version
define ( 'WPOP_VERSION', '1.0-beta6' );

// Define some shortcuts
define( 'DS', DIRECTORY_SEPARATOR );
define( 'PS', PATH_SEPARATOR );

// WPop core directories
define( 'WPOP_PATH',            dirname( __FILE__ ) );
define( 'WPOP_FUNCTIONS',       WPOP_PATH . DS . 'functions' );
define( 'WPOP_CLASSES',         WPOP_PATH . DS . 'classes' );
define( 'WPOP_WIDGETS',         WPOP_PATH . DS . 'widgets' );
define( 'WPOP_BUNDLED',         WPOP_PATH . DS . 'bundled' );
define( 'WPOP_LANGUAGES',       WPOP_PATH . DS . 'languages' );
define( 'WPOP_ASSETS',          get_bloginfo( 'template_url' ) . '/libs/wpop/assets' );
define( 'WPOP_FEED_URL',        'http://feeds.wordspop.com/' );
define( 'WPOP_FEED_THEMES_URL', 'http://feeds.wordspop.com/themes' );
define( 'WPOP_API_URL',         'http://api.wordspop.com/' );
define( 'WPOP_THEMES_URL',      'http://wordspop.com/themes' );
define( 'WPOP_DOCS_URL',        'http://docs.wordspop.com/theme' );
define( 'WPOP_FORUM_URL',       'http://forum.wordspop.com/' );

// Theme constant
define( 'WPOP_THEME_CONFIG',            TEMPLATEPATH . DS . 'libs' . DS . 'config' );
define( 'WPOP_THEME_FUNCTIONS',         TEMPLATEPATH . DS . 'libs' . DS . 'functions' );
define( 'WPOP_THEME_CLASSES',           TEMPLATEPATH . DS . 'libs' . DS . 'classes' );
define( 'WPOP_THEME_3RDPARTY',          TEMPLATEPATH . DS . 'libs' . DS . '3rdparty' );
define( 'WPOP_THEME_WIDGETS',           TEMPLATEPATH . DS . 'libs' . DS . 'widgets' );
define( 'WPOP_THEME_LANGUAGES',         TEMPLATEPATH . DS . 'languages' );
define( 'WPOP_THEME_URL',               get_template_directory_uri() );
define( 'WPOP_THEME_SCHEMES',           TEMPLATEPATH . DS . 'schemes' );

// Mobile constant
define( 'WPOP_THEME_MOBILE',            TEMPLATEPATH . DS . 'mobile' );
define( 'WPOP_THEME_MOBILE_CONFIG',     TEMPLATEPATH . DS . 'mobile' . DS . 'libs' . DS . 'config' );
define( 'WPOP_THEME_MOBILE_FUNCTIONS',  TEMPLATEPATH . DS . 'mobile' . DS . 'libs' . DS . 'functions' );

// Add WPop libs directory into include_path
set_include_path(
    WPOP_THEME_CLASSES . PS .
    WPOP_THEME_FUNCTIONS . PS .
    WPOP_THEME_3RDPARTY . PS .
    WPOP_CLASSES . PS .
    WPOP_FUNCTIONS . PS .
    get_include_path()
);

// Autoload functions
require_once WPOP_FUNCTIONS . DS . 'core.php';
require_once WPOP_FUNCTIONS . DS . 'hooks.php';
require_once WPOP_FUNCTIONS . DS . 'pages.php';
require_once WPOP_FUNCTIONS . DS . 'shortcodes.php';

/**
 * @see WPop
 */
require_once 'wpop.php';

// Simply call the initialization routine
WPop::init();
