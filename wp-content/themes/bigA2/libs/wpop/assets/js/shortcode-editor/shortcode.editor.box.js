var WPop_shortcode_editor = {
  shortcode: function() {
    var $dialog = WPop_shortcode_dialog.$dialog;
    var atts = {
      title:  jQuery('input[name=title]', $dialog).val(),
      type:   jQuery('select[name=type] option:selected', $dialog).val()
    }

    return WPop_shortcode_helper.make('box', atts, true, jQuery('textarea[name=content]', $dialog).val());
  }
};