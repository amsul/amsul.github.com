<?php
/**
 * WPSDMiniGraphWidget
 * @author Dave Ligthart <info@daveligthart.com>
 * @version 0.4
 * @package wp-stats-dashboard
 */
class WPSDMiniGraphWidget extends WP_Widget {
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	function __construct() {
		
		$widget_ops = array('description' => __( 'Show a mini graph displaying your blog\'s daily views') );

		parent::__construct(false, __('WPSD - Mini Graph', 'wpsd'), $widget_ops);
	}
	
	/**
	 * WPSDMiniGraphWidget function.
	 * 
	 * @access public
	 * @return void
	 */
	function WPSDMiniGraphWidget() {
		
		$this->__construct();
	}
	
	
	/* (non-PHPdoc)
	 * @see wp-includes/WP_Widget#form($instance)
	 */
	function form($instance) {
		
		// outputs the options form on admin
		$title = esc_attr($instance['title']);
		
		$desc = esc_attr($instance['desc']);
	
        ?>
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
		//echo $this->id_base;
?>
        <?php echo $before_widget; ?>
        <!-- wp-stats-dashboard widget by http://daveligthart.com -->
        <div id="wpsd-mini-graph-widget">
        <?php if ( $title ) { echo $before_title . $title . $after_title; } ?>
          	<?php if('' != trim($desc)): ?>             
          	<p><?php echo $desc; ?></p>
          	<?php endif; ?>
			<div id="wpsd-mini-graph" class="wpsd-mini-graph"></div>			
		</div>
		<!-- wp-stats-dashboard widget by http://daveligthart.com -->			
        <?php echo $after_widget; ?>      
<?php
	}
}
add_action('widgets_init', create_function('', 'return register_widget("WPSDMiniGraphWidget");'));
?>