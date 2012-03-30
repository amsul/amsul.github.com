<?php
/**
 * Admin config template.
 * @author dligthart <info@daveligthart.com>
 * @version 0.9
 * @package wp-stats-dashboard
 */
$cache_path = wpsd_get_cache_path();

$img_path = WPSD_PLUGIN_URL . '/resources/images/icons/';

$types = wpsd_get_metrics_types();

$checked = ' checked="checked"';

function wpsd_create_cb($id, $title, $form2) {
			
		if(!$id) return false;
		
		$key = 'wpsd_option_' . $id;

		if(isset($form2->$key)) $value = $form2->$key;
				
		$checked = ' checked="checked"';
?>

<style type="text/css">
.wrap input[type=text] {width:80%;}
.wrap input[type=password] {width:80%;}
</style>

<div class="ckb_column" style="width:200px; overflow:hidden; vertical-align:top; display:inline-block; margin-bottom:5px;">
  <input type="checkbox" id="<?php echo $id; ?>" name="wpsd_option_<?php echo $id; ?>"<?php if($value) echo $checked; ?>/>
  <?php echo $title; ?> </div>
<?php 		
}
?>

<div class="wrap">

  <h2><span style="display:none;">
    <?php _e('WP-Stats-Dashboard Options','wpsd');?>
    </span></h2>

  <img src="<?php echo WPSD_PLUGIN_URL; ?>/resources/images/logo-wpstatsdashboard-300x45.png" alt="wp-stats-dashboard" width="300" height="45" />

  <?php  if ( $_REQUEST['submit'] ) {
  	
  	if(!wpsd_login()) {
  		echo '<div class="error fade"><p><strong>'.__('WordPress.com Username or password is incorrect. Stats will not load. Please create an account at <a href="http://www.wordpress.com" target="_blank" title="wordpress.com">http://www.wordpress.com</a> afterwards use those credentials below.', 'wpsd').'</strong></p></div>'; 
  	}
  	
  	echo '<div class="updated fade"><p><strong>'.__('Saved', 'wpsd').'</strong></p></div>'; 
  }?>
  <?php if(!function_exists('curl_init')){ ?>
  <div class="error fade">
    <p> <strong>
      <?php _e('CURL extension not installed:','wpsd'); ?>
      </strong> &nbsp;
      <?php _e('you must have CURL extension enabled in your php configuration','wpsd');?>
      . </p>
  </div>
  <?php } ?>
  <?php if(!file_exists($cache_path)) { ?>
  <div class="error fade">
    <p> <strong>
      <?php _e('wp-content/cache does not exist:','wpsd');?>
      </strong> &nbsp;
      <?php _e('please make sure that the "wp-content/cache" directory is created','wpsd');?>
      . </p>
    <p>
      <?php _e('please use your ftp browser to navigate to the "wp-content" folder and create a directory named "cache".', 'wpsd'); ?>
    </p>
  </div>
  <?php } else { ?>
  <?php if(!is_writable($cache_path)) { ?>
  <div class="error fade">
    <p> <strong>
      <?php _e('wp-content/cache is not writable:','wpsd');?>
      </strong> &nbsp;
      <?php _e('please make sure that the "wp-content/cache" directory is writable by webserver.','wpsd');?>
      . </p>
    <p>
      <?php _e('please use your ftp client to navigate to the "wp-content" folder and change file permissions of the "cache" folder to (chmod) 777.', 'wpsd'); ?>
    </p>
  </div>
  <?php } } ?>
  <?php if('' == $form->getWpsdBlogId()) { ?>
  <div class="error fade">
    <p> <strong>
      <?php _e('please install the wordpress.com stats plugin and follow the instructions it presents:','wpsd');?>
      </strong> <br/>
      <a href="http://wordpress.org/extend/plugins/stats/" target="_blank">http://wordpress.org/extend/plugins/stats/</a> </p>
  </div>
  <?php } ?>
 
  <div id="poststuff" class="metabox-holder has-right-sidebar">
    <!--  Sidebar -->
    <div id="side-info-column" class="inner-sidebar">
      <div id="side-sortables" class="meta-box-sortables ui-sortable">
      
       <?php include('blocks/sidebar.php'); ?>
       
      </div>
    </div>
    <!--  end Sidebar -->
    <div id="post-body">
      
      <!-- #post-body-content -->
      <div id="post-body-content">
      
      <?php if(file_exists($cache_path) && is_writable($cache_path)) { ?>
        
      <form name="wpsd_config_form" method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post" accept-charset="utf-8">
      <?php echo $form->htmlFormId(); ?>
        
        <div class="stuffbox wpsd">
          <h3>
            <?php _e('Basic Settings', 'wpsd'); ?>
          </h3>
          <div class="inside">
          
            <table class="form-table" cellspacing="2" cellpadding="5" width="100%">
              <tr>
                <th scope="row"> <label for="wpsd_blog_id">
                    <?php _e('Blog ID', 'wpsd'); ?>
                    :</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_blog_id' ";
												echo "id='wpsd_blog_id' ";
												echo "value='".$form->getWpsdBlogId()."'" .
														"/>\n";
												?>
                  <?php 
                  	wpsd_jtip(
                  		__('Install the Jetpack plugin', 'wpsd'), 
                  		__('Make sure the Jetpack plugin is installed and configured. Your blog-id will be automatically filled in by this plugin.','wpsd')); ?></td>
              </tr>
              <tr>
                <th scope="row"> <label for="wpsd_un">
                    <?php _e('Username', 'wpsd'); ?>
                    :</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_un' ";
												echo "id='wpsd_un' ";
												echo "value='".$form->getWpsdUn()."'" .
														"/>\n";
												?>
                  <?php 
                  	wpsd_jtip( 
                  		__('WordPress.com Username', 'wpsd'),  
                  		__('Enter your WordPress.com username', 'wpsd')); ?></td>
              </tr>
              <tr>
                <th scope="row"> <label for="wpsd_pw">
                    <?php _e('Password', 'wpsd'); ?>
                    :</label>
                </th>
                <td><?php
												echo "<input type='password' size='60' ";
												echo "name='wpsd_pw' ";
												echo "id='wpsd_pw' ";
												echo "value='".$form->getWpsdPw()."'" .
														"/>\n";
												?>
                  <?php wpsd_jtip(__('WordPress.com Password', 'wpsd'), __('Enter your WordPress.com password', 'wpsd')); ?></td>
              </tr>
              <tr>
                <th scope="row" valign="top"> <label>
                    <?php _e('Enabled / Disable <br/> Dashboard Widgets','wpsd'); ?>
                    :</label>
                </th>
                <td>
                
                  <br/>
                  
                  <input type="checkbox" name="wpsd_widget_overview"<?php echo $form->getWpsdWidgetOverview() ? $checked : ''; ?> />
                  <?php _e('Stats - Overview', 'wpsd'); ?>
                  <br/>
                  
                  <input type="checkbox" name="wpsd_widget_postviews"<?php echo $form->getWpsdWidgetPostViews() ? $checked : ''; ?> />
                  <?php _e('Stats - Post views', 'wpsd'); ?>
                  <br/>
                  
                  <input type="checkbox" name="wpsd_widget_referrers"<?php echo $form->getWpsdWidgetReferrers() ? $checked : ''; ?> />
                  <?php _e('Stats - Referrers', 'wpsd'); ?>
                  <br/>
                  
                  <input type="checkbox" name="wpsd_widget_clicks"<?php echo $form->getWpsdWidgetClicks() ? $checked : ''; ?> />
                  <?php _e('Stats - Clicks', 'wpsd'); ?>
                  <br/>
                  
                  <input type="checkbox" name="wpsd_widget_searchterms"<?php echo $form->getWpsdWidgetSearchTerms() ? $checked : ''; ?> />
                  <?php _e('Stats - Search terms', 'wpsd'); ?>
                  <br/>
                  
                  <input type="checkbox" name="wpsd_widget_trends"<?php echo $form->getWpsdWidgetTrends() ? $checked : ''; ?> />
                  <?php _e('Stats - Trends', 'wpsd'); ?>
                  <br/>
                  
                  <input type="checkbox" name="wpsd_widget_compete"<?php echo $form->getWpsdWidgetCompete() ? $checked : ''; ?> />
                  <?php _e('Stats - Compete.com', 'wpsd'); ?>
                  <br/>
                 
                  <?php 
                  /*<input type="checkbox" name="wpsd_widget_blogpulse"<?php echo $form->getWpsdWidgetBlogPulse() ? $checked : ''; ?> />
                  <?php _e('Stats - BlogPulse.com', 'wpsd'); ?>
                  <br/>*/ 
                  ?>
                  
                  <input type="checkbox" name="wpsd_widget_authors"<?php echo $form->getWpsdWidgetAuthors() ? $checked : ''; ?> />
                  <?php _e('Stats - Top 5 Authors', 'wpsd'); ?>
                  <br/>

                  </td>
              </tr>
              <tr>
                <th scope="row"> <label>
                    <?php _e('Default Trend Graph','wpsd'); ?>:</label>
                </th>
                <td><?php $wpsd_trends_type = $form->getWpsdTrendsType(); ?>
 
                  <?php include('blocks/select-trend.php'); ?>
 
                  <?php wpsd_jtip(__('Default trends graph', 'wpsd'), __('This graph will be displayed by default in the trends widget.','wpsd')); ?></td>
              </tr>
              
              <tr>
              	<th scope="row"> <label>
                    <?php _e('Disable widgets on main dashboard page','wpsd'); ?>
                    :</label>
                </th>
              	<td>		
              		<input type="checkbox" name="wpsd_disable_widgets"<?php echo $form->getWpsdDisableWidgets() ? $checked : ''; ?> />
                  <?php wpsd_jtip(__('Stats dashboard page', 'wpsd'), __('The stats widgets will only be visible on the stats dashboard page.', 'wpsd')); ?>
                  <br/>
              	</td>
              </tr>
             
              <tr>
              	<th scope="row"></th>
              	<td>		             	
              	</td>
              </tr>

  
              </table>
              
                <input type="submit" name="submit" value="<?php _e('Save Changes', 'wpsd'); ?>" class="button-primary" />
       
            </div>
         </div> 

         <!-- Visibility -->
         <div class="stuffbox wpsd">
         	<h3><?php _e('Visibility', 'wpsd'); ?></h3>
         	 <div class="inside">
         	 	<table class="form-table" cellspacing="2" cellpadding="5" width="100%" background="">
         			<tr valign="top"><th scope="row"><?php _e('Select the roles that will be able to view stats.', 'wpsd'); ?></th>
						<td>
							<br>
							<label><input type="checkbox" disabled="disabled" name="wpsd_role_administrator" checked="checked"> <?php _e('Administrator', 'wpsd'); ?></label><br>
								<label><input type="checkbox" name="wpsd_role_editor"<?php echo $form->getWpsdRoleEditor() ? $checked : ''; ?>> <?php _e('Editor', 'wpsd'); ?></label><br>
								<label><input type="checkbox" name="wpsd_role_author"<?php echo $form->getWpsdRoleAuthor() ? $checked : ''; ?>> <?php _e('Author', 'wpsd'); ?></label><br>
								<label><input type="checkbox" name="wpsd_role_contributor"<?php echo $form->getWpsdRoleContributor() ? $checked : ''; ?>> <?php _e('Contributor', 'wpsd'); ?></label><br>
								<label><input type="checkbox" name="wpsd_role_subscriber"<?php echo $form->getWpsdRoleSubscriber() ? $checked : ''; ?>> <?php _e('Subscriber', 'wpsd'); ?></label><br>
						</td>
					</tr>
				</table>
				 <input type="submit" name="submit" value="<?php _e('Save Changes', 'wpsd'); ?>" class="button-primary" />
			</div>
         </div>
         
         <!-- Autopost -->
         <div class="stuffbox wpsd">
         	 <h3><?php _e('Autopost settings', 'wpsd'); ?></h3>
         	 <div class="inside">
         	 <table class="form-table" cellspacing="2" cellpadding="5" width="100%" background="">
         	  
         	  <tr class="highlight">
                <th scope="row"><img src="<?php echo $img_path; ?><?php echo $types['amplify'][1]; ?>.png" alt="" /> <label for="wpsd_amplify_un">
                    <?php _e('Amplify username','wpsd'); ?>
                    :</label>
                </th>
                <td><?php
						echo "<input type='text' size='60' ";
						echo "name='wpsd_amplify_un' ";
						echo "id='wpsd_amplify_un' ";
						echo "value='".$form->getWpsdAmplifyUn()."'" ."/>\n";
					
					wpsd_jtip(
						__('Amplify', 'wpsd'),
						__('Amplify username. e.g: 
							http://<strong>username</strong>.amplify.com.','wpsd'));
							
					wpsd_go('http://amplify.com/', __('Amplify', 'wpsd'));
				?> 
                </td>
              </tr>
              
               <tr class="highlight">
                <th scope="row"><img src="<?php echo $img_path; ?><?php echo $types['amplify'][1]; ?>.png" alt="" /> <label for="wpsd_amplify_autopost_email">
                    <?php _e('Amplify Post Email','wpsd'); ?>
                    :</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_amplify_autopost_email' ";
												echo "id='wpsd_amplify_autopost_email' ";
												echo "value='".$form->getWpsdAmplifyAutoPostEmail()."'" .
														"/>\n";
												?>
                
                  <?php 
                  
                  	wpsd_jtip(
                  	__('Amplify Post by Email', 'wpsd'), 
                  	__('Amplify Post by Email. Spread your content across twitter, facebook, posterous etc. You can find it under Settings / Post by Email. 
                  		E.g abcdef@yourlog.amplify.com . The post excerpt is used for the autopost content. When writing a post please manually add an excerpt.','wpsd')); 
                  	
                  	wpsd_go('http://amplify.com/wp-admin/admin.php?page=clogs-easy-admin.php&eaoption=aep', __('Amplify Post by Email Settings Page', 'wpsd'));
                  
                  ?> 
                  </td>
              </tr>

         	 <tr>
                <th scope="row"><img src="<?php echo $img_path; ?><?php echo $types['posterous'][1]; ?>.png" alt="" /> <label for="wpsd_posterous_un">
                    <?php _e('Posterous username','wpsd'); ?>
                    :</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_posterous_un' ";
												echo "id='wpsd_posterous_un' ";
												echo "value='".$form->getWpsdPosterousUn()."'" .
														"/>\n";
												?>
                  
                  <?php 
                  
                  wpsd_jtip(
                  	__('Posterous username', 'wpsd'), 
                  	__('Posterous username. e.g: http://<strong>username</strong>.posterous.com. Registration required.','wpsd')); 
                  	
                  wpsd_go('http://www.posterous.com/', __('Posterous', 'wpsd'));	
                  	
                  ?></td>
              </tr>
              
               <tr>
                <th scope="row"><img src="<?php echo $img_path; ?><?php echo $types['posterous'][1]; ?>.png" alt="" /> <label for="wpsd_posterous_autopost_email">
                    <?php _e('Posterous Post Email','wpsd'); ?>
                    :</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_posterous_autopost_email' ";
												echo "id='wpsd_posterous_autopost_email' ";
												echo "value='".$form->getWpsdPosterousAutoPostEmail()."'" .
														"/>\n";
												?>
                 
                  <?php 
                  	wpsd_jtip(
                  		__('Posterous Post By Email.', 'wpsd'), 
                  		__(' Make sure to add', 'wpsd') .  ' ' . get_bloginfo('admin_email') . ' ' . 
                  		__(' to your posterous contact settings to allow posting. 
                  		Spread your content across twitter, facebook, posterous.
                  		E.g abcdef@posterous.com. 
                  		The post excerpt is used for the autopost content. 
                  		When writing a post please manually add an excerpt.','wpsd')); 
                  	
                  	wpsd_go('http://posterous.com/manage#settings/contact', __('Posterous contact settings'));		
                  ?> 
                  </td>
                </tr>
                
				</table>
				<br/>
				<input type="submit" name="submit-autopost" value="<?php _e('Save Changes', 'wpsd'); ?>" class="button-primary" />
			</div>
         </div>
         <!-- More -->
         <div class="stuffbox wpsd">
          <h3>
            <?php _e('Profile Settings', 'wpsd'); ?>
          </h3>
         
          <div class="inside">
          
          <p style="font-weight:bold;"><?php _e('Use <a href="?page=wpsd_profile_finder">the profile finder</a> to quickly retrieve and save your profiles.', 'wpsd'); ?></p>
         
          <br/>
         
          <table class="form-table" cellspacing="2" cellpadding="5" width="100%" background="">
              
              <tbody>
              <!-- Twitter -->
              <tr class="highlight">
                <th scope="row"><img src="<?php echo $img_path; ?><?php echo $types['twitter'][1]; ?>.png" alt="" /> <label for="wpsd_twitter_un">
                    <?php _e('Twitter username','wpsd'); ?>
                    :</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_twitter_un' ";
												echo "id='wpsd_twitter_un' ";
												echo "value='".$form->getWpsdTwitterUn()."'" .
														"/>\n";
												?>
                
                  <?php 
                  
                  wpsd_jtip(
                  	__('Twitter username', 'wpsd'), 
                  	__('Twitter username for twitter socials metrics in the overview widget. Registration required.','wpsd')); 
                  	
                  wpsd_go('http://twitter.com/', __('Twitter', 'wpsd'));
                  	
                  ?></td>
              </tr>
              
              <!-- LinkedIn -->
              <tr>
                <th scope="row"><img src="<?php echo $img_path; ?><?php echo $types['linkedin'][1]; ?>.png" alt="" /> <label for="wpsd_linkedin_un">
                    <?php _e('LinkedIn username','wpsd'); ?>
                    :</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_linkedin_un' ";
												echo "id='wpsd_linkedin_un' ";
												echo "value='".$form->getWpsdLinkedInUn()."'" .
														"/>\n";
												?>
                
                  <?php 
                  	wpsd_jtip(
                  		__('LinkedIn username', 'wpsd'), 
                  		__('LinkedIn username for connection count in the overview widget. Registration required.','wpsd')); 
                  	
                  	wpsd_go('http://www.linkedin.com/', __('LinkedIn', 'wpsd'));
                  	
                  	?></td>
              </tr>
              
              <tr>
                <th scope="row"><img src="<?php echo $img_path; ?><?php echo $types['linkedin'][1]; ?>.png" alt="" /> <label for="wpsd_linkedincompany_un">
                    <?php _e('LinkedIn Company username','wpsd'); ?>
                    :</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_linkedincompany_un' ";
												echo "id='wpsd_linkedincompany_un' ";
												echo "value='".$form->getWpsdLinkedInCompanyUn()."'" .
														"/>\n";
												?>
                
                  <?php 
                  
                  wpsd_jtip(
                  	__('LinkedIn company username', 'wpsd'), 
                  	__('Company username. e.g: http://www.linkedin.com/company/<strong>company-name</strong>. Registration required.','wpsd')); 
                  	
                  wpsd_go('http://www.linkedin.com/company/', __('LinkedIn Company', 'wspd'));	
                  		
                  ?></td>
              </tr>
              
              <!-- Feedburner -->
              <tr class="highlight">
                <th scope="row"><img src="<?php echo $img_path; ?><?php echo $types['feedburner'][1]; ?>.png" alt="" /> <label for="wpsd_feedburner_uri">
                    <?php _e('FeedBurner','wpsd'); ?>
                    :</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_feedburner_uri' ";
												echo "id='wpsd_feedburner_uri' ";
												echo "value='".$form->getWpsdFeedburnerUri()."'" .
														"/>\n";
												?>
                 
                  <?php 
                  
                  wpsd_jtip(
                  	__('Feedburner url', 'wpsd'), 
                  	__('Feedburner url e.g: <strong>http://feeds.feedburner.com/username</strong>. Registration required.','wpsd')); 
                  
                  wpsd_go('http://www.feedburner.com/', 'Feedburner');
                  
                  ?></td>
              </tr>
              
              <!-- Bit.ly -->
              <tr>
                <th scope="row"><img src="<?php echo $img_path; ?><?php echo $types['bitly'][1]; ?>.png" alt="" /> <label for="wpsd_bitly_un">
                    <?php _e('Bit.ly username','wpsd'); ?>
                    :</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_bitly_un' ";
												echo "id='wpsd_bitly_un' ";
												echo "value='".$form->getWpsdBitlyUn()."'" .
														"/>\n";
					?> 
                  <?php 
                  
                  	wpsd_jtip(
                  		__('Bit.ly', 'wpsd'), 
                 		__('Bit.ly username for click stats. API Key required. Case sensitive. Registration required.', 'wpsd')); 
                 
                 	wpsd_go('http://bit.ly/', __('Bit.ly', 'wpsd'));
                 		
                  ?></td>
              </tr>
              
          
              <tr class="highlight">
                <th scope="row"><img src="<?php echo $img_path; ?><?php echo $types['bitly'][1]; ?>.png" alt="" /> <label for="wpsd_bitly_key">
                    <?php _e('Bit.ly api key','wpsd'); ?>
                    :</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_bitly_key' ";
												echo "id='wpsd_bitly_key' ";
												echo "value='".$form->getWpsdBitlyKey()."'" .
														"/>\n";
												?>
          
                  <?php
                   
                  	wpsd_jtip(
                  		__('Bit.ly api key', 'wpsd'), 
                  		__('Bit.ly api key for click stats. Case sensitive.','wpsd')); 
                  
                  	wpsd_go('http://bit.ly/a/your_api_key', __('Bit.ly api key', 'wpsd'));
                  		
                  ?></td>
              </tr>
              
              <!-- Last.fm -->
              <tr>
                <th scope="row"><img src="<?php echo $img_path; ?><?php echo $types['lastfm'][1]; ?>.png" alt="" /> <label for="wpsd_lastfm_un">
                    <?php _e('Last.fm username','wpsd'); ?>
                    :</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_lastfm_un' ";
												echo "id='wpsd_lastfm_un' ";
												echo "value='".$form->getWpsdLastFmUn()."'" .
														"/>\n";
												?>

                  <?php 
                  
                 	 wpsd_jtip(
                  		__('Last.fm', 'wpsd'), 
                  		__('Last.fm username for friend stats. Registration required.','wpsd')); 
                  	
                  	 wpsd_go('http://www.last.fm/', __('Last.fm', 'wpsd'));
                  	
                  	?>
                  	</td>
              </tr>
              
              <!-- Facebook -->
              
              <tr class="highlight">
                <th scope="row"><img src="<?php echo $img_path; ?><?php echo $types['facebook'][1]; ?>.png" alt="" /> <label for="wpsd_facebook_un">
                    <?php _e('Facebook fan page id','wpsd'); ?>
                    :</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_facebook_un' ";
												echo "id='wpsd_facebook_un' ";
												echo "value='".$form->getWpsdFacebookUn()."'" .
														"/>\n";
												?>
 
                  <?php 
                  	wpsd_jtip(
                  		__('Facebook', 'wpsd'), 
                  		__('Facebook page ID. (note: NOT the full url, only the id) e.g:<br/>http://www.facebook.com/pages/wp-stats-dashboard/<strong>123487341019112</strong>. Registration required.','wpsd')); 
                  		
                  	wpsd_go('http://www.facebook.com/', __('Facebook', 'wpsd')); 
                  		
                  ?>
                  </td>
              </tr>
              
              <!-- Flickr -->
              <tr>
                <th scope="row"><img src="<?php echo $img_path; ?><?php echo $types['flickr'][1]; ?>.png" alt="Flickr logo" /> 
                <label for="wpsd_flickr_uri"><?php _e('Flickr username','wpsd'); ?>:</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_flickr_un' ";
												echo "id='wpsd_flickr_un' ";
												echo "value='".$form->getWpsdFlickrUsername()."'" .
														"/>\n";
												?>
                  <?php 
                  
                  	wpsd_jtip(
                  		__('Flickr', 'wpsd'), 
                  		__('Flickr username e.g: http://www.flickr.com/photos/<strong>username</strong>. Registration required.','wpsd')); 
                  
                  	wpsd_go('http://www.flickr.com/', __('Flickr', 'wpsd')); 
                  			
                  ?>
                  </td>
              </tr>
              
              <!-- Diigo -->
              <tr class="highlight">
                <th scope="row"><img src="<?php echo $img_path; ?><?php echo $types['diigo'][1]; ?>.png" alt="" /> <label for="wpsd_diigo_un">
                    <?php _e('Diigo profile name','wpsd'); ?>
                    :</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_diigo_un' ";
												echo "id='wpsd_diigo_un' ";
												echo "value='".$form->getWpsdDiigoUn()."'" .
														"/>\n";
												?>
                  <?php 
                  
                  	wpsd_jtip(
                  		__('Diigo', 'wpsd'), 
                  		__('Diigo profile name. e.g: http://www.diigo.com/profile/<strong>username</strong>. Registration required.','wpsd')); 
                  	
                  	wpsd_go('http://www.diigo.com/', __('Diigo', 'wpsd'));
                  	
                  	?>
                  	</td>
              </tr>
              
              <!-- Brazen -->
              <tr>
                <th scope="row"><img src="<?php echo $img_path; ?><?php echo $types['brazencareerist'][1]; ?>.png" alt="" /> <label for="wpsd_brazencareerist_un">
                    <?php _e('Brazen Careerist','wpsd'); ?>
                    :</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_brazencareerist_un' ";
												echo "id='wpsd_brazencareerist_un' ";
												echo "value='".$form->getWpsdBrazenCareeristUn()."'" .
														"/>\n";
												?>
                  <?php 
                  	wpsd_jtip(
                  		__('Brazen Careerist', 'wpsd'), 
                  		__('Brazen careerist profile name. e.g: http://www.brazencareerist.com/profile/<strong>username</strong>. Registration required.','wpsd')); 
                  		
                  	
                  	wpsd_go('http://www.brazencareerist.com/', __('Brazen Careerist', 'wpsd')); 		
                  ?>
                  </td>
              </tr>
              
              <!-- Newsvine -->
              <tr class="highlight">
                <th scope="row"><img src="<?php echo $img_path; ?><?php echo $types['newsvine'][1]; ?>.png" alt="" /> <label for="wpsd_newsvine_un">
                    <?php _e('Newsvine profile name','wpsd'); ?>
                    :</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_newsvine_un' ";
												echo "id='wpsd_newsvine_un' ";
												echo "value='".$form->getWpsdNewsVineUn()."'" .
														"/>\n";
												?>
                  <?php 
                  	wpsd_jtip(
                  		__('Newsvine', 'wpsd'), 
                  		__('Newsvine profile name. e.g: http://<strong>username</strong>.newsvine.com/. Registration required.','wpsd')); 
                  	
                  	wpsd_go('http://www.newsvine.com/', __('Newsvine', 'wpsd'));		
                  ?>
                  </td>
              </tr>
              
              <!-- Youtube -->
              <tr>
                <th scope="row"><img src="<?php echo $img_path; ?><?php echo $types['youtube'][1]; ?>.png" alt="" /> <label for="wpsd_youtube_un">
                    <?php _e('Youtube username','wpsd'); ?>
                    :</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_youtube_un' ";
												echo "id='wpsd_youtube_un' ";
												echo "value='".$form->getWpsdYoutubeUn()."'" .
														"/>\n";
												?>

                  <?php 
                  	wpsd_jtip(
                  		__('Youtube', 'wpsd'), 
                  		__('Youtube username. e.g: http://www.youtube.com/<strong>username</strong>. Registration required.','wpsd')); 
                  	
                  	wpsd_go('http://www.youtube.com/', __('Youtube', 'wpsd'));
                  	
                  	?></td>
              </tr>
              
              <!-- Myspace -->
              <tr class="highlight">
                <th scope="row"><img src="<?php echo $img_path; ?><?php echo $types['myspace'][1]; ?>.png" alt="" /> <label for="wpsd_myspace_un">
                    <?php _e('Myspace username','wpsd'); ?>
                    :</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_myspace_un' ";
												echo "id='wpsd_myspace_un' ";
												echo "value='".$form->getWpsdMyspaceUn()."'" .
														"/>\n";
												?>
                  
                  <?php 
                  	wpsd_jtip(
                  		__('Myspace', 'wpsd'), 
                  		__('Myspace username. e.g: http://www.myspace.com/<strong>username</strong>. Registration required.','wpsd')); 
                  
                  	wpsd_go('http://www.myspace.com/', __('Myspace', 'wpsd')); 		
                  ?>
                  </td>
              </tr>
              
              <!-- Plancast -->
              <tr>
                <th scope="row"><img src="<?php echo $img_path; ?><?php echo $types['plancast'][1]; ?>.png" alt="" /> <label for="wpsd_plancast_un">
                    <?php _e('Plancast username','wpsd'); ?>
                    :</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_plancast_un' ";
												echo "id='wpsd_plancast_un' ";
												echo "value='".$form->getWpsdPlancastUn()."'" .
														"/>\n";
												?>
                 
                  <?php 
                  	wpsd_jtip(
                  		__('Plancast', 'wpsd'),
                  		__('Plancast username. e.g: http://plancast.com/<strong>username</strong>. Registration required.','wpsd')); 
                  	
                  	wpsd_go('http://www.plancast.com/', __('Plancast', 'wpsd')); 	
                  ?>		
                 </td>
              </tr>      
               
               <!-- Lazyfeed -->   
               <tr class="highlight">
                <th scope="row"><img src="<?php echo $img_path; ?><?php echo $types['lazyfeed'][1]; ?>.png" alt="" /> <label for="wpsd_lazyfeed_un">
                    <?php _e('Lazyfeed username','wpsd'); ?>
                    :</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_lazyfeed_un' ";
												echo "id='wpsd_lazyfeed_un' ";
												echo "value='".$form->getWpsdLazyfeedUn()."'" .
														"/>\n";
												?>
                  <?php 
                  
                  	wpsd_jtip(
                  		__('Lazyfeed', 'wpsd'),
                  		__('lazyfeed username. e.g: http://www.lazyfeed.com/user/<strong>username</strong>. Registration required.','wpsd')); 
                  	
                  	wpsd_go('http://www.lazyfeed.com/', __('Lazyfeed', 'wpsd'));
                  		
                  	?>
                  </td>
              </tr>
              
              <!-- Sphinn -->
              <tr>
                <th scope="row"><img src="<?php echo $img_path; ?><?php echo $types['sphinn'][1]; ?>.png" alt="" /> <label for="wpsd_sphinn_un">
                    <?php _e('Sphinn username','wpsd'); ?>
                    :</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_sphinn_un' ";
												echo "id='wpsd_sphinn_un' ";
												echo "value='".$form->getWpsdSphinnUn()."'" .
														"/>\n";
												?>
                  <?php 
                  
                  	wpsd_jtip(
                  		__('Sphinn', 'wpsd'),
                  		__('sphinn username. e.g: http://sphinn.com/user/<strong>username</strong>. Registration required.','wpsd')); 
                  	
                  	wpsd_go('http://www.sphinn.com/', __('Sphinn', 'wpsd')); 
                  		
                  	?>
                  </td>
              </tr>
              
              <!-- Jaiku -->
              <tr class="highlight">
                <th scope="row"><img src="<?php echo $img_path; ?><?php echo $types['jaiku'][1]; ?>.png" alt="" /> <label for="wpsd_jaiku_un">
                    <?php _e('Jaiku username','wpsd'); ?>
                    :</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_jaiku_un' ";
												echo "id='wpsd_jaiku_un' ";
												echo "value='".$form->getWpsdJaikuUn()."'" .
														"/>\n";
												?>
                  <?php 
                  	wpsd_jtip(
                  		__('Jaiku', 'wpsd'),
                  		__('Jaiku username. e.g: http://<strong>username</strong>.jaiku.com. Registration required.','wpsd')); 
                  
                  	wpsd_go('http://www.jaiku.com/', __('Jaiku', 'wpsd'));		
                  ?>
                  </td>
              </tr>
              
              <!-- Plurk -->            
              <tr>
                <th scope="row"><img src="<?php echo $img_path; ?><?php echo $types['plurk'][1]; ?>.png" alt="" /> <label for="wpsd_plurk_un">
                    <?php _e('Plurk username','wpsd'); ?>
                    :</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_plurk_un' ";
												echo "id='wpsd_plurk_un' ";
												echo "value='".$form->getWpsdPlurkUn()."'" .
														"/>\n";
												?>
                  <?php 
                  	wpsd_jtip(
                  		__('Plurk', 'wpsd'),
                  		__('Plurk username. e.g: http://www.plurk.com/<strong>username</strong>. Registration required.','wpsd')); 
                  
                  	wpsd_go('http://www.plurk.com/', __('Plurk', 'wpsd'));
                  		
                  ?></td>
              </tr>
              
              <!-- Hyves -->
               <tr class="highlight">
                <th scope="row"><img src="<?php echo $img_path; ?><?php echo $types['hyves'][1]; ?>.png" alt="" /> <label for="wpsd_hyves_un">
                    <?php _e('Hyves username','wpsd'); ?>
                    :</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_hyves_un' ";
												echo "id='wpsd_hyves_un' ";
												echo "value='".$form->getWpsdHyvesUn()."'" .
														"/>\n";
												?>

                  <?php 
                  	wpsd_jtip(
                  		__('Hyves', 'wpsd'),
                 		__('Hyves username. e.g: http://<strong>username</strong>.hyves.nl. Registration required.','wpsd')); 
                 	
                 	wpsd_go('http://www.hyves.nl/', __('Hyves', 'wpsd'));          	
                 ?>
                 </td>
              </tr>
              
              <?php /*<tr class="highlight">
                <th scope="row"><img src="<?php echo $img_path; ?><?php echo $types['xbox'][1]; ?>.png" alt="" /> <label for="wpsd_xbox_un">
                    <?php _e('Xbox username','wpsd'); ?>
                    :</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_xbox_un' ";
												echo "id='wpsd_xbox_un' ";
												echo "value='".$form->getWpsdXboxUn()."'" .
														"/>\n";
												?>
                  <br/>
                  <?php _e('Xbox live gamer tag. <a href="http://live.xbox.com/" target="_blank" title="xbox live">xbox live</a>.','wpsd'); ?> <?php _e('Registration required.', 'wpsd'); ?></td>
              </tr> */ ?>
              
              <!-- Foursquare -->
              <tr>
                <th scope="row"><img src="<?php echo $img_path; ?><?php echo $types['foursquare'][1]; ?>.png" alt="" /> <label for="wpsd_foursquare_un">
                    <?php _e('Foursquare username','wpsd'); ?>
                    :</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_foursquare_un' ";
												echo "id='wpsd_foursquare_un' ";
												echo "value='".$form->getWpsdFoursquareUn()."'" .
														"/>\n";
												?>
          
                  <?php 
                  	wpsd_jtip(
                  		__('Foursquare', 'wpsd'), 
                  		__('Foursquare username. Registration required.','wpsd')); 
                  	
                  	wpsd_go('http://foursquare.com/', __('Foursquare', 'wpsd'));	
                  ?>
                  </td>
              </tr>
              
              <!-- Disqus -->
              <tr class="highlight">
                <th scope="row"><img src="<?php echo $img_path; ?><?php echo $types['disqus'][1]; ?>.png" alt="" /> <label for="wpsd_disqus_un">
                    <?php _e('Disqus username','wpsd'); ?>
                    :</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_disqus_un' ";
												echo "id='wpsd_disqus_un' ";
												echo "value='".$form->getWpsdDisqusUn()."'" .
														"/>\n";
												?>
                  <?php 
                  	wpsd_jtip(
                  		__('Disqus', 'wpsd'),
                  		__('Disqus username. Registration required.','wpsd')); 
                  	
                  	wpsd_go('http://disqus.com/', __('Disqus', 'wpsd'));
                  	
                  ?>
                  </td>
              </tr>
              
               <!-- Blippr -->
               <tr>
                <th scope="row"><img src="<?php echo $img_path; ?><?php echo $types['blippr'][1]; ?>.png" alt="" /> <label for="wpsd_blippr_un">
                    <?php _e('Blippr username','wpsd'); ?>
                    :</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_blippr_un' ";
												echo "id='wpsd_blippr_un' ";
												echo "value='".$form->getWpsdBlipprUn()."'" .
														"/>\n";
												?>
                  <?php 
                  	wpsd_jtip(
                  		__('Blippr', 'wpsd'), 
                  		__('blippr username. Registration required.','wpsd')); 
                  	
                  	wpsd_go('http://blippr.com/', __('Blippr', 'wpsd')); 		
                  ?>
                  </td>
              </tr>
              
              <!-- Runkeeper -->             
              <tr class="highlight">
                <th scope="row"><img src="<?php echo $img_path; ?><?php echo $types['runkeeper'][1]; ?>.png" alt="" /> <label for="wpsd_runkeeper_un">
                    <?php _e('Runkeeper username','wpsd'); ?>
                    :</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_runkeeper_un' ";
												echo "id='wpsd_runkeeper_un' ";
												echo "value='".$form->getWpsdRunkeeperUn()."'" .
														"/>\n";
												?>
    
                  <?php 
                  
                  	wpsd_jtip(
                  		__('Runkeeper', 'wpsd'), 
                  		__('Runkeeper username. e.g: http://runkeeper.com/user/<strong>daveligthart</strong>/profile. Registration required.','wpsd')); 
                  	
                  	wpsd_go('http://runkeeper.com/', __('Runkeeper', 'wpsd'));
                  	
                  	
                  	?>
                  	</td>
              </tr>
              
              <!-- Blippy -->
              <tr>
                <th scope="row"><img src="<?php echo $img_path; ?><?php echo $types['blippy'][1]; ?>.png" alt="" /> <label for="wpsd_blippy_un">
                    <?php _e('Blippy username','wpsd'); ?>
                    :</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_blippy_un' ";
												echo "id='wpsd_blippy_un' ";
												echo "value='".$form->getWpsdBlippyUn()."'" .
														"/>\n";
												?>
            
                  <?php 
                  	wpsd_jtip(
                  		__('Blippy', 'wpsd'), 
                  		__('Blippy username. e.g: http://blippy.com/<strong>daveligthart</strong>. Registration required.','wpsd')); 
                  	
                  	wpsd_go('http://blippy.com/', __('Blippy', 'wpsd'));
                  			
                  ?>
                  </td>
              </tr>
              
              <!-- Weread -->
              <tr class="highlight">
                <th scope="row"><img src="<?php echo $img_path; ?>weread.png" alt="" /> <label for="wpsd_weread_un">
                    <?php _e('WeRead username/id','wpsd'); ?>
                    :</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_weread_id' ";
												echo "id='wpsd_weread_id' ";
												echo "value='".$form->getWpsdWeReadId()."'" .
														"/>\n";
												?>
         
                  <?php 
                  	wpsd_jtip(
                  		__('Weread', 'wpsd'), 
                  		__('Weread username/id. e.g: http://weread.com/profile/<strong>Dave/1052583991</strong>/. Registration required.','wpsd')); 
                  	
                  	wpsd_go('http://weread.com/', __('Weread', 'wpsd'));		
                  ?>
                  </td>
              </tr>
              
              <!-- Empire Avenue -->
               <tr>
                <th scope="row"><img src="<?php echo $img_path; ?><?php echo $types['eave'][1]; ?>.png" alt="" /> <label for="wpsd_eave_un">
                    <?php _e('Empire Avenue ticker name','wpsd'); ?>
                    :</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_eave_un' ";
												echo "id='wpsd_eave_un' ";
												echo "value='".$form->getWpsdEaveUn()."'" .
														"/>\n";
												?>
                
                  <?php 
                  
                  	wpsd_jtip(
                  		__('Empire Avenue', 'wpsd'), 
                  		__('Empire Avenue Ticker. Registration required.','wpsd')); 
                  	
                  	wpsd_go('http://www.empireavenue.com', __('Empire Avenue', 'wpsd')); 
                  			
                  ?>
                  </td>
              </tr>
              
               <tr>
                <th scope="row"><img src="<?php echo $img_path; ?><?php echo $types['eave'][1]; ?>.png" alt="" /> <label for="wpsd_eave_pw">
                    <?php _e('Empire Avenue password','wpsd'); ?>
                    :</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_eave_pw' ";
												echo "id='wpsd_eave_pw' ";
												echo "value='".$form->getWpsdEavePw()."'" .
														"/>\n";
												?>
                 
                  <?php wpsd_jtip(__('Empire Avenue', 'wpsd'), __('Please enter your password', 'wpsd') ); ?>
                  </td>
              </tr>
              
              <!-- FriendFeed -->
               <tr class="highlight">
                <th scope="row"><img src="<?php echo $img_path; ?><?php echo $types['friendfeed'][1]; ?>.png" alt="" /> <label for="wpsd_friendfeed_un">
                    <?php _e('FriendFeed username','wpsd'); ?>
                    :</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_friendfeed_un' ";
												echo "id='wpsd_friendfeed_un' ";
												echo "value='".$form->getWpsdFriendFeedUn()."'" .
														"/>\n";
												?>
              
                  <?php 
                  	
                  	wpsd_jtip(
                  		__('FriendFeed', 'wpsd'),  
                  		__('FriendFeed username. Registration required.','wpsd')); 
                  	
                  	wpsd_go('http://www.friendfeed.com', __('FriendFeed', 'wpsd'));
                  	
                  ?>
                  	</td>
              </tr>
              
              <!-- Digg -->
               <tr>
                <th scope="row"><img src="<?php echo $img_path; ?><?php echo $types['digg'][1]; ?>.png" alt="" /> <label for="wpsd_digg_un">
                    <?php _e('Digg username','wpsd'); ?>
                    :</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_digg_un' ";
												echo "id='wpsd_digg_un' ";
												echo "value='".$form->getWpsdDiggUn()."'" .
														"/>\n";
												?>
               
                  <?php 
                  	wpsd_jtip(
                  		__('Digg', 'wpsd'), 
                  		__('Digg username. Registration required.','wpsd')); 
                  
                  	wpsd_go('http://digg.com', __('Digg', 'wpsd')); 
                  	
                  ?></td>	
              </tr>
              
             <?php /*  <tr>
                <th scope="row"> <label for="wpsd_society_un">
                    <?php _e('Society username','wpsd'); ?>
                    :</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_society_un' ";
												echo "id='wpsd_society_un' ";
												echo "value='".$form->getWpsdSocietyUn()."'" .
														"/>\n";
												?>
                  <br/>
                  <?php _e('Society username. <a href="http://society.me" target="_blank" title="Society">Society</a>. Registration required.','wpsd'); ?></td>
              </tr> */ ?>
              
             <?php /*  <tr class="highlight">
                <th scope="row"><img src="<?php echo $img_path; ?><?php echo $types['mylikes'][1]; ?>.png" alt="" /> <label for="wpsd_mylikes_un">
                    <?php _e('Mylikes username','wpsd'); ?>
                    :</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_mylikes_un' ";
												echo "id='wpsd_mylikes_un' ";
												echo "value='".$form->getWpsdMylikesUn()."'" .
														"/>\n";
												?>
                  <br/>
                  <?php _e('Mylikes username. <a href="http://mylikes.com/signup?token=daveligthart" target="_blank" title="Mylikes">Mylikes</a>.','wpsd'); ?> <?php _e('Registration required.', 'wpsd'); ?></td>
              </tr> */ ?>
              
              <!-- Battlenet -->
              <tr  class="highlight">
                <th scope="row"><img src="<?php echo $img_path; ?><?php echo $types['battlenet'][1]; ?>.png" alt="" /> <label for="wpsd_battlenet_uri">
                    <?php _e('Battlenet profile url','wpsd'); ?>
                    :</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_battlenet_uri' ";
												echo "id='wpsd_battlenet_uri' ";
												echo "value='".$form->getWpsdBattlenetUri()."'" .
														"/>\n";
												?>
              
                  <?php 
                  	wpsd_jtip(
                  		__('Battlenet', 'wpsd'), 
                  		__('Battlenet profile url. e.g <strong>http://eu.battle.net/sc2/en/profile/1491154/1/David/</strong>. Registration required.','wpsd')); 
                  	
                  	wpsd_go('http://eu.battle.net/en/', __('Battlenet', 'wpsd')); 
                  		
                  ?>
                  </td>
              </tr>
              
              <!-- Educopark -->
              <tr>
                <th scope="row"><img src="<?php echo $img_path; ?><?php echo $types['educopark'][1]; ?>.png" alt="" /> <label for="wpsd_educopark_un">
                    <?php _e('Educopark profile url','wpsd'); ?>
                    :</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_educopark_un' ";
												echo "id='wpsd_educopark_un' ";
												echo "value='".$form->getWpsdEducoParkUn()."'" .
														"/>\n";
												?>
                  <?php 
                  	wpsd_jtip(
                  		__('Educopark', 'wpsd'), 
                  		__('Educopark username. Registration required.','wpsd')); 
                  
                  	wpsd_go('http://educopark.com', __('Educopark', 'wpsd')); 
                  			
                  ?>
                  </td>
              </tr>
              
			  <?php 
			  /**
			  * deprecated 
			  <tr>
                <th scope="row"><img src="<?php echo $img_path; ?><?php echo $types['yahoobuzz'][1]; ?>.png" alt="" /> <label for="wpsd_yahoobuzz_uri">
                    <?php _e('YahooBuzz url','wpsd'); ?>
                    :</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_yahoobuzz_uri' ";
												echo "id='wpsd_yahoobuzz_uri' ";
												echo "value='".$form->getWpsdYahooBuzzUri()."'" .
														"/>\n";
												?>
                  <br/>
                  <?php _e('YahooBuzz url. e.g <strong>http://buzz.yahoo.com/activity/u/UBG7TETTMGJOTVDMKVODKQRDA4</strong> <a href="http://buzz.yahoo.com/" target="_blank" title="YahooBuzz">YahooBuzz</a>.','wpsd'); ?> <?php _e('Registration required.', 'wpsd'); ?></td>
              </tr>
              */ 
              ?>
              
              <!-- Vimeo -->
              <tr class="highlight">
                <th scope="row"><img src="<?php echo $img_path; ?><?php echo $types['vimeo'][1]; ?>.png" alt="" /> <label for="wpsd_vimeo_un">
                    <?php _e('Vimeo username','wpsd'); ?>
                    :</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_vimeo_un' ";
												echo "id='wpsd_vimeo_un' ";
												echo "value='".$form->getWpsdVimeoUn()."'" .
														"/>\n";
												?>
                  <?php 
                  
                  	wpsd_jtip(
                  		__('Vimeo', 'wpsd'), 
                  		__('Vimeo username. e.g http://www.vimeo.com/<strong>daveligthart</strong>. Registration required.','wpsd')); 
                  
                  	wpsd_go('http://vimeo.com/', __('Vimeo', 'wpsd')); 		
                  ?>
                  </td>
              </tr>
              
              <!-- Identi.ca -->
              <tr>
                <th scope="row"><img src="<?php echo $img_path; ?><?php echo $types['identica'][1]; ?>.png" alt="" /> <label for="wpsd_identica_un">
                    <?php _e('Identi.ca username','wpsd'); ?>
                    :</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_identica_un' ";
												echo "id='wpsd_identica_un' ";
												echo "value='".$form->getWpsdIdenticaUn()."'" .
														"/>\n";
												?>
                  <?php 
                  	
                  	wpsd_jtip(
                  	 __('Identi.ca', 'wpsd'),
                 	 __('Identi.ca username. e.g http://identi.ca/<strong>daveligthart</strong>. Registration required.','wpsd')); 
                 	 
                 	wpsd_go('http://identi.ca/', __('Identi.ca', 'wpsd')); 
                 		 
                  ?></td>
              </tr>
			
			  <!-- BlogCatalog -->
			  <tr class="highlight">
                <th scope="row"><img src="<?php echo $img_path; ?><?php echo $types['blogcatalog'][1]; ?>.png" alt="" /> <label for="wpsd_identica_un">
                    <?php _e('BlogCatalog username','wpsd'); ?>
                    :</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_blogcatalog_un' ";
												echo "id='wpsd_blogcatalog_un' ";
												echo "value='".$form->getWpsdBlogCatalogUn()."'" .
														"/>\n";
												?>
                  <?php 
                  	wpsd_jtip(
                  		__('BlogCatalog', 'wpsd'),
                  		__('BlogCatalog username. e.g http://www.blogcatalog.com/user/<strong>dligthart</strong>. Registration required','wpsd')); 
                  		
                  	wpsd_go('http://www.blogcatalog.com', __('BlogCatalog', 'wpsd'));
                  			
                  ?>
                  </td>
              </tr>
			  
			  <!-- Plaxo -->	
			  <tr>
                <th scope="row"><img src="<?php echo $img_path; ?><?php echo $types['plaxo'][1]; ?>.png" alt="" /> <label for="wpsd_plaxo_uri">
                    <?php _e('Plaxo url','wpsd'); ?>
                    :</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_plaxo_uri' ";
												echo "id='wpsd_plaxo_uri' ";
												echo "value='".$form->getWpsdPlaxoUri()."'" .
														"/>\n";
												?>
                  
                  <?php 
                  	wpsd_jtip(
                  		__('Plaxo', 'wpsd'),
                  		__('Plaxo profile url. Registration required.','wpsd')); 
                  	
                  	wpsd_go('http://plaxo.com/', __('Plaxo', 'wpsd'));
                  	
                  	?></td>
              </tr>
				
			  <!-- Delicious -->			
			  <tr class="highlight">
                <th scope="row"><img src="<?php echo $img_path; ?><?php echo $types['delicious'][1]; ?>.png" alt="" /> <label for="wpsd_delicious_search">
                    <?php _e('Delicious search phrase','wpsd'); ?>
                    :</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_delicious_search' ";
												echo "id='wpsd_delicious_search' ";
												echo "value='".$form->getWpsdDeliciousSearch()."'" .
														"/>\n";
												?>
                  
                  <?php 
                  
                  	wpsd_jtip(
                  		__('Delicious Search Phrase', 'wpsd'),
                  		__('Delicious search phrase to monitor.','wpsd')); 
                  		
                  	wpsd_go('http://delicious.com/', __('Delicious', 'wpsd'));
                  
                  ?></td>
              </tr> 
              
              <tr>
                <th scope="row"><img src="<?php echo $img_path; ?><?php echo $types['delicious'][1]; ?>.png" alt="" /> <label for="wpsd_delicious_uri">
                    <?php _e('Delicious url','wpsd'); ?>
                    :</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_delicious_uri' ";
												echo "id='wpsd_delicious_uri' ";
												echo "value='".$form->getWpsdDeliciousUri()."'" .
														"/>\n";
												?>
                  
                  <?php 
                  	wpsd_jtip(
                  		__('Delicious Url', 'wpsd'),  
                  		__('Delicious url to monitor e.g: <strong>http://www.delicious.com/url/591d3f6192c88f638547d183355587a6</strong>','wpsd')); 
                  		
                  ?></td>
              </tr> 
	
				 <tr class="highlight">
                <th scope="row"><img src="<?php echo $img_path; ?><?php echo $types['reddit'][1]; ?>.png" alt="" /> 
                <label for="wpsd_reddit_un">
                    <?php _e('Reddit username','wpsd'); ?>
                    :</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_reddit_un' ";
												echo "id='wpsd_reddit_un' ";
												echo "value='".$form->getWpsdRedditUn()."'" .
														"/>\n";
												?>
            
                  <?php 
                  	wpsd_jtip(
                  		__('Reddit', 'wpsd'), 
                  		__('Reddit username. Registration required.','wpsd')); 
                  	
                  	wpsd_go('http://reddit.com/', __('Reddit', 'wpsd')); 
                  			
                  ?>
                  </td>
              </tr> 
              
               <tr>
                <th scope="row"><img src="<?php echo $img_path; ?><?php echo $types['stumbleupon'][1]; ?>.png" alt="" /> 
                <label for="wpsd_stumbleupon_un">
                    <?php _e('StumbleUpon username','wpsd'); ?>
                    :</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_stumbleupon_un' ";
												echo "id='wpsd_stumbleupon_un' ";
												echo "value='".$form->getWpsdStumbleUponUn()."'" .
														"/>\n";
												?>
                  <?php 
                  	wpsd_jtip(
                  		__('StumbleUpon', 'wpsd'), 
                  		__('StumbleUpon username. Registration required.','wpsd')); 
                  	
                  	wpsd_go('http://stumbleupon.com/', __('StumbleUpon', 'wpsd'));
                  			
                  ?>
                  </td>
              </tr> 
              
              <tr class="highlight">
                <th scope="row"><img src="<?php echo $img_path; ?><?php echo $types['netlog'][1]; ?>.png" alt="" /> 
                <label for="wpsd_netlog_un">
                    <?php _e('Netlog username','wpsd'); ?>
                    :</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_netlog_un' ";
												echo "id='wpsd_netlog_un' ";
												echo "value='".$form->getWpsdNetlogUn()."'" .
														"/>\n";
												?>
                  <?php 
                  
                  	wpsd_jtip(
                  		__('Netlog', 'wpsd'), 
                  		__('Netlog username. Registration required.','wpsd')); 
                  	
                  	wpsd_go('http://netlog.com/', __('Netlog', 'wpsd'));
                  		
                  	?>
                  </td>
              </tr> 

			
			<tr>
                <th scope="row"><img src="<?php echo $img_path; ?><?php echo $types['99designs'][1]; ?>.png" alt="" /> 
                <label for="wpsd_99designs_un">
                    <?php _e('99Designs.com username','wpsd'); ?>:</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_99designs_un' ";
												echo "id='wpsd_99designs_un' ";
												echo "value='".$form->getWpsd99DesignsUn()."'" .
														"/>\n";
												?>                  
                  <?php 
                  	wpsd_jtip(
                  		__('99Designs', 'wpsd'),  
                  		__('99Designs.com username. Registration required.','wpsd')); 
                  	
                  	wpsd_go('http://99designs.com/', __('99Designs', 'wpsd'));
                  	
                  ?>
                  </td>
              </tr> 
              
               <tr class="highlight">
                <th scope="row"><img src="<?php echo $img_path; ?><?php echo $types['pagerank'][1]; ?>.png" alt="" /> <label for="wpsd_ego_search">
                    <?php _e('Ego search phrase ','wpsd'); ?>
                    :</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_ego_search' ";
												echo "id='wpsd_ego_search' ";
												echo "value='".$form->getWpsdEgoSearch()."'" .
														"/>\n";
												?>
                
                  <?php wpsd_jtip(__('Ego Search', 'wpsd'), __('E.g your firstname and lastname.','wpsd')); ?></td>
              </tr> 
              
              
              <tr>
                <th scope="row"><img src="<?php echo $img_path; ?><?php echo $types['quora'][1]; ?>.png" alt="" /> 
                <label for="wpsd_quora_un">
                    <?php _e('Quora username','wpsd'); ?>
                    :</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_quora_un' ";
												echo "id='wpsd_quora_un' ";
												echo "value='".$form->getWpsdQuoraUn()."'" .
														"/>\n";
												?>
                  <?php 
                  	wpsd_jtip(
                  		__('Quora', 'wpsd'), 
                  		__('Quora username. http://www.quora.com/<strong>Dave-Ligthart</strong>. Registration required.','wpsd')); 
                  		
                  	wpsd_go('http://quora.com', __('Quora', 'wpsd')); 
                  	
                  ?>
                  </td>
              </tr> 
              
              <tr class="highlight">
                <th scope="row"><img src="<?php echo $img_path; ?><?php echo $types['getglue'][1]; ?>.png" alt="Getglue.com logo" /> 
                <label for="wpsd_getglue_un">
                    <?php _e('Getglue username','wpsd'); ?>
                    :</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_getglue_un' ";
												echo "id='wpsd_getglue_un' ";
												echo "value='".$form->getWpsdGetGlueUn()."'" .
														"/>\n";
												?>

                  <?php 
                  	wpsd_jtip(
                  		__('Getglue', 'wpsd'),
                  		__('Getglue username. http://getglue.com/<strong>daveligthart</strong>. Registration required.','wpsd')); 
                  	
                  	wpsd_go('http://getglue.com', __('Getglue', 'wpsd'));
                  			
                  ?>
                  </td>
              </tr> 
              
               <tr>
                <th scope="row"><img src="<?php echo $img_path; ?><?php echo $types['pagerank'][1]; ?>.png" alt="" /> <label for="wpsd_googleplus_un">
                    <?php _e('Google Plus Id','wpsd'); ?>
                    :</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_googleplus_un' ";
												echo "id='wpsd_googleplus_un' ";
												echo "value='".$form->getWpsdGooglePlusUn()."'" .
														"/>\n";
												?>
                  <?php 
                  	wpsd_jtip(
                  		__('Google Plus', 'wpsd'), 
                  		__('Google Plus ID. (note: NOT the full url, only the id) e.g: https://plus.google.com/<strong>107127911590007452165</strong>/posts. 
                  		Registration required.','wpsd')); 
                  
                  	wpsd_go('https://plus.google.com/', __('Google Plus', 'wpsd'));
                  			
                  ?></td>
              </tr>
              
              <tr class="highlight">
                <th scope="row"><img src="<?php echo $img_path; ?><?php echo $types['hunch'][1]; ?>.png" alt="Hunch logo" /> 
                <label for="wpsd_hunch_un">
                    <?php _e('Hunch username','wpsd'); ?>
                    :</label>
                </th>
                <td><?php
												echo "<input type='text' size='60' ";
												echo "name='wpsd_hunch_un' ";
												echo "id='wpsd_hunch_un' ";
												echo "value='".$form->getWpsdHunchUn()."'" .
														"/>\n";
												?>
                  <?php 
                  	wpsd_jtip(
                  		__('Hunch', 'wpsd'), 
                  		__('Hunch username. http://hunch.com/<strong>daveligthart</strong>. Registration required.','wpsd')); 
                  	
                  	wpsd_go('http://hunch.com', __('Hunch', 'wpsd')); 
                  			
                  ?>
                  </td>
              </tr> 

			  </tbody>	
            
            </table>

            <p>&nbsp;</p>

            <input type="submit" name="submit" value="<?php _e('Save Changes', 'wpsd'); ?>" class="button-primary" />
       
          </div>

        </div>
        
        </form> <!-- end config form -->
        
        	 
        <div class="stuffbox wpsd">
          <h3> <?php echo _e('Enable / Disable Social Metrics','wpsd'); ?></h3>
          <div class="inside"><br/>
            <form name="wpsd_config_metrics_form" method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post" accept-charset="utf-8">
            <?php echo $form2->htmlFormId(); ?>
            <?php $wpsd_metrics = wpsd_get_metrics_types(); ?>
            <?php if(null != $wpsd_metrics && is_array($wpsd_metrics)): ksort($wpsd_metrics); ?>
            <?php foreach($wpsd_metrics as $k=>$v): if(is_array($v)) { $title = $v[0]; }?>
            <?php wpsd_create_cb($k, $title, $form2); ?>
            <?php endforeach; ?>
            <?php endif;?>
            <br/>
            <br/>
            <input type="submit" name="submit" value="<?php _e('Save Changes', 'wpsd'); ?>" class="button-primary" />
            </form>
          </div>
        </div>
        
      </div>
    <?php } ?>
    </div>
    <!-- /#post-body-content -->
  </div>
</div>