---
layout: post
title: The Simplest Gruntfile
postscript: [
    'Any dependencies that aren’t Grunt tasks can be listed under `dependencies`, otherwise Grunt will try to load it as a task and fail.'
]
---

More often than not, I’ve come across Gruntfiles that are monstrous configuration blocks that are difficult to digest. This is most likely an artifact of how the official guides suggest constructing a Gruntfile, to make it easy for beginners to get started with Grunt.js.

However, there is a much cleaner and modular approach we can take by separating the tasks and configurations into their own individual modules. What that ends up as is a tiny Gruntfile whose sole purpose is to load tasks from a directory and initialize them with configurations from another.

Here’s the Gruntfile in it’s entirety:

```javascript
/**
 * This Gruntfile is used to import configs and tasks
 * from the `node_configs` and `node_tasks` folders.
 */

'use strict';

module.exports = function(grunt) {
    initTasks(grunt, 'node_tasks')
    initConfigs(grunt, 'node_configs')
}

function initTasks(grunt, folderPath) {
    var pkg = grunt.file.readJSON('package.json')
    var tasks = pkg.devDependencies
    delete tasks.grunt
    for ( var task in tasks ) {
        grunt.loadNpmTasks(task)
    }
    grunt.loadTasks(folderPath)
}

function initConfigs(grunt, folderPath) {
    var config = {}
    grunt.file.expand(folderPath + '/**/*.js').forEach(function(filePath) {
        var fileName = filePath.split('/').pop().split('.')[0]
        var fileData = require('./' + filePath)
        config[fileName] = fileData
    })
    grunt.initConfig(config)
}
```


### The breakdown

The first four lines are the gist of the work:

```javascript
module.exports = function(grunt) {
    initTasks(grunt, 'node_tasks')
    initConfigs(grunt, 'node_configs')
}
```

We first initialize tasks from the `node_tasks` directory and then we initialize configurations for those tasks from the `node_configs` directory.


#### `initTasks(grunt, folderPath)`

Within the `initTasks` function, we first read the `package.json` file:

```javascript
var pkg = grunt.file.readJSON('package.json')
```

...grab the `devDependencies`, which should only have Grunt tasks<sup>[1](#postscript_1)</sup>:

```javascript
var tasks = pkg.devDependencies
delete tasks.grunt
```

...and load the named NPM tasks and our own custom tasks and aliases:

```javascript
for ( var task in tasks ) {
    grunt.loadNpmTasks(task)
}
grunt.loadTasks(folderPath)
```


#### `initConfigs(grunt, folderPath)`

Within the `initConfigs` function, we create an empty `config` object and glob through the `node_configs` directory:

```javascript
var config = {}
grunt.file.expand(folderPath + '/**/*.js').forEach(...)
```

...grab each file’s name, which should be the name of the task, and add it to the `config` object:

```javascript
var fileName = filePath.split('/').pop().split('.')[0]
var fileData = require('./' + filePath)
config[fileName] = fileData
```

...and finally, we initialize the configuration:

```javascript
grunt.initConfig(config)
```


### Setting up `node_configs` and `node_tasks`

These directories should look something like this:

    node_configs/
        ├── autoprefixer.js
        ├── connect.js
        ├── handlebars.js
        ├── jshint.js
        ├── less.js
        ├── uglify.js
        └── watch.js
    node_tasks/
        ├── aliases.js
        ├── custom-task.js
        └── another-custom-task.js

Each task gets it’s own configuration file in `node_configs`:

```javascript
// node_configs/connect.js
'use strict';
var grunt = require('grunt')
module.exports = {
    options: {
        port: grunt.option('port') || 9001
    },
    server: {
        // ...
    }
}
```

Each custom task gets it’s own registration file in `node_tasks`:

```javascript
// node_tasks/custom-task.js
'use strict';
module.exports = function(grunt) {
    grunt.registerMultiTask('custom-task', 'Make Grunt do something custom', function() {
        // ...
    })
}
```

And all the task aliases, such as the default task, live in `node_tasks/aliases.js`:

```javascript
'use strict';
module.exports = function(grunt) {
    grunt.registerTask('default', ['less', 'autoprefixer'])
    grunt.registerTask('server', ['connect', 'watch'])
}
```


### Brilliant :bowtie:

I’ve found this to be much more flexible and extremely simple to find your way around all the tasks and configurations for complex build steps.
