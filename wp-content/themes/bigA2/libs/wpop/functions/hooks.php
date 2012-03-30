<?php
/**
 * Collection of core functions.
 *
 * @package    Wordspop
 * @subpackge  Wordspop_Framework
 * @copyright  Copyright (c) 2010-2011 Wordspop
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU GPL version 2
 * @since      1.0-beta5
 * @todo       Attachment template filter, but how?
 */

/**
 * Remove wpautop, wptexturize with a shortcode.
 * http://wordpress.org/support/topic/plugin-remove-wpautop-wptexturize-with-a-shortcode
 * Temporary fix known issue http://core.trac.wordpress.org/ticket/12061
 *
 * @since 1.0-beta6
 */
function wpop_filter_autop( $content ) {
    $new_content = '';

    /* Matches the contents and the open and closing tags */
    $pattern_full = '@(\$raw\$.*?\$-raw\$)@is';

    /* Matches just the contents */
    $pattern_contents = '@\$raw\$(.*?)\$-raw\$@is';

    /* Divide content into pieces */
    $pieces = preg_split($pattern_full, $content, -1, PREG_SPLIT_DELIM_CAPTURE);

    /* Loop over pieces */
    foreach ($pieces as $piece) {
        /* Look for presence of the shortcode */
        if (preg_match($pattern_contents, $piece, $matches)) {
            /* Append to content (no formatting) */
            if ( preg_match( '@\$op\$(.*?)\$-[^\$]+\$@ism', $matches[1], $matches2 ) ) {
                $new_content .= wptexturize( wpautop( preg_replace('@^\r\n@', '', $matches2[1] ) ) );
            } else {
                $new_content .= $matches[1];
            }
        } else {
            /* Format and append to content */
            $new_content .= wptexturize( wpautop( $piece ) );
        }
    }

    // Remove the invalid closing </p>
    $new_content = preg_replace('@</p>(\r|\n|\r\n|)</p>@', '</p>', $new_content);

    return $new_content;
} // e:wpop_filter_autop()

// Remove the 2 main auto-formatters, so the shortcode content have a valid result.
remove_filter('the_content', 'wpautop');
remove_filter('the_content', 'wptexturize');

// Before displaying for viewing, apply this function
add_filter('the_content', 'wpop_filter_autop', 99);
add_filter('widget_text', 'wpop_filter_autop', 99);

// Enable shortcodes in widget
add_filter( 'widget_text', 'do_shortcode' );

/**
 * Resize the image smart and effectively.
 *
 * @since 1.0-beta5
 */
function wpop_filter_image_downsize( $ignore, $id, $size = 'medium' ) {
    global $_wp_additional_image_sizes;

    $meta = wp_get_attachment_metadata( $id );
    $url = wp_get_attachment_url( $id );

    $height = $width = 0;
    $crop = true;
    if ( is_array( $size ) ) {
        if ( isset( $size[0] ) && isset( $size[1] ) ) { // Indexed array given
            $width = (int) $size[0];
            $height = (int) $size[1];
            if ( $width == 9999 ) {
                $size = wp_constrain_dimensions( $meta['width'], $meta['height'], 0, (int) $height );
                list($width, $height) = $size;
            } else if ( $height == 9999 ) {
                $size = wp_constrain_dimensions( $meta['width'], $meta['height'], (int) $width );
                list($width, $height) = $size;
            }
        } else if ( array_key_exists( 'width', $size ) && array_key_exists( 'height', $size ) ) { // Associated array given
            $width = (int) $size['width'];
            $height = (int) $size['height'];
            $size = array( $width, $height ); // Assign as indexed array for resize if needed
        } else if ( array_key_exists( 'width', $size ) || array_key_exists( 'height', $size ) ) { // One key of associated array given
            if ( array_key_exists( 'width', $size ) ) { // Resize by width
                $size = wp_constrain_dimensions( $meta['width'], $meta['height'], (int) $size['width'] );
                list($width, $height) = $size;
            } else { // Resize by height
                $size = wp_constrain_dimensions( $meta['width'], $meta['height'], 0, (int) $size['height'] );
                list($width, $height) = $size;
            }
        }

        if ( $meta['width'] == $width && $meta['height'] == $height ) {
            return array( $url, $width, $height, true );
        }

        if ( isset( $meta['sizes'] ) ) {
            foreach ( $meta['sizes'] as $image ) {
                if ( $image['width'] == $width && $image['height'] == $height ) {
                    $url = str_replace( basename( $url ), $image['file'], $url );
                    return array( $url, $width, $height, true );
                }
            }
        }
    } else if ( is_string( $size ) && isset( $_wp_additional_image_sizes[ $size ] ) ) {
        $width = (int) $_wp_additional_image_sizes[ $size ][ 'width' ];
        $height = (int) $_wp_additional_image_sizes[ $size ][ 'height' ];
        $crop = (int) $_wp_additional_image_sizes[ $size ][ 'crop' ];
        
        // Attachment already exists with the equal size
        if ( isset( $meta['sizes'][$size] ) && $meta['sizes'][$size]['width'] == $width && $meta['sizes'][$size]['height'] == $height ) {
            return false;
        }

        // Valid dimension?
        if ( !$width || !$height ) {
            return false;
        }
    }

    // Create new attachment
    $file = get_attached_file( $id );

    // File already exists or should create a new one
    $ext = WPop_Utils::getFileExtension( $file );
    $resized = sprintf( '%s-%dx%d.%s', str_replace( ".{$ext}", '', $file ), $width, $height, $ext );
    if ( !file_exists( $resized ) ) {
        // Create new image size
        // Load wp image function
        if ( ! function_exists( 'wp_load_image' ) ) {
            require_once ABSPATH . 'wp-admin/includes/image.php';
        }

        $resized = @image_resize( $file, $width, $height, $crop);
        if ( is_wp_error( $resized ) || is_array( $resized ) ) {
            return false;
        }

        $url = str_replace( basename( $url ), basename( $resized ), $url );
    }

    // Add to sizes meta for future use and can be deleted by wp
    $size_id = '';
    if ( is_string( $size ) ) {
        $size_id = $size;
    } else {
        $size_id = "{$size[0]}x{$size[1]}";
    }
    
    $meta[ 'sizes' ][ $size_id ] = array(
        'file'    => basename( $resized ),
        'width'   => $width,
        'height'  => $height
    );

    wp_update_attachment_metadata( $id, $meta );

    return array( $url, $width, $height, true );
} 
add_filter( 'image_downsize', 'wpop_filter_image_downsize', 99, 3 );
// e:wpop_filter_image_downsize()

/**
 * Stylesheet and template filter.
 *
 * @since 1.0-beta6
 */
function wpop_filter_stylesheet_and_template( $stylesheet ) {
    if ( WPop::isMobile() && defined( 'WPOP_MOBILE_AGENT' ) ) {
        $stylesheet .= '/mobile/' . WPOP_MOBILE_AGENT;
    }
    return $stylesheet;
}
add_filter( 'stylesheet', 'wpop_filter_stylesheet_and_template', 1, 1 );
add_filter( 'template', 'wpop_filter_stylesheet_and_template', 1, 1 );
// e:wpop_filter_stylesheet_and_template()

/**
 * index_template filter.
 *
 * @since 1.0-beta6
 * @see wpop_locate_template()
 */
function wpop_filter_index_template() {
    $templates = array( 'index.php' );
    return wpop_locate_template( $templates );
}
add_filter( 'index_template', 'wpop_filter_index_template', 1 );
// e:wpop_filter_index_template();

/**
 * front_page_template filter.
 *
 * @since 1.0-beta6
 * @see wpop_locate_template()
 */
function wpop_filter_front_page_template() {
    $templates = array( 'front-page.php' );
    return wpop_locate_template( $templates );
}
add_filter( 'front_page_template', 'wpop_filter_front_page_template', 1 );
// e:wpop_filter_front_page_template()

/**
 * home_template filter.
 *
 * @since 1.0-beta6
 * @see wpop_locate_template()
 */
function wpop_filter_home_template() {
    $templates = array( 'home.php', 'index.php' );
    return wpop_locate_template( $templates );
}
add_filter( 'home_template', 'wpop_filter_home_template', 1 );
// e:wpop_filter_home_template()

/**
 * category_template filter.
 *
 * @since 1.0-beta6
 * @see wpop_locate_template()
 */
function wpop_filter_category_template() {
    $category = get_queried_object();
    $templates = array(
        "category-{$category->slug}.php",
        "category-{$category->term_id}.php",
        'category.php',
    );
    return wpop_locate_template( $templates );
}
add_filter( 'category_template', 'wpop_filter_category_template', 1 );
// e: wpop_filter_category_template()

/**
 * 404_template filter.
 *
 * @since 1.0-beta6
 * @see wpop_locate_template()
 */
function wpop_filter_404_template() {
    $templates = array( '404.php' );
    return wpop_locate_template( $templates );
}
add_filter( '404_template', 'wpop_filter_404_template', 1 );
// e:wpop_filter_404_template()

/**
 * archive_template filter.
 *
 * @since 1.0-beta6
 * @see wpop_locate_template()
 */
function wpop_filter_archive_template() {
    $post_type = get_query_var( 'post_type' );
    $templates = array();

    if ( $post_type ) { 
        $templates[] = "archive-{$post_type}.php";
    }

    $templates[] = 'archive.php';
    return wpop_locate_template( $templates );
}
add_filter( 'archive_template', 'wpop_filter_archive_template', 1 );
// e:wpop_filter_archive_template()

/**
 * author_template filter.
 *
 * @since 1.0-beta6
 * @see wpop_locate_template()
 */
function wpop_filter_author_template() {
    $author = get_queried_object();
    $templates = array(
        "author-{$author->user_nicename}.php",
        "author-{$author->ID}.php",
        'author.php'
    );
    return wpop_locate_template( $templates );
}
add_filter( 'author_template', 'wpop_filter_author_template', 1 );
// e:wpop_author_template()

/**
 * tag_template filter.
 *
 * @since 1.0-beta6
 * @see wpop_locate_template()
 */
function wpop_filter_tag_template() {
    $tag = get_queried_object();

    $templates = array();
    $templates[] = "tag-{$tag->slug}.php";
    $templates[] = "tag-{$tag->term_id}.php";
    $templates[] = 'tag.php';

    return wpop_locate_template( $templates );
}
add_filter( 'tag_template', 'wpop_filter_tag_template', 1);
// e:wpop_filter_tag_template()

/**
 * taxonomy_template filter.
 *
 * @since 1.0-beta6
 * @see wpop_locate_template()
 */
function wpop_filter_taxonomy_template() {
    $term = get_queried_object();
    $taxonomy = $term->taxonomy;

    $templates = array();
    $templates[] = "taxonomy-$taxonomy-{$term->slug}.php";
    $templates[] = "taxonomy-$taxonomy.php";
    $templates[] = 'taxonomy.php';

    return wpop_locate_template( $templates );
}
add_filter( 'taxonomy_template', 'wpop_filter_taxonomy_template', 1 );
// e:wpop_filter_taxonomy_template()

/**
 * date_template filter.
 *
 * @since 1.0-beta6
 * @see wpop_locate_template()
 */
function wpop_filter_date_template() {
    $templates = array( 'date.php' );
    return wpop_locate_template( $templates );
}
add_filter( 'date_template', 'wpop_filter_date_template', 1 );
// e:wpop_filter_date_template()

/**
 * index_template filter.
 *
 * @since 1.0-beta6
 * @see wpop_locate_template()
 */
function wpop_filter_page_template() {
    $id = get_queried_object_id();
    $template = get_post_meta($id, '_wp_page_template', true);
    $pagename = get_query_var('pagename');

    if ( !$pagename && $id > 0 ) {
        // If a static page is set as the front page, $pagename will not be set. Retrieve it from the queried object
        $post = get_queried_object();
        $pagename = $post->post_name;
    }

    if ( 'default' == $template )
        $template = '';

    $templates = array();
    if ( !empty($template) && !validate_file($template) )
        $templates[] = $template;
    if ( $pagename )
        $templates[] = "page-$pagename.php";
    if ( $id )
        $templates[] = "page-$id.php";
    $templates[] = 'page.php';

    return wpop_locate_template( $templates );
}
add_filter( 'page_template', 'wpop_filter_page_template', 1 );
// e:wpop_filter_page_template()

/**
 * paged_template filter.
 *
 * @since 1.0-beta6
 * @see wpop_locate_template()
 */
function wpop_filter_paged_template() {
    $templates = array( 'paged.php' );
    return wpop_locate_template( $templates );
}
add_filter( 'paged_template', 'wpop_filter_paged_template', 1 );
// e:wpop_filter_paged_template()

/**
 * search_template filter.
 *
 * @since 1.0-beta6
 * @see wpop_locate_template()
 */
function wpop_filter_search_template() {
    $templates = array( 'search.php' );
    return wpop_locate_template( $templates );
}
add_filter( 'search_template', 'wpop_filter_search_template', 1 );
// e:wpop_filter_search_template()

/**
 * single_template filter.
 *
 * @since 1.0-beta6
 * @see wpop_locate_template()
 */
function wpop_filter_single_template() {
    $object = get_queried_object();

    $templates = array();
    $templates[] = "single-{$object->post_type}.php";
    $templates[] = "single.php";
    return wpop_locate_template( $templates );
}
add_filter( 'single_template', 'wpop_filter_single_template', 1 );
// e:wpop_filter_single_template()

/**
 * comments_popup_template filter.
 *
 * @since 1.0-beta6
 * @see wpop_locate_template()
 */
function wpop_filter_comments_popup_template() {
    $templates = array( 'comments-popup.php' );
    return wpop_locate_template( $templates );
}
add_filter( 'comments_popup_template', 'wpop_filter_comments_popup_template', 1 );
// e:wpop_filter_comments_popup_template()

/**
 * Get the selector options.
 *
 * @since 1.0-beta5
 * @deprecated
 */
function wpop_action_ajax_get_selector_options()
{
    $source = $_GET['source'];
    $options = array();
    switch ( $source ) {
        case 'posts':
            $options = wpop_posts_options();
            break;
        case 'pages':
            $options = wpop_posts_options( 'page' );
            break;
        case 'categories':
            $options = wpop_categories_options();
            break;
        case 'tags':
            $options = wpop_tags_options();
            break;
        default:
            if ( post_type_exists( $source ) ) {
                $options = wpop_posts_options( $source );
            } elseif ( taxonomy_exists( $source ) ) {
                $options = wpop_taxonomies_options( $source );
            }
          
    }

    echo json_encode( $options );
    exit;
}
add_action( 'wp_ajax_wpop_get_selector_options', 'wpop_action_ajax_get_selector_options' );
// e:wpop_action_ajax_get_selector_options()

/**
 * Get the permalink destination.
 *
 * @since 1.0-beta5
 */
function wpop_action_ajax_get_permalink_destination() {
    if ( !isset( $_GET['target'] ) || empty( $_GET[ 'target' ] ) ) {
        exit;
    }
    
    switch ( $_GET[ 'target' ] ) {
        case 'post':
            echo json_encode( wpop_posts_options() );
            break;
        case 'page':
            echo json_encode( wpop_pages_options() );
            break;
        case 'category':
            echo json_encode( wpop_categories_options() );
            break;
        case 'tag':
            echo json_encode( wpop_tags_options() );
            break;
    }

    exit;
}
add_action( 'wp_ajax_wpop_get_permalink_destination', 'wpop_action_ajax_get_permalink_destination' );
// e:wpop_action_ajax_get_permalink_destination()

/**
 * Filter the permalink to enable custom permalink.
 *
 * @return string
 * @since 1.0-beta5
 */
function wpop_filter_the_permalink()
{
    $target = get_post_meta( get_the_ID(), 'custom_permalink_target', true );
    $destination = get_post_meta( get_the_ID(), 'custom_permalink_destination', true );

    switch ( $target ) {
        case 'post':
            return get_permalink( $destination );
            break;

        case 'page':
            return get_page_link( $destination );
            break;

        case 'category':
            return get_category_link( $destination );
            break;

        case 'tag':
            return get_tag_link( $destination );
            break;
            
        case 'external':
            return $destination;
            break;

        default:
            return get_permalink();
            break;
    }
}
add_filter( 'the_permalink', 'wpop_filter_the_permalink' );
// e:wpop_filter_the_permalink()

/**
 * Output the Javascript object option available shortcodes.
 *
 * @since 1.0-beta5
 */
function wpop_action_available_shortcodes() {
    $shortcode = WPop_Shortcode::instance();
    $shortcodes = $shortcode->get();
    $menu = array();
    foreach ( $shortcodes as $info ) {
        if ( !isset( $info['show_menu'] ) || $info['show_menu'] ) {
            $menu[] = $info;
        }
    }
    
    echo '<script type="text/javascript">' . "\n"
       . 'WPop_shortcodes = ' . json_encode( $menu ) . ";\n"
       . '</script>' . "\n";
}
add_action( 'admin_head', 'wpop_action_available_shortcodes' );
// e:wpop_action_available_shortcodes()

/**
 * Output the shortcode dialog page.
 *
 * @since 1.0-beta6
 */
function wpop_action_ajax_dialog_shortcode()
{
    if ( !isset( $_GET['tag'] ) || empty( $_GET['tag'] ) ) {
        return;
    }

    $shortcode = WPop_Shortcode::instance();
    $info = $shortcode->get( $_GET['tag'] );
    if ( array_key_exists( 'atts', $info ) ) {
        foreach ( $info['atts'] as $i => $att ) {
            $info['atts'][$i]['atts']['id'] = "wpop-sc-input-{$att['name']}";
        }
    }
?>
<!DOCTYPE html>
<html lang="en-US" dir="ltr">
<head></head>
<body>
<div id="wpop-shortcode-dialog">
  <div id="wpop-shortcode-options">
    <input type="hidden" value="<?php echo $info['tag']; ?>" name="wpop-sc-tag" id="wpop-sc-tag">
    <div class="wpop-header">
      <h3><?php printf( '%s %s', __( 'Customize', WPOP_THEME_SLUG ), $info['title'] ); ?></h3>
      <div class="input">
        <input type="button" id="wpop-sc-btn-cancel" class="button" value="Cancel">
        <input type="button" id="wpop-sc-btn-insert" class="button-primary" value="Insert">
      </div>
    </div>
    <div class="wpop-content">
      <?php
      foreach ( $info['atts'] as $att ):
          WPop_UI::render( $att );
      endforeach;
      ?>
    </div>
  </div>
</div>
<?php if ( isset( $info['editor'] ) ): ?>
<script type="text/javascript" src="<?php echo $info['editor']; ?>"></script>
<?php endif; ?>
<script type="text/javascript" src="<?php echo WPOP_ASSETS; ?>/js/shortcode-editor/shortcode.dialog.js"></script>
</body>
</html>
<?php
    exit;
}
add_action( 'wp_ajax_wpop_dialog_shortcode', 'wpop_action_ajax_dialog_shortcode' );
// e:wpop_action_ajax_dialog_shortcode()
