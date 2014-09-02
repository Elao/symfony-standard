var
    gulp   = require('gulp'),
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
    ].join('\n')
});

/*********/
/* Tasks */
/*********/

gulp.task('install', ['js', 'sass', 'images', 'fonts']);
gulp.task('watch',   ['watch:js', 'watch:sass', 'watch:images']);
gulp.task('lint',    ['lint:js', 'lint:sass']);
gulp.task('clean',   ['clean:js', 'clean:sass', 'clean:images', 'clean:fonts']);
gulp.task('default', ['install', 'watch']);
