import { getTopNodeByNode } from "src/library/utils/tree-utils";
import { uniqueArray } from "src/library/utils";
import getSelectedMenuByPath from "src/commons/getSelectedMenuByPath";
import getMenus from "src/menus";
import { convertToTree } from "src/library/utils/tree-utils";

export default {
    initialState: {
        loading: false, // 请求菜单loading
        openKeys: [], // 当前展开菜单keys
        selectedMenu: null, // 当前选中菜单
        topMenu: [], // 当前选中菜单的顶级菜单
        keepOtherOpen: true, // 点击菜单进入页面时，保持其他菜单打开状态
        menus: [], // 菜单数据，树状结构
        mostUsedMenus: [], // 最常用菜单，使用此时usedTimes降序排列
        plainMenus: [] // 菜单数据，扁平化
    },
    syncStorage: {
        openKeys: true,
        selectedMenu: true,
        topMenu: true,
        keepOtherOpen: true,
        mostUsedMenus: true
    },

    /**
     * 获取系统菜单
     */
    getMenus: {
        payload: ({ params } = {}) => getMenus(params.userId),
        reducer: {
            resolve: (state, { payload: menus }) => {
                // 重新获取菜单之后，过滤mostUsedMenus，防止脏数据
                const mostUsedMenus = menus.filter(item =>
                    state.mostUsedMenus.find(it => it.key === item.key)
                );
                const { menuTreeData } = getMenuTreeDataAndPermissions(menus);

                return { menus: menuTreeData, mostUsedMenus, plainMenus: menus };
            }
        }
    },

    setKeepOtherOpen: keepOtherOpen => ({ keepOtherOpen }),
    setOpenKeys: openKeys => ({ openKeys }),
    setMenus: menus => ({ menus }),
    getMenuStatus: (arg, state) => {
        const path = window.location.pathname;
        const { keepOtherOpen } = state;
        const mostUsedMenus = [...state.mostUsedMenus];

        let openKeys = [...state.openKeys];
        let selectedMenu = getSelectedMenuByPath(path, state.menus);
        let topMenu = {};
        // 如果没有匹配到，使用上一次菜单
        if (!selectedMenu && path !== "/") {
            // 首页除外
            selectedMenu = state.selectedMenu;
        }

        if (selectedMenu) {
            topMenu = getTopNodeByNode(state.menus, selectedMenu);
            const parentKeys = selectedMenu.parentKeys || [];

            openKeys = keepOtherOpen ? openKeys.concat(parentKeys) : [...parentKeys];

            openKeys = uniqueArray(openKeys);

            // 更新最常用菜单
            const existMostUsedMenu = mostUsedMenus.find(
                item => item.key === selectedMenu.key
            );
            if (existMostUsedMenu) {
                existMostUsedMenu.usedTimes += 1;
            } else {
                mostUsedMenus.push({ ...selectedMenu, usedTimes: 1 });
            }

            mostUsedMenus.sort((a, b) => b.usedTimes - a.usedTimes);
        }

        return {
            topMenu,
            selectedMenu,
            openKeys,
            mostUsedMenus
        };
    }
};

// --------------------------------- helps -----------------------------------
/**
 * 获取菜单树状结构数据 和 随菜单携带过来的权限
 * @param menus 扁平化菜单数据
 */
export function getMenuTreeDataAndPermissions(menus) {
    // 用户权限code，通过菜单携带过来的 1 => 菜单 2 => 功能
    const permissions = menus.map(item => {
        if (item.type === "1") return item.key;
        if (item.type === "2") return item.code;
        return null;
    });

    // 获取菜单，过滤掉功能码
    menus = menus.filter(item => item.type !== "2");

    // 处理path： 只声明了url，为iframe页面
    menus = menus.map(item => {
        if (item.url) {
            item.path = `/iframe_page_/${window.encodeURIComponent(item.url)}`;
        }
        return item;
    });

    const orderedData = [...menus];

    // 菜单根据order 排序

    // const orderedData = [...menus].sort((a, b) => {
    //     const aOrder = a.order || 0;
    //     const bOrder = b.order || 0;

    //     // 如果order都不存在，根据 text 排序
    //     if (!aOrder && !bOrder) {
    //         return a.text > b.text ? 1 : -1;
    //     }

    //     return bOrder - aOrder;
    // });

    // 设置顶级节点path，有的顶级没有指定path，默认设置为子孙节点的第一个path
    // const findPath = node => {
    //     const children = orderedData.filter(item => item.parentKey === node.key)
    //     let path = ""
    //     if (children && children.length) {
    //         for (let i = 0; i < children.length; i++) {
    //             const child = children[i]
    //             if (child.path) {
    //                 path = child.path
    //                 break
    //             }
    //             path = findPath(child)
    //         }
    //     }
    //     return path
    // }

    orderedData.forEach(item => {
        if (!item.path) {
            // item.path = findPath(item)
            item.path = "";
        }
    });

    const menuTreeData = convertToTree(orderedData);
    return { menuTreeData, permissions };
}
