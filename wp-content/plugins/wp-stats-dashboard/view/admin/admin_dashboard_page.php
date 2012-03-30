<?php

/**
 * form
 * 
 * (default value: new WPSDAdminConfigForm())
 * 
 * @var mixed
 * @access public
 */
$form = new WPSDAdminConfigForm();

/**
 * wpsd_trends_type
 * 
 * (default value: $form->getWpsdTrendsType())
 * 
 * @var mixed
 * @access public
 */
$wpsd_trends_type = $form->getWpsdTrendsType();


/**
 * loading_img
 * 
 * (default value: '<img src="'. WPSD_PLUGIN_URL .'/resources/images/ajax-loader.gif" alt="'.__('loading', 'wpsd').'" width="24" height="24" />')
 * 
 * @var string
 * @access public
 */
$loading_img = '<img src="'. WPSD_PLUGIN_URL .'/resources/images/ajax-loader.gif" alt="'.__('loading', 'wpsd').'" width="24" height="24" />';
		
?>
<style type="text/css">
.postbox .inside{ padding:5px; }
.postbox .inside a { text-decoration:none; }
</style>

    <div class="wrap">
        <h2><span style="display:none;"><?php _e('WP-Stats-Dashboard Options', 'wpsd');?></span></h2><img style="margin:-2px 5px;" src="<?php echo WPSD_PLUGIN_URL; ?>/resources/images/logo-wpstatsdashboard-300x45.png" alt="wp-stats-dashboard" width="300" height="45">

        <div id="dashboard-widgets-wrap">
            <div id="dashboard-widgets" class="metabox-holder">
                <div class="postbox-container" style="width:49%;">
                    <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                        <div id="wpstatsdashboard_widget" class="postbox">
                            <div class="handlediv" title="<?php _e('Click to toggle'); ?>">
                                <br>
                            </div>

                            <h3 class="hndle"><span><?php _e('Stats - Views per day', 'wpsd'); ?></span></h3>

                            <div class="inside">
                                <div id="wpsd_chart_container">
                                    <a href="http://www.adobe.com/go/getflashplayer"><img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player"></a>
                                </div>
                            </div>
                        </div>

                        <div id="wpsd_trends" class="postbox">
                            <div class="handlediv" title="<?php _e('Click to toggle'); ?>">
                                <br>
                            </div>

                            <h3 class="hndle"><span><?php _e('Stats - Trends', 'wpsd'); ?></span></h3>

                            <div class="inside">
                                   
                                    <?php include('admin_trend.php'); ?>
                            </div>
                        </div>

                        <?php /* <div id="wpsd_blogpulse" class="postbox">
                            <div class="handlediv" title="<?php _e('Click to toggle'); ?>">
                                <br>
                            </div>

                            <h3 class='hndle'><span><?php _e('Stats - BlogPulse.com', 'wpsd'); ?></span></h3>

                            <div class="inside">
                                <div id="wpsd_blogpulse_inner">
                                    &nbsp; <?php echo $loading_img; ?>
                                </div>
                            </div>
                        </div> */ ?>

                        <div id="wpsd_compete" class="postbox">
                            <div class="handlediv" title="<?php _e('Click to toggle'); ?>">
                                <br>
                            </div>

                            <h3 class='hndle'><span><?php _e('Stats - Compete.com', 'wpsd'); ?></span></h3>

                            <div class="inside">
                                <div id="wpsd_compete_inner">
                                    &nbsp; <?php echo $loading_img; ?>
                                </div>
                            </div>
                        </div>
                        
                         <div id="wpsd_author" class="postbox">
                            <div class="handlediv" title="<?php _e('Click to toggle'); ?>">
                                <br>
                            </div>

                            <h3 class='hndle'><span><?php _e('Stats - Top 5 Authors', 'wpsd'); ?></span></h3>

                            <div class="inside">
                                <div id="wpsd_author_inner">
                                    &nbsp; <?php echo $loading_img; ?>
                                </div>
                            </div>
                        </div>

                        <div id="wpsd_postviews" class="postbox ">
                            <div class="handlediv" title="<?php _e('Click to toggle'); ?>">
                                <br>
                            </div>

                            <h3 class="hndle"><span><?php _e('Stats - Post Views', 'wpsd'); ?></span></h3>

                            <div id="wpsd_postviews_inner" class="inside"></div>
                        </div>

                        <div id="wpsd_referrers" class="postbox">
                            <div class="handlediv" title="<?php _e('Click to toggle'); ?>">
                                <br>
                            </div>

                            <h3 class='hndle'><span><?php _e('Stats - Referrers', 'wpsd'); ?></span></h3>

                            <div class="inside">
                                <div id="wpsd_referers_inner">
                                    &nbsp; <?php echo $loading_img; ?>
                                </div>
                            </div>
                        </div>

                        <div id="wpsd_searchterms" class="postbox">
                            <div class="handlediv" title="<?php _e('Click to toggle'); ?>">
                                <br>
                            </div>

                            <h3 class='hndle'><span><?php _e('Stats - Search terms', 'wpsd'); ?></span></h3>

                            <div class="inside">
                                <div id="wpsd_searchterms_inner">
                                    &nbsp; <?php echo $loading_img; ?>
                                </div>
                            </div>
                        </div>

                        <div id="wpsd_clicks" class="postbox" style="">
                            <div class="handlediv" title="<?php _e('Click to toggle'); ?>">
                                <br>
                            </div>

                            <h3 class="hndle"><span><?php _e('Stats - Clicks', 'wpsd'); ?></span></h3>

                            <div id="wpsd_clicks_inner" class="inside"></div>
                        </div>
                    </div>
                </div>

                <div class="postbox-container" style="width:49%;">
                    
                    <div id="wpsd_optimize" class="postbox " style="display: block; ">
						<div class="handlediv" title="<?php _e('Click to toggle'); ?>"><br></div>
						<h3 class="hndle"><span><?php _e('Stats - Optimization Suggestions', 'wpsd'); ?></span></h3>
						<div class="inside">
							<div id="wpsd_optimize_inner">&nbsp; <?php echo $loading_img; ?></div>
						</div>
					</div>
                    
                    <div id="wpsd_overview" class="postbox">
                        <div class="handlediv" title="<?php _e('Click to toggle'); ?>">
                            <br>
                        </div>

                        <h3 class="hndle"><span><?php _e('Stats - Social Media Metrics', 'wpsd'); ?></span></h3>
						
						<div class="inside">
                       	   
                       	    <div align="center" id="wpsd-loading" style="display: none;"> <img src="<?php echo WPSD_PLUGIN_URL; ?>/resources/images/ajax-loader.gif" alt="loading ranking stats"></div>
                       
                        	<div id="wpsd-stats-ranking"></div>
                        
                        </div>
                    </div>
					
					
					
                 <?php /*   
                 	<div id="wpsd_blogpulse_conv" class="postbox ">
                        <div class="handlediv" title="<?php _e('Click to toggle'); ?>">
                            <br>
                        </div>

                        <h3 class='hndle'><span><?php _e('Stats - BlogPulse.com Conversations', 'wpsd'); ?></span></h3>

                        <div class="inside">
                            <div id="wpsd_blogpulse_conversations_inner">&nbsp; <?php echo $loading_img; ?>
                            </div>

                            <p><a href="http://blogpulse.com/conversation?query=&link=<?php bloginfo('url'); ?>&max_results=25&start_date=20100101&Submit.x=18&Submit.y=11&Submit=Submit" target="_blank" title="<?php _e('BlogPulse Conversations', 'wpsd'); ?>"><?php _e('Track conversations about your blog', 'wpsd'); ?></a></p>
                        </div>
                    </div>
                    */ ?>
                </div>
            </div>
        </div>
    </div>

