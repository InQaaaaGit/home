const path = require('path');
const VueLoaderPlugin = require('vue-loader/lib/plugin')

module.exports = {
    mode: 'development',
    //mode: 'production',
    entry: './src/index.js',
    devtool: 'inline-source-map',
    module: {
        rules: [
            {
                test: /\.vue$/,
                loader: 'vue-loader'
            },
            {
                test: /\.css$/,
                use: [
                    'vue-style-loader',
                    'style-loader',
                    'css-loader',
                ],
            },
            {
                test: /\.s(c|a)ss$/,
                use: [
                    'vue-style-loader',
                    'css-loader',
                    {
                        loader: 'sass-loader',
                        // Requires sass-loader@^7.0.0
                        options: {
                            implementation: require('sass'),
                            indentedSyntax: true // optional
                        },
                        // Requires sass-loader@^8.0.0
                        options: {
                            implementation: require('sass'),
                            sassOptions: {
                                indentedSyntax: true // optional
                            },
                        },
                    },
                ],
            },
        ]
    },
    plugins: [
        new VueLoaderPlugin()
    ],
    output: {
        path: path.resolve(__dirname, 'amd/build'),
        filename: 'files-program-lazy.min.js',
        publicPath: '/blocks/cdo_files_learning_plan/amd/build/',
        // libraryTarget: 'amd',
    }
}