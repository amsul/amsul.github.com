<?php
/*

	Section: PageLines Boxes
	Author: Andrew Powers
	Description: Creates boxes and box layouts
	Version: 1.0.0
	
*/

class PageLinesBoxes extends PageLinesSection {

   function __construct( $registered_settings = array() ) {
	
		$name = __('PageLines Boxes', 'pagelines');
		$id = 'boxes';
		
		$default_settings = array(
			'description' 	=> 'Inline boxes on your page that support images and media.  Great for feature lists, and media.',
			'icon'			=> PL_ADMIN_ICONS . '/boxes.png', 
		);
		
		$settings = wp_parse_args( $registered_settings, $default_settings );
		
		parent::__construct($name, $id, $settings);    
   }


	/*
		Loads php that will run on every page load (admin and site)
		Used for creating administrative information, like post types
	*/

	function section_persistent(){
		/* 
			Create Custom Post Type 
		*/
			$args = array(
					'label' => __('Boxes', 'pagelines'),  
					'singular_label' => __('Box', 'pagelines'),
					'description' => 'For creating boxes in box type layouts.',
				);
			$taxonomies = array(
				"box-sets" => array(	
						"label" => __('Box Sets', 'pagelines'), 
						"singular_label" => __('Box Set', 'pagelines'), 
					)
			);
			$columns = array(
				"cb" => "<input type=\"checkbox\" />",
				"title" => "Title",
				"bdescription" => "Text",
				"bmedia" => "Media",
				"box-sets" => "Box Sets"
			);
		
			$column_value_function = 'box_column_display';
		
			$this->post_type = new PageLinesPostType($this->id, $args, $taxonomies, $columns, $column_value_function);
		
				/* Set default posts if none are present */
				$this->post_type->set_default_posts('pagelines_default_boxes');


		/*
			Meta Options
		*/
		
				/*
					Create meta fields for the post type
				*/
					$type_meta_array = array(
						'the_box_icon' 		=> array(
								'type' => 'image_upload',					
								'title' => 'Box Image',
								'desc' => 'Upload an image for the box.<br/> Depending on your settings this image will be used as an icon, or splash image; so desired size may vary.'
							), 
						'the_box_icon_link'		=> array(
								'type' => 'text',					
								'title' => 'Box Link (Optional)',
								'desc' => 'Make the box image and title clickable by adding a link here (optional)...'
							)
					);

					$post_types = array($this->id); 
					
					$type_metapanel_settings = array(
							'id' 		=> 'boxes-metapanel',
							'name' 		=> THEMENAME." Box Options",
							'posttype' 	=> $post_types,
						);
					
					global $boxes_meta_panel;
					
					$boxes_meta_panel =  new PageLinesMetaPanel( $type_metapanel_settings );
					
					$type_metatab_settings = array(
						'id' 		=> 'boxes-type-metatab',
						'name' 		=> "Box Setup Options",
						'icon' 		=> $this->icon,
					);

					$boxes_meta_panel->register_tab( $type_metatab_settings, $type_meta_array );
						
						
		/*
			Build Ind. Page and Post Options
		*/
					$metatab_array = array(
							'box_set' => array(
								'type' 			=> 'select_taxonomy',
								'taxonomy_id'	=> "box-sets",				
								'title'		 	=> 'Select Box Set To Show',
								'desc' 			=> 'If you are using the box section, select the box set you would like to show on this page.'
							), 
							'box_col_number' => array(
								'type' 			=> 'select_count',
								'count_number'	=> '5', 
								'count_start'	=> '2',
								'inputlabel' 	=> 'Number of Feature Box Columns',
								'title' 		=> 'Box Columns',
								'label' 		=> "Select the number of columns to show boxes in.",
								'desc' 			=> "The number you select here will be the number of boxes listed in a row on a page. Note: This won't work on the blog page (use the global option)."
							), 
							'box_thumb_type' => array(
								'type' => 'select',
								'selectvalues'	=> array(
										'inline_thumbs'	=> array("name" => "Image At Left"),
										'top_thumbs'	=> array("name" => "Image On Top"), 
										'only_thumbs'	=> array("name" => "Only The Image, No Text")
									), 
								'title' => 'Box Thumb Position',				
								'desc' => 'Choose between thumbs on left and thumbs on top of boxes.',
								
							),
							'box_thumb_size' => array(
								'type' 			=> 'text',
								'size'			=> 'small',
								'title' 		=> 'Box Icon Size (in Pixels)',
								'label' 		=> "Enter the icon size in pixels",
								'desc' 			=> "Select the default icon size in pixels, set the images when creating new boxes.",
							),
							'box_items' => array(
								'type' 			=> 'text',
								'size'			=> 'small',
								'inputlabel' 	=> 'Maximum Boxes To Show On Page',
								'title' 		=> 'Max Number of Boxes',
								'desc' 			=> "Select the max number of boxes to show on this page (overrides default).",
							),
						);

					$metatab_settings = array(
							'id' => 'fboxes_meta',
							'name' => "Boxes Section",
							'icon' => $this->icon
						);

					register_metatab($metatab_settings, $metatab_array);
	}

   function section_template() {    
	
		global $post; 
		global $pagelines_ID;
				
		// inserts a clearing element at the end of each line of boxes
		$perline = (pagelines_option('box_col_number', $pagelines_ID)) ? pagelines_option('box_col_number', $pagelines_ID) : 3;
	
		if( get_pagelines_meta('box_set', $pagelines_ID) ) 
			$set = get_post_meta($pagelines_ID, 'box_set', true);
		elseif (pagelines_non_meta_data_page() && pagelines_option('box_default_tax')) 
			$set = pagelines_option('box_default_tax');
		else $set = null;
		
		if(pagelines_option('box_items', $pagelines_ID)) $limit = pagelines_option('box_items', $pagelines_ID);
		else $limit = null;
	
		$b = $this->load_pagelines_boxes($set, $limit); 
		
		$this->draw_boxes($b, $perline, $set);
		
		
	}

	function draw_boxes($b, $perline = 3, $class = ""){ 
		global $post;
		global $pagelines_ID;
	
		$box_thumb_type = (pagelines_option('box_thumb_type', $pagelines_ID)) ? pagelines_option('box_thumb_type', $pagelines_ID) : 'inline_thumbs';

		$count = $perline;
?>
		<div class="dcol_container_<?php echo $perline;?> <?php echo $class;?> fboxes fix">
<?php 	foreach($b as $bpost):
			setup_postdata($bpost); 
 			$box_link = get_post_meta($bpost->ID, 'the_box_icon_link', true);
			$box_icon = get_post_meta($bpost->ID, 'the_box_icon', true);
?>
			<div id="<?php echo 'fbox_'.$bpost->ID;?>" class="dcol_<?php echo $perline;?> dcol fbox">
				<div class="dcol-pad <?php echo $box_thumb_type;?>">	
					<?php if($box_icon):?>
						<div class="fboxgraphic">
							<?php echo self::_get_box_image( $bpost, $box_icon, $box_link ); ?>
						</div>
					<?php endif;?>
						<div class="fboxinfo fix">
							<div class="fboxtitle">
								<h3>
<?php 							if($box_link) printf('<a href="%s">%s</a>', $box_link, $bpost->post_title );
								else echo do_shortcode($bpost->post_title); ?>
								</h3>
							</div>
							<div class="fboxtext"><?php echo do_shortcode($bpost->post_content); ?><?php edit_post_link(__('[Edit Box]', 'pagelines'), '<br/>', '', $bpost->ID);?></div>
						</div>
						<?php pagelines_register_hook( 'pagelines_box_inside_bottom', $this->id ); // Hook ?>
				</div>
			</div>
<?php 
			$end = ($count+1) / $perline;  
			if(is_int($end)) echo '<div class="clear"></div>';
			$count++; 
		endforeach;	?>
		</div>
		<div class="clear"></div>
<?php }


	function load_pagelines_boxes($set = null, $limit = null){
		$query = array();
		
		$query['post_type'] = 'boxes'; 
		$query['orderby'] 	= 'ID'; 
		
		if(isset($set)) 
			$query['box-sets'] = $set; 
			
		if(isset($limit)) 
			$query['showposts'] = $limit; 

		$q = new WP_Query($query);
		
		if(is_array($q->posts)) 
			return $q->posts;
		else 
			return array();
	
	}
	

	function _get_box_image( $bpost, $box_icon, $box_link = false ){
			global $pagelines_ID;
			
			// Get thumb size
			$box_thumb_size = (pagelines_option('box_thumb_size', $pagelines_ID)) ? pagelines_option('box_thumb_size', $pagelines_ID) : 64;
			
			// Make the image's tag with url
			$image_tag = sprintf('<img src="%s" alt="%s" style="width:%dpx" />', $box_icon, esc_html($bpost->post_title), $box_thumb_size );
			
			// If link for box is set, add it
			if( $box_link ) 
				$image_output = sprintf('<a href="%s" title="%s">%s</a>', $box_link, esc_html($bpost->post_title), $image_tag );
			else $image_output = $image_tag;
			
			// Filter output
			return apply_filters('pl_box_image', $image_output, $bpost->ID);
	}
	

	function section_options($optionset = null, $location = null) {
		
		if($optionset == 'new' && $location == 'bottom'){
			return array(
				'box_settings' => array(
						'box_col_number' => array(
								'default' 		=> 3,
								'type' 			=> 'count_select',
								'count_number'	=> '5', 
								'count_start'	=> '2',
								'inputlabel' 	=> 'Default Number of Feature Box Columns',
								'title' 		=> 'Box Columns',
								'shortexp' 		=> "Select the number of columns to show boxes in.",
								'exp' 			=> "The number you select here will be the number of boxes listed in a row on a page.",
								'docslink'		=> 'http://www.pagelines.com/docs/boxes-soapboxes', 
								'vidtitle'		=> 'View Box Documentation'
							),
						'box_items' => array(
								'default' 		=> 5,
								'type' 			=> 'text_small',
								'inputlabel' 	=> 'Maximum Boxes To Show',
								'title' 		=> 'Default Number of Boxes',
								'shortexp' 		=> "Select the max number of boxes to show.",
								'exp' 			=> "This will be the maximum number of boxes shown on an individual page.",
							), 
						'box_thumb_size' => array(
								'default' 		=> 64,
								'type' 			=> 'text_small',
								'inputlabel' 	=> 'Box Icon Size (in Pixels)',
								'title' 		=> 'Default Box Icon Size',
								'shortexp' 		=> "Add the icon size in pixels",
								'exp' 			=> "Select the default icon size in pixels, set the images when creating new boxes.",
							), 
						'box_default_tax' => array(
								'default' 		=> 'default-boxes',
								'taxonomy_id'	=> 'box-sets',
								'type' 			=> 'select_taxonomy',
								'inputlabel' 	=> 'Select Posts/404 Box Set',
								'title' 		=> 'Posts Page and 404 Box-Set',
								'shortexp' 		=> "Posts pages and similar pages (404) will use this box-set ID",
								'exp' 			=> "Posts pages and 404 pages in WordPress don't support meta data so you need to assign a set here. (If you want to use 'boxes' on these pages.)",
							)
					)
				);
		}
	}

	
// End of Section Class //
}

function box_column_display($column){
	global $post;
	
	switch ($column){
		case "bdescription":
			the_excerpt();
			break;
		case "bmedia":
			if(get_post_meta($post->ID, 'the_box_icon', true )){
			
				echo '<img src="'.get_post_meta($post->ID, 'the_box_icon', true ).'" style="max-width: 80px; margin: 10px; border: 1px solid #ccc; padding: 5px; background: #fff" />';	
			}
			
			break;
		case "box-sets":
			echo get_the_term_list($post->ID, 'box-sets', '', ', ','');
			break;
	}
}

		
function pagelines_default_boxes($post_type){
	
	$d = array_reverse(get_default_fboxes());
	
	foreach($d as $dp){
		// Create post object
		$default_post = array();
		$default_post['post_title'] = $dp['title'];
		$default_post['post_content'] = $dp['text'];
		$default_post['post_type'] = $post_type;
		$default_post['post_status'] = 'publish';
		
		$newPostID = wp_insert_post( $default_post );
		
		if(isset($dp['media']))
			update_post_meta($newPostID, 'the_box_icon', $dp['media']);
		
		wp_set_object_terms($newPostID, 'default-boxes', 'box-sets');
		
		// Add other default sets, if applicable.
		if(isset($dp['set']))
			wp_set_object_terms($newPostID, $dp['set'], 'box-sets', true);

	}
}
