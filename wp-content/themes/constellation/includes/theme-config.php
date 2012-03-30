<?php

################################################################################
// Set up menus within the wordpress admin sections
################################################################################

function wp_constellation_menu() { 		
	// Add a new top-level menu:
	    add_menu_page(__('Theme Options', 'constellation'), __('Theme Options', 'constellation'), 'manage_options', basename(__FILE__) , 'wp_constellation_admin', get_bloginfo('template_url').'/includes/menu_icon.png', 3);
	// Add submenus to the custom top-level menu:
		add_submenu_page(basename(__FILE__), __('Options', 'constellation'),  __('Options', 'constellation') , 'manage_options', basename(__FILE__) , 'wp_constellation_admin');
}
add_action('admin_menu', 'wp_constellation_menu');

################################################################################
// Init our options
################################################################################

// Options = name, default, label, hint, rules
$constellation_options = (
	array( 
		array('General Settings', array(
			array('constellation_google_analytics', '', 'Google Analytics site ID','UA-XXXXX-X',''),
			array('constellation_show_subpages', 'yes', 'Show sub-pages?','Displays a list of sub-pages when viewing a page.','yesno'),
			)
		)
	)
);
	
foreach($constellation_options as $section) {
	foreach($section[1] as $option) {
		add_option($option[0], $option[1]);
	}
}

function wp_constellation_admin_css() {
	?>
	<style type="text/css">
		<!--
			#constellation_form h3 {
				background: #E3E3E3;
				padding: 12px;
				margin-bottom: 0 !important
			}
			#constellation_form .constellation_section {
				border: 1px solid #E3E3E3;
				padding: 0 6px
			}
			#constellation_form table {				
				margin-top: 0 !important;
				border-collapse: collapse;
				border-bottom: 2px solid #F9F9F9;
			}
			#constellation_form table td, #constellation_form table th {
				padding: 12px 6px;
				border-bottom: 1px solid #E3E3E3
			}
			#constellation_form .message {
				padding: 12px;
				border: 2px dashed #98BFE6;
				background: #EAF2FA;
				line-height: 23px;
				font-weight: bold;
			}
		-->
	</style>
	<?php
}
add_action('admin_head', 'wp_constellation_admin_css');
	
function wp_constellation_admin() {

	global $constellation_options;

	if ($_POST['save_constellation_options']) {
	
		foreach($constellation_options as $section) {
			foreach($section[1] as $option) {
				update_option($option[0],stripslashes($_POST[$option[0]]));
			}
		}

		/* Sucess */
		echo '<div id="message" class="updated fade"><p><strong>Theme Options Saved</strong></p></div>';
	}
	?>

	<div class="wrap">
		<h2>Theme Options</h2>
		<form method="post" action="admin.php?page=theme-config.php" id="constellation_form">
			<p class="submit message" style="text-align:right"><span style="float:left">These options control various parts of the theme.</span> <input type="submit" value="Save Changes" name="save_constellation_options" /></p>
			<?php	
			foreach($constellation_options as $section) {
				echo '<h3>'.$section[0].'</h3><div class="constellation_section"><table cellspacing="0" cellpadding="0" class="form-table">';
				foreach($section[1] as $option) {
					echo '<tr valign="top">';
					
					echo '<th><label for="'.$option[0].'">'.$option[2].'</label></th><td>';
					
					if ($option[4]=='yesno') {
						$yes = '';
						$no = '';
						if (get_option($option[0])=='yes') $yes='selected="selected"'; else $no='selected="selected"';
						echo '<select name="'.$option[0].'">
							<option value="yes" '.$yes.'>Yes</option>
							<option value="no" '.$no.'>No</option>
						</select>';
					} elseif ($option[4]=='textarea') {
						echo '<textarea id="'.$option[0].'" name="'.$option[0].'" cols="50" rows="6">'.get_option($option[0]).'</textarea>';
					} else {
						echo '<input type="text" id="'.$option[0].'" name="'.$option[0].'" size="25" value="'.get_option($option[0]).'" />';
					}
					
					if ($option[3]) echo '<br/><span class="setting-description">'.$option[3].'</span>';
					
					echo '</td></tr>';
				}
				echo '</table></div><br class="clear" />';
			}
			?>
			<p class="submit" style="text-align:right"><input type="submit" value="Save Changes" name="save_constellation_options" /></p>
		</form>
	</div>
	<?php
}
?>