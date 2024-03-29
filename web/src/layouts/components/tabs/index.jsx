import React, { Component } from "react";
import {
    CloseCircleOutlined,
    CloseOutlined,
    CloseSquareOutlined,
    SyncOutlined,
    VerticalLeftOutlined,
    VerticalRightOutlined
} from "@ant-design/icons";
import Icon from "src/components/icon";
import { dropByCacheKey, getCachingKeys, clearCache } from "react-router-cache-route";
import { Menu } from "antd";
import config from "src/commons/configHoc";
import ContextMenu from "./context-menu";
import DraggableTabsBar from "./draggable-tabs-bar";
import "./style.less";

@config({
    router: true,
    connect: state => ({
        dataSource: state.system.tabs,
        keepAlive: state.system.keepAlive
    })
})
export default class PageTabs extends Component {
    state = {
        contextVisible: false,
        contextEvent: null,
        contextMenu: ""
    };

    handleClose = item => {
        const { path: targetPath } = item;
        this.deleteCache(targetPath);
        this.props.action.system.closeTab(targetPath);
    };

    deleteCache = cacheKey => {
        setTimeout(() => {
            dropByCacheKey(cacheKey);
        }, 50);
    };

    handleSortEnd = ({ oldIndex, newIndex }) => {
        const dataSource = [...this.props.dataSource];

        // 元素移动
        dataSource.splice(newIndex, 0, dataSource.splice(oldIndex, 1)[0]);

        this.props.action.system.setTabs(dataSource);
    };

    handleClick = item => {
        const separator = "/iframe_page_/";
        let path = item.path;
        if (path.indexOf(separator) !== -1) {
            const url = window.encodeURIComponent(path.split(separator)[1]);
            path = `${separator}${url}`;
        }
        this.props.history.push(path);
    };

    handleRightClick = (e, tab) => {
        e.preventDefault();

        const contextMenu = this.renderContextMenu(tab);

        this.setState({
            contextVisible: true,
            contextEvent: { clientX: e.clientX, clientY: e.clientY },
            contextMenu
        });
    };

    renderContextMenu = tab => {
        const { dataSource, keepAlive } = this.props;
        const disabledClose = dataSource.length === 1;
        const tabIndex = dataSource.findIndex(item => item.path === tab.path);
        const disabledCloseLeft = tabIndex === 0;
        const disabledCloseRight = tabIndex === dataSource.length - 1;

        return (
            <Menu
                selectable={false}
                onClick={({ key: action }) => this.handleMenuClick(action, tab.path)}
            >
                {keepAlive
                    ? [
                          <Menu.Item key="refresh">
                              <SyncOutlined /> 刷新
                          </Menu.Item>,
                          <Menu.Item key="refreshAll">
                              <SyncOutlined /> 刷新全部
                          </Menu.Item>,
                          <Menu.Divider key="divider" />
                      ]
                    : null}
                <Menu.Item key="close" disabled={disabledClose}>
                    <CloseOutlined /> 关闭
                </Menu.Item>
                <Menu.Item key="closeOthers" disabled={disabledClose}>
                    <CloseCircleOutlined /> 关闭其他
                </Menu.Item>
                {/* <Menu.Item key="closeAll" disabled={disabledClose}>
                    <CloseSquareOutlined /> 关闭所有
                </Menu.Item> */}
                <Menu.Item key="closeLeft" disabled={disabledCloseLeft}>
                    <VerticalLeftOutlined /> 关闭左侧
                </Menu.Item>
                <Menu.Item key="closeRight" disabled={disabledCloseRight}>
                    <VerticalRightOutlined /> 关闭右侧
                </Menu.Item>
            </Menu>
        );
    };

    handleMenuClick = (action, targetPath) => {
        const cacheKeys = getCachingKeys();
        const {
            action: { system }
        } = this.props;

        if (action === "refresh") system.refreshTab(targetPath);
        if (action === "refreshAll") system.refreshAllTab();
        if (action === "close") {
            system.closeTab(targetPath);
            this.deleteCache(targetPath);
        }
        if (action === "closeOthers") {
            system.closeOtherTabs(targetPath);
            cacheKeys
                .filter(item => item != targetPath)
                .forEach(item => {
                    this.deleteCache(item);
                });
        }
        if (action === "closeAll") system.closeAllTabs();
        if (action === "closeLeft") {
            system.closeLeftTabs(targetPath);
            cacheKeys
                .filter(item => item != targetPath)
                .forEach(item => {
                    this.deleteCache(item);
                });
        }
        if (action === "closeRight") {
            system.closeRightTabs(targetPath);
            cacheKeys
                .filter(item => item != targetPath)
                .forEach(item => {
                    this.deleteCache(item);
                });
        }
    };

    render() {
        let { dataSource, width } = this.props;
        let { contextVisible, contextEvent, contextMenu } = this.state;
        let currentTab = dataSource.find(item => item.active);
        dataSource = dataSource.filter(item => item.path !== "/");

        let tabsBarDataSource = dataSource.map(item => {
            let { text: tabTitle, path, icon } = item;
            let title = tabTitle;

            if (typeof tabTitle === "object" && tabTitle.text) title = tabTitle.text;

            if (tabTitle?.icon) icon = tabTitle.icon;

            if (icon)
                title = (
                    <div style={{ flex: 1, textAlign: "center" }}>
                        <Icon type={icon} style={{ marginRight: 4 }} />
                        {title}
                    </div>
                );

            return {
                key: path,
                title,
                closable: true,
                ...item
            };
        });

        if (tabsBarDataSource.length <= 0) return null;

        return (
            <div styleName="comp-tabs">
                <ContextMenu
                    visible={contextVisible}
                    onChange={contextVisible => this.setState({ contextVisible })}
                    event={contextEvent}
                    content={contextMenu}
                />
                <DraggableTabsBar
                    dataSource={tabsBarDataSource}
                    itemWrapper={(itemJsx, item, wrapperClassName) => {
                        return (
                            <div
                                className={wrapperClassName}
                                onContextMenu={e => this.handleRightClick(e, item)}
                            >
                                {itemJsx}
                            </div>
                        );
                    }}
                    onSortEnd={this.handleSortEnd}
                    onClose={this.handleClose}
                    onClick={this.handleClick}
                    activeKey={currentTab?.path}
                    parentWidth={width}
                />
            </div>
        );
    }
}
