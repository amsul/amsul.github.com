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
 * @category   Wordspop
 * @package    Wordspop_Framework
 * @copyright  Copyright (c) 2010-2011 Wordspop
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU GPL version 2
 * @author     Firman Wandayandi <firmanw@wordspop.com>
 */
class WPop_Form
{
    /**
     * Output the input
     *
     * @param string $name Option name
     * @param array $option Options
     * @param mixed $value Current value (default: null)
     *
     * @return string
     */
    public static function input( $option, $value = null )
    {
        if ( $value === null && isset( $option['std'] ) ) {
            $value = $option['std'];
        }

        $atts = isset( $option['atts'] ) ? $option['atts'] : array();

        switch ( $option['type'] ) {
            case 'text':
            case 'checkbox':
            case 'textarea':
            case 'hidden':
            case 'button':
            case 'submit':
                return call_user_func( array( 'WPop_Form', $option['type'] ), $option['name'], $value, $atts );
                break;

            case 'select':
                return self::select( $option['name'], $option['options'], $value, $atts );
                break;

            case 'radio':
                $align = isset( $option['align'] ) ? $option['align'] : 'horizontal';
                return self::radio( $option['name'], $option['options'], $value, $atts, $align );
                break;
        }
    }

    /**
     * Output the input text
     *
     * @param string $name Option name
     * @param array $option Options
     * @param mixed $value Current value (default: null)
     *
     * @return string
     */
    public static function text( $name, $value, $atts = array() )
    {
        return sprintf( '<input type="text" name="%s" value="%s" %s />', 
                  $name, $value, WPop_Utils::atts( $atts )
               );
    }
    
    /**
     * Output the input text
     *
     * @param string $name Option name
     * @param array $option Options
     * @param mixed $value Current value (default: null)
     *
     * @return string
     */
    public static function button( $name, $value, $atts = array() )
    {
        return sprintf( '<input type="button" name="%s" value="%s" %s />', 
                  $name, $value, WPop_Utils::atts( $atts )
               );
    }
    
    /**
     * Output the input text
     *
     * @param string $name Option name
     * @param array $option Options
     * @param mixed $value Current value (default: null)
     *
     * @return string
     */
    public static function submit( $name, $value, $atts = array() )
    {
        return sprintf( '<input type="submit" name="%s" value="%s" %s />', 
                  $name, $value, WPop_Utils::atts( $atts )
               );
    }

    /**
     * Output the textarea input
     *
     * @param string $name Option name
     * @param array $option Options
     * @param mixed $value Current value (default: null)
     *
     * @return string
     */
    public static function textarea( $name, $value, $atts = array() )
    {
        return sprintf( '<textarea name="%1$s" %3$s>%2$s</textarea>',
                  $name, $value, WPop_Utils::atts( $atts )
               );
    }

    /**
     * Output the select input
     *
     *
     * @param string $name Option name
     * @param array $option Options
     * @param mixed $value Current value (default: null)
     *
     * @return string
     */
    public static function select( $name, $options, $value = '', $atts = array() )
    {
        $html  = sprintf( '<select name="%s" %s>' . "\n", $name, WPop_Utils::atts( $atts ) );
        $html .= self::options( $options, $value );
        $html .= '</select>' . "\n";

        return $html;
    }

    /**
     * Output checkbox input
     *
     *
     * @param string $name Option name
     * @param array $option Options
     * @param mixed $value Current value (default: null)
     *
     * @return string
     */
    public static function checkbox( $name, $value = '', $atts = array() )
    {
        if ( !empty( $value ) ) {
            $atts['checked'] = 'checked';
        }

        return sprintf( '<input type="checkbox" name="%s" value="1" %s />', $name, WPop_Utils::atts( $atts ) );
    }

    /**
     * Output radio input
     *
     *
     * @param string $name Option name
     * @param array $option Options
     * @param mixed $value Current value (default: null)
     *
     * @return string
     */
    public static function radio( $name, $options, $value = null, $atts = array(), $align = 'horizontal' )
    {
        $html = array();
        foreach ( $options as $val => $caption ) {
            $checked = '';
            if ( $val == $value ) {
                $checked = ' checked="checked"';
            }

            $class = '';
            if ( $align == 'vertical' ) {
                $class = ' class="' . $align . '"';
            }

            $html[] = sprintf( '<input type="radio" id="%1$s" name="%2$s" value="%3$s"%5$s%6$s /> <label for="%1$s">%4$s</label>',
                        WPop_Utils::namify( "{$name}_{$val}", '-', false ), $name, $val, $caption, WPop_Utils::atts( $atts ), $checked
                     );
        }

        if ( $align == 'horizontal') {
            return implode( '&nbsp;&nbsp;', $html );
        } else {
            return implode( '<br>', $html );
        }
    }

    /**
     * Output hidden input
     *
     *
     * @param string $name Option name
     * @param array $option Options
     * @param mixed $value Current value (default: null)
     *
     * @return string
     */
    public static function hidden( $name, $value, $atts = array() )
    {
        return sprintf( '<input type="hidden" name="%s" value="%s" %s />', 
                  $name, $value, WPop_Utils::atts( $atts )
               );
    }

    /**
     * Output the option element
     *
     * @param array $options List of options
     * @param mixed $current Selected value
     *
     * @return string
     */
    public static function options( $options, $current )
    {
        if ( is_string( $options ) || is_callable( $options ) ) {
            require_once 'wpop.php';
            $res = WPop::call( $options );

            // Make the returns is an array
            settype( $res, 'array' );
            $options = $res;
        }

        settype( $options, 'array' );

        $html = '';
        foreach ( $options as $value => $caption ) {
            $selected = '';
            if ( $value == $current ) {
                $selected = ' selected="selected"';
            }

            $html .= sprintf( '<option value="%1$s"%3$s>%2$s</option>' . "\n",
                        esc_html( $value ), esc_html( $caption ), $selected
                     );
        }
        return $html;
    }
}
