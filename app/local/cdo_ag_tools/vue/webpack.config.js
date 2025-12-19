const path = require('path');
const webpack = require('webpack');
const {VueLoaderPlugin} = require('vue-loader');

module.exports = (env, options) => {
    exports = {
        mode: 'development',
        entry: './index.js',
        output: {
            path: path.resolve(__dirname, '../amd/build'),
            publicPath: '/dist/',
            filename: 'app-lazy.min.js',
            chunkFilename: "[id].app-lazy.min.js?v=[hash]",
            libraryTarget: 'amd',
        },
        module: {
            rules: [
                {
                    test: /\.css$/,
                    use: [
                        'vue-style-loader',
                        'css-loader',
                        /*{
                            loader: 'postcss-loader',
                            options: {
                                postcssOptions: {
                                    plugins: [
                                        require('postcss-class-prefix')({ prefix: 'v-' })
                                    ]
                                }
                            }
                        }*//*{
                            loader: 'postcss-loader',
                            options: {
                                postcssOptions: {
                                    ident: 'postcss',
                                    sourceMap: true,
                                    plugins: (loader) => [
                                        require('autoprefixer')(),
                                        require('cssnano')(),
                                        require('postcss-parent-selector')({selector: '.vuetify_wrapper'})
                                    ]
                                }

                            }
                        }*/
                    ],
                },
                {
                    test: /\.vue$/,
                    loader: 'vue-loader',
                    options: {
                        loaders: {}
                        // Other vue-loader options go here
                    }
                },
                {
                    test: /\.js$/,
                    loader: 'babel-loader',
                    exclude: /node_modules/
                },
                {
                    test: /\.ts$/,
                    loader: "ts-loader",
                    options: { appendTsSuffixTo: [/\.vue$/] }
                }
            ]
        },
        resolve: {
            extensions: ['.tsx', '.ts', '.js', '.vue', '.*'],
            alias: {
                'vue': '@vue/runtime-dom'
            }
        },
        devServer: {
            historyApiFallback: true,
            noInfo: true,
            overlay: true,
            headers: {
                'Access-Control-Allow-Origin': '\*'
            },
            disableHostCheck: true,
            https: true,
            public: 'https://127.0.0.1:8080',
            hot: true,
        },
        performance: {
            hints: false
        },
        devtool: 'eval-source-map',
        plugins: [
            new VueLoaderPlugin(),
            new webpack.DefinePlugin({
                __VUE_OPTIONS_API__: JSON.stringify(true),
                __VUE_PROD_DEVTOOLS__: JSON.stringify(false),
                __VUE_PROD_HYDRATION_MISMATCH_DETAILS__: JSON.stringify(false)
            }),
        ],
        watchOptions: {
            ignored: /node_modules/
        },
        externals: {
            'core/ajax': {
                amd: 'core/ajax'
            },
            'core/str': {
                amd: 'core/str'
            },
            'core/modal_factory': {
                amd: 'core/modal_factory'
            },
            'core/modal_events': {
                amd: 'core/modal_events'
            },
            'core/fragment': {
                amd: 'core/fragment'
            },
            'core/yui': {
                amd: 'core/yui'
            },
            'core/localstorage': {
                amd: 'core/localstorage'
            },
            'core/notification': {
                amd: 'core/notification'
            },
            'jquery': {
                amd: 'jquery'
            }
        }
    };

    if (options.mode === 'production') {
        exports.devtool = false;
        // http://vue   -loader.vuejs.org/en/workflow/production.html
        exports.plugins = (exports.plugins || []).concat([
            new webpack.DefinePlugin({
                'process.env': {
                    NODE_ENV: '"production"'
                },
                __VUE_OPTIONS_API__: JSON.stringify(true),
                __VUE_PROD_DEVTOOLS__: JSON.stringify(false),
                __VUE_PROD_HYDRATION_MISMATCH_DETAILS__: JSON.stringify(false)
            }),
            new webpack.LoaderOptionsPlugin({
                minimize: true
            })
        ]);
        exports.optimization = {
            minimizer: [
                (compiler) => {
                    const TerserPlugin = require('terser-webpack-plugin');
                    new TerserPlugin({
                        terserOptions: {
                            compress: {},
                        }
                    }).apply(compiler);
                },
            ]
        }
    }

    return exports;
};
