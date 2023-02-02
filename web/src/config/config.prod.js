export default {
    ajaxPrefix:
        process.env.VEEKA_ENV == "prod"
            ? process.env.VEEKA_PROD_URL
            : process.env.VEEKA_TEST_URL
};
