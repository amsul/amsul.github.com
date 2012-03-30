<style type="text/css">

.user-stats-item { 
float: left;
width: 100px;
background: white;
box-shadow: 0px 0px 5px #bcbcbc;
border-radius: 5px;
-moz-border-radius: 5px;
margin: 18px 18px 0 0;
}

.user-stats-item .header {
color: white;
font-size:0.9em;
letter-spacing: 1px;
text-transform: uppercase;
text-align: center;
background: #1388CA;
padding: 7px;
border-top-right-radius: 5px;
border-top-left-radius: 5px;
-moz-border-top-right-radius: 5px;
-moz-border-top-left-radius: 5px;
line-height: 12px;
margin:0;
height:35px;
}

.user-stats-item .value {
font-size: 1.7em;
color: #1388CA;
text-align: center;
padding:5px 0;
display:block;
}
</style>

		
	<h3><?php _e('WPSD - User Config', 'wpsd'); ?></h3>

	<table class="form-table">

		<tr>
			<th><label for="wpsd_user_klout"><?php _e('Klout', 'wpsd'); ?></label></th>

			<td>
				<input type="text" name="wpsd_user_klout" id="wpsd_user_klout" value="<?php 
				echo esc_attr( get_the_author_meta( 'wpsd_user_klout' , $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description"><?php _e('Please enter your Klout username. E.g Last part of the url: http://klout.com/<strong>daveligthart</strong>', 'wpsd'); ?></span>
			</td>
		</tr>
		
		<tr>
			<th><label for="wpsd_user_twitter"><?php _e('Twitter', 'wpsd'); ?></label></th>

			<td>
				<input type="text" name="wpsd_user_twitter" id="wpsd_user_twitter" value="<?php 
				echo esc_attr( get_the_author_meta( 'wpsd_user_twitter' , $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description"><?php _e('Please enter your Twitter username. E.g Last part of the url: http://twitter.com/<strong>daveligthart</strong>', 'wpsd'); ?></span>
			</td>
		</tr>

	</table>
	
	<h3><?php _e('WPSD - User Metrics', 'wpsd'); ?></h3>
	
	<ul>
		<li class="user-stats-item">
	    	<h3 class="header"><?php _e('Total Articles', 'wpsd'); ?></h3>
	        <span class="value"><?php echo $metrics->getPostCount(); ?></span>
	    </li>
		<li class="user-stats-item">
	    	<h3 class="header"><?php _e('Total Comments Received', 'wpsd'); ?></h3>
	        <span class="value"><?php echo $metrics->getCommentsReceived(); ?></span>
	    </li>
	    <li class="user-stats-item">
	    	<h3 class="header"><?php _e('Total Comments Placed', 'wpsd'); ?></h3>
	        <span class="value"><?php echo $metrics->getCommentsPlaced(); ?></span>
	    </li>
	     <li class="user-stats-item">
	    	<h3 class="header"><?php _e('Klout Score', 'wpsd'); ?></h3>
	        <span class="value"><?php echo $metrics->getKloutScore(); ?></span>
	    </li>
	    <li class="user-stats-item">
	    	<h3 class="header"><?php _e('Twitter F/F Ratio', 'wpsd'); ?></h3>
	        <span class="value"><?php echo $metrics->getTwitterRatio(); ?></span>
	    </li>
	</ul>
	
	<br style="clear:both;" />
	