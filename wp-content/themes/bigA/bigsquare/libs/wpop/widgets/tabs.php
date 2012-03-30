<?php
/**
 * Tabs widget.
 *
 * @package    Wordspop
 * @subpackge  Wordspop_Framework
 * @copyright  Copyright (c) 2010-2011 Wordspop
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU GPL version 2
 * @version    $Id:$
 */

/**
 * Widget tabs class extends from WPop_Widget
 *
 * @package    Wordspop
 * @subpackge  Wordspop_Framework
 * @copyright  Copyright (c) 2010-2011 Wordspop
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU GPL version 2
 * @author     Firman Wandayandi <firmanw@wordspop.com>
 * @since      1.0-beta6
 */
class WPop_Widget_Tabs extends WPop_Widget
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
          echo $before_title . $title . $after_title;
        }

        $pane = $tabs = '';
        if ( $instance['popular'] ) {
            $pane .= $this->_popular( (bool) $instance['thumbnail'], (bool) $instance['excerpt'] );
            $tabs .= sprintf( '<li class="current"><a href="#">%s</a></li>', $instance['popular_title' ] );
        }
        
        if ( $instance['latest'] ) {
            $pane .= $this->_latest( (bool) $instance['thumbnail'], (bool) $instance['excerpt'] );
            $tabs .= sprintf( '<li><a href="#">%s</a></li>', $instance['latest_title' ] );
        }
        
        if ( $instance['comments'] ) {
            $pane .= $this->_comments();
            $tabs .= sprintf( '<li><a href="#">%s</a></li>', $instance['comments_title' ] );
        }
        
        if ( $instance['tags'] ) {
            $pane .= $this->_tags();
            $tabs .= sprintf( '<li><a href="#">%s</a></li>', $instance['tags_title' ] );
        }

        if ( $tabs ) {
          echo '<div class="tabs-widget">';
          echo '<div class="wpop-tabs">' . "\n";
          echo '<ul class="tabs-items clearfix">' . "\n";
          echo $tabs;
          echo '</ul>' . "\n";
          echo '<div class="tabs-panes">' . "\n";
          echo $pane;
          echo '</div>' . "\n";
          echo '</div>' . "\n";
          echo '</div>';
        }

        // After the widget
        echo $after_widget;
    }
    
    private function _popular($thumbnail = true, $excerpt = true)
    {
        $query = new WP_Query('showposts=5&orderby=comment_count');
        $html  = '<div class="tab-popular tab-content">' . "\n";
        $html .= '<ul>' . "\n";
        while( $query->have_posts() ) {
            $query->the_post();
            $html .= '<li>' . "\n";
            $html .= '<header class="entry-meta">' . "\n";
            if ( $thumbnail && has_post_thumbnail() ) {
                $html .= sprintf( '<div class="entry-thumbnail"><a href="%s" class="thumb">%s</a></div>' . "\n", get_permalink(), get_the_post_thumbnail( get_the_ID(), array( 50, 50 ) ) );
            }
            $html .= sprintf( '<h4 class="entry-title"><a href="%s">%s</a></h4>', get_permalink(), get_the_title() );
            $html .= '</header>' . "\n";

            if ( $excerpt ) {
              $html .= sprintf( '<div class="entry-excerpt">%s</div>', get_the_excerpt() );
            }
  
            ob_start();
            comments_popup_link();
            $comments_link = ob_get_clean();
            $html .= sprintf(
                '<footer class="entry-meta"><span class="published"><time datetime="%s">%s</time></span>, <span class="comment-count">%s</span></footer>',
                get_the_time( 'c' ), get_the_time( get_option('date_format') ), $comments_link
            );
            $html .= '</li>' . "\n";
        }
        $html .= '</ul>' . "\n";
        $html .= '</div>' . "\n";

        return $html;
    }
    
    private function _latest($thumbnail = true, $excerpt = true)
    {
        $query = new WP_Query('showposts=5');
        $html  = '<div class="tab-latest tab-content" style="display: none;">' . "\n";
        $html .= '<ul>' . "\n";
        while( $query->have_posts() ) {
            $query->the_post();
            $html .= '<li>' . "\n";
            $html .= '<article class="post">' . "\n";
            $html .= '<header class="entry-meta">' . "\n";
            if ( $thumbnail && has_post_thumbnail() ) {
                $html .= sprintf( '<div class="entry-thumbnail"><a href="%s" class="thumb">%s</a></div>' . "\n", get_permalink(), get_the_post_thumbnail( get_the_ID(), array( 50, 50 ) ) );
            }
            $html .= sprintf( '<h4 class="entry-title"><a href="%s">%s</a></h4>', get_permalink(), get_the_title() );
            $html .= '</header>' . "\n";

            if ( $excerpt ) {
              $html .= sprintf( '<div class="entry-excerpt">%s</div>', get_the_excerpt() );
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
        $html .= '</div>' . "\n";

        return $html;
    }
    
    private function _comments()
    {
        global $wpdb;
        
        $sql = "SELECT DISTINCT
                  ID, post_title, post_password, comment_ID, comment_post_ID, comment_author,
                  comment_author_email, comment_date, comment_approved, comment_type,
                  comment_author_url, comment_content
                FROM $wpdb->comments
                LEFT OUTER JOIN $wpdb->posts
                ON ($wpdb->comments.comment_post_ID = $wpdb->posts.ID)
                WHERE
                  comment_approved = '1' AND comment_type = '' AND post_password = ''
                ORDER BY comment_date_gmt DESC LIMIT 5";

        $res = $wpdb->get_results( $sql );
        
        $html  = '<div class="tab-comments tab-content" style="display: none;">' . "\n";
        $html .= '<ul>' . "\n";
        foreach ( $res as $comment ) {
            $author = apply_filters( 'comment_author', $comment->comment_author );
            $link = get_permalink( $comment->ID ) . '#comment-' . $comment->comment_ID;
            $title = $author . ' ' . __( 'on', WPOP_THEME_SLUG ) . ' ' . $comment->post_title;

            $html .= '<li>' . "\n";
            $html .= '<article class="comment">' . "\n";
            $html .= '<header>' . "\n";
            $html .= '<div class="comment-author vcard">' . "\n";
            $html .= sprintf(
                '<a href="%s" title="%s" class="comment-avatar">%s</a>' . "\n",
                $link, $title, get_avatar( $comment, '50' )
            );
            $html .= sprintf(
                '<h4 class="fn"><a href="%s" title="%s">%s</a></h4>' . "\n",
                $link, $title, $author
            );
            $html .= '</div>' . "\n";
            $html .= '</header>' . "\n";
            $html .= '<div class="comment-content">' . self::_comment_excerpt( $comment->comment_content ) . '</div>' . "\n";
            $html .= '</article>' . "\n";
            $html .= '</li>' . "\n";
        }
        return $html;
    }
    
    private static function _comment_excerpt( $content )
    {
        $comment_text = strip_tags( $content );
        $blah = explode( ' ', $comment_text );
        if ( count( $blah ) > 20 ) {
            $k = 20;
            $use_dotdotdot = 1;
        } else {
            $k = count( $blah );
            $use_dotdotdot = 0;
        }
        $excerpt = '';
        for ( $i=0; $i<$k; $i++ ) {
            $excerpt .= $blah[$i] . ' ';
        }
        $excerpt .= ( $use_dotdotdot ) ? '...' : '';
        return apply_filters( 'comment_excerpt', $excerpt );
    }

    private function _tags()
    {
        $html  = '<div class="tab-tags tab-content" style="display: none;">' . "\n";
        $html .= wp_tag_cloud( array( 'largest' => 12, 'smallest' => 12, 'unit' => 'px', 'echo' => false ) );
        $html .= '</div>' . "\n";
        
        return $html;
    }
}
