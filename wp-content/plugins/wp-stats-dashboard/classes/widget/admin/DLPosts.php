<?php
/**
 * Dashboard feed widget.
 * @author dligthart
 * @version 0.2
 * @package com.daveligthart
 */
if (!class_exists('DLPosts')) {
	
	class DLPosts {

		/**
		 * DLPosts.
		 * @access public
		 */
		function DLPosts() {
			if (isset($_GET['show_dl_widget'])) {
				if ($_GET['show_dl_widget'] == "true") {
					update_option( 'show_dl_widget', 'noshow' );
				} else {
					update_option( 'show_dl_widget', 'show' );
				}
			}
			add_action( 'wp_dashboard_setup', array(&$this, 'register_widget') );
			add_filter( 'wp_dashboard_widgets', array(&$this, 'add_widget') );
		}

		/**
		 * @access private
		 */
		function register_widget() {
			wp_register_sidebar_widget('dl_posts', 'DaveLigthart.com - Freelance Webdeveloper',
			array(&$this, 'widget'),
			array(
				//'all_link' => 'http://daveligthart.com/',
				'feed_link' => 'http://daveligthart.com/feed/',
				//'edit_link' => 'options.php' 
				)
			);
		}

		/**
		 * @access private
		 */
		function add_widget( $widgets ) {
			global $wp_registered_widgets;
			if ( !isset($wp_registered_widgets['dl_posts']) ) return $widgets;
			array_splice( $widgets, 2, 0, 'dl_posts' );
			return $widgets;
		}

		/**
		 * @access private
		 */
		function widget($args = array()) {
				
			if (is_array($args))
			extract( $args, EXTR_SKIP );

			echo $before_widget.$before_title.$widget_name.$after_title;

			echo '<div id="dl_posts_inner">loading</div>';

			echo $after_widget;
				
		}
	}

	// Start this plugin once all other plugins are fully loaded
	add_action( 'plugins_loaded', create_function( '', 'global $dlPosts; $dlPosts = new DLPosts();' ) );
}
?>