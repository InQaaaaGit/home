const path = require('path');
const webpack = require('webpack');
const {VueLoaderPlugin} = require('vue-loader');

module.exports = (env, options) => {
    exports = {
        mode: options.mode || 'development',
        //mode: 'production',
        entry: './index.js',
        output: {
            path: path.resolve(__dirname, '../amd/build'),
            publicPath: '/dist/',
            filename: 'full-schedule-for-all-app-lazy.min.js',
            chunkFilename: "[id].full-schedule-for-all-app-lazy.min.js?v=[hash]",
            libraryTarget: 'amd',
        },
        module: {
            rules: [
                {
                    test: /\.css$/,
                    use: [
                        'vue-style-loader',
                        {
                            loader: 'css-loader',
                            options: {
                                importLoaders: 1,
                                modules: false
                            }
                        }
                    ],
                },
                {
                    test: /\.vue$/,
                    loader: 'vue-loader',
                    options: {
                        loaders: {
                            css: [
                                'vue-style-loader',
                                {
                                    loader: 'css-loader',
                                    options: {
                                        importLoaders: 1,
                                        modules: false
                                    }
                                }
                            ]
                        }
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
        devtool: options.mode === 'production' ? false : 'eval-source-map',
        plugins: [
            new VueLoaderPlugin()
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
            'core/localstorage': {
                amd: 'core/localstorage'
            },
            'core/notification': {
                amd: 'core/notification'
            },
        }
    };

    if (options.mode === 'production') {
        exports.devtool = false;
        exports.plugins = (exports.plugins || []).concat([
            new webpack.DefinePlugin({
                'process.env': {
                    NODE_ENV: '"production"'
                },
                __VUE_OPTIONS_API__: JSON.stringify(true),
                __VUE_PROD_DEVTOOLS__: JSON.stringify(false),
                __VUE_PROD_HYDRATION_MISMATCH_DETAILS__: JSON.stringify(false),
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
