const path = require('path');
const VueLoaderPlugin = require('vue-loader/lib/plugin');
//const { CleanWebpackPlugin } = require('clean-webpack-plugin');


module.exports = env => {
  mode = ('NODE_ENV' in env && env.NODE_ENV == 'production') ? 'production' : 'development'
  return {
    mode,
    devtool:  'inline-source-map',
    resolve: {
      extensions: ['.js', '.vue'],
      alias: {
        '@': path.resolve(__dirname, 'src'),
      }
    },
    entry: {
      index: './src/index.js',
    },
    module: {
      rules: [
        {
          test: /\.vue$/,
          use: [
            'vue-loader',
          ],
        },

        {
          test: /\.css$/,
          use: [
            'style-loader',
            'vue-style-loader',
            'css-loader',
          ],
        },

      ],
    },
    plugins: [
      new VueLoaderPlugin(),
      //     new CleanWebpackPlugin(),
    ],
    externals: {
      'core/ajax': {
        amd: 'core/ajax',
      },
      'core/localstorage': {
        amd: 'core/localstorage',
      },
      'core/notification': {
        amd: 'core/notification',
      },
      'core/str': {
        amd: 'core/str',
      },
      'core/user_date': {
        amd: 'core/user_date',
      },
    },
    output: {
      filename: '[name]-app-lazy.min.js',
      chunkFilename: '[id].app-lazy.min.js?v=[hash]',
      path: path.resolve(__dirname, 'amd/build'),
      //publicPath: '/blocks/vuetify/amd/build/',
      publicPath: '/local/cdo_vkr/amd/build/',
      libraryTarget: 'amd',
    },
  };
};
