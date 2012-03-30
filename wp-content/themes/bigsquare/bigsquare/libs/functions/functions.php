<?php

/* Load Web Font */
add_theme_support('webfont');

/* Load J-Query */
if(!is_admin()) {
	wp_enqueue_script('theme_js', get_template_directory_uri().'/js/wpop.js', array('jquery'));
	wp_enqueue_script('theme_js');
}

/* Header */
function wpop_head() { ?>
		<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" />
		<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/superfish.js"></script>
		<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/supersubs.js"></script>
		<script type="text/stylesheet" src="<?php bloginfo('template_url'); ?>/js/css/superfish.css"></script>
		<script type="text/stylesheet" src="<?php bloginfo('template_url'); ?>/js/css/superfish-navbar.css"></script>
		<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/textbox-hint.js"></script>
<?php }
add_filter('wp_head', 'wpop_head');

/* Add Thumbnail Support */
add_theme_support( 'post-thumbnails' );
add_image_size( 'pic-excerpt', 100, 100, true ); // Permalink thumbnail size
add_image_size( 'pic-archive', 220, 220, true ); // Permalink thumbnail size
add_image_size( 'full', 700, 9999, false ); // Permalink thumbnail size

/* The Image Excerpt */
function the_image_excerpt($pics = 4) {
	global $post;
	$images =& get_children( 'post_type=attachment&post_mime_type=image&post_parent=' . $post->ID );
	foreach(array_slice($images, 0, $pics) as $image) {
		$imgsrc = wp_get_attachment_image_src($image->ID, 'pic-excerpt', false);
		echo "<img src=\"$imgsrc[0]\" widht=\"$imgsrc[1]\" height=\"$imgsrc[2]\" />";
	}
}

/* Adding Nav Menus */
add_action( 'init', 'register_my_menus' );

function register_my_menus() {
	register_nav_menus(
		array(
			'main-menu' => __( 'Main Menu' ),
		)
	);
}

/* Comments Template */

function wpop_comment($comment, $args, $depth) {
   $GLOBALS['comment'] = $comment; ?>
   <li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
     <div id="comment-<?php comment_ID(); ?>">
      <div class="comment-author vcard">
         <?php echo get_avatar($comment,$size='80',$default='<path_to_url>' ); ?>
      </div>
      <?php if ($comment->comment_approved == '0') : ?>
         <em style="padding-left: 20px;"><?php _e('Your comment is awaiting moderation.') ?></em>
         <br />
      <?php endif; ?>
	
	<div class="comment-post">
      <div class="comment-meta commentmetadata">
      <?php printf(__('<cite class="fn">%s</cite> on'), get_comment_author_link()) ?> 
      <a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>"><?php printf(__('%1$s'), get_comment_date(),  get_comment_time()) ?></a><?php edit_comment_link(__('(Edit)'),'  ','') ?></div>

      <?php comment_text() ?>

      <div class="reply">
         <?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
      </div>
     </div> <!-- comment-post -->
     <div class="clear"></div>
     </div>
<?php
        }

/* Get Search Form */

function wpop_search_form( $form ) {

    $form = '<form role="search" method="get" id="searchform" action="'.get_bloginfo('url').'" >
    <div>
    <input type="text" value="' . get_search_query() . '" name="s" id="s" />
    </div>
    </form>';

    return $form;
}

add_filter( 'get_search_form', 'wpop_search_form' );

/* Shortcode */

function wpop_devider() {
    return '<div class="devider"><a href="#header">'.__(Top).'</a></div>';
}
add_shortcode('devider', 'wpop_devider');

/* How To Page */

function wpop_bs_howto() {
	add_theme_page(__('Big Square Help &amp; How To'), __('Help &amp; How To'), 'manage_options', 'how-to', 'wpop_howto');
}
add_action('admin_menu', 'wpop_bs_howto');
function wpop_howto() {
	include_once(TEMPLATEPATH . "/functions/help.php");
}

?>