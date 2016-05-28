## RestFUL Widgets Gulpfile

## Startup

### Build configuration object
    
    config =
      developmentBuild:    false
      notifyOnSuccess:     false
      version:             '0.1'
      coffeeGlob:          './coffee/**/*.?(lit)coffee'
      phpGlob:             'php/**/*.php'
      sassGlob:            'sass/**/*.sass'
      buildPath:           'build'
      localWordpressUrl:   'localhost/wordpress/'

### Require the necessary gulp plugins
    
    changed      = require 'gulp-changed'
    coffee       = require 'gulp-coffee'
    gulp         = require 'gulp'
    gulpif       = require 'gulp-if'
    sass         = require 'gulp-ruby-sass'
    shell        = require 'gulp-shell'
    sourcemaps   = require 'gulp-sourcemaps'
    uglify       = require 'gulp-uglify'

### Require the necessary general node modules
    
    browserSync  = require 'browser-sync'
                    .create()
    node_notify  = require 'node-notifier'
    path         = require 'path'

## Functions

### Notifier function

    notify = (taskDescription, logoName) ->
      subtitleSuffix = if @error then 'error' else 'complete'
      message = if @error then 'Oops!' else 'Hurray!'
      if @error or config.notifyOnSuccess
        node_notify.notify
          icon:        path.join __dirname, 'notify-assets', logoName
          title:       'Gulp'
          subtitle:    taskDescription + ' ' + subtitleSuffix
          message:     message

### CoffeeScript functions

    compileCoffeeScript = ->
      taskProgress = error: false
      onError = ->
        taskProgress.error = true
        @emit 'end'

      coffeeNotify = notify.bind taskProgress,
                                 'CoffeeScript compilation',
                                 'coffee-logo.png'

      return gulp.src config.coffeeGlob
        .pipe changed config.buildPath + '/js', extension: '.js'
        .pipe gulpif config.developmentBuild, sourcemaps.init()
        .pipe coffee()
        .on 'error', onError
        .on 'end', coffeeNotify
        .pipe gulpif !config.developmentBuild, uglify()
        .pipe gulpif config.developmentBuild, sourcemaps.write()
        .pipe gulp.dest config.buildPath + '/js'

    reloadCoffeeScript = ->
      return compileCoffeeScript()
        .pipe browserSync.stream match: '**/*.js'

### PHP functions

    lintPhp = ->
      taskProgress = error: false
      onError = ->
        taskProgress.error = true
        @emit 'end'

      phpNotify = notify.bind taskProgress,
                              'PHP linting',
                              'php-logo.png'

      return gulp.src config.phpGlob
        .pipe changed config.buildPath
        .pipe shell 'php -l php/<%= file.relative %>',
          quiet: true
        .on 'error', onError
        .on 'end', phpNotify
        .pipe gulp.dest config.buildPath

    reloadPhp = ->
      lintPhp()
      browserSync.reload()

### Sass functions

    compileSass = ->
      taskProgress = error: false
      onError = ->
        taskProgress.error = true
        @emit 'end'

      sassNotify = notify.bind taskProgress,
                               'Sass compilation',
                               'sass-logo.png'

      sass.clearCache()
      return sass config.sassGlob,
          style: if config.developmentBuild then 'expanded' else 'compressed'
          emitCompileError: true
          sourcemap: config.developmentBuild
          quiet: true
        .on 'error', onError
        .on 'end', sassNotify
        .pipe gulp.dest config.buildPath + '/styles'

    reloadSass = ->
      return compileSass()
        .pipe browserSync.stream match: '**/*.css'

## Tasks

### CoffeeScript tasks

    gulp.task 'coffee', compileCoffeeScript
    gulp.task 'coffee:watch', -> gulp.watch config.coffeeGlob, reloadCoffeeScript

### PHP tasks
    
    gulp.task 'php', lintPhp
    gulp.task 'php:watch', -> gulp.watch config.phpGlob, reloadPhp

### Sass tasks

    gulp.task 'sass', compileSass
    gulp.task 'sass:watch', -> gulp.watch config.sassGlob, reloadSass

### BrowserSync server task

    gulp.task 'browser-sync', ->
      browserSync.init proxy: config.localWordpressUrl

### Build tasks

    gulp.task 'build',
              gulp.parallel 'coffee',
                            'php',
                            'sass'
    gulp.task 'build:watch',
              gulp.parallel 'browser-sync',
                            'coffee:watch',
                            'php:watch',
                            'sass:watch'

### Make `build` the default task

    gulp.task 'default', gulp.parallel 'build'