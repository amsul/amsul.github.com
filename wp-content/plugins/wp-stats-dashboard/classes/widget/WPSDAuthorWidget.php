<?php
/**
 * WPSDAuthorWidget.
 * @author Dave Ligthart <info@daveligthart.com>
 * @version 0.1
 * @package wp-stats-dashboard
 */
class WPSDAuthorWidget extends WP_Widget {
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	function __construct() {
		
		$widget_ops = array('description' => __( 'Displays the top 5 list of your blog\'s most popular authors') ); 
		
		parent::__construct(false, __('WPSD - Top 5 Authors', 'wpsd'), $widget_ops);
	}
	
	/**
	 * WPSDAuthorWidget function.
	 * 
	 * @access public
	 * @return void
	 */
	function WPSDAuthorWidget() {
		
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
		
		global $wpdb; 
		
        $title = apply_filters('widget_title', $instance['title']);
        
        $desc = $instance['desc'];
        
        $results = $wpdb->get_results("
			SELECT COUNT(*) as c, post_author as user_id
			FROM {$wpdb->posts} WHERE post_status = 'publish'
			GROUP BY post_author
			ORDER BY c DESC
			LIMIT 5");
?>
        <?php echo $before_widget; ?>
       	<!-- wp-stats-dashboard widget by http://daveligthart.com -->
        <div id="wpsd-author-widget">
        <?php if ( $title ) { echo $before_title . $title . $after_title; } ?>
          	<?php if('' != trim($desc)): ?>             
          	<p><?php echo $desc; ?></p>
          	<?php endif; ?>
          	
          	<?php 
          	
          	if(null != $results && is_array($results) && count($results) > 0) {
          	
           		echo '<ul>';
           							
				$list = array();
				
				foreach($results as $r) {
				
					if(null == $r->user_id) continue;
				
					$metrics = new WPSDUserMetrics($r->user_id);
					
					$avatar = get_avatar($r->user_id, 32);
				
					$user_data =  get_userdata($r->user_id);
				
					$author = sprintf('%s %s', $avatar, $user_data->user_login);					
										
					$wpsd_score = $metrics->getWpsdScore();
					
					$list[$wpsd_score] = array('metrics' => $metrics, 'avatar' => $avatar, 'user_data' => $user_data, 'author' => $author);
				}			
				
				ksort($list, SORT_NUMERIC);
				
				$list = array_reverse($list);
				
				$i = 0;
				
				foreach($list as $item) {
								
					$metrics = $item['metrics'];
					
					$avatar = $item['avatar'];
				
					$user_data =  $item['user_data'];
				
					$author = $item['author'];					
					
					$rank = $i + 1;
																			
					?>
					
					<li> 
						<ul style="list-style:none;margin:0;padding:0;">	
							<li><span class="wpsd-author-avatar"><?php echo $avatar; ?></span></li>					
							<li>
							<span class="wpsd-rank">
								<strong>
								<span><?php _e('Rank', 'wpsd'); ?>:</span>
								<span class="value"><?php echo $rank; ?></span>
								</strong>
							</span> 
							</li>
							<li>						
							<span class="wpsd-score">
								<strong>
								<span><?php _e('WPSD Score', 'wpsd'); ?>:</span>
								<span class="value">
									<?php echo round($metrics->getWpsdScore(),2); ?>
								</span>
								</strong>
							</span>
							</li>
							<li>
							<span class="wpsd-post-count">
								<span><?php _e('Posts', 'wpsd'); ?>:</span>
								<span class="value"><?php echo $metrics->getPostCount(); ?></span>
							</span>
							</li>
							<li>
							<span class="wpsd-comments-received-count">
								<span><?php _e('Comments In', 'wpsd'); ?>:</span>
								<span class="value"><?php echo $metrics->getCommentsReceived(); ?></span>
							</span>	
							</li>
							<li>
							<span class="wpsd-comments-received-count">
								<span><?php _e('Comments Out', 'wpsd'); ?>:</span>
								<span class="value"><?php echo $metrics->getCommentsPlaced(); ?></span>
							</span>	
							</li>
							<li>
							<span class="wpsd-klout-score">
								<span><?php _e('Klout Score', 'wpsd'); ?>:</span>
								<span class="value"><?php echo $metrics->getKloutScore(); ?></span>
							</span>
							</li>
							<li>
							<span class="wpsd-post-count">
								<span><?php _e('Twitter Ratio', 'wpsd'); ?>:</span>
								<span class="value"><?php echo $metrics->getTwitterRatio(); ?></span>
							</span>
							</li>
						</ul>
					</li>
					
					<?php
									
					$i++;
				}
				
				echo '</ul>';
				
			}
			else {
		
				_e('No authors found with comments placed...', 'wpsd');
			}
          	          	?>
					
		</div>
		<!-- wp-stats-dashboard widget by http://daveligthart.com -->	
        <?php echo $after_widget; ?>
        
<?php
	}
}
add_action('widgets_init', create_function('', 'return register_widget("WPSDAuthorWidget");'));
?>