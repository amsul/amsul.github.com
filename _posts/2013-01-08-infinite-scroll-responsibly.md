---
layout: post
postscript: [
    '**K**eep **I**t **S**imple, **S**tupid.',
    'It&rsquo;s interesting to note that Google Images automagically loads more results - although not infinitely.'
]
title: Infinite scroll responsibly
---


Using infinite scroll on a site is conceptually quite simple: as the visitor approaches the bottom of the page, load more of whatever stuff they&rsquo;re looking at.

Yet, time and time again, I come across implementations without an understanding of the impact on the user experience (such as a footer that suddenly [vanishes out of view](http://jeffcouturier.com/2011/11/infinite-scroll-use-it-well-or-dont-use-it-at-all/)).

Common solutions to this are adding a time/[distance](http://jeffcouturier.com/2011/11/infinite-scroll-use-it-well-or-dont-use-it-at-all/) delay (super unpredictable), using a [sticky footer](http://mattsoave.com/blog/post.php?id=5) (yanky on some mobile devices), or using an [off-canvas layout](http://jasonweaver.name/lab/offcanvas/) (my preferred method). Or just KISS^[1](#postscript_1) it and stick with manual progression. There&rsquo;s absolutely nothing wrong with that! Heck, even Google still has pagination^[2](#postscript_2).

It&rsquo;s just a matter of figuring out how your application is most effective for the people using it &ndash; which is definitely [easier said than done](http://danwin.com/2013/01/infinite-scroll-fail-etsy/).