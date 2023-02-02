import { getUrlParams } from "../utils";

export const cacheFilter = param => {
    const currentGuid = getUrlParams("guid");
    localStorage.setItem(currentGuid, JSON.stringify(param));
};

export const cacheColumns = param => {
    const currentGuid = getUrlParams("guid");
    localStorage.setItem(`${currentGuid}-current-columns`, JSON.stringify(param));
};

export const cacheInitColumns = param => {
    const currentGuid = getUrlParams("guid");
    localStorage.setItem(`${currentGuid}-init-columns`, JSON.stringify(param));
};

export const getCacheInitColumns = param => {
    const currentGuid = getUrlParams("guid");
    return JSON.parse(localStorage.getItem(`${currentGuid}-init-columns`) || "[]");
};

export const getCacheColumns = () => {
    const currentGuid = getUrlParams("guid");
    return JSON.parse(localStorage.getItem(`${currentGuid}-current-columns`) || "[]");
};

export const clearFilter = () => {
    const currentGuid = getUrlParams("guid");
    localStorage.removeItem(currentGuid);
};

export const getCacheFilter = () => {
    const currentGuid = getUrlParams("guid");
    return localStorage.getItem(currentGuid) || {};
};
