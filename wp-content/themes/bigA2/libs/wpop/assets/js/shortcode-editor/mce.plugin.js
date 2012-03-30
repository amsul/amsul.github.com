/**
 * Wordspop Shortcodes TinyMCE plugins
 * Version 1.0
 *
 * Copyright (c) 2011 Wordspop
 * Licensed under the Apache License, Version 2.0 http://www.apache.org/licenses/LICENSE-2.0
 */

(function($) {
  var assets = '';
  var selectedText = '';

  tinymce.create('tinymce.plugins.wpopshortcode', {
    editor: null,
    init: function(editor, url) {
      var self = this;
      self.editor = editor;

      assets = url.replace(/\/js\/shortcode-editor$/, '');

      editor.addCommand('WPopOpenDialog', function(shortcode) {
        $.get(ajaxurl, {action: 'wpop_dialog_shortcode', 'tag': shortcode.tag}, function(res) {
          $('#wpop-shortcode-dialog').remove();
          $('body').append(res);
          $('#wpop-shortcode-dialog').hide();

          var height = $(window).height() - 84;

          tb_show('Insert "' + shortcode.title + '" Shortcode', '#TB_inline?inlineId=wpop-shortcode-dialog&width=640&height=' + height);
        });
      });
    },
    getInfo: function() {
      return {
        longname: 'Wordspop Shortcode Plugin',
        author: 'Wordspop',
        authorurl: 'http://wordspop.com/',
        infourl: 'http://docs.wordspop.com/shortcodes',
        version: '1.0'
      }
    },
    createControl: function(button, controlmanager) {
      var self = this;

      if (button == 'wpop_shortcode') {
        var control = controlmanager.createMenuButton('wpop_shortcode_button', {
          title: 'Insert Wordspop Shortcode',
          image: assets + '/images/editor-wordspop-shortcode.png',
          icons: false
        });
        
        var groups = [], group = parent = null;
        control.onRenderMenu.add(function(control, menu) {
          $.each(WPop_shortcodes, function(i, shortcode) {
            // Default parent is menu
            parent = menu;

            if (shortcode.menu_group) {
              // Nulled the group
              group = null;

              $.each(groups, function(i, g){
                // Take a look if group is already exists
                if (g.title == shortcode.menu_group) group = g.menu;
              });

              if (group === null) { // Group not exists, create one
                group = menu.addMenu({title: shortcode.menu_group});
                groups.push({title: shortcode.menu_group, menu: group});
              }

              // Set parent to group
              parent = group;
            }

            if (shortcode.show_dialog === false) self.insertDirectly(parent, shortcode);
            else self.insertByDialog(parent, shortcode);
          });
        });

        return control;
      }
      
      return null;
    },
    insertByDialog: function(menu, shortcode) {
      menu.add({
        title: shortcode.title,
        onclick: function(){
          tinyMCE.activeEditor.execCommand('WPopOpenDialog', shortcode);
        }
      })
    },
    insertDirectly: function(menu, shortcode) {
      var self = this;

      menu.add({
        title: shortcode.title,
        onclick: function(){
          var content = '[' + shortcode.tag + ']';
          if (shortcode.close_tag === true) {
            if ( self.editor.selection.getContent().length > 0 ) content += self.editor.selection.getContent();
            content += '[/' + shortcode.tag + ']';
          }
          
          tinyMCE.activeEditor.execCommand('mceInsertContent', false, content);
        }
      })
    }
  });
  
  // Register plugin
  tinymce.PluginManager.add('wpopshortcode', tinymce.plugins.wpopshortcode);
})(jQuery);