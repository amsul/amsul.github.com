<?php
/**
 * WPSDMetricsWidget.
 * @author Dave Ligthart <info@daveligthart.com>
 * @version 0.4
 * @package wp-stats-dashboard
 */
class WPSDMetricsWidget extends WP_Widget {
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	function __construct() {
		
		$widget_ops = array('description' => __( 'Displays your social metrics') );
		
		parent::__construct(false, __('WPSD - Social Metrics', 'wpsd'), $widget_ops);
	}
	
	/**
	 * WPSDMetricsWidget function.
	 * 
	 * @access public
	 * @return void
	 */
	function WPSDMetricsWidget() {
		
		$this->__construct();
	}
	
	/**
	 * create_cb function.
	 * 
	 * @access public
	 * @param mixed $id
	 * @param mixed $title
	 * @param mixed $instance
	 * @return void
	 */
	function create_cb($id, $title, $instance) {
	
		$value = $instance[$id];
		
		$checked = ' checked="checked"';
?>
<input type="checkbox" id="<?php echo $this->get_field_id($id); ?>" name="<?php echo $this->get_field_name($id); ?>"<?php if($value) echo $checked; ?>/> <?php echo $title; ?>
<br/>
<?php 		
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
       		
       		<fieldset>
       		
       		<p><strong><?php _e('Enable / Disable Metrics', 'wpsd'); ?></strong><br/>
			<?php $wpsd_metrics = wpsd_get_metrics_types(); ?>
			<?php if(null != $wpsd_metrics && is_array($wpsd_metrics)): ?>			
			<?php foreach($wpsd_metrics as $k=>$v): if(is_array($v)) { $title = $v[0]; } ?>
			<?php  $this->create_cb($k, $title, $instance); ?>
			<?php endforeach; ?>
			<?php endif;?>
			</p>
       		</fieldset>
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
?>
        <?php echo $before_widget; ?>
        <!-- wp-stats-dashboard widget by http://daveligthart.com -->
        <div id="wpsd-metrics-widget">
      
        <?php if ( $title ) { echo $before_title . $title . $after_title; } ?>
                         
          	<?php if('' != trim($desc)): ?>             
          	<p><?php echo $desc; ?></p>
          	<?php endif; ?>
									
			<div id="wpsd-metrics-data" class="wpsd-metrics-data"></div>			
						
		</div>
		<!-- wp-stats-dashboard widget by http://daveligthart.com -->			
        <?php echo $after_widget; ?>
        
<?php
	}
}
add_action('widgets_init', create_function('', 'return register_widget("WPSDMetricsWidget");'));
?>