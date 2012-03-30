var WPop_shortcode_editor = {
  shortcode: function() {
    var $dialog = WPop_shortcode_dialog.$dialog;
    var atts = {
      location:   jQuery('input[name=location]', $dialog).val(),
      width:      jQuery('input[name=width]', $dialog).val(),
      height:     jQuery('input[name=height]', $dialog).val(),
      type:       jQuery('input[name=type]:checked', $dialog).val(),
      zoom:       jQuery('select[name=zoom] option:selected', $dialog).val(),
      title:      jQuery('input[name=title]', $dialog).val(),
      icon:       jQuery('input[name=icon]', $dialog).val(),
      animation:  jQuery('input[name=animation]:checked', $dialog).val()
    }

    return WPop_shortcode_helper.make('gmaps', atts, false, jQuery('textarea[name=content]', $dialog).val(), true);
  }
};