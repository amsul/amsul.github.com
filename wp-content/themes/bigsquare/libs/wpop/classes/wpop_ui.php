<?php
/**
 * Wordspop Framework
 *
 * @package    Wordspop
 * @subpackage Wordspop_Framework
 * @copyright  Copyright (c) 2010-2011 Wordspop
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU GPL version 2
 * @since      1.0.0-beta5
 */

/**
 * @see WPop_Utils
 */
require_once 'wpop_utils.php';

/**
 * WPop_Form
 */
require_once 'wpop_form.php';

/**
 * Wordspop user interface handler.
 *
 * @package    Wordspop
 * @subpackage Wordspop_Framework
 * @copyright  Copyright (c) 2010-2011 Wordspop
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU GPL version 2
 */
class WPop_UI
{
    /**
     * Fonts list
     *
     * @var array
     */
    public static $fonts = array(
        // Web-safe fonts
        'web-safe' => array(
            'Arial, sans-serif'                                                           => 'Arial',
            '"Arial Black", sans-serif'                                                   => 'Arial Black',
            'Calibri, Candara, Segoe, Optima, sans-serif'                                 => 'Calibri',
            '"Gill Sans", "Gill Sans MT", Calibri, sans-serif'                            => 'Gill Sans',
            'Geneva, Tahoma, Verdana, sans-serif'                                         => 'Geneva',
            'Georgia, serif'                                                              => 'Georgia',
            '"Helvetica Neue", Helvetica, sans-serif'                                     => 'Helvetica',
            'Impact, Charcoal, sans-serif'                                                => 'Impact',
            '"Lucida Grande", "Lucida Sans Unicode", "Lucida Sans", sans-serif'           => 'Lucida Grande',
            '"Myriad Pro", Myriad, sans-serif'                                            => 'Myriad Pro',
            'Tahoma, Geneva, Verdana, sans-serif'                                         => 'Tahoma',
            '"Times New Roman", serif'                                                    => 'Times New Roman',
            '"Trebuchet MS", Tahoma, sans-serif'                                          => 'Trebuchet',
            'Palatino, "Palatino Linotype", serif'                                        => 'Palatino',
            'Verdana, Geneva, sans-serif'                                                 => 'Verdana'
        ),

        // Google Webfonts
        'google-webfonts' => array(
            'google: Abel, sans-serif'                                                    => 'Abel',
            'google: Aclonia, sans-serif'                                                 => 'Aclonia',
            'google: Actor, sans-serif'                                                   => 'Actor',
            'google: Aldrich, sans-serif'                                                 => 'Aldrich',
            'google: Alice, sans-serif'                                                   => 'Alice',
            'google: Alike, sans-serif'                                                   => 'Alike',
            'google: Allan, Arial, serif'                                                 => 'Allan',
            'google: Allerta, Georgia, "Times New Roman", Arial, serif'                   => 'Allerta',
            'google: "Allerta Stencil, Georgia, "Times New Roman", Arial, serif"'         => 'Allerta Stencil',
            'google: Amaranth, sans-serif'                                                => 'Amaranth',
            'google: Andika, sans-serif'                                                  => 'Andika',
            'google: "Annie Use Your Telescope", sans-serif'                              => 'Annie Use Your Telescope',
            'google: "Anonymous Pro", Geneva, Verdana, serif'                             => 'Anonymous Pro',
            'google: Anton, Georgia, "Times New Roman", Arial, serif'                     => 'Anton',
            'google: "Architects Daughter", sans-serif'                                   => 'Architects Daughter',
            'google: Arimo, Georgia, "Times New Roman", Arial, serif'                     => 'Arimo',
            'google: Artifika, sans-serif'                                                => 'Artifika',
            'google: Arvo, Georgia, "Times New Roman", Arial, serif'                      => 'Arvo',
            'google: Asset, sans-serif'                                                   => 'Asset',
            'google: Astloch, Georgia, "Times New Roman", Arial, serif'                   => 'Astloch',
            'google: Aubrey, sans-serif'                                                  => 'Aubrey',
            'google: Bangers, sans-serif'                                                 => 'Bangers',
            'google: Bentham, Georgia, "Times New Roman", Arial, serif'                   => 'Bentham',
            'google: Bevan, Georgia, "Times New Roman", Arial, serif'                     => 'Bevan',
            'google: "Bigshot One", sans-serif'                                           => 'Bigshot One',
            'google: "Black Ops One", sans-serif'                                         => 'Black Ops One',
            'google: "Blowbly One", sans-serif'                                           => 'Blowbly One',
            'google: "Blowbly One SC", sans-serif'                                        => 'Blowbly One SC',
            'google: Brawler, sans-serif'                                                 => 'Brawler',
            'google: Buda, Georgia, "Times New Roman", Arial, serif'                      => 'Buda',
            'google: Cabin, Georgia, "Times New Roman", Arial, serif'                     => 'Cabin',
            'google: "Cabin Sketch", sans-serif'                                          => 'Cabin Sketch',
            'google: Calligraffitti, Georgia, "Times New Roman", Arial, serif'            => 'Calligraffitti',
            'google: Candal, sans-serif'                                                  => 'Candal',
            'google: Cantarell, Georgia, "Times New Roman", Arial, serif'                 => 'Cantarell',
            'google: Cardo, Georgia, "Times New Roman", Arial, serif'                     => 'Cardo',
            'google: Carme, sans-serif'                                                   => 'Carme',
            'google: "Carter One", sans-serif'                                            => 'Carter One',
            'google: Caudex, sans-serif'                                                  => 'Caudex',
            'google: "Cedarville Cursive", sans-serif'                                    => 'Cedarville Cursive',
            'google: "Cherry Cream Soda", Georgia, "Times New Roman", Arial, serif'       => 'Cherry Cream Soda',
            'google: Chewy, Georgia, "Times New Roman", Arial, serif'                     => 'Chewy',
            'google: Coda, Georgia, "Times New Roman", Arial, serif'                      => 'Coda',
            'google: "Coming Soon", Georgia, "Times New Roman", Arial, serif'             => 'Coming Soon',
            'google: Copse, Georgia, "Times New Roman", Arial, serif'                     => 'Copse',
            'google: Corben, Georgia, "Times New Roman", Arial, serif'                    => 'Corben',
            'google: Cousine, Georgia, "Times New Roman", Arial, serif'                   => 'Cousine',
            'google: Coustard, sans-serif'                                                => 'Coustard',
            'google: "Covered By Your Grace", Georgia, "Times New Roman", Arial, serif'   => 'Covered By Your Grace',
            'google: "Crafty Girls", Georgia, "Times New Roman", Arial, serif'            => 'Crafty Girls',
            'google: "Crimson Text", Georgia, "Times New Roman", Arial, serif'            => 'Crimson Text',
            'google: Crushed, Georgia, "Times New Roman", Arial, serif'                   => 'Crushed',
            'google: Cuprum, Georgia, "Times New Roman", Arial, serif'                    => 'Cuprum',
            'google: Damion, sans-serif'                                                  => 'Damion',
            'google: "Dancing Script", Georgia, "Times New Roman", Arial, serif'          => 'Dancing Script',
            'google: "Dawning of a New Day", sans-serif'                                  => 'Dawning of a New Day',
            'google: "Days One", sans-serif'                                              => 'Days One',
            'google: Delius, sans-serif'                                                  => 'Delius',
            'google: "Delius Swash Caps", sans-serif'                                     => 'Delius Swash Caps',
            'google: "Delius Unicase", sans-serif'                                        => 'Delius Unicase',
            'google: "Didact Gothic", sans-serif'                                         => 'Didact Gothic',
            'google: "Droid Sans", Georgia, "Times New Roman", Arial, serif'              => 'Droid Sans',
            'google: "Droid Sans Mono", Georgia, "Times New Roman", Arial, serif'         => 'Droid Sans Mono',
            'google: "Droid Serif", Georgia, "Times New Roman", Arial, serif'             => 'Droid Serif',
            'google: "EB Garamond", sans-serif'                                           => 'EB Garamond',
            'google: "Expletus Sans", Georgia, "Times New Roman", Arial, serif'           => 'Expletus Sans',
            'google: Federo, sans-serif'                                                  => 'Federo',
            'google: "Fontdiner Swanky", Georgia, "Times New Roman", Arial, serif'        => 'Fontdiner Swanky',
            'google: Forum, sans-serif'                                                   => 'Forum',
            'google: "Francois One", sans-serif'                                          => 'Francois One',
            'google: "Gentium Basic", sans-serif'                                         => 'Gentium Basic',
            'google: "Gentium Book Basic", sans-serif'                                    => 'Gentium Book Basic',
            'google: Geo, Georgia, "Times New Roman", Arial, serif'                       => 'Geo',
            'google: Geostar, sans-serif'                                                 => 'Geostar',
            'google: "Geostar Fill", sans-serif'                                          => 'Geostar Fill',
            'google: "Give You Glory", sans-serif'                                        => 'Give You Glory',
            'google: "Gloria Hallelujah", sans-serif'                                     => 'Gloria Hallelujah',
            'google: "Goblin One", sans-serif'                                            => 'Goblin One',
            'google: "Goudy Bookletter 1911", Georgia, "Times New Roman", Arial, serif'   => 'Goudy Bookletter 1911',
            'google: "Gravitas One", sans-serif'                                          => 'Gravitas One',
            'google: Gruppo, Georgia, "Times New Roman", Arial, serif'                    => 'Gruppo',
            'google: "Hammersmith One", sans-serif'                                       => 'Hammersmith One',
            'google: "Holtwood One SC", sans-serif'                                       => 'Holtwood One SC',
            'google: "Homemade Apple", Georgia, "Times New Roman", Arial, serif'          => 'Homemade Apple',
            'google: "IM Fell DW Pica", sans-serif'                                       => 'IM Fell DW Pica',
            'google: "IM Fell DW Pica SC", sans-serif'                                    => 'IM Fell DW Pica SC',
            'google: "IM Fell Double Pica", sans-serif'                                   => 'IM Fell Double Pica',
            'google: "IM Fell Double Pica SC", sans-serif'                                => 'IM Fell Double Pica SC',
            'google: "IM Fell English", sans-serif'                                       => 'IM Fell English',
            'google: "IM Fell English SC", sans-serif'                                    => 'IM Fell English SC',
            'google: "IM Fell French Canon", sans-serif'                                  => 'IM Fell French Canon',
            'google: "IM Fell French Canon SC", sans-serif'                               => 'IM Fell French Canon SC',
            'google: "IM Fell Great Primer", sans-serif'                                  => 'IM Fell Great Primer',
            'google: "IM Fell Great Primer SC", sans-serif'                               => 'IM Fell Great Primer SC',
            'google: Inconsolata, "Myriad Pro", Geneva, Arial, serif'                     => 'Inconsolata',
            'google: "Indie Flower", sans-serif'                                          => 'Indie Flower',
            'google: "Irish Grover", Georgia, "Times New Roman", Arial, serif'            => 'Irish Grover',
            'google: "Istok Web", sans-serif'                                             => 'Istok Web',
            'google: "Josefin Sans", Georgia, "Times New Roman", Arial, serif'            => 'Josefin Sans',
            'google: "Josefin Slab", Georgia, "Times New Roman", Arial, serif'            => 'Josefin Slab',
            'google: Judson, sans-serif'                                                  => 'Judson',
            'google: Jura, sans-serif'                                                    => 'Jura',
            'google: "Just Another Hand", Georgia, "Times New Roman", Arial, serif'       => 'Just Another Hand',
            'google: "Just Me Again Down Here", Georgia, "Times New Roman", Arial, serif' => 'Just Me Again Down Here',
            'google: Kameron, sans-serif'                                                 => 'Kameron',
            'google: "Kelly Slab", sans-serif'                                            => 'Kelly Slab',
            'google: Kenia, Georgia, "Times New Roman", Arial, serif'                     => 'Kenia',
            'google: Kranky, Georgia, "Times New Roman", Arial, serif'                    => 'Kranky',
            'google: Kreon, Georgia, "Times New Roman", Arial, serif'                     => 'Kreon',
            'google: Kristi, Georgia, "Times New Roman", Arial, serif'                    => 'Kristi',
            'google: "La Belle Aurore", sans-serif'                                       => 'La Belle Aurore',
            'google: Lato, Georgia, "Times New Roman", Arial, serif'                      => 'Lato',
            'google: "League Script", sans-serif'                                         => 'League Script',
            'google: "Leckerli One", sans-serif'                                          => 'Leckerli One',
            'google: Lekton, Georgia, "Times New Roman", Arial, serif'                    => 'Lekton',
            'google: Limelight, sans-serif'                                               => 'Limelight',
            'google: Lobster, Georgia, "Times New Roman", Arial, serif'                   => 'Lobster',
            'google: "Lobster Two", sans-serif'                                           => 'Lobster Two',
            'google: Lora, sans-serif'                                                    => 'Lora',
            'google: "Love Ya Like A Sister", sans-serif'                                 => 'Love Ya Like A Sister',
            'google: "Loved by the King", sans-serif'                                     => 'Loved by the King',
            'google: "Luckiest Guy", Georgia, "Times New Roman", Arial, serif'            => 'Luckiest Guy',
            'google: "Maiden Orange", sans-serif'                                         => 'Maiden Orange',
            'google: Mako, sans-serif'                                                    => 'Mako',
            'google: Marvel, sans-serif'                                                  => 'Marvel',
            'google: "Maven Pro", sans-serif'                                             => 'Maven Pro',
            'google: Meddon, Georgia, "Times New Roman", Arial, serif'                    => 'Meddon',
            'google: MedievalSharp, sans-serif'                                           => 'MedievalSharp',
            'google: Megrim, sans-serif'                                                  => 'Megrim',
            'google: Merriweather, Georgia, "Times New Roman", Arial, serif'              => 'Merriweather',
            'google: Metrophobic, sans-serif'                                             => 'Metrophobic',
            'google: Michroma, sans-serif'                                                => 'Michroma',
            'google: Miltonian, sans-serif'                                               => 'Miltonian',
            'google: "Miltonian Tattoo", sans-serif'                                      => 'Miltonian Tattoo',
            'google: "Modern Antiqua", sans-serif'                                        => 'Modern Antiqua',
            'google: Molengo, Georgia, "Times New Roman", Arial, serif'                   => 'Molengo',
            'google: Monofett, sans-serif'                                                => 'Monofett',
            'google: Monoton, sans-serif'                                                 => 'Monoton',
            'google: Montez, sans-serif'                                                  => 'Montez',
            'google: "Mountains of Christmas", Georgia, "Times New Roman", Arial, serif'  => 'Mountains of Christmas',
            'google: Muli, sans-serif'                                                    => 'Muli',
            'google: Neucha, Georgia, "Times New Roman", Arial, serif'                    => 'Neucha',
            'google: Neuton, Georgia, "Times New Roman", Arial, serif'                    => 'Neuton',
            'google: "News Cycle", sans-serif'                                            => 'News Cycle',
            'google: "Nixie One", sans-serif'                                             => 'Nixie One',
            'google: Nobile, Georgia, "Times New Roman", Arial, serif'                    => 'Nobile',
            'google: "Nothing You Could Do", sans-serif'                                  => 'Nothing You Could Do',
            'google: "Nova Cut", sans-serif'                                              => 'Nova Cut',
            'google: "Nova Flat", sans-serif'                                             => 'Nova Flat',
            'google: "Nova Mono", sans-serif'                                             => 'Nova Mono',
            'google: "Nova Round", sans-serif'                                            => 'Nova Round',
            'google: "Nova Script", sans-serif'                                           => 'Nova Script',
            'google: "Nova Slim", sans-serif'                                             => 'Nova Slim',
            'google: "Nova Square", sans-serif'                                           => 'Nova Square',
            'google: Numans, sans-serif'                                                  => 'Numans',
            'google: Nunito, sans-serif'                                                  => 'Nunito',
            'google: "OFL Sorts Mill Goudy TT", Georgia, "Times New Roman", Arial, serif' => 'OFL Sorts Mill Goudy TT',
            'google: "Old Standard TT", Georgia, "Times New Roman", Arial, serif'         => 'Old Standard TT',
            'google: "Open Sans", sans-serif'                                             => 'Open Sans',
            'google: "Open Sans Condensed", sans-serif'                                   => 'Open Sans Condensed',
            'google: Orbitron, Georgia, "Times New Roman", Arial, serif'                  => 'Orbitron',
            'google: Oswald, sans-serif'                                                  => 'Oswald',
            'google: "Over the Rainbow", sans-serif'                                      => 'Over the Rainbow',
            'google: Ovo, sans-serif'                                                     => 'Ovo',
            'google: "PT Sans", Georgia, "Times New Roman", Arial, serif'                 => 'PT Sans',
            'google: "PT Sans Caption", sans-serif'                                       => 'PT Sans Caption',
            'google: "PT Sans Narrow", sans-serif'                                        => 'PT Sans Narrow',
            'google: "PT Serif", Georgia, "Times New Roman", Arial, serif'                => 'PT Serif',
            'google: "PT Serif Caption", sans-serif'                                      => 'PT Serif Caption',
            'google: Pacifico, sans-serif'                                                => 'Pacifico',
            'google: "Patrick Hand", sans-serif'                                          => 'Patrick Hand',
            'google: "Paytone One", sans-serif'                                           => 'Paytone One',
            'google: "Permanent Marker", Georgia, "Times New Roman", Arial, serif'        => 'Permanent Marker',
            'google: Philosopher, Georgia, "Times New Roman", Arial, serif'               => 'Philosopher',
            'google: Play, sans-serif'                                                    => 'Play',
            'google: "Playfair Display", sans-serif'                                      => 'Playfair Display',
            'google: Podkova, sans-serif'                                                 => 'Podkova',
            'google: Pompiere, sans-serif'                                                => 'Pompiere',
            'google: Puritan, Georgia, "Times New Roman", Arial, serif'                   => 'Puritan',
            'google: Radley, Georgia, "Times New Roman", Arial, serif'                    => 'Radley',
            'google: Raleway, Georgia, "Times New Roman", Arial, serif'                   => 'Raleway',
            'google: Rationale, sans-serif'                                               => 'Rationale',
            'google: Redressed, sans-serif'                                               => 'Redressed',
            'google: "Reenie Beanie", Georgia, "Times New Roman", Arial, serif'           => 'Reenie Beanie',
            'google: Rochester, sans-serif'                                               => 'Rochester',
            'google: "Rock Salt", Georgia, "Times New Roman", Arial, serif'               => 'Rock Salt',
            'google: Rokkitt, sans-serif'                                                 => 'Rokkitt',
            'google: Rosario, sans-serif'                                                 => 'Rosario',
            'google: "Ruslan Display", sans-serif'                                        => 'Ruslan Display',
            'google: Schoolbell, Georgia, "Times New Roman", Arial, serif'                => 'Schoolbell',
            'google: "Shadows Into Light", sans-serif'                                    => 'Shadows Into Light',
            'google: Shanti, sans-serif'                                                  => 'Shanti',
            'google: "Short Stack", sans-serif'                                           => 'Short Stack',
            'google: "Sigmar One", sans-serif'                                            => 'Sigmar One',
            'google: "Six Caps", sans-serif'                                              => 'Six Caps',
            'google: Slackey, Georgia, "Times New Roman", Arial, serif'                   => 'Slackey',
            'google: Smokum, sans-serif'                                                  => 'Smokum',
            'google: Smythe, sans-serif'                                                  => 'Smythe',
            'google: Sniglet, Georgia, "Times New Roman", Arial, serif'                   => 'Sniglet',
            'google: Snippet, sans-serif'                                                 => 'Snippet',
            'google: "Special Elite", sans-serif'                                         => 'Special Elite',
            'google: "Stardos Stencil", sans-serif'                                       => 'Stardos Stencil',
            'google: "Sue Ellen Francisco", sans-serif'                                   => 'Sue Ellen Francisco',
            'google: Sunshiney, Georgia, "Times New Roman", Arial, serif'                 => 'Sunshiney',
            'google: "Swanky and Moo Moo", sans-serif'                                    => 'Swanky and Moo Moo',
            'google: Syncopate, Georgia, "Times New Roman", Arial, serif'                 => 'Syncopate',
            'google: Tangerine, Georgia, "Times New Roman", Arial, serif'                 => 'Tangerine',
            'google: "Tenor Sans", sans-serif'                                            => 'Tenor Sans',
            'google: "Terminal Dosis Light", sans-serif'                                  => 'Terminal Dosis Light',
            'google: "The Girl Next Door", sans-serif'                                    => 'The Girl Next Door',
            'google: Tienne, sans-serif'                                                  => 'Tienne',
            'google: Tinos, Georgia, "Times New Roman", Arial, serif'                     => 'Tinos',
            'google: "Tulpen One", sans-serif'                                            => 'Tulpen One',
            'google: Ubuntu, Georgia, "Times New Roman", Arial, serif'                    => 'Ubuntu',
            'google: Ultra, sans-serif'                                                   => 'Ultra',
            'google: UnifrakturCook, Georgia, "Times New Roman", Arial, serif'            => 'UnifrakturCook',
            'google: UnifrakturMaguntia, Georgia, "Times New Roman", Arial, serif'        => 'UnifrakturMaguntia',
            'google: Unkempt, Georgia, "Times New Roman", Arial, serif'                   => 'Unkempt',
            'google: Unna, sans-serif'                                                    => 'Unna',
            'google: VT323, Georgia, "Times New Roman", Arial, serif'                     => 'VT323',
            'google: Varela, sans-serif'                                                  => 'Varela',
            'google: "Varela Round", sans-serif'                                          => 'Varela Round',
            'google: Vibur, Georgia, "Times New Roman", Arial, serif'                     => 'Vibur',
            'google: Vidaloka, sans-serif'                                                => 'Vidaloka',
            'google: Volkhov, sans-serif'                                                 => 'Volkhov',
            'google: Vollkorn, Georgia, "Times New Roman", Arial, serif'                  => 'Vollkorn',
            'google: Voltaire, sans-serif'                                                => 'Voltaire',
            'google: "Waiting for the Sunrise", sans-serif'                               => 'Waiting for the Sunrise',
            'google: Wallpoet, sans-serif'                                                => 'Wallpoet',
            'google: "Walter Turncoat", Georgia, "Times New Roman", Arial, serif'         => 'Walter Turncoat',
            'google: "Wire One", sans-serif'                                              => 'Wire One',
            'google: "Yanone Kaffeesatz", Georgia, "Times New Roman", Arial, serif'       => 'Yanone Kaffeesatz',
            'google: Yellowtail, sans-serif'                                              => 'Yellowtail',
            'google: "Yeseva One", sans-serif'                                            => 'Yeseva One',
            'google: Zeyada, sans-serif'                                                  => 'Zeyada'
        )
    );

    /**
     * Output the ui component
     *
     * @param array $option Component option
     */
    public static function render( $option, $value = null )
    {
        switch ( $option['type'] ) {
            // system context
            case 'system':
                switch ($option['name']) {
                    case 'wpop_theme_wpop_importexport':
                        self::_importexport( $option );
                        break;

                    case 'wpop_theme_wpop_scheme':
                        self::_scheme( $option );
                        break;
                }
                break;

            // custom input elements
            case 'group':
                self::_group( $option );
                break;

            case 'upload':
                self::_section( $option, self::_upload( $option, $value ) );
                break;
            
            case 'background':
                self::_section( $option, self::_background( $option, $value ) );
                break;

            case 'character':
                self::_section( $option, self::_character( $option, $value ) );
                break;

            case 'color':
                self::_section( $option, self::_color( $option, $value ) );
                break;
                
            case 'selector':
                self::_section( $option, self::_selector( $option, $value ) );
                break;
                
            case 'link':
                self::_section( $option, self::_link( $option, $value ) );
                break;
                
            case 'date':
                self::_section( $option, self::_date( $option ), $value );
                break;

            // generic input elements
            case 'text':
            case 'textarea':
            case 'checkbox':
            case 'select':
            case 'radio':
            case 'hidden':
                self::_input( $option, $value );
                break;
        }
    }
    
    /**
     * Output the import/export interface
     *
     * @since 1.0-beta6
     */
    private static function _importExport()
    {
        $html  = '<p class="info">' . __( 'This interface provide import and export function for the options data between the installations of this theme.', WPOP_THEME_SLUG ) . '</p>';
  
        $html .= '<div class="section section-textarea">'
               . '<label class="section-label" for="wpop-theme-import">Import</label>'
               . '<div class="option">'
                . '<div class="input">'
                  . '<textarea id="wpop-theme-import" rows="10" name="wpop_theme_import" class="ignore"></textarea>'
                . '</div>'
                . '<div class="info"><span class="description">Paste the encoded data of options provided from another installations to apply into this installation.</span></div>'
                . '<div class="clear"></div>'
               . '</div>'
               . '</div>'
               . '<div class="section section-textarea">'
               . '<label class="section-label" for="wpop-theme-export">Export</label>'
               . '<div class="option">'
                . '<div class="input">'
                  . '<textarea id="wpop-theme-export" rows="10" name="wpop_theme_export" readonly="readonly" class="ignore">' . WPop_Theme::exportOptions() . '</textarea>'
                . '</div>'
                . '<div class="info"><span class="description">Copy the encoded data of options for import to another installation of this theme.</span></div>'
                . '<div class="clear"></div>'
               . '</div>'
               . '</div>';
        
        echo $html;
        
    }

    /**
     * Output the scheme chooser
     *
     * @since 1.0-beta5
     */
    private static function _scheme()
    {
        $option = array( 'name' => 'wpop_theme_wpop_scheme', 'type' => 'scheme', 'std' => 'default' );
        $value = self::_value( $option );
  
        $html  = sprintf(
            '<p class="info">' . __( 'Select the scheme would you like to use and make sure hit the Save button.<br>Get more theme visual styles by purchasing %sthe alternate scheme%s', WPOP_THEME_SLUG ) . '</p>',
            sprintf( '<a href="%s/%s" target="_blank">', WPOP_THEMES_URL, WPOP_THEME_SLUG ), '</a>'
        );

        $html .= '<div class="section section-scheme">';
        
        // value place holder
        $html .= WPop_Form::hidden( WPop_Utils::namify( $option['name'], '_' ), $value );
        
        // list of available schemes
        $schemes = WPop_Theme::availableSchemes();
        $html .= '<ul>';
        $i = 0;
        foreach ( $schemes as $id => $info ) {
            $class = $current = '';
            if ( $i >= count( $schemes ) - 2 && ( $i % 2 == 0 || $i == count( $schemes ) - 1 ) ) {
                $class = ' class="last"';
            }

            if ( $value == $id ) {
                $current = sprintf('<span class="current">%s</span>', __( 'Current', WPOP_THEME_SLUG ) );
            }

            $html .= sprintf(
                        '<li%1$s><a id="scheme-%2$s" href="#" class="scheme" title="%2$s">' .
                          '<span class="caption">%3$s</span>' . 
                          '<img src="%4$s" width="240" height="180" title="%3$s" alt="%3$s"/>%5$s</a>' .
                        '</li>',
                        $class, $id, $info['name'], $info['screenshot'], $current
                     );
            $i++;
        }
        $html .= '</ul>';

        $html .= '<div class="clear"></div>';
        $html .= '</div>';

        echo $html;
    }

    /**
     * Output the section block
     *
     * @param array $option Component options.
     * @param string $input The input component.
     */
    private static function _section( $option, $input )
    {
        $html = sprintf('<div class="section section-%s">', $option['type']); // b:section
        if ( $option['type'] != 'checkbox' && array_key_exists( 'title', $option ) ) {
            $html .= sprintf('<label for="%s" class="section-label">%s</label>', WPop_Utils::namify( $option['name'] ), esc_html( $option['title'] ) );
        }

        $html .= '<div class="option">'; // b:option

        $html .= '<div class="input">'; // b:input
        if ( $option['type'] == 'checkbox' ) {
            $html .= sprintf('%s <label for="%s" class="section-label">%s</label>', $input, WPop_Utils::namify( $option['name'] ), esc_html( $option['title'] ) );
        } else {
            $html .= $input;
        }
        $html .= '</div>'; // e:input

        if (isset($option['desc'])) {
            $html .= '<div class="info"><span class="description">' . $option['desc'] . '</span></div>';
        }

        $html .= '<div class="clear"></div>';
        $html .= '</div>'; // e:option
        $html .= '</div>'; // e:section

        echo $html;
    }

    /**
     * Output the generic input element
     *
     * @param array $option Component option.
     */
    private static function _input( $option, $value = null )
    {
        $atts = isset( $option['atts'] ) ? $option['atts'] : array();
        $atts['id'] = isset( $atts['id'] ) ?  WPop_Utils::namify( $atts['id'] ) : WPop_Utils::namify( $option['name'] );

        $input = '';
        $value = self::_value( $option, $value );
        switch ( $option['type'] ) {
            case 'text':
                $input = WPop_Form::text( $option['name'], $value, $atts );
                break;
            case 'textarea':
                $input = WPop_Form::textarea( $option['name'], $value, $atts );
                break;
            case 'hidden':
                $input = WPop_Form::hidden( $option['name'], $value, $atts );
                echo $input;
                return;
                break;
            case 'checkbox':
                $input = WPop_Form::checkbox( $option['name'], $value, $atts );
                break;
            case 'select':
                $input = WPop_Form::select( $option['name'], $option['options'], $value, $atts );
                break;
            case 'radio':
                $input = WPop_Form::radio( $option['name'], $option['options'], $value, $atts );
                break;
        }
        
        self::_section( $option, $input );
    }

    /**
     * Returns upload input element
     *
     * @param array $option Component option.
     * @return string
     */
    private static function _upload( $option, $value = null )
    {
        global $post;

        $value = self::_value( $option, $value );

        // image element
        $img = '';
        if ( $value ) {
            if ( WPop_Utils::attachmentExists( $value ) ) {
                $img = sprintf(
                          '<div><a href="%1$s" title="View full size" class="upload-fullsize" target="_blank">' .
                          '<img src="%1$s" /></a><a href="#" class="upload-remove" title="%2$s">%2$s</a>' .
                          '</div>',
                          $value, __( 'Remove', WPOP_THEME_SLUG )
                       );
            } else {
                $img = sprintf( '<div class="wpop-filenotfound">%s</div>', __( 'File not found', WPOP_THEME_SLUG ) );
            }
        }

        // image url element
        $html  = WPop_Form::text(
            $option['name'], $value, array( 'id' => WPop_Utils::namify( $option['name'], '-', false ), 'class' => 'upload-value' )
        );
        
        // upload button
        $html .= WPop_Form::button(
            WPop_Utils::namify( "{$option['name']}_upload", '_', false ), __( 'Upload', WPOP_THEME_SLUG ),
            array( 'id' => WPop_Utils::namify( "{$option['name']}-upload", '-', false ) , 'class' => 'button upload-button' )
        );

        if ( $post === null ) {
            // custom post type
            $html .= WPop_Form::hidden(
                WPop_Utils::namify( "{$option['name']}_post", '_', false ),
                WPop::getInternalPost( WPop_Utils::namify( $option['name'] ) ),
                array( 'id' => WPop_Utils::namify( "{$option['name']}-post", '-', false ) , 'class' => 'ignore' )
            );
        }

        // preview element
        $html .= sprintf( '<div id="%s" class="upload-preview">%s</div>', WPop_Utils::namify( "{$option['name']}-preview", '-', false ), $img );

        return $html;
    }

    /**
     * Returns the background input component.
     *
     * @param array $option Component option.
     * @return string
     */
    private static function _background( $option, $value = null )
    {
        $value = self::_value( $option, $value );

        $html  = '<div class="background-none">';
        $html .= WPop_Form::checkbox(
            "{$option['name']}[none]", $value['none'],
            array( 'id' => WPop_Utils::namify( "{$option['name']}[none]", '-', false ), 'class' => 'background-none-checkbox' )
        );
        $html .= sprintf( '&nbsp;<label for="%s">%s</label>', WPop_Utils::namify( "{$option['name']}[none]", '-', false ), __( 'None', WPOP_THEME_SLUG ) );
        $html .= '</div>';
    
        if ( isset( $option['options'] ) ) {
            $html .= '<div class="predefined">';
            $html .= '<label>Predefined:</label>';
            $html .= '<div class="options">';
            foreach ( $option['options'] as $v => $background ) {
                $bg = explode( '|', $background );
                $style  = isset( $bg[0] ) && $bg[0] ? sprintf( 'background-color: %s;', $bg[0]) : '';
                $style .= isset( $bg[1] ) && $bg[1] ? sprintf( 'background-image: url(%s);', $bg[1]) : '';
                $style .= isset( $bg[2] ) && $bg[2] ? sprintf( 'background-position: %s;', $bg[2]) : '';
                $style .= isset( $bg[3] ) && $bg[3] ? sprintf( 'background-repeat: %s;', $bg[3]) : '';

                $class = 'predefined-option';
                if ( isset( $value['image'] ) && isset( $bg[1] ) && $value['image'] == $bg[1]) {
                    $class .= ' current';
                }
                $html .= sprintf( '<div class="%s"><span style="%s">&nbsp;</span></div>', $class, $style );
            }
            $html .= '</div></div>';
        }

        $html .= '<label>Custom:</label>';

        // background color
        $color = array(
            'type' => 'color',
            'name' => "{$option['name']}[color]",
            'std'  => $value['color']
        );
        $html .= '<div class="wpop-color-input">' . self::_color( $color ) . '</div>';

        // background image
        $image = array(
          'type' => 'upload',
          'name' => "{$option['name']}[image]",
          'std'  => $value['image']
        );
        $html .= self::_upload( $image );
        
        // background position
        $position = array(
            'left top'      => __( 'Left Top', WPOP_THEME_SLUG ),
            'center top'    => __( 'Center Top', WPOP_THEME_SLUG ),
            'right top'     => __( 'Right Top', WPOP_THEME_SLUG ),
            'left center'   => __( 'Left Center', WPOP_THEME_SLUG ),
            'center center' => __( 'Center Center', WPOP_THEME_SLUG ),
            'right center'  => __( 'Right Center', WPOP_THEME_SLUG ),
            'left bottom'   => __( 'Left Bottom', WPOP_THEME_SLUG ),
            'center bottom' => __( 'Center Bottom', WPOP_THEME_SLUG ),
            'right bottom'  => __( 'Right Bottom', WPOP_THEME_SLUG )
        );
        $html .= WPop_Form::select(
            "{$option['name']}[position]", $position, $value['position'], array( 'class' => 'background-position' )
        );

        // background repeat
        $repeat = array(
            'no-repeat' => __( 'No Repeat', WPOP_THEME_SLUG ),
            'repeat-x'  => __( 'Repeat Horizontally', WPOP_THEME_SLUG ),
            'repeat-y'  => __( 'Repeat Vertically', WPOP_THEME_SLUG ),
            'repeat'    => __( 'Repeat Both', WPOP_THEME_SLUG )
        );
        $html .= WPop_Form::select(
            "{$option['name']}[repeat]", $repeat, $value['repeat'], array( 'class' => 'background-repeat' )
        );

        return $html;
    }

    /**
     * Color input component.
     *
     * @param array $option Component option.
     * @return string
     */
    static private function _color( $option, $value = null )
    {
        $value = self::_value( $option, $value );

        // color picker
        $color = '';
        if ( $value ) {
            $color = sprintf( ' style="background-color: %s;"', $value );
        }
        $html = sprintf(
            '<div id="%s-picker" class="wpop-colorpicker"><div%s></div></div>',
            WPop_Utils::namify( $option['name'], '-', false ), $color
        );
        
        // color hexadecimal value
        $html .= WPop_Form::text(
            $option['name'], $value, array( 'id' => WPop_Utils::namify( $option['name'], '-', false ), 'class' => 'wpop-color' )
        );

        return $html;
    }

    /**
     * Returns character input component.
     *
     * @param array $option Component option.
     * @return string
     */
    private static function _character( $option, $value = null )
    {
        $value = self::_value( $option, $value );

        // font options
        $fonts = array_merge(
            array( '' => 'Default &hellip;' ), self::$fonts['web-safe'],
            array( 'google' => '&mdash; Google Webfonts &mdash;' ), self::$fonts['google-webfonts']
        );
        $html = WPop_Form::select(
            WPop_Utils::namify( "{$option['name']}[font]", '_' ), $fonts, $value['font'],
            array( 'id' => WPop_Utils::namify( "{$option['name']}-font" ), 'class' => 'character-font' )
        );

        // size options
        $sizes = array( '' => '&hellip;' );
        for ( $i = 9; $i <= 70; $i++ ) {
            $sizes[$i] = $i;
        }
        $html .= WPop_Form::select(
            WPop_Utils::namify( "{$option['name']}[size]", '_' ), $sizes, $value['size'],
            array( 'id' => WPop_Utils::namify( "{$option['name']}-size" ), 'class' => 'character-size' )
        );

        // unit options
        $units = array( '' => '&hellip;', 'px' => 'px', 'em' => 'em', 'ex' => 'ex', '%' => '%');
        $html .= WPop_Form::select(
            WPop_Utils::namify( "{$option['name']}[unit]", '_' ), $units, $value['unit'],
            array( 'id' => WPop_Utils::namify( "{$option['name']}-unit" ), 'class' => 'character-unit' )
        );

        // style options
        $styles = array(
            ''            => '&hellip;',
            'normal'      => __( 'Normal', WPOP_THEME_SLUG ),
            'bold'        => __( 'Bold', WPOP_THEME_SLUG ),
            'italic'      => __( 'Italic', WPOP_THEME_SLUG ),
            'bold italic' => __( 'Bold Italic', WPOP_THEME_SLUG )
        );
        $html .= WPop_Form::select(
            WPop_Utils::namify( "{$option['name']}[style]", '_' ), $styles, $value['style'],
            array( 'id' => WPop_Utils::namify( "{$option['name']}-style" ), 'class' => 'character-style' )
        );

        // color
        $color = array(
            'type' => 'color',
            'name' => "{$option['name']}[color]",
            'std'  => $value['color']
        );
        $html .= self::_color( $color );
        
        // enable
        $html .= WPop_Form::checkbox(
            WPop_Utils::namify( "{$option['name']}[enable]", '_' ), $value['enable'],
            array('id' => WPop_Utils::namify( "{$option['name']}-enable" ), 'class' => 'character-enable' )
        );
        $html .= sprintf(
            '<label for="%s">%s</label>',
            WPop_Utils::namify( "{$option['name']}-enable" ), __( 'Enable', WPOP_THEME_SLUG )
        );

        return $html;
    }
    
    /**
     * Returns the selector input element.
     *
     * @param array $option Component option.
     * @return string
     */
    private static function _selector( $option, $value = null )
    {
        $value = self::_value( $option, $value );

        $sources = array( 'posts' => 'Posts', 'pages' => 'Pages', 'categories' => 'Categories', 'tags' => 'Tags' );
        if ( isset( $option['sources'] ) && is_array( $sources ) ) {
            $tmp_sources = array();
            foreach ( $option['sources'] as $source ) {
                if ( isset( $sources[$source] ) ) {
                    $tmp_sources[$source] = $sources[$source];
                } else if ( post_type_exists( $source ) ) {
                    $post_type = get_post_type_object( $source );
                    $tmp_sources[$source] = $post_type->labels->name;
                } else if ( taxonomy_exists( $source ) ) {
                    $tmp_sources[$source] = $source;
                }
            }

            $sources = $tmp_sources;
        }

        $html  = '<div class="wpop-selector">'; // b:wpop-selector

        // entries place holder
        $html .= WPop_Form::hidden(
            WPop_Utils::namify( "{$option['name']}[entries]", '_' ), $value['entries'],
            array( 'id' => WPop_Utils::namify( $option['name'] ), 'class' => 'wpop-selector-entries-value' )
        );
        $html .= '<div class="wpop-selector-action widget-top"><img class="ajax-feedback " src="./images/wpspin_light.gif"><a href="#" class="widget-action">Close</a></div>';
        $html .= '<div class="wpop-selector-entries">'
               . '<ul class="wpop-sortable">' . self::_selectorEntries( $option['name'] ) . '</ul>'
               . '</div>';
        $html .= '<div class="wpop-selector-select">'; // b:wpop-selector-select
        $html .= '<div class="section-radio wpop-selector-sources">';

        if ( count($sources) == 1 ) {
            $html .= WPop_Form::hidden(
                      "{$option['name']}[source]", $value['source'],
                      array( 'id' => WPop_Utils::namify( "{$option['name']}-source" ), 'class' => 'wpop-selector-source' )
                    );
        } else {
            $html .= WPop_Form::radio(
                      "{$option['name']}[source]", $sources, $value['source'],
                      array( 'id' => WPop_Utils::namify( "{$option['name']}-source" ), 'class' => 'wpop-selector-source' )
                    );
        }

        $html .= '</div>';
        $html .= '<select></select><input type="button" class="button" value="Add" />';
        $html .= '</div>'; // e:wpop-selector-select
        $html .= '</div>'; // e:wpop-selector
        

        return $html;
    }

    /**
     * Returns a formatted list of selector entries.
     *
     * @param array $value Selector value
     * @return string
     */
    private static function _selectorEntries( $option )
    {
        $selector = new WPop_Selector_Entries( $option );
        $entries = $selector->entries();

        $html = '';
        foreach ( $entries as $id => $title ) {
            $html .= '<li class="widget entry">' . "\n" .
                       '<div class="widget-top">' . "\n" .
                         '<div class="widget-title-action">' . "\n" .
                           sprintf( '<a class="widget-action" href="#" title="%1$s">%1$s</a>', __( 'Remove', WPOP_THEME_SLUG ) ) . "\n" .
                           '<span class="wpop-selector-entry-value" style="display: none;">' . $id . '</span>' . "\n" .
                         '</div>' . "\n" .
                         '<div class="widget-title"><h4>' . $title . '</h4></div>' . "\n" .
                       '</div>' . "\n" .
                     '</li>' . "\n";
        }

        return $html;
    }

    /**
     * Returns a link input element.
     *
     * @param array $option Component option.
     * @return string
     */
    private static function _link( $option, $value = null )
    {
        $value = self::_value( $option, $value );

        // link pseudo-class
        $link = array(
            'type' => 'color',
            'name' => "{$option['name']}[link]",
            'std'  => $value['link']
        );
        $html = '<div class="wpop-color-input"><label>Default:</label> ' . self::_color( $link ) . '</div>';
        
        // visited pseudo-class
        $visited = array(
            'type' => 'color',
            'name' => "{$option['name']}[visited]",
            'std'  => $value['visited']
        );
        $html .= '<div class="wpop-color-input"><label>Visited:</label> ' . self::_color( $visited ) . '</div>';

        // hover pseudo-class
        $hover = array(
            'type' => 'color',
            'name' => "{$option['name']}[hover]",
            'std'  => $value['hover']
        );
        $html .= '<div class="wpop-color-input"><label>Hover:</label> ' . self::_color( $hover ) . '</div>';

        // active pseudo-class
        $active = array(
            'type' => 'color',
            'name' => "{$option['name']}[active]",
            'std'  => $value['active']
        );
        $html .= '<div class="wpop-color-input"><label>Active:</label> ' . self::_color( $active ) . '</div>';

        return $html;
    }
    
    /**
     * Returns a date input element.
     *
     * @param array $option Component option.
     * @return string
     */
    static private function _date( $option, $value = null )
    {
        $value = self::_value( $option, $value );

        $months = array(
            1   => __( 'January', WPOP_THEME_SLUG ),
            2   => __( 'February', WPOP_THEME_SLUG ),
            3   => __( 'March', WPOP_THEME_SLUG ),
            4   => __( 'April', WPOP_THEME_SLUG ),
            5   => __( 'May', WPOP_THEME_SLUG ),
            6   => __( 'June', WPOP_THEME_SLUG ),
            7   => __( 'July', WPOP_THEME_SLUG ),
            8   => __( 'August', WPOP_THEME_SLUG ),
            9   => __( 'Semptember', WPOP_THEME_SLUG ),
            10  => __( 'October', WPOP_THEME_SLUG ),
            11  => __( 'November', WPOP_THEME_SLUG ),
            12  => __( 'December', WPOP_THEME_SLUG ),
        );
        $html = WPop_Form::select(
            "{$option['name']}[month]", $months, $value['month'], array( 'class' => 'date_month' )
        );
        
        $days = array();
        for ( $i = 1; $i < 31; $i++ ) {
            $days[$i] = $i;
        }
        $html .= WPop_Form::select(
            "{$option['name']}[day]", $days, $value['day'], array( 'class' => 'date_day' )
        );
        
        $html .= WPop_Form::text(
            "{$option['name']}[year]", $value['year'], array( 'class' => 'date_year' )
        );
        
        return $html;
    }

    /**
     * Echoes a group heading element.
     *
     * @param array $option Component option.
     */
    static private function _group( $option )
    {
        $html = '<div class="section-group">'
              . '<h3>' . esc_html( $option['title'] ) . '</h3>';
        
        if ( isset( $option['desc'] ) ) {
            $html .= '<p>' . $option['desc'] . '</p>';
        }
        
        $html .= '</div>';

        echo $html;
    }

    /**
     * Get the option value
     *
     * @param string $name Option name
     * @param array $option Option
     *
     * @return mixed
     * @access private
     */
    function _value( $option, $value )
    {
        $name = $option['name'];
        
        if ( $value === null ) {
            if ( trim( get_option( $name ) ) != '' ) {
                $value = maybe_unserialize( get_option( $name ) );
            } else if ( array_key_exists( 'std', $option ) ) {
                $value = $option['std'];
            }
        }

        switch ( $option['type'] ) {
            case 'character':
                $default = array(
                    'font'  => '',
                    'size'  => '',
                    'unit'  => '',
                    'style' => '',
                    'color' => '',
                    'enable' => ''
                );
            
                $value = !is_array( $value ) ? $default : array_merge( $default, $value );
                break;

            case 'selector':
                $default = array(
                    'source' => 'posts',
                    'entries' => ''
                );
                
                $value = !is_array( $value ) ? $default : array_merge( $default, $value );
                break;

            case 'background':
                $default = array(
                    'color'     => '',
                    'image'     => '',
                    'repeat'    => '',
                    'position'  => '',
                    'none'      => false
                );
                
                $value = !is_array( $value ) ? $default : array_merge( $default, $value );
                break;

            case 'link':
                $default = array(
                    'link'      => '',
                    'active'    => '',
                    'hover'     => '',
                    'visited'   => ''
                );
                
                $value = !is_array( $value ) ? $default : array_merge( $default, $value );
                break;

            case 'date':
                $default = array(
                    'month' => date( 'n' ),
                    'day'   => date( 'j' ),
                    'year'  => date( 'Y' )
                );

                $value = !is_array( $value ) ? $default : array_merge( $default, $value );
                break;
        }

        return $value;
    }
}
