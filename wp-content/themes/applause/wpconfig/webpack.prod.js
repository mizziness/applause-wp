const paths = require('./paths')
const path = require('path')
const MiniCssExtractPlugin = require('mini-css-extract-plugin')
const TerserPlugin = require("terser-webpack-plugin")
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin')
const { WebpackManifestPlugin } = require('webpack-manifest-plugin')
const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;
// const WebpackBar = require('webpackbar')

var SHARED_SEED = {};

var entries = {
  'app': [ paths.src + '/app.js' ],
  'blog': [ paths.src + '/entry/blog.js'],
  // sdq2022: [ paths.src + '/entry/sdq2022.js'],
  // sdq2023: [ paths.src + '/entry/sdq2023.js'],
};

let legacyBundle = {
  entry: entries,
  mode: 'production',
  devtool: false,
  name: "legacy-bundle",
  target: 'web',
  output: {
    library: 'applause',
    path: paths.build,
    publicPath: "/wp-content/themes/applause/build/",
    chunkLoading: 'jsonp',
    clean: false,
    environment: { module: false },
    filename: 'js/[name].es5.js',
    chunkFilename: 'js/[name].es5.js',
    assetModuleFilename: 'images/[name][ext]'
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /(node_modules)/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: [
              ["@babel/preset-env", {
                  targets: { browsers: ["safari >= 7"] },
                  bugfixes: true,
                  modules: false,
                  useBuiltIns: 'usage',
                  corejs: 3,
                },
              ],
            ],
            plugins: [
              ["@babel/plugin-transform-class-properties"],
              ["@babel/plugin-transform-runtime", { "corejs": 3 }],
              ["@babel/plugin-syntax-dynamic-import"]
            ],
          },
        }
      },
      {
        test: /\.svg(\?.*)?$/,
        type: 'asset/resource'
      },
      {
        test: /\.(scss|css)$/,
        use: [
          {
            loader: MiniCssExtractPlugin.loader,
            options: {
              emit: true
            }
          },
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
        // type: 'asset/inline',
        loader: 'url-loader',
        options: {
          limit: 100,
          name: 'fonts/[name].[ext]'
        }
      },
    ],
  },
  plugins: [
    new MiniCssExtractPlugin({
      filename: "css/[name].css",
    }),
    new WebpackManifestPlugin({
      fileName: 'manifest-legacy.json',
      seed: SHARED_SEED,
      filter: ({name, path}) => !path.match(/images/gi) || !name.match(/images/gi),
    }),
    // new WebpackBar()
    // new BundleAnalyzerPlugin({
    //   analyzerMode: "disabled",
    //   generateStatsFile: true
    // }),
  ],
  optimization: {
    removeEmptyChunks: true,
    providedExports: true,
    splitChunks: {
      chunks: 'initial',
      hidePathInfo: true,
      cacheGroups: {
        blog: {
          name: 'chunk-blog',
          reuseExistingChunk: true,
          test: /[\\/]node_modules[\\/](pace-js|sharer.js)[\\/]/,
          priority: 0,
          filename: 'js/[name].es5.js'
        },
        vendors: {
          name: 'chunk-vendors',
          reuseExistingChunk: true,
          test: /[\\/]node_modules[\\/]/,
          priority: -10,
          filename: 'js/[name].es5.js'
        },
      }
    },
    usedExports: true,
    minimize: true,
    minimizer: [
      new CssMinimizerPlugin({
        minimizerOptions: {
          preset: [
            "default",
            {
              discardComments: { removeAll: true },
            },
          ],
        },
      }),
      new TerserPlugin({
        terserOptions: {
          format: {
            comments: false,
          },
        },
        extractComments: false,
      }),
    ],
  },
  performance: {
    hints: 'warning',
    maxEntrypointSize: 512000,
    maxAssetSize: 512000,
  },
};

let modernBundle = {
  entry: entries,
  mode: 'production',
  devtool: false,
  name: "modern-bundle",
  target: 'web',
  output: {
    library: 'applause',
    path: paths.build,
    publicPath: "/wp-content/themes/applause/build/",
    chunkLoading: 'jsonp',
    clean: false,
    environment: { module: false },
    filename: 'js/[name].js',
    chunkFilename: 'js/[name].js',
    assetModuleFilename: 'images/[name][ext]'
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /(node_modules)/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: [
              ["@babel/preset-env", {
                targets: { esmodules: true }, //For ES6 supporting browsers
                useBuiltIns: false
              }],
            ],
            plugins: [
              ["@babel/plugin-transform-class-properties"],
              ["@babel/plugin-transform-runtime", { "corejs": 3 }],
              ["@babel/plugin-syntax-dynamic-import"]
            ],
          },
        }
      },
      {
        test: /\.svg(\?.*)?$/,
        type: 'asset/resource'
      },
      {
        test: /\.(scss|css)$/,
        use: [
          {
            loader: MiniCssExtractPlugin.loader,
            options: {
              emit: true
            }
          },
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
        // type: 'asset/inline',
        loader: 'url-loader',
        options: {
          limit: 100,
          name: 'fonts/[name].[ext]'
        }
      },
    ],
  },
  plugins: [
    new MiniCssExtractPlugin({
      filename: "css/[name].css"
    }),
    new WebpackManifestPlugin({
      fileName: 'manifest.json',
      seed: SHARED_SEED,
      filter: ({name, path}) => !name.match(/es5/gi) && !path.match(/es5/gi) && !path.match(/images/gi),
    }),
    new BundleAnalyzerPlugin({
      analyzerMode: "disabled",
      generateStatsFile: true
    }),
    // new WebpackBar()
  ],
  optimization: {
    removeEmptyChunks: true,
    providedExports: true,
    splitChunks: {
      chunks: 'initial',
      hidePathInfo: true,
      cacheGroups: {
        blog: {
          name: 'chunk-blog',
          reuseExistingChunk: true,
          test: /[\\/]node_modules[\\/](pace-js|sharer.js)[\\/]/,
          priority: 0,
          filename: 'js/[name].js'
        },
        vendors: {
          name: 'chunk-vendors',
          reuseExistingChunk: true,
          test: /[\\/]node_modules[\\/]/,
          priority: -10,
          filename: 'js/[name].js',
        },
      }
    },
    usedExports: true,
    minimize: true,
    minimizer: [
      new CssMinimizerPlugin({
        minimizerOptions: {
          preset: [
            "default",
            {
              discardComments: { removeAll: true },
            },
          ],
        },
      }),
      new TerserPlugin({
        terserOptions: {
          format: {
            comments: false,
          },
        },
        extractComments: false,
      }),
    ],
  },
  performance: {
    hints: 'warning',
    maxEntrypointSize: 512000,
    maxAssetSize: 512000,
  },
};

module.exports = [ legacyBundle, modernBundle ];
