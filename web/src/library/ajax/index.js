import "./promise-extends";
import SxAjax from "./sx-ajax";
import createHoc from "./create-hoc";
import createHooks from "./create-hooks";
import handleError from "src/commons/handleError";
import handleSuccess from "src/commons/handleSuccess";
import cfg from "src/config";
import { getLoginUid } from "src/commons/loginFn";
import { toLogin } from "src/commons/loginFn";
import PubSub from "pubsub-js";

const { ajaxPrefix, ajaxTimeout } = cfg;
const _ = require("lodash");

// 默认配置在这里设置
export function withDefaultSettings(instance) {
    instance.defaults.baseURL = ajaxPrefix;
    instance.defaults.timeout = ajaxTimeout;
    instance.mockDefaults.baseURL = "/";
    // instance.defaults.headers["User-Language"] = language
    // instance.defaults.headers['XXX-TOKEN'] = 'token-value';
    // instance.defaults.headers.get['token'] = 'token-value';
    return instance;
}

// ajax工具，含有errorTip 和 successTip
const _ajax = withDefaultSettings(
    new SxAjax({
        onShowErrorTip: (error, errorTip) => handleError({ error, errorTip }),
        onShowSuccessTip: (response, successTip) => handleSuccess({ successTip }),
        isMock,
        reject: true
    })
);

// ajax工具，不含有 errorTip和successTip 一般models会使用
const __ajax = withDefaultSettings(new SxAjax({ isMock }));

// hooks
const {
    useGet: _useGet,
    useDel: _useDel,
    usePost: _usePost,
    usePut: _usePut,
    usePatch: _usePatch
} = createHooks(_ajax);

// 请求响应拦截
[__ajax.instance, _ajax.instance].forEach(instance => {
    // 请求拦截
    instance.interceptors.request.use(
        cfg => {
            // Do something before request is sent
            let loginUid = getLoginUid();
            if (loginUid) _.set(cfg, "params.loginuid", loginUid);
            cfg.headers["lang"] = localStorage.getItem("lang") || "zh_cn";
            return cfg;
        },
        error => {
            // Do something with request error
            return Promise.reject(error);
        }
    );

    // 响应拦截
    instance.interceptors.response.use(
        res => {
            if (res?.data?.code == 401) {
                toLogin();
                return Promise.reject(res);
            }
            PubSub.publish("network-end");
            return res?.data?.success ? res : Promise.reject(res);
        },
        error => {
            PubSub.publish("network-end");
            console.log("-------");
            console.log(error);
            console.log("-------");
            return Promise.reject(error);
        }
    );
});

// 判断请求是否是mock
function isMock(url /* url, data, method, options */) {
    // return mockUrls.indexOf(url) > -1 || url.startsWith("/mock");
    return false;
}

// hooks
export const useGet = _useGet;
export const useDel = _useDel;
export const usePost = _usePost;
export const usePut = _usePut;
export const usePatch = _usePatch;

// ajax高阶组件
export const ajaxHoc = createHoc(_ajax);

// ajax工具，不含有 errorTip和successTip 一般models会使用
export const ajax = __ajax;

export const http = _ajax;

// mockjs使用的axios实例
export const mockInstance = (__ajax.mockInstance = _ajax.mockInstance);
