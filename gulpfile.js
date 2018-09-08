var gulp = require('gulp');
var gulputil = require('gulp-util');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var sass = require('gulp-sass');
var sourcemaps = require('gulp-sourcemaps');
var rename = require('gulp-rename');


var styles = [
	'./src/scss/acf-quickedit.scss',
	'./src/scss/acf-qef-field-group.scss'
];
var scripts = [
	'./src/js/acf-quickedit-base.js',
	'./src/js/acf-quickedit-fields.js',
	'./src/js/acf-quickedit.js',
	'./src/js/thumbnail-col.js'
];
var scripts_legacy_56 = [
	'./src/js/legacy/5.6/acf-quickedit-base.js',
	'./src/js/acf-quickedit-fields.js',
	'./src/js/legacy/5.6/acf-quickedit.js',
	'./src/js/thumbnail-col.js'
];

gulp.task('styles-build',function(){
    return gulp.src( styles )
		.pipe(sourcemaps.init())
        .pipe( sass( {
        	outputStyle: 'compressed'
        } ).on('error', sass.logError) )
        .pipe( sourcemaps.write() )
        .pipe( gulp.dest( './css/' ) );
});

gulp.task('scripts-build-field', function() {
	return gulp.src( scripts )
		.pipe(sourcemaps.init())
		.pipe( uglify().on('error', gulputil.log ) )
		.pipe( concat('acf-quickedit.min.js') )
		.pipe( sourcemaps.write() )
		.pipe( gulp.dest( './js/' ) );
});
gulp.task('scripts-build-fieldgroup', function() {
	return gulp.src( './src/js/acf-qef-field-group.js' )
		.pipe(sourcemaps.init())
		.pipe( uglify().on('error', gulputil.log ) )
		.pipe( concat('acf-qef-field-group.min.js') )
		.pipe( sourcemaps.write() )
		.pipe( gulp.dest( './js/' ) );
});
gulp.task('scripts-build-legacy', function() {
	return gulp.src( scripts_legacy_56 )
		.pipe(sourcemaps.init())
		.pipe( uglify().on('error', gulputil.log ) )
		.pipe( concat('acf-quickedit.min.js') )
		.pipe( sourcemaps.write() )
		.pipe( gulp.dest( './js/legacy/5.6/' ) )
});

gulp.task('scripts-build', gulp.parallel( 'scripts-build-field', 'scripts-build-fieldgroup', 'scripts-build-legacy' ) );


gulp.task( 'watch', function() {
	gulp.watch('./src/scss/**/*.scss', gulp.parallel('styles-build') );
	gulp.watch('./src/js/**/*.js', gulp.parallel('scripts-build') );
} );

gulp.task( 'build', gulp.parallel('styles-build','scripts-build') );

gulp.task( 'default', gulp.series('build','watch') );
