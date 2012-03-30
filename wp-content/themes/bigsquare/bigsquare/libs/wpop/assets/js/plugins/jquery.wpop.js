(function ($) {
  // Center the element
  $.fn.center = function () {
    return this.each(function() {
      $(this).css({
        'top': ($(window).height() - $(this).height()) / 2 + $(window).scrollTop(),
        'left': ($(window).width() - $(this).width()) / 2 + $(window).scrollLeft(),
        'position': 'absolute'
      });
    })
  };
})(jQuery);
