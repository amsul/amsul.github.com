<?php
/**
 * Recent comments widget.
 *
 * @package    Wordspop
 * @subpackage  Wordspop_Framework
 * @copyright  Copyright (c) 2010-2011 Wordspop
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU GPL version 2
 * @version    $Id:$
 */

/**
 * Widget Recent comments extends from WPop_Widget
 *
 * @package    Wordspop
 * @subpackage  Wordspop_Framework
 * @copyright  Copyright (c) 2010-2011 Wordspop
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU GPL version 2
 * @author     Firman Wandayandi <firmanw@wordspop.com>
 * @since      1.0-beta6
 */
class WPop_Widget_Comments extends WPop_Widget
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

        $comments = get_comments( array( 'number' => $instance['count'], 'status' => 'approve', 'post_status' => 'publish' ) );

        $html = '<ul>' . "\n";
        foreach ( $comments as $comment ) {
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
            $html .= '<div class="comment-content">' . wpop_excerpt( $comment->comment_content, 10 ) . '</div>' . "\n";
            $html .= '</article>' . "\n";
            $html .= '</li>' . "\n";
        }

        $html .= '</ul>' . "\n";
        echo $html;

        // After the widget
        echo $after_widget;
    }
}
