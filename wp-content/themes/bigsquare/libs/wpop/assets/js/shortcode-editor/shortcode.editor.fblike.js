var WPop_shortcode_editor = {
  shortcode: function() {
    var $dialog = WPop_shortcode_dialog.$dialog;
    var atts = {
      href:         jQuery('input[name=href]', $dialog).val(),
      send:         jQuery('input[name=send]', $dialog).attr('checked') ? 'true' : '',
      layout:       jQuery('input[name=layout]:checked', $dialog).val(),
      show_faces:   jQuery('input[name=show_faces]', $dialog).attr('checked') ? 'true' : '',
      width:        jQuery('input[name=width]', $dialog).val(),
      action:       jQuery('input[name=action]:checked', $dialog).val(),
      colorscheme:  jQuery('input[name=colorscheme]:checked', $dialog).val(),
      font:         jQuery('select[name=font] option:selected', $dialog).val()
    }

    return WPop_shortcode_helper.make('fblike', atts, false);
  }
}