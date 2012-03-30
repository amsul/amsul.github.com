=== WP SyntaxHighlighter ===
Contributors: redcocker
Donate link: http://www.near-mint.com/blog/donate
Tags: syntaxhighlighter, sourcecode, code, syntax, highlight, highlighting, prettify, snippet, tinymce, quicktag, button, shortcode, comment, widget, bbPress
Requires at least: 2.8
Tested up to: 3.3.1
Stable tag: 1.7.1

This plugin is code syntax highlighter based on SyntaxHighlighter ver. 3.0.83 and 2.1.382.

== Description ==

This plugin is code syntax highlighter based on Alex Gorbatchev's SyntaxHighlighter ver. 3.0.83 and 2.1.382.

= Features =

* Based on Alex Gorbatchev's "[SyntaxHighlighter](http://alexgorbatchev.com/SyntaxHighlighter/ "SyntaxHighlighter")" JavaScript library.
* Including both SyntaxHighlighter 3.0.83 and 2.1.382.(Can switch between 3.0.83 and 2.1.382.)
* Built-in TinyMCE buttons and Quicktag button are easy to operate.
* Easy to wrap your code in `<pre>` tag and change options of previously-wrapped code.
* Support [shortcode](http://en.support.wordpress.com/code/posting-source-code/ "shortcode") also.
* Easy to highlight code in comments using buttons.(WordPress 3.0 or higher)
* Widget to show highlighted code.
* Support [bbPress](http://wordpress.org/extend/plugins/bbpress/ "bbPress") plugin 2.0 or higher.
* Support "[Dynamic Brush Loading](http://alexgorbatchev.com/SyntaxHighlighter/manual/api/autoloader.html "A key feature")" which allows to load only necessary brush files dynamically.
* Loading JavaScripts on only posts, pages, home, archives, search results and comments which have the code to highlight.
* Easy to configure features through the setting panel.
* Localization: English(Default), 日本語(Japanese, UTF-8).

= Supported languages =

AppleScript, ActionScript3, Bash, ColdFusion, C, C++, C#, CSS, Delphi, Diff, Erlang, Groovy, HTML, Java, Java FX, JavaScript, Pascal, Patch, Perl, PHP, Plain text, PowerShell, Python, Ruby, Ruby on Rails, Sass, SCSS, Shell, SQL, VB, VB NET, XHTML, XML and XSLT

Note: A part of languages are only for SyntaxHighlighter 3.0.

You can also use a bundled plugin to add following languages.

Biferno, Clojure, DOS batch file, F#, LISP, Lua(only for SyntaxHighlighter 3.0), MEL Script, Objective-C, PowerCLI, Processing, R, S, S-PLUS, Tcl, Verilog, Vim Script and YAML

For details, see "sample" directory.

There are many other languages for "SyntaxHighlighter". But I can't bundle them because they are not compatible with this plugin license. If you want, try to search.

= Recommended plugin =

* "[SyntaxHighlighter TinyMCE Button](http://wordpress.org/extend/plugins/syntaxhighlighter-tinymce-button/ "SyntaxHighlighter TinyMCE Button")" can provide feature-rich tinyMCE buttons for SyntaxHighlighter.
* "[CodeMirror for CodeEditor](http://wordpress.org/extend/plugins/codemirror-for-codeeditor/ "CodeMirror for CodeEditor")" can highlight sourcecodes in theme and plugin editor and provide a useful toolbar.

= Thanks =

* "SyntaxHighlighter" libraries was created by [Alex Gorbatchev](http://alexgorbatchev.com/SyntaxHighlighter/ "Alex Gorbatchev").
* "shBrushBiferno.js" was created by [Sandro Bilbeisi](http://www.sandrobilbeisi.org/wp/works/web-development/biferno-javascript-brush-for-syntaxhighlighter-shbrush-js/ "Sandro Bilbeisi").
* "shBrushClojure.js" was created by [Daniel Solano Gómez](https://github.com/sattvik/sh-clojure "Daniel Solano Gómez").
* "shBrushDosBatch-V2.js" and "shBrushDosBatch-V3.js" were created by [Andreas Breitschopp](http://www.ab-tools.com/en/ "Andreas Breitschopp"). He also developed [nice plugins](http://wordpress.org/extend/plugins/profile/ab-tools "nice plugins").
* F# brush was written by [Steve Gilham](http://stevegilham.blogspot.com/2009/10/syntaxhighlighter-20-brushes-for-f-and.html "Steve Gilham").
* "shBrushLisp.js" was created by [Knut Haugen](http://blog.knuthaugen.no/2009/10/a-syntaxhighlighter-brush-for-lisp.html "Knut Haugen").
* "shBrushLua.js" was created by [최익필](http://ikpil.com/1191 "최익필").
* "shBrushMel.js" was created by [Skye Book](http://www.skyebook.net/blog/2011/02/syntaxhighlighter-brush-for-mel-script/ "Skye Book").
* "shBrushObjC.js" was created by [Matej Bukovinski](http://www.bukovinski.com "Matej Bukovinski").
* "shBrushPowerCLI.js" was created by [Dan J](http://vm-pro.com/vmware-powercli-syntax-highlighter-brush/ "Dan J").
* "shBrushProcessing.js" was created by [Sebastian Korczak](http://en.myinventions.pl/index.php?page=ProcessingSyntaxHighlighting "Sebastian Korczak").
* The css for "shBrushProcessing.js" was written by [Sebastian Korczak](http://en.myinventions.pl/index.php?page=ProcessingSyntaxHighlighting "Sebastian Korczak").
* "shBrushR.js" was created by [Yihui Xie](http://yihui.name/en/2010/09/syntaxhighlighter-brush-for-the-r-language "Yihui Xie").
* "shBrushTcl.js" was created by [henix](http://www.henix-blog.co.cc/blog/tcl-syntaxhighlighter-brush.html "henix").
* "shBrushVerilog.js" was created by [Hanly De Los Santos](http://www.hdelossantos.com/2010/05/20/verilog-syntaxhighlighter-brush/ "Hanly De Los Santos").
* "shBrushVimscript.js" was created by [nelstrom(Drew Neil)](http://vimcasts.org/blog/2010/04/syntaxhighlighter-vimscript-brush-and-blackboard-theme/ "nelstrom(Drew Neil)").
* "shBrushYaml.js" was created by [Nicolas Perriault](http://prendreuncafe.com/blog/post/2009/07/26/YAML-Brush-for-the-SyntaxHighlighter-Javascript-Library "Nicolas Perriault").
* [Judah](http://www.judahfrangipane.com/blog/ "Judah") revised the awkward descriptive text that was shown before comment form.

== Installation ==

= Installation =

1. Upload plugin folder to the `/wp-content/plugins/` directory.
1. Activate the plugin through the "Plugins" menu in WordPress.
1. If you need, go to "Settings" -> "WP SyntaxHighlighter" to configure.

= Usage in Visual editor =

Note: The build-in "SH TinyMCE Button" can work only with the default visual editor(TinyMCE). Other visual editors(e.g. CKEditor etc.) are not supported.

Note: If you don't have "[unfiltered_html](http://codex.wordpress.org/Roles_and_Capabilities "unfiltered_html")" capability, `<!--[XXXX]-->` can't be included in your code.

XXXX = 2-4 letter character string including 0-9, A-F or a-f.

**Usage: Wrap your code in `<pre>` tag with "SH TinyMCE Button".**

If you have previously-written code on your post or page, by this way, you can wrap your code in `<pre>` tag for 'SyntaxHighlighter'.

1. With the mouse, select and highlight your code where you want to aplly "SyntaxHighlighter".
1. Click "pre" button.
1. Select language and options.
1. Click "Insert" button.

**Usage: Paste your code into the post or page with "SH TinyMCE Button".**

If you want to copy the code from the other document and paste into your post or page, this way is best. Your pasted code will be warpped in `<pre>` tag automatically.

1. Click "CODE" button.
1. Select language and options and paste your code into textbox.
1. Click "Insert" button.

**Usage: Change language and options of previously-markuped code with "SH TinyMCE Button".**

1. With the mouse, select and highlight your code.
1. Click "pre" button.
1. Change language and options.
1. Click "Update" button.

**Usage: Indent by tabs.**

1. Just type tab in your code. But till your code is wrapped in `<pre>` tag, you can not type any tabs.

= Usage in HTML editor =

Note: If you don't have "[unfiltered_html](http://codex.wordpress.org/Roles_and_Capabilities "unfiltered_html")" capability, `<!--[XXXX]-->` can't be included in your code.

XXXX = 2-4 letter character string including 0-9, A-F or a-f.

**Usage: Wrap your code in `<pre>` tag with "SH pre" button.**

If you have previously-written code on your post or page, by this way, you can wrap your code in `<pre>` tag for 'SyntaxHighlighter'.

Note: Before you use "SH pre" button, you may need to go to setting panel and activate "Add Quicktag Button" option in "HTML Editor Settings" section.

1. With the mouse, select and highlight your code where you want to aplly "SyntaxHighlighter".
1. Click "SH pre" button.
1. Select language and options.
1. Click "OK" button. Then your code will be wrapped in `<pre>` tag and escape to HTML entities.

**Usage: Wrap your code in `<pre>` tag without "SH pre" button.**

1. Just wrap Your Code in `<pre>` tag with the class attribute as below in HTML editor or using the "Preformatted" style in Visual editor.

You do not need to escape your code to HTML entities. This plugin will escape your code to HTML entities automatically after saving. However, To prevent destroying your code after switching to "Visual editor", You had better escape your code to HTML entities or save it before switching to "Visual editor".

Note: If `</pre>` tags are included in your code, replace `</pre>` with `<!--[/pre]-->`.

`<pre class="brush: lang">Your Code</pre>` *"lang" = your language

* e.g. `<pre class="brush: php">Your PHP Code</pre>`
* e.g. `<pre class="brush: xhtml">Your XHTML Code</pre>`

See "[Available languages](http://alexgorbatchev.com/SyntaxHighlighter/manual/brushes/ "Brushes")". *"Brush aliases" means "lang".

You can also use some options with `<pre>` tag.

See "[Options](http://alexgorbatchev.com/SyntaxHighlighter/manual/configuration/ "Class value")".

* e.g. `<pre class="brush: xhtml; first-line: 10">Your XHTML Code</pre>`
* e.g. `<pre class="brush: php; html-script: true">Your PHP Code</pre>`

This plugin also can support `<script>` tag method. However, No one should use `<script>` tag. You must use "HTML editor" to type `<script>` tag, and when switching to "Visual editor", TinyMCE will destroy your tag.

Even if using "<script>" method, your codes must be escape to HTML entities. 

Just escape following 3 characters: <, >, &

**Usage: Wrap your code in shorcode(without unsig buttons).**

You can also wrap your in "shorcode" to highlight your code without using buttons.

Note: Before you use "shorcode", you must go to setting panel and activate "Support shortcode" option in "HTML Editor Settings" section.

Note: Don't edit a post/page with codes wrapped in shortcode in "Visual editor" or switch from "HTML editor" to "Visual editor". When your code wrapped in shorcode is displayed in Visual editor, "Visual editor" will destroy your code.

* e.g. `[sourcecode language="php" gutter="true" firstline="1" highlight="" htmlscript="false"]Your PHP Code[/sourcecode]`
* e.g. `[sourcecode lang="xhtml" gutter="true" firstline="1" highlight="" htmlscript="false"]Your XHTML Code[/sourcecode]`

This plugin supports WordPress.com's posting sourcecode method.

[Posting Source&nbsp;Code &#8212; Support &#8212; WordPress.com](http://en.support.wordpress.com/code/posting-source-code/ "Posting Source&nbsp;Code &#8212; Support &#8212; WordPress.com")

You do not need to escape your code to HTML entities. This plugin will escape your code to HTML entities automatically.

Note: The shorcodes can't be included in your code.

= Post a comment with code(s) =

Note: Once you enable "Commnets" in "Higlight your code in" option through the setting panel, this plugin allows visitors to post their source code as comments.
Note: The default comment form is required to highlight codes in comments. When default comment system is replaced with other(e.g. DISQUS etc.) or closed, the codes in commnets can't be highlighted and "Comment Highlighter Button" can't be shown.

Note: If you don't have "[unfiltered_html](http://codex.wordpress.org/Roles_and_Capabilities "unfiltered_html")" capability, `<!--[XXXX]-->` can't be included in your code.

XXXX = 2-4 letter character string including 0-9, A-F or a-f.

If you use WordPress 3.0 or higher, you had better enable "Comment Highlighter Button" in the setting panel. "Comment Highlighter Button" will help visitors to post a comment with their sourcecodes and highlight them.

**Usage: Post a comment with codes using "`<pre>` tag" button.**

1. Paste your code in the comment form.
1. Select it and then click the language link button.
1. This will wrap your code in `<pre>` tag and format it when submitted

You do not need to escape your code to HTML entities. This plugin will escape your code to HTML entities automatically.

Note: `<!--[/pre]-->` can't be included in your code.

**Usage: Post a comment with codes using "Shorcode" button.**

1. Paste your code in the textarea.
1. Select it and then click the language link button.
1. This will wrap your code in shortcode(like a BBcode) and format it when submitted

You do not need to escape your code to HTML entities. This plugin will escape your code to HTML entities automatically.

Note: The shorcodes can't be included in your code.

**Usage: Wrap your code in `<pre>` tag(without unsig buttons).**

You can also enter `<pre>` tag directly without using "Comment Highlighter Button".

1. Just wrap Your Code in `<pre>` tag with the class attribute as when you use HTML editor without "SH pre" button.

`<pre class="brush: lang">Your Code</pre>` *"lang" = your language

* e.g. `<pre class="brush: php">Your PHP Code</pre>`

You do not need to escape your code to HTML entities. This plugin will escape your code to HTML entities automatically.

Note: If `</pre>` tags are included in your code, replace `</pre>` with `<!--[/pre]-->`.

**Usage: Wrap your code in shorcode(without unsig buttons).**

You can also enter "shorcode" directly without using "Comment Highlighter Button".

Note: Before you use "shorcode", you must go to setting panel and activate "Support shortcode" option in "Comment Form Settings" section.

* e.g. `[sourcecode language="php" gutter="true" firstline="1" highlight="" htmlscript="false"]Your PHP Code[/sourcecode]`
* e.g. `[sourcecode lang="xhtml" gutter="true" firstline="1" highlight="" htmlscript="false"]Your XHTML Code[/sourcecode]`

This plugin supports WordPress.com's posting sourcecode method.

[Posting Source&nbsp;Code &#8212; Support &#8212; WordPress.com](http://en.support.wordpress.com/code/posting-source-code/ "Posting Source&nbsp;Code &#8212; Support &#8212; WordPress.com")

You do not need to escape your code to HTML entities. This plugin will escape your code to HTML entities automatically.

Note: The shorcodes can't be included in your code.

**Usage: Update comments.(For administrator)**

You can use "SH pre" button in the "Comment editor".

Note: Note: Before you use "SH pre" button, you may need to go to setting panel and activate "Add Quicktag Button" option in "Comment Editor Settings" section.

You can also wrap your code in `<pre>` tag or shortcode without using "SH pre" button.

Note: Before you use "shorcode", you must go to setting panel and activate "Support shortcode" option in "Comment Form Settings" section.

= WP SyntaxHighlighter Widget =

"WP SyntaxHighlighter Widget" is the widget to show highlighted code.

Before you use "WP SyntaxHighlighter Widget", you must enable "Use WP SyntaxHighlighter Widget" in setting panel. Then go to "Widgets" section under "Appearance" menu to add the "WP SyntaxHighlighter Widget" in your sidebar.

You can use plain text, html tag and sourcecode in the widget.

**Usage: Using "`<pre>` tag" button.**

1. Paste your code in the textarea.
1. Select it and then click the language link button.
1. This will wrap your code in `<pre>` tag.

You do not need to escape your code to HTML entities. This plugin will escape your code to HTML entities automatically.

Note: `<!--[/pre]-->` can't be included in your code.

**Usage: Using "Shorcode" button.**

1. Paste your code in the textarea.
1. Select it and then click the language link button.
1. This will wrap your code in shortcode(like a BBcode).

You do not need to escape your code to HTML entities. This plugin will escape your code to HTML entities automatically.

Note: The shorcodes can't be included in your code.

**Usage: Wrap your code in `<pre>` tag(without unsig buttons).**

You can also enter `<pre>` tag directly without using buttons.

1. Just wrap your code in `<pre>` tag with the class attribute as when you use HTML editor without "SH pre" button.

`<pre class="brush: lang">Your Code</pre>` *"lang" = your language

* e.g. `<pre class="brush: php">Your PHP Code</pre>`

You do not need to escape your code to HTML entities. This plugin will escape your code to HTML entities automatically.

Note: If `</pre>` tags are included in your code, replace `</pre>` with `<!--[/pre]-->`.

**Usage: Wrap your code in shorcode(without unsig buttons).**

You can also enter "shorcode" directly without using buttons.

Note: Before you use "shorcode", you must go to setting panel and activate "Support shortcode" option in "WP SyntaxHighlighter Widget Settings" section.

* e.g. `[sourcecode language="php" gutter="true" firstline="1" highlight="" htmlscript="false"]Your PHP Code[/sourcecode]`
* e.g. `[sourcecode lang="xhtml" gutter="true" firstline="1" highlight="" htmlscript="false"]Your XHTML Code[/sourcecode]`

This plugin supports WordPress.com's posting sourcecode method.

[Posting Source&nbsp;Code &#8212; Support &#8212; WordPress.com](http://en.support.wordpress.com/code/posting-source-code/ "Posting Source&nbsp;Code &#8212; Support &#8212; WordPress.com")

You do not need to escape your code to HTML entities. This plugin will escape your code to HTML entities automatically.

Note: The shorcodes can't be included in your code.

= bbPress =

If [bbPress](http://wordpress.org/extend/plugins/bbpress/ "bbPress") plugin(2,0 or higher) is installed and activated, "bbPress Highlighter Button" can be shown above topic/reply form to make easy to post sourcecodes.

Before you use "bbPress Highlighter Button", you must enable "bbPress Highlighter Button" in setting panel.

Note: If you don't have "[unfiltered_html](http://codex.wordpress.org/Roles_and_Capabilities "unfiltered_html")" capability, `<!--[XXXX]-->` can't be included in your code.

XXXX = 2-4 letter character string including 0-9, A-F or a-f.

**Usage: Post a topic/reply with codes using "`<pre>` tag" button.**

1. Paste your code in the topic/reply form.
1. Select it and then click the language link button.
1. This will wrap your code in `<pre>` tag and format it when submitted

You do not need to escape your code to HTML entities. This plugin will escape your code to HTML entities automatically.

Note: `<!--[/pre]-->` can't be included in your code.

**Usage: Post a topic/reply with codes using "Shorcode" button.**

1. Paste your code in the topic/reply form.
1. Select it and then click the language link button.
1. This will wrap your code in shortcode(like a BBcode) and format it when submitted

You do not need to escape your code to HTML entities. This plugin will escape your code to HTML entities automatically.

Note: The shorcodes can't be included in your code.

**Usage: Wrap your code in `<pre>` tag(without unsig buttons).**

You can also enter `<pre>` tag directly without using "bbPress Highlighter Button".

1. Just wrap Your Code in `<pre>` tag with the class attribute as when you use HTML editor without "SH pre" button.

`<pre class="brush: lang">Your Code</pre>` *"lang" = your language

* e.g. `<pre class="brush: php">Your PHP Code</pre>`

You do not need to escape your code to HTML entities. This plugin will escape your code to HTML entities automatically.

Note: If `</pre>` tags are included in your code, replace `</pre>` with `<!--[/pre]-->`.

**Usage: Wrap your code in shorcode(without unsig buttons).**

You can also enter "shorcode" directly without using "bbPress Highlighter Button".

Note: Before you use "shorcode", you must go to setting panel and activate "Support shortcode" option in "bbPress Settings" section.

* e.g. `[sourcecode language="php" gutter="true" firstline="1" highlight="" htmlscript="false"]Your PHP Code[/sourcecode]`
* e.g. `[sourcecode lang="xhtml" gutter="true" firstline="1" highlight="" htmlscript="false"]Your XHTML Code[/sourcecode]`

This plugin supports WordPress.com's posting sourcecode method.

[Posting Source&nbsp;Code &#8212; Support &#8212; WordPress.com](http://en.support.wordpress.com/code/posting-source-code/ "Posting Source&nbsp;Code &#8212; Support &#8212; WordPress.com")

You do not need to escape your code to HTML entities. This plugin will escape your code to HTML entities automatically.

Note: The shorcodes can't be included in your code.

**In Topics/Replies sections on the setting panel**

In Topics/Replies sections, Use "pre" and "code" buttons in Visual editor or "SH pre" button in HTML editor.

Note: Note: Before you use "SH pre" button, you may need to go to setting panel and activate "Add Quicktag Button" option in "HTML Editor Settings" section.

You can also wrap your code in `<pre>` tag or shortcode without using "SH pre" button.

Note: Before you use "shorcode", you must go to setting panel and activate "Support shortcode" option in "bbPress Settings" section.

Note: Don't edit a topic/reply with code wrapped in shortcode in "Visual editor" or switch from "HTML editor" to "Visual editor". When your code wrapped in shorcode is displayed in Visual editor, "Visual editor" will destroy your code.

= Legacy mode =

You can switch to "Legacy mode" through setting panel.
"Legacy mode" allows you to use the old style tag in SyntaxHighlighter ver. 1.5 library(Not mean WP SyntaxHighlighter ver. 1.5).

Your codes must be escape to HTML entities, even if using `<textarea>` method.

Just escape following 5 characters: <, >, &, ", '

See "[Ver. 1.5 Usage](http://code.google.com/p/syntaxhighlighter/wiki/Usage "Old style tag")".

In "Legacy mode", your selected brushes(languages) will be pre-loaded. Only pre-loaded brushes(languages) can highlight your source code with old style tag. The old style tag can't depended on "autoloader" in ver. 3.0 mode. By default, all brushes(languages) will be pre-loaded in "Legacy mode".

"Legacy mode" may save your previous posts with old style tag. However, No one should continue using old style tag. You must use "HTML editor" to type old style tag.

= Add new languages(brush files) =

You can add new languages(brush files) using "Action hooks" and associative array.

For details, see "sample" directory.

There is a sample plugin for adding new languages.

== Frequently Asked Questions ==

= Q. The highlighted code can not be displayed. The code is displayed as just raw. =

A. This plugin requires placing `<?php wp_head(); ?>` and `<?php wp_footer(); ?>` in your theme files. You must place `<?php wp_head(); ?>` before the closing head tag in header.php and place `<?php wp_footer(); ?>` before the closing body tag in footer.php.

= Q. Buttons are missing on my Visual editor. =

A. This pluguin requires TinyMCE(default Visual editor). If you replaced default Visual editor with other(e.g. CKEditor etc.), Buttons can't be shown on your Visual editor.

= Q. I want to change language and options of previously-markuped code with "pre" button. But I can't select whole my code. =

A. You need not to select whole your code. It's enough to select first line of your code.

= Q. After the update to new version, Buttons do not work correctly. =

A. Old javascript files may be cached. Please clear your browser's cache or delete cached javascript files.

= Q. How to stop translating my code with "Google Translate". =

A. Go to setting panel and enter "notranslate" into "Class name". When another class has already been defined, add "notranslate" separated by space.

= Q. I get a message saying "invalid" in a textarea on setting panel. =

A. The value of textarea contains some character strings that are not allowed to use. Re-enter valid value and save it. It may occur after you upgraded to 1.5.6 or higher.

= Q. "SH pre" button(Quicktag) or other buttons in the editor doesn't pop up a dialog box. =

A. Go to setting panel and try to enable/disable "Load jQuery and jQuery UI" option in "HTML Editor Settings"(or "Comment Editor Settings") section.

== Screenshots ==

1. This is a highlighted code.
2. This is a highlighted code in comments and buttons added to the form.
3. This is a highlighted code in bbPress and buttons added to the form
4. This is setting panel.
5. This is "SH TinyMCE" button.
6. This is pop up window at the click of "pre" button.
7. This is pop up window at the click of "CODE" button.
8. This is "SH pre" button.
9. This is pop up window at the click of "SH pre" button.

== Changelog ==

= 1.7.1 =
* Fix a bug: Filters can't handle sourcecodes including $n(n = numbers) correctly.

= 1.7 =
* Support [bbPress](http://wordpress.org/extend/plugins/bbpress/ "bbPress") plugin.
* Support [shortcode](http://en.support.wordpress.com/code/posting-source-code/ "shortcode").
* Added another button which makes easy to insert "shortcode" for the comment form.
* Added another button which makes easy to insert "shortcode" for "WP SyntaxHighlighter Widget".
* Users without "unfiltered_html" also can edit a post with codes.
* Comments before approved can be highlighted.
* Added new filters for posts.
* Replaced filters for comment with new ones.
* A part of defaut setting values changed.
* Bundled sample plugin can add new language(Biferno).
* Splited the main php file in order to reduce file size.
* Fix a bug: Added filters for comment form can't handle sourcecodes including `&amp;#xxx;` correctly.
* Fix a bug: Using bloginfo() in the wrong way.
* Fix a bug: Some missing textdomains.
* Fix a bug: The mix LF and CR/LF in a part of SyntaxHighligter core files.

= 1.6.7 =
* Modified Quicktag processing to be compliant with WordPress 3.3.
* Added "Quick code copy" option into the setting panel.
* Fix a bug: Wrong textdomain fot localization.
* Fix a bug: Wrong replacing `</pre>` tag in the code when comment updated.

= 1.6.5 =
* Added "SH pre" button into the Comment editor.
* Added "Load jQuery and jQuery UI" option into the setting panel.
* Enabled to escape souececodes to HTML entities automatically when updating a commnet.
* Fix a bug: Loaded jQuery UI component affects other ajax buttons.
* Fix a bug: When using WordPress 3.0.6 or older, "SH pre" button doesn't work.
* Fix a bug: When using WordPress 2.9.2 or older, after saving changes, some setting values get empty. Some problems might come to the surface after upgrading to WordPress 3.0 or higher.
* Fix a bug: TinyMCE "code" button can't handle sourcecodes including HTML entities(`&amp;`, `&lt;`, `&gt;`, `&quot;`, `&#039`;) correctly.
* Fix a bug: Added filters for comment form can't handle sourcecodes including HTML entities(`&amp;`, `&lt;`, `&gt;`, `&quot;`, `&#039`;) correctly.
* Fix a bug: "WP SyntaxHighlighter Widget" can't handle sourcecodes including HTML entities(`&amp;`, `&lt;`, `&gt;`, `&quot;`, `&#039`;) correctly.
* Fix a bug: A typo in setting values.

= 1.6 =
* Added "SH pre" button to HTML editor. It make easy to wrap your code in `<pre>` tag.

= 1.5.8 =
* Rewritten the codes for array definition.
* Fix a bug: When using code button, some browsers give extra line break.

= 1.5.7 =
* Validating the setting values more closely.
* Added the icon before title block on the setting panel.
* Fix a bug: The setting data migration processing can't work concurrently with auto-update.
* Fix a bug: A error message don't be translated.

= 1.5.5 =
* Added new setting option to change text label for collapsed code block.
* Most of setting parameters are stored as associative arrays in SQL.
* Redesigned setting panel.
* Changed the method of displaying the notice message for admin.
* Only when "comments_open()" is true, "Comment Highlight Button" processing is run.
* After "Comment Highlight Button" is enabled, force on "comments" option.
* Moved javascript files to "js" directory.(except TinyMCE plugin and SyntaxHighlighter library)
* Moved css files to "js" css.(except SyntaxHighlighter library)
* Bundled sample plugin support i18n and new method of displaying the notice for admin.
* Changed line feed codes to LF.(except SyntaxHighlighter library, added brush and text files) The line feed codes were CR/LF in the many of files.
* Checking if the current request carries a valid nonce when settings are saved or reset.
* Changed default text which is show before comment form. Thanks Judah.
* Changed the processing when plugin is updated.
* Fix a bug: Unnecessary `<form>` tags in source code of setting panel.
* Fix a bug: Incorrect target versions of brush files are shown in the setting panel.

= 1.5 =
* Added the widget to show highlighted code.
* Added "Comment Highlight button" to post a comment with their sourcecodes and highlight them easily.
* Bundled sample plugin can add new languages(DOS batch file, Objective-C). Thanks [Andreas Breitschopp](http://www.ab-tools.com/en/ "Andreas Breitschopp") who created "shBrushDosBatch-V2.js" and "shBrushDosBatch-V3.js" for WP SyntaxHighlighter.
* Allowed to enter single quotes into "Title".
* Changed the way to show target versions of brush files in the setting panel.
* Changed the way to show notice for admin.
* Using dirname() and plugin_basename() instead of hardcoded directory name.
* Changed directory name stored translation files.

= 1.4.4 =
* Added new theme "None" that allows to apply no stylesheet.
* TinyMCE buttons supported new fullscreen mode in WordPress 3.2 or higher.
* Fix a bug: "Insert" function of "pre" button does not work in the fullscreen mode.
* Fix "Notice: Undefined variable: page_highlight" and "Notice: Undefined variable: comment_highlight".
* Bundled sample plugin can add new languages(Clojure, LISP, MEL Script, PowerCLI, Vim Script).

= 1.4.3.1 =
* Fix a bug: The misdescribed `<script>` tag.
* Modified Japanese translation.

= 1.4.3 =
* Added new theme "Random" to apply different theme on each page.
* Added break statements to foreach loops for performance improvement.
* Changed the conditional branching for performance improvement.
* Changed "the_content" hook priority to prevent conflict.
* Added "others" checkbox into "Higlight your code in" option to allow highlighting in particular pages.
* Added new hook to add css files for external plugin.
* Added new method to add brush files for external plugin.
* Bundled new sample plugin.

= 1.4 =
* "Autoloading JavaScripts" function will be applicable to not only posts and pages but also home, archives, search results and comments.
* This plugin allows visitors to post comments with their code.
* Changed the conditional branching for adding stylesheet.
* Fix a bug: The addtional stylesheet isn't added in Categories, Archives and Search result.
* Fix a bug: A message can't be translate into Japanese.

= 1.3.9 =
* Added "Higlight your code in" option that allows you highlight your code in Categories, Archives and Search result.
* Added "Title" and "Line Number Padding" options.
* Users allows "Default languages settings" to be reflected in buttons and "autoloader".
* Changed processing code for creating buttons.
* Changed the method to add javascripts and css into setting panel.
* Fix a bug: The misdescribed argument of wp_enqueue_style() function.
* Fix a bug: WP SyntaxHighlighter don't work with [WP to Twitter](http://wordpress.org/extend/plugins/wp-to-twitter/ "WP to Twitter") etc.

= 1.3.8.2 =
* Fix a bug: The misdescribed `<script>` tag caused HTML validation errors.

= 1.3.8.1 =
* The value of $wp_sh_ver was updated.

= 1.3.8 =
* Fix a bug: When selectd ver.2.1.382, "copy to clipboard" icon is disappeared from tool bar.

= 1.3.7 =
* Added the option to choose pop-up windows size.
* Changed the method to get plugin directory url.

= 1.3.6 =
* Fix "Notice: has_cap was called with an argument that is deprecated since version 2.0! Usage of user levels by plugins and themes is deprecated. Use roles and capabilities instead." when "WP_DEBUG" is turned on.
* Changed processing javascripts for admin panel.

= 1.3.5 =
* Can define your own stylesheet for the code block.
* Can choose TinyMCE toolbar row which buttons will be placed in.
* Add legacy `<pre>` and `<textarea>` tags and attribtes to TinyMCE extended_valid_elements.
* Added "System Info" in setting panel.
* Changed processing code for creating buttons.

= 1.3 =
* In ver. 2.0 or "Legacy mode", loaded languages become pre-selectable through setting panel.
* "Reset All Setting" button Added to setting panel.
* `<script>` and `<textarea>` tag methods are supported.
* Fix a bug: In "Legacy mode", the source code with old style tag can't be highlighted correctly.

= 1.2.3 =
* Added more language for ver. 2.1.382 by defaut, or this version will load all bundled languages for ver. 2.1.382 by defaut.(added languages are: Actionscript3, Bash shell, ColdFusion, Diff, Erlang, Groovy, JavaFX, Perl, Plain Text, PowerShell,Scala)
* Added "Action hooks" and associative array for developers.
* Comments before approved can be highlighted.
* Fix a bug: When chosen "MDUltra" theme in ver. 2.1, "Your Current Theme" in setting panel can't be updated correctly.

= 1.2.2 =
* To prevent conflict with other TinyMCE button, the priority of a function hooked has been changed.
* Removed an unnecessary file from the file set.

= 1.2.1 =
* Fix a bug: "CODE" button does not work in fullscreen mode.

= 1.2 =
* Added "CODE" button which allows to paste sourcecode into post or page, keeping indent by tab.
* Enable to change language and options of previously-markuped code.
* Button icons has been changed.
* layout of buttons on TinyMCE popup window has been changed.

= 1.1.1 =
* Fix a bug: Sometimes Code do not be highlighted in home.

= 1.1 =
* Adding the button to type `<pre>` tag in Visual editor.
* You can preview your current theme in setting panel.
* Fix a bug: Setting values in databese can't be removed when uninstall.

= 1.0.1 =
* Fix a warning in setting panel.

= 1.0 =
* This is the initial release.

== Upgrade Notice ==

= 1.7.1 =
This version has a bug fix.

= 1.7 =
This version has new features, changes and bug fixes.

= 1.6.7 =
This version supports WordPress 3.3. This version has a new feature.

= 1.6.5 =
This version has new features and bug fixes.

= 1.6 =
This version has a new feature.

= 1.5.8 =
This version has a change and bug fix.

= 1.5.7 =
This version has some changes and bug fixes.

= 1.5.5 =
This version has new features, changes and bug fixes. New bundled sample plugin, "Lang Pack for WP SyntaxHighlighter 1.2" is released. If you use it, please update it.

= 1.5 =
This version has new features and changes.

= 1.4.4 =
This version has a new feature, change and bug fixes.

= 1.4.3.1 =
This version has a bug fix and change.

= 1.4.3 =
This version has a new feature and some changes.

= 1.4 =
This version has some new features, changes and bug fixes.

= 1.3.9 =
This version has some new features, changes and bug fixes.

= 1.3.8.2 =
This version has a bug fix.

= 1.3.8.1 =
This version has a low-priority change.

= 1.3.8 =
This version has a bug fix.

= 1.3.7 =
This version has a new feature and change.

= 1.3.6 =
This version has some changes and bug fix.

= 1.3.5 =
This version has some new features and changes.

= 1.3 =
This version has some new features, changes and a bug fix.

= 1.2.2 =
This version has some low-priority changes.

= 1.2.1 =
This version has a bug fix.

= 1.2 =
This version has some new features.

= 1.1.1 =
This version fixes a bug.

= 1.1 =
This version has some new features and a bug fix.

= 1.0.1 =
This version fixes a warning in setting panel.

= 1.0 =
This is the initial release.
