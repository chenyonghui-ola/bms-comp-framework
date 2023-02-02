export const getUrlParams = paramKey => {
    var reg = new RegExp("(^|&)" + paramKey + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return decodeURIComponent(r[2]);
    return "";
};

export const isEmpty = obj => {
    return obj == "" || obj == undefined || obj == null || obj == "undefined" || obj == "null";
};

export const filterObj = obj => {
    let param = obj;
    Object.keys(param).forEach(item => {
        if (isEmpty(param[item])) {
            delete param[item];
        }
    });
    return param;
};
