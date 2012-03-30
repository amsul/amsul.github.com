<?php
/**
 * Bundled shortcodes table list.
 *
 * @package    Wordspop
 * @subpackage Wordspop_Framework
 * @copyright  Copyright (c) 2010-2011 Wordspop
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU GPL version 2
 * @since      1.0-beta5
 */

$shortcodes = array(

    /*-- INLINE COMPONENTS
    ------------------------------------------------------------------------------*/

    // Button
    array(
        'tag'       => 'button',
        'title'     => __( 'Button', WPOP_THEME_SLUG ),
        'callback'  => 'wpop_shortcode_button',
        'atts'      => array(
            array(
                'name'      => 'url',
                'title'     => 'Link',
                'type'      => 'text',
                'std'       => '#',
                'desc'      => __( 'The button link url.', WPOP_THEME_SLUG )
            ),
            array(
                'name'      => 'content',
                'title'     => __( 'Content', WPOP_THEME_SLUG ),
                'type'      => 'textarea'
            ),
            array(
                'name'      => 'color',
                'title'     => __( 'Color', WPOP_THEME_SLUG ),
                'type'      => 'select',
                'options'   => array(
                    ''  => __( 'Default', WPOP_THEME_SLUG )
                )
            ),
            array(
                'name'    => 'size',
                'title'   => __( 'Size', WPOP_THEME_SLUG ),
                'type'    => 'radio',
                'options' => array(
                    ''        => __( 'Default', WPOP_THEME_SLUG ),
                    'small'   => __( 'Small', WPOP_THEME_SLUG ),
                    'Large'   => __( 'Large', WPOP_THEME_SLUG )
                ),
                'std'     => ''
            ),
            array(
                'name'    => 'align',
                'title'   => __( 'Align', WPOP_THEME_SLUG ),
                'type'    => 'radio',
                'options' => array(
                    ''        => __( 'Default', WPOP_THEME_SLUG ),
                    'left'    => __( 'Left', WPOP_THEME_SLUG ),
                    'center'  => __( 'Center', WPOP_THEME_SLUG ),
                    'right'   => __( 'Right', WPOP_THEME_SLUG )
                ),
                'std'     => ''
            ),
            array(
                'name'    => 'icon',
                'title'   => __( 'Icon', WPOP_THEME_SLUG ),
                'type'    => 'select',
                'options' => array(
                    ''        => __( 'None', WPOP_THEME_SLUG ),
                    'custom'  => __( 'Custom', WPOP_THEME_SLUG )
                ),
                'std'     => '',
                'desc'    => __( 'Choose the button icon.', WPOP_THEME_SLUG )
            ),
            array(
                'name'      => 'new_window',
                'title'     => __( 'Open in New Window', WPOP_THEME_SLUG ),
                'type'      => 'checkbox',
                'std'       => false
            )
        ),
        'editor'    => WPOP_ASSETS . '/js/shortcode-editor/shortcode.editor.button.js'
    ),

    /*-- TYPOGRAPHY
    ------------------------------------------------------------------------------*/
    
    // Dropcap
    array(
        'tag'         => 'dropcap',
        'callback'    => 'wpop_shortcode_dropcap',
        'title'       => __( 'Dropcap', WPOP_THEME_SLUG ),
        'menu_group'  => __( 'Typography', WPOP_THEME_SLUG ),
        'close_tag'   => true,
        'show_dialog' => false
    ),

    // Highlight
    array(
        'tag'         => 'highlight',
        'callback'    => 'wpop_shortcode_highlight',
        'title'       => __( 'Highlight', WPOP_THEME_SLUG ),
        'menu_group'  => __( 'Typography', WPOP_THEME_SLUG ),
        'close_tag'   => true,
        'show_dialog' => false
    ),
    
    /*-- LAYOUT
    ------------------------------------------------------------------------------*/

    // Toggle
    array(
        'tag'         => 'toggle',
        'title'       => __( 'Toggle', WPOP_THEME_SLUG ),
        'callback'    => 'wpop_shortcode_toggle',
        'menu_group'  => __( 'Layout', WPOP_THEME_SLUG ),
        'atts'        => array(
            array(
                'name'  => 'title',
                'title' => __( 'Title', WPOP_THEME_SLUG ),
                'type'  => 'text',
                'std'   => __( 'Toggle', WPOP_THEME_SLUG )
            ),
            array(
                'name'      => 'content',
                'title'     => __( 'Content', WPOP_THEME_SLUG ),
                'type'      => 'textarea'
            ),
            array(
                'name'  => 'state',
                'title' => __( 'Initial State', WPOP_THEME_SLUG ),
                'type'  => 'radio',
                'options' => array(
                    ''        => __( 'Closed', WPOP_THEME_SLUG ),
                    'opened'  => __( 'Opened', WPOP_THEME_SLUG )
                ),
                'std'   => ''
            ),
            array(
                'name'  => 'style',
                'title' => __( 'Style', WPOP_THEME_SLUG ),
                'type'  => 'radio',
                'options' => array(
                    ''        => __( 'Default', WPOP_THEME_SLUG ),
                    'framed'  => __( 'Framed', WPOP_THEME_SLUG )
                ),
                'std'   => ''
            )
        ),
        'editor'    => WPOP_ASSETS . '/js/shortcode-editor/shortcode.editor.toggle.js'
    ),

    // Tabs
    array(
        'tag'         => 'tabs',
        'callback'    => 'wpop_shortcode_tabs',
        'title'       => __( 'Tab', WPOP_THEME_SLUG ),
        'menu_group'  => __( 'Layout', WPOP_THEME_SLUG ),
        'atts'        => array(
            array(
                'name'    => 'tabs',
                'type'    => 'select',
                'title'   => __( 'Tabs', WPOP_THEME_SLUG ),
                'desc'    => __( 'Number of tabs.', WPOP_THEME_SLUG ),
                'options' => array(
                    ''  => __( 'Choose &hellip;', WPOP_THEME_SLUG ),
                    2   => __( 'Two', WPOP_THEME_SLUG ),
                    3   => __( 'Three', WPOP_THEME_SLUG ),
                    4   => __( 'Four', WPOP_THEME_SLUG ),
                    5   => __( 'Five', WPOP_THEME_SLUG ),
                    6   => __( 'Six', WPOP_THEME_SLUG ),
                    7   => __( 'Seven', WPOP_THEME_SLUG ),
                    8   => __( 'Eight', WPOP_THEME_SLUG ),
                    9   => __( 'Nine', WPOP_THEME_SLUG ),
                    10  => __( 'Ten', WPOP_THEME_SLUG )
                ),
                'std'     => ''
            )
        ),
        'editor'      => WPOP_ASSETS . '/js/shortcode-editor/shortcode.editor.tabs.js'
    ),
    // Tabs item
    array(
        'tag'         => 'tab',
        'callback'    => 'wpop_shortcode_tab',
        'title'       => __( 'Tab Layout', WPOP_THEME_SLUG ),
        'show_menu'   => false
    ),
    
    // Box
    array(
        'tag'         => 'box',
        'callback'    => 'wpop_shortcode_box',
        'title'       => __( 'Box', WPOP_THEME_SLUG ),
        'menu_group'  => __( 'Layout', WPOP_THEME_SLUG ),
        'atts'        => array(
            array(
                'name'  => 'title',
                'title' => __( 'Title', WPOP_THEME_SLUG ),
                'type'  => 'text',
                'std'   => '',
                'desc'  => __( 'Leave blank to be untitled box.', WPOP_THEME_SLUG )
            ),
            array(
                'name'    => 'type',
                'type'    => 'select',
                'title'   => __( 'Type', WPOP_THEME_SLUG ),
                'options' => array(
                    ''          => __( 'Normal', WPOP_THEME_SLUG )
                ),
                'std'     => ''
            ),
            array(
                'name'      => 'content',
                'title'     => __( 'Content', WPOP_THEME_SLUG ),
                'type'      => 'textarea'
            )
        ),
        'editor'    => WPOP_ASSETS . '/js/shortcode-editor/shortcode.editor.box.js'
    ),

    // Columns Layout UI
    array(
        'tag'         => 'columns',
        'title'       => __( 'Columns', WPOP_THEME_SLUG ),
        'menu_group'  => __( 'Layout', WPOP_THEME_SLUG ),
        'ui_only'     => true,
        'atts'        => array(
            array(
                'name'    => 'columns',
                'title'   => __( 'Columns', WPOP_THEME_SLUG ),
                'type'    => 'radio',
                'options' => array(
                    'two'   => __( 'Two', WPOP_THEME_SLUG ),
                    'three' => __( 'Three', WPOP_THEME_SLUG ),
                    'four'  => __( 'Four', WPOP_THEME_SLUG ),
                    'five'  => __( 'Five', WPOP_THEME_SLUG ),
                    'six'   => __( 'Six', WPOP_THEME_SLUG )
                ),
                'std'     => 'two',
                'desc'    => __( 'Number of columns.', WPOP_THEME_SLUG )
            )
        ),
        'editor'      => WPOP_ASSETS . '/js/shortcode-editor/shortcode.editor.columns.js'
    ),

    // Column layouts
    array(
        'tag'       => 'one_half',
        'callback'  => 'wpop_shortcode_one_half',
        'show_menu' => false
    ),
    array(
        'tag'       => 'one_half_last',
        'callback'  => 'wpop_shortcode_one_half_last',
        'show_menu' => false
    ),
    array(
        'tag'       => 'one_third',
        'callback'  => 'wpop_shortcode_one_third',
        'show_menu' => false
    ),
    array(
        'tag'       => 'one_third_last',
        'callback'  => 'wpop_shortcode_one_third_last',
        'show_menu' => false
    ),
    array(
        'tag'       => 'two_third',
        'callback'  => 'wpop_shortcode_two_third',
        'show_menu' => false
    ),
    array(
        'tag'       => 'two_third_last',
        'callback'  => 'wpop_shortcode_two_third_last',
        'show_menu' => false
    ),
    array(
        'tag'       => 'one_fourth',
        'callback'  => 'wpop_shortcode_one_fourth',
        'show_menu' => false
    ),
    array(
        'tag'       => 'one_fourth_last',
        'callback'  => 'wpop_shortcode_one_fourth_last',
        'show_menu' => false
    ),
    array(
        'tag'       => 'three_fourth',
        'callback'  => 'wpop_shortcode_three_fourth',
        'show_menu' => false
    ),
    array(
        'tag'       => 'three_fourth_last',
        'callback'  => 'wpop_shortcode_three_fourth_last',
        'show_menu' => false
    ),
    array(
        'tag'       => 'one_fifth',
        'callback'  => 'wpop_shortcode_one_fifth',
        'show_menu' => false
    ),
    array(
        'tag'       => 'one_fifth_last',
        'callback'  => 'wpop_shortcode_one_fifth_last',
        'show_menu' => false
    ),
    array(
        'tag'       => 'two_fifth',
        'callback'  => 'wpop_shortcode_two_fifth',
        'show_menu' => false
    ),
    array(
        'tag'       => 'two_fifth_last',
        'callback'  => 'wpop_shortcode_two_fifth_last',
        'show_menu' => false
    ),
    array(
        'tag'       => 'three_fifth',
        'callback'  => 'wpop_shortcode_three_fifth',
        'show_menu' => false
    ),
    array(
        'tag'       => 'three_fifth_last',
        'callback'  => 'wpop_shortcode_three_fifth_last',
        'show_menu' => false
    ),
    array(
        'tag'       => 'four_fifth',
        'callback'  => 'wpop_shortcode_four_fifth',
        'show_menu' => false
    ),
    array(
        'tag'       => 'four_fifth_last',
        'callback'  => 'wpop_shortcode_four_fifth_last',
        'show_menu' => false
    ),
    array(
        'tag'       => 'one_sixth',
        'callback'  => 'wpop_shortcode_one_sixth',
        'show_menu' => false
    ),
    array(
        'tag'       => 'one_sixth_last',
        'callback'  => 'wpop_shortcode_one_sixth_last',
        'show_menu' => false
    ),
    array(
        'tag'       => 'five_sixth',
        'callback'  => 'wpop_shortcode_five_sixth',
        'show_menu' => false
    ),
    array(
        'tag'       => 'five_sixth_last',
        'callback'  => 'wpop_shortcode_five_sixth_last',
        'show_menu' => false
    ),

    /* SOCIAL BUTTONS
    ------------------------------------------------------------------------------*/

    // Twitter Tweets
    array(
        'tag'         => 'tweet',
        'title'       => 'Tweet',
        'callback'    => 'wpop_shortcode_tweet',
        'menu_group'  => __( 'Social Buttons', WPOP_THEME_SLUG ),
        'atts'        => array(
            array(
                'name'    => 'count',
                'title'   => __( 'Count', WPOP_THEME_SLUG ),
                'type'    => 'radio',
                'options' => array(
                    'vertical'    => __( 'Vertical', WPOP_THEME_SLUG ),
                    'horizontal'  => __( 'Horizontal', WPOP_THEME_SLUG ),
                    'none'        => __( 'None', WPOP_THEME_SLUG )
                ),
                'std'     => 'vertical',
                'desc'    => __( 'Data count display style.', WPOP_THEME_SLUG )
            ),
            array(
                'name'    => 'url',
                'title'   => __( 'URL', WPOP_THEME_SLUG ),
                'type'    => 'text',
                'desc'    => __( 'URL to share (default: URL of page).', WPOP_THEME_SLUG )
            ),
            array(
                'name'    => 'text',
                'title'   => __( 'Text', WPOP_THEME_SLUG ),
                'type'    => 'text',
                'desc'    => __( 'Tweet text (default: title of page).', WPOP_THEME_SLUG )
            ),
            array(
                'name'    => 'lang',
                'title'   => __( 'Language', WPOP_THEME_SLUG ),
                'type'    => 'select',
                'options' => array(
                    'nl'    => __( 'Dutch', WPOP_THEME_SLUG ),
                    ''      => __( 'English', WPOP_THEME_SLUG ),
                    'fr'    => __( 'French', WPOP_THEME_SLUG ),
                    'de'    => __( 'German', WPOP_THEME_SLUG ),
                    'id'    => __( 'Indonesian', WPOP_THEME_SLUG ),
                    'it'    => __( 'Italian', WPOP_THEME_SLUG ),
                    'ja'    => __( 'Japanese', WPOP_THEME_SLUG ),
                    'ko'    => __( 'Korean', WPOP_THEME_SLUG ),
                    'pt'    => __( 'Portuguese', WPOP_THEME_SLUG ),
                    'ru'    => __( 'Russian', WPOP_THEME_SLUG ),
                    'es'    => __( 'Spanish', WPOP_THEME_SLUG ),
                    'tr'    => __( 'Turkish', WPOP_THEME_SLUG )
                ),
                'std'     => ''
            ),
            array(
                'name'    => 'mention',
                'type'    => 'text',
                'title'   => __( 'Mention', WPOP_THEME_SLUG ),
                'desc'    => __( 'User will be @ mentioned in the suggested Tweet.', WPOP_THEME_SLUG )
            ),
            array(
                'name'    => 'rel',
                'type'    => 'text',
                'title'   => __( 'Related', WPOP_THEME_SLUG ),
                'desc'    => __( 'Related account.', WPOP_THEME_SLUG )
            ),
            array(
                'name'    => 'desc',
                'type'    => 'text',
                'title'   => __( 'Description', WPOP_THEME_SLUG ),
                'desc'    => __( 'Related account description.', WPOP_THEME_SLUG )
            )
        )
    ),
    
    // Facebook Like
    array(
        'tag'         => 'fblike',
        'title'       => __( 'Facebook Like', WPOP_THEME_SLUG ),
        'callback'    => 'wpop_shortcode_fblike',
        'menu_group'  => __( 'Social Buttons', WPOP_THEME_SLUG ),
        'atts'        => array(
            array(
                'name'  => 'href',
                'title' => __( 'URL', WPOP_THEME_SLUG ),
                'type'  => 'text',
                'desc'  => __('The URL to like (defaults to the current page).', WPOP_THEME_SLUG )
            ),
            array(
                'name'  => 'send',
                'title' => 'Include Send Button',
                'type'  => 'checkbox',
                'std'   => false,
                'desc'  => __( 'Whether to include a Send button with the Like button.', WPOP_THEME_SLUG )
            ),
            array(
                'name'    => 'layout',
                'title'   => __( 'Layout Style', WPOP_THEME_SLUG ),
                'type'    => 'radio',
                'options' => array(
                    ''              => __( 'Standard', WPOP_THEME_SLUG ),
                    'button_count'  => __( 'Button Count', WPOP_THEME_SLUG ),
                    'box_count'     => __( 'Box Count', WPOP_THEME_SLUG )
                ),
                'std'     => ''
            ),
            array(
                'name'    => 'show_faces',
                'title'   => __( 'Show faces', WPOP_THEME_SLUG ),
                'type'    => 'checkbox',
                'std'     => false,
                'desc'    => __( 'Whether to display profile photos below the button (standard layout only).', WPOP_THEME_SLUG )
            ),
            array(
                'name'    => 'width',
                'title'   => __( 'Width', WPOP_THEME_SLUG ),
                'type'    => 'text',
                'desc'    => __( 'Leave blank for default.', WPOP_THEME_SLUG )
            ),
            array(
                'name'    => 'action',
                'title'   => __( 'Verb to display', WPOP_THEME_SLUG ),
                'type'    => 'radio',
                'options' => array(
                    ''            => __( 'Like', WPOP_THEME_SLUG ),
                    'recommended' => __( 'Recommended', WPOP_THEME_SLUG )
                ),
                'std'     => ''
            ),
            array(
                'name'    => 'colorscheme',
                'title'   => __( 'Color Scheme', WPOP_THEME_SLUG ),
                'type'    => 'radio',
                'options' => array(
                    ''      => __( 'Light', WPOP_THEME_SLUG ),
                    'dark'  => __( 'Dark', WPOP_THEME_SLUG )
                ),
                'std'     => ''
            ),
            array(
                'name'    => 'font',
                'title'   => __( 'Font', WPOP_THEME_SLUG ),
                'type'    => 'select',
                'options' => array(
                    ''              => __( 'Default', WPOP_THEME_SLUG ),
                    'arial'         => 'Arial',
                    'lucida grande' => 'Lucida Grande',
                    'segoe ui'      => 'Segoe Ui',
                    'tahoma'        => 'Tahoma',
                    'trebuchet ms'  => 'Trebuchet Ms',
                    'verdana'       => 'Verdana'
                ),
                'std'     => ''
            )
        ),
        'editor'    => WPOP_ASSETS . '/js/shortcode-editor/shortcode.editor.fblike.js'
    ),
    
    // Google +1
    array(
        'tag'         => 'gplus1',
        'title'       => 'Google +1',
        'callback'    => 'wpop_shortcode_gplus1',
        'menu_group'  => __( 'Social Buttons', WPOP_THEME_SLUG ),
        'atts'        => array(
            array(
                'name'    => 'size',
                'type'    => 'radio',
                'title'   => __( 'Size', WPOP_THEME_SLUG ),
                'options' => array(
                    ''        => __( 'Standard', WPOP_THEME_SLUG ),
                    'small'   => __( 'Small', WPOP_THEME_SLUG ),
                    'medium'  => __( 'Medium', WPOP_THEME_SLUG ),
                    'tall'    => __( 'Tall', WPOP_THEME_SLUG )
                ),
                'std'   => ''
            ),
            array(
                'name'  => 'lang',
                'type'  => 'select',
                'title' => __( 'Language', WPOP_THEME_SLUG ),
                'options' => array(
                    'ar'      => __( 'Arabic', WPOP_THEME_SLUG ),
                    'bg'      => __( 'Bulgarian', WPOP_THEME_SLUG ),
                    'ca'      => __( 'Catalan', WPOP_THEME_SLUG ),
                    'zh-CN'   => __( 'Chinese (Simplified)', WPOP_THEME_SLUG ),
                    'zh-TW'   => __( 'Chinese (Traditional)', WPOP_THEME_SLUG ),
                    'hr'      => __( 'Croatian', WPOP_THEME_SLUG ),
                    'cs'      => __( 'Czech', WPOP_THEME_SLUG ),
                    'da'      => __( 'Danish', WPOP_THEME_SLUG ),
                    'nl'      => __( 'Dutch', WPOP_THEME_SLUG ),
                    ''        => __( 'English (US)', WPOP_THEME_SLUG ),
                    'en-GB'   => __( 'English (UK)', WPOP_THEME_SLUG ),
                    'et'      => __( 'Estonian', WPOP_THEME_SLUG ),
                    'fil'     => __( 'Filipino', WPOP_THEME_SLUG ),
                    'fi'      => __( 'Finnish', WPOP_THEME_SLUG ),
                    'fr'      => __( 'French', WPOP_THEME_SLUG ),
                    'de'      => __( 'German', WPOP_THEME_SLUG ),
                    'el'      => __( 'Greek', WPOP_THEME_SLUG ),
                    'iw'      => __( 'Hebrew', WPOP_THEME_SLUG ),
                    'hi'      => __( 'Hindi', WPOP_THEME_SLUG ),
                    'hu'      => __( 'Hungarian', WPOP_THEME_SLUG ),
                    'id'      => __( 'Indonesian', WPOP_THEME_SLUG ),
                    'it'      => __( 'Italian', WPOP_THEME_SLUG ),
                    'ja'      => __( 'Japanese', WPOP_THEME_SLUG ),
                    'ko'      => __( 'Korean', WPOP_THEME_SLUG ),
                    'lv'      => __( 'Latvian', WPOP_THEME_SLUG ),
                    'lt'      => __( 'Lithuanian', WPOP_THEME_SLUG ),
                    'ms'      => __( 'Malay', WPOP_THEME_SLUG ),
                    'no'      => __( 'Norwegian', WPOP_THEME_SLUG ),
                    'fa'      => __( 'Persian', WPOP_THEME_SLUG ),
                    'pl'      => __( 'Polish', WPOP_THEME_SLUG ),
                    'pt-BR'   => __( 'Portuguese (Brazil)', WPOP_THEME_SLUG ),
                    'pt-PT'   => __( 'Portuguese (Portugal)', WPOP_THEME_SLUG ),
                    'ro'      => __( 'Romanian', WPOP_THEME_SLUG ),
                    'ru'      => __( 'Russian', WPOP_THEME_SLUG ),
                    'sr'      => __( 'Serbian', WPOP_THEME_SLUG ),
                    'sv'      => __( 'Swedish', WPOP_THEME_SLUG ),
                    'sk'      => __( 'Slovak', WPOP_THEME_SLUG ),
                    'sl'      => __( 'Slovenian', WPOP_THEME_SLUG ),
                    'es'      => __( 'Spanish', WPOP_THEME_SLUG ),
                    'es-419'  => __( 'Spanish (Latin America)', WPOP_THEME_SLUG ),
                    'th'      => __( 'Thai', WPOP_THEME_SLUG ),
                    'tr'      => __( 'Turkish', WPOP_THEME_SLUG ),
                    'uk'      => __( 'Ukrainian', WPOP_THEME_SLUG ),
                    'vi'      => __( 'Vietnamese', WPOP_THEME_SLUG )
                )
            ),
            array(
                'name'    => 'count',
                'type'    => 'checkbox',
                'title'   => __( 'Include Count', WPOP_THEME_SLUG ),
                'std'     => true
            )
        ),
        'editor'    => WPOP_ASSETS . '/js/shortcode-editor/shortcode.editor.gplus1.js'
    ),


    // Google Maps
    array(
        'tag'         => 'gmaps',
        'callback'    => 'wpop_shortcode_gmaps',
        'title'       => 'Google Maps',
        'atts'        => array(
            array(
                'name'  => 'location',
                'title' => __( 'Location', WPOP_THEME_SLUG ),
                'type'  => 'text',
                'desc'  => __( 'Any valid address or latitude, longitude position.', WPOP_THEME_SLUG )
            ),
            array(
                'name'      => 'content',
                'title'     => __( 'Content', WPOP_THEME_SLUG ),
                'type'      => 'textarea',
                'desc'      => __( 'HTML and shortcodes accepted.', WPOP_THEME_SLUG )
            ),
            array(
                'name'  => 'width',
                'title' => __( 'Width', WPOP_THEME_SLUG ),
                'type'  => 'text',
                'desc'  => __( 'Any acceptable CSS value. ie: 200px, 300em, 60%. Default is auto.', WPOP_THEME_SLUG )
            ),
            array(
                'name'  => 'height',
                'title' => __( 'Height', WPOP_THEME_SLUG ),
                'type'  => 'text',
                'std'   => '350px',
                'desc'  => __( 'Any acceptable CSS value. ie: 200px, 300em, 60%. Default is 350 pixels.', WPOP_THEME_SLUG )
            ),
            array(
                'name'    => 'type',
                'title'   => __( 'Type', WPOP_THEME_SLUG ),
                'type'    => 'radio',
                'options' => array(
                    'roadmap'   => __( 'Roadmap', WPOP_THEME_SLUG ),
                    'satellite' => __( 'Satellite', WPOP_THEME_SLUG ),
                    'hybrid'    => __( 'Hybrid', WPOP_THEME_SLUG ),
                    'terrain'   => __( 'Terrain', WPOP_THEME_SLUG )
                ),
                'std'     => 'roadmap',
                'desc'    => ''
            ),
            array(
                'name'  => 'zoom',
                'title' => __( 'Zoom', WPOP_THEME_SLUG ),
                'type'  => 'select',
                'options' => array(
                    0   => 0,
                    1   => 1,
                    2   => 2,
                    3   => 3,
                    4   => 4,
                    5   => 5,
                    6   => 6,
                    7   => 7,
                    8   => 8,
                    9   => 9,
                    10  => 10,
                    11  => 11,
                    12  => 12,
                    13  => 13,
                    14  => 14,
                    15  => 15,
                    16  => 16,
                    17  => 17,
                    18  => 18,
                    19  => 19,
                    20  => 20
                ),
                'std'     => 17,
                'desc'  => ''
            ),
            array(
                'name'  => 'title',
                'title' => __( 'Title', WPOP_THEME_SLUG ),
                'type'  => 'text',
                'desc'  => __( 'Marker title.', WPOP_THEME_SLUG )
            ),
            array(
                'name'    => 'icon',
                'title'   => __( 'Icon', WPOP_THEME_SLUG ),
                'type'    => 'text',
                'desc'    => __( 'Full url of custom marker icon. ie: http://example.com/images/marker.png', WPOP_THEME_SLUG )
            ),
            array(
                'name'    => 'animation',
                'title'   => __( 'Animation', WPOP_THEME_SLUG ),
                'type'    => 'radio',
                'options' => array(
                    ''        => __( 'None', WPOP_THEME_SLUG ),
                    'drop'    => __( 'Drop', WPOP_THEME_SLUG ),
                    'bounce'  => __( 'Bounce', WPOP_THEME_SLUG )
                ),
                'std'     => '',
                'desc'    => __( 'Marker animation.', WPOP_THEME_SLUG )
            )
        ),
        'editor'    => WPOP_ASSETS . '/js/shortcode-editor/shortcode.editor.gmaps.js'
    ),

    /*-- MISCELLANIOUS
    ------------------------------------------------------------------------------*/

    // Shortcode Escape
    array(
        'tag'         => 'esc',
        'callback'    => 'wpop_shortcode_esc',
        'title'       => __( 'Escape', WPOP_THEME_SLUG ),
        'close_tag'   => true,
        'menu_group'  => __( 'Miscellanious', WPOP_THEME_SLUG ),
        'show_dialog' => false
    ),

    // Clearing
    array(
        'tag'         => 'clear',
        'callback'    => 'wpop_shortcode_clear',
        'title'       => __( 'Clear', WPOP_THEME_SLUG ),
        'menu_group'  => __( 'Miscellanious', WPOP_THEME_SLUG ),
        'show_dialog' => false
    ),

    // Line break
    array(
        'tag'         => 'break',
        'callback'    => 'wpop_shortcode_break',
        'title'       => __( 'Break', WPOP_THEME_SLUG ),
        'menu_group'  => __( 'Miscellanious', WPOP_THEME_SLUG ),
        'show_dialog' => false
    )

);
