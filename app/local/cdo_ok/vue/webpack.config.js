var path = require('path');
var webpack = require('webpack');
const { VueLoaderPlugin } = require('vue-loader');
const TerserPlugin = require('terser-webpack-plugin');

const isDevServer = process.argv.find(v => v.includes('webpack-dev-server'));

module.exports = (env, options) => {

    const isProduction = options.mode === 'production';

    exports = {
        entry: {
            app: './index.js',
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
                'vue$': 'vue/dist/vue.esm-bundler.js',
                '@': path.resolve(__dirname, './')
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
            new VueLoaderPlugin(),
            // Vue 3 feature flags
            new webpack.DefinePlugin({
                __VUE_OPTIONS_API__: JSON.stringify(true),
                __VUE_PROD_DEVTOOLS__: JSON.stringify(false),
                __VUE_PROD_HYDRATION_MISMATCH_DETAILS__: JSON.stringify(false)
            })
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

    // КРИТИЧНО: отключаем разделение на чанки для одного файла
    exports.optimization = {
        splitChunks: false,      // Не создавать vendor чанки
        runtimeChunk: false,     // Не создавать runtime чанк
        minimize: isProduction   // Минификация только в production
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
        exports.optimization.minimizer = [
            new TerserPlugin({
                parallel: true,
                terserOptions: {
                    // https://github.com/webpack-contrib/terser-webpack-plugin#terseroptions
                }
            }),
        ];
    }

    return exports;
};
