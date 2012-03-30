var WPop_shortcode_dialog = {
  $dialog: null,
  shortcode: {},
  init: function() {
    var self = this;
    var tag = jQuery('#wpop-sc-tag').val();
    
    self.$dialog = jQuery('#wpop-shortcode-options');

    if (typeof WPop_shortcode_editor.init == 'function') WPop_shortcode_editor.init();

    // Binds the main dialog buttons
    jQuery('#wpop-sc-btn-cancel').click(function() { self.close() });
    jQuery('#wpop-sc-btn-insert').click(function() { self.insert(); });
  },
  insert: function() {
    if (typeof WPop_shortcode_editor.shortcode != 'function') return;

    var res = WPop_shortcode_editor.shortcode();
    if (res) tinyMCE.activeEditor.execCommand( 'mceInsertContent', false, res);
    this.close();
  },
  close: function () {
    tb_remove();
    jQuery('#wpop-shortcode-dialog').remove()
  }
};

var WPop_shortcode_helper = {
  make: function(tag, atts, close_tag, content, multiline) {
    var submitted = [];

    if (typeof close_tag == 'undefined') close_tag = true;
    if (typeof content == 'undefined') content = '';
    if (typeof multiline == 'undefined') multiline = false;
    
    jQuery.each(atts, function(k, v) {
      if (v != '') submitted.push(k + '="' + v + '"');
    });

    var shortcode = '[' + tag + ']' + (multiline ? '<br>' : '');
    if (submitted.length > 0) shortcode = '[' + tag + ' ' + submitted.join(' ') + ']' + (multiline ? '<br>' : '');
    if (content || close_tag) {
      shortcode += content + (multiline ? '<br>' : '');
      shortcode += '[/' + tag + ']' + (multiline ? '<br>' : '');
    }
    return shortcode;
  }
}

jQuery(document).ready(function() {
  WPop_shortcode_dialog.init();
});