import React, { Component } from "react";
import { Helmet } from "react-helmet";
import { Input, Button, Form, message } from "antd";
import { UserOutlined, LockOutlined } from "@ant-design/icons";
import config from "src/commons/configHoc";
import Lange from "src/layouts/components/header/Lange";
import Banner from "./banner/index";
import cfg from "src/config";
import "./style.less";
const { baseName } = cfg;

@config({
    path: "/login",
    ajax: true,
    noFrame: true,
    noAuth: true
})
export default class extends Component {
    state = {
        loading: false,
        message: "",
        isMount: false
    };

    componentDidMount() {
        setTimeout(() => this.setState({ isMount: true }), 300);
    }

    handleSubmit = async values => {
        const { ajax } = this.props;
        if (this.state.loading) return;
        this.setState({ loading: true, message: "" });
        try {
            await ajax.post("/api/auth/login/index", values, { errorTip: false });
            window.location.href = `${baseName}/`;
        } catch (error) {
            this.setState({ message: error?.data?.msg || "用户名或密码错误！" });
        } finally {
            this.setState({ loading: false });
        }
    };
    // 企业微信登录
    qiyeWechatLogin = () => {
        window.location.href = "/api/auth/login/qwindex";
    };
    render() {
        const { loading, message, isMount } = this.state;
        const formItemStyleName = isMount ? "form-item active" : "form-item";
        return (
            <div styleName="root" className="login-bg">
                <Helmet title="欢迎登陆" />
                <div styleName="login-lange">
                    <Lange dark />
                </div>
                <div styleName="left">
                    <Banner />
                </div>
                <div styleName="right">
                    <div styleName="box">
                        <Form
                            ref={form => (this.form = form)}
                            name="login"
                            className="inputLine"
                            onFinish={this.handleSubmit}
                        >
                            <div styleName={formItemStyleName}>
                                <div styleName="header">欢迎登录</div>
                            </div>

                            <div styleName={formItemStyleName}>
                                <Form.Item
                                    name="username"
                                    rules={[
                                        { required: true, message: "请输入用户名或邮箱" }
                                    ]}
                                >
                                    <Input
                                        allowClear
                                        autoFocus
                                        prefix={
                                            <UserOutlined className="site-form-item-icon" />
                                        }
                                        placeholder="请输入用户名或邮箱"
                                    />
                                </Form.Item>
                            </div>
                            <div styleName={formItemStyleName}>
                                <Form.Item
                                    name="password"
                                    rules={[{ required: true, message: "请输入密码" }]}
                                >
                                    <Input.Password
                                        prefix={
                                            <LockOutlined className="site-form-item-icon" />
                                        }
                                        placeholder="请输入密码"
                                    />
                                </Form.Item>
                            </div>
                            <div styleName={formItemStyleName}>
                                <Form.Item
                                    name="repassword"
                                    rules={[{ required: true, message: "请输入验证码" }]}
                                >
                                    <Input
                                        prefix={
                                            <LockOutlined className="site-form-item-icon" />
                                        }
                                        placeholder="请输入验证码"
                                        autocomplete="off"
                                    />
                                </Form.Item>
                            </div>
                            <div styleName={formItemStyleName}>
                                <Form.Item
                                    shouldUpdate={true}
                                    style={{ marginBottom: 20 }}
                                >
                                    {() => (
                                        <Button
                                            styleName="submit-btn"
                                            loading={loading}
                                            type="primary"
                                            htmlType="submit"
                                            disabled={
                                                !this.form?.isFieldsTouched(true) ||
                                                this.form
                                                    ?.getFieldsError()
                                                    .filter(({ errors }) => errors.length)
                                                    .length
                                            }
                                        >
                                            登录
                                        </Button>
                                    )}
                                </Form.Item>
                                <Form.Item
                                    shouldUpdate={true}
                                    style={{ marginBottom: 0 }}
                                >
                                    {() => (
                                        <Button
                                            styleName="submit-btn"
                                            type="primary"
                                            onClick={this.qiyeWechatLogin}
                                        >
                                            企微/slack登录
                                        </Button>
                                    )}
                                </Form.Item>
                            </div>
                        </Form>
                        <div styleName="error-tip">{message}</div>
                    </div>
                </div>
            </div>
        );
    }
}
