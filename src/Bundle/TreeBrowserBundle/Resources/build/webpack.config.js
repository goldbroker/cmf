var webpack = require('webpack');
var path = require('path');
var ExtractTextPlugin = require("extract-text-webpack-plugin");

module.exports = {
    context : path.join(__dirname, '../assets/'),
    entry : {
        fancytree : './js/jquery.cmf_tree.js'
    },
    output : {
        path : path.resolve(__dirname, '../public/js'),
        filename : 'cmf_tree_browser.[name].js'
    },
    externals : {
        'jquery' : 'jQuery'
    },
    module : {
        rules: [
            {
                test: /\.js$/,
                exclude: /node_modules/,
                loader: 'babel-loader',
            },
            {
                test: /\.css$/,
                use: ExtractTextPlugin.extract(
                    {
                        fallback: 'style-loader',
                        use: ['css-loader']
                    })
            },
            // {
            //     test : /(assets|Tests).+?\.js$/i,
            //     use: {
            //         loader : 'babel-loader', query : {presets : ['es2015']}
            //     }
            // },
            {
                test : /\.(jpe?g|png|gif|svg)$/i,
                use:  'file-loader?name=../img/[hash].[ext]&context=default'
            }
        ]
    },
    resolve : {
        extensions: ['.js', '.jsx', '.css'],
        modules: ['node_modules'],
        alias : {
            // jquery : path.join(__dirname, 'node_modules/jquery/dist/jquery.js'),
            // 'jquery-ui' : path.join(__dirname, 'node_modules/jquery-ui/dist/jquery-ui.js'),
            'jquery.fancytree': path.join(__dirname, 'node_modules/jquery.fancytree/dist'),
            'core-js' : path.join(__dirname, 'node_modules/core-js'),
        },
    },
    resolveLoader: {
        // Configure how Webpack finds `loader` modules.
        modules: [
            path.join(__dirname, 'node_modules'),
        ],
    },
    plugins : [
        new ExtractTextPlugin({filename: '../css/cmf_tree_browser.[name].css'})
    ]
};
