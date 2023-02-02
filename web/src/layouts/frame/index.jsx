import React, { Component } from "react";
import PropTypes from "prop-types";
import { Spin } from "antd";
import { Helmet } from "react-helmet";
import { withRouter } from "react-router-dom";
import { connect } from "src/models";
import getSelectedMenuByPath from "src/commons/getSelectedMenuByPath";
import { PAGE_FRAME_LAYOUT } from "src/models/settings";
import Header from "../components/header";
import Side from "../components/side";
import Tabs from "../components/tabs";
import "./style.less";

@withRouter
@connect(state => {
    const { selectedMenu, menus } = state.menu;
    const { title, breadcrumbs } = state.page;
    const { show: showSide, width, collapsed, collapsedWidth, dragging } = state.side;
    const { loading, loadingTip, isMobile } = state.system;
    const { pageFrameLayout, pageHeadShow, tabsShow } = state.settings;
    const loginUser = state.user;
    return {
        menus,
        selectedMenu,
        title,
        breadcrumbs,

        showSide,
        sideWidth: width,
        sideCollapsed: collapsed,
        sideCollapsedWidth: collapsedWidth,
        globalLoading: loading,
        globalLoadingTip: loadingTip,
        sideDragging: dragging,
        layout: pageFrameLayout,
        pageHeadShow, // 设置中统一控制的头部是否显示
        tabsShow,
        isMobile,
        loginUser,
    };
})
export default class FrameTopSideMenu extends Component {
    static propTypes = {
        layout: PropTypes.string,
    };
    static defaultProps = {
        layout: PAGE_FRAME_LAYOUT.SIDE_MENU, // top-menu side-menu
    };

    constructor(...props) {
        super(...props);
        // 从Storage中获取出需要同步到redux的数据
        this.props.action.getStateFromStorage();
        this.state = { loading: true };

        const {
            action: { menu, side, system },
            isMobile,
            loginUser,
        } = this.props;
        const userId = loginUser?.uid;
        // 获取系统菜单 和 随菜单携带过来的权限
        menu.getMenus({
            params: { userId },
            onResolve: res => {
                const menus = res || [];
                const permissions = [];
                const paths = [];

                menus.forEach(({ type, path, code }) => {
                    if (type === "2" && code) permissions.push(code);
                    if (path) paths.push(path);
                });
                // 保存用户权限到model中
                system.setPermissions(permissions);
                // 保存当前用户可用path到model中
                system.setUserPaths(paths);
            },
            onComplete: () => {
                this.setState({ loading: false });
            },
        });

        // 等待getStateFromStorage获取配置之后再设置
        setTimeout(() => {
            menu.getMenuStatus();
            side.show();
            this.setTitleAndBreadcrumbs();
            isMobile && side.setCollapsed(true);
        });

        this.props.history.listen(() => {
            // 加上timeout之后，tab页切换之后，对应页面就不render！
            setTimeout(() => {
                menu.getMenuStatus();
                side.show();
                this.setTitleAndBreadcrumbs();
                // this.props.action.user.fetchTodoCount();
                // 如果是移动端 隐藏菜单
                isMobile && side.setCollapsed(true);
            });
        });
    }

    setTitleAndBreadcrumbs() {
        const {
            action: { page },
            pageHeadShow,
            menus,
            title: prevTitle,
            breadcrumbs: prevBreadcrumbs,
        } = this.props;

        let selectedMenu = getSelectedMenuByPath(window.location.pathname, menus);
        let breadcrumbs = [];
        let title = "";

        if (selectedMenu) {
            title = { text: selectedMenu.text };
            if (selectedMenu.parentNodes) {
                breadcrumbs = selectedMenu.parentNodes.map(item => ({
                    key: item.key,
                    icon: item.icon,
                    text: item.text,
                    path: item.path,
                }));
            }
            breadcrumbs.push({
                key: selectedMenu.key,
                icon: selectedMenu.icon,
                text: selectedMenu.text,
            });
        }
        // 从菜单中没有获取到，有肯能是当前页面设置了，但是没有菜单对应
        page.setBreadcrumbs(
            !breadcrumbs.length && prevBreadcrumbs && prevBreadcrumbs.length
                ? prevBreadcrumbs
                : breadcrumbs
        );
        // 从菜单中没有获取到，有肯能是当前页面设置了，但是没有菜单对应
        page.setTitle(!title && prevTitle ? prevTitle : title);
        pageHeadShow ? page.showHead() : page.hideHead();
    }

    render() {
        let {
            layout,
            tabsShow,
            title,
            showSide,
            sideCollapsed,
            sideCollapsedWidth,
            sideWidth,
            globalLoading,
            globalLoadingTip,
            sideDragging,
            isMobile,
        } = this.props;

        let transitionDuration = sideDragging ? "0ms" : `300ms`;
        let isTopSideMenu = layout === PAGE_FRAME_LAYOUT.TOP_SIDE_MENU;
        let isSideMenu = layout === PAGE_FRAME_LAYOUT.SIDE_MENU;
        let hasSide = isTopSideMenu || isSideMenu;
        let theme = "dark"; // (isTopSideMenu || isSideMenu) ? 'dark' : 'default';
        let titleText = title?.text || title;
        let titleIsString = typeof titleText === "string";
        let topSpaceClass = ["frame-top-space"];
        let windowWidth = window.innerWidth;
        let sideWidthSpace = hasSide ? (!sideCollapsed ? sideWidth : 64) : 0;

        sideWidth = showSide ? (sideCollapsed ? sideCollapsedWidth : sideWidth) : 0;
        window.document.body.style.paddingLeft = !hasSide || isMobile ? "0px" : `${sideWidth}px`;

        if (isMobile) tabsShow = false;
        if (tabsShow) topSpaceClass.push("with-tabs");

        return (
            <div styleName="frame" className="no-print">
                <Helmet title={titleIsString ? titleText : ""} />
                <Header />
                <Side layout={layout} theme={theme} />
                <div styleName={topSpaceClass.join(" ")} />
                {tabsShow && (
                    <div
                        styleName="frame-tabs"
                        id="frame-page-tabs"
                        style={{
                            left: sideWidthSpace,
                            width: windowWidth - sideWidthSpace,
                            transitionDuration,
                        }}
                    >
                        <Tabs width={windowWidth - sideWidthSpace} />
                    </div>
                )}
                <div
                    styleName="frame-loading"
                    style={{ display: globalLoading ? "block" : "none" }}
                >
                    <Spin spinning size="large" tip={globalLoadingTip} />
                </div>
            </div>
        );
    }
}
