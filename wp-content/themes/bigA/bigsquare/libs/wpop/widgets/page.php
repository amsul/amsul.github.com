<?php
/**
 * Page widget.
 *
 * @package    Wordspop
 * @subpackge  Wordspop_Framework
 * @copyright  Copyright (c) 2010-2011 Wordspop
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU GPL version 2
 * @version    $Id:$
 */

/**
 * Page widget class extends from WPop_Widget
 *
 * @package    Wordspop
 * @subpackge  Wordspop_Framework
 * @copyright  Copyright (c) 2010-2011 Wordspop
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU GPL version 2
 * @author     Firman Wandayandi <firmanw@wordspop.com>
 * @since      1.0-beta5
 */
class WPop_Widget_Page extends WPop_Widget
{
    public function __construct()
    {
        parent::init();
    }
    
    public function widget( $args, $instance )
    {
        extract($args);

        if ( empty( $instance['page_id'] ) ) {
            return;
        }
        
        $page =  get_page( $instance['page_id'] );
        if ( $page === null) {
            return;
        }

        // Before the widget
        echo $before_widget;

        // The title
        if ( $instance['show_title'] ) {
          $title = apply_filters( 'widget_title', $page->post_title );
          echo $before_title . $title . $after_title;
        }
        
        echo apply_filters('the_content', $page->post_content);
        
        // After the widget
        echo $after_widget;
    }
}
