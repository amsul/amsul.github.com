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
 * @see WP_Query
 */
include_once ABSPATH . WPINC . DS . 'query.php';

/**
 * Wordspop selector.
 *
 * @package    Wordspop
 * @subpackage Wordspop_Framework
 * @copyright  Copyright (c) 2010-2011 Wordspop
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU GPL version 2
 * @author     Firman Wandayandi <firmanw@wordspop.com>
 */
class WPop_Selector extends WP_Query
{
    /**
     * Constructor
     *
     * @param string $option The option name.
     * @param string $extras Extra parameters according to WP_Query.
     */
    public function __construct( $option, $extras = null )
    {
        // Get the options
        $value = wpop_get_option( $option );

        // Parse extra arguments
        $args = array();
        if ( $extras !== null ) {
            $args = wp_parse_args( $extras );
        }

        // Show all posts
        $args['nopaging'] = true;

        $ids = explode( ',', $value['entries'] );

        switch ( $value['source'] ) {
            case 'categories':
                if ( !empty($args) && isset( $args['cat'] ) ) {
                    $args[ 'cat' ] = $value['entries'] . ',' . $args[ 'cat' ];
                } else {
                    $args[ 'cat' ] = $value['entries'];
                }

                return parent::WP_Query( $args );
                break;

            case 'tags':
                if ( !empty($args) && isset( $args['tag__in'] ) ) {
                    $args[ 'tag__in' ] = $value['entries'] . ',' . $args[ 'tag__in' ];
                } else {
                    $args[ 'tag__in' ] = $value['entries'];
                }

                return parent::WP_Query( $args );
                break;

            default:
                parent::init();

                foreach ( $ids as $id ) {
                    $post = get_post($id);
                    if ($post) {
                        $this->posts[] = sanitize_post($post, 'raw');
                        $this->post_count++;
                        $this->found_posts = $this->post_count;
                    }
                }

                update_post_caches( $this->posts, 'slider', true, true);

                if ( $this->post_count > 0 ) {
                    $this->post = $this->posts[0];
                }

                break;
        }
    }
}

/**
 * Wordspop selector entries.
 *
 * @package    Wordspop
 * @subpackage Wordspop_Framework
 * @copyright  Copyright (c) 2010-2011 Wordspop
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU GPL version 2
 * @author     Firman Wandayandi <firmanw@wordspop.com>
 */
class WPop_Selector_Entries
{
    private $_option;
    private $_source;
    private $_entries;

    public function __construct( $option )
    {
        // Get the options
        $value = wpop_get_option( $option );

        $this->_option = $option;
        $this->_source = $value['source'];
        $this->_entries = explode( ',', $value['entries'] );
    }

    public function source()
    {
        return $this->_source;
    }

    public function entries()
    {
        $entries = array();
        switch ( $this->_source ) {
            case 'categories':
                foreach ( $this->_entries as $id ) {
                    $cat = get_category( $id );
                    if ( $cat ) {
                        $entries[ $id ] = $cat->name;
                    }
                }

                break;
            case 'tags':
                foreach ( $this->_entries as $id ) {
                    $tag = get_tag( $id );
                    if ( $tag ) {
                        $entries[ $id ] = $tag->name;
                    }
                }

                break;
            default:
                $selector = new WPop_Selector( $this->_option );
                while ( $selector->have_posts() ) {
                    $selector->the_post();
                    $entries[ get_the_ID() ] = get_the_title();
                }

                break;
        }

        return $entries;
    }
}
