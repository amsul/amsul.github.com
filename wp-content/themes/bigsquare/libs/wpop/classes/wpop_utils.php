<?php
/**
 * Wordspop Framework
 *
 * @package    Wordspop
 * @subpackage Wordspop_Framework
 * @copyright  Copyright (c) 2010-2011 Wordspop
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU GPL version 2
 * @since      1.0-beta1
 */

/**
 * Utilities functions.
 *
 * @package    Wordspop
 * @subpackage Wordspop_Framework
 * @copyright  Copyright (c) 2010-2011 Wordspop
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU GPL version 2
 * @author     Firman Wandayandi <firmanw@wordspop.com>
 */
class WPop_Utils
{
    /**
     * Get the list of files in directory
     *
     * @param   string  $dir string Directory
     * @param   array   $extension  Only file with these extensions
     * @return  array   List of files
     */
    public static function getFiles( $dir, $extensions = array() )
    {
        if ( !is_dir( $dir ) ) {
            return false;
        }

        if ( !is_readable( $dir ) ) {
            return false;
        }

        $files = array();
        $dh = dir( $dir );
        while ( false !== ( $entry = $dh->read() ) ) {
            if ( $entry != '.' && $entry != '..' ) {
                if ( !empty( $extensions ) && !in_array( self::getFileExtension( $entry ), $extensions ) ) {
                    continue;
                }

                $files[] = $dir . DIRECTORY_SEPARATOR . $entry;
            }
        }

        $dh->close();

        return $files;
    }

    /**
     * Get file extension
     *
     * @param   string  $filename Filename
     * @return  string  File extension
     * @access  public
     * @static
     */
    public static function getFileExtension( $filename )
    {
        $filename = basename( $filename );
        return strtolower( substr( $filename, strrpos( $filename, '.' ) + 1 ) );
    }

    /**
     * Subtitute a string into url friendly
     *
     * @param   string  $string     A string
     * @param   string  $separator  Separator replacemet
     * @return  string  Formatted string
     * @access  public
     * @static
     */
    public static function slugify( $string, $separator = '_' )
    {
        return rtrim( strtolower( preg_replace( '/[^a-z0-9%_\-]+/i', $separator, $string ) ),$separator );
    }

    /**
     * Find out whether attachement exists or not
     *
     * @param   string  $filename  An absolute path or an URL of filename.
     * @return  bool
     * @access  public
     * @static
     */
    public static function attachmentExists( $filename )
    {
        // Find the whether the filename parameter is an absolute path or an url.
        if ( preg_match( '%(http|https):\/\/.+\/(files|uploads)\/(\d{4})\/(\d{2})\/(.+)%', $filename, $matches ) == 1 ) {
            // Translate the url to absolute path agains WP rules
            if ( $matches[2] == 'files' ) { // Network enabled and file on the sub site.
                global $blog_id;

                $filename = WP_CONTENT_DIR . DS . 'blogs.dir' . DS . $blog_id . DS . 'files' . DS . $matches[3] . DS . $matches[4] . DS . $matches[5];
            } else { // File in main site.
                // Unset unnecessary elements
                unset( $matches[0] );
                unset( $matches[1] );

                // Prepend content directory to the beginning of array
                array_unshift( $matches, WP_CONTENT_DIR );
                $filename = implode( DS, $matches );
            }
        } else if ( preg_match( '%' . get_bloginfo( 'template_directory' ) . '%', $filename, $matches ) ) {
            $filename = TEMPLATEPATH . preg_replace( '%' . get_bloginfo( 'template_directory' ) .'%', '', $filename);
        }

        return file_exists( $filename );
    }
    
    /**
     * Get the valid name for the given text.
     *
     * @param string $text The text
     * @param string $replacement Replacement
     * @param bool $allow_array Whether array allowed or not
     *
     * @return string
     * @since 1.0-beta5
     */
    public static function namify( $text, $replacement = '-', $allow_array = true )
    {
        $text = preg_replace( '/_/', $replacement, $text );
        if ( !$allow_array ) {
            $text = preg_replace('/\]/', '', preg_replace('/\[/', $replacement, $text));
        }

        return $text;
    }

    /**
     * Creates tag attributes from an associated array.
     *
     * @param array $properties An associated array of properties
     *
     * @return string
     * @since 1.0-beta6
     */
    public static function atts( $properties )
    {
        $atts = array();
        foreach ( $properties as $name => $value ) {
            if ( $value != '') {
                $atts[] = "{$name}=\"{$value}\"";
            }
        }

        return implode(' ', $atts);
    }
}
