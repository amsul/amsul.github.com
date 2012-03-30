/**
 * Wordspop core
 * Version 1.1
 *
 * Copyright (c) 2011 Wordspop
 * Licensed under the Apache License, Version 2.0 http://www.apache.org/licenses/LICENSE-2.0
 */

(function ($) {
  WPop_Nav = {
    init: function() {
      var self = this;

      $('a', '#wpop-nav').each(function(i) {
        var context = $(this).attr('href');

        $(this).bind('click', function() {
          self.current(this);
          return false;
        });

        if (i == 0) { $(this).parent('li').addClass('current'); }
        else if (i == 1) { $(this).parent('li').addClass('alt'); $(context).hide(); }
        else { $(context).hide(); }
      });

      $('#wpop-container').fadeIn('fast', function() {
        $('#wpop-content').css('min-height', $('#wpop-sidebar').height());
      });
    },
    current: function(ref) {
      var current = $('div.context:visible', '#wpop-content');
      var $after = $(ref).parent('li').next();

      $(current).fadeOut('fast', function() {
        $('li', '#wpop-nav').removeClass('current alt');

        $(ref).parent('li').addClass('current');
        if ($after.length > 0) $after.addClass('alt');

        $($(ref).attr('href')).fadeIn('fast');
      });
    }
  };

  WPop_ColorPicker = {
    current: '',
    init: function() {
      $('.wpop-colorpicker').ColorPicker({
        onBeforeShow: function(picker) {
          WPop_ColorPicker.current = this;

          var color = $(this).children('div').css('backgroundColor');
          if (color == 'transparent') color = '#ffffff';

          $(this).ColorPickerSetColor(WPop_ColorPicker.fixRGB(color));
        },
        onShow: function (picker) {
          $(picker).fadeIn('fast');
          return false;
        },
        onHide: function (picker) {
          $(picker).fadeOut('fast');
          return false;
        },
        onChange: function (hsb, hex, rgb) {
          if (hex == 'NaNNaNNaN') return;

          $(WPop_ColorPicker.current).children('div').css('backgroundColor', '#' + hex);
          $(WPop_ColorPicker.current).next('input').attr('value','#' + hex);
        }
      });

      $('.wpop-color').bind('change keypress', function() {
        try {
          $(this).prev('div').children('div').animate({backgroundColor: $(this).val()}, 'fast');
        } catch(e) {
          $(this).prev('div').children('div').css('backgroundColor', $(this).val());
        }
      })
    },
    fixRGB: function(rgb) {
      var res = rgb.replace(/rgb\(|rgba\(|\)/g, '').split(',');
      
      color = {
        r: parseInt(res[0]),
        g: parseInt(res[1]),
        b: parseInt(res[2]),
        a: 1
      };
      if ( res[3] != null) color.a = parseFloat(res[3]);

      return color;
    },
    RGBToHex: function(color) {
      if (typeof(color) == 'string') var rgb = this.fixRGB(color);
      else var rgb = color;

      if (rgb.a != null && rgb.a == 0) return '';

      var hex = [
        rgb.r.toString(16),
        rgb.g.toString(16),
        rgb.b.toString(16)
      ];
      $.each(hex, function (nr, val) {
        if (val.length == 1) {
          hex[nr] = '0' + val;
        }
      });
      return hex.join('');
    },
    HexToRGB: function (hex) {
      var hex = parseInt(((hex.indexOf('#') > -1) ? hex.substring(1) : hex), 16);
      return {r: hex >> 16, g: (hex & 0x00FF00) >> 8, b: (hex & 0x0000FF)};
    },
    maybeHEX: function(color) {
      if (color.match(/^#/g)) return this.HexToRGB(color);
      return color;
    }
  };

  WPop_Uploader = {
    init: function() {
      $('.upload-remove').bind('click', function() {
          WPop_Uploader.remove(this);
      });

      $('.upload-button').click(function() {
        var button = this;
        var input = $(this).prev('input');
        var post_id = 0;
        var title = $($(this).parents('.option').get(0)).prev('h3').text();

        if ( $('#post_ID').length > 0 ) post_id = $('#post_ID').val();
        else post_id = $(this).next('input').val();
        
        if (title == '') title = 'Add Media';

        tb_show(title, 'media-upload.php?post_id=' + post_id + '&amp;type=image&amp;TB_iframe=1');

        window.send_to_editor = function(html) {
          tb_remove();
          var img = $('img', html).attr('src');
          $(input).val(img);
          WPop_Uploader.display(input);
        }

        return false;
      });
    },
    display: function(ref) {
      var $preview = $(ref).nextAll('div');

      $preview.html('<div style="display: none;"><a href="' + $(ref).val() + '" class="upload-fullsize" target="_blank" title="View full size"><img src="' + $(ref).val() + '" /></a><a href="#" class="upload-remove" title="Remove">Remove</a></div>');
      $('div', $preview).fadeIn();

      $('.upload-remove', $preview).bind('click', function() {
        WPop_Uploader.remove(this);
        return false;
      });
    },
    remove: function(ref) {
      var block = $(ref).parents('.input').get(0);
      $('.upload-preview div', block).fadeOut('fastx', function() {
        $(this).remove();
        $('.upload-value', block).val('');
      });
    }
  };

  WPop_Scheme = {
    init: function() {
      $('.scheme').click(function() {
        WPop_Scheme.set($(this).attr('id').substring(7));
        return false;
      });
    },
    set: function(scheme) {
      $('.section-scheme .current').fadeOut('fast', function() {
        $('.section-scheme .current').appendTo('#scheme-' + scheme).fadeIn('fast');
        $('.section-scheme input').val(scheme);
      });
    }
  };

  $.fn.WPopSelector = function(){
    this.each(function() {
      var entries = [];
      var $input = $('input.wpop-selector-entries-value', this);
      var $toggle = $('.wpop-selector-action a', this);
      var $entries = $('.wpop-selector-entries', this);
      var $list = $('ul', this);
      var $select = $('select', this);
      var $feedback = $('.ajax-feedback', this);

      // make the entries sortable
      $list.sortable({update: function(event, ui) { sort(this); } });

      // hide the entries
      $entries.hide();

      // get the options
      if ( $('.wpop-selector-source', this).attr('type') == 'radio' ) {
        var source = $(':checked', this).val();
      } else {
        var source = $('.wpop-selector-source', this).val();
      }
      options({'action': 'wpop_get_selector_options', 'source': source});

      // bind the radio sources
      $(':input.wpop-selector-source', this).bind('click', function() {
        var data = {
          'action': 'wpop_get_selector_options',
          'source': $(this).val()
        };

        $('li', $list).fadeOut('fast', function() { $list.empty(); });
        options(data);
      });

      // bind the add button
      $('input[type=button]', this).bind('click', function() {
        // show the entries if hidden
        if ($entries.css('display') == 'none') $toggle.trigger('click');

        var $selected = $(':selected', $select);

        // create entry element by clone a dummy
        var $entry = $('#wpop-selector-entry').clone().attr('id', '').show();
        $('h4', $entry).html($selected.text());
        $('.wpop-selector-entry-value', $entry).text($selected.val());

        // append to list
        $entry = $('<li></li>').wrapInner($entry);
        $entry.hide().addClass('widget').appendTo($list);
        $entry.fadeIn();

        // bind the remove button
        $('.widget-action', $entry).bind('click', function() { dispose(this); return false; });

        sort();
      });

      // bind the remove button
      $('.widget-action', $('li', $list)).bind('click', function() { dispose(this); return false; });

      // bind the show/hide the entries
      $toggle.click(function() {
        $list.parent('.wpop-selector-entries').slideToggle();
        return false;
      });

      function options(data) {
        $select.empty();

        $feedback.css('visibility', 'visible');
        $.getJSON(ajaxurl, data, function(res) {
          $feedback.css('visibility', 'hidden');

          $.each(res, function(val, text){
            var $option = $('<option value="' + val + '">' + text + '</option>');
            $option.appendTo($select);
          });
        });
      }

      function sort() {
        entries = [];
        $('.wpop-selector-entry-value', $list).each(function() {
          entries.push($(this).text());
        });

        $input.val(entries.join(','));
      }

      function dispose(ref) {
        $($(ref).parents('li').get(0)).fadeOut('normal', function() {
          $(this).remove();
          sort();
        });
      }


    });
  };

  // Theme options form
  WPop_ThemeOptions = {
    init: function() {
      var self = this;

      $('#wpop-theme-settings').submit(function() {
        var encoded = $('#wpop-theme-import', '#wpop-theme-settings').val();
        if (encoded != '') {
          self.doImport(encoded);
          return false;
        }

        WPop.flashMessage('Saving theme settings, please wait &hellip;', 'loading');
        $.post('admin-ajax.php', {
          action: 'wpop_theme_save_options',
          data: $("#wpop-theme-settings *").not( '.ignore').serialize()
        }, function(res) {
           WPop.flashMessage(res.text, res.type);
        }, 'json');

        return false;
      });
    },
    doImport: function(encoded) {
      WPop.flashMessage('Importing theme options, please wait &hellip;', 'loading');
      $.post('admin-ajax.php', {
        action: 'wpop_theme_import_options',
        data: encoded
      }, function(res) {
        if (res) WPop.flashMessage(res.text, res.type);
        if (res.type == 'succeed') document.location.replace('?page=wordspop-framework-theme');
      }, 'json');
    }
  };

  /**
   * Custom permalink
   */
  WPop_CustomPermalink = {
    $target: null,
    $select: null,
    $destination: null,
    $external: null,
    init: function() {
      var self = this;

      self.$target = $('select[name=custom_permalink_target]', '#metabox-custom-permalink');
      self.$destination = $('input[name=custom_permalink_destination]', '#metabox-custom-permalink');
      self.$select = $('select[name=custom_permalink_destination_select]', '#metabox-custom-permalink');
      self.$external = $('input[name=custom_permalink_destination_external]', '#metabox-custom-permalink');

      self.$select.parent('p').hide();
      self.$external.parent('p').hide();

      if ($(':selected', self.$target).val() != '') self.options($(':selected', self.$target).val());

      self.$target.change(function() {
        self.options($(':selected', this).val());
      })

      $('#post').submit(function() {
        if ($(':selected', self.$target).val() == 'external') self.$destination.val(self.$external.val());
        else self.$destination.val($(':selected', self.$select).val());
      })
    },
    options: function(target) {
      var self = this;

      self.$select.parent('p').slideUp();

      if (target == 'external') {
          self.$external.val(self.$destination.val());
          self.$external.parent('p').slideDown();
          return;
      } else {
          self.$external.parent('p').slideUp();
      }

      $.getJSON(
        'admin-ajax.php',
        {'action': 'wpop_get_permalink_destination', 'target': target},
        function(res) {
          if ( res == null) return;

          // Empty all childs
          self.$select.empty();

          // Add the options
          $.each(res, function(value, text) {
            $option = $('<option value="' + value + '">' + text + '</option>' + "\n");
            if (value == self.$destination.val()) $option.attr('selected', 'selected');
            $option.appendTo(self.$select);
          });

          self.$select.parent('p').slideDown();
        }
      );
    }
  };
  
  WPop_StylingBackground = {
    init: function() {
      var self = this;
      $('.predefined-option span', '.section-background').click(function() {
        self.set(this);
      });
    },
    set: function(ref) {
      $input = $($(ref).parents('.input').get(0));

      var color = $(ref).css('background-color');
      if (color == 'transparent') color = '';
      //alert(WPop_ColorPicker.maybeHEX($.dump(color)));
      $('.wpop-colorpicker', $input).children('div').css('background-color', color);
      $('.wpop-color', $input).val(color != '' ? WPop_ColorPicker.RGBToHex(WPop_ColorPicker.maybeHEX(color)) : color);

      var image = $(ref).css('background-image');
      var res = image.replace(/"([^"]*)"/g, '$1').match(/\(([\S]+)\)/);
      if (res != null) $('.upload-value', $input).val(res[1]);
      
      var position = $(ref).css('background-position');
      if (position == '0% 0%') position = 'left top';
      if (position == '100% 0%') position = 'right top';
      if (position == '50% 0%') position = 'center top';
      if (position == '0% 100%') position = 'left bottom';
      if (position == '100% 100%') position = 'right bottom';
      if (position == '50% 100%') position = 'center bottom';
      if (position == '50% 50%') position = 'center center';
      if (position) $('.background-position', $input).val(position);

      var repeat = $(ref).css('background-repeat');
      if (repeat) $('.background-repeat', $input).val(repeat);
      
      $('.predefined-option', $input).removeClass('current');
      $($(ref).parents('.predefined-option').get(0)).addClass('current');
      $('.upload-preview', $input).fadeOut(); 
    }
  };

  WPop_SlidesComposer = {
    init: function() {
      $('select[name=tag_ID]', '#wpop-compose-slides').change(function() {
        var tag_ID = $('option:selected', this).val();
        if (tag_ID) document.location.replace('./edit.php?post_type=slide&page=slides_composer&tag_ID=' + tag_ID);
      });
      
      $('#presentation-slide-list', '#wpop-compose-slides').sortable({
        axis: 'y',
        containment: 'parent',
        items: '.presentation-slide'
      });
    }
  }

  WPop = {
    init: function() {
      $('#wpop_message').center();

      // Theme Options
      WPop_Nav.init();
      WPop_ColorPicker.init();
      WPop_Uploader.init();
      WPop_Scheme.init();
      WPop_StylingBackground.init();
      WPop_ThemeOptions.init();

      // Metabox
      //WPop_CustomPermalink.init();

      // Slides
      WPop_SlidesComposer.init();

      $('.input', '.section-selector').WPopSelector();

      $('#wpop-container').show();
      $(window).bind('scroll resize load', function() {
        $('#wpop-message').center();
      });
    },
    flashMessage: function(msg, type) {
      var popup = $('#wpop-message');

      $(popup).html(msg).center();
      $(popup).removeClass('wpop-message-loading wpop-message-succeed wpop-message-error wpop-message-info');
      $(popup).addClass('wpop-message-' + type);
      $(popup).fadeIn('fast', function() {
        window.setTimeout(function(){
          $(popup).fadeOut('fast');
        }, 3000);
      });
    }
  };

})(jQuery);

jQuery(document).ready(function() {
  WPop.init();
});
