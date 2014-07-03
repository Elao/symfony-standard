var
    gulp     = require('gulp'),
    gulpUtil = require('gulp-util');

var
    options = {
        assetsTarget : 'web/assets',
        customs      : [],
        isRubySass   : true,
        isDev        : gulpUtil.env.dev    ? true : false,
        isNotify     : gulpUtil.env.notify ? true : false
    };

// Default
gulp.task('default', function() {
    gulp.start('assets', 'watch');
});

// Assets
gulp.task('assets', function() {
    gulp.start('customs', 'css', 'sass', 'images', 'fonts', 'js');
});

// Watch
gulp.task('watch', function(callback) {
    return assets.watch(callback);
});

// Customs
gulp.task('customs', function() {
    return assets.customs();
});

// Sass
gulp.task('css', function() {
    return assets.css();
});

// Sass
gulp.task('sass', function() {
    return assets.sass();
});

// Images
gulp.task('images', function() {
    return assets.images();
});

// Fonts
gulp.task('fonts', function() {
    return assets.fonts();
});

// Js
gulp.task('js', function(callback) {
    assets.js(callback);
});

var
    assets = {
        customs: function() {
            var
                log         = require('gulp-util').log,
                _           = require('lodash'),
                eventStream = require('event-stream'),
                gulpIf      = require('gulp-if'),
                gulpRename  = require('gulp-rename'),
                gulpNotify  = require('gulp-notify'),
                streams     = [],
                message;

            _.forEach(options.customs, function(custom) {
                streams.push(
                    gulp.src(custom.src)
                        .pipe(gulpIf(custom.rename !== undefined, gulpRename(custom.rename)))
                        .pipe(gulp.dest(options.assetsTarget + '/' + (custom.dest ? custom.dest : '')))
                );
            });

            message = 'Customs complete';

            return eventStream.readArray(streams)
                .pipe(gulpIf(
                    options.isNotify,
                    gulpNotify({
                        message: message,
                        onLast: true
                    })
                ))
                .on('end', function(){log(message);});
        },
        css: function(source) {
            var
                log           = require('gulp-util').log,
                gulpMinifyCss = require('gulp-minify-css'),
                gulpCsslint   = require('gulp-csslint'),
                gulpIf        = require('gulp-if'),
                gulpNotify    = require('gulp-notify'),
                sources       = this.getSources(),
                globs         = [],
                glob, i, message;

            for (i in sources) {
                if (!source || (source && source === i)) {
                    glob = sources[i] + '/css/*.css';
                    globs.push(glob);
                }
            }

            message = 'Css ' + (source ? '(' + source + ') ' : '')  + 'complete';

            return gulp.src(globs)
                .pipe(gulpCsslint({
                    'overqualified-elements': false,
                    'universal-selector'    : false,
                    'adjoining-classes'     : false,
                    'star-property-hack'    : false,
                    'floats'                : false,
                    'qualified-headings'    : false
                }))
                .pipe(gulpCsslint.reporter())
                .pipe(gulpIf(!options.isDev, gulpMinifyCss({
                    keepSpecialComments: 0
                })))
                .pipe(gulp.dest(options.assetsTarget + '/css'))
                .pipe(gulpIf(
                    options.isNotify,
                    gulpNotify({
                        message: message,
                        onLast: true
                    })
                ))
                .on('end', function(){log(message);});
        },
        sass: function(source) {
            var
                log        = require('gulp-util').log,
                gulpSass   = options.isRubySass ? require('gulp-ruby-sass') : require('gulp-sass'),
                gulpIf     = require('gulp-if'),
                gulpNotify = require('gulp-notify'),
                sources    = this.getSources(),
                globs      = [],
                glob, i, message;

            for (i in sources) {
                if (!source || (source && source === i)) {
                    glob = sources[i] + '/sass/*.scss';
                    globs.push(glob);
                }
            }

            message = 'Sass ' + (source ? '(' + source + ') ' : '')  + 'complete';

            return gulp.src(globs)
                .pipe(gulpSass({
                    style: options.isDev ? 'expanded' : 'compressed'
                }))
                .pipe(gulp.dest(options.assetsTarget + '/css'))
                .pipe(gulpIf(
                    options.isNotify,
                    gulpNotify({
                        message: message,
                        onLast: true
                    })
                ))
                .on('end', function(){log(message);});
        },
        images: function(source) {
            var
                log          = require('gulp-util').log,
                gulpImagemin = require('gulp-imagemin'),
                gulpChanged  = require('gulp-changed'),
                gulpIf       = require('gulp-if'),
                gulpNotify   = require('gulp-notify'),
                sources      = this.getSources(),
                globs        = [],
                glob, i, message;

            for (i in sources) {
                if (!source || (source && source === i)) {
                    glob = sources[i] + '/images/**/*';
                    globs.push(glob);
                }
            }

            message = 'Images ' + (source ? '(' + source + ') ' : '')  + 'complete';

            return gulp.src(globs)
                .pipe(gulpChanged(options.assetsTarget + '/images'))
                .pipe(gulpImagemin({
                    optimizationLevel: 7,
                    progressive      : true,
                    pngquant         : true
                }))
                .pipe(gulp.dest(options.assetsTarget + '/images'))
                .pipe(gulpIf(
                    options.isNotify,
                    gulpNotify({
                        message: message,
                        onLast: true
                    })
                ))
                .on('end', function(){log(message);});
        },
        fonts: function(source) {
            var
                log         = require('gulp-util').log,
                gulpChanged = require('gulp-changed'),
                gulpIf      = require('gulp-if'),
                gulpNotify  = require('gulp-notify'),
                sources     = this.getSources(),
                globs       = [],
                glob, i, message;

            for (i in sources) {
                if (!source || (source && source === i)) {
                    glob = sources[i] + '/fonts/**/*';
                    globs.push(glob);
                }
            }

            message = 'Fonts ' + (source ? '(' + source + ') ' : '')  + 'complete';

            return gulp.src(globs)
                .pipe(gulpChanged(options.assetsTarget + '/fonts'))
                .pipe(gulp.dest(options.assetsTarget + '/fonts'))
                .pipe(gulpIf(
                    options.isNotify,
                    gulpNotify({
                        message: message,
                        onLast: true
                    })
                ))
                .on('end', function(){log(message);});
        },
        js: function(callback, source) {
            var
                log           = require('gulp-util').log,
                gulpJshint    = require('gulp-jshint'),
                jshintStylish = require('jshint-stylish'),
                requirejs     = require('requirejs'),
                notifier      = require('node-notifier'),
                fs            = require('fs'),
                sources       = this.getSources(),
                i, message;

            message = 'Requirejs ' + (source ? '(' + source + ') ' : '')  + 'complete';

            for (i in sources) {
                if (!source || (source && source === i)) {
                    gulp.src(sources[i] + '/js/**/*.js')
                        .pipe(gulpJshint({
                            'camelcase': true,
                            'curly'    : true,
                            'eqeqeq'   : true,
                            'es3'      : true,
                            'forin'    : true,
                            'freeze'   : true,
                            'immed'    : true,
                            'indent'   : 4,
                            'latedef'  : true,
                            'newcap'   : true,
                            'noarg'    : true,
                            'noempty'  : true,
                            'nonbsp'   : true,
                            'quotmark' : 'single',
                            'undef'    : true,
                            'unused'   : true,
                            'trailing' : true,
                            'predef'   : [
                                // Base
                                'window',
                                'document',
                                'setInterval',
                                // Console
                                'console',
                                // Requirejs
                                'define',
                                'require',
                                'requirejs'
                            ]
                        }))
                        .pipe(gulpJshint.reporter(jshintStylish))
                        .on('end', function(source) {
                            var build = sources[source] + '/js/build.js';
                            if (fs.existsSync(build)) {
                                requirejs.optimize(
                                    {
                                        mainConfigFile          : build,
                                        dir                     : options.assetsTarget + '/js',
                                        optimize                : options.isDev ? 'none' : 'uglify2',
                                        preserveLicenseComments : options.isDev ? true : false,
                                        removeCombined          : true,
                                        keepBuildDir            : true
                                    },
                                    function(buildResponse) {
                                        fs.unlinkSync(options.assetsTarget + '/js/build.js');
                                        fs.unlinkSync(options.assetsTarget + '/js/build.txt');
                                        log(message);
                                        if (options.isNotify) {
                                            notifier.notify({message: message});
                                        }
                                    },
                                    function(error) {
                                        log('Requirejs ' + (source ? '(' + source + ') ' : '')  + 'error');
                                        if (options.isNotify) {
                                            notifier.notify({message: 'Requirejs ' + (source ? '(' + source + ') ' : '')  + 'error'});
                                        }
                                    }
                                );
                            }
                        }.bind(this, i));
                }
            }
        },
        watch: function(callback) {
            var
                sources = this.getSources(),
                glob, i;

            for (i in sources) {

                // Css
                glob = sources[i] + '/css/**/*.css';
                gulp.watch(glob, function(source, event) {
                    return this.css(source);
                }.bind(this, i));

                // Sass
                glob = sources[i] + '/sass/**/*.scss';
                gulp.watch(glob, function(source, event) {
                    return this.sass(source);
                }.bind(this, i));

                // Images
                glob = sources[i] + '/images/**/*';
                gulp.watch(glob, function(source, event) {
                    return this.images(source);
                }.bind(this, i));

                // Fonts
                glob = sources[i] + '/fonts/**/*';
                gulp.watch(glob, function(source, event) {
                    return this.fonts(source);
                }.bind(this, i));

                // Js
                glob = sources[i] + '/js/**/*.js';
                gulp.watch(glob, function(callback, source, event) {
                    this.js(callback, source);
                }.bind(this, callback, i));
            }
        },
        getSources: function() {
            var
                fs     = require('fs'),
                glob   = require('glob'),
                log    = require('gulp-util').log,
                colors = require('gulp-util').colors,
                i, source, sourcePath, sources;

            if (this.sources === null) {

                this.sources = [];

                // App assets
                if (fs.existsSync('app/Resources/assets')) {
                    this.sources['app'] = 'app/Resources/assets';
                }

                // Src bundle assets
                sources = glob.sync('src/**/*Bundle/Resources/assets');
                for (i in sources) {
                    sourcePath = sources[i];
                    source = sourcePath
                        .replace('src/', '')
                        .replace('/Resources/assets', '')
                        .replace('/Bundle/', '')
                        .replace(/\//g, '');
                    this.sources[source] = sourcePath;
                }

                // Log
                for (source in this.sources) {
                    log('Found', "'" + colors.cyan(source) + "'", 'assets source in', colors.magenta(this.sources[source]));
                }
            }

            return this.sources;
        },
        sources: null
    };
