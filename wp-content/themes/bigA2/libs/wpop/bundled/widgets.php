<?php
/**
 * Bundled widgets table list.
 *
 * @package    Wordspop
 * @subpackage Wordspop_Framework
 * @copyright  Copyright (c) 2010-2011 Wordspop
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU GPL version 2
 * @since      1.0-beta5
 */
 
$widgets = array(

    // Page widget
    array(
        'name'        => 'wpop_page',
        'title'       => 'Wordspop - Page',
        'description' => __( 'Stick a page as widget.', WPOP_THEME_SLUG ),
        'control'     => array(),
        'options'     => array(
            array(
                'type'    => 'select',
                'title'   => __( 'Select Page', WPOP_THEME_SLUG ),
                'name'    => 'page_id',
                'options' => 'wpop_pages_options',
                'std'     => ''
            ),
            array(
                'type'    => 'checkbox',
                'title'   => __( 'Show Title', WPOP_THEME_SLUG ),
                'name'    => 'show_title',
                'std'     => 0
            )
        )
    ),

    // Twitter widget
    array(
        'name'        => 'wpop_twitter',
        'title'       => 'Wordspop - Twitter',
        'description' => __( 'Display the tweets.', WPOP_THEME_SLUG ),
        'control'     => array(),
        'options'     => array(
            array(
                'type'    => 'text',
                'title'   => __( 'Title', WPOP_THEME_SLUG ),
                'name'    => 'title',
                'std'     => '',
                'atts'   => array( 'class' => 'widefat' )
            ),
            array(
                'type'    => 'text',
                'name'    => 'username',
                'title'   => __( 'Username', WPOP_THEME_SLUG ),
                'std'     => '',
                'atts'   => array( 'class' => 'widefat' )
            ),
            array(
                'type'    => 'text',
                'name'    => 'count',
                'title'   => __( 'Count', WPOP_THEME_SLUG ),
                'std'     => 3,
                'atts'   => array( 'size' => 3 )
            ),
            array(
                'type'    => 'checkbox',
                'title'   => __( 'Show follow', WPOP_THEME_SLUG ),
                'name'    => 'show_follow',
                'std'     => 1
            ),
            array(
                'type'    => 'checkbox',
                'title'   => __( 'Animate', WPOP_THEME_SLUG ),
                'name'    => 'animate',
                'std'     => 0
            )
        )
    ),

    // Feedback widget
    array(
        'name'        => 'wpop_feedback',
        'title'       => 'Wordspop - Feedback',
        'description' => __( 'The recorded feedback items.', WPOP_THEME_SLUG ),
        'control'     => array(),
        'if_supports' => 'feedback',
        'options'     => array(
            array(
                'type'    => 'text',
                'title'   => __( 'Title', WPOP_THEME_SLUG ),
                'name'    => 'title',
                'std'     => __( 'Feedback', WPOP_THEME_SLUG ),
                'atts'   => array( 'class' => 'widefat' )
            ),
            array(
                'type'    => 'text',
                'title'   => __( 'Count', WPOP_THEME_SLUG ),
                'name'    => 'count',
                'std'     => '5',
                'atts'   => array( 'size' => 3 )
            ),
            array(
                'type'    => 'select',
                'title'   => __( 'Order', WPOP_THEME_SLUG ),
                'name'    => 'order',
                'options' => array(
                    'date'  => __( 'Date', WPOP_THEME_SLUG ),
                    'rand'  => __( 'Random', WPOP_THEME_SLUG ),
                    'none'  => __( 'None', WPOP_THEME_SLUG )
                ),
                'std'     => 3
            ),
            array(
                'type'    => 'checkbox',
                'title'   => __( 'Include Role', WPOP_THEME_SLUG ),
                'name'    => 'role',
                'std'     => 1
            ),
            array(
                'type'    => 'checkbox',
                'title'   => __( 'Include Website', WPOP_THEME_SLUG ),
                'name'    => 'url',
                'std'     => 1
            ),
            array(
                'type'    => 'checkbox',
                'title'   => __( 'Animate', WPOP_THEME_SLUG ),
                'name'    => 'animate',
                'std'     => 0
            )
        )
    ),

    // Flickr widget
    array(
        'name'    => 'wpop_flickr',
        'title'   => 'Wordspop - Flickr',
        'description' => __( 'Flickr photostream badge.', WPOP_THEME_SLUG ),
        'control'     => array(),
        'mobile'      => false,
        'options'     => array(
            array(
                'type'    => 'text',
                'title'   => __( 'Title', WPOP_THEME_SLUG ),
                'name'    => 'title',
                'std'     => 'Flickr',
                'atts'   => array( 'class' => 'widefat' )
            ),
            array(
                'type'    => 'text',
                'title'   => 'Flickr ID',
                'name'    => 'user',
                'desc'    => __( 'You can find out your Flickr ID at <a href="http://idgettr.com" target="_blank">idGettr.com</a>.', WPOP_THEME_SLUG ),
                'atts'    => array( 'class' => 'widefat' )
            ),
            array(
                'type'    => 'radio',
                'title'   => __( 'Content', WPOP_THEME_SLUG ),
                'name'    => 'source',
                'options' => array(
                    'user'  => __( 'Yours', WPOP_THEME_SLUG ),
                    'all'   => __( 'Everyone\'s', WPOP_THEME_SLUG ),
                ),
                'std'     => 'user'
            ),
            array(
                'type'    => 'select',
                'title'   => __( 'Items', WPOP_THEME_SLUG ),
                'name'    => 'count',
                'options' => array(
                    1   => 1,
                    2   => 2,
                    3   => 3,
                    4   => 4,
                    5   => 5,
                    6   => 6,
                    7   => 7,
                    8   => 8,
                    9   => 9,
                    10  => 10
                ),
                'std'     => 4
            ),
            array(
                'type'    => 'radio',
                'title'   => __( 'Display', WPOP_THEME_SLUG ),
                'name'    => 'display',
                'options' => array(
                    'latest'  => __( 'Most recent', WPOP_THEME_SLUG ),
                    'random'  => __( 'Random', WPOP_THEME_SLUG ),
                ),
                'std'     => 'latest'
            ),
            array(
                'type'    => 'select',
                'title'   => __( 'Size', WPOP_THEME_SLUG ),
                'name'    => 'size',
                'options' => array(
                    's' => __( 'Square', WPOP_THEME_SLUG ),
                    't' => __( 'Thumbnail', WPOP_THEME_SLUG ),
                    'm' => __( 'Mid-size', WPOP_THEME_SLUG ),
                ),
                'std'     => 's'
            )
        )
    ),
    
    // Tabs widget
    array(
        'name'    => 'wpop_tabs',
        'title'   => 'Wordspop - Tabs',
        'description' => __( 'A tabbed widget that display popular posts, recent posts, comments and tags.', WPOP_THEME_SLUG ),
        'control'     => array(),
        'mobile'      => false,
        'options'     => array(
            array(
                'type'    => 'text',
                'title'   => __( 'Title', WPOP_THEME_SLUG ),
                'name'    => 'title',
                'std'     => '',
                'atts'   => array( 'class' => 'widefat' )
            ),
            array(
                'type'    => 'checkbox',
                'title'   => __( 'Show Post Thumbnail', WPOP_THEME_SLUG ),
                'name'    => 'thumbnail',
                'std'     => 1
            ),
            array(
                'type'    => 'checkbox',
                'title'   => __( 'Show Post Excerpt', WPOP_THEME_SLUG ),
                'name'    => 'excerpt',
                'std'     => 1
            ),
            array(
                'type'    => 'checkbox',
                'title'   => __( 'Include Popular', WPOP_THEME_SLUG ),
                'name'    => 'popular',
                'std'     => 1
            ),
            array(
                'type'    => 'text',
                'title'   => __( 'Popular Tab Title', WPOP_THEME_SLUG ),
                'name'    => 'popular_title',
                'std'     => 'Popular',
                'atts'   => array( 'class' => 'widefat' )
            ),
            array(
                'type'    => 'checkbox',
                'title'   => __( 'Include Latest Posts', WPOP_THEME_SLUG ),
                'name'    => 'latest',
                'std'     => 1
            ),
            array(
                'type'    => 'text',
                'title'   => __( 'Latest Tab Title', WPOP_THEME_SLUG ),
                'name'    => 'latest_title',
                'std'     => 'Latest',
                'atts'   => array( 'class' => 'widefat' )
            ),
            array(
                'type'    => 'checkbox',
                'title'   => __( 'Include Comments', WPOP_THEME_SLUG ),
                'name'    => 'comments',
                'std'     => 1
            ),
            array(
                'type'    => 'text',
                'title'   => __( 'Comments Tab Title', WPOP_THEME_SLUG ),
                'name'    => 'comments_title',
                'std'     => 'Comments',
                'atts'   => array( 'class' => 'widefat' )
            ),
            array(
                'type'    => 'checkbox',
                'title'   => __( 'Include Tags', WPOP_THEME_SLUG ),
                'name'    => 'tags',
                'std'     => 1
            ),
            array(
                'type'    => 'text',
                'title'   => __( 'Tags Tab Title', WPOP_THEME_SLUG ),
                'name'    => 'tags_title',
                'std'     => 'Tags',
                'atts'   => array( 'class' => 'widefat' )
            )
        )
    ),
    
    // Popular Posts widget
    array(
        'name'    => 'wpop_popular',
        'title'   => 'Wordspop - Popular Posts',
        'description' => __( 'The most popular posts on your site.', WPOP_THEME_SLUG ),
        'control'     => array(),
        'mobile'      => false,
        'options'     => array(
            array(
                'type'    => 'text',
                'title'   => __( 'Title', WPOP_THEME_SLUG ),
                'name'    => 'title',
                'std'     => '',
                'atts'   => array( 'class' => 'widefat' )
            ),
            array(
                'type'    => 'text',
                'title'   => __( 'Number of posts to show', WPOP_THEME_SLUG ),
                'name'    => 'count',
                'std'     => '5',
                'atts'   => array( 'size' => 3 )
            ),
            array(
                'type'    => 'checkbox',
                'title'   => __( 'Show Post Thumbnail', WPOP_THEME_SLUG ),
                'name'    => 'thumbnail',
                'std'     => 1
            ),
            array(
                'type'    => 'checkbox',
                'title'   => __( 'Show Post Excerpt', WPOP_THEME_SLUG ),
                'name'    => 'excerpt',
                'std'     => 1
            )
        )
    ),
    
    // Recent Posts widget
    array(
        'name'    => 'wpop_posts',
        'title'   => 'Wordspop - Custom Recent Posts',
        'description' => __( 'The most recent posts on your site with a custom layout.', WPOP_THEME_SLUG ),
        'control'     => array(),
        'mobile'      => false,
        'options'     => array(
            array(
                'type'    => 'text',
                'title'   => __( 'Title', WPOP_THEME_SLUG ),
                'name'    => 'title',
                'std'     => '',
                'atts'   => array( 'class' => 'widefat' )
            ),
            array(
                'type'    => 'text',
                'title'   => __( 'Number of posts to show', WPOP_THEME_SLUG ),
                'name'    => 'count',
                'std'     => '5',
                'atts'   => array( 'size' => 3 )
            ),
            array(
                'type'    => 'checkbox',
                'title'   => __( 'Show Post Thumbnail', WPOP_THEME_SLUG ),
                'name'    => 'thumbnail',
                'std'     => 1
            ),
            array(
                'type'    => 'checkbox',
                'title'   => __( 'Show Post Excerpt', WPOP_THEME_SLUG ),
                'name'    => 'excerpt',
                'std'     => 1
            )
        )
    ),
    
    // Recent comments widget
    array(
        'name'    => 'wpop_comments',
        'title'   => 'Wordspop - Custom Recent Comments',
        'description' => __( 'The most recent comments with a custom layout.', WPOP_THEME_SLUG ),
        'control'     => array(),
        'mobile'      => false,
        'options'     => array(
            array(
                'type'    => 'text',
                'title'   => __( 'Title', WPOP_THEME_SLUG ),
                'name'    => 'title',
                'std'     => '',
                'atts'   => array( 'class' => 'widefat' )
            ),
            array(
                'type'    => 'text',
                'title'   => __( 'Number of posts to show', WPOP_THEME_SLUG ),
                'name'    => 'count',
                'std'     => '5',
                'atts'   => array( 'size' => 3 )
            ),
            array(
                'type'    => 'checkbox',
                'title'   => __( 'Show Post Excerpt', WPOP_THEME_SLUG ),
                'name'    => 'excerpt',
                'std'     => 1
            )
        )
    )
);
