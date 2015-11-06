---
layout: post
title: "&ldquo;Yo dawg, I heard you like build tools&hellip;&rdquo;"
meta: Build tools, like Gulp & Grunt, only add bloat when it comes to complex setups. On the other hand, npm was built for this.
postscript: [
    'The power of npm in managing packages has also pushed Bower out of the limelight. It is now a dying project, and rightfully so.'
]
---

When most front-end devs think of npm, they think of package management. They think it's a thing built into Node.js just to help manage dependencies.

And I don't blame them for not knowing any better. Heck, I didn't!

However, recently, I stumbled upon a tweet by an ex-collegue of mine:

<blockquote class="twitter-tweet" lang="en"><p lang="en" dir="ltr">Learning <a href="https://twitter.com/gulpjs">@gulpjs</a> has improved my workflow so much! I&#39;m realy only using it for vendor prefixing + sass/js sourcemaps on this project but omg</p>&mdash; NataSpooks (@natacoops) <a href="https://twitter.com/natacoops/status/657558536302542848">October 23, 2015</a></blockquote>

<blockquote class="twitter-tweet" data-conversation="none" lang="en"><p lang="en" dir="ltr">...even those small improvements have changed my world! Thx <a href="https://twitter.com/wesbos">@wesbos</a> for the awesome workshop. 👌</p>&mdash; NataSpooks (@natacoops) <a href="https://twitter.com/natacoops/status/657558904763760640">October 23, 2015</a></blockquote>

To which I replied:

<blockquote class="twitter-tweet" data-conversation="none" data-cards="hidden" lang="en"><p lang="en" dir="ltr"><a href="https://twitter.com/natacoops">@natacoops</a> <a href="https://twitter.com/wesbos">@wesbos</a> i had my honeymoon phase with build tools like gulp&amp;grunt too! Until i realized this: <a href="https://t.co/vkcrT24VZ9">https://t.co/vkcrT24VZ9</a> 🙌🙌</p>&mdash; amsul (@amsul_) <a href="https://twitter.com/amsul_/status/657583299204882432">October 23, 2015</a></blockquote>

To which Wes Bos replied:

<blockquote class="twitter-tweet" data-conversation="none" lang="en"><p lang="en" dir="ltr"><a href="https://twitter.com/amsul_">@amsul_</a> <a href="https://twitter.com/natacoops">@natacoops</a> I&#39;ve yet to see any complex examples with that. I use it for simple stuff but find gulp much easier to write</p>&mdash; Wes Bos (@wesbos) <a href="https://twitter.com/wesbos/status/657584451531051008">October 23, 2015</a></blockquote>

<script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>

So, I figured I'd take this opportunity to show exactly why I stopped using Gulp.

Actually, not just Gulp, but rather any build tool or task runner on top of Node.js.

But first, let's clear some stuff up:

1. It is not easy to write with Gulp; streams are anything but trivial. It is only easy to utilize existing Gulp plugins to quickly get up and running.
* Everything that exists in the Gulp ecosystem (and **much** more) already exists in the npm ecosystem, except with stronger community support and faster releases.
* Using npm scripts does *not* mean needing to learn how to write `bash`; you can write in JavaScript just as easily. :bowtie:


### "What the hell are npm scripts?" :confused:

If that's your question and you've used Gulp/Grunt before, I'm glad you're here.

In a nutshell, [npm scripts](https://docs.npmjs.com/misc/scripts) give you the power to run any script from the command line with a simple:

```bash
npm run <script_name>
```

Y'know, exactly like you'd do `gulp <task_name>`, except without having to install any additional tools.

[Keith Cirkel talks about it plenty in his blog post](http://blog.keithcirkel.co.uk/how-to-use-npm-as-a-build-tool/). So instead of reiterating what he's said, I'm going to go through a real-life scenario of project requirements and see how both systems differ in their approach to development and future-friendliness.



### A "relatively" complex setup

Here's what my current setup requires:

1. Preprocess SCSS to CSS
* Preprocess JSX to JS
* Preprocess ES2015 (and beyond) to ES5
* Launch a static server that serves the compiled files from memory
* Watch files for changes to recompile
* Write files to disk only when a separate script is run
* Build and launch an iOS (XCode) and Android (Android Studio) app using the compiled files
* Version bump in `package.json` and in the iOS and Android configuration files
* Run tests in various environments and instrument files to gather coverage reports


#### Requirements 1 - 6: developing, serving, & compiling

These requirements (or a variation of them) is a very common scenario for web devs these days.

Just a little while ago, you'd need a *bunch* of tools to get all of this set up. Thankfully, the noble Tobias Koppers (and later the community) built [webpack](http://webpack.github.io/) for us. :pray:

If you haven't had the chance to use it yet, I highly suggest you take it for a spin.


##### The Gulp way

Before even getting started with the setup, you need to figure out if a Gulp plugin exists for webpack.

If one exists, you need to evaluate your choices by learning their APIs and comparing which works best (for your scenario) with the webpack API.

If one does not exist, you need to learn how to write Gulp tasks that work with the webpack API.

Either way, you have to learn the webpack API. But instead, you'll spend the majority of your time with Gulp-related activities.

There are several different ways to set up webpack with Gulp. I was going to write out an example, but honestly with the verbosity and various different ways to accomplish things, it's not even worth it.

Instead you can look at the docs by the first Google result for a Gulp-webpack plugin: [webpack-stream](https://github.com/shama/webpack-stream).


##### The npm way

This is the gist of it:

```js
// scripts/webpack.js
var compiler = webpack(config)
var server   = new WebpackDevServer(compiler, {})
server.listen(8000, 'localhost')
```

You directly learn [how webpack works on Node.js](http://webpack.github.io/docs/Node.js-api.html) and you set up an npm script to run it:

```js
// package.json
{
    "scripts": {
        "start": "node ./scripts/webpack.js"
    }
}
```

To write the files to disk instead of launching a webpack-dev-server, you can pass a flag to the script:

```js
// package.json
{
    "scripts": {
        "start": "node ./scripts/webpack.js",
        "build": "npm start -- --build"
    }
}
```

And the script can react to the flag (simplified with [Commander.js](https://github.com/tj/commander.js)):

```js
// scripts/webpack.js
if (program.build) {
    writeFilesToDisk()
}
else {
    startServer()
}
```

Moving on.


#### Requirement 7: building and launching native apps

Before building the native apps with the latest web code, the files are copied from the compiled source into each platform's directory. Then either [`xcodebuild`](https://developer.apple.com/library/mac/documentation/Darwin/Reference/ManPages/man1/xcodebuild.1.html) or [`adb`](http://developer.android.com/intl/ko/tools/help/adb.html) is used to build and launch the respective app on a connected device.


##### The Gulp way

There is definitely no plugin out there to do specifically this. So, now there are two possibilities:

1. Write a Gulp task that communicates with `xcodebuild` and `adb` through their command line APIs.
* Write a Node.js script that communicates with `xcodebuild` and `adb` through their command line APIs and exposes an API that enables it to work with Gulp, then write a Gulp task to run the script.

The problem with possibility #1 is that you're tied in with Gulp. Tools should be easy to replace and being bound into one system is at the crux of stagnation when better ways of doing things are realized.

The problem with possibility #2 is that it involves a lot of effort and therefore time. The Node.js script needs to be flexible enough to work with or without Gulp.


##### The npm way

There is no difference here from any other npm script. You directly work with the `xcodebuild` and `adb` command line APIs.


#### Requirement 8: version bumping

The version in the `package.json` file is used to manage the versions in each platform's configuration file. For iOS, that's the project's main `.plist` file; for Android, that's the `AndroidManifest.xml` file.

To bridge the gap between native and web code, I currently use [Cordova](https://cordova.apache.org/) (although hopefully soon [that won't be the case](https://facebook.github.io/react-native/)).

So, to bump the version, I utilize it's internal ability to update the configuration files for each platform and just perform version bumps.


##### The Gulp way

There are several Gulp plugins that help with versioning. However, none of them update the versions in configuration files for iOS or Android apps.

So, you'd have to hook into one of the Gulp plugins' API to use the updated version and pipe it into another Gulp task. This version would then be passed to a custom Node.js script which would then in turn update the versions in each configuration file.


##### The npm way

Some younger developers (in terms of Node.js experience) don't know that [npm actually comes with versioning built in](https://docs.npmjs.com/cli/version).

To do something custom when the version updates, simply add a `version` script:

```js
// package.json
{
    "scripts": {
        "version": "node ./scripts/version-bump.js"
    }
}
```

And version bump as you normally would with npm:

```bash
npm version 3.2.1
```

The version bumps to `3.2.1` in `package.json` and then `./scripts/version-bump.js` is run. The updated files are then committed and tagged with the bumped version. :star2:


#### Requirement 9: running tests and recording coverage

I use the [Mocha](https://mochajs.org/) testing suite for writing tests.

For quick and easy testing in the browser, I use the Mocha runner launched with [webpack-dev-server](http://webpack.github.io/docs/webpack-dev-server.html).

On the CI ([Travis](https://travis-ci.org/)), I use [Karma](http://karma-runner.github.io/) to launch [PhantomJS](http://phantomjs.org/) as the testing environment where the Mocha suite is run. To instrument the files and record coverage, I use [Istanbul](https://github.com/gotwarlost/istanbul) and send the reports over to [Coveralls](http://coveralls.io/).


##### The Gulp way

To run Mocha in the browser, you can utilize the Gulp task that launches your webpack-dev-server.

However, setting up the tools to run on Travis means directly working with these tools. Gulp has nothing to offer.

In fact, this is a quote from Karma's [official `gulp-karma` repo](https://github.com/karma-runner/gulp-karma):

> You don't need any gulp plugins to run Karma from the Gulp-based build, use Karma directly

Okay, so for Karma and PhantomJS, you'd use the npm way, and then wrap that with a Gulp task (instead of an npm script, because y'know&hellip; the hype). And you'd use something like [gulp-istanbul](https://github.com/SBoudrias/gulp-istanbul) for instrumenting with Istanbul and [gulp-coveralls](https://github.com/markdalgleish/gulp-coveralls) for sending reports to Coveralls. :weary:

##### The npm way

Practically the same - minus the need to wrap everything in a Gulp task. :relieved:


### Use tools to build instead of building to use tools

When using Gulp, or any of it's sibling tools, you start off by learning the Gulp API and it's numerous quirks and differences from other build tools. Then you'd get around to solving your actual problem by using the build tool's API that wraps the actual API of the tool you want to use.

The workflow becomes:

1. Learn how to use Gulp
* Learn how to use npm to the extent of installing packages
* `npm install gulp --global`
* `npm install gulp --save-dev`
* `npm install <tool_i_want_to_use>`
* Write the Gulp task to run the tool I want to use in `Gulpfile.js`
* Run the Gulp task with `gulp <task_name>`


Instead of more directly addressing the problem:

1. Learn how to utilize npm properly
* `npm install <tool_i_want_to_use>`
* Write the npm script that runs the tool in `package.json`
* Run the npm script with `npm run <script_name>`


Not only is the mental model divergent, but you also end up locked in with whatever tool you chose, which opens up the door to the evils of stagnation. :no_good:


So, my question to you is&hellip; what exactly is it Gulp has to offer, besides redundancy?
