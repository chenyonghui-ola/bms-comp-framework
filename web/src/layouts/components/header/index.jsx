import React, { Component } from "react";
import PropTypes from "prop-types";
import { MenuFoldOutlined, MenuUnfoldOutlined, MenuOutlined } from "@ant-design/icons";
import Link from "src/components/page-link";
import Logo from "./logo";
import HeaderUser from "./user";
import { connect } from "src/models";
import { PAGE_FRAME_LAYOUT } from "src/models/settings";
import Breadcrumb from "../breadcrumb";
import "./style.less";

@connect(state => {
    const { menus, topMenu } = state.menu;
    const { show: showSide, width, collapsed, collapsedWidth, dragging } = state.side;
    const { breadcrumbs } = state.page;
    const { pageFrameLayout } = state.settings;
    const { isMobile, language } = state.system;

    return {
        menus,
        topMenu,
        showSide,
        sideWidth: width,
        sideCollapsed: collapsed,
        sideCollapsedWidth: collapsedWidth,
        sideDragging: dragging,
        breadcrumbs,
        isMobile,
        language,
        layout: pageFrameLayout
    };
})
export default class Header extends Component {
    static propTypes = {
        layout: PropTypes.string,
        theme: PropTypes.string
    };

    static defaultProps = {
        layout: PAGE_FRAME_LAYOUT.SIDE_MENU, // top-side-menu top-menu side-menu
        theme: "default" // default dark
    };

    handleToggle = () => {
        const { sideCollapsed } = this.props;
        this.props.action.side.setCollapsed(!sideCollapsed);
    };

    renderToggle = (showToggle, sideCollapsed, theme) => {
        if (!showToggle) return null;

        let props = {
            onClick: this.handleToggle,
            style:
                theme === "dark"
                    ? { color: "#fff", backgroundColor: "#222" }
                    : { color: "rgba(0,0,0,0.45)" }
        };
        if (!this.props.isMobile) {
            return sideCollapsed ? (
                <MenuUnfoldOutlined {...props} styleName="mh-trigger" />
            ) : (
                <MenuFoldOutlined {...props} styleName="mh-trigger" />
            );
        } else {
            return <MenuOutlined {...props} styleName="mh-trigger-mobile" />;
        }
    };

    render() {
        let {
            layout,
            sideCollapsed,
            sideCollapsedWidth,
            sideWidth,
            sideDragging,
            breadcrumbs,
            children,
            isMobile
        } = this.props;
        let isTopSideMenu = layout === PAGE_FRAME_LAYOUT.TOP_SIDE_MENU;
        let isSideMenu = layout === PAGE_FRAME_LAYOUT.SIDE_MENU;
        let showToggle = isTopSideMenu || isSideMenu;
        let transitionDuration = sideDragging ? "0ms" : "300ms";
        let theme =
            this.props.theme || (isTopSideMenu || isSideMenu ? "default" : "dark");

        sideWidth = sideCollapsed ? sideCollapsedWidth : sideWidth;

        return (
            <div styleName="mod-header" data-theme={theme}>
                {!isMobile && (
                    <div
                        styleName="mh-logo"
                        style={{
                            flex: `0 0 ${sideWidth}px`,
                            transition: "all " + transitionDuration
                        }}
                    >
                        <Link to="/">
                            <Logo min={sideCollapsed} title="Veeka 系统" />
                        </Link>
                    </div>
                )}

                <div styleName="mod-header-right">
                    {this.renderToggle(showToggle, sideCollapsed, theme)}

                    {children ? (
                        <div styleName="mh-center">{children}</div>
                    ) : (
                        <div styleName="mh-center">
                            {isSideMenu && !isMobile ? (
                                <div style={{ marginLeft: 16 }}>
                                    <Breadcrumb theme={theme} dataSource={breadcrumbs} />
                                </div>
                            ) : null}
                        </div>
                    )}

                    <div styleName="mh-right">
                        <HeaderUser styleName="action" theme={theme} />
                    </div>
                </div>
            </div>
        );
    }
}
