var WPop_shortcode_editor = {
  shortcode: function() {
    var $dialog = WPop_shortcode_dialog.$dialog;
    var atts = {
      url:    jQuery('input[name=url]', $dialog).val(),
      color:  jQuery('select[name=color] option:selected', $dialog).val(),
      size:   jQuery('input[name=size]:checked', $dialog).val(),
      align:  jQuery('input[name=align]:checked', $dialog).val(),
      target: jQuery('input[name=new_window]').attr('checked') ? '_blank' : '_self',
      icon:   jQuery('select[name=icon] option:selected', $dialog).val()
    }

    return WPop_shortcode_helper.make('button', atts, true, jQuery('textarea[name=content]', $dialog).val());
  }
};
