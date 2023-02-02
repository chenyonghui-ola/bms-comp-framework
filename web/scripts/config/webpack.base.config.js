const fs = require("fs");
const path = require("path");
const webpack = require("webpack");
const PnpWebpackPlugin = require("pnp-webpack-plugin");
const HtmlWebpackPlugin = require("html-webpack-plugin");
const CaseSensitivePathsPlugin = require("case-sensitive-paths-webpack-plugin");
const InlineChunkHtmlPlugin = require("react-dev-utils/InlineChunkHtmlPlugin");
const TerserPlugin = require("terser-webpack-plugin");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const OptimizeCSSAssetsPlugin = require("optimize-css-assets-webpack-plugin");
const safePostCssParser = require("postcss-safe-parser");
const ManifestPlugin = require("webpack-manifest-plugin");
const InterpolateHtmlPlugin = require("react-dev-utils/InterpolateHtmlPlugin");
const WorkboxWebpackPlugin = require("workbox-webpack-plugin");
const WatchMissingNodeModulesPlugin = require("react-dev-utils/WatchMissingNodeModulesPlugin");
const ModuleScopePlugin = require("react-dev-utils/ModuleScopePlugin");
const paths = require("../utils/paths");
const modules = require("../utils/modules");
const getClientEnvironment = require("../utils/env");
const ModuleNotFoundPlugin = require("react-dev-utils/ModuleNotFoundPlugin");
const AddAssetHtmlPlugin = require("add-asset-html-webpack-plugin");
const postcssNormalize = require("postcss-normalize");
const appPackageJson = require(paths.appPackageJson);
const webpackbar = require("webpackbar");
const theme = require("../../src/theme");
const BundleAnalyzerPlugin = require("webpack-bundle-analyzer").BundleAnalyzerPlugin;

const { name } = require("../../package");

// Source maps are resource heavy and can cause out of memory issue for large source files.
// const shouldUseSourceMap = process.env.GENERATE_SOURCEMAP !== 'false';
const shouldUseSourceMap = false;
const shouldInlineRuntimeChunk = process.env.INLINE_RUNTIME_CHUNK !== "false";

const imageInlineSizeLimit = parseInt(process.env.IMAGE_INLINE_SIZE_LIMIT || "10000");

// Check if TypeScript is setup
const useTypeScript = fs.existsSync(paths.appTsConfig);

module.exports = function (webpackEnv) {
    const isEnvDevelopment = webpackEnv === "development";
    const isEnvProduction = webpackEnv === "production";
    const isEnvProductionProfile = isEnvProduction && process.argv.includes("--profile");
    const env = getClientEnvironment(paths.publicUrlOrPath.slice(0, -1));

    // common function to get style loaders
    const getStyleLoaders = (cssOptions, preProcessor) => {
        const loaders = [
            isEnvDevelopment && require.resolve("style-loader"),
            isEnvProduction && {
                loader: MiniCssExtractPlugin.loader,
                options: paths.publicUrlOrPath.startsWith(".")
                    ? { publicPath: "../../" }
                    : {}
            },
            {
                loader: require.resolve("css-loader"),
                options: cssOptions
            },
            {
                loader: require.resolve("postcss-loader"),
                options: {
                    ident: "postcss",
                    plugins: () => [
                        require("postcss-flexbugs-fixes"),
                        require("postcss-preset-env")({
                            autoprefixer: {
                                flexbox: "no-2009"
                            },
                            stage: 3
                        }),
                        postcssNormalize()
                    ],
                    sourceMap: isEnvProduction && shouldUseSourceMap
                }
            }
        ].filter(Boolean);
        if (preProcessor) {
            const loaderName =
                typeof preProcessor === "string" ? preProcessor : preProcessor.loader;
            const loaderOptions =
                typeof preProcessor === "string" ? {} : preProcessor.options;
            loaders.push(
                {
                    loader: require.resolve("resolve-url-loader"),
                    options: {
                        sourceMap: isEnvProduction && shouldUseSourceMap
                    }
                },
                {
                    loader: require.resolve(loaderName),
                    options: {
                        ...loaderOptions,
                        sourceMap: true
                    }
                }
            );
        }
        return loaders;
    };

    return {
        mode: isEnvProduction ? "production" : isEnvDevelopment && "development",
        // Stop compilation early in production
        bail: isEnvProduction,
        devtool: isEnvProduction
            ? shouldUseSourceMap
                ? "source-map"
                : false
            : isEnvDevelopment && "cheap-module-source-map",
        entry: [
            isEnvDevelopment && require.resolve("react-dev-utils/webpackHotDevClient"),
            paths.appIndexJs
        ].filter(Boolean),
        output: {
            path: isEnvProduction ? paths.appBuild : undefined,
            pathinfo: isEnvDevelopment,
            filename: isEnvProduction
                ? "static/js/[name].[contenthash:8].js"
                : isEnvDevelopment && "static/js/bundle.js",
            futureEmitAssets: true,
            chunkFilename: isEnvProduction
                ? "static/js/[name].[contenthash:8].chunk.js"
                : isEnvDevelopment && "static/js/[name].chunk.js",
            publicPath: paths.publicUrlOrPath,
            devtoolModuleFilenameTemplate: isEnvProduction
                ? info =>
                      path
                          .relative(paths.appSrc, info.absoluteResourcePath)
                          .replace(/\\/g, "/")
                : isEnvDevelopment &&
                  (info => path.resolve(info.absoluteResourcePath).replace(/\\/g, "/")),
            jsonpFunction: `webpackJsonp${appPackageJson.name}`,
            globalObject: "window",
            library: `${name}-[name]`,
            libraryTarget: "umd",
            jsonpFunction: `webpackJsonp_${name}`
        },
        optimization: {
            minimize: isEnvProduction,
            minimizer: [
                // This is only used in production mode
                new TerserPlugin({
                    terserOptions: {
                        parse: {
                            ecma: 8
                        },
                        compress: {
                            ecma: 5,
                            warnings: false,
                            comparisons: false,
                            inline: 2
                        },
                        mangle: {
                            safari10: true
                        },
                        // Added for profiling in devtools
                        keep_classnames: isEnvProductionProfile,
                        keep_fnames: isEnvProductionProfile,
                        output: {
                            ecma: 5,
                            comments: false,
                            ascii_only: true
                        }
                    },
                    sourceMap: shouldUseSourceMap
                }),
                // This is only used in production mode
                new OptimizeCSSAssetsPlugin({
                    cssProcessorOptions: {
                        parser: safePostCssParser,
                        map: shouldUseSourceMap
                            ? {
                                  inline: false,
                                  annotation: true
                              }
                            : false
                    },
                    cssProcessorPluginOptions: {
                        preset: ["default", { minifyFontValues: { removeQuotes: false } }]
                    }
                })
            ],
            splitChunks: {
                chunks: "all",
                name: false
            },
            runtimeChunk: {
                name: entrypoint => `runtime-${entrypoint.name}`
            }
        },
        resolve: {
            modules: ["node_modules", paths.appNodeModules].concat(
                modules.additionalModulePaths || []
            ),
            extensions: paths.moduleFileExtensions
                .map(ext => `.${ext}`)
                .filter(ext => useTypeScript || !ext.includes("ts")),
            alias: {
                "react-native": "react-native-web",
                ...(isEnvProductionProfile && {
                    "react-dom$": "react-dom/profiling",
                    "scheduler/tracing": "scheduler/tracing-profiling"
                }),
                ...(modules.webpackAliases || {}),
                "@ant-design": "@ant-design",
                "@": paths.appSrc,
                "src": paths.appSrc,
                "I18N": path.resolve(__dirname, "../../src/lang/index.js")
            },
            plugins: [
                PnpWebpackPlugin,
                new ModuleScopePlugin(paths.appSrc, [paths.appPackageJson])
            ]
        },
        resolveLoader: {
            plugins: [PnpWebpackPlugin.moduleLoader(module)]
        },
        module: {
            strictExportPresence: true,
            rules: [
                {
                    test: /page-routes\.js$/,
                    enforce: "pre",
                    use: path.resolve(__dirname, "../utils/route-loader.js"),
                    include: paths.appSrc
                },
                { parser: { requireEnsure: false } },
                {
                    test: /\.(js|mjs|jsx|ts|tsx)$/,
                    enforce: "pre",
                    use: ["eslint-loader"],
                    include: paths.appSrc,
                    exclude: [/node_modules/]
                },
                {
                    oneOf: [
                        {
                            test: [/\.bmp$/, /\.gif$/, /\.jpe?g$/, /\.png$/],
                            loader: require.resolve("url-loader"),
                            options: {
                                limit: imageInlineSizeLimit,
                                name: "static/image/[name].[hash:8].[ext]"
                            }
                        },
                        {
                            test: /\.(js|mjs|jsx|ts|tsx)$/,
                            include: paths.appSrc,
                            loader: require.resolve("babel-loader"),
                            options: {
                                customize: require.resolve(
                                    "babel-preset-react-app/webpack-overrides"
                                ),
                                plugins: [
                                    [
                                        require.resolve(
                                            "babel-plugin-named-asset-import"
                                        ),
                                        {
                                            loaderMap: {
                                                svg: {
                                                    ReactComponent:
                                                        "@svgr/webpack?-svgo,+titleProp,+ref![path]"
                                                }
                                            }
                                        }
                                    ]
                                ],
                                cacheDirectory: true,
                                cacheCompression: false,
                                compact: isEnvProduction
                            }
                        },
                        {
                            test: /\.css$/,
                            include: paths.appSrc,
                            use: getStyleLoaders({
                                importLoaders: 1,
                                sourceMap: isEnvProduction && shouldUseSourceMap
                            }),
                            sideEffects: true
                        },
                        {
                            test: /\.less$/,
                            include: paths.appSrc,
                            use: getStyleLoaders(
                                {
                                    importLoaders: 2,
                                    sourceMap: isEnvProduction && shouldUseSourceMap,
                                    modules: {
                                        localIdentName: "[local]-[hash:base64:5]"
                                    }
                                },
                                {
                                    loader: "less-loader",
                                    options: {
                                        javascriptEnabled: true,
                                        modifyVars: theme
                                    }
                                }
                            )
                        },
                        {
                            test: /\.less$/,
                            include: `${paths.appPath}/node_modules/antd`,
                            use: getStyleLoaders(
                                {
                                    importLoaders: 2,
                                    sourceMap: isEnvProduction && shouldUseSourceMap
                                },
                                {
                                    loader: "less-loader",
                                    options: {
                                        javascriptEnabled: true,
                                        modifyVars: theme
                                    }
                                }
                            )
                        },
                        {
                            loader: require.resolve("file-loader"),
                            exclude: [/\.(js|mjs|jsx|ts|tsx)$/, /\.html$/, /\.json$/],
                            options: {
                                name: "static/media/[name].[hash:8].[ext]"
                            }
                        }
                    ]
                }
            ],
            noParse: [require.resolve("typescript/lib/typescript.js")]
        },
        plugins: [
            new webpackbar(),
            new webpack.ProvidePlugin({
                $I18N: ["I18N", "default"]
            }),
            process.env.ANALYZ ? new BundleAnalyzerPlugin() : undefined,
            // new webpack.DllReferencePlugin({
            //     manifest: require(path.join(__dirname, '../dll', 'vendor-manifest.json')),
            // }),
            // new webpack.DllReferencePlugin({
            //     manifest: require(path.join(__dirname, '../dll', 'reactVendor-manifest.json')),
            // }),
            // Generates an `index.html` file with the <script> injected.
            new HtmlWebpackPlugin(
                Object.assign(
                    {},
                    { inject: true, template: paths.appHtml },
                    isEnvProduction
                        ? {
                              minify: {
                                  removeComments: true,
                                  collapseWhitespace: true,
                                  removeRedundantAttributes: true,
                                  useShortDoctype: true,
                                  removeEmptyAttributes: true,
                                  removeStyleLinkTypeAttributes: true,
                                  keepClosingSlash: true,
                                  minifyJS: true,
                                  minifyCSS: true,
                                  minifyURLs: true
                              }
                          }
                        : undefined
                )
            ),
            new AddAssetHtmlPlugin({
                filepath: path.resolve(__dirname, "../dll/*.dll.js"),
                publicPath: paths.publicUrlOrPath + "static/js",
                outputPath: path.join("static", "js")
            }),
            isEnvProduction &&
                shouldInlineRuntimeChunk &&
                new InlineChunkHtmlPlugin(HtmlWebpackPlugin, [/runtime-.+[.]js/]),
            new InterpolateHtmlPlugin(HtmlWebpackPlugin, env.raw),
            new ModuleNotFoundPlugin(paths.appPath),
            new webpack.DefinePlugin(env.stringified),
            // This is necessary to emit hot updates (currently CSS only):
            isEnvDevelopment && new webpack.HotModuleReplacementPlugin(),
            isEnvDevelopment && new CaseSensitivePathsPlugin(),
            isEnvDevelopment && new WatchMissingNodeModulesPlugin(paths.appNodeModules),
            isEnvProduction &&
                new MiniCssExtractPlugin({
                    filename: "static/css/[name].[contenthash:8].css",
                    chunkFilename: "static/css/[name].[contenthash:8].chunk.css"
                }),
            new ManifestPlugin({
                fileName: "asset-manifest.json",
                publicPath: paths.publicUrlOrPath,
                generate: (seed, files, entrypoints) => {
                    const manifestFiles = files.reduce((manifest, file) => {
                        manifest[file.name] = file.path;
                        return manifest;
                    }, seed);
                    const entrypointFiles = entrypoints.main.filter(
                        fileName => !fileName.endsWith(".map")
                    );

                    return {
                        files: manifestFiles,
                        entrypoints: entrypointFiles
                    };
                }
            }),
            new webpack.IgnorePlugin(/^\.\/locale$/, /moment$/),
            isEnvProduction &&
                new WorkboxWebpackPlugin.GenerateSW({
                    clientsClaim: true,
                    exclude: [/\.map$/, /asset-manifest\.json$/],
                    importWorkboxFrom: "cdn",
                    navigateFallback: paths.publicUrlOrPath + "index.html",
                    navigateFallbackBlacklist: [
                        new RegExp("^/_"),
                        new RegExp("/[^/?]+\\.[^/]+$")
                    ]
                })
        ].filter(Boolean),
        node: {
            module: "empty",
            dgram: "empty",
            dns: "mock",
            fs: "empty",
            http2: "empty",
            net: "empty",
            tls: "empty",
            child_process: "empty"
        },
        performance: false
    };
};
