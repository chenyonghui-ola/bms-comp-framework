import React from "react";
import { message, Button, Table, Input } from "antd";
import config from "src/commons/configHoc";
import Navigation from "./components/RoleNavigation";
import PageContent from "src/components/page-content";
import GaiaForm from "src/components/gaia-form";
import { connect } from "src/models";
import { updateNode, getNodeByKey } from "src/library/utils/tree-utils";
import { If } from "./components";
import "./less/BasicConfigure.less";

@config({
    path: "/auth/roleDetail",
    title: props => {
        const { id } = props.query;
        return { text: id ? "编辑角色" : "创建角色", icon: "home" };
    },
    breadcrumbs: props => {
        const { id } = props.query;
        return [
            { key: "roleManage", text: "角色管理" },
            {
                key: "roleDetal",
                text: id ? "编辑角色" : "创建角色"
            }
        ];
    },
    ajax: true,
    query: true
})
@connect(state => ({
    user: state.user,
    menus: state.menu.menus,
    openKeys: state.menu.openKeys
}))
export default class NewRoleDetail extends React.Component {
    state = {
        showPoint: false,
        pointLoading: false,
        pointData: [],
        selectedRowKeys: [],
        roleName: "",
        roleExplain: "",
        menus: this.props.menus,
        openKeys: []
    };
    selectedRowKeysTotal = {};

    treeToArray(tree) {
        let arr = [];
        const expanded = datas => {
            if (datas && datas.length > 0) {
                datas.forEach(e => {
                    arr.push(e);
                    expanded(e.children);
                });
            }
        };
        expanded(tree);
        return arr;
    }

    async componentDidMount() {
        const { menus } = this.state;
        const {
            query: { id },
            ajax
        } = this.props;
        const newMenus = this.formatData(menus);
        const nodes = this.treeToArray(newMenus);
        const subpages = nodes.filter(
            item => item.type == "menu-1" || item.type == "menu-2" || item.type == "menu-3"
        );
        const defaultOpenkeys = subpages.map(item => `${item.key}`);
        if (id) {
            this.getInitData(ajax, id, newMenus, defaultOpenkeys);
        } else {
            this.setState({
                menus: newMenus,
                openKeys: defaultOpenkeys
            });
        }
    }

    getInitData = async (ajax, id, menus, defaultOpenkeys) => {
        const copyMenus = [...menus];
        const {
            data: { role_name, menus: selectedMenus = [], pages = [], points = [] }
        } = await ajax.get(`/api/auth/role/info?role_id=${id}`);
        selectedMenus.map(item => {
            const node = getNodeByKey(copyMenus, item.code);
            const { children } = node??{};
            const newChild =
                children &&
                children.map(item => ({
                    ...item,
                    checkDisabled: false
                }));
            updateNode(copyMenus, {
                ...node,
                checked: true,
                checkDisabled: false,
                children: newChild
            });
        });
        pages.map(item => {
            const node = getNodeByKey(copyMenus, item.code);
            updateNode(copyMenus, { ...node, checked: true, checkDisabled: false });
            this.selectedRowKeysTotal[item.code] = points
                .filter(pitem => pitem.page_id == item.id)
                .map(citem => citem.id);
        });
        this.setState({
            roleName: role_name,
            menus: copyMenus,
            openKeys: defaultOpenkeys
        });
    };

    formatData = data => {
        return data.map(item => {
            let { children, type } = item;
            if (children && children.length > 0) {
                children = this.formatData(children);
            }
            return {
                ...item,
                children,
                checked: false,
                checkDisabled: type == "menu-1" ? false : true
            };
        });
    };

    showPagePoint = async key => {
        this.currentPageKey = key;
        const currentPageSelectPoints = this.selectedRowKeysTotal[key] || [];
        const { ajax } = this.props;
        try {
            this.setState({
                pointLoading: true,
                showPoint: true
            });
            const { data: pointData } = await ajax.get(
                `/api/auth/modules/point?parent_module_id=${key}`
            );
            this.setState({
                pointLoading: false,
                pointData,
                selectedRowKeys: currentPageSelectPoints
            });
        } catch (error) {}
    };

    onSelectChange = selectedRowKeys => {
        this.setState({ selectedRowKeys });
        this.selectedRowKeysTotal[this.currentPageKey] = selectedRowKeys;
    };

    handleCheckBoxChange = (checked, obj) => {
        const { menus } = this.state;
        let copyMenus = menus;
        const node = getNodeByKey(copyMenus, obj.key);
        const { children } = node;
        let newChild = children;
        if (node.type != "page") {
            newChild =
                children &&
                children.map(item => ({
                    ...item,
                    checkDisabled: !checked
                }));
        }
        updateNode(copyMenus, { ...node, children: newChild, checked });
        this.setState({
            menus: copyMenus
        });
    };

    submitRoleData = async () => {
        const {
            ajax,
            query: { id }
        } = this.props;
        const { menus, roleName } = this.state;
        if (!roleName) {
            message.error("请先输入角色名称");
            return;
        }
        const point = Object.values(this.selectedRowKeysTotal).reduce(
            (value, currentValue) => value.concat(currentValue),
            []
        );
        if (!id) {
            await ajax.post("/api/auth/role/create", {
                role_name: roleName,
                tree: JSON.stringify(menus),
                module_ids: point
            });
        } else {
            await ajax.post("/api/auth/role/modify", {
                role_name: roleName,
                tree: JSON.stringify(menus),
                module_ids: point,
                role_id: id
            });
        }

        message.success("提交成功");
        setTimeout(() => {
            this.props.action.system.closeCurrentTab();
        }, 1000);
    };

    render() {
        const columns = [
            { title: "ID", dataIndex: "module_id" },
            { title: "名称", dataIndex: "module_name" },
            { title: "控制器", dataIndex: "controller" },
            { title: "方法", dataIndex: "action" },
            { title: "描述", dataIndex: "description" }
        ];
        const {
            showPoint,
            pointLoading,
            pointData,
            selectedRowKeys,
            menus,
            roleName,
            roleExplain
        } = this.state;
        const rowSelection = {
            selectedRowKeys,
            onChange: this.onSelectChange
        };
        return (
            <PageContent>
                <div styleName="roleDetail-content">
                    <GaiaForm title="基础信息" key="card1" ref={this.baseInfoForm}>
                        <GaiaForm.Item
                            key={1}
                            label="角色名称"
                            rules={{ required: true, message: "Please Input" }}
                        >
                            <Input x-model={roleName} style={{ width: 240 }} />
                        </GaiaForm.Item>
                        {/* <GaiaForm.Item
                            key={2}
                            label="角色说明"
                            rules={{ required: true, message: "Please Input" }}
                        >
                            <Input x-model={roleExplain} />
                        </GaiaForm.Item> */}
                    </GaiaForm>
                    <div styleName="empty-block" />
                    <div styleName="newRole-detail">
                        <div style={{ width: 240, flexShrink: 0 }}>
                            <Navigation
                                theme="light"
                                dataSource={menus}
                                openKeys={this.state.openKeys}
                                showPagePoint={this.showPagePoint}
                                handleCheckBoxChange={this.handleCheckBoxChange}
                            />
                        </div>
                        <div styleName="basicConfig-line" />
                        <If data={showPoint}>
                            <div styleName="basicConfig-action">
                                <Table
                                    rowKey={record => record.module_id}
                                    rowSelection={rowSelection}
                                    columns={columns}
                                    dataSource={pointData}
                                    loading={pointLoading}
                                    pagination={false}
                                />
                                <div
                                    style={{
                                        display: "flex",
                                        justifyContent: "center",
                                        marginTop: 72
                                    }}
                                >
                                    <Button
                                        style={{ marginRight: 48 }}
                                        onClick={() => this.props.action.system.closeCurrentTab()}
                                    >
                                        取消
                                    </Button>
                                    <Button type="primary" onClick={this.submitRoleData}>
                                        确定
                                    </Button>
                                </div>
                            </div>
                        </If>
                    </div>
                </div>
            </PageContent>
        );
    }
}
