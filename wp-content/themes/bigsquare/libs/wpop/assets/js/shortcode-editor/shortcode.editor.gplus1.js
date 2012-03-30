/**
 * Google +1 shortcode editor.
 *
 * @author      Wordspop
 * @version     1.0
 * @package     Wordspop
 * @subpackage  Wordspop_Framework
 * @license     The MIT License http://www.opensource.org/licenses/mit-license.php
 */
var WPop_shortcode_editor = {
  shortcode: function() {
    var $dialog = WPop_shortcode_dialog.$dialog;
    var atts = {
      size:   jQuery('input[name=size]:checked', $dialog).val(),
      lang:   jQuery('select[name=lang] option:selected', $dialog).val(),
      count:  jQuery('input[name=count]', $dialog).attr('checked') ? '' : 'false'
    }

    return WPop_shortcode_helper.make('gplus1', atts, false);
  }
}
