const paths = require('./paths')
const webpack = require('webpack')
const { WebpackManifestPlugin } = require('webpack-manifest-plugin')

module.exports = {
  entry: {
    'app':  [ paths.src + '/app.js' ],
    'blog': [ paths.src + '/entry/blog.js']
  },
  output: {
    path:           paths.build,
    publicPath:     "/",
    filename:       'js/[name].bundle.js',
    chunkFilename:  'js/[name].bundle.js',
    clean:           false,
  },
  stats: {
    modules: true,
    reasons: true,
    errorDetails: true
  },
  resolve: {
    fallback: { "fs": false },
    modules: [ "node_modules" ]
  },
  plugins: [
    // new WebpackBar({ fancy: true, profile: true }),
    new WebpackManifestPlugin(),
  ],
  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /node_modules/,
        use: ['babel-loader']
      },
      {
        test: /\.svg(\?.*)?$/,
        type: 'asset/resource'
      },
      {
        test: /\.(scss|css)$/,
        use: [
          'style-loader',
          {
            loader: 'css-loader',
            options: {
              importLoaders: 2,
            }
          },
          { loader: "postcss-loader" },
          {
            loader: 'sass-loader',
            options: {
              sourceMap: true,
              implementation: require("sass"),
            }
          },
        ],
      },
      {
        test: /\.(?:ico|gif|png|jpg|jpeg)$/i,
        type: 'asset/inline',
        loader: 'file-loader',
        options: {
          outputPath: 'images',
        },
      },
      {
        test: /\.(woff(2)?|eot|ttf|otf)$/,
        type: 'asset/inline'
      },
    ],
  },
}
