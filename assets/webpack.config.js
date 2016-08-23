/**
 * Created by oshry on 14/08/2016.
 */

var debug = process.env.NODE_ENV !== "production";
var webpack = require('webpack');
//NODE_ENV=production webpack
module.exports = {
    context: __dirname,
    devtool: debug ? "inline-sourcemap" : null,
    entry: "./js/src/app.js",
    output: {
        path: __dirname + "/js",
        filename: "bundle.min.js"
    },
    module: {
        loaders: [
            {
                test: /.jsx?$/,
                loader: 'babel-loader',
                exclude: /node_modules/,
                query: {
                    presets: ['es2015']
                }
            }
        ]
    },

    plugins: debug ? [] : [
        new webpack.optimize.DedupePlugin(),
        new webpack.optimize.OccurenceOrderPlugin(),
        new webpack.optimize.UglifyJsPlugin({ mangle: false, sourcemap: false }),
    ],
};