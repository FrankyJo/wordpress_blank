const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const IgnoreEmitPlugin = require('ignore-emit-webpack-plugin');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');
const CopyPlugin = require('copy-webpack-plugin');
const TerserPlugin = require('terser-webpack-plugin');
const ImageminWebpWebpackPlugin = require('imagemin-webp-webpack-plugin');
const ImageminPlugin = require('imagemin-webpack-plugin').default;
const path = require('path');
const glob = require('glob');

const ENV = process.env;
const isWatchMode = ENV.WATCH === 'true';

const PATHS = {
    src: path.resolve(__dirname, 'src'),
    src_js: path.resolve(__dirname, 'src/js'),
    src_css: path.resolve(__dirname, 'src/css'),
    src_img: path.resolve(__dirname, 'src/image'),
    public_img: path.resolve(__dirname, 'public/'),
};

const getEntries = (pattern, extension) => glob
    .sync(pattern)
    .reduce((entries, filename) => {
        const file = filename.split('/').pop();
        // получаем имя без расширения
        const [name] = file.match(/([a-zA-Z0-9-]+)(?=\.[a-z]+)/);
        // критические css или обычные
        const entryName = file.indexOf('critical') === 0
            ? `${extension}/critical/${name}`
            : `${extension}/${name}`;
        return { ...entries, [entryName]: filename };
    }, {});

module.exports = () => ({
    target: ['web', 'es5'], // для поддержки более старых браузеров
    mode: ENV.NODE_ENV,
    watch: isWatchMode,

    entry: {
        ...getEntries(`${PATHS.src_js}/main.js`, 'js'),
        ...getEntries(`${PATHS.src_js}/pages/*.js`, 'js'),
        ...getEntries(`${PATHS.src_css}/pages/**/*.scss`, 'css'),
    },

    output: {
        path: path.resolve(__dirname, 'public'),
        // filename будет по умолчанию [name].js, если нужно, можно указать
        // filename: '[name].js',
    },

    devtool: ENV.NODE_ENV === 'development' ? 'source-map' : false,

    resolve: {
        alias: {
            Components: `${PATHS.src_js}/components`,
        },
    },

    module: {
        rules: [
            // JS c Babel
            {
                test: /\.js$/,
                exclude: /node_modules\/(?!tippy.js|swiper).*/,
                use: {
                    loader: 'babel-loader',
                },
            },
            // SCSS + CSS
            {
                test: /\.(sa|sc|c)ss$/,
                exclude: /node_modules/,
                include: PATHS.src_css,
                use: [
                    MiniCssExtractPlugin.loader,
                    {
                        loader: 'css-loader',
                        options: {
                            url: false,
                        },
                    },
                    'postcss-loader',
                    'sass-loader',
                ],
            },
            // Подключение изображений через встроенные asset-модули
            {
                test: /\.(png|jpe?g|gif|svg)$/i,
                type: 'asset/resource',
                generator: {
                    // здесь вы указываете, куда сложить изображения
                    // примерно то же самое, что раньше делали в file-loader
                    filename: 'image/[name][ext]',
                },
            },
        ],
    },

    optimization: {
        minimize: true,
        minimizer: [
            new TerserPlugin({
                extractComments: false,
                terserOptions: {
                    format: {
                        comments: false,
                    },
                },
            }),
        ],
    },

    plugins: [
        // Плагин для преобразования в WebP
        new ImageminWebpWebpackPlugin({
            disable: ENV.NODE_ENV !== 'production',
            config: [{
                test: /\.(jpe?g|png)/,
                options: { quality: 85 },
            }],
            overrideExtension: true,
            detailedLogs: false,
            silent: true,
            strict: true,
        }),

        // Плагин для оптимизации изображений (png, jpg и т.д.)
        new ImageminPlugin({
            disable: ENV.NODE_ENV !== 'production',
            pngquant: {
                quality: '95-100',
            },
            jpegtran: {
                quality: '95-100',
            },
        }),

        new MiniCssExtractPlugin(),
        new IgnoreEmitPlugin(/(css)\/.*\.(js)/),

        new CleanWebpackPlugin(),

        // Копирование некоторых файлов (если нужно):
        new CopyPlugin({
            patterns: [
                { from: `${PATHS.src_img}`, to: 'image' },
                { from: `${PATHS.src}/media`, to: 'media' },
            ],
        }),
    ],
});
