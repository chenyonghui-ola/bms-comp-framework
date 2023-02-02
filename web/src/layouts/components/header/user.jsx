import React, { Component, useState } from "react";
import Icon from "src/components/icon";
import { Menu, Dropdown, Space, Modal, Empty, Input } from "antd";
import { toLogin } from "src/commons/loginFn";
import config from "src/commons/configHoc";
import { connect } from "src/models";
import emptySvg from "src/style/image/empty.svg";
import Lange from "./Lange";
import "./style.less";
@config({ ajax: true, router: true })
@connect(state => {
    const loginUser = state.user;
    const { language } = state.system;
    return {
        loginUser,
        language
    };
})
export default class HeaderUser extends Component {
    static defaultProps = {
        theme: "default"
    };

    handleMenuClick = ({ key }) => {
        if (key === "logout") this.props.ajax.get("/api/auth/login/logout").then(toLogin);
        if (key === "modifyPassword") this.setState({ passwordVisible: true });
    };

    render() {
        const { theme, loginUser = {} /*language*/ } = this.props;
        const menu = (
            <Menu theme={theme} selectedKeys={[]} onClick={this.handleMenuClick}>
                <Menu.Item key="logout">
                    <Icon type="logout" />
                    退出登录
                </Menu.Item>
            </Menu>
        );

        return (
            <Space styleName="comp-header-userinfo">
                <Lange />
                <Dropdown
                    trigger="click"
                    overlay={menu}
                    placement="bottomRight"
                    getPopupContainer={() => this.userMenu || document.body}
                >
                    <span styleName="chu-user">
                        <span>{loginUser.user_name}</span>
                        <Icon type="caretDown" />
                    </span>
                </Dropdown>
            </Space>
        );
    }
}
