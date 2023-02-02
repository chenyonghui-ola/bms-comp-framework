import React, { Component } from "react";
import PropTypes from "prop-types";
import { Menu } from "antd";
import { withRouter } from "react-router-dom";
import cfg from "src/config";
import Icon from "src/components/icon";
import Link from "src/components/page-link";
import "./style.less";

/**
 * 渲染树，cb(node[, children nodes])
 * @param {Array} treeData 树的树状结构数据
 * @param {function} cb 回调函数：cb(node[, children nodes])
 */
const renderNode = function (treeData, cb) {
    const loop = data =>
        data.map(item => {
            if (item.children) {
                return cb(item, loop(item.children)); // item children Item
            }
            return cb(item); // 叶子节点
        });
    return loop(treeData);
};

class SideMenu extends Component {
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
        this.props.onOpenChange(openKeys);
    };

    gotoRightContent = item => {
        let { path, flag, guid } = item;
        const less_code_url = `/lesscode/common?guid=${guid}`;
        const need_open_other_tab = ["/cs/workbench/chatInit"];
        if (flag == 1) {
            this.props.history.push(less_code_url);
        } else {
            if (need_open_other_tab.includes(path)) {
                window.open(`${cfg.baseName}${path}?noFrame=true`, "_blank");
            } else {
                this.props.history.push(path);
            }
        }
    };

    renderMenus() {
        const { dataSource, collapsed } = this.props;

        if (dataSource && dataSource.length) {
            return renderNode(dataSource, (item, children) => {
                let { key, path, text, icon, flag, guid } = item;
                const less_code_url = `/lesscode/common?guid=${guid}`;
                let title = <span>{text}</span>;
                if (icon)
                    title = (
                        <span>
                            <Icon
                                type={icon}
                                className={
                                    collapsed
                                        ? "csn-icon csn-icon-collapsed"
                                        : "side-icon"
                                }
                            />
                            <i
                                style={{ fontStyle: "normal" }}
                                styleName={
                                    collapsed ? "csn-text csn-text-hide" : "csn-text"
                                }
                            >
                                {text}
                            </i>
                        </span>
                    );
                return children ? (
                    <Menu.SubMenu key={key} title={title}>
                        {children}
                    </Menu.SubMenu>
                ) : (
                    <Menu.Item key={key} onClick={() => this.gotoRightContent(item)}>
                        {title}
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
                    {...menuProps}
                    selectedKeys={selectedKeys.map(item => `${item}`)}
                    theme={theme}
                    getPopupContainer={trigger => trigger.parentElement}
                    inlineCollapsed={collapsed}
                    onOpenChange={this.handleOpenChange}
                >
                    {this.renderMenus()}
                </Menu>
            </div>
        );
    }
}

export default withRouter(SideMenu);
