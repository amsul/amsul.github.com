/**
 * Wordspop Feedback Widget
 * Version 1.0
 *
 * Copyright (c) 2011 Wordspop
 * Licensed under the Apache License, Version 2.0 http://www.apache.org/licenses/LICENSE-2.0
 */

(function($) {
  WPop_Widget_Feedback = {
    init: function() {
      $('ul.animate', '.wpop_widget_feedback').cycle({
        'fx':     'scrollUp',
        'before': function(currSlideElement, nextSlideElement, options, forwardFlag) {
          $(currSlideElement).parents('div.feedback-widget').animate({'height': $(nextSlideElement).outerHeight()});
        },
        'timeout': 10000
      });
    }
  };
})(jQuery);

jQuery(function() {
  WPop_Widget_Feedback.init();
});