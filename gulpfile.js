const fs			= require( 'fs' );
const gulp			= require( 'gulp' );
const glob			= require( 'glob' );
const autoprefixer	= require( 'gulp-autoprefixer' );
const browserify	= require( 'browserify' );
const babelify		= require( 'babelify' );
const buffer		= require( 'vinyl-buffer' );
const sourcemaps	= require( 'gulp-sourcemaps' );
const sass			= require( 'gulp-sass' );
const source		= require( 'vinyl-source-stream' );
const uglify		= require( 'gulp-uglify' );
const es			= require( 'event-stream' );

const package = require( './package.json' );

const config = {
	sass : {
		outputStyle: 'compressed',
		precision: 8,
		stopOnError: false,
		functions: {
			'base64Encode($string)': $string => {
				var buffer = new Buffer( $string.getValue() );
				return sass.types.String( buffer.toString('base64') );
			}
		},
		includePaths:['src/scss/']
	}
}

gulp.task('build:js',cb => {
	let tasks = glob.sync("./src/js/**/index.js")
		.map( entry => {
			let target = entry.replace(/(\.\/src\/js\/|\/index)/g,'');
			return browserify({
			        entries: [entry],
					debug: false,
					paths:['./src/js/lib']
			    })
				.transform( babelify.configure({}) )
				.transform( 'browserify-shim' )
				.bundle()
				.pipe(source(target))
				.pipe(buffer())
			    .pipe(uglify())
				.pipe(gulp.dest("./js"));
		} );

	return es.merge(tasks).on('end',cb)

});

gulp.task('build:scss', cb => {
	return gulp.src( './src/scss/**/*.scss' )
		.pipe(
			sass( config.sass )
		)
		.pipe( autoprefixer( { browsers: package.browserlist } ) )
		.pipe( gulp.dest('./css'));
});


gulp.task('dev:js', cb => {
	let tasks = glob.sync("./src/js/**/index.js")
		.map( entry => {
			let target = entry.replace(/(\.\/src\/js\/|\/index)/g,'');
			return browserify({
			        entries: [entry],
					debug: true,
					paths:['./src/js/lib']
			    })
				.transform( babelify.configure({}) )
				.transform( 'browserify-shim' )
				.bundle()
				.pipe(source(target))
				.pipe(buffer())
			    .pipe(sourcemaps.init({loadMaps:true}))
			    .pipe(uglify())
			    .pipe(sourcemaps.write())
				.pipe(gulp.dest("./js"));
		} );

	return es.merge(tasks).on('end',cb)
});

gulp.task('dev:scss', cb => {
	return gulp.src( './src/scss/**/*.scss' )
		.pipe( sourcemaps.init() )
		.pipe(
			sass( config.sass )
		)
		.pipe( autoprefixer( { browsers: package.browserlist } ) )
		.pipe( sourcemaps.write( ) )
		.pipe( gulp.dest('./css'));
});


gulp.task('watch', cb => {
	gulp.watch('./src/scss/**/*.scss',gulp.parallel('dev:scss'));
	gulp.watch('./src/js/**/*.js',gulp.parallel('dev:js'));
});

gulp.task('dev',gulp.series('dev:scss','dev:js','watch'));

gulp.task('build', gulp.parallel('build:js','build:scss'));

gulp.task('default',cb => {
	console.log('run either `gulp build` or `gulp dev`');
	cb();
});

module.exports = {
	build:gulp.series('build')
}
