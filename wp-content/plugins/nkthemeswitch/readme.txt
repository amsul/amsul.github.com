=== Theme Switch and Preview ===
Contributors: nkuttler
Author URI: http://www.nkuttler.de/
Plugin URI: http://www.nkuttler.de/wordpress/theme-switch-and-preview-plugin/
Donate link: http://www.nkuttler.de/wordpress/donations/
Tags: theme, themes, template, templates, switch, theme development, admin, plugin, widget, widgets, preview, theme preview, wpmu, multisite
Requires at least: 2.7
Tested up to: 3.0
Stable tag: 0.9.0.3

Allows admins or all visitors to switch the blog theme. Has a restricted preview mode for your clients and includes a widget.

== Description ==

The plugin is great for theme development as you can tweak the template online without breaking stuff for visitors. You can also use it for presentations, to show off various templates.  There is a widget and a theme cloud shortcode to make this easier.

This plugin has three basic configurations:

* <strong>Only admin</strong>: This is useful for theme developers and to preview themes on live sites.
* <strong>Only with passkey</strong>: Send your clients preview links. They won't need an account on your blog for this to work.
* <strong>Everybody</strong>: Theme switching for everybody.

= Usage =
The easiest way to use this plugin is to drag the widget into one of your sidebars.

There is as well the shortcode [nkthemeswitch]. Examples:

1. [nkthemeswitch] This displays the theme switch cloud as text links.
1. [nkthemeswitch mode="screenshot"] This displays the switch links with screenshots.
1. [nkthemeswitch mode="screenshot" addname="yes"] This displays the switch links with screenshots and the theme name.

= Using the switch cloud in your theme =
You can call the function `nkthemeswitch_cloud()` from your theme to get the theme switch cloud. Example:

<code>
if ( function_exists( 'nkthemeswitch_cloud' ) ) {
	echo nkthemeswitch_cloud( $target = '_blank', $passkey = false, $screenshot = false, $addname = false );
}
</code>

Parameters:

* target: link target attribute
* passkey: add the passkey to the links. This might be useful if you use passkeys.
* screenshot: display screenshots instead of name
* addname: add theme names to screenshots

= My plugins =

[MU fast backend switch](http://www.nkuttler.de/2010/06/07/wpmu-switch-backend/): Switch between your MU blog backends with one click

[Visitor Movies for WordPress](http://www.nkuttler.de/2010/05/21/record-movies-of-visitors/): Did you ever want to know what your visitors are really doing on your site? Watch them!

[Custom Avatars For Comments](http://www.nkuttler.de/wordpress/custom-avatars-for-comments/): Your visitors will be able to choose from the avatars you upload to your website for each and every comment they make.

[Better tag cloud](http://www.nkuttler.de/wordpress/nktagcloud/): I was pretty unhappy with the default WordPress tag cloud widget. This one is more powerful and offers a list HTML markup that is consistent with most other widgets.

[Theme switch](http://www.nkuttler.de/wordpress/nkthemeswitch/): I like to tweak my main theme that I use on a variety of blogs. If you have ever done this you know how annoying it can be to break things for visitors of your blog. This plugin allows you to use a different theme than the one used for your visitors when you are logged in.

[Zero Conf Mail](http://www.nkuttler.de/wordpress/zero-conf-mail/): Simple mail contact form, the way I like it. No ajax, no bloat. No configuration necessary, but possible.

[Move WordPress comments](http://www.nkuttler.de/wordpress/nkmovecomments/): This plugin adds a small form to every comment on your blog. The form is only added for admins and allows you to [move comments](http://www.nkuttler.de/nkmovecomments/) to a different post/page and to fix comment threading.

[Delete Pending Comments](http://www.nkuttler.de/wordpress/delete-pending-comments): This is a plugin that lets you delete all pending comments at once. Useful for spam victims.

[Snow and more](http://www.nkuttler.de/wordpress/nksnow/): This one lets you see snowflakes, leaves, raindrops, balloons or custom images fall down or float upwards on your blog.

[Fireworks](http://www.nkuttler.de/wordpress/nkfireworks/): The name says it all, see fireworks on your blog!

[Rhyming widget](http://www.rhymebox.de/blog/rhymebox-widget/): I wrote a little online [rhyming dictionary](http://www.rhymebox.com/). This is a widget to search it directly from one of your sidebars.

== Installation ==
Unzip, upload to your plugin directory, enable the plugin and configure it as needed. If you want to add the developer widget do that in your dashboard's Design section.

== Screenshots ==
1. The configuration menu, including preview links for you or for customers (if passkeys are allowed).
2. The text theme cloud. No default CSS included in the plugin. See the demo at the [theme switch and preview plugin](http://www.nkuttler.de/wordpress/theme-switch-and-preview-plugin/) site.
3. The screenshot theme cloud. The screenshots won't be resized or anything. See the demo at [theme switch and preview plugin](http://www.nkuttler.de/wordpress/theme-switch-and-preview-plugin/) site.

== Frequently Asked Questions ==
Q: I need the plugin to work for everybody, not just logged in users.<br />
A: This is possible since 0.6.0.

Q: The preview links don't work!<br />
A: Please check if you have some cache activated, that will most likely prevent the preview links from working. WP Super Cache only works in <strong>HALF ON</strong> mode.

Q: The previews look broken!<br />
A: Do you use a complex theme with theme options? Some themes need to be activated for real at least once before they work.

Q: How do I exclude themes from being shown in the widget or the cloud?<br/>
A: Add <tt>Status: unpublished</tt> to the header of the theme's <tt>style.css</tt>.

Q: I use a >= 3.0 multisite install and don't want inactive themes to be shown on certain blogs. Anything I can do?<br/>
A: Add <tt>define( 'NKTHEMESWITCH_LOAD_MS', true );</tt> to your install's <tt>wp-config.php</tt>. Careful, this might break on upgrades as I had to copy stuff from wp-admin/ into the plugin. Not very likely but possible. I'm not sure I'll continue to support this in the future. I probably won't test it, so report any bugs.

Q: My site went blank when I used your plugin, what should I do?<br/>
A: Deactivate the plugin in the admin section. Remove the plugin files from your server if that doesn't work.

== Changelog ==
= 0.9.0.3 ( 2010-07-16 ) =
 * Make the multisite feature really work. Hopefully.
= 0.9 ( 2010-07-14 ) =
 * Make it possible to exclude themes that aren't activated on a multisite ( >= 3.0 ) install. Please see the FAQ for details.
= 0.8 ( 2010-06-12 ) =
 * Incompatible change in nkthemeswitch_cloud() parameters. If you call this function from your theme you'll need to update your code.
 * Documentation update
 * Make it possible to show screenshots with theme names.
 * Bugfix for cloud with screenshots: Show text link when no screenshot exists
= 0.7.4.1 ( 2010-05-27 ) =
 * Bugfix for theme cloud and unpublished themes.
 * Fix donation link
 * Update docs
= 0.7.4 ( 2010-05-12 ) =
 * Make it possible to use an unordered list for the theme cloud, thanks to Helen for the suggestion!
= 0.7.3.1 ( 2010-04-04 ) =
 * Really exclude the new theme
= 0.7.3 ( 2010-04-02 ) =
 * Exclude the new WordPress 3.0 default theme as well if requested.
= 0.7.2 ( 2010-03-24 ) =
 * Add a title to the widget as requested by [ukmaceman](http://www.mywordpress.co.uk/). Thanks for the suggestion!
 * Fix problems with the widget when no permalinks are used.
= 0.7.1 ( 2010-03-17 ) =
 * This release should fix the problem that theme editing isn't possible when the plugin is active. Reported first by [Lizzy](http://selena-li.com/), thank you!
= 0.7.0.1 =
 * Restore the widget, thanks [Richard Walters](http://richimages.net/)
= 0.7.0 =
 * Fix for when WordPress is installed into a subdirectory. Thanks [Martin](http://ten-fingers-and-a-brain.com).
 * Add noscript submit button to theme switch dropdown widget.
 * Update docs and translations.
= 0.6.1.5 =
 * Fix the problem behind the bug, not the symptoms. Thanks again Richard!
= 0.6.1.4 =
 * Really fix the bug. And another one.
= 0.6.1.3 =
 * Fix incorrect widget URIs for the home page as reported by [Richard Walters](http://www.richimages.net/).
= 0.6.1.2 =
 * Fix incorrect preview/switch URIs as reported by melanie.
= 0.6.1.1 =
 * Don't display unpublished themes in the cloud
= 0.6.1 =
 * Added option to exclude the default and classic themes from the widget and theme cloud.
 * Stay on the same blog page when switching with the widget.
 * Stay on the same blog page when switching without permalinks enabled.
 * Add icon by [famfamafm](http://www.famfamfam.com) to the [Admin Drop Down Menu](http://planetozh.com/blog/my-projects/wordpress-admin-menu-drop-down-css/).
 * Add italian translation, thanks to [Gianni](http://gidibao.net/)!
 * Bugfixes
= 0.6.0.1 =
 * Switch links from the cloud didn't work with pretty permalinks enabled. And switching is allowed to admins when passkeys are enabled.
= 0.6.0 =
 * Big rewrite, add ideas and code from <a href="http://boren.nu/">Ryan Boren's</a> <a href="http://wordpress.org/extend/plugins/theme-switcher/">Theme Switcher</a> plugin. His plugin is much smaller and more efficient than mine, but it offers no admin-only previews, configuration screens, shortcodes etc.
 * Theme switching for everybody, even anonymous users possible.
 * Shortcode for theme switch cloud. No default CSS included.
 * Shortcode for screenshots of all themes. No screenshot resize built-in.
 * Update german translation.
= 0.5.0 =
 * I18N
 * Add german translation
 * Improve security and make code more efficient.
 * Switching is only available to admins. I think the user level thing never worked, please report if it did for you.
 * Please attach patches to feature requests.
= 0.4.2 =
 * Fix typos
 * Fix bad upgrade bug that deletes plugin settings
= 0.4.1 =
 * New FAQ entries
 * Doc updates
= 0.4.0 =
 * Add preview links
 * Add &lt;select&gt; building helper function
 * Add install hook
 * Use WordPress CSS for submit buttons
 * Fix FAQ formatting
= 0.3.6 =
 * Added a FAQ entry
= 0.3.5 =
 * Sort themes alphabetically, thanks to <a href="http://wordpress.xklaim.com/">xklaim</a> for the suggestion
= 0.3.4 =
 * Doc updates, fix typo<br />
= 0.3.3 =
 * Minor updates<br />
= 0.3.2 =
 * Compatibility tests<br />
= 0.3.1 =
 * Bugfix<br />
= 0.3.0 =
 * Add a Widget, better settings page, Documentation updates<br />
= 0.2.1 =
 * Documentation updates, correct version numbering<br />
= 0.01 =
 * Ready plugin for upload at the [WordPress](http://wordpress.org/) site<br />

== Upgrade Notice ==
= 0.8.0 =
 * Incompatible change in nkthemeswitch_cloud() parameters. If you call this function from your theme you'll need to update your code.
= 0.7.1 =
 * Fix theme editing.
= 0.7.0 =
 * This is a maintenance release with one big internal but no big visible changes. Priority: Low.
