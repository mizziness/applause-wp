const paths = require('./paths')
const webpack = require('webpack')
const common = require('./webpack.common.js')
const { merge } = require('webpack-merge')
const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;
const { WebpackManifestPlugin } = require('webpack-manifest-plugin')

module.exports = merge(common, {
  mode: 'development',
  target: 'web',
  devtool: 'source-map',
  stats: 'normal',
  cache: true,
  infrastructureLogging: {
    colors: true,
    level: 'verbose',
  },
  output: {
    publicPath: "https://localhost:8080/",
  },
  watchOptions: {
    ignored: [ '**/stats.json', '**/build', '**/public', '**/font', '**/node_modules', '**/cache' ]
  },
  experiments: {
    lazyCompilation: {
      entries: true,
      imports: true,
    }
  },
  // Spin up a server for quick development
  devServer: {
    compress: true,
    server: 'spdy',
    historyApiFallback: false,
    watchFiles: [ paths.src ],
    hot: true,
    port: 8080,
    host: "localhost",
    headers: {
      "Access-Control-Allow-Origin": "*",
      "Access-Control-Allow-Headers": "*",
      "Access-Control-Allow-Methods": "*",
    },
    allowedHosts: 'all',
    static: [
      {
        directory: paths.public,
        serveIndex: false,
        watch: false,
      }
    ],
  },

  resolve: {
    extensions: [
      ".js"
    ]
  },

  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /node_modules/,
        use: [
          {loader: 'babel-loader?cacheDirectory'}
        ],
      }
    ]
  },

  plugins: [
    // Spits out the manifest json file so that Twigpack can get to it
    new WebpackManifestPlugin({
      writeToFileEmit: true
    }),

    // new BundleAnalyzerPlugin({
    //   analyzerMode: "disabled",
    //   generateStatsFile: true
    // }),
  ],

  // Creates our chunk-vendors file
  optimization: {
    splitChunks: {
      cacheGroups: {
        vendors: {
          name: 'chunk-vendors',
          test: /[\\/]node_modules[\\/]/,
          priority: -10,
          chunks: 'initial',
          reuseExistingChunk: true
        }
      }
    },
  }
})
