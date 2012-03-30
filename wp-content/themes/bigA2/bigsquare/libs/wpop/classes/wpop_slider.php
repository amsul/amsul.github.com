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
 * @see WP_Query
 */
include_once ABSPATH . WPINC . DS . 'query.php';

/**
 * Wordspop slider query.
 *
 * @package    Wordspop
 * @subpackage Wordspop_Framework
 * @copyright  Copyright (c) 2010-2011 Wordspop
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU GPL version 2
 * @author     Firman Wandayandi <firmanw@wordspop.com>
 * @since      1.0-beta6
 */
class WPop_Slider extends WP_Query
{
    public function __construct( $presentation, $extras = '' )
    {
        // Parse extra arguments
        $args = array();
        if ( $extras !== null ) {
            $args = wp_parse_args( $extras );
        }

        $args['post_type'] = 'slide';
        $args['orderby']   = 'menu_order';
        $args['order']     = 'ASC';

        $presentation_query = array(
            'taxonomy'  => 'presentation',
            'field'     => 'id',
            'terms'     => $presentation
        );

        // Find the whether if presentation taxonomy query already exists or not
        $query_exists = false;
        if ( array_key_exists( 'tax_query', $args ) ) {
            foreach ( $args['tax_query'] as $arg ) {
                if ( is_array( $arg ) ) {
                    $diff = array_diff_assoc( $presentation_query, $arg );
                    if ( empty( $diff ) ) {
                        $query_exists = true;
                        break;
                    }
                }
            }
        }
 
        if ( !$query_exists ) {
            $args['tax_query'][] = $presentation_query;
        }

        parent::__construct( $args );
    }
}
