<?php  

		if(in_array('blacklisted', $enabled)) { 
		
			$mtype = 'green';
			
			$n = __('No', 'wpsd');
		
			//$ip = $_SERVER['SERVER_ADDR'];
	
				
			$ip = wpsd_get_ip();

			if(wpsd_is_blacklisted($ip)) {
				
				$mtype = 'red';
				
				$n = __('Yes', 'wpsd');
			}		
			
			echo '<li class="wpsd_toggle alternate"><span class="metric_label">';
			_e('Blacklisted:', 'wpsd');
			echo '</span>&nbsp;<span class="metric_'.$mtype.'">' . $n . '</span>';			
			
			echo '<ul class="wpsd_toggle_contents">';
		
			if($mtype == 'red') {
				
				printf('<li class="highlight">%s %s <span>%s</span></li>', 
					__('This means that you might have trouble delivering mail to some clients from your server ip address.', 'wpsd'),
					__('Your server ip is: ', 'wpsd'),
					$ip
				);

			} else {
				
				printf('<li class="highlight">%s %s <span>%s</span></li>', 
					__('Your server ip ', 'wpsd'),
					__('is not listed on a spam list.', 'wpsd'),
					$ip
				);
			}
			
			echo '</ul>';
			
			echo '</li>';
		}
?>