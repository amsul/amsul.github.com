<?php
/**
 * Shortcode functions.
 *
 * @package    Wordspop
 * @subpackge  Wordspop_Framework
 * @copyright  Copyright (c) 2010-2011 Wordspop
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU GPL version 2
 * @since      1.0-beta5
 */

/**
 * Escape shortcode - [esc]
 *
 * @since 1.0-beta5
 */
function wpop_shortcode_esc( $atts, $content = null ) {
    $trans = get_html_translation_table();
    $trans['['] = '&#91;';
    $trans[']'] = '&#93;';

    return '<pre>' . strtr( shortcode_unautop( $content ), $trans ) . '</pre>'. "\n";
} // e:wpop_shortcode_esc()

/**
 * Clear shortcode - [clear]
 *
 * @since 1.0-beta5
 */
function wpop_shortcode_clear( $atts, $content = null ) {
    return '<div class="clear"></div>'. "\n";
} // e:wpop_shortcode_clear()

/**
 * Break shortcode - [break]
 *
 * @since 1.0-beta6
 */
function wpop_shortcode_break( $atts, $content = null ) {
    return '<br class="wpop-sc-break clear">'. "\n";
} // e:wpop_shortcode_break()

/**
 * Button shortcode - [button]
 *
 * @since 1.0-beta5
 */
function wpop_shortcode_button( $atts, $content = null ) {
    extract( shortcode_atts(array(
        'url'     => '#',
        'color'   => '',
        'icon'    => '',
        'size'    => 'normal',
        'align'   => 'none',
        'target'  => '_self',
    ), $atts ) );

    $class = 'wpop-sc-button';
    $style = '';
    
    // Add color
    if ( $color ) {
        $class .= ' ' . $color;
    }
    
    if ( $align != 'none' ) {
        $class .= ' align' . $align;
    }
    
    // Add icon
    if ( $icon ) {
        if ( preg_match('@^http://@i', $icon) ) { // Custom icon
            $style = sprintf( ' style="background: url(%s) left center no-repeat;"', $icon );
            $class .= ' custom';
        } else {
            $class .= ' ' . $icon;
        }
    }

    return sprintf(
        '<a class="%s" href="%s" target="%s"><span %s>%s</span></a>',
        $class, esc_url( $url ), $target, $style, do_shortcode( $content )
    );
} // e:wpop_shortcode_button()

/**
 * One half column - [one_half]
 *
 * @since 1.0-beta6
 */
function wpop_shortcode_one_half( $atts, $content = null ) {
    return '<div class="wpop-sc-one-half wpop-sc-col">' . do_shortcode( wpop_the_rawop( $content ) ) . '</div>';
} // e:wpop_shortcode_one_half()

/**
 * One half last column - [one_half_last]
 *
 * @since 1.0-beta6
 */
function wpop_shortcode_one_half_last( $atts, $content = null ) {
    return '<div class="wpop-sc-one-half wpop-sc-col last">' . do_shortcode( wpop_the_rawop( $content ) ) . '</div>'
         . '<div class="clear"></div>';
} // e:wpop_shortcode_one_half_last()

/**
 * One third column - [one_third]
 *
 * @since 1.0-beta6
 */
function wpop_shortcode_one_third( $atts, $content = null ) {
    return '<div class="wpop-sc-one-third wpop-sc-col">'. do_shortcode( wpop_the_rawop( $content ) ) . ' </div>';
} // e:wpop_shortcode_one_third()

/**
 * One third last column - [one_third_last]
 *
 * @since 1.0-beta6
 */
function wpop_shortcode_one_third_last( $atts, $content = null ) {
    return '<div class="wpop-sc-one-third wpop-sc-col last">'. do_shortcode( wpop_the_rawop( $content ) ) . ' </div>'
         . '<div class="clear"></div>';
} // e:wpop_shortcode_one_third_last()

/**
 * Two third column - [two_third]
 *
 * @since 1.0-beta6
 */
function wpop_shortcode_two_third( $atts, $content = null ) {
    return '<div class="wpop-sc-two-third wpop-sc-col">'. do_shortcode( wpop_the_rawop( $content ) ) . ' </div>';
} // e:wpop_shortcode_two_third()

/**
 * Two third last column - [two_third_last]
 *
 * @since 1.0-beta6
 */
function wpop_shortcode_two_third_last( $atts, $content = null ) {
    return '<div class="wpop-sc-two-third wpop-sc-col last">'. do_shortcode( wpop_the_rawop( $content ) ) . ' </div>'
         . '<div class="clear"></div>';
} // e:wpop_shortcode_two_third_last()

/**
 * One fourth column - [one_fourth]
 *
 * @since 1.0-beta6
 */
function wpop_shortcode_one_fourth( $atts, $content = null ) {
    return '<div class="wpop-sc-one-fourth wpop-sc-col">'. do_shortcode( wpop_the_rawop( $content ) ) . ' </div>';
} // e:wpop_shortcode_one_fourth()

/**
 * One fourth last column - [one_fourth_last]
 *
 * @since 1.0-beta6
 */
function wpop_shortcode_one_fourth_last( $atts, $content = null ) {
    return '<div class="wpop-sc-one-fourth wpop-sc-col last">'. do_shortcode( wpop_the_rawop( $content ) ) . ' </div>'
         . '<div class="clear"></div>';
} // e:wpop_shortcode_one_fourth_last()

/**
 * Three fourth column - [three_fourth]
 *
 * @since 1.0-beta6
 */
function wpop_shortcode_three_fourth( $atts, $content = null ) {
    return '<div class="wpop-sc-three-fourth wpop-sc-col">'. do_shortcode( wpop_the_rawop( $content ) ) . ' </div>';
} // e:wpop_shortcode_three_fourth()

/**
 * Three fourth last column - [three_fourth_last]
 *
 * @since 1.0-beta6
 */
function wpop_shortcode_three_fourth_last( $atts, $content = null ) {
    return '<div class="wpop-sc-three-fourth wpop-sc-col last">'. do_shortcode( wpop_the_rawop( $content ) ) . ' </div>'
         . '<div class="clear"></div>';
} // e:wpop_shortcode_three_fourth_last()

/**
 * One fifth column - [one_fifth]
 *
 * @since 1.0-beta6
 */
function wpop_shortcode_one_fifth( $atts, $content = null ) {
    return '<div class="wpop-sc-one-fifth wpop-sc-col">'. do_shortcode( wpop_the_rawop( $content ) ) . ' </div>';
} // e:wpop_shortcode_one_fifth()

/**
 * One fifth last column - [one_fifth_last]
 *
 * @since 1.0-beta6
 */
function wpop_shortcode_one_fifth_last( $atts, $content = null ) {
    return '<div class="wpop-sc-one-fifth wpop-sc-col last">'. do_shortcode( wpop_the_rawop( $content ) ) . ' </div>'
         . '<div class="clear"></div>';
} // e:wpop_shortcode_one_fifth_last()

/**
 * Two fifth column - [two_fifth]
 *
 * @since 1.0-beta6
 */
function wpop_shortcode_two_fifth( $atts, $content = null ) {
    return '<div class="wpop-sc-two-fifth wpop-sc-col">'. do_shortcode( wpop_the_rawop( $content ) ) . ' </div>';
} // e:wpop_shortcode_two_fifth()

/**
 * Two fifth last column - [two_fifth_last]
 *
 * @since 1.0-beta6
 */
function wpop_shortcode_two_fifth_last( $atts, $content = null ) {
    return '<div class="wpop-sc-two-fifth wpop-sc-col last">'. do_shortcode( wpop_the_rawop( $content ) ) . ' </div>'
         . '<div class="clear"></div>';
} // e:wpop_shortcode_two_fifth_last()

/**
 * Three fifth column - [three_fifth]
 *
 * @since 1.0-beta6
 */
function wpop_shortcode_three_fifth( $atts, $content = null ) {
    return '<div class="wpop-sc-three-fifth wpop-sc-col">'. do_shortcode( wpop_the_rawop( $content ) ) . ' </div>';
} // e:wpop_shortcode_three_fifth()

/**
 * Three fifth last column - [three_fifth]
 *
 * @since 1.0-beta6
 */
function wpop_shortcode_three_fifth_last( $atts, $content = null ) {
    return '<div class="wpop-sc-three-fifth wpop-sc-col last">'. do_shortcode( wpop_the_rawop( $content ) ) . ' </div>'
         . '<div class="clear"></div>';
} // e:wpop_shortcode_three_fifth_last()

/**
 * Four fifth column - [four_fifth]
 *
 * @since 1.0-beta6
 */
function wpop_shortcode_four_fifth( $atts, $content = null ) {
    return '<div class="wpop-sc-four-fifth wpop-sc-col">'. do_shortcode( wpop_the_rawop( $content ) ) . ' </div>';
} // e:wpop_shortcode_four_fifth()

/**
 * Four fifth last column - [four_fifth_last]
 *
 * @since 1.0-beta6
 */
function wpop_shortcode_four_fifth_last( $atts, $content = null ) {
    return '<div class="wpop-sc-four-fifth wpop-sc-col last">'. do_shortcode( wpop_the_rawop( $content ) ) . ' </div>'
         . '<div class="clear"></div>';
} // e:wpop_shortcode_four_fifth_last()

/**
 * One sixth column - [one_sixth]
 *
 * @since 1.0-beta6
 */
function wpop_shortcode_one_sixth( $atts, $content = null ) {
    return '<div class="wpop-sc-one-sixth wpop-sc-col">'. do_shortcode( wpop_the_rawop( $content ) ) . ' </div>';
} // e:wpop_shortcode_one_sixth()

/**
 * One sixth last column - [one_sixth_last]
 *
 * @since 1.0-beta6
 */
function wpop_shortcode_one_sixth_last( $atts, $content = null ) {
    return '<div class="wpop-sc-one-sixth wpop-sc-col last">'. do_shortcode( wpop_the_rawop( $content ) ) . ' </div>'
         . '<div class="clear"></div>';
} // e:wpop_shortcode_one_sixth_last()

/**
 * Five sixth column - [five_sixth]
 *
 * @since 1.0-beta6
 */
function wpop_shortcode_five_sixth( $atts, $content = null ) {
    return '<div class="wpop-sc-five-sixth wpop-sc-col">'. do_shortcode( wpop_the_rawop( $content ) ) . ' </div>';
} // e:wpop_shortcode_five_sixth()

/**
 * Five sixth last column - [five_sixth_last]
 *
 * @since 1.0-beta6
 */
function wpop_shortcode_five_sixth_last( $atts, $content = null ) {
    return '<div class="wpop-sc-five-sixth wpop-sc-col last">'. do_shortcode( wpop_the_rawop( $content ) ) . ' </div>'
         . '<div class="clear"></div>';
} // e:wpop_shortcode_five_sixth_last()

/**
 * Dropcap - [dropcap]
 *
 * @since 1.0-beta6
 */
function wpop_shortcode_dropcap( $attr, $content = null) {
    return '<span class="wpop-sc-dropcap">' . do_shortcode( $content ) . '</span>';
} // e:wpop_shortcode_dropcap()

/**
 * Highlight - [dropcap]
 *
 * @since 1.0-beta6
 */
function wpop_shortcode_highlight( $attr, $content = null) {
    return '<span class="wpop-sc-highlight">' . do_shortcode( $content ) . '</span>';
} // e:wpop_shortcode_highlight()


/**
 * Toggle shortcode - [toggle]
 *
 * @since 1.0-beta6
 */
function wpop_shortcode_toggle( $atts, $content = null) {
    extract( shortcode_atts(array(
        'title' => 'Toggle',
        'state' => '',
        'style' => ''
    ), $atts ) );
    
    $props = array(
        'class' => 'wpop-sc-toggle clearfix'
    );
    if ($state == 'opened') {
        $props['class'] .= ' opened';
    }
    if ($style == 'framed') {
        $props['class'] .= ' framed';
    }

    return sprintf(
      '<div %s>' .
      '<h4 class="toggle-title"><a href="#">%s</a></h4>' .
      '<div class="toggle-content"><div class="toggle-inner">%s</div></div>' .
      '</div>',
      WPop_Utils::atts( $props ), $title, do_shortcode( wpop_the_rawop( $content ) )
    );
} // e:wpop_shortcode_toggle()

/**
 * Tabs - [tabs]
 *
 * @since 1.0-beta6
 */
function wpop_shortcode_tabs( $atts, $content ){
    $GLOBALS['tabs'] = array();

    do_shortcode( $content );

    $res  = '<div class="wpop-sc-tabs">';
    $res .= '<div class="wpop-tabs">';
    $tabs = $panes = array();
    foreach ( $GLOBALS['tabs'] as $i => $tab ) {
        $current = '';
        if ( $i == 0 ) {
            $current = 'class="current"';
        }
        $tabs[] = sprintf( '<li %s><a href="#">%s</a></li>', $current, $tab['title'] );
        $panes[] = sprintf( '<div class="tab-content" style="display: none;">%s</div>', $tab['content'] );
    }
    $res .= '<ul class="tabs-items clearfix">' . implode( "\n", $tabs ) . '</ul>';
    $res .= '<div class="tabs-panes">' . implode( "\n", $panes ) . '</div>';
    $res .= '</div>';
    $res .= '</div>';

    return $res;
} // e:wpop_shortcode_tabs()

/**
 * Tab - [tab]
 *
 * @since 1.0-beta6
 */
function wpop_shortcode_tab( $atts, $content ){
    extract(shortcode_atts(array(
      'title' => 'Tab %d'
    ), $atts));

    $GLOBALS['tabs'][] = array(
        'title'   => sprintf( $title, count( $GLOBALS['tabs'] ) + 1 ),
        'content' => do_shortcode( wpop_the_rawop( $content ) )
    );
} // e:wpop_shortcode_tab()

/**
 * Twitter tweet shortcode - [tweet]
 *
 * @since 1.0-beta5
 */
function wpop_shortcode_tweet( $atts, $content = null ) {
    extract( shortcode_atts( array(
        'count'   => 'vertical', // vertical, horizontal, none
        'url'     => '', // the url to share
        'text'    => '', // tweet text
        'lang'    => '', // fr, de, it, ja, ko, pt, ru, es, tr (default english)
        'mention' => '', // user will be @ mentioned in the suggested Tweet
        'rel'     => '', // related account
        'desc'    => ''  // related account description
    ), $atts ) );

    $props = array();
    if ( $count ) {
        $props['data-count'] = $count;
    }
    if ( $url ) {
        $props['data-url'] = $url;
    }
    if ( $text ) {
        $props['data-text'] = $text;
    } 
    if ( $lang ) {
        $props['data-lang'] = $lang;
    }
    if ( $mention ) {
        $props['data-via'] = $mention;
    }
    if ( $rel ) {
        $props['data-related'] = $rel;
        if ( $reldesc ) {
            $props['data-related'] .= ':' . $desc;
        }
    }
    
    $attrs = '';
    foreach ( $props as $k => $v ) {
        $attrs .= sprintf( '%s="%s"', $k, $v);
    }
    
    return sprintf(
        '<div class="wpop-sc-socials">' .
        '<a href="http://twitter.com/share" class="twitter-share-button" %s>Tweet</a>' .
        '</div>',
        WPop_Utils::atts( $props )
    );
} // e:wpop_shortcode_tweet()

/**
 * Facebook like shortcode - [fblike]
 *
 * @since 1.0-beta6
 */
function wpop_shortcode_fblike( $atts, $content = null ) {
    $props = shortcode_atts(array(
        'href'        => '',
        'send'        => 'false',
        'layout'      => '',
        'show_faces'  => 'true',
        'width'       => '',
        'action'      => '',
        'colorscheme' => '',
        'font'        => '',  
        'ref'         => ''
    ), $atts);
 
    return sprintf(
        '<div class="wpop-sc-socials">' .
        '<fb:like %s></fb:like>' .
        '</div>',
        WPop_Utils::atts( $props )
    );
} // e:wpop_shortcode_fblike()

/**
 * Google +1 - [gplus1]
 *
 * @since 1.0-beta6
 */
function wpop_shortcode_gplus1( $atts, $content = null ) {
    extract( shortcode_atts( array(
        'size'    => '',
        'lang'    => '',
        'count'   => ''
    ), $atts ) );

    $props = array();
    if ($size) {
        $props['size'] = $size;
    }
    
    if ($count == 'false') {
        $props['count'] = 'false';
    }

    $res  = sprintf( '<div class="wpop-sc-socials"><g:plusone %s></g:plusone></div>', WPop_Utils::atts( $props ) );
    $res .= sprintf(
        '<script type="text/javascript">' .
        '%s' .
        '</script>',
        $lang ? 'window.___gcfg = {lang: \'' . $lang . '\'}' : ''
    );
    
    return $res;
} // e:wpop_shortcode_gplus1()

/**
 * Google Maps - [gmaps]
 *
 * @since 1.0-beta6
 */
function wpop_shortcode_gmaps( $atts, $content = null ) {
    $config = array(
        'mapType'         => array( 
            'roadmap'   => 'google.maps.MapTypeId.ROADMAP',
            'satellite' => 'google.maps.MapTypeId.SATELLITE', 
            'hybrid'    => 'google.maps.MapTypeId.HYBRID',
            'terrain'   => 'google.maps.MapTypeId.TERRAIN'
        ),
        'markerAnimation' => array( 
            'drop'      => 'google.maps.Animation.DROP',
            'bounce'    => 'google.maps.Animation.BOUNCE'
        )
    );

    $atts = shortcode_atts( array(
        'location'  => '',
        'width'     => 'auto',
        'height'    => '350px',
        'zoom'      => 15,
        'title'     => '',
        'type'      => 'roadmap',
        'icon'      => '',
        'animation' => '',
        'refresh'   => 'false'
    ), $atts );

    extract( $atts );

    if ( empty( $location ) ) {
        return;
    }
    
    $location_type = 'address';
    if ( strpos( $location, ',' ) !== false ) {
        $pieces = preg_split( '/,\s*/', $location );
        if ( count( $pieces ) == 2 && is_numeric( $pieces[0] ) && is_numeric( $pieces[1] ) ) {
            $location = implode( ',', $pieces );
            $location_type = 'latlng';
        }
    }

    // Map properties
    $props = array(
        'width'   => $width,
        'height'  => $height,
        'title'   => $title,
        'info'    => ''
    );

    // Verify a valid map type
    $props['mapType'] = 'roadmap';
    if ( array_key_exists( $type, $config['mapType'] ) ) {
        $props['mapType'] = $config['mapType'][$type];
    }

    // Verify a zoom level
    settype( $zoom, 'integer' );
    if ( $zoom < 0 ){
        $zoom = 0;
    } else if ( $zoom > 20 ) {
        $zoom = 20;
    }
    $props['zoom'] = $zoom;

    // Verify marker animation
    $props['markerAnimation'] = null;
    if ( array_key_exists( $animation, $config['markerAnimation'] ) ) {
        $props['markerAnimation'] = $config['markerAnimation'][$animation];
    }
    
    // Verify icon
    $props['icon'] = $icon ? $icon : null;
    
    if ( $content !== null && $content != '') {
        $content = '<div class="entry-content wpop-sc-gmaps-info">' . 
                   do_shortcode( wpop_the_rawop( $content ) ) .
                   '</div>';
    }

    $cacheid = md5( $location );
    $data = get_transient( $cacheid );
    if ( $data === false || $refresh == 'true' ) {
        $geocode_api = sprintf(
            'http://maps.googleapis.com/maps/api/geocode/json?sensor=true&%s',
            $location_type == 'latlng' ? "latlng={$location}" : 'address=' . urlencode( $location )
        );

        $res = @file_get_contents( $geocode_api );
        if ( $res ) {
            $res = json_decode( $res );
            if ($res->status != 'OK') {
                return;
            }

            $data = array(
                'address' => $res->results[0]->formatted_address,
                'lat'     => $res->results[0]->geometry->location->lat,
                'lng'     => $res->results[0]->geometry->location->lng,
            );

            // Stores to cache for 3 months 
            set_transient( $cacheid, $data, 3600*24*30*3 );
        }
    }

    if ( is_array( $data )  ) {
        if ( !isset( $GLOBALS['gmaps'] ) ) {
            $GLOBALS['gmaps'] = 0;
        }
        return preg_replace('|\r\n|', '', sprintf(
          '<div id="%5$s" class="wpop-sc-gmaps"><div id="%5$s-map"></div><div id="%5$s-info" style="display: none;">%4$s</div></div>' . "\n" .
          wpop_the_raw( '<script type="text/javascript">WPop_Shortcodes.doGMaps(%1$f, %2$f, %3$s, \'#%5$s\');</script>' . "\n" ),
            $data['lat'], $data['lng'], json_encode( $props), $content, "gmap-{$GLOBALS['gmaps']}"
        ));

        $GLOBALS['gmaps']++;
    }
} // e:wpop_shortcode_gmaps()

/**
 * Box - [box]
 *
 * @since 1.0-beta6
 */
function wpop_shortcode_box( $atts, $content = null ) {
    extract( shortcode_atts( array(
        'title' => '',
        'type'  => ''
    ), $atts ) );
    
    $props = array(
        'class' => 'wpop-sc-box'
    );
    if ( $type ) {
        $props['class'] .= ' ' . $type;
    }
    
    $res = sprintf( '<div %s>', WPop_Utils::atts( $props ) );
    if ( $title ) {
        $res .= sprintf( '<div class="wpop-sc-box-title">%s</div>', $title );
    }
    $res .= '<div class="wpop-sc-box-content">' . do_shortcode( $content ) . '</div>';
    $res .= '</div>';
    return $res;
}
