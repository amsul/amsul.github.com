/**
 * Wordspop Shortcodes support
 * Version 1.0
 *
 * Copyright (c) 2011 Wordspop
 * Licensed under the Apache License, Version 2.0 http://www.apache.org/licenses/LICENSE-2.0
 */

(function() {
  // Google +1
  var po = document.createElement('script');
  po.type = 'text/javascript'; po.async = true; po.src = 'https://apis.google.com/js/plusone.js';

  var s = document.getElementsByTagName('script')[0];
  s.parentNode.insertBefore(po, s);

  // Twitter
  var twitter = document.createElement('script');
  twitter.type = 'text/javascript'; twitter.src = 'http://platform.twitter.com/widgets.js';
  s.parentNode.insertBefore(twitter, s);
  
  // Facebook
  var fb = document.createElement('script');
  fb.type = 'text/javascript'; fb.src = 'http://connect.facebook.net/en_US/all.js#xfbml=1';
  s.parentNode.insertBefore(fb, s);
})();

(function($) {
  WPop_Shortcodes = {
    init: function() {
      this.doToggle();
    },
    doToggle: function() {
      $('.wpop-sc-toggle').each(function() {
        var $container = $(this);
        var $content = $('.toggle-content', $container);
        var $inner = $('.toggle-inner', $container);
        var content_pad = $content.outerHeight() - $inner.height();

        $content.css({overflow: 'hidden'});
        $inner.css({position: 'relative', left: 0});

        if ($container.hasClass('opened')) $inner.css({top: 0});
        else {
          $content.hide();
          $inner.css({top: -$inner.height() - content_pad});
        }

        $('h4 a', $container).click(function() {
          if ($container.hasClass('opened')) {
            $container.removeClass('opened');
            $content.slideUp();
            $inner.animate({top: -$inner.height()  - content_pad});
          } else {
            $container.addClass('opened');
            $content.slideDown();
            $inner.animate({top: 0});
          }
          return false;
        });
      });
    },
    doGMaps: function(lat, lng, settings, container) {
      $(container + '-map').css({'width': settings.width, 'height': settings.height});

      var latlng = new google.maps.LatLng(lat, lng);
      var mapopts = {
        center:     latlng,
        zoom:       settings.zoom,
        mapTypeId:  eval(settings.mapType),
      };

      var map = new google.maps.Map($(container + '-map').get(0), mapopts);
      var marker = new google.maps.Marker({
        map:        map,
        position:   latlng,
        title:      settings.title,
        animation:  eval(settings.markerAnimation),
        icon:       settings.icon
      });
      
      var info = $(container + '-info').html();
      if (info != '') {
        $('.wpop-sc-gmaps-info', $(container + '-info')).css('height', $(container + '-info').outerHeight() + 20);

        var infowindow = new google.maps.InfoWindow({
          content: $(container + '-info').html(),
          maxWidth: 900
        });

        google.maps.event.addListener(marker, 'click', function() {
          infowindow.open(map, marker);
        });
        
        $(container + '-info').remove();
      }
    }
  };
})(jQuery);

jQuery(function() {
  WPop_Shortcodes.init();
});
