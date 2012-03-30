// Smoothscroll
// http://blog.medianotions.de/en/articles/2009/smoothscroll-for-jquery

jQuery(document).ready(function($)
{
	$('a[href*=#]').click(function() {
	   var duration=1000;
	   var easing='swing';
	   var newHash=this.hash;
	   var target=$(this.hash).offset().top;
	   var oldLocation=window.location.href.replace(window.location.hash, '');
	   var newLocation=this;
	   if(oldLocation+newHash==newLocation)
	   {
	      $('html:not(:animated),body:not(:animated)').animate({ scrollTop: target }, duration, easing, function() {
	         window.location.href=newLocation;
	      });
	      return false;
		}
	});
	$(".wp-caption").attr("style", "");
	$("ul.sf-menu").superfish();
	$("ul.sub-menu > li:last-child").addClass("last");
	
	$('input#s').tbHinter({
		text: 'Search'
	});

});