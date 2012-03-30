/**
 * Wordspop extras
 * Version 1.0
 *
 * Copyright (c) 2011 Wordspop
 * Licensed under the Apache License, Version 2.0 http://www.apache.org/licenses/LICENSE-2.0
 */
(function() {
  var wf = document.createElement('script');
  wf.src = ('https:' == document.location.protocol ? 'https' : 'http') +
           '://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
  wf.type = 'text/javascript';
  wf.async = 'true';
  var s = document.getElementsByTagName('script')[0];
  s.parentNode.insertBefore(wf, s);
})();

(function($) {
  WPop_Tabs = {
    init: function() {
      $('.wpop-tabs').each(function() {
        var index = 0;
        $('li', $('ul.tabs-items', this)).each(function(i) {
          if ($(this).hasClass('current')) index = i;
          $('a', this).data('tab:index', i).click(function() {
            var index = $(this).data('tab:index');
            var $parent = $($(this).parents('.wpop-tabs').get(0));
            var $content = $($('.tab-content', $parent).get(index));

            $('li', $('ul.tabs-items', $parent)).removeClass('current');
            $(this).parent('li').addClass('current');

            $('.tab-content', $parent).fadeOut();

            $panes.animate({height: $content.outerHeight()});
            $content.fadeIn();

            return false;
          });
        });

        $('.tab-content', this).hide();

        var $panes = $('.tabs-panes', this);
        var $content = $($('.tab-content', this).get(index));
        
        $panes.height($content.outerHeight());
        $content.show();
      });
    }
  }

})(jQuery);

jQuery(function() {
  WPop_Tabs.init();
});
