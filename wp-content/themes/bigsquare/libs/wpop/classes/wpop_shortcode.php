<?php
/**
 * Wordspop Framework
 *
 * @package    Wordspop
 * @subpackage Wordspop_Framework
 * @copyright  Copyright (c) 2010-2011 Wordspop
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU GPL version 2
 * @since      1.0-beta6
 */

/**
 * Wordspop shortcode.
 *
 * @package    Wordspop
 * @subpackage Wordspop_Framework
 * @copyright  Copyright (c) 2010-2011 Wordspop
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU GPL version 2
 * @author     Firman Wandayandi <firmanw@wordspop.com>
 * @since      1.0-beta6
 */
class WPop_Shortcode
{
    /**
     * This object instance.
     *
     * @var object
     * @since 1.0-beta6
     */
    private static $_instance;
    
    /**
     * WPop_Config object instance.
     *
     * @var object
     * @since 1.0-beta6
     */
    private $_config;
    
    /**
     * List of registered shortcodes.
     *
     * @var array
     * @since 1.0-beta6
     */
    private $_shortcodes = array();

    /**
     * Constructor.
     *
     * @since 1.0-beta6
     */
    public function __construct()
    {
        $this->_config = WPop_Config::instance();
        $this->_shortcodes = $this->_config->get( 'shortcodes' );

        add_action( 'admin_init', array( $this, 'init' ) );
        $this->_registerShortcodes();
    }

    /**
     * Get an instance of this object.
     *
     * @return object
     * @since 1.0-beta6
     */
    public function instance()
    {
        if ( !self::$_instance instanceof WPop_Shortcode ) {
            self::$_instance = new WPop_Shortcode;
        }

        return self::$_instance;
    }
    
    /**
     * Initialization called by admin_init hook.
     *
     * @since 1.0-beta6
     */
    public function init()
    {
        if ( !current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) && !get_user_option('rich_editing' ) ) {
            return;
        }

        add_filter('mce_external_plugins', array( $this, 'addMcePlugin' ) );
        add_filter('mce_buttons', array( $this, 'registerButton' ) );
    }
    
    /**
     * TinyMCE plugins filter added to mce_external_plugins filter.
     *
     * @since 1.0-beta6
     */
    public function addMcePlugin( $plugins )
    {
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            $plugins['wpopshortcode'] = WPOP_ASSETS . '/js/shortcode-editor/mce.plugin.js';
        } else {
            $plugins['wpopshortcode'] = WPOP_ASSETS . '/js/shortcode-editor/mce.plugin.min.js';
        }
        return $plugins;
    }
    
    /**
     * Add TinyMCE editor button added to mce_buttons filter.
     *
     * @since 1.0-beta6
     */
    public function registerButton( $buttons )
    {
        array_push( $buttons, 'separator', 'wpop_shortcode' );
        return $buttons;
    }

    /**
     * Register the shortcodes.
     *
     * @since 1.0-beta6
     */
    public function _registerShortcodes()
    {
        foreach ( $this->_shortcodes as $shortcode ) {
            if ( !isset( $shortcode['ui_only'] ) ||  !$shortcode['ui_only'] ) {
                add_shortcode( $shortcode['tag'], $shortcode['callback'] );
            }
        }
    }
    
    /**
     * Get the shortcode properties.
     *
     * @param string $tag Shortcode tag.
     *
     * @return mixed An array on success or FALSE on failure.
     * @since 1.0-beta6
     */
    public function get( $tag = null)
    {
        if ( is_string( $tag ) ) {
            foreach ( $this->_shortcodes as $shortcode ){
                if ( $shortcode['tag'] == $tag ) {
                    return $shortcode;
                }
            }
        } else if ( $tag === null ) {
            return $this->_shortcodes;
        }
        
        return false;
    }
}
