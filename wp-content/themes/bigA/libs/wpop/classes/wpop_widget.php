<?php
/**
 * Wordspop Framework
 *
 * @category   Wordspop
 * @package    WPop_Widget
 * @copyright  Copyright (c) 2010-2011 Wordspop
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU GPL version 2
 * @since      1.0-beta1
 */

/**
 * @category   Wordspop
 * @package    WPop_Widget
 * @copyright  Copyright (c) 2010-2011 Wordspop
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU GPL version 2
 * @author     Firman Wandayandi <firmanw@wordspop.com>
 */
class WPop_Widget extends WP_Widget
{
    /**
     * Parameters
     *
     * @var     array
     * @access  protected
     */
    private $params = array();

    /**
     * Default values
     *
     * @var     array
     * @access  protected
     */
    private $defaults = array();

    /**
     * Initialization
     *
     * Any class extends this class should call this function on their constructor.
     *
     * function Example_Widget()
     * {
     *     WPop_Widget::init();
     * }
     *
     * @access protected
     */
    protected function init()
    {
        $theme = WPop_Theme::instance();

        $classname = strtolower( get_class( $this ) );
        $params = $theme->widgets( $classname );
        $this->params = array_merge( $this->params, $params );

        // Clone the name since we will change it later but still need it for saving.
        foreach ( $this->params['options'] as $i => $option ) {
            $this->params['options'][$i]['id'] = $option['name'];
        }

        $widget_ops = array(
            'classname' => $classname
        );

        if ( isset( $this->params['description'] ) ) {
             $widget_ops['description'] = $this->params['description'];
        }

        if ( isset( $this->params['control'] ) ) {
            $control_ops = $this->params['control'];
        }

        parent::__construct( $this->params['name'], $this->params['title'], $widget_ops, $control_ops );
    }

    /**
     * Render the widget on theme
     *
     * @access public
     */
    public function widget( $args, $instance )
    {
       echo 'Please extends WPop_Widget::widget() on your widget class';
    }

    /**
     * Update the widget
     *
     * @access public
     */
    public function update( $new_instance, $old_instance )
    {
        $instance = $old_instance;

        foreach ( $this->params['options'] as $option ) {
            // Get new value
            $value = $new_instance[ $option['id'] ];

            // Apply the filter if any
            if ( isset( $option['filters'] ) ) {
                $filters = preg_split( '/\s*,\s*/', $option['filters'] );
                foreach ( $filters as $function ) {
                    $value = WPop::call($function, $value);
                }
            }

            // Save the value
            $instance[ $option['id'] ] = $value;
        }

        return $instance;
    }

    /**
     * Render the widget form options.
     *
     * @param   WP_Widget $instance
     * @access  public
     */
    public function form($instance)
    {
        // Load the WPop_Form
        require_once 'wpop_form.php';

        $instance = wp_parse_args( (array) $instance, $this->defaults() );

        // Prepare the options to generate by WPop_Form
        foreach ( $this->params['options'] as $option ) {
            $name = $option['name'];
            $option['name'] = $this->get_field_name( $name );
            $option['atts']['id'] = $this->get_field_id( $name );

            // Checkbox will have null value when not checked.
            // Null value mean the default value will be supplied, so just supply false.
            if ( $instance[ $option['id'] ] === null ) {
                $instance[ $option['id'] ] = false;
            }

            // Create the html
            $input = WPop_Form::input( $option, $instance[ $option['id'] ] );
            if ( $option['type'] == 'checkbox' ) {
                $html = sprintf( '<p>%s <label for="%s">%s</label>', $input, $option['atts']['id'], $option['title'] );
            } else {
                $html = sprintf( '<p><label for="%s">%s:</label> %s', $option['atts']['id'], $option['title'], $input );
            }
            if ( isset( $option['desc'] ) ) {
                $html .= sprintf( '<small class="metabox-description">%s</small>', __( $option['desc'] ) );
            }
            $html .= '</p>' . "\n";

            // Echoes the html
            echo $html;
        }
    }

    /**
     * Get default value.
     *
     * @return  array
     * @access  protected
     */
    protected function defaults()
    {
        foreach ( $this->params['options'] as $option ) {
            if ( isset( $option['std'] ) ) {
                $this->defaults[ $option['id'] ] = $option['std'];
            } else {
                $this->defaults[ $option['id'] ] = '';
            }
        }

        return $this->defaults;
    }
}
