<?php
/**
 * Admin export.
 *
 * @author dligthart <info@daveligthart.com>
 * @version 0.1
 * @package wp-stats-dashboard
 */
?>

<div class="wrap">
<h2><span style="display: none;">
  <?php _e('WP-Stats-Dashboard Export','wpsd');?>
  </span></h2>
<img
	src="<?php echo WPSD_PLUGIN_URL; ?>/resources/images/logo-wpstatsdashboard-300x45.png"
	alt="wp-stats-dashboard" width="300" height="45" />

<div id="poststuff" class="metabox-holder has-right-sidebar">
  <!--  Sidebar -->
  <div id="side-info-column" class="inner-sidebar">
    <?php include('blocks/sidebar.php'); ?>
  </div>
  <!--  end Sidebar -->
  <div id="post-body">
    <!-- #post-body-content -->
    <div id="post-body-content">
      <div class="stuffbox" style="background-color: #fff;">
        <h3>
          <?php _e('Export Social Trends Data'); ?>
        </h3>
        <div class="inside">
          <form method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post" accept-charset="utf-8">
          <ul>
            <li><h4><?php _e('Export to excel','wpsd')?></h4>
              <input type="submit" class="button-primary"
		id="wpsd_export_excel_btn" name="wpsd_export_excel_btn" value="<?php _e('Excel','wpsd'); ?>" />
            </li>       
            <?php /* if(version_compare(phpversion(), '5', '>=')) { ?>
             <li>
              <h4><?php _e('Export to Google Spreadsheet', 'wpsd')?></h4>
              <p><strong><?php _e('!!: You need to create a <a href="http://docs.google.com/" target="_blank" title="go to google docs">google spreadsheet</a> with columns: date, type and number.</strong>', 'wpsd'); ?></p>
           
              <label for="wpsd_google_un"><?php _e('Google username','wpsd')?></label><br/>
              <input type="text" name="wpsd_google_un" id="wpsd_google_un" /> <span style="font-size:x-small;"><?php _e('Enter your google username', 'wpsd'); ?></span><br/>
              <label for="wpsd_google_un"><?php _e('Google password','wpsd')?></label><br/>
              <input type="text" name="wpsd_google_pw" /> <span style="font-size:x-small;"><?php _e('Enter your google password', 'wpsd'); ?></span><br/>
              <label for="wpsd_google_un"><?php _e('Google Spreadsheet Name','wpsd')?></label><br/>
              <input type="text" name="wpsd_export_google_ss_name" /> <span style="font-size:x-small;"><?php _e('Enter the name of the spreadsheet here.', 'wpsd'); ?></span><br/>
              <br/>
              <input type="submit" class="button-primary"
		id="wpsd_export_google_btn" name="wpsd_export_google_btn" value="<?php _e('Google Spreadsheet', 'wpsd'); ?>" />
           	 
            </li>
            <?php } ?> */ ?>
          </ul>
        </div>
        </form>
      </div>
    </div>
    <!-- /#post-body-content -->
  </div>
</div>