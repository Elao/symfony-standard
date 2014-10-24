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

gulp.task('default', ['install', 'watch']);
gulp.task('install', ['js', 'sass', 'less', 'images', 'files']);
gulp.task('watch',   ['watch:js', 'watch:sass', 'watch:less', 'watch:images']);
gulp.task('clean',   function(cb) {
    del(assets.getDest() + '/*', cb);
});
