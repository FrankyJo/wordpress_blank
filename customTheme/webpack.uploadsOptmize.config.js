const CopyPlugin = require('copy-webpack-plugin');
const ImageminPlugin = require('imagemin-webpack-plugin').default
const imageminMozjpeg = require('imagemin-mozjpeg');
const WebpackBackupOutputPlugin = require('webpack-backup-output-plugin');
const path = require("path");


console.log(path.resolve(__dirname, '../../uploads-backup'))

const ENV = process.env;

module.exports = api => ({
    target: ['web', 'es5'],
    mode: ENV.NODE_ENV,
    entry: {},
    output: {
        path: path.resolve(__dirname, '../../uploads'),
    },
    resolve: {},
    module: {
        rules: [
            {
                test: /\.(png|jpe?g|gif|svg)$/i,
                use: [
                    {
                        loader: 'file-loader',
                        options: {
                            name: '[path][name].[ext]',
                            context: ('../../uploads'),
                            publicPath: '../',
                            useRelativePaths: true,
                        },
                    }
                ],
            },
        ],
    },

    plugins: [
        new WebpackBackupOutputPlugin({
            files: ['**/*.*'],
            clean: false,
            backupRoot: '../../uploads-backup'
        }),
        new CopyPlugin({
            patterns: [
                {from: `../../uploads`, to: `../uploads`},
            ]
        }),
        new ImageminPlugin({
            disable: ENV.NODE_ENV !== 'production',
            pngquant: {
                quality: '80'
            },
            plugins: [
                imageminMozjpeg({
                    quality: 80,
                    progressive: true
                })
            ]
        })
    ],
});
