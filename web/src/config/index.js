import defaultConfig from './config.default';
import devConfig from './config.dev';
import prodConfig from './config.prod';

let config = {
    development: devConfig,
    production: prodConfig,
};

const envConfig = config[process.env.NODE_ENV] || {};

export default {
    ...defaultConfig,
    ...envConfig,
};
