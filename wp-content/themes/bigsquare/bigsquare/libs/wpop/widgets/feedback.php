<?php
/**
 * Feedback widget.
 *
 * @package    Wordspop
 * @subpackge  Wordspop_Framework
 * @copyright  Copyright (c) 2010-2011 Wordspop
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU GPL version 2
 * @version    $Id:$
 */

/**
 * Feedback widget class extends from WPop_Widget
 *
 * @package    Wordspop
 * @subpackge  Wordspop_Framework
 * @copyright  Copyright (c) 2010-2011 Wordspop
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU GPL version 2
 * @author     Firman Wandayandi <firmanw@wordspop.com>
 * @since      1.0-beta5
 */
class WPop_Widget_Feedback extends WPop_Widget
{
    public function __construct()
    {
        parent::init();
        
        if (  is_active_widget(false, false, $this->id_base ) && !is_admin() && !is_login() && !WPop::isMobile() ) {
            if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                wp_enqueue_script( 'wpop-widget-feedback', WPOP_ASSETS . '/js/widgets/widget.feedback.js', array( 'jquery-plugins' ), WPOP_VERSION );
            } else {
                wp_enqueue_script( 'wpop-widget-feedback', WPOP_ASSETS . '/js/widgets/widget.feedback.min.js', array( 'jquery-plugins' ), WPOP_VERSION );
            }
        }
    }
    
    public function widget( $args, $instance )
    {
        extract($args, EXTR_SKIP);

        $cache = wp_cache_get('widget_wpop_feedback', 'widget');

        if ( ! is_array( $cache ) ) {
            $cache = array();
        }

        if ( isset( $cache[ $args['widget_id'] ] ) ) {
            echo $cache[ $args['widget_id'] ];
            return;
        }

        ob_start();

        // Before the widget
        echo $before_widget;

        // The title
        if ( $instance['title'] ) {
          $title = apply_filters( 'widget_title', $instance['title'] );
          echo $before_title . $title . $after_title;
        }
        
        echo '<div class="feedback-widget">';
        echo '<ul' . ($instance['animate'] ? ' class="animate"' : '') . '>';
        $the_query = new WP_Query( sprintf( 'post_type=feedback&posts_per_page=%d&orderby=%s', $instance['count'], $instance['order'] ) );
        while ( $the_query->have_posts() ) {
            $the_query->the_post();

            echo '<li>';
            printf( '<article class="%s">', implode( ' ', get_post_class( 'clearfix' ) ) );
            if( has_post_thumbnail() ) {
                echo '<div class="entry-thumb">';
                the_post_thumbnail( array( 55, 55 ) );
                echo '</div>';
            }

            $meta = array();
            if ( $instance['role'] ) {
                $meta_content = get_post_meta( get_the_ID(), 'feedback_author_role', true );
                if ( $meta_content ) {
                    $meta['role'] = $meta_content;
                }
            }
            
            if ( $instance['url'] ) {
                $meta_content = get_post_meta( get_the_ID(), 'feedback_author_url', true );
                if ( $meta_content ) {
                    $meta['url'] = sprintf( '<a href="%1$s">%1$s</a>', $meta_content );
                }
            }

            echo '<div class="entry-meta">';
            echo '<span class="name">' . get_the_title() . '</span>';
            foreach ( $meta as $key => $value ) {
                printf( '<span class="%s">%s</span>', $key, $value );
            }
            echo '</div>';
    
            echo '<div class="entry-content">';
            echo apply_filters( 'the_content', apply_filters( 'feedback_content', get_the_content() ) );
            echo '</div>';
            echo '</article>';
            echo '</li>';
        }
        echo '</ul>';
        echo '</div>';

        // After the widget
        echo $after_widget;

        $output = ob_get_clean();
        echo $output;

        $cache[ $args['widget_id'] ] = $output;
        wp_cache_set('widget_wpop_feedback', $cache, 'widget');
    }
}