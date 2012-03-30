<?php
/**
 * Wordspop Framework
 *
 * @package    Wordspop
 * @subpackage Wordspop_Framework
 * @copyright  Copyright (c) 2010-2011 Wordspop
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU GPL version 2
 * @since      1.0-beta1
 */

/**
 * @see WPop_Utils
 */
require_once 'wpop_utils.php';

/**
 * @see WPop_Metabox
 */
require_once 'wpop_metabox.php';

/**
 * @package    Wordspop
 * @subpackage Wordspop_Framework
 * @copyright  Copyright (c) 2010-2011 Wordspop
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU GPL version 2
 * @author     Firman Wandayandi <firmanw@wordspop.com>
 */
class WPop_Theme
{
    /**
     * An instance of WPop_Config object
     * @var object
     */
    private $_config;

    /**
     * An instance of WPop_Metabox object
     * @var     object
     */
    private $_metabox;

    /**
     * Theme options
     * @var array
     */
    private $_options = array();
    
    /**
     * Theme meta data
     * @var     array
     */
    private $_meta;

    /**
     * Notification
     * @var     string
     */
    private $_notification = '';

    /**
     * Initialition flag
     * @var     bool
     */
    private $_initialized = false;
    
    /**
     * List of the webfonts are used.
     * @var array
     */
    private $_webFonts = array();

    /**
     * Constructor
     *
     * @access  public
     */
    public function __construct()
    {
        // Core support elements
        $this->_config = WPop_Config::instance();
        $this->_metabox = new WPop_Metabox;

        // Read theme metadata
        $this->_readMeta();

        // Sets the theme properties
        $this->_meta['id'] = WPOP_THEME_ID;
        $this->_meta['slug'] = WPOP_THEME_SLUG;
        $this->_meta['doc'] = WPOP_DOCS_URL . '/' . WPOP_THEME_SLUG;
        $this->_meta['changelog'] = WPOP_DOCS_URL . '/' . WPOP_THEME_SLUG . '#changelog-' . WPOP_THEME_VERSION;

        $this->_options = $this->_config->get( 'options' );
    }

    /**
     * Get this object instance
     *
     * @return  WPop_Theme
     */
    public static function instance()
    {
        if ( !isset( $GLOBALS['wpop_theme'] ) || !is_object( $GLOBALS['wpop_theme'] ) || !is_a( $GLOBALS['wpop_theme'], 'wpop_theme' ) ) {
            $GLOBALS['wpop_theme'] = new WPop_Theme;
        }

        return $GLOBALS['wpop_theme'];
    }

    /**
     * Read theme metadata.
     */
    private function _readMeta()
    {
        $headers = array(
            'name'        => 'Theme Name',
            'uri'         => 'Theme URI',
            'description' => 'Description',
            'author'      => 'Author',
            'authorURI'   => 'Author URI',
            'version'     => 'Version',
            'template'    => 'Template',
            'status'      => 'Status',
            'tags'        => 'Tags',
            'note'        => 'Note',
            'copyright'   => 'Copyright'
        );

        $this->_meta = get_file_data( TEMPLATEPATH . '/style.css', $headers, 'theme' );
    }

    /**
     * Overloading method references to metadata.
     *
     * @param string $name Metadata key.
     */
    public function __get( $name )
    {
        if ( isset( $this->_meta[$name] ) ) {
            return $this->_meta[$name];
        }

        return null;
    }

    /**
     * Get theme options
     *
     * @return  array
     */
    public function options()
    {
        return $this->_options;
    }

    /**
     * Get the specified option attributes
     *
     * @param string $name Option name.
     *
     * @return array|FALSE
     */
    public function option( $name )
    {
        foreach ( $this->_options as $option ) {
            if ( $option['name'] == $name ) {
                return $option;
            }
        }

        return false;
    }

    /**
     * Get the registered widget(s)
     *
     * @return  array
     */
    public function widgets( $id = null )
    {
        if ( is_string( $id ) && array_key_exists( $id, $this->_widgets ) ) {
            return $this->_widgets[$id];
        }

        return $this->_widgets;
    }

    /**
     * Set/get noticiation
     *
     * @param string $notification Message
     * @return string
     */
    public function notification( $message = null )
    {
        if ( is_string( $message ) ) {
            $this->_notification = $message;
        }

        return $this->_notification;
    }

    /**
     * Initialization
     */
    public function init()
    {
        if ( $this->_initialized ) {
            return;
        }

        // Load translations
        load_theme_textdomain( $this->slug, WP_CONTENT_DIR . '/languages' ); // Load general WordPress translations
        load_theme_textdomain( $this->slug, WPOP_THEME_LANGUAGES ); // Load theme translations

        // Register theme framework hooks
        add_action( 'admin_init', array( $this, 'initOptions' ) );
        add_action( 'wp_ajax_wpop_theme_save_options', array( $this, 'ajaxSaveOptions' ) );
        add_action( 'wp_ajax_wpop_theme_import_options', array( 'WPop_Theme', 'ajaxImportOptions' ) );
        add_action( 'widgets_init', array( $this, 'registerWidgets' ) );
        add_action( 'wp_head', array( $this, 'addHeader' ), 999 );
        add_action( 'wp_footer', array( $this, 'addFooter' ) );
        add_action( 'init', array($this, 'addStylesheets' ), 13 );

        $this->_registerHooks();
        $this->_registerMetaboxes();
        $this->_loadFunctions();

        // Set initialization flag
        $this->_initialized = true;
    }

    /**
     * Register hooks.
     */
    private function _registerHooks()
    {
        foreach ( $this->_config->get( 'hooks' ) as $hook ) {
            if ( !isset( $hook['tag'] ) || !isset( $hook['callback'] ) ) {
                continue;
            }

            $args = array(
                $hook['tag'], $hook['callback'],
                isset( $hook['priority' ] ) ? (int) $hook['priority' ] : 10,
                isset( $hook['args' ] ) ? (int) $hook['args' ] : 1
            );

            if ( $hook['context'] == 'action' ) {
                call_user_func_array( 'add_action', $args );
            } else if ( $hook['context'] == 'filter' ) {
                call_user_func_array( 'add_filter', $args );
            }
        }
    }

    /**
     * Register metaboxes.
     */
    private function _registerMetaboxes()
    {
        $metaboxes = $this->_config->get( 'metaboxes' );
        if ( $metaboxes ) {
            foreach ( $metaboxes as $properties ) {
                $this->_metabox->add( $properties['name'], $properties );
            }

            // Add metaboxes
            add_action( 'add_meta_boxes', array( $this->_metabox, 'init' ) );
            add_action( 'save_post', array( $this->_metabox, 'save' ) );
        }
    }

    /**
     * Autoload the theme functions.
     */
    private function _loadFunctions()
    {
        if ( is_dir( WPOP_THEME_FUNCTIONS ) ) {
            // Load the function files automatically
            $files = WPop_Utils::getFiles( WPOP_THEME_FUNCTIONS, array( 'php' ) );
            foreach ( $files as $file ) {
                include_once $file;
            }
        }
        
        if ( is_dir( WPOP_THEME_MOBILE_FUNCTIONS ) ) {
            // Load the function files automatically
            $files = WPop_Utils::getFiles( WPOP_THEME_MOBILE_FUNCTIONS, array( 'php' ) );
            foreach ( $files as $file ) {
                include_once $file;
            }
        }
    }

    /**
     * Hook: admin_init
     *
     * Prepare the options
     */
    public function initOptions()
    {
        foreach ( $this->_options as $option ) {
            if ( $option['type'] != 'heading' && $option['type'] != 'group' ) {
                if ( get_option( $option['name'], false ) === false ) {
                    $this->saveOption( $option['name'], isset( $option['std'] ) ? $option['std'] : '' );
                }
            }
        }
    }

    /**
     * Display theme options page.
     */
    public function displayOptions()
    {
        require_once 'wpop.php';
        WPop::call( 'wpop_page_theme_options', $this );
    }

    /**
     * Hook: widgets_init
     *
     * Register current distributed theme widgets.
     */
    public function registerWidgets()
    {
        $this->_widgets = $this->_config->get( 'widgets' );
        foreach ( $this->_widgets as $classname => $widget ) {
            register_widget( $classname );
        }
    }

    /**
     * Hook: wp_ajax_wpop_theme_save_options
     *
     * @see WPop_Theme::_normalizeOptions()
     * @since 1.0-beta6
     */
    public function ajaxSaveOptions()
    {
        $data = array();
        if ( !empty( $_POST ) ) {
            parse_str( $_POST['data'], $data );
            $data = $this->_normalizeOptions( $data );
        }

        $updated = false;
        foreach ( $data as $option => $value ) {
            $res = $this->saveOption( $option, $value );
            $updated = $updated || $res;
        }


        if ( $updated ) {
            $message = array(
                'type' => 'succeed',
                'text' => 'Settings has been saved successfully.'
            );
        } else {
            $message = array(
                'type' => 'error',
                'text' => 'No option changed, update canceled.'
            );
        }

        echo json_encode( $message );
        exit;
    }

    /**
     * Normalize post data
     *
     * Rewrite the value for saving into database according the data type
     *
     * @param $post array Post data
     * @return array
     */
    private function _normalizeOptions( $post )
    {
        $res = array();
        foreach ( $this->_options as $option ) {
            $value = isset( $option['std'] ) ? $option['std'] : '';

            if ( $option['type'] == 'checkbox' ) {
                if ( array_key_exists( $option['name'], $post ) ) {
                    $res[$option['name']] = 1;
                } else {
                    $res[$option['name']] = 0;
                }
            } else if ( array_key_exists( $option['name'], $post ) ) {
                $value = $post[$option['name']];
                switch ( $option['type'] ) {
                    case 'date':
                        $time = mktime( 0, 0, 0, $value['month'], $value['day'], $value['year'] );
                        $res[$option['name']] = date( 'Y/m/d', $time );
                        break;
                    case 'checkbox':
                        $res[$option['name']] = (bool) $value;
                        break;
                    case 'character':
                        $res[$option['name']] = serialize( $value );
                        break;
                    default:
                        $res[$option['name']] = $value;
                        break;
                }
            }
        }

        return $res;
    }

    /**
     * Save option
     *
     * @param   string  $name Option name
     * @param   mixed   $value Option value
     * @return  bool
     */
    public function saveOption( $name, $value )
    {
        if ( is_array( $value ) || is_object( $value ) ) {
            $value = serialize( $value );
        }

        return update_option( $name, stripslashes_deep( $value ) );
    }

    /**
     * Get theme option value
     *
     * Attempt to retrieve value from database or return default if not exists.
     *
     * @param   string  $name  Option name
     * @return  mixed
     */
    public static function getOptions( $name = null )
    {
        if ( isset( $this ) ) {
            $theme = $this;
        } else {
            $theme = WPop_Theme::instance();
        }

        // Returns all options
        $res = array();
        if ( $name === null ) {
            foreach ($theme->options() as $option ) {
                if ( $option['type'] == 'heading' || $option['type'] == 'system' ) {
                    continue;
                }
                
                $name = preg_replace( '/^wpop_theme_/', '', "wpop_theme_{$option['name']}" );

                $res[ $name ] = maybe_unserialize( get_option( $name ) );
                if ( $res[ $name ] === false && array_key_exists( 'std', $option ) ) {
                    $res[ $name ] = $option['std'];
                }
            }
            
            return $res;
        }

        // Returns specified options
        $name = preg_replace( '/^wpop_theme_/', '', $name );
        $res = maybe_unserialize( get_option( "wpop_theme_{$name}" ) );
        if ( $res === false ) {
            foreach ( $theme->options() as $option ) {
                if ( $option['name'] == $name && isset( $option['std'] ) ) {
                    return $option['std'];
                }
            }
        }

        return $res;
    }
    
    /**
     * Export the options.
     *
     * @return string base64 encoded string.
     * @since 1.0-beta6
     */
    public static function exportOptions()
    {
        return base64_encode( serialize( self::getOptions() ) );
    }
    
    /**
     * Import the options.
     *
     * @param array $options An associated array of options.
     *
     * @return bool
     * @since 1.0-beta6
     */
    public static function importOptions( $options )
    {
        $updated = false;
        $theme = self::instance();
        foreach( $options as $key => $val ) {
            $res = $theme->saveOption( $key, $val );
            $updated = $updated || $res;
        }
        return $updated;
    }
    
    /**
     * Import the option from the AJAX POST request.
     *
     * @since 1.0-beta6
     */
    public static function ajaxImportOptions()
    {
        $res = false;

        if ( isset($_POST['data']) && !empty( $_POST['data'] ) ) {
            $options = @unserialize( base64_decode( $_POST['data'] ) );
            if ( is_array( $options ) ) {
                if ( self::importOptions( $options ) ) {
                    $res = array(
                        'text'  => 'Options has been imported successfully.',
                        'type'  => 'succeed'
                    );
                } else {
                    $res = array(
                        'text'  => 'Nothing imported.',
                        'type'  => 'succeed'
                    );
                }
            } else {
                $res = array(
                    'text'  => 'Import failed. Invalid encoded options.',
                    'type'  => 'error'
                );
            }
        }

        echo json_encode( $res );
        exit;
    }

    /**
     * Get available schemes
     *
     * @return array
     */
    public static function availableSchemes()
    {
        $schemes = array(
            'default' => array(
                'name'        => 'Default',
                'screenshot'  => WPOP_THEME_URL . '/screenshot.png'
            )
        );

        if ( !is_dir( WPOP_THEME_SCHEMES ) ) {
            return $schemes;
        }

        $d = dir( WPOP_THEME_SCHEMES );
        while ( false !== ( $entry = $d->read() ) ) {
            if ( $entry != '.' && $entry != '..' && $entry != '.svn' && is_dir( WPOP_THEME_SCHEMES . DS . $entry ) ) {
                $style = WPOP_THEME_SCHEMES . DS . $entry . DS . 'style.css';
                if ( file_exists( $style ) ) {
                    $headers = array(
                        'name'        => 'Scheme Name',
                        'version'     => 'Version'
                    );
                    $data = get_file_data( $style, $headers );
                    if ( empty( $data['name'] ) ) {
                        $data['name'] = $entry;
                    }

                    if ( !file_exists( WPOP_THEME_SCHEMES . DS . $entry . DS . 'screenshot.png' ) ) {
                        $data['screenshot'] = WPOP_ASSETS . '/images/no-screenshot.png';
                    } else {
                        $data['screenshot'] = sprintf( '%s/schemes/%s/screenshot.png', WPOP_THEME_URL, $entry );
                    }

                    $schemes[$entry] = $data;
                }
            }
        }
        $d->close();

        return $schemes;
    }
    
    /**
     * Find the whether mobile version is available or not.
     *
     * @return bool
     */
    public static function haveMobile()
    {
        if ( !is_dir( WPOP_THEME_MOBILE ) ) {
            return false;
        }

        $d = dir( WPOP_THEME_MOBILE );
        $exceptions = array('.svn', 'libs','functions');
        while ( false !== ( $entry = $d->read() ) ) {
            if ( $entry != '.' && $entry != '..' && !in_array( $entry, $exceptions ) ) {
                if ( file_exists( WPOP_THEME_MOBILE . DS . $entry . DS . 'style.css' ) ) {
                    return true;
                }
            }
        }

        $d->close();
    }

    /**
     * Add favicon link
     *
     * @return string
     */
    private function _favIcon() {
        $favicon = self::getOptions( 'favicon' );
        if ($favicon) {
            return sprintf( '<link rel="shortcut icon" href="%s" />' . "\n", $favicon );
        }
    }

    /**
     * Get custom header
     *
     * @return string
     */
    private function _customHeader()
    {
        return self::getOptions( 'header_extras' );
    }

    /**
     * Get custom footer
     *
     * @return string
     */
    private function _customFooter()
    {
        $retval = '';

        $extras = self::getOptions( 'footer_extras' );
        if ( $extras ) {
            $retval = $extras;
        }

        $tracking = self::getOptions( 'tracking_code' );
        if ( $tracking ) {
            $retval .= $tracking;
        }

        return $retval;
    }

    /**
     * Get custom styling
     *
     * @return string
     * @access private
     */
    private function _customStyle()
    {
        // custom styling enable?
        if ( !self::getOptions( 'styling_enable' ) ) {
            return false;
        }

        $css = '';

        // css background
        $bgcss = array();
        $background = self::getOptions( 'background' );
        settype( $background, 'array' );
        if ( array_key_exists( 'none', $background ) && $background['none'] ) {
            $bgcss[] = 'background: none;';
        } else {
            if ( array_key_exists( 'color', $background ) && !empty( $background['color'] ) ) {
                $bgcss[] = sprintf( 'background-color: %s;', $background['color'] );
            }
            if ( array_key_exists( 'image', $background ) && !empty( $background['image'] ) ) {
                $bgcss[] = sprintf( 'background-image: url(%s);', $background['image'] );
                if ( array_key_exists( 'position', $background ) && !empty( $background['image'] ) ) {
                    $bgcss[] = sprintf( 'background-position: %s;', $background['position'] );
                }
                if ( array_key_exists( 'repeat', $background ) && !empty( $background['repeat'] ) ) {
                    $bgcss[] = sprintf( 'background-repeat: %s;', $background['repeat'] );
                }
            }
        }
        if ( $bgcss ) {
            $css .= sprintf( 'body { %s }' . "\n", implode( "\n", $bgcss ) );
        }

        // css link
        $link = self::getOptions( 'link' );
        settype( $link, 'array' );
        if ( array_key_exists( 'link', $link ) && !empty( $link['link'] ) ) {
            $css .= sprintf( 'a:link { color: %s; }', $link['link'] );
        }
        if ( array_key_exists( 'active', $link ) && !empty( $link['active'] ) ) {
            $css .= sprintf( 'a:active { color: %s; }', $link['active'] );
        }
        if ( array_key_exists( 'hover', $link ) && !empty( $link['hover'] ) ) {
            $css .= sprintf( 'a:hover { color: %s; }', $link['hover'] );
        }
        if ( array_key_exists( 'visited', $link ) && !empty( $link['visited'] ) ) {
            $css .= sprintf( 'a:visited { color: %s; }', $link['visited'] );
        }

        // css codes
        $css_codes = self::getOptions( 'custom_css' );
        if ( $css_codes ) {
            $css .= $css_codes . "\n";
        }

        return $css;
    }

    /**
     * Get custom typography css
     *
     * @return string
     */
    private function _customTypography()
    {
        $output = '';
        foreach ( $this->_options as $option ) {
            if ( $option['type'] == 'character' && isset( $option['selector'] ) ) {
                $output .= $this->_getTypographyCSS( $option['selector'], self::getOptions( $option['name'] ) );
            }
        }

        return $output;
    }

    /**
     * Get typography css
     *
     * @return string
     */
    private function _getTypographyCSS( $selector, $options )
    {
        if ( !is_array( $options ) || !isset($options['enable']) || !$options['enable'] ) {
            return false;
        }

        if ( self::_isWebFont( $options['font'] ) ) {
            $res = $this->_addWebFont( $options['font'] );
            if ( !$res ) {
                return false;
            }
            $options['font'] = $res;
        }

        $css = "{$selector} {\n";
        
        if ( !empty( $options['font'] ) ) {
            $css .= "font-family: {$options['font']} !important;\n";
        }
        
        if ( !empty( $options['size'] ) ) {
            $css .= "font-size: {$options['size']}{$options['unit']} !important;\n";
        }

        switch ( $options['style'] ) {
            case 'normal':
                $css .= 'font-weight: normal !important;' . "\n" .
                        'font-style: normal !important;' . "\n";
                break;
            case 'bold':
                $css .= 'font-weight: bold !important;' . "\n" .
                        'font-style: normal !important;' . "\n";
                break;
            case 'italic':
                $css .= 'font-weight: normal !important;' . "\n" .
                        'font-style: italic !important;' . "\n";
                break;
            case 'bold italic':
                $css .= 'font-weight: bold !important;' . "\n" .
                        'font-style: italic !important;' . "\n";
        }

        if ( !empty( $options['color'] ) ) {
            $css .= "color: {$options['color']} !important;\n";
        }

        $css .= '}' . "\n";

        return $css;
    }

    /**
     * Find the whether font is a webfont or not
     *
     * @return bool
     */
    private function _isWebFont( $font )
    {
        if ( ( $pos = strpos( $font, ':' ) ) !== false ) {
            return true;
        }

        return false;
    }

    /**
     * Add webfont to the list for loading
     *
     * @access private
     */
    private function _addWebFont( $font )
    {
        if ( !preg_match( '/(\w+):\s+("*([\w\s]+)"*(,|$).*)/', $font, $matches ) ) {
            return false;
        }

        $this->_webFonts[ $matches[1] ][] = $matches[3];
        return $matches[2];
    }

    /**
     * Hook: init
     *
     * Enqueue stylesheets.
     *
     * @since  Version 1.0.0-beta3
     */
    public function addStylesheets()
    {
        if ( !is_admin() ) {
            $main = get_stylesheet_uri();
            wp_enqueue_style( WPOP_THEME_SLUG, $main, false, WPOP_THEME_VERSION, 'screen' );

            $scheme = wpop_get_scheme();
            if ( $scheme !== false && $scheme != 'default' && file_exists( wpop_get_scheme_stylesheet() ) ) {
                wp_enqueue_style( WPOP_THEME_SLUG . '-scheme', wpop_get_scheme_stylesheet_uri(), array( WPOP_THEME_SLUG ), WPOP_THEME_VERSION, 'screen' );
            }
        }
    }

    /**
     * Hook: wp_head
     *
     * Add the custom generated header
     */
    public function addHeader()
    {
        echo $this->_favIcon();

        $custom_style = $this->_customStyle();

        $custom_typography = $this->_customTypography();
        if ( $custom_typography && !empty( $this->_webFonts ) ) {
            echo '<script type="text/javascript">' . "\n";
            echo 'WebFontConfig = {';

            $i = 0;
            foreach ( $this->_webFonts as $provider => $fonts ) {
                echo "{$provider}: {";
                switch ($provider) {
                    case 'google':
                        echo 'families: [ \'' .implode( '\',\'', $fonts ) . '\' ]';
                        break;
                }
                echo '}' . ( $i < count($this->_webFonts) - 1 ? ',' : '' );
                $i++;
            }
            echo '};' . "\n";
            echo '</script>'. "\n";
        }

        if ( $custom_style || $custom_typography ) {
            echo '<style type="text/css">' . "\n" .
                 $custom_style .
                 $custom_typography .
                 '</style>' . "\n";
        }

        $custom_header = $this->_customHeader();
        if ( $custom_header ) {
            echo $custom_header;
        }
    }

    /**
     * Hook: wp_footer
     *
     * Add custom generated footer
     */
    public function addFooter()
    {
        $custom_footer = $this->_customFooter();
        if ( $custom_footer ) {
            echo $custom_footer;
        }
    }
}
