var path = require('path');
var webpack = require('webpack');
const { VueLoaderPlugin } = require('vue-loader');
const TerserPlugin = require('terser-webpack-plugin');

const isDevServer = process.argv.find(v => v.includes('webpack-dev-server'));

module.exports = (env, options) => {

    const isProduction = options.mode === 'production';

    exports = {
        entry: {
            app: './main.js',
        },
        output: {
            path: path.resolve(__dirname, '../amd/build'),
            filename: isProduction ? 'prod-app-lazy.min.js' : 'dev-app-lazy.min.js',
            libraryTarget: 'amd',
            publicPath: '',
        },
        module: {
            rules: [
                {
                    test: /\.css$/,
                    use: [
                        'vue-style-loader',
                        'css-loader'
                    ],
                },
                {
                    test: /\.vue$/,
                    loader: 'vue-loader',
                    options: {
                        loaders: {}
                    }
                },
                {
                    test: /\.js$/,
                    loader: 'babel-loader',
                    exclude: /node_modules/
                }
            ]
        },
        resolve: {
            alias: {
                'vue$': 'vue/dist/vue.esm-bundler.js'
            },
            extensions: ['.js', '.vue', '.json'],
            fallback: {
                "path": false,
                "fs": false
            }
        },
        devServer: {
            historyApiFallback: true,
            noInfo: true,
            overlay: true,
            headers: {
                'Access-Control-Allow-Origin': '*'
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
        exports.plugins = (exports.plugins || []).concat([
            new webpack.DefinePlugin({
                'process.env': {
                    NODE_ENV: '"production"'
                }
            }),
            new webpack.LoaderOptionsPlugin({
                minimize: true
            })
        ]);
        exports.optimization = {
            minimizer: [
                new TerserPlugin({
                    parallel: true,
                    terserOptions: {
                        // https://github.com/webpack-contrib/terser-webpack-plugin#terseroptions
                    }
                }),
            ]
        }
    }

    return exports;
};
