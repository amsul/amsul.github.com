<?php
/**
 * Wordspop Framework
 *
 * @package    Wordspop
 * @subpackage Wordspop_Framework
 * @copyright  Copyright (c) 2010-2011 Wordspop
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU GPL version 2
 * @since      1.0-beta5
 */

/**
 * Wordspop configuration handler.
 *
 * @package    Wordspop
 * @subpackage Wordspop_Framework
 * @copyright  Copyright (c) 2010-2011 Wordspop
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU GPL version 2
 * @author     Firman Wandayandi <firmanw@wordspop.com>
 */
class WPop_Config
{
    /**
     * An instance of this object
     *
     * @var object
     * @since 1.0-beta5
     */
    static private $_instance = null;

    /**
     * List of bundled configuration
     *
     * @var array
     * @since 1.0-beta5
     */
    private $_bundled = array(
        'options'     => array(),
        'metaboxes'   => array(),
        'widgets'     => array(),
        'shortcodes'  => array()
    );

    /**
     * List of theme configuration
     *
     * @var array
     * @since 1.0-beta5
     */
    private $_theme = array(
        'options'     => array(),
        'hooks'       => array(),
        'metaboxes'   => array(),
        'widgets'     => array(),
        'shortcodes'  => array()
    );
    
    /**
     * List of mobile configuration
     *
     * @var array
     * @since 1.0-beta6
     */
    private $_mobile = array(
        'options'     => array(),
        'hooks'       => array(),
        'metaboxes'   => array(),
        'widgets'     => array(),
        'shortcodes'  => array()
    );

    /**
     * List of complete merged configuration
     *
     * @var array()
     * @since 1.0-beta6
     */
    private $_merged = array();

    /**
     * List of system option which will automatically added.
     *
     * @var array
     */
    private $_systemOptions = array(
        array(
            'type'  => 'system',
            'icon'  => 'scheme',
            'name'  => 'wpop_scheme',
            'title' => 'Scheme'
        ),
        
        array(
            'type'  => 'system',
            'icon'  => 'importexport',
            'name'  => 'wpop_importexport',
            'title' => 'Import/Export'
        )
    );

    /**
     * Constructor
     *
     * @since 1.0-beta5
     */
    public function __construct()
    {
        // Load theme options

        $this->_loadBundled();
        $this->_loadTheme();
    }

    /**
     * Get an instance of this object as singleton.
     *
     * @return object
     * @since 1.0-beta5
     */
    static public function instance()
    {
        if ( !self::$_instance instanceof WPop_Config ) {
            self::$_instance = new WPop_Config;
        }

        return self::$_instance;
    }

    /**
     * Load bundled configuration
     *
     * @since 1.0-beta5
     */
    private function _loadBundled()
    {
        foreach ( $this->_bundled as $context => $val ) {
            $file = WPOP_BUNDLED . DS . $context . '.php';
            if ( file_exists( $file ) ) {
                include $file;
                if ( isset( $$context ) ) {
                    $this->_bundled[ $context ] = $$context;
                }
            }
        }
    }

    /**
     * Load theme configuration
     *
     * @see WPop_Config::_mergeOptions()
     * @see WPop_Config::_normalizeOptions()
     * @see WPop_Config::_mergeWidgets()
     * @since 1.0-beta5
     */
    private function _loadTheme()
    {
        foreach ( $this->_theme as $context => $val ) {
            $default = WPOP_THEME_CONFIG . DS . $context . '.php';
            $mobile = WPOP_THEME_MOBILE_CONFIG . DS . $context . '.php';

            if ( file_exists( $default ) ) {
                include $default;
                if ( isset( $$context ) ) {
                    $this->_theme[ $context ] = $$context;
                }
                unset( $$context );
            }

            if ( file_exists( $mobile ) ) {
                include $mobile;
                if ( isset( $$context ) ) {
                    $this->_mobile[ $context ] = $$context;
                }
                unset( $$context );
            }
        }
    }

    /**
     * Merge bundled options and theme options into an array
     *
     * @see WPop_Config::_compileOption()
     * @since 1.0-beta5
     */
    private function _mergeOptions()
    {
        $merged = array();

        $options = $this->_theme['options'];
        if ( WPop::isMobile() ) {
            $options = $this->_mobile['options'];
        }

        foreach ( $options as $i => $option ) {
            $compiled = $this->_compileOption( $option['type'], $option );
            if ( is_array( $compiled ) ) {
                foreach( $compiled as $bundled ) {
                    $merged[] = $bundled;
                }
            } else {
                $merged[] = $option;
            }
        }

        if ( WPop::isMobile() ) {
            // System options not merged on mobile mode
            return $merged;
        } else {
            return array_merge( $merged, $this->_systemOptions );
        }
    }

    /**
     * Compile the bundled option shortcuts into actual options
     *
     * @return array
     * @since 1.0-beta5
     */
    private function _compileOption( $section, $info )
    {
        if ( !isset( $this->_bundled['options'][$section] ) ) {
            return false;
        }

        $options = array();
        foreach ( $this->_bundled['options'][$section] as $i => $option ) {
            if ( isset( $info['excepts']) && in_array( $option['name'], $info['excepts'] ) ) {
                continue;
            }

            $options[$i] = $option;

            if ( isset( $info['overrides'][$option['name']] ) ) {
                $override = $info['overrides'][$option['name']];
                if ( isset( $override['name'] ) ) {
                    // name is restricted to be override.
                    unset( $override['name'] );
                }

                $options[$i] = array_merge( $options[$i], $override );
            }
        }

        return $options;
    }

    /**
     * Normalize option name into valid one
     *
     * @return array
     * @see WPop_Utils::namify()
     * @since 1.0-beta6
     */
    private function _normalizeOptions( $options )
    {
        require_once 'wpop_utils.php';

        $prefix = 'wpop_theme_';
        if ( WPop::isMobile() ) {
            $prefix = 'wpop_mobile_';
        }

        foreach ( $options as $i => $option ) {
            $options[$i]['name'] = $prefix . WPop_Utils::namify( $option['name'], '_' );
        }
        
        return $options;
    }
    
    /**
     * Merge the hooks.
     *
     * @return array
     * @since 1.0-beta6
     */
    private function _mergeHooks()
    {
        $hooks = array_merge( $this->_theme['hooks'] );
        if ( WPop::isMobile() ) {
            $hooks = array_merge( $hooks, $this->_mobile['hooks'] );
            foreach ( $hooks as $i => $hook ) {
                if ( array_key_exists( 'mobile', $hook ) && !$hook['mobile'] ) {
                    unset( $hooks[$i] );
                }
            }
        }

        return $hooks;
    }

    /**
     * Merge the bundled widgets and the theme widgets
     *
     * @return array
     * @since 1.0-beta5
     */
    private function _mergeWidgets()
    {
        $widgets = array();

        // Process the bundled
        foreach ( $this->_bundled['widgets'] as $i => $widget ) {
            if ( isset( $widget['if_supports'] ) && !current_theme_supports( $widget['if_supports'] ) ) {
                continue;
            }

            $filename = sprintf( '%s.php', WPOP_WIDGETS . DS . substr( $widget['name'], 5 ) );
            $classname = sprintf( 'wpop_widget_%s', substr( $widget['name'], 5 ) );
            if ( file_exists( $filename ) ) {
                include $filename;
                if ( class_exists( $classname ) ) {
                    $widgets[$classname] = $widget;
                    $widgets[$classname]['filename'] = $filename;
                } else {
                    unset( $this->_bundled['widgets'][$i] );
                }
            } else {
                unset( $this->_bundled['widgets'][$i] );
            }
        }
        
        // Process the theme
        foreach ( $this->_theme['widgets'] as $i => $widget ) {
            $filename = sprintf( '%s.php', WPOP_THEME_WIDGETS . DS . $widget['name'] );
            $classname = sprintf( '%s_widget_%s', WPOP_THEME_ID, $widget['name'] );

            // rename to have unique name
            $widget['name'] = sprintf( 'wpop_%s_%s', WPOP_THEME_ID, $widget['name'] );

            if ( file_exists( $filename ) ) {
                include $filename;
                if ( class_exists( $classname ) ) {
                    $widgets[$classname] = $widget;
                    $widgets[$classname]['filename'] = $filename;
                } else {
                    unset( $this->_theme['widgets'][$i] );
                }
            } else {
                unset( $this->_theme['widgets'][$i] );
            }
        }
        
        if ( WPop::isMobile() && !is_admin() ) {
            foreach ( $widgets as $i => $widget ) {
                if ( array_key_exists( 'mobile', $widget ) && !$widget['mobile'] ) {
                    unset( $widgets[$i] );
                }
            }
        }

        return $widgets;
    }

    /**
     * Merges the shortcodes.
     *
     * @return array
     * @since 1.0-beta6
     */
    private function _mergeShortcodes()
    {
        $shortcodes = $bundled = $merged = array();
        foreach ( $this->_theme['shortcodes'] as $i => $shortcode ) {
            $shortcodes[] = $shortcode['tag'];
            $bundled = $this->_getBundledShortcode( $shortcode['tag'] );
            if ( $bundled ) {
                if ( isset( $bundled['atts'] ) ) {
                    $atts = array();

                    // Merge the existing attributes
                    foreach ( $bundled['atts'] as $j => $att ) {
                        $xatt = self::_getShortcodeAttribute( $bundled, $att['name'] );
                        $yatt = self::_getShortcodeAttribute( $shortcode, $att['name'] );
                        if ( $xatt && $yatt ) {
                            $att = array_merge( $xatt, $yatt );
                        }
                        $atts[ $att['name'] ] = $att;
                    }

                    // Add the addional attributes if any
                    foreach ( $shortcode['atts'] as $att ) {
                        if ( !array_key_exists( $att['name'], $atts ) ) {
                            $atts[ $att['name'] ] = $att;
                        }
                    }

                    // Replace the shortcode attributes
                    $shortcode['atts'] = $atts;
                }

                // Merge the shortcode data
                $merged[$i] = array_merge( $bundled, $shortcode );
            } else {
                $merged[$i] = $shortcode;
            }
        }

        foreach ( $this->_bundled['shortcodes']  as $shortcode ) {
            if ( !in_array( $shortcode['tag'], $shortcodes ) ) {
                $merged[] = $shortcode;
            }
        }
        
        return $merged;
    }

    /**
     * Get the bundled shortcode properties by tag.
     *
     * @param string $tag shortcode tag
     *
     * @since 1.0-beta6
     */
    private function _getBundledShortcode( $tag )
    {
        foreach ( $this->_bundled['shortcodes']  as $shortcode ) {
            if ( $shortcode['tag'] == $tag ) {
                return $shortcode;
            }
        }
        
        return false;
    }
    
    /**
     * Get the specified shortcode attribute properties from supplied shortcode array.
     *
     * @param array $shortcode A valid shortcode associated array.
     * @param string $attribute Attribute name.
     *
     * @since 1.0-beta6
     */
    static private function _getShortcodeAttribute( $shortcode, $attribute )
    {
        if ( !isset( $shortcode['atts'] ) ) {
            return false;
        }
        
        foreach ( $shortcode['atts'] as $att ) {
            if ( $att['name'] == $attribute ) {
                return $att;
            }
        }
        
        return false;
    }

    /**
     * Get bundled configuration
     *
     * @param string $context (optional) Configuration context (options, hooks or widgets).
     *
     * @return array
     * @since 1.0-beta5
     */
    public function getBundled( $context )
    {
        if ( !isset( $this->_bundled[ $context ] ) ) {
            return array();
        }

        return $this->_bundled[ $context ];
    }

    /**
     * Get the configuration
     *
     * @param string $context (optional) Configuration context (core, options, hooks, metaboxes, shortcodes or widgets).
     *
     * @return array
     * @since 1.0-beta5
     */
    public function get( $context )
    {
        if ( !isset( $this->_merged[ $context ] ) ) {
            switch ( $context ) {
                case 'options':
                     // Merge the options
                    $options = $this->_mergeOptions();

                    // Normalize the options name
                    $this->_merged['options'] = $this->_normalizeOptions( $options );
                    break;

                case 'hooks':
                    // Merge the hooks
                    $this->_merged['hooks'] = $this->_mergeHooks();
                    break;

                case 'widgets':
                    // Merge the widgets
                    $this->_merged['widgets'] = $this->_mergeWidgets();
                    break;

                case 'shortcodes':
                    $this->_merged['shortcodes'] = $this->_mergeShortcodes();
                    break;

                case 'metaboxes':
                    $this->_merged['metaboxes'] = array_merge( $this->_bundled['metaboxes'], $this->_theme['metaboxes'] );
                    break;
            }
        }

        return $this->_merged[ $context ];
    }
}
