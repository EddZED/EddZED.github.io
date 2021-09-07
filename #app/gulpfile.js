const preprocessor = "sass";
const fileswatch = "html,woff2";

const { src, dest, parallel, series, watch } = require("gulp"),
  gulp = require("gulp"),
  browserSync = require("browser-sync").create(),
  bssi = require("browsersync-ssi"),
  ssi = require("ssi"),
  webpack = require("webpack-stream"),
  sass = require("gulp-sass"),
  sassglob = require("gulp-sass-glob"),
  cleancss = require("gulp-clean-css"),
  autoprefixer = require("gulp-autoprefixer"),
  rename = require("gulp-rename"),
  imagemin = require("gulp-imagemin"),
  newer = require("gulp-newer"),
  rsync = require("gulp-rsync"),
  del = require("del"),
  webp = require("gulp-webp"),
  webpcss = require("gulp-webp-css"),
  ttf2woff = require("gulp-ttf2woff"),
  ttf2woff2 = require("gulp-ttf2woff2"),
  fonter = require("gulp-fonter"),
  replace = require('gulp-replace');

function browsersync() {
  browserSync.init({
    server: {
      baseDir: "./",
      middleware: bssi({
          baseDir: "./",
          ext: ".html"
      }),
    },
    ghostMode: {
        clicks: false
    },
    notify: false,
    online: true,
    // tunnel: 'yousutename', // Attempt to use the URL https://yousutename.loca.lt
  });
}

function scripts() {
  return src(["./app/js/*.js", "!./app/js/*.min.js"])
    .pipe(
      webpack({
        mode: "production",
        performance: {
            hints: false
        },
        module: {
          rules: [
            {
              test: /\.(js)$/,
              exclude: /(node_modules)/,
              loader: "babel-loader",
              query: {
                presets: ["@babel/env"],
                plugins: ["babel-plugin-root-import"],
              },
            },
          ],
        },
      })
    )
    .on("error", function handleError() {
      this.emit("end");
    })
    .pipe(rename("main.min.js"))
    .pipe(dest("./dist/js"))
    .pipe(browserSync.stream());
}

function styles() {

    return gulp.src('./app/sass/**/*.scss')
        .pipe(sass.sync().on('error', sass.logError))
        .pipe(replace('../../', '../'))
        .pipe(
            autoprefixer({
                overrideBrowserslist: ["last 10 versions"],
                grid: true
            })
        )
        .pipe(rename({ suffix: ".min" }))
        .pipe(dest("./dist/css"))
        .pipe(browserSync.stream());
}

function images() {
  return src(["./app/img/**/*"])
    .pipe(
      webp({
        quality: 70,
      })
    )
    .pipe(
      imagemin({
        interlaced: true,
        progressive: true,
        optimizationLevel: 3,
        svgoPlugins: [
          {
            removeViewBox: true,
          },
        ],
      })
    )
    .pipe(dest("./dist/img"))
    .pipe(browserSync.stream());
}

gulp.task("ttf2woff2", function () {
  src("./app/fonts/*.ttf").pipe(ttf2woff()).pipe(dest("./dist/fonts"));
  return src("./app/fonts/*.ttf").pipe(ttf2woff2()).pipe(dest("./dist/fonts"));
});

gulp.task("otf2ttf", function () {
  return src(["./app/fonts/*.otf"])
    .pipe(
      fonter({
        formats: ["ttf"],
      })
    )
    .pipe(dest("./app/fonts/"));
});

function cleandist() {
  return del("./dist/**/*", { force: true });
}

function deploy() {
  return src("dist/").pipe(
    rsync({
      root: "dist/",
      hostname: "username@yousite.com",
      destination: "yousite/public_html/",
      // clean: true, // Mirror copy with file deletion
      include: [
        /* '*.htaccess' */
      ], // Included files to deploy,
      exclude: ["**/Thumbs.db", "**/*.DS_Store"],
      recursive: true,
      archive: true,
      silent: false,
      compress: true,
    })
  );
}

function startwatch() {
  watch(`./app/sass/**/*`, { usePolling: true }, styles);
  watch(
    ["app/js/**/*.js", "!app/js/**/*.min.js"],
    { usePolling: true },
    scripts
  );
  /*watch(
    "./app/img/!**!/!*.{jpg,jpeg,png,webp,svg,gif}",
    { usePolling: true },
    images
  );*/
  /*watch(`./!**!/!*.{${fileswatch}}`, {
      usePolling: true
  }).on(
    "change",
    browserSync.reload
  );*/
}

exports.scripts = scripts;
exports.styles = styles;
exports.images = images;
exports.deploy = deploy;
exports.assets = series(scripts, styles, images);
exports.build = series(
  cleandist,
  scripts,
  styles,
  images,
  // buildcopy,
  // buildhtml
);
exports.default = series(
  scripts,
  styles,
  images,
  parallel(browsersync, startwatch)
);
