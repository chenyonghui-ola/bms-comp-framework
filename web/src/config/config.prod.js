export default {
    ajaxPrefix:
        process.env.ADMIN_ENV == "prod"
            ? process.env.ADMIN_PROD_URL
            : process.env.ADMIN_TEST_URL
};
