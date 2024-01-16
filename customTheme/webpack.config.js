const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const IgnoreEmitPlugin = require('ignore-emit-webpack-plugin');
const {CleanWebpackPlugin} = require('clean-webpack-plugin');
const CopyPlugin = require('copy-webpack-plugin');
const TerserPlugin = require('terser-webpack-plugin');
const ImageminWebpWebpackPlugin = require('imagemin-webp-webpack-plugin');
const ImageminPlugin = require('imagemin-webpack-plugin').default

const path = require('path');
const glob = require('glob');

const ENV = process.env;

const isWatchMode = ENV.WATCH === 'true';

const PATHS = {
    src: path.resolve(__dirname, 'src'),
    src_js: path.resolve(__dirname, 'src/js'),
    src_css: path.resolve(__dirname, 'src/css'),
    src_img: path.resolve(__dirname, 'src/images'),
    public_img: path.resolve(__dirname, 'public/'),
};

const getEntries = (pattern, extension) => glob
    .sync(pattern)
    .reduce((entries, filename) => {
        const file = filename.split('/').pop();
        const [name] = file.match(/([a-z-A-Z-0-9]+)(?=\.[a-z]+)/g);
        const entryName = file.indexOf('critical') === 0 ? `${extension}/critical/${name}` : `${extension}/${name}`;
        return {...entries, [entryName]: filename};
    }, {});

module.exports = api => ({
    target: ['web', 'es5'],
    mode: ENV.NODE_ENV,
    watch: isWatchMode,
    entry: {
        ...getEntries(`${PATHS.src_js}/main.js`, 'js'),
        ...getEntries(`${PATHS.src_js}/pages/*.js`, 'js'),
        ...getEntries(`${PATHS.src_css}/pages/**/*.scss`, 'css'),
    },
    output: {
        path: path.resolve(__dirname, 'public'),
    },
    devtool: ENV.NODE_ENV === 'development' ? 'source-map' : false,
    resolve: {
        alias: {
            Components: `${PATHS.src_js}/components`,
        },
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /node_modules\/(?!tippy.js|swiper).*/,
                use: {
                    loader: 'babel-loader',
                }
            },
            {
                test: /\.css$/,
                loader: 'style-loader',
            },
            {
                test: /\.css$/,
                loader: 'css-loader',
                options: {
                    url: false,
                },
            },
            {
                test: /\.(sa|sc|c)ss$/,
                exclude: /node_modules/,
                include: PATHS.src_css,
                use: [
                    MiniCssExtractPlugin.loader,
                    'css-loader',
                    'postcss-loader',
                    'sass-loader',
                ]
            },
            {
                test: /\.(png|jpe?g|gif|svg)$/i,
                use: [
                    {
                        loader: 'file-loader',
                        options: {
                            name: '[path][name].[ext]',
                            context: path.resolve(__dirname, 'src/'),
                            publicPath: '../',
                            useRelativePaths: true,
                        },
                    }
                ],
            },
        ],
    },
    optimization: {
        minimize: true,
        minimizer: [
            new TerserPlugin({
                terserOptions: {
                    format: {
                        comments: false,
                    },
                },
                extractComments: false,
            })
        ],
    },
    plugins: [
        new ImageminWebpWebpackPlugin({
            disable: ENV.NODE_ENV !== 'production',
            config: [{
                test: /\.(jpe?g|png)/,
                options: {
                    quality: 85
                }
            }],
            overrideExtension: true,
            detailedLogs: false,
            silent: true,
            strict: true
        }),
        new ImageminPlugin({
            disable: ENV.NODE_ENV !== 'production',
            pngquant: {
                quality: '95-100'
            },
            jpegtran:{
                quality: '95-100'
            }
        }),
        new MiniCssExtractPlugin(),
        new IgnoreEmitPlugin(/(css)\/.*\.(js)/),
        new CleanWebpackPlugin(),
        new CopyPlugin({
            patterns: [
                {from: `${PATHS.src_img}`, to: `img`},
                {from: `${PATHS.src}/media`, to: `media`}
            ]
        })
    ],
});
