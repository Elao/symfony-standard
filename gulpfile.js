var
    gulp   = require('gulp'),
    del    = require('del'),
    assets = require('elao-assets-gulp');

/************************/
/* Assets Configuration */
/************************/

assets.config({
    header: [
        '/*',
        ' * =============================================================',
        ' * <%= name %>',
        ' *',
        ' * (c) <%= date.getFullYear() %> <%= author.name %> <<%= author.email %>>',
        ' * =============================================================',
        ' */\n\n'
    ].join('\n'),
    autoprefixer: {
        browsers: ['> 1%', 'last 2 versions', 'Firefox ESR', 'Opera 12.1']
    }
});

/*********/
/* Tasks */
/*********/

gulp.task('default', ['js', 'sass', 'css', 'images', 'fonts', 'swf', 'files']);
gulp.task('watch',   ['watch:js', 'watch:sass', 'watch:css', 'watch:images', 'watch:files']);
gulp.task('clean',   function(cb) {
    del(assets.getDest() + '/*', cb);
});
