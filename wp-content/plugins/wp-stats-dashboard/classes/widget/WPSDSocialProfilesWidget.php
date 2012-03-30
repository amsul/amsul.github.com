<?php
/**
 * WPSDSocialProfilesWidget.
 * @author Dave Ligthart <info@daveligthart.com>
 * @version 0.3
 * @package wp-stats-dashboard
 */
class WPSDSocialProfilesWidget extends WP_Widget {
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	function __construct() {
		
		$widget_ops = array('description' => __( 'Displays your social profiles') );
		
		parent::__construct(false, __('WPSD - Social Profiles', 'wpsd'), $widget_ops);
	}
	
	/**
	 * WPSDSocialProfilesWidget function.
	 * 
	 * @access public
	 * @return void
	 */
	function WPSDSocialProfilesWidget() {
		
		$this->__construct();
	}
	
	/**
	 * create_cb function.
	 * 
	 * @access public
	 * @param mixed $id
	 * @param mixed $title
	 * @param mixed $instance
	 * @param mixed $url
	 * @return void
	 */
	function create_cb($id, $title, $instance, $url) {
											
		$value = $instance[$id];
		
		$checked = ' checked="checked"';
?>
<input type="checkbox" id="<?php echo $this->get_field_id($id); ?>" name="<?php echo $this->get_field_name($id); ?>"<?php if($value) echo $checked; ?>/> <?php if('' != trim($url)){ ?><a href="<?php echo $url; ?>" target="_blank" rel="me" title="<?php echo $title; ?>"><?php echo $title; ?></a><?php } else { echo $title; }?>
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
        	<p><?php _e('Use the social profiles widget on your website to extend and optimize your social graph.', 'wpsd'); ?></p>
            
            <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
       		
       		<p><label for="<?php echo $this->get_field_id('desc'); ?>"><?php _e('Description:'); ?> <textarea class="widefat" id="<?php echo $this->get_field_id('desc'); ?>" name="<?php echo $this->get_field_name('desc'); ?>" type="text"><?php echo $desc; ?></textarea></label></p>
       		
       		<fieldset>
       		
       		<p><strong><?php _e('Enable Icon Images', 'wpsd'); ?></strong>
       		<br/>
        	<?php $this->create_cb('enable_images', __('Enabled'), $instance, ''); ?>
       		<br/>
       		
       		<strong><?php _e('Enable / Disable Profiles', 'wpsd'); ?></strong>
			<br/>
        	<?php $wpsd_metrics = wpsd_get_metrics_types(); ?>
			<?php if(null != $wpsd_metrics && is_array($wpsd_metrics)): ?>			
			<?php foreach($wpsd_metrics as $k=>$v): if(is_array($v)) { $title = $v[0]; } ?>
			<?php if('klout' == $k) { $url = str_replace('{username}' , get_option('wpsd_twitter_un'), $v[2]);  $this->create_cb($k, $title, $instance, $url); continue; } ?>
			<?php if('' != trim($v[2])): $username = get_option("wpsd_{$k}_un"); if('' == $username) { $username = get_option("wpsd_{$k}_uri"); } ?>
			<?php if('' != trim($username)): $url = str_replace('{username}' , $username, $v[2]);?>
			<?php if('facebook' == $k) { $url = get_option('wpsd_facebook_link'); } ?>
			
			<?php $this->create_cb($k, $title, $instance, $url); ?>
			<?php endif; endif;?>
			<?php endforeach; ?>
			<?php endif;?>
			
			<?php $this->create_cb('enable_attribute', 'Attribute', $instance, 'http://www.daveligthart.com/wp-stats-dashboard-10/'); ?>
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
        <!-- wp-stats-dashboard social profiles widget by http://daveligthart.com -->
        <div id="wpsd-profiles-widget">
      
        <?php if ( $title ) { echo $before_title . $title . $after_title; } ?>
            
            <?php if('' != trim($desc)): ?>             
          	<p><?php echo $desc; ?></p>
          	<?php endif; ?>
								
			<ul id="wpsd-profiles-list" class="wpsd-profiles-list">
			<?php 
				
				$enabled_images = false;
				$enabled_attribute = false;
				
				$wpsd_metrics = wpsd_get_metrics_types();
			
				$widget_opts = get_option('widget_wpsdsocialprofileswidget');
				
				if(null != $widget_opts) {
					
					foreach($widget_opts as $opt) {
						
						if(is_array($opt)) {
							
							foreach($opt as $key=>$value) {
								
								if('on' == $value) {
									
									if('enable_attribute' == $key) {
										$enabled_attribute = true;
									} 
									else if('enable_images' == $key) {
										$enabled_images = true;
									}
									
								}
							}
						}
					}
					
					
					foreach($widget_opts as $opt) {
						
						if(null != $opt && count($opt) > 0) {
							
							if(is_array($opt)) {
								
								foreach($opt as $key=>$value) {
									
									if($value == 'on') {
																		
										$v = $wpsd_metrics[$key];
										
										$username = get_option("wpsd_{$key}_un");
										
										if('' == $username) {
											
											$username = get_option("wpsd_{$key}_uri");
										}
										
										if('facebook' == $key) {
										
											$username = get_option('wpsd_facebook_link');
										}
										
										if('klout' == $key) {
											
											$username = get_option('wpsd_twitter_un');
										}
										
										if('' != trim($username)) {
											
											$tag = $v[1];
											
											$url = str_replace('{username}' , $username, $v[2]);
											
											$link_title = __('Visit my', 'wpsd') . "{$v[0]}" . __('profile', 'wpsd');
											
											$img_src = WPSD_PLUGIN_URL . '/resources/images/icons/' . $tag . '.png';
											
											$link = "<a href=\"{$url}\" target=\"_blank\" rel=\"me\" title=\"{$link_title}\">{$v[0]}</a>";
											
											if($enabled_images) {
												
												$img = "<a href=\"{$url}\" target=\"_blank\" rel=\"me\" title=\"{$link_title}\"><img class=\"wpsd-profiles-icon\" src=\"{$img_src}\" alt=\"\" width=\"34\" height=\"34\"/></a>";
											}
											
											echo "<li class=\"wpsd-profiles-list-item\">{$img} {$link}</li>";
										}
									}
								}
							}
						}	
					}
				}
				
				if($enabled_attribute) {
					
					$link = "<a href=\"http://www.daveligthart.com/wp-stats-dashboard-10/\" target=\"_blank\" rel=\"external\" title=\"Powered by WP-Stats-Dashboard. Created by Dave Ligthart Freelance WordPress Developer.\">WPSD</a>";

					$img_src = WPSD_PLUGIN_URL . '/resources/images/icons/pow.png';

					if($enabled_images) {
												
						$img = "<a href=\"http://www.daveligthart.com/wp-stats-dashboard-10/\" target=\"_blank\" rel=\"external\" title=\"\"><img class=\"wpsd-profiles-icon\" src=\"{$img_src}\" alt=\"\" width=\"34\" height=\"34\"/></a>";
					}
					
					echo "<li class=\"wpsd-profiles-list-item\">{$img} {$link}</li>";
					
				}
			
			?>
			</ul>			
						
		</div>
		<!-- wp-stats-dashboard social profiles widget by http://daveligthart.com -->			
        <?php echo $after_widget; ?>   
<?php	
	}
}
add_action('widgets_init', create_function('', 'return register_widget("WPSDSocialProfilesWidget");'));
?>