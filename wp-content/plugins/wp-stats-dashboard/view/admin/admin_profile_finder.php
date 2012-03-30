<?php
/**
 * Admin config template.
 * @author dligthart <info@daveligthart.com>
 * @version 0.6
 * @package wp-stats-dashboard
 */
?>
<div class="wrap">
  <h2><span style="display:none;">
    <?php _e('WP-Stats-Dashboard Profile Finder','wpsd');?>
    </span></h2>
  <img src="<?php echo WPSD_PLUGIN_URL; ?>/resources/images/logo-wpstatsdashboard-300x45.png" alt="wp-stats-dashboard" width="300" height="45" />

	<?php  if ($_POST) {
  	
  	echo '<div class="updated fade"><p><strong>'.__('Saved', 'wpsd').'</strong></p></div>'; 
  }?>

  <div id="poststuff" class="metabox-holder has-right-sidebar">
    <!--  Sidebar -->
    <div id="side-info-column" class="inner-sidebar">
      
      <?php include('blocks/sidebar.php'); ?>
      
    </div>
    <!--  end Sidebar -->
    <div id="post-body">
      <!-- #post-body-content -->
      <div id="post-body-content">
      
        <div class="stuffbox" style="background-color:#fff;">
          <h3>
            <?php _e('Profile Finder', 'wpsd'); ?>
          </h3>
          <div class="inside">
          
          <div style="padding:5px;">
	          <label for="profile-name"><?php _e('Enter regularly used profile name:', 'wpsd')?></label>
	          
	          <input id="profile-name" name="profile-name" type="text" size="32" />
	          
	          <input type="button" class="button-primary" onclick="javascript:wpsd_find_profile();" value="<?php _e('Find', 'wpsd'); ?>"/>
	           
	           <span id="wpsd_profile_loading" style="display:none; position:absolute; margin-top:2px; "><?php echo '<img src="'. WPSD_PLUGIN_URL .'/resources/images/ajax-loader.gif" alt="'.__('loading', 'wpsd').'" />'; ?></span>
           </div>
                
          </div>
          
        </div>
        
        <div class="stuffbox">
        	
        	 <h3>
           	 <?php _e('Select profiles to save.', 'wpsd'); ?>
         	 </h3>
        	
	        <div class="inside">
	        
	        	<p><?php _e('Profiles are saved to settings.', 'wpsd'); ?></p>
	        
	            <form method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post" accept-charset="utf-8">
	           
	           <div id="wpsd_profile_finder_content"></div><br/>
	           
	           <input type="submit" class="button-primary" id="wpsd_profile_btn_save" value="<?php _e('Save', 'wpsd'); ?>" style="display:none;" />
	          
	           </form>
	        
	        </div>
        
        </div>
      
    </div>
    <!-- /#post-body-content -->
  </div>
</div>