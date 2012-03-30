function wpsd_loading() {
	jQuery("#wpsd-loading").show();
};

function wpsd_loaded() {
	jQuery("#wpsd-loading").hide();
};

function wpsd_load_trend(type) {
	
	var data = {
		action: 'wpsd_load_trend',
		type: type
	};	

	jQuery.post(ajaxurl, data, function(response) {		
		jQuery('#wpsd_trend').html(response);
	});
};

function wpsd_load_clicks() {

	var data = {
		action: 'wpsd_load_clicks'
	};	

	jQuery.post(ajaxurl, data, function(response) {		
		jQuery('#wpsd_clicks_inner').html(response);
	});
};

function wpsd_load_postviews() {

	var data = {
		action: 'wpsd_load_postviews'
	};	

	jQuery.post(ajaxurl, data, function(response) {		
		jQuery('#wpsd_postviews_inner').html(response);
	});
};

function wpsd_load_referers() {

	var data = {
		action: 'wpsd_load_referers'
	};	

	jQuery.post(ajaxurl, data, function(response) {		
		jQuery('#wpsd_referers_inner').html(response);
	});
};

function wpsd_load_searchterms() {

	var data = {
		action: 'wpsd_load_searchterms'
	};	

	jQuery.post(ajaxurl, data, function(response) {		
		jQuery('#wpsd_searchterms_inner').html(response);
	});
};

function wpsd_load_compete() {

	var data = {
		action: 'wpsd_load_compete'
	};	

	jQuery.post(ajaxurl, data, function(response) {		
		jQuery('#wpsd_compete_inner').html(response);
	});
};

/* 
function wpsd_load_blogpulse() {

	var data = {
		action: 'wpsd_load_blogpulse'
	};	

	jQuery.post(ajaxurl, data, function(response) {		
		jQuery('#wpsd_blogpulse_inner').html(response);
	});
};

function wpsd_load_blogpulse_conv() {

	var data = {
		action: 'wpsd_load_blogpulse_conversations'
	};	

	jQuery.post(ajaxurl, data, function(response) {		
		jQuery('#wpsd_blogpulse_conversations_inner').html(response);
	});
}; 

*/

function wpsd_load_rss() {

	var data = {
		action: 'wpsd_load_rss'
	};	

	jQuery.post(ajaxurl, data, function(response) {		
		jQuery('#dl_posts_inner').html(response);
	});
};

function wpsd_load_optimize() {

	var data = {
		action: 'wpsd_load_optimize'
	};	

	jQuery.post(ajaxurl, data, function(response) {		
		jQuery('#wpsd_optimize_inner').html(response);
	});
};

function wpsd_load_authors() {

	var data = {
		action: 'wpsd_load_authors'
	};	

	jQuery.post(ajaxurl, data, function(response) {		
		jQuery('#wpsd_author_inner').html(response);
	});
};

function wpsd_find_profile() {

	jQuery('#wpsd_profile_loading').show();
	
	var data = {
		action: 'wpsd_find_profile',
		name: jQuery('#profile-name').val()
	};	

	jQuery.post(ajaxurl, data, function(response) {	
			
		jQuery('#wpsd_profile_finder_content').html(response);
		
		jQuery('#wpsd_profile_btn_save').show();

		jQuery('#wpsd_profile_loading').hide();
	});
};