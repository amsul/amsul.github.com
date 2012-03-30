var WPop_shortcode_editor = {
  shortcode: function() {
    var $dialog = WPop_shortcode_dialog.$dialog;
    var atts = {
      title:  jQuery('input[name=title]', $dialog).val(),
      state:  jQuery('input[name=state]:checked', $dialog).val(),
      style:  jQuery('input[name=style]:checked', $dialog).val()
    }

    return WPop_shortcode_helper.make('toggle', atts, true, jQuery('textarea[name=content]', $dialog).val(), true);
  }
};