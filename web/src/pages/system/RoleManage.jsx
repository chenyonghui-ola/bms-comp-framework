import React, { Component } from "react";
import config from "src/commons/configHoc";
import PageContent from "src/components/page-content";
import { Button, Space, Modal } from "antd";
import CompGaiaTable from "../../components/gaia-table";
import { PlusOutlined } from "@ant-design/icons";
import Iconfont from "src/components/icon/iconfont";
import "./less/RoleMange.less";

@config({
    path: "/auth/role/index",
    title: { text: "角色管理", icon: "home" },
    router: true,
    ajax: true,
    query: true
})
export default class RoleManage extends Component {
    state = {
        tableData: [],
        loading: false
    };

    componentDidMount() {
        this.queryRoles();
    }

    queryRoles = async () => {
        this.setState({ loading: true });
        const { data = [] } = await this.props.ajax.get("/api/auth/role/all");
        this.setState({ tableData: data, loading: false });
    };

    genTableAtcionVDom = (text, record) => {
        return (
            <Space>
                <Iconfont
                    title="编辑"
                    type="icon-xiugai"
                    onClick={() => this.props.history.push(`/auth/roleDetail?id=${record.role_id}`)}
                />
            </Space>
        );
    };

    render() {
        const { tableData, loading } = this.state;
        const columns = [
            { title: "角色ID", dataIndex: "role_id" },
            { title: "角色名称", dataIndex: "role_name" },
            { title: "操作时间", dataIndex: "modify_time" },
            {
                title: "操作",
                dataIndex: "id",
                width: 100,
                render: this.genTableAtcionVDom
            }
        ];
        return (
            <PageContent styleName="page">
                <div styleName="page-module-blank" />
                <CompGaiaTable columns={columns} dataSource={tableData} loading={loading}>
                    <Button
                        type="primary"
                        icon={<PlusOutlined />}
                        onClick={() => this.props.history.push("/auth/roleDetail")}
                    >
                        新建
                    </Button>
                </CompGaiaTable>
            </PageContent>
        );
    }
}
