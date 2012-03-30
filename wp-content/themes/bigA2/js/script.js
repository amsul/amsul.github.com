
var A = {
	
	ready:				function() {
									
									A.banner();
									A.forms.init();
	},
	
	
	
	banner:				function() {
									var banner = $('#banner');
									$(window).on({
										'scroll.banner':		function() {
																					var position = $(window).scrollTop();
																					
																					if ( position > 248 )
																						banner.stop().animate({ 'top': 0 }, 150);
																					
																					else
																						banner.stop().animate({ 'top': '-54px' }, 150);
										}
									});
	},
	
	
	
	forms:				{
									/********
										form submission, etc
									********/
									
									init:			function() {
															var form = $('form');
															
															if ( !form.length ) {
																
																return false;
															
															}
															
															//if there is a form
															else {
															
																var button = form.find('input:submit');
																
																button.on('click', A.forms.submit);
																
															}
									},
									
									
									submit:		function(e) {
															
															var ok = true,
																	form = e.target.form;
															
															$(form.elements).each(function() {
																
																var elem = $(this);
																
																//check the required fields
																if ( elem.attr('aria-required') === 'true' ) {
																	
																	//if there is no value
																	if ( !elem.check_field() ) {
																	  A.forms.errors( elem.attr('id'), elem );
																	  ok = false;
																	  return false;
																  }
																  
																}
															});
															
															
															if ( ok ) return true;
															else			return false;
									},
									
									
									errors:			function(id, elem) {
																
																var msg;
																
																if			( id === 'author' )		msg = 'I\'d like to know your name';
																else if ( id ==='email' )			msg = 'Enter your valid email • It won\'t be publicly visible';
																else if ( id ==='comment' )		msg = 'Enter your message';
																
																if ( !elem.siblings('.warning-comment').length ) {
																	
																	elem
																		.on({
																			'keyup.warning':		function() {
																														setTimeout(function() {
																															elem
																																.off('.warning')
																																.closest('p').removeClass('warning')
																																.find('.warning-comment').remove();
																														}, 250);
																			}
																		})
																		.after('<span class="warning-comment">' + msg + '</span>')
																		.closest('p').addClass('warning');
																}
																
																elem.focus();
									}
	}
	
	
} //A








/********

	jQuery extension
	
********/

$.fn.extend ({

	
	check_field:			function() {
											
											/********
												check if the required field has a value
											********/
											
											if ( !this.val().length ) return false;
											
											else {
												
												if ( this.attr('id') === 'email' )
													if ( !this.check_email() ) return false;
												return true;
											}
	},
	
	
	
	check_email:			function() {
											
											var E = this.val();
											
											/********
												shecks the email address entered is valid
											********/
											if ( !(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i.test(E)) )
												return false;
											
											else return true;
	}
	
	
	
});













/********

	Doc ready
	
********/

$(function(){ A.ready() });