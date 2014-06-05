---
layout: post
title: On the road to an Ember app
---

I absolutely love [Ember.js](http://emberjs.com/). The framework is extremely powerful and is able to handle so much boilerplate that developing an Ember app is a real joy.

I always imagined that converting an existing large, “multi-page” site into an Ember app would be a complicated process. But it recently occurred to me that it might be easier than expected&hellip; once we sacrifice the router and temporarily transition through a hybrid Ember site.


### The markup

Let’s say we have a homepage:

    <!-- home.html -->
    <body>
        <header></header>
        <h1>Homepage</h1>
        <footer>
            <button id="press-me">Press me!</button>
        </footer>
    </body>

And an about page:

    <!-- about.html -->
    <body>
        <header></header>
        <h1>About</h1>
        <footer>
            <button id="press-me">Press me!</button>
        </footer>
    </body>

To Ember-ify this, we basically move all the page-specific content into an “index” template within those pages:

    <!-- home.html -->
    <body>
        <script type="text/x-handlebars" data-template-name="index">
            <h1>Homepage</h1>
        </script>
    </body>

<!-- -->

    <!-- about.html -->
    <body>
        <script type="text/x-handlebars" data-template-name="index">
            <h1>About</h1>
        </script>
    </body>

And move the rest into the application template:

    {{ "{{!-- application.hbs --" }}}}
    <body>
        <header></header>
        {{ "{{outlet" }}}}
        <footer>
            <button id="press-me">Press me!</button>
        </footer>
    </body>


### The scripts

Let’s say our original scripts are something like this:

    function init() {
        $('#press-me').on('click', function() {
            alert('hi there!')
        })
    }

    // When the document is ready, initialize the scripts.
    $(document).ready(init)

In order to let Ember take over, we need to do some setup *before* initializing the scripts. So we change it to something like this:

    function init() {
        $('#press-me').on('click', function() {
            alert('hi there!')
        })
    }

    var App = Em.Application.create()

    // Disable the router.
    App.Router.reopenClass({
        location: 'none'
    })

    // Once the `index` template of each individual page is
    // inserted into the DOM, initialize the non-Ember scripts.
    App.IndexView = Em.View.extend({
        didInsertElement: function() {
            init()
        }
    })


### And that’s pretty much it :smile:

We got our old application code working as it was before without having to tweak it. And we can build any new features using the power of Ember views.

However, we obviously don’t want to **not** use the epic Ember router. That would be a tragedy. But that is also the trickier part. It heavily depends on your backend setup and your ability to map your URLs over correctly to the Ember router.

That is out of scope of this post. But this is the first step in getting to a full-fledged Ember app.

Now all this is a great start and all - but until we can completely transition to Ember router powered views, we need to have ways to communicate between our Ember logic and old application logic.


### Communicating from Ember to non-Ember

This one is pretty obvious. We pass this responsibility on to the `ApplicationController` or `IndexController` based on whichever is more suitable for the scenario:

    <!-- home.html -->
    <body>
        <script type="text/x-handlebars" data-template-name="index">
            <h1>Homepage</h1>
            <button {{ '{{action "say-hello"' }}}}>Say hello!</button>
        </script>
    </body>

<!-- -->

    App.IndexController = Em.Controller.extend({
        actions: {
            'say-hello': function() {
                Em.$('#press-me').trigger('click')
            }
        }
    })


### Communicating from non-Ember to Ember

To do things the other way around, we need to make sure the Ember view we want to communicate with has an explicit ID set on it:

    <!-- home.html -->
    <body>
        <script type="text/x-handlebars" data-template-name="index">
            <h1>Homepage</h1>
            <button id="some-button">Trigger Ember action</button>
            {{ '{{ui-component id="some-ember-component"' }}}}
        </script>
    </body>

<!-- -->

    $('#some-button').on('click', function() {

        // All Ember views in the DOM are referenced under
        // the `views` object by the element’s ID.
        var uiComponent = Em.View.views['some-ember-component']

        // Do whatever you like with uiComponent now!
    })


### That’s all there is to it :+1:

I’ve used this method in several example apps and it has worked great for me so far. I’d love to hear about how other people have gone about dealing with hybrid sites and issues they’ve come across.