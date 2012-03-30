<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title><?php wp_title(' | ', true, 'right'); ?> <?php bloginfo('name'); ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<!-- <link rel="stylesheet" href="wpoptheme/hipblue.css" type="text/css" /> -->
		<?php wp_head(); ?>
		<?php if (wpop_get_option('favicon')): ?><link rel="shortcut icon" href="<?php echo wpop_get_option('favicon'); ?>" /><?php endif; ?>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
		<script src="<?php echo get_template_directory_uri(); ?>/js/script.js"></script>
	</head>
	<body <?php body_class(); ?>>
	<div class="main-outer">
	<!-- #header -->
	<div id="banner">
		<div class="container">
			<a class="logo" href="<?php bloginfo('url'); ?>">amsul</a>
		</div>
	</div>
	<div id="header">
		<div class="container">
			<?php if(!home): ?>
			<h1 class="title">
				<a href="<?php bloginfo('url'); ?>" name="top">
					amsul
					<?php/* if(wpop_get_option('logo')): echo '<img src="'.wpop_get_option('logo').'" alt="'.get_bloginfo('title').'" />'; else: echo get_bloginfo('title'); endif; */?>
				</a>
			</h1>
			<?php else: ?>
			<h1 class="title">
				<a href="<?php bloginfo('url'); ?>" name="top">
					amsul
					<?php/* if(wpop_get_option('logo')): echo '<img src="'.wpop_get_option('logo').'" alt="'.get_bloginfo('title').'" />'; else: echo get_bloginfo('title'); endif; */?>
				</a>
			</h1>
			<?php endif; ?>
			<? /*
			<div class="tagline"><?php bloginfo('description'); ?></div>
			<div class="search">
				<?php get_search_form(); ?>
			</div>
			*/ ?>
			<div class="welcome">welcome,<br />to my virtual abode<img class="caret" src="<?php echo get_template_directory_uri(); ?>/images/caret.gif" width="1" height="36" /></div>
			<div class="clear"></div>
		</div>
	</div>
	<!-- end of #header -->
	<? /*
	<!-- #navigation -->
	<div id="navigation">
		<?php wp_nav_menu('menu_class=container sf-menu&theme_location=main-menu'); ?>
	</div>
	<!-- end of #navigation -->
	*/ ?>