/**
 * Wordspop Twitter Widget
 * Version 1.0
 *
 * Copyright (c) 2011 Wordspop
 * Licensed under the Apache License, Version 2.0 http://www.apache.org/licenses/LICENSE-2.0
 */
 
(function ($) {

  WPop_Twitter = {
    widgets: [],
    timeline: {},
    add: function( id, username, show_follow, animate ) {
      var widget = {'id': id, 'username': username, 'show_follow': show_follow, 'animate': animate};
      this.widgets.push( widget );
    },
    push: function( timeline ) {
      if ( timeline == null || timeline.length == 0 ) return;

      var username = timeline[0].user.screen_name;
      var html = '';
      $.each( timeline, function(i, status ) {
        var content = status.text.replace(/((https?|s?ftp|ssh)\:\/\/[^"\s\<\>]*[^.,;'">\:\s\<\>\)\]\!])/g, function(url) {
          return '<a href="'+url+'">'+url+'</a>';
        }).replace(/\B(@([_a-z0-9]+))/ig, function(reply) {
          return '<a href="http://twitter.com/' + reply.substring(1) + '">' + reply + '</a>';
        });
        html += '<li><span class="content">' + content + '</span> <a href="http://twitter.com/' + username + '/statuses/' + status.id_str + '"><small>' + $.cuteTime({}, status.created_at) + '</small></a></li>' + "\n";
      });

      this.timeline[username] = html;
      this.display();
    },
    display: function() {
      var self = this;
      $.each(this.widgets, function(i, widget) {
        $('#twitter-timeline-' + widget.id).html( self.timeline[widget.username] );

        if ( widget.show_follow && $('.twitter-widget .follow', '#' + widget.id).length == 0) {
          $('.twitter-widget', '#' + widget.id).append( '<p class="follow">Follow <a href="http://twitter.com/' + widget.username + '">@' + widget.username + '</a> on Twitter</p>' + "\n");
        }

        if ( widget.animate ) self.animate( widget.id );
      });
    },
    animate: function(widget) {
      var $container = $('.timeline', '#' + widget);
      $container.addClass('animate');
      $container.css({'position': 'relative'});
      $('#twitter-timeline-' + widget).cycle({
        'before': function(currSlideElement, nextSlideElement, options, forwardFlag) {
          $(currSlideElement).parents('div.timeline').animate({'height': $(nextSlideElement).outerHeight()});
        },
        'timeout': 10000
      });
    }
  };

})(jQuery);
