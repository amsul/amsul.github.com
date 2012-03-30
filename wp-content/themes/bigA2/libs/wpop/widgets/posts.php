<?php
/**
 * Custom recent posts widget.
 *
 * @package    Wordspop
 * @subpackage  Wordspop_Framework
 * @copyright  Copyright (c) 2010-2011 Wordspop
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU GPL version 2
 * @version    $Id:$
 */

/**
 * Widget recent posts extends from WPop_Widget
 *
 * @package    Wordspop
 * @subpackage  Wordspop_Framework
 * @copyright  Copyright (c) 2010-2011 Wordspop
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU GPL version 2
 * @author     Firman Wandayandi <firmanw@wordspop.com>
 * @since      1.0-beta6
 */
class WPop_Widget_Posts extends WPop_Widget
{
    public function __construct()
    {
        parent::init();
    }

    public function widget( $args, $instance )
    {
        extract($args);

        // Before the widget
        echo $before_widget;

        // The title
        if ( $instance['title'] ) {
          $title = apply_filters( 'widget_title', $instance['title'] );
          echo $before_title . $title . $after_title . "\n";
        }

        $query = new WP_Query("showposts={$instance['count']}");
        $html = '<ul>' . "\n";
        while( $query->have_posts() ) {
            $query->the_post();
            $html .= '<li>';
            $html .= '<article class="post clearfix">';
            $html .= '<header class="entry-meta">' . "\n";
            if ( $instance['thumbnail'] && has_post_thumbnail() ) {
                $html .= sprintf( '<div class="entry-thumbnail"><a href="%s" class="thumb">%s</a></div>' . "\n", get_permalink(), get_the_post_thumbnail( get_the_ID(), array( 50, 50 ) ) );
            }
            $html .= sprintf( '<h4 class="entry-title"><a href="%s">%s</a></h4>' . "\n", get_permalink(), get_the_title() );
            $html .= '</header>' . "\n";

            if ( $instance['excerpt'] ) {
              $html .= sprintf( '<div class="entry-excerpt">%s</div>' . "\n", wpop_excerpt( get_the_excerpt(), 10 ) );
            }
  
            ob_start();
            comments_popup_link();
            $comments_link = ob_get_clean();
            $html .= sprintf(
                '<footer class="entry-meta"><span class="published"><time datetime="%s">%s</time></span>, <span class="comment-count">%s</span></footer>',
                get_the_time( 'c' ), get_the_time( get_option('date_format') ), $comments_link
            );
            $html .= '</article>' . "\n";
            $html .= '</li>' . "\n";
        }
        $html .= '</ul>' . "\n";

        echo $html;

        // After the widget
        echo $after_widget;
    }
}
