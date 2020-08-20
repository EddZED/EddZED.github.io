
let project_folder="dist";
let sourse_folder="#src";

let path={
  build: {
    html: project_folder+"/",
    css: project_folder+ "/css/",
    js: project_folder+ "/js/",
    img: project_folder+ "/img/",
    font: project_folder+ "/font/",
  },
  src: {
    html: sourse_folder + "/",
    css: sourse_folder + "/scss/style.scss",
    js: sourse_folder + "/js/script.js",
    img: sourse_folder + "/img/**/*.{jpg,png,svg,gif,ico,webp}",
    font: sourse_folder + "/font/*.ttf",
  },
  watch: {
    html: sourse_folder + "/**/*.html",
    css: sourse_folder + "/scss/**/*.scss",
    js: sourse_folder + "/js/**/*.js",
    img: sourse_folder + "/img/**/*.{jpg,png,svg,gif,ico,webp}",
  },
  clean: "./" + project_folder + "/" 
}

let {src, dest} = require('gulp'),
gulp = require('gulp'),
