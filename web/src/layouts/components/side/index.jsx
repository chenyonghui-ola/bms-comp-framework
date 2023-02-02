import React, { Component } from "react";
import PropTypes from "prop-types";
import { getScrollBarWidth } from "src/library/utils";
import Navigation from "./navigation";
import { connect } from "../../../models/index";
import { PAGE_FRAME_LAYOUT } from "src/models/settings";
import DragBar from "./dragbar";
import "./style.less";

const scrollBarWidth = getScrollBarWidth();

@connect(state => {
    const { menus, openKeys, topMenu, selectedMenu } = state.menu;
    const { show: showSide, width, collapsed, collapsedWidth, dragging } = state.side;
    const { isMobile } = state.system;
    return {
        menus,
        openKeys,
        topMenu,
        selectedMenu,

        showSide,
        isMobile,
        sideWidth: width,
        sideCollapsed: collapsed,
        sideCollapsedWidth: collapsedWidth,
        sideDragging: dragging
    };
})
export default class Side extends Component {
    static propTypes = {
        layout: PropTypes.string,
        theme: PropTypes.string
    };

    static defaultProps = {
        layout: PAGE_FRAME_LAYOUT.SIDE_MENU // top-menu side-menu
    };

    componentDidMount() {
        this.scrollMenu();
    }

    componentDidUpdate(prevProps) {
        this.scrollMenu(prevProps);
    }

    scrollMenu = (prevProps = {}) => {
        // 等待当前菜单选中
        setTimeout(() => {
            const { selectedMenu } = this.props;
            const { selectedMenu: prevSelectedMenu } = prevProps;
            if (selectedMenu && prevSelectedMenu && selectedMenu.key === prevSelectedMenu.key) {
                return;
            }
            const selectedMenuNode = this.inner?.querySelector(".ant-menu-item-selected");
            if (!selectedMenuNode) return;

            const innerHeight = this.inner.clientHeight;
            const innerScrollTop = this.inner.scrollTop;
            const selectedMenuTop = selectedMenuNode.offsetTop;
            const selectedMenuHeight = selectedMenuNode.offsetHeight;

            // 选中的菜单在非可视范围内，滚动到中间位置
            if (
                selectedMenuTop < innerScrollTop ||
                selectedMenuTop + selectedMenuHeight > innerScrollTop + innerHeight
            ) {
                this.inner.scrollTop =
                    selectedMenuTop - selectedMenuHeight - (innerHeight - selectedMenuHeight) / 2;
            }
        }, 300);
    };

    handleMenuOpenChange = openKeys => {
        const { sideCollapsed } = this.props;
        if (!sideCollapsed) this.props.action.menu.setOpenKeys(openKeys);
    };

    handleSideResizeStart = () => {
        this.props.action.side.setDragging(true);
    };

    handleSideResize = ({ clientX }) => {
        this.props.action.side.setWidth(clientX + 5);
    };

    handleSideResizeStop = () => {
        this.props.action.side.setDragging(false);
    };

    handleMaskClick = () => {
        this.props.action.side.setCollapsed(true);
    };

    render() {
        let {
            theme,
            layout,
            menus,
            openKeys,
            topMenu,
            selectedMenu,
            isMobile,
            showSide,
            sideCollapsed,
            sideCollapsedWidth,
            sideWidth,
            sideDragging,
            style
        } = this.props;
        let sideInnerWidth = sideWidth + scrollBarWidth;
        let outerOverFlow = sideCollapsed ? "visible" : "hidden";
        let innerOverFlow = sideCollapsed ? "visible" : "";
        let transitionDuration = sideDragging ? "0ms" : `300ms`;
        let isTopSideMenu = layout === PAGE_FRAME_LAYOUT.TOP_SIDE_MENU;
        let isSideMenu = layout === PAGE_FRAME_LAYOUT.SIDE_MENU;
        let hasSide = isTopSideMenu || isSideMenu;
        let sideMenus = menus;

        sideWidth = sideCollapsed ? sideCollapsedWidth : sideWidth;

        if (isTopSideMenu) sideMenus = topMenu && topMenu.children;
        if (isSideMenu) sideMenus = menus;

        return (
            <section
                styleName="mod-side"
                className={
                    (!isMobile && sideCollapsed) || (isMobile && !sideCollapsed)
                        ? "frame-side-collapsed"
                        : "frame-side-extended"
                }
                style={{
                    width: isMobile ? sideCollapsedWidth : sideWidth,
                    display: showSide ? "block" : "none",
                    ...style,
                    transition: "all " + transitionDuration
                }}
            >
                {hasSide && !isMobile && (
                    <>
                        <div className="frame-side-mask" onClick={this.handleMaskClick} />
                        {sideCollapsed ? null : (
                            <DragBar
                                styleName="drag-bar"
                                onDragStart={this.handleSideResizeStart}
                                onDragging={this.handleSideResize}
                                onDragEnd={this.handleSideResizeStop}
                            />
                        )}
                        <div styleName="outer" style={{ overflow: outerOverFlow }}>
                            <div
                                styleName="inner"
                                ref={node => (this.inner = node)}
                                style={{
                                    width: sideInnerWidth,
                                    overflow: innerOverFlow,
                                    transition: "all " + transitionDuration
                                }}
                            >
                                <Navigation
                                    theme={theme}
                                    dataSource={sideMenus}
                                    collapsed={sideCollapsed}
                                    openKeys={openKeys.map(item => `${item}`)}
                                    selectedKeys={[selectedMenu && selectedMenu.key]}
                                    onOpenChange={this.handleMenuOpenChange}
                                />
                            </div>
                        </div>
                    </>
                )}
            </section>
        );
    }
}
