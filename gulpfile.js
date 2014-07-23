var gulp, plugins, pngcrush;

gulp = require("gulp");

plugins = require("gulp-load-plugins")();

pngcrush = require('imagemin-pngcrush');

gulp.task("default", ["watch", "stylus", "coffee", "image"]);

gulp.task("watch", function() {
  gulp.watch(["./images/**/*"], ["image"]);
  gulp.watch(["./**/*.styl?(us)", "!./**/_*.styl?(us)", "!./node_modules/**"], ["stylus"]);
  gulp.watch(["./**/*.coffee", "!./node_modules/**", "!./gulpfile.coffee"], ["coffee"]);
  return gulp.watch(["./gulpfile.coffee"], ["gulpfile"]);
});

gulp.task("image", function() {
  return gulp.src(["./images/**/*"]).pipe(plugins.plumber({
    errorHandler: plugins.notify.onError({
      title: "task: image",
      message: "Error: <%= error.message %>"
    })
  })).pipe(plugins.imagemin({
    progressive: true,
    svgoPlugins: [
      {
        removeViewBox: false
      }
    ],
    use: [pngcrush()]
  })).pipe(gulp.dest("./images/"));
});

gulp.task("stylus", function() {
  return gulp.src(["./**/*.styl?(us)", "!./**/_*.styl?(us)", "!./node_modules/**"]).pipe(plugins.plumber({
    errorHandler: plugins.notify.onError({
      title: "task: stylus",
      message: "Error: <%= error.message %>"
    })
  })).pipe(plugins.stylus({})).pipe(plugins.autoprefixer("last 2 versions", "ie 8")).pipe(gulp.dest("./")).pipe(plugins.minifyCss()).pipe(plugins.rename({
    extname: ".min.css"
  })).pipe(gulp.dest("./"));
});

gulp.task("coffee", function() {
  return gulp.src(["./**/*.coffee", "!./node_modules/**", "!./gulpfile.coffee"]).pipe(plugins.plumber({
    errorHandler: plugins.notify.onError({
      title: "task: coffee",
      message: "Error: <%= error.message %>"
    })
  })).pipe(plugins.newer({
    dest: "./",
    ext: ".min.js"
  })).pipe(plugins.coffeelint()).pipe(plugins.coffee({
    bare: true
  })).pipe(gulp.dest("./")).pipe(plugins.uglify()).pipe(plugins.rename({
    extname: ".min.js"
  })).pipe(gulp.dest("./"));
});

gulp.task("gulpfile", function() {
  return gulp.src("./gulpfile.coffee").pipe(plugins.plumber({
    errorHandler: plugins.notify.onError({
      title: "task: gulpfile.coffee",
      message: "Error: <%= error.message %>"
    })
  })).pipe(plugins.newer({
    dest: "./",
    ext: ".js"
  })).pipe(plugins.coffeelint()).pipe(plugins.coffee({
    bare: false
  })).pipe(gulp.dest("./"));
});
