---
layout: post
title: SVG &amp; Fireworks for retina-ready sites
---


Developing and designing responsive sites requires a lot of forethought about the UI variations for all viewport sizes. This calls for many considerations and unique challenges at each media-query break point.

On top of varying UI requirements based on screen size, high-density pixel displays have introduced a new challenge: serving images for retina display devices. All sites developed without consideration for high-pixel density displays lose all crispness in images and logos. Now with the new Macbooks going retina, the web is about to get [a whole lot fuzzier](http://thenextweb.com/dd/2012/06/12/the-web-is-going-to-look-hideous-on-the-new-retina-macbook-pro/).

### One way about it..
[Github](http://github.com), as well as many others, go about this with [icon fonts](https://github.com/blog/1135-the-making-of-octicons). This method works **really** well. If you are able to serve custom fonts without [FOUT](http://paulirish.com/2009/fighting-the-font-face-fout/), and only require basic icon styling, then this is the best solution. However, there are some alignment issues to tackle, and handling and creating custom fonts is not the easiest task.

### SVG to the rescue!
Here&rsquo;s where the `.svg` format becomes incredibly useful. **S**calable **V**ector **G**raphics contain the mathematical data of the images rather than the pixel data &ndash; which allows them to be infinitely scalable.

### Demo
For a quick demonstration, here are two Facebook icon images shown at their original sizes of `25px × 25px`:

<figure class="alignleft"><img style="display:inline-block" src="/uploads/2012/05/fb.jpg"><small style="display:block;margin:4px 0 12px;color:#666">JPG - `13 kB`</small></figure>
<div class="alignleft">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
<figure class="alignleft"><img style="display:inline-block" src="/uploads/2012/05/fb.svg"><small style="display:block;margin:4px 0 12px;color:#666">SVG - `1 kB`</small></figure>

<div class="clear"></div>

And here are the same images scaled up 800%:

<figure class="alignleft"><img style="display:inline-block" width="200" height="200" src="/uploads/2012/05/fb.jpg"><small style="display:block;margin:4px 0 12px;color:#666">JPG - `13 kB`</small></figure>
<div class="alignleft">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
<figure class="alignleft"><img style="display:inline-block" width="200" height="200" src="/uploads/2012/05/fb.svg"><small style="display:block;margin:4px 0 12px;color:#666">SVG - `1 kB`</small></figure>

<div class="clear"></div>

The main thing to notice here, other than the obvious infinite scalability, is the difference in file sizes. Since `.svg` files hold no pixel data, the file sizes are incredibly small: `1 kB` vs `13 kB`. Using `.svg` removes the need for serving different images based on pixel density and saves a lot of bandwidth.

### SVG, take the wheel
Support for `.svg` images on mobile and desktop browsers is [surprisingly good](http://caniuse.com/#cats=SVG) and so I feel it&rsquo;s quite safe to use on all my projects from now on.


### Let&rsquo;s make some Fireworks
Unfortunately, there&rsquo;s no way to export files as `.svg` in Photoshop and I don&rsquo;t feel comfortable with Illustrator&rsquo;s rulers and grid system for pixel-specific design.

Adobe Fireworks plus the [Export to SVG plugin](http://fireworks.abeall.com/extensions/commands/Export/) is the **perfect solution**. Yeah it&rsquo;s kinda dumb that Adobe hasn&rsquo;t developed built-in support for this in Fireworks, but this plugin is extremely simple to install. Once you&rsquo;ve installed the plugin, you will have an option under Commands > Export > Export SVG&hellip; and you&rsquo;re good to go.