import cfg from "src/config";
import { session } from "src/library/utils/storage";

const localStorage = window.localStorage;
const sessionStorage = window.sessionStorage;
const { baseName, LOGIN_USER_STORAGE_KEY, LOGIN_UID } = cfg;

/**
 * 设置当前用户信息
 * @param loginUser 当前登录用户信息
 */
export function setLoginUser(loginUser = {}) {
    // 将用户属性在这里展开，方便查看系统都用到了那些用户属性
    // const { id, name, avatar, token, permissions, ...others } = loginUser;
    /* const userStr = JSON.stringify({
        id,             // 用户id 必须
        name,           // 用户名 必须
        avatar,         // 用头像 非必须
        token,          // 登录凭证 非必须 ajax请求有可能会用到，也许是cookie
        permissions,    // 用户权限
        ...others,      // 其他属性
    }); */
    const userStr = JSON.stringify(loginUser);

    sessionStorage.setItem(LOGIN_USER_STORAGE_KEY, userStr);
}

/**
 * 获取当前用户信息
 * @returns {any}
 */
export function getLoginUser() {
    const loginUser = sessionStorage.getItem(LOGIN_USER_STORAGE_KEY);
    return loginUser ? JSON.parse(loginUser) : null;
}

/**
 * 设置当前用户UID(注：用于测试环境)≈
 * @param id 当前登录用户id
 */
export function setLoginUid(id = null) {
    id && localStorage.setItem(LOGIN_UID, id);
}

/**
 * 获取当前用户UID(注：用于测试环境)
 * @returns {any}
 */
export function getLoginUid() {
    const loginUid = localStorage.getItem(LOGIN_UID);
    return loginUid;
}

/**
 * 判断用户是否登录 前端简单通过登录用户是否存在来判断
 * @returns {boolean}
 */
export function isLogin() {
    // 如果当前用户存在，就认为已经登录了
    return !!getLoginUser();
}

/**
 * 跳转到登录页面
 */
export function toLogin() {
    const loginPath = "/login";

    // 判断当前页面是否已经是login页面，如果是，直接返回，不进行跳转，防止出现跳转死循环
    const pathname = window.location.pathname;
    if (pathname.indexOf(loginPath) !== -1) return null;

    // 清除相关数据
    session.clear();
    localStorage.clear();
    sessionStorage.clear();
    sessionStorage.setItem("last-href", window.location.pathname);
    console.log("&&&&&&&&&");
    console.log(`${baseName}${loginPath}`);
    console.log("&&&&&&&&&");
    window.location.href = `${baseName}${loginPath}`;
    return null;
}
