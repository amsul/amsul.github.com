<?php
/**
 * Twitter widget.
 *
 * @package    Wordspop
 * @subpackge  Wordspop_Framework
 * @copyright  Copyright (c) 2010-2011 Wordspop
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU GPL version 2
 * @version    $Id:$
 */

/**
 * Widget twitter class extends from WPop_Widget
 *
 * @package    Wordspop
 * @subpackge  Wordspop_Framework
 * @copyright  Copyright (c) 2010-2011 Wordspop
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU GPL version 2
 * @author     Firman Wandayandi <firmanw@wordspop.com>
 * @since      1.0-beta5
 */
class WPop_Widget_Twitter extends WPop_Widget
{
    public function __construct()
    {
        parent::init();

        if (  is_active_widget(false, false, $this->id_base ) && !is_admin() && !is_login() ) {
            if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                wp_enqueue_script( 'wpop-widget-twitter', WPOP_ASSETS . '/js/widgets/widget.twitter.js', array( 'jquery-plugins' ), WPOP_VERSION );
            } else {
                wp_enqueue_script( 'wpop-widget-twitter', WPOP_ASSETS . '/js/widgets/widget.twitter.min.js', array( 'jquery-plugins' ), WPOP_VERSION );
            }
        }
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
        echo '<div class="twitter-widget">';
        printf( '<div class="timeline"><ul id="twitter-timeline-%s"><li>Loading tweet &hellip;</li></ul></div>' . "\n", $args['widget_id'] );
        printf( '<script type="text/javascript">WPop_Twitter.add("%s", "%s", %d, %d);</script>' . "\n",  $args['widget_id'], $instance['username'], $instance['show_follow'], $instance['animate'] );
        printf( '<script type="text/javascript" src="http://api.twitter.com/1/statuses/user_timeline/%s.json?callback=WPop_Twitter.push&amp;count=%d&amp;include_rts=t"></script>' . "\n", $instance['username'], $instance['count'] );
        echo '</div>';

        // After the widget
        echo $after_widget;
    }
}
