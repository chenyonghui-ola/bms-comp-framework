/**
 * 菜单数据 返回Promise各式，支持前端硬编码、异步获取菜单数据
 */
import * as _ from "lodash";
import { store } from "src/models";

export default function getMenus() {
    // 根据userId获取菜单数据 或在此文件中前端硬编码菜单
    let { menus, pages } = _.get(store.getState(), "user", {});
    menus = _.map(menus, m => {
        let _pmenu = _.find(menus, pm => pm.id === m.pid);
        let result = {
            id: m.id,
            key: m.code,
            text: m.name,
            icon: m.icon,
            order: m.weight,
            type: m.type
        };
        if (_pmenu?.id) result["parentKey"] = _pmenu.code;
        return result;
    });

    let menuIds = _.map(menus, m => m.id);
    pages = _.filter(pages, p => p.page_id <= 0 && _.includes(menuIds, p.menu_id));
    pages = _.map(pages, p => {
        let _menu = _.find(menus, m => m.id === p.menu_id);
        return {
            id: p.id,
            key: p.code,
            text: p.name,
            path: p.path,
            parentKey: _menu.key,
            order: p.weight,
            flag: p.flag,
            guid: p.guid,
            type: "page"
        };
    });

    // return Promise.resolve(MenuMaps)
    return Promise.resolve(_.concat(menus, pages));
}
