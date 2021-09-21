const HtmlWebpackPlugin = require("html-webpack-plugin");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");

module.exports = {
  mode: process.env.NODE_ENV || "production",
  entry: "./plugins/arDominionB5Plugin/webpack.entry.js",
  output: {
    path: __dirname + "/plugins/arDominionB5Plugin/build",
    filename: "js/bundle.[contenthash].js",
  },
  module: {
    rules: [
      {
        test: /\.(sa|sc|c)ss$/i,
        use: [
          MiniCssExtractPlugin.loader,
          "css-loader",
          "resolve-url-loader",
          { loader: "sass-loader", options: { sourceMap: true } },
        ],
      },
    ],
  },
  plugins: [
    new HtmlWebpackPlugin({
      template:
        "./plugins/arDominionB5Plugin/templates/_layout_start_webpack.php",
      filename: "../templates/_layout_start.php",
      publicPath: "/plugins/arDominionB5Plugin/build",
      inject: false,
      minify: false,
    }),
    new MiniCssExtractPlugin({
      filename: "css/bundle.[contenthash].css",
    }),
  ],
};