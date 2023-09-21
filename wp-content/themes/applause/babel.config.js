module.exports = {
    presets: [
      [
        '@babel/preset-env',
        {
          useBuiltIns: 'entry',
          corejs: "3.31.0",
          debug: false
        }
      ]
    ]
  };