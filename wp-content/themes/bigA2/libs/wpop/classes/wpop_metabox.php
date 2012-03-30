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
 * Wordspop metabox handler.
 *
 * @package    Wordspop
 * @subpackage Wordspop_Framework
 * @copyright  Copyright (c) 2010-2011 Wordspop
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU GPL version 2
 * @author     Firman Wandayandi <firmanw@wordspop.com>
 */
class WPop_Metabox
{
    /**
     * List of added metaboxes
     *
     * @var     array
     */
    private $_metaboxes = array();

    /**
     * Add metabox
     *
     * @param string $id Metabox id
     * @param array $properties Metabox properties
     */
    public function add( $id, $properties )
    {
        $this->_metaboxes[ $id ] = $properties;
    }

    /**
     * Initialization
     *
     * This function will be called as hook on add_metaboxes hook on WPop_Theme::_init().
     *
     * @see WPop_Theme::_init()
     */
    public function init()
    {
        foreach ( $this->_metaboxes as $id => $properties ) {
            $context = isset( $properties[ 'context'] ) ? $properties[ 'context'] : 'advanced';
            $priority = isset( $properties[ 'priority'] ) ? $properties[ 'priority'] : 'default';

            if ( is_array( $properties[ 'page' ] ) ) {
                foreach ( $properties[ 'page' ] as $value ) {
                    add_meta_box( $id, $properties[ 'title' ], array( $this, 'render' ), $value, $context, $priority );
                }
            } else {
                add_meta_box( $id, $properties[ 'title' ], array( $this, 'render' ), $properties[ 'page'], $context, $priority, array( $id ) );
            }
        }
    }

    /**
     * Metabox render callback.
     */
    public function render()
    {
        require_once 'wpop_ui.php';

        $post = func_get_arg(0);
        $arg = func_get_arg(1);
        $metabox = $this->_metaboxes[ $arg['id'] ];
        $metabox[ 'id' ] = $arg['id'];
 
        printf( '<div id="%s" class="wpop-metabox">', WPop_Utils::namify( "metabox-{$metabox['id']}" ) );
        if ( isset( $metabox[ 'options' ] ) )
        {
            wp_nonce_field( plugin_basename( __FILE__ ), "{$metabox[ 'id' ]}_noncename" );
            if ( isset( $metabox[ 'desc' ] ) ) {
                printf( '<p>%s</p>', $metabox[ 'desc' ] );
            }

            foreach ( $metabox[ 'options' ] as $option ) {
                $field = "{$metabox[ 'id' ]}_{$option[ 'name' ]}";
                $option['name'] = $field;
                $option['atts']['id'] = WPop_Utils::namify( $field );

                WPop_UI::render( $option, get_post_meta( $post->ID, $field, true ) );
            }
        }
        echo '</div>';
    }

    /**
     * Callback called on save_post hook which handling metabox field value storing.
     *
     * @param integer $post_id Post ID
     *
     * @see WPop_Metabox::_save()
     */
    public function save( $post_id )
    {
        // Is it auto save routine?
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        
        foreach ( $this->_metaboxes as $id => $properties) {
            if ( !isset( $_POST[ "{$id}_noncename" ] ) || !wp_verify_nonce( $_POST[ "{$id}_noncename" ], plugin_basename( __FILE__ ) ) ) {
                continue;
            }

            // Check permissions
            if ( $_POST['post_type'] == 'page'  && !current_user_can( 'edit_page', $post_id ) ) {
                continue;
            } else if ( !current_user_can( 'edit_post', $post_id ) ) {
                continue;
            }

            $this->_save( $post_id, $id );
        }
    }

    /**
     * Store the metabox fields
     *
     * @param integer $post_id Post ID
     * @param string $metabox Metabox ID
     */
    private function _save( $post_id, $metabox )
    {
        if ( isset( $this->_metaboxes[ $metabox ][ 'options'] ) ) {
            foreach ( $this->_metaboxes[ $metabox ][ 'options'] as $option ) {
                if ( isset( $option[ 'ignore' ] ) && $option[ 'ignore' ] ) {
                    continue;
                }

                $field = "{$metabox}_{$option[ 'name' ]}";
                if ( !isset ( $_POST[ $field ] ) ) {
                    continue;
                }

                $value = $_POST[ $field ];
                if ( get_post_meta( $post_id, $field, true ) != $value ) {
                    update_post_meta( $post_id, $field, $value );
                } else if ( $value == '' ) {
                    delete_post_meta( $post_id, $field );
                }
            }
        }
    }
}
