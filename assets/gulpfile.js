'use strict';
const gulp = require('gulp');
const $    = require('gulp-load-plugins')();
const sourcemaps = require('gulp-sourcemaps');
const babel = require('gulp-babel');
const concat = require('gulp-concat');
const webpack = require('webpack-stream');
const minify = require('gulp-minify');

gulp.task('build-me', ()=> {
    return gulp.src('./js/src/app.js')
        .pipe(webpack( require('./webpack.config.js') ))
        .pipe(babel({
            presets: ['es2015']
        }))
        .pipe(minify())
        .pipe(gulp.dest('./js/dist'));
});
gulp.task('sass-me', ()=> {
    gulp.src('./scss/**/*.scss')
        .pipe($.sass({
            outputStyle: 'compressed' // if css compressed **file size**
        }).on('error', $.sass.logError))
        .pipe($.sass().on('error', $.sass.logError))
        .pipe(gulp.dest('./css'))
});

gulp.task('default', ()=> {
    gulp.watch(['scss/**/*.scss'], ['sass-me']);
    gulp.watch(['js/src/**/*.js'], ['build-me']);
});
