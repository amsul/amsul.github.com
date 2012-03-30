<?php
/**
 * This file initializes the PageLines framework 
 *
 * @package Platform
 *
 **/

/**
* Before we start, check for PHP4. It is not supported and crashes with a parse error.
* We have to do it here before any other files are loaded.
*
* This can be removed with WordPress 3.2, which will only support PHP 5.2
*
**/ 
if( floatval( phpversion() ) < 5.0 ) {
	echo '<div style="border: 1px red solid">This server is running <strong>PHP ' . phpversion() . '</strong> we are switching back to the default theme for you!<br />';
	echo 'Please contact your host and switch to PHP5 before activating Platform. <a href="' . get_admin_url() . '">Site admin</a></div>';
	switch_theme( 'twentyten', 'twentyten');
	die(); // Brutal but we need to suppress those ugly php errors!
}

/**
 * Run the starting hook
 */
do_action('pagelines_hook_pre', 'core'); // Hook

define('PL_INCLUDES', TEMPLATEPATH . "/includes");

/**
 * Setup all the globals for the framework
 */
require_once( PL_INCLUDES . '/core.globals.php');

/**
 * Localization - Needs to come after config_theme and before localized config files
 */
require_once( PL_INCLUDES . '/library.I18n.php');

/**
 * Load core functions
 */
require_once( PL_INCLUDES . '/library.functions.php');

/**
 * Load Options Functions 
 */
require_once( PL_INCLUDES . '/library.options.php' );

/**
 * Load template related functions
 */
require_once( PL_INCLUDES . '/library.templates.php');

/**
 * Load shortcode library
 */
require_once( PL_INCLUDES . '/library.shortcodes.php');


/**
 * Theme configuration files
 */
require_once( PL_INCLUDES . '/config.options.php' );
require_once( PL_INCLUDES . '/config.templates.php' );


/* Options Singleton */
$GLOBALS['global_pagelines_settings'] = get_option(PAGELINES_SETTINGS);	


/**
 * Load Custom Post Type Class
 */
require_once( PL_INCLUDES . '/class.types.php' );

/**
 * Load layout class and setup layout singleton
 * @global object $pagelines_layout
 */
require_once( PL_INCLUDES . '/class.layout.php' ); 
$GLOBALS['pagelines_layout'] = new PageLinesLayout();
	
/**
 * Load sections handling class
 */
require_once( PL_INCLUDES . '/class.sections.php' );

/**
 * Load template handling class
 */	
require_once( PL_INCLUDES . '/class.template.php' );

/**
 * Load metapanel option handling class
 */
require_once( PL_ADMIN . '/class.options.metapanel.php' );

/**
 * Singleton for Metapanel Options
 */
$GLOBALS['metapanel_options'] =  new PageLinesMetaPanel();

/**
 * Load options UI
 */
require_once( PL_ADMIN . '/class.options.ui.php' );

/**
 * Load Type Foundry Class
 */
require_once( PL_INCLUDES . '/class.typography.php' );

/**
 * Load Colors
 */
//require_once( PL_INCLUDES . '/class.colors.php' );

/**
 * Load dynamic CSS handling
 */
require_once( PL_INCLUDES . '/class.css.php' );

/**
 * PageLines Section Factory Object (Singleton)
 * Note: Must load before the config template file
 * @global object $pl_section_factory
 * @since 4.0.0
 */
$GLOBALS['pl_section_factory'] = new PageLinesSectionFactory();

/**
 * Register and load all sections
 */
pagelines_register_sections();

pagelines_register_hook('pagelines_setup'); // Hook

load_section_persistent(); // Load persistent section functions (e.g. custom post types)
if(is_admin()) load_section_admin(); // Load admin only functions from sections
do_global_meta_options(); // Load the global meta settings tab

	
/**
 * Support optional WordPress functionality
 */
add_theme_support( 'post-thumbnails', apply_filters( 'pagelines_post-thumbnails', array('post') ) );
add_theme_support( 'menus' );
add_theme_support( 'automatic-feed-links' );

// Add editor styling
// -- relative link
add_editor_style( 'admin/css/editor-style.css' );

// Sets Content Width for Large images when adding media
// Re: if ( ! isset( $content_width ) ) $content_width = 640;
pagelines_current_page_content_width(); 

/**
 * Setup Framework Versions
 */
if(VPRO) require_once(PAGELINES_PRO . '/init_pro.php');
if(VDEV) require_once(PAGELINES_DEV . '/init_dev.php');	
	
require_once( PL_INCLUDES . '/version.php' );

/**
 * Enable debug if required.
 * 
 * @since 1.4.0
 */
if ( get_pagelines_option( 'enable_debug' ) ) {

	require_once ( PL_ADMIN . '/class.debug.php');
	add_filter( 'pagelines_options_array', 'pagelines_enable_debug' );
}

/**
 * Load admin actions
 */
require_once (PL_ADMIN.'/actions.admin.php'); 

/**
 * Load option actions
 */
require_once (PL_ADMIN.'/actions.options.php');


/**
 * Load site actions
 */
require_once (PL_INCLUDES.'/actions.site.php');

/**
 * Load actions list
 */
//require_once (PL_INCLUDES.'/class.actions.php');


/**
 * Run the pagelines_init Hook
 */
pagelines_register_hook('pagelines_hook_init'); // Hook
