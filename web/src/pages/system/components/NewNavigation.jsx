import React, { Component } from "react";
import PropTypes from "prop-types";
import { Menu } from "antd";
import { ContextMenu, MenuItem, ContextMenuTrigger } from "react-contextmenu";
import { If, actionType } from "./index";
import "./index.less";

/**
 * 渲染树，cb(node[, children nodes])
 * @param {Array} treeData 树的树状结构数据
 * @param {function} cb 回调函数：cb(node[, children nodes])
 */
const renderNode = function (treeData, cb) {
    const loop = data =>
        data.map(item => {
            if (item && item.children) {
                return cb(item, loop(item.children)); // item children Item
            }
            return cb(item); // 叶子节点
        });
    return loop(treeData);
};

export default class SideMenu extends Component {
    static propTypes = {
        dataSource: PropTypes.array, // 菜单数据
        theme: PropTypes.string, // 主题
        collapsed: PropTypes.bool, // 是否收起
        openKeys: PropTypes.array, // 打开菜单keys
        selectedKeys: PropTypes.array, // 选中菜单keys
        onOpenChange: PropTypes.func // 菜单打开关闭时触发
    };

    static defaultProps = {
        dataSource: [],
        theme: "dark",
        collapsed: false,
        openKeys: [],
        selectedKeys: [],
        onOpenChange: () => true
    };

    handleOpenChange = openKeys => {
        // this.props.onOpenChange(openKeys);
    };

    handleClick = (e, data, type) => {
        e.stopPropagation();
        this.props.handleRightMenuClick(data, type);
    };

    handleOnMenuItemClick = obj => {
        this.props.showPagePoint(obj.key);
    };

    renderMenuItem = obj => {
        return (
            <>
                <If data={!obj.parentKey}>
                    {/* <MenuItem onClick={e => this.handleClick(e, obj, actionType.ADDMENUTOP)}>
                        <span styleName="menu-item-text">上方添加导航</span>
                    </MenuItem> */}
                    <MenuItem onClick={e => this.handleClick(e, obj, actionType.ADDMENUBOTTOM)}>
                        <span styleName="menu-item-text">下方添加导航</span>
                    </MenuItem>
                    <MenuItem divider />
                </If>
                <If data={obj.parentKey}>
                    {/* <MenuItem onClick={e => this.handleClick(e, obj, actionType.ADDPAGETOP)}>
                        <span styleName="menu-item-text">上方添加同级页面</span>
                    </MenuItem> */}
                    {/* <MenuItem onClick={e => this.handleClick(e, obj, actionType.ADDPAGEBOTTOM)}>
                        <span styleName="menu-item-text">下方添加同级页面</span>
                    </MenuItem>
                    <MenuItem divider /> */}
                    {null}
                </If>
                <If data={obj.type == "menu-1" || obj.type == "menu-2"}>
                    <MenuItem onClick={e => this.handleClick(e, obj, actionType.ADDCHILDMENU)}>
                        <span styleName="menu-item-text">添加子导航</span>
                    </MenuItem>
                </If>
                <If data={obj.type != "page"}>
                    <MenuItem onClick={e => this.handleClick(e, obj, actionType.ADDCHILDRENPAGE)}>
                        <span styleName="menu-item-text">添加子页面</span>
                    </MenuItem>
                </If>
                {/* <MenuItem onClick={e => this.handleClick(e, obj, actionType.TOP)}>
                    <span styleName="menu-item-text">上移</span>
                </MenuItem>
                <MenuItem onClick={e => this.handleClick(e, obj, actionType.BOTTOM)}>
                    <span styleName="menu-item-text">下移</span>
                </MenuItem> */}
                <MenuItem divider />
                <MenuItem onClick={e => this.handleClick(e, obj, actionType.DELETE)}>
                    <span styleName="menu-item-text">删除</span>
                </MenuItem>
                <MenuItem onClick={e => this.handleClick(e, obj, actionType.UPDATE)}>
                    <span styleName="menu-item-text">编辑</span>
                </MenuItem>
            </>
        );
    };

    renderMenus() {
        const { dataSource } = this.props;
        if (dataSource && dataSource.length) {
            return renderNode(dataSource, (item = {}, children) => {
                let { key, text, type } = item;
                let title = (
                    <div>
                        <ContextMenuTrigger id={`${item.key}`}>
                            <div>{text}</div>
                        </ContextMenuTrigger>
                        <ContextMenu
                            id={`${item.key}`}
                            style={{
                                backgroundColor: "white",
                                padding: 16,
                                border: "1px solid #ccc",
                                cursor: "pointer",
                                zIndex: 9999
                            }}
                        >
                            {this.renderMenuItem(item)}
                        </ContextMenu>
                    </div>
                );

                return children || type != "page" ? (
                    <Menu.SubMenu key={key} title={title}>
                        {children}
                    </Menu.SubMenu>
                ) : (
                    <Menu.Item key={key} title={title} type={type}>
                        <div>{title}</div>
                    </Menu.Item>
                );
            });
        }
        return null;
    }

    render() {
        let { theme, collapsed, openKeys, selectedKeys } = this.props;
        let menuProps = collapsed ? {} : { openKeys };

        return (
            <div styleName="comp-side-navigation">
                <Menu
                    mode="inline"
                    // {...menuProps}
                    // selectedKeys={selectedKeys}
                    defaultOpenKeys={openKeys}
                    // expandIcon={
                    //     <div style={{ width: 20, height: 20, backgroundColor: "transparent" }} />
                    // }
                    // openKeys={openKeys}
                    theme={theme}
                    getPopupContainer={trigger => trigger.parentElement}
                    inlineCollapsed={collapsed}
                    // onOpenChange={this.handleOpenChange}
                    onClick={this.handleOnMenuItemClick}
                >
                    {this.renderMenus()}
                </Menu>
            </div>
        );
    }
}
