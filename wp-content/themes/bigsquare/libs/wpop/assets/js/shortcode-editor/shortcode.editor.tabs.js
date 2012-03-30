/**
 * Tabs shortcode editor.
 *
 * @author      Wordspop
 * @version     1.0
 * @package     Wordspop
 * @subpackage  Wordspop_Framework
 * @license     The MIT License http://www.opensource.org/licenses/mit-license.php
 */
var WPop_shortcode_editor = {
  tabs: 0,
  init: function() {
    var self = this;
    jQuery('select[name=tabs]', WPop_shortcode_dialog.$dialog).change(function() {
      self.tabs = parseInt(jQuery('option:selected', this).val());
      self.showOptions();
    });
  },
  showOptions: function() {
    if (jQuery('#wpop-sc-tab-options', WPop_shortcode_dialog.$dialog)[0]) {
      var $options = jQuery('#wpop-sc-tab-options', WPop_shortcode_dialog.$dialog);
      $options.html('');
    } else { 
      var $options = jQuery('<div id="wpop-sc-tab-options" class="section section-text" />');
      $options.appendTo('.wpop-content', WPop_shortcode_dialog.$dialog);
    }

    for (var i = 0; i < this.tabs; i++) {
      $options.append(
        '<label class="section-label" for="wpop-sc-tab-' + i + '">Tab #' + (i+1) + ' Title</label>' +
        '<div class="option">' +
          '<div class="input"><input type="text" id="wpop-sc-tab-' + i + '" name="tab-' + i + '" class="wpop-sc-option-tab"></div>' +
        '</div>'
      );
    }
  },
  shortcode: function() {
    var tabs = [];
    jQuery('.wpop-sc-option-tab', jQuery('#wpop-sc-tab-options', WPop_shortcode_dialog.$dialog)).each(function(i) {
      tabs.push(WPop_shortcode_helper.make('tab', {'title': jQuery(this).val()}, true, 'Tab ' + i + ' content goes here', false));
    });
    if (tabs.length > 0) return WPop_shortcode_helper.make('tabs', {}, true, tabs.join("<br>\n"), true);
  }
}
