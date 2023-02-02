/**
 * 根据path获取对应的菜单
 * @param path
 * @param menuTreeData
 * @returns {*}
 */

import cfg from "src/config";
import { getNodeByKey, getNodeByPropertyAndValue } from "src/library/utils/tree-utils";
import { pathToRegexp } from "path-to-regexp";
const { baseName } = cfg;

export default function getSelectedMenuByPath(path, menuTreeData) {
    path = path.replace(baseName, "");
    let selectedMenu;
    if (menuTreeData) {
        if (path.indexOf("/_") > -1) {
            path = path.substring(0, path.indexOf("/_"));
        }

        // 先精确匹配
        selectedMenu = getNodeByPropertyAndValue(
            menuTreeData,
            "path",
            path,
            (itemValue, value, item) => {
                const isTop = item.children && item.children.length;
                return itemValue === value && !isTop; // 排除父级节点
            }
        );

        // 正则匹配，路由中有`:id`的情况
        // fixme 容易出问题：a/b/:id,会匹配 a/b/1, a/b/detail，有可能不是期望的结果，注意路由写法，a/b/tab/:id 具体的:id，添加一级，用来表明id是什么
        if (!selectedMenu && path !== "/") {
            // selectedMenu = getNodeByPropertyAndValue(
            //     menuTreeData,
            //     "path",
            //     path,
            //     (itemValue, value, item) => {
            //         const isTop = item.children && item.children.length;
            //         const re = pathToRegexp(itemValue);
            //         return !!re.exec(value) && !isTop // 排除父级节点
            //     }
            // );
            const currentUrl = `${location.pathname}${location.search}`;
            const node = getNodeByPropertyAndValue(
                menuTreeData,
                "path",
                currentUrl.replace(baseName, "")
            );
            selectedMenu = node;
        }
    }
    return selectedMenu;
}
