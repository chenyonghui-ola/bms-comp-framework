import React, { Component } from "react";
import { Menu, Checkbox } from "antd";
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
    handleOnMenuItemClick = obj => {
        this.props.showPagePoint(obj.key);
    };

    handleOnCheckBoxChange = (e, obj) => {
        const checked = e.target.checked;
        this.props.handleCheckBoxChange(checked, obj);
    };

    renderMenus() {
        const { dataSource } = this.props;
        if (dataSource && dataSource.length) {
            return renderNode(dataSource, (item = {}, children) => {
                let { key, text, type, checked, checkDisabled } = item;
                let title = (
                    <div style={{ position: "relative" }}>
                        <Checkbox
                            onChange={e => this.handleOnCheckBoxChange(e, item)}
                            disabled={checkDisabled}
                            checked={checked}
                        />
                        <span style={{ marginLeft: 12 }}>{text}</span>
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
        const { theme, openKeys } = this.props;
        return (
            <div styleName="comp-side-navigation">
                <Menu
                    mode="inline"
                    // defaultOpenKeys={openKeys}
                    openKeys={openKeys}
                    theme={theme}
                    getPopupContainer={trigger => trigger.parentElement}
                    onClick={this.handleOnMenuItemClick}
                >
                    {this.renderMenus()}
                </Menu>
            </div>
        );
    }
}
