const path = require('path')

module.exports = {
  src: path.resolve(__dirname, '../src'),

  build: path.resolve(__dirname, '../build'),

  public: path.resolve(__dirname, '../public'),

  storage: path.resolve(__dirname, '../storage/webpack'),

  dist:  path.resolve(__dirname, '../dist')
}
