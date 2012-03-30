<?php
/**
 * Wordspop functions to render page.
 *
 * @category   Wordspop
 * @package    Wordspop_Framework
 * @copyright  Copyright (c) 2010-2011 Wordspop
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU GPL version 2
 * @since      1.0-beta1
 */

/**
 * Display theme options page
 *
 * @since 1.0-beta6
 */
function wpop_page_theme_options( $theme ) {
    if ( isset( $_GET['migrate'] ) ) {
        WPop::migrate( (bool) $_GET['migrate'] );
    }

    // Works out on the options to collect the headings
    $headings = array();
    $options = $theme->options();
    foreach( $options as $i => $option ) {
        if ( $option['type'] == 'heading' || $option['type'] == 'system' ) {
            $headings[ $option['name'] ] = array(
                'title' => $option['title'],
                'icon'  => $option['icon']
            );
        }
    }

    require_once 'wpop_form.php';
    // Load the WPop_Form
    require_once 'wpop_ui.php';
?>
<div id="wpop-container" class="wrap" style="display: none;">
  <div id="wpop-message" style="display: none;"></div>
  <div id="wpop-header">
    <div class="icon32" id="icon-options-general"><br></div>
    <h2><?php echo  WPOP_THEME_NAME; ?></h2>
    <div class="clear"></div>
  </div>
  <form id="wpop-theme-settings" method="post" action="options.php">
    <?php settings_fields( 'wpop_theme_options' ) ?> 
    <div id="wpop-body"><!--b:body-->
      <div id="wpop-sidebar"><!--b:sidebar-->
        <h3><?php _e( 'Theme Options', WPOP_THEME_SLUG ); ?></h3>
        <div id="wpop-nav">
          <ul>
            <?php foreach( $headings as $name => $info ): ?>
            <li class="<?php echo $info['icon']; ?>"><a href="#<?php echo $name; ?>"><span><?php echo esc_html($info['title']); ?></span></a></li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div><!--e:sidebar-->
      <div id="wpop-main"><!--b:main-->
        

        <div id="wpop-content-header" class="wpop-bar">
          <ul id="wpop-support">
            <li><a href="<?php echo $theme->changelog; ?>" target="_blank" title="<?php _e( 'View theme changelog', WPOP_THEME_SLUG ); ?>"><?php _e('Changelog', WPOP_THEME_SLUG); ?></a></li>
            <li><a href="<?php echo $theme->doc; ?>" target="_blank" title="<?php _e( 'View theme documentation', WPOP_THEME_SLUG ); ?>"><?php _e( 'Documentation', WPOP_THEME_SLUG ); ?></a></li>
            <li><a href="<?php echo WPOP_FORUM_URL; ?>" target="_blank" title="<?php _e( 'Visit the Wordspop support forum', WPOP_THEME_SLUG ); ?>"><?php _e( 'Support Forum', WPOP_THEME_SLUG ); ?></a></li>
          </ul>
          <div class="wpop-submit"><input type="submit" class="button-primary" value="<?php _e( 'Save Changes', WPOP_THEME_SLUG ); ?>"></div>
          <div class="clear"></div>
        </div>
        <?php if ( $theme->notification() ): ?><div id="wpop-notification"><?php echo $theme->notification(); ?></div><?php endif; ?>

        <div id="wpop-content" class="wpop-content"> <!-- content -->
          <?php $heading = 0; ?>
          <?php foreach( $options as $i => $option ): ?>
            <?php if ( $option['type'] == 'heading' || $option['type'] == 'system' ): // hits the heading ?>

              <?php if ( $heading > 0 ): // close if heading greater than zero ?>
            </div> <!-- end section -->
              <?php endif; ?>

            <div id="<?php echo $option['name'] ?>" class="context"> <!-- start section -->
              <?php if ( isset( $option['desc'] ) ): ?><p class="info"><?php echo $option['desc']; ?></p><?php endif; ?>

            <?php endif; ?>
  
            <?php WPop_UI::render($option) ?>

            <?php if ( $i == count( $options ) - 1 ): // close the heading on the last option ?>
            </div> <!-- end section -->
            <?php endif; ?>

          <?php $heading++; endforeach; ?>

        </div> <!-- end content -->

        <div id="wpop-content-footer" class="wpop-bar">
          <div id="wpop-versions">
            <a href="<?php echo $theme->uri; ?>" target="_blank"><?php echo WPOP_THEME_NAME; ?></a> <?php echo WPOP_THEME_VERSION; ?> &bull;
            <a href="http://p.yusukekamiyamane.com/" target="_blank">Icons</a>
          </div>
          <div class="wpop-submit"><input type="submit" class="button-primary" value="<?php _e( 'Save Changes', WPOP_THEME_SLUG ); ?>"></div>
          <div class="clear"></div>
        </div>
      </div><!--e:main layout-->
      <div class="clear"></div>
    </div><!--e:body-->
    <div id="wpop-bottom"><!--b:bottom-->
      <?php if ( $theme->note ): ?><p class="note"><?php echo $theme->note; ?></p><?php endif; ?>
    </div><!--e:bottom -->
  </form>
  <div id="slider_entries_dummy_entry" class="widget-top" style="display: none;">
    <div class="widget-title-action">
      <a class="widget-action slider_entries_remove" href="#" title="<?php _e( 'Remove', WPOP_THEME_SLUG ); ?>"><?php _e( 'Remove', WPOP_THEME_SLUG ); ?></a>
      <span class="slider_entry_value" style="display: none;">0</span>
    </div>
    <div class="widget-title"><h4><?php _e( 'Title', WPOP_THEME_SLUG ); ?></h4></div>
  </div>
  
  <div id="wpop-selector-entry" class="widget-top" style="display: none;">
    <div class="widget-title-action">
      <a class="widget-action" href="#" title="<?php _e( 'Remove', WPOP_THEME_SLUG ); ?>"><?php _e( 'Remove', WPOP_THEME_SLUG ); ?></a>
      <span class="wpop-selector-entry-value" style="display: none;">0</span>
    </div>
    <div class="widget-title"><h4><?php _e( 'Title', WPOP_THEME_SLUG ); ?></h4></div>
  </div>
</div>
<?php
} // e:wpop_page_theme_options()

/**
 * Display available Wordspop themes
 *
 * @since 1.0-beta6
 */
function wpop_page_themes() {
    include_once ABSPATH . WPINC . DS . 'class-feed.php';
    $cache = new WP_Feed_Cache_Transient( '', md5( WPOP_FEED_THEMES_URL ), '' );
    $cache->unlink();
    $cache->load();

    $feed = fetch_feed( WPOP_FEED_THEMES_URL );
    if ( is_wp_error( $feed ) ) {
        wp_die( $feed->get_error_message() );
    }

    $items = $feed->get_items();
?>
<div class="wrap">
<div class="icon32" id="icon-wpop"><br></div>
<h2>Wordspop <?php _e( 'Themes', WPOP_THEME_SLUG ); ?> <a class="add-new-h2" href="http://wordspop.com/" target="_blank">wordspop.com</a></h2>
<p><?php _e( 'We popularize your website and take your essentials to your mobile device', WPOP_THEME_SLUG ); ?></p>
<table cellspacing="0" cellpadding="0" id="availablethemes">
<tbody>
<?php
$rows = ceil( count( $items ) / 3 );
$k = 0;
?>
  <?php for ( $i = 0; $i < $rows; $i++ ): ?>
<tr>
  <?php for ( $j = 0; $j < 3; $j++ ): ?>
  <?php
    $pos = '';
    if ( $k % 3 == 0 ) {
      $pos = ' left';
    } else if ( $k % 3 == 2 ) {
      $pos = ' right';
    }
  ?>
  <td class="available-theme<?php echo $pos ?>">
    <?php if ( isset( $items[$k] ) ): ?><?php echo $items[$k]->get_description(); ?><?php else: ?>&nbsp;<?php endif; ?>
  </td>
  <?php $k++; ?>
  <?php endfor; ?>
</tr>
  <?php endfor; ?>
</tbody>
</table>
<br class="clear">
<br class="clear">
</div>
<?php
} // e:wpop_page_themes()

/**
 * Output the page for compose the slides.
 *
 * @since 1.0-beta6
 */
function wpop_page_slides_composer() {
    $updated = 0;

    if ( isset ( $_GET['tag_ID'] )  && !empty( $_GET['tag_ID'] ) && isset( $_POST['order'] ) ) {
        $order = $_POST['order'];
        settype( $order, 'array' );
        foreach ( $order as $i => $post_ID ) {
            $post = array(
                'ID'          => $post_ID,
                'menu_order'  => $i + 1
            );
            $updated = wp_update_post( $post );
        }
    }

    $terms = get_terms( 'presentation', 'hide_empty=0' );
?>
<div id="wpop-compose-slides" class="wrap"><!--b:.wrap-->
  <div class="icon32 icon32-posts-slide" id="icon-edit"><br></div>
  <h2><?php _e( 'Compose Slides', WPOP_THEME_SLUG ); ?></h2>
  <?php if ( $updated ): ?>
  <div class="updated below-h2" id="message"><p><?php _e( 'Presentation slides updated.', WPOP_THEME_SLUG ); ?></p></div>
  <?php endif; ?>
  <div class="widget-liquid-right" style="margin: 10px 0 20px; float: none; width: auto;">
    <div class="sidebar-name" style="padding: 8px 10px; cursor: default;">
      <label>Presentation:</label>
      <select id="presentation-id" name="tag_ID">
        <option value="">Choose &hellip;</option>
        <?php foreach ( $terms as $term ): ?>
        <option value="<?php echo $term->term_id; ?>" <?php if ( @$_GET['tag_ID'] == $term->term_id ): ?>selected="selected"<?php endif; ?>><?php echo $term->name; ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="widgets-sortables" style="padding: 20px;">
      <?php if ( isset ( $_GET['tag_ID'] )  && !empty( $_GET['tag_ID'] ) ): ?>
      <form method="post" action="edit.php?post_type=slide&page=slides_composer&tag_ID=<?php echo $_GET['tag_ID']; ?>">
      <div class="description"><p><?php _e( 'Drag and drop to put them in the order you want.', WPOP_THEME_SLUG ); ?></p></div>
      <div id="presentation-slide-list">
        <?php
        // Get the right posts under specified presentation.
        $the_query = new WP_Query(array(
          'post_type' => 'slide',
          'tax_query' => array(
            array(
              'taxonomy' => 'presentation',
              'field'    => 'id',
              'terms'    => (int) $_GET['tag_ID']
            ),
          ),
          'orderby'   => 'menu_order',
          'order'     => 'ASC'
        ));
        while( $the_query->have_posts() ): $the_query->the_post();
        ?>
        <div class="widget presentation-slide">
          <div class="widget-top" style="margin-top: 0;">
            <div class="widget-title"><h4><?php the_title(); ?></h4></div>
          </div>
          <input type="hidden" name="order[]" value="<?php the_ID(); ?>">
        </div>
        <?php endwhile; ?>
      </div>
      <p class="button-controls" style="text-align: right;"><input type="submit" value="Save" class="button-primary"></p>
      </form>
      <?php else: ?>
      <div class="description">
        <p><?php _e( 'This is the interface to compose or set the display order of slides in presentation rather than
        you manually enter the order attribute on edit screen. Please keep in mind each slide may only have
        one order attribute which will be uses by any presentations its included', WPOP_THEME_SLUG ); ?>.</p>
        <p><?php _e( 'First select the presentation you want to compose above, once the slides revealed you can drag and drop to put them in the order you want.', WPOP_THEME_SLUG ); ?></p>
        <p><?php _e( 'When you have finished composing the slides, make sure you click the Save button', WPOP_THEME_SLUG ); ?></p>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div><!--e:.wrap-->
<?php
} // e:wpop_page_slides_compose()
