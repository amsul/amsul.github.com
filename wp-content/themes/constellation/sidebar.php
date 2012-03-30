<aside id="sidebar">
	<?php if ( is_active_sidebar( 'primary-widget-area' ) ) : ?>
	
		<?php if ( function_exists('dynamic_sidebar') ) dynamic_sidebar( 'primary-widget-area' ); ?>
		
		<section id="poll" class="widget-container">
			<h3 class="widget-title">How awesome is this poll?</h3>
			<form>
			<dl class="ninety-poll not-voted poll-title">
				<dt><label for="input">Very Awesome</label></dt>
				<dd>
					<input type="radio" name="input" />
				</dd>
				<dt><label for="input">Pretty Awesome</label></dt>
				<dd>
					<input type="radio" name="input" />
				</dd>
				<dt><label for="input">Moderately Awesome</label></dt>
				<dd>
					<input type="radio" name="input" />
				</dd>
				<dt><label for="input">Plain Awesome</label></dt>
				<dd>
					<input type="radio" name="input" />
				</dd>
			</dl>
			<input class="button" type="submit" value="Vote" />
			</form>
		</section>
		
		<section id="poll" class="widget-container">
			<h3 class="widget-title">How awesome is this poll?</h3> 
			<dl class="ninety-poll voted poll-title">	
				<dt>Very Awesome</dt>
				<dd>
					<span rel="50" style="width:50%;"><var>50%</var></span>
				</dd>
				<dt>Pretty Awesome</dt>
				<dd>
					<span rel="32" style="width:32%;"><var>32%</var></span>
				</dd>
				<dt>Moderately Awesome</dt>
				<dd>
					<span rel="16" style="width:16%;"><var>16%</var></span>
				</dd>
				<dt>Plain Awesome</dt>
				<dd>
					<span rel="2" style="width:2%;"><var>2%</var></span>
				</dd>
			</dl>
		</section>
		
	<?php else : ?>
	
		<section class="widget-container widget_search">
			<?php get_search_form(); ?>
		</section>

		<section id="archives" class="widget-container">
			<h3 class="widget-title"><?php _e( 'Archives', 'twentyten' ); ?></h3>
			<ul>
				<?php wp_get_archives( 'type=monthly' ); ?>
			</ul>
		</section>

		<section id="meta" class="widget-container">
			<h3 class="widget-title"><?php _e( 'Meta', 'twentyten' ); ?></h3>
			<ul>
				<?php wp_register(); ?>
				<li><?php wp_loginout(); ?></li>
				<?php wp_meta(); ?>
			</ul>
		</section>
		
	<?php endif; ?>
</aside>