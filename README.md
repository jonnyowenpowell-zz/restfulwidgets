# RESTful Widgets!

RESTful Widgets is a Wordpress plugin which adds a single widget type, the RESTful Widget. This widget gives users access to resources from RESTful API endpoints via a series of customisable input fields, corresponding to endpoint query string parameters. The data type of the fields, the range of values for the numeric fields, and the endpoint URL are all specified in the admin settings, per widget.

### Building the plugin from this repository

The plugin is available on in the Wordpress plugins directory, but should you wish to build the plugin in a development environment, you can clone this repository.


The plugin is written in pure PHP, Sass (a CSS extension language) and CoffeeScript (a JavaScript extension language). In order to build the plugin, the Sass and CoffeeScript must be compiled and placed in the build directory.

#### Building

We use Gulp to manage the build process, and npm to manage packages needed by the build.
If you don't have npm, read this: https://docs.npmjs.com/getting-started/installing-node.
Once npm is installed, cd to your clone of this repository, run `npm install`, and then `gulp`. The build directory defaults to a `build` folder inside your clone, but you can configure this in the config object in `gulpfile.litcoffee`, detailed below.

#### Building and watching

For development, you may also run `gulp build:watch` which will recompile any files you change on the fly, which it is running. It will also spawn a BrowserSync server, which will inject the recompiled files into the Wordpress page you are on, without you needing to manually reload. To make this work, you need to configure some options in the config object in `gulpfile.litcoffee`, again, detailed below.

### `gulpfile.litcoffee` `config` object

* `developmentBuild`
 * `true` Adds Sourcemaps to Coffee and Sass outputs.
 * `false` Minifies Coffee and Sass outputs.
* `notifyOnSuccess`
 * `true` Push notifications shown whenever files compile, indicating success or failure.
 * `false` Push notifications shown only when files fail to compile.
* `version`
* `(coffee|php|sass)Glob` Globs for respective files to compile or lint. Relative to base of repository.
* `buildpath` Location for compiled plugin files (can be set to a local wordpress installs plugin directory for live reloading). Relative to base of repository.
* `localWordpressUrl` URL of local Wordpress install, used by BrowserSync, for example `localhost/~user/wordpress`.