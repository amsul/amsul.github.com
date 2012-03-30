<?php
/**
 * The bundled metaboxes.
 *
 * An array of metabox arrays. Each metabox array consist with following keys:
 *
 * name: metabox id
 * title: title to show on metabox ui
 * page: post, page or custom post type
 * context: side, normal
 * priority: normal, core, high
 * desc: description
 * options: refers to wpop/bundled/options.php
 *
 * @package    Wordspop
 * @subpackage Wordspop_Framework
 * @copyright  Copyright (c) 2010-2011 Wordspop
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU GPL version 2
 * @since      1.0-beta5
 */

$metaboxes = array(
    /*
    array(
        'name'      => 'custom_permalink',
        'title'     => __( 'Custom Permalink', WPOP_THEME_SLUG ),
        'page'      => array( 'post', 'page', 'slide', 'portfolio' ),
        'context'   => 'side',
        'priority'  => 'high',
        'desc'      => __( 'This theme is support custom permalink, so this post/page may have permalink to other.', WPOP_THEME_SLUG ),
        'options'   => array(
            array(
                'type'    => 'select',
                'title'   => __( 'Target', WPOP_THEME_SLUG ),
                'name'    => 'target',
                'options' => array(
                    ''         => '&mdash; None &mdash;',
                    'post'     => __( 'Post', WPOP_THEME_SLUG ),
                    'page'     => __( 'Page', WPOP_THEME_SLUG ),
                    'category' => __( 'Category', WPOP_THEME_SLUG ),
                    'tag'      => __( 'Tag', WPOP_THEME_SLUG ),
                    'external' => __( 'External', WPOP_THEME_SLUG )
                ),
                'atts' => array( 'class' => 'metabox-fullwidth' ),
            ),
            array(
                'type'    => 'hidden',
                'name'    => 'destination',
            ),
            array(
                'type'    => 'select',
                'title'   => __( 'Destination', WPOP_THEME_SLUG ),
                'name'    => 'destination_select',
                'options' => array(),
                'atts'    => array( 'class' => 'metabox-fullwidth' ),
                'ignore'  => true
            ),
            array(
                'type'    => 'text',
                'title'   => __( 'Destination', WPOP_THEME_SLUG ),
                'name'    => 'destination_external',
                'atts'    => array( 'class' => 'metabox-fullwidth' ),
                'ignore'  => true
            )
        )
    ),*/
    array(
        'name'      => 'feedback_author',
        'title'     => __( 'Extras', WPOP_THEME_SLUG ),
        'page'      => array( 'feedback' ),
        'context'   => 'normal',
        'priority'  => 'high',
        'desc'      => __( 'Additional information of this feedback.', WPOP_THEME_SLUG ),
        'options'   => array(
            array(
                'type'  => 'text',
                'name'  => 'role',
                'title' => __( 'Role', WPOP_THEME_SLUG ),
                'atts'  => array( 'class' => 'metabox-fullwidth' )
            ),
            array(
                'type'  => 'text',
                'name'  => 'url',
                'title' => __( 'Website', WPOP_THEME_SLUG ),
                'atts'  => array( 'class' => 'metabox-fullwidth' )
            )
        )
    ),
    array(
        'name'      => 'portfolio_info',
        'title'     => __( 'Extras', WPOP_THEME_SLUG ),
        'page'      => array( 'portfolio' ),
        'context'   => 'normal',
        'priority'  => 'high',
        'desc'      => __( 'Additional information of this portfolio item.', WPOP_THEME_SLUG ),
        'options'   => array(
            array(
                'type'  => 'text',
                'name'  => 'url',
                'title' => __( 'Link', WPOP_THEME_SLUG ),
                'atts'  => array( 'class' => 'metabox-fullwidth' )
            )
        )
    )
);
