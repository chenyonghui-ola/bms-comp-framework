import React, { Component } from "react";
import config from "src/commons/configHoc";
import PageContent from "src/components/page-content";
import { Input, Select, Form, Table, Space, Button, message } from "antd";
import "./less/UserDetail.less";

const layout = {
    labelCol: { span: 4 },
    wrapperCol: { span: 18 }
};

@config({
    path: "/user/detail",
    title: props => {
        let _text = "新增用户";
        if (props.query?.user_id) _text = "编辑用户";
        return { text: _text, icon: "home" };
    },

    ajax: true,
    query: true
})
export default class Detail extends Component {
    constructor(props) {
        super(props);
        this.formRef = React.createRef(undefined);
        this.state = {
            roleData: [],
            tableData: [],
            app: [],
            system: []
        };
    }
    pageData = [];
    pointData = [];

    componentDidMount() {
        this.getData();
    }
    findMenuName = (menus, id) => {
        let str = "";
        const menuObj = menus.find(item => item.id == id);
        if (menuObj && menuObj.parent_id > 0) {
            str = menuObj.name;
            const secMenuObj = menus.find(sitem => sitem.id == menuObj.parent_id);
            return `${secMenuObj?.name}-${str}`;
        } else {
            return menuObj && menuObj.name;
        }
    };

    getData = async () => {
        const {
            ajax,
            query: { user_id }
        } = this.props;
        let data = undefined;
        if (user_id) {
            const { data: userData } = await ajax.get(
                `/api/auth/staff/info?user_id=${user_id}`
            );
            data = userData;
            this.formRef.current.setFieldsValue({
                ...userData,
                user_status: `${userData.user_status}`
            });
        }
        const { data: roleData } = await ajax.get("/api/auth/role/all");
        const {
            data: { app, system, language, bigarea }
        } = await ajax.get("/api/auth/staff/index?c=config");

        const temRoleData = roleData.map(item => {
            if ((data ? data.role_ids : []).includes(item.role_id)) {
                return {
                    ...item,
                    label: item.role_name,
                    value: item.role_id,
                    checked: true
                };
            } else {
                return {
                    ...item,
                    label: item.role_name,
                    value: item.role_id,
                    checked: false
                };
            }
        });

        this.setState({
            roleData: temRoleData,
            app,
            system,
            language,
            bigarea
        });
    };

    filterArr = arr => {
        let newArr = [];
        let arrId = [];
        for (let item of arr) {
            if (arrId.indexOf(item["id"]) == -1) {
                arrId.push(item["id"]);
                newArr.push(item);
            }
        }
        return newArr;
    };

    handleRoleSelectChange = value => {
        const { roleData } = this.state;
        const { ajax } = this.props;
        this.setState({
            roleData: roleData.map(item => ({
                ...item,
                checked: value.includes(item.id)
            }))
        });
        this.pageData = [];
        this.pointData = [];
        if (value.length == 0) {
            this.pageData = [];
            this.pointData = [];
            this.setState({
                tableData: []
            });
            return;
        }
    };

    handleSubmit = async () => {
        const {
            query: { user_id },
            ajax
        } = this.props;
        const result = await this.formRef.current.validateFields();
        if (user_id) {
            await ajax.post("/api/auth/staff/modify", result);
            message.success("编辑成功");
        } else {
            await ajax.post("/api/auth/staff/create", result);
            message.success("新增成功");
        }
        setTimeout(() => {
            this.props.action.system.closeCurrentTab();
        }, 1000);
    };

    render() {
        const { roleData, tableData, app, system, bigarea, language } = this.state;
        const {
            ajax,
            query: { user_id }
        } = this.props;
        return (
            <PageContent styleName="fms-user-detail">
                <div styleName="form-content">
                    <Form {...layout} ref={this.formRef}>
                        <Form.Item label="用户id" name="user_id" hidden>
                            <Input placeholder="" />
                        </Form.Item>
                        <Form.Item
                            label="用户名"
                            name="user_name"
                            rules={[{ required: true, message: "请输入" }]}
                        >
                            <Input placeholder="" />
                        </Form.Item>
                        <Form.Item
                            label="用户密码"
                            name="password"
                            rules={[{ required: !user_id, message: "请输入" }]}
                        >
                            <Input.Password placeholder="" />
                        </Form.Item>
                        <Form.Item
                            label="邮箱"
                            name="user_email"
                            rules={[{ required: true, message: "请选择" }]}
                        >
                            <Input />
                        </Form.Item>
                        <Form.Item
                            label="状态"
                            name="user_status"
                            rules={[{ required: true, message: "请选择" }]}
                        >
                            <Select
                                options={[
                                    { label: "启用", value: "1" },
                                    { label: "禁用", value: "0" }
                                ]}
                            />
                        </Form.Item>
                        <Form.Item
                            label="角色"
                            name="role_ids"
                            rules={[{ required: true }]}
                        >
                            <Select
                                allowClear={true}
                                placeholder="请选择"
                                onChange={this.handleRoleSelectChange}
                                mode="multiple"
                                options={roleData}
                                optionFilterProp="children"
                                filterOption={(inputValue, option) =>
                                    option.label
                                        .toLowerCase()
                                        .indexOf(inputValue.toLowerCase()) !== -1
                                }
                            />
                        </Form.Item>

                        <Form.Item
                            label="语言"
                            name="language"
                            // rules={[{ required: true }]}
                        >
                            <Select
                                allowClear={true}
                                placeholder="请选择"
                                // onChange={this.handleRoleSelectChange}
                                mode="multiple"
                                options={language}
                                optionFilterProp="children"
                                filterOption={(inputValue, option) =>
                                    option.label
                                        .toLowerCase()
                                        .indexOf(inputValue.toLowerCase()) !== -1
                                }
                            />
                        </Form.Item>

                        <Form.Item
                            label="大区"
                            name="bigarea"
                            // rules={[{ required: true }]}
                        >
                            <Select
                                allowClear={true}
                                placeholder="请选择"
                                // onChange={this.handleRoleSelectChange}
                                mode="multiple"
                                options={bigarea}
                                optionFilterProp="children"
                                filterOption={(inputValue, option) =>
                                    option.label
                                        .toLowerCase()
                                        .indexOf(inputValue.toLowerCase()) !== -1
                                }
                            />
                        </Form.Item>
                    </Form>
                </div>
                <div styleName="line" />
                <div styleName="bottom-button">
                    <Space align="center" size={40}>
                        <Button
                            onClick={() => this.props.action.system.closeCurrentTab()}
                            style={{ width: 120, height: 40 }}
                        >
                            取消
                        </Button>
                        <Button
                            type="primary"
                            onClick={this.handleSubmit}
                            style={{ width: 120, height: 40 }}
                        >
                            提交
                        </Button>
                    </Space>
                </div>
            </PageContent>
        );
    }
}
