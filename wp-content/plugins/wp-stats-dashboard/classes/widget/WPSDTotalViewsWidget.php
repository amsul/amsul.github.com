<?php
/**
 * WPSDTotalViewsWidget.
 * @author Dave Ligthart <info@daveligthart.com>
 * @version 0.2
 * @package wp-stats-dashboard
 */
class WPSDTotalViewsWidget extends WP_Widget {
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	function __construct() {
	
		$widget_ops = array('description' => __( 'Displays your blog\'s total views') );
	
		parent::__construct(false, __('WPSD - Total Views', 'wpsd'), $widget_ops);
	}
	
	/**
	 * WPSDTotalViewsWidget function.
	 * 
	 * @access public
	 * @return void
	 */
	function WPSDTotalViewsWidget() {
		
		$this->__construct();
	}
	
	/**
	 * form function.
	 * 
	 * @access public
	 * @param mixed $instance
	 * @return void
	 */
	function form($instance) {
			
		// outputs the options form on admin
		$title = esc_attr($instance['title']);
		
		$desc = esc_attr($instance['desc']);
	
        ?>
        	<p><?php _e('This widget will show your blogs total views.', 'wpsd'); ?></p>
            
            <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
       		
       		<p><label for="<?php echo $this->get_field_id('desc'); ?>"><?php _e('Description:'); ?> <textarea class="widefat" id="<?php echo $this->get_field_id('desc'); ?>" name="<?php echo $this->get_field_name('desc'); ?>" type="text"><?php echo $desc; ?></textarea></label></p>
       		
        <?php 
	}

	/* (non-PHPdoc)
	 * @see wp-includes/WP_Widget#update($new_instance, $old_instance)
	 */
	function update($new_instance, $old_instance) {
		// processes widget options to be saved
		return $new_instance;
	}

	/* (non-PHPdoc)
	 * @see wp-includes/WP_Widget#widget($args, $instance)
	 */
	function widget($args, $instance) {
		extract( $args );
			
        $title = apply_filters('widget_title', $instance['title']);
        
        $desc = $instance['desc'];
        
        $totalviews = get_option('wpsd_alltime_hits');
?>
        <?php echo $before_widget; ?>
        <!-- wp-stats-dashboard social profiles widget by http://daveligthart.com -->
        <div id="wpsd-total-views-widget">
      
        <?php if ( $title ) { echo $before_title . $title . $after_title; } ?>
            
            <?php if('' != trim($desc)): ?>             
          	<p><?php echo $desc; ?></p>
          	<?php endif; ?>
								
			<ul id="wpsd-total-views-list" class="wpsd-total-views-list">
				<li><?php echo $totalviews; ?></li>
			</ul>			
						
		</div>
		<!-- wp-stats-dashboard social profiles widget by http://daveligthart.com -->			
        <?php echo $after_widget; ?>   
<?php	
	}
}
add_action('widgets_init', create_function('', 'return register_widget("WPSDTotalViewsWidget");'));
?>