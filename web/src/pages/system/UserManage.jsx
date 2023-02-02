import React from "react";
import { Button, message, Modal, Space, Tooltip } from "antd";
import config from "src/commons/configHoc";
import CompGaiaTable from "src/components/gaia-table";
import CompGaiaFilter from "src/components/gaia-filter";
import PageContent from "src/components/page-content";
import Iconfont from "src/components/icon/iconfont";
import { columns, fieldData } from "./help/index";
import { connect } from "src/models";
import { formatStr } from "src/library/utils";
import { PlusOutlined } from "@ant-design/icons";
import cfg from "src/config/config.prod";

@config({
    path: "/auth/staff/index",
    title: props => ({ text: "用户管理" }),
    ajax: true
})
@connect(({ user }) => ({ user }))
export default class List extends React.Component {
    state = {
        data: [],
        total: 0,
        current: 1,
        loading: false,
        fieldData
    };
    param = {
        page: 1,
        limit: 15
    };

    componentDidMount() {
        this.getData();
        this.getConfig();
    }

    getConfig = async () => {
        const { ajax } = this.props;
        const { data: configData } = await ajax.get("/api/auth/staff/index?c=config");
        const { user_status, is_salt } = configData;
        const valueArr = { user_status, is_salt };
        const newFieldData = fieldData.map(item => ({
            ...item,
            options: valueArr[item.name]
        }));
        this.setState({
            fieldData: newFieldData
        });
    };

    getData = async () => {
        const { ajax } = this.props;
        this.setState({
            loading: true
        });
        const { data, total } = await ajax.get(`/api/auth/staff/index`, this.param);
        this.setState({
            data,
            total,
            loading: false
        });
    };

    handleOnPageChange = (page, pageSize) => {
        this.param.page = page;
        this.param.limit = pageSize;
        this.setState({ current: page });
        this.getData();
    };

    search = data => {
        this.param.page = 1;
        this.param = { ...this.param, ...data };
        this.setState({ current: 1 });
        this.getData();
    };

    render() {
        const { data, total, loading, fieldData, current } = this.state;
        const { permission } = this.props.user;
        const finalColumns = [
            ...columns,
            {
                title: "操作",
                dataIndex: "user_id",
                width: 125,
                render: (text, record) => (
                    <Space>
                        <Iconfont
                            title="编辑"
                            type="icon-xiugai"
                            onClick={() =>
                                this.props.history.push("/user/detail?user_id=" + text)
                            }
                        />
                        <a
                            href={`${cfg.ajaxPrefix}/api/auth/staff/showSalt?user_id=${text}`}
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            <Iconfont title="二次验证" type="icon-mingxi" />
                        </a>
                    </Space>
                )
            }
        ];
        return (
            <PageContent>
                <div>
                    <CompGaiaFilter fileds={fieldData} onSubmit={this.search} />
                    <div style={{ height: 10, backgroundColor: "#F2F3F6" }} />
                    <CompGaiaTable
                        columns={finalColumns}
                        loading={loading}
                        dataSource={data}
                        pagination={{
                            total,
                            current,
                            showTotal: total => `共 ${total} 条`,
                            onChange: this.handleOnPageChange
                        }}
                    >
                        <Button
                            type="primary"
                            icon={<PlusOutlined />}
                            onClick={() => this.props.history.push("/user/detail")}
                        >
                            新建
                        </Button>
                    </CompGaiaTable>
                </div>
            </PageContent>
        );
    }
}
