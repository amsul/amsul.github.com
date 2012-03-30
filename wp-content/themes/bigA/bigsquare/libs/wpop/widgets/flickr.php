<?php
/**
 * Flickr widget.
 *
 * @package    Wordspop
 * @subpackge  Wordspop_Framework
 * @copyright  Copyright (c) 2010-2011 Wordspop
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU GPL version 2
 * @version    $Id:$
 */

/**
 * Flickr widget class extends from WPop_Widget
 *
 * @package    Wordspop
 * @subpackge  Wordspop_Framework
 * @copyright  Copyright (c) 2010-2011 Wordspop
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU GPL version 2
 * @author     Firman Wandayandi <firmanw@wordspop.com>
 * @since      1.0-beta5
 */
class WPop_Widget_Flickr extends WPop_Widget
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
        
        if ( $instance['user'] ) {
            echo '<div id="flickr_badge_wrapper">';
            printf(
                '<script type="text/javascript" src="http://www.flickr.com/badge_code_v2.gne?count=%d&display=%s&size=%s&layout=x&source=%s&user=%s"></script>',
                $instance['count'], $instance['display'], $instance['size'], $instance['source'], $instance['user']
            );
            echo '</div>';
        }

        // After the widget
        echo $after_widget;
    }
}
