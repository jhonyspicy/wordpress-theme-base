gulp = require("gulp")
plugins = require("gulp-load-plugins")()
pngcrush = require('imagemin-pngcrush')
stylus = require("stylus")
nib = require('nib')


gulp.task "default", ["watch", "stylus", "coffee", "image"]


gulp.task "watch", ->
	gulp.watch(["./images/**/*.{png,jpg,jpeg,gif,svg}", "!./images/**/*.min.{png,jpg,jpeg,gif,svg}", "!./node_modules/**"], ["image"])
	gulp.watch(["./**/*.styl?(us)", "!./**/_*.styl?(us)", "!./node_modules/**"], ["stylus"])
	gulp.watch(["./**/*.coffee", "!./node_modules/**", "!./gulpfile.coffee"], ["coffee"])
	gulp.watch(["./gulpfile.coffee"], ["gulpfile"])


gulp.task "image", ->
	gulp.src(["./images/**/*.{png,jpg,jpeg,gif,svg}", "!./images/**/*.min.{png,jpg,jpeg,gif,svg}", "!./node_modules/**"])
		.pipe plugins.plumber(
			errorHandler: plugins.notify.onError(
				title: "task: image"
				message: "Error: <%= error.message %>"
			)
		)
		.pipe plugins.imagemin(
			progressive: true
			svgoPlugins: [{removeViewBox: false}]
			use: [pngcrush()]
		)
		.pipe plugins.rename({suffix: ".min"})
		.pipe gulp.dest("./images/")


gulp.task "stylus", ->
	gulp.src(["./**/*.styl?(us)", "!./**/_*.styl?(us)", "!./node_modules/**"])
		.pipe plugins.plumber(
			errorHandler: plugins.notify.onError(
				title: "task: stylus"
				message: "Error: <%= error.message %>"
			)
		)
		.pipe plugins.stylus(
			define: {'url': stylus.resolver()}
			"resolve url": true
			use: [nib()]
			import: "nib"
		)
		.pipe plugins.autoprefixer("last 2 versions", "ie 8")
		.pipe gulp.dest("./")
		.pipe plugins.minifyCss()
		.pipe plugins.rename({extname: ".min.css"})
		.pipe gulp.dest("./")


gulp.task "coffee", ->
	gulp.src(["./**/*.coffee", "!./node_modules/**", "!./gulpfile.coffee"])
		.pipe plugins.plumber(
			errorHandler: plugins.notify.onError(
				title: "task: coffee"
				message: "Error: <%= error.message %>"
			)
		)
		.pipe plugins.newer({dest:"./", ext:".min.js"})
		.pipe plugins.coffeelint()
		.pipe plugins.coffee({bare:true})
		.pipe gulp.dest("./")
		.pipe plugins.uglify()
		.pipe plugins.rename({extname: ".min.js"})
		.pipe gulp.dest("./")
