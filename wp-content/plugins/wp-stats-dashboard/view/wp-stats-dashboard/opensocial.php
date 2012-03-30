<?php 
/**
 * OpenSocial gadget.
 * 
 * @author dligthart <info@daveligthart.com>
 * @version 0.1
 * @package wp-stats-dashboard
 */
include_once(realpath(dirname(__FILE__) . '/../../../../..') . '/wp-load.php'); // load wordpress context.
?><?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<Module>
	<ModulePrefs title="__MSG_title__" author="Dave Ligthart" author_email="dligthart@gmail.com" title_url="http://daveligthart.com/opensocialgadgets" screenshot="__MSG_screenshot__" thumbnail="__MSG_thumbnail__" description="__MSG_description__" height="400">
		<Locale messages="<?php echo WPSD_PLUGIN_URL; ?>/resources/opensocial/ALL_ALL.xml"/>
	</ModulePrefs>
	<Content type="html">
	<![CDATA[
	<style type="text/css">
	img{
		width:16px;
	}
	</style>
  		<div id="wpsd">
  		<?php
		
		if ( file_exists(realpath(dirname(__FILE__) . '/../../classes/ajax.php'))) {
			include(realpath(dirname(__FILE__) . '/../../classes/ajax.php'));
		}
		
		?>		
		</div>
  	]]>
  	</Content>
</Module>
