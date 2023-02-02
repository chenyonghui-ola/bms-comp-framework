import React from "react";
import { message, Modal, Button, Table, Space } from "antd";
import { ExclamationCircleOutlined } from "@ant-design/icons";
import config from "src/commons/configHoc";
import Navigation from "./components/NewNavigation";
import PageContent from "src/components/page-content";
import { connect } from "src/models";
import { If, actionType, CompPageModal } from "./components";
import MenuModal from "./components/MenuModal";
import PageModal from "./components/PageModal";
import PointModal from "./components/PointModal";
import PubSub from "pubsub-js";
import Iconfont from "src/components/icon/iconfont";
import { getNodeByKey, getTopNodeByNode } from "src/library/utils/tree-utils";
import "./less/BasicConfigure.less";

@config({
    path: "/auth/modules/index",
    title: props => {
        return { text: "系统模块", icon: "home" };
    },
    router: true,
    ajax: true,
    query: true
})
@connect(state => {
    return {
        user: state.user,
        menus: state.menu.menus,
        openKeys: state.menu.openKeys
    };
})
export default class NewBasicConfig extends React.Component {
    state = {
        showMenu: false,
        showPoint: false,
        showPage: false,
        pointLoading: false,
        pointData: [],
        allPageData: [],
        pointModal: false,
        newMenus: this.props.menus
    };

    accordObjFindData = (obj, flag) => {
        const menus = [...this.state.newMenus];
        const translateNode = getNodeByKey(menus, obj.key);
        let currentObj = getNodeByKey(menus, obj.parentKey);
        const { children } = currentObj;
        let currentIndex = children.findIndex(item => item.key == translateNode.key);
        return [currentIndex, currentObj];
    };

    handleTranslate = (obj, type) => {
        const { newMenus: menus } = this.state;
        if (obj.type == "menu-1") {
            const currentIndex = menus.findIndex(item => item.key == obj.key);
            if (type == "up") {
                if (currentIndex == 0) {
                    message.info("已经是同级第一了");
                    return;
                }
                const arr = menus.splice(currentIndex, 1);
                menus.splice(currentIndex - 1, 0, arr[0]);
            } else {
                if (currentIndex == menus.length - 1) {
                    message.info("已经是同级末尾了");
                    return;
                }
                const arr = menus.splice(currentIndex, 1);
                menus.splice(currentIndex + 1, 0, arr[0]);
            }
            this.saveMenuTree(obj.key, type);
        } else {
            const [currentIndex, currentObj] = this.accordObjFindData(obj, type);
            if (currentObj.children.length == 1) {
                message.info("同级只有一个元素,无需移动");
                return;
            }
            if (type == "up") {
                if (currentIndex == 0) {
                    message.info("已经是同级第一了");
                    return;
                }
            } else {
                if (currentIndex == currentObj.children.length - 1) {
                    message.info("已经是同级末尾了");
                    return;
                }
            }
            this.saveMenuTree(obj.key, type);
        }
    };

    saveMenuTree = async (key, type) => {
        const { ajax } = this.props;
        await ajax.post("/api/menu/sort", { anchor_key: key, direction: type });
        this.reloadUserInfo();
    };

    handleRightMenuClick = async (obj = {}, flag) => {
        switch (flag) {
            case actionType.ADDMENUTOP:
                this.addDirection = "up";
                this.currentActionType = undefined;
                this.currentUpdateObj = undefined;
                this.currentAddBaseObj = obj;
                this.setState({ showMenu: true });
                break;
            case actionType.ADDMENUBOTTOM:
                this.addDirection = "down";
                this.currentAddBaseObj = obj;
                this.currentActionType = undefined;
                this.currentUpdateObj = undefined;
                this.setState({ showMenu: true });
                break;
            case actionType.ADDCHILDMENU:
                this.addDirection = undefined;
                this.currentAddBaseObj = obj;
                this.currentActionType = undefined;
                this.currentUpdateObj = undefined;
                this.setState({ showMenu: true });
                break;

            case actionType.TOP:
                this.handleTranslate(obj, "up");
                break;
            case actionType.ADDPAGETOP:
                this.handleChildPage(obj);
                this.addPageType = "brother-up";
                break;
            case actionType.ADDPAGEBOTTOM:
                this.handleChildPage(obj);
                this.addPageType = "brother-down";
                break;
            case actionType.ADDCHILDRENPAGE:
                this.addPageType = undefined;
                this.handleChildPage(obj);
                break;
            case actionType.BOTTOM:
                this.handleTranslate(obj, "down");
                break;
            case actionType.DELETE:
                Modal.confirm({
                    title: "提示",
                    icon: <ExclamationCircleOutlined />,
                    content: "确认删除吗？",
                    okText: "确认",
                    cancelText: "取消",
                    onOk: () => {
                        this.handleDelete(obj);
                    }
                });
                break;
            case actionType.UPDATE:
                const {
                    data: { sub_pages, controller, action, path, module_name }
                } = await this.props.ajax.get(
                    `/api/auth/modules/info?module_id=${obj.id}`
                );
                this.currentUpdateObj = {
                    ...obj,
                    module_name: obj.type == "page" ? path : module_name,
                    code: obj.key,
                    controller,
                    action,
                    sub_pages
                };
                this.currentActionType = "update";
                if (obj.type == "page") {
                    this.getPageDataByNode(obj);
                } else {
                    this.setState({ showMenu: true });
                }
                break;
            default:
                break;
        }
    };

    getPageDataByNode = async obj => {
        const { ajax } = this.props;
        const { newMenus: menus } = this.state;
        const topObj = getTopNodeByNode(menus, obj);
        const { data } = await ajax.get(`/api/auth/modules/search?page=${topObj.text}`);
        this.setState({
            allPageData: data.map(item => ({
                ...item,
                label: item.name,
                value: item.path
            })),
            showPage: true
        });
    };

    handleChildPage = async obj => {
        this.currentActionType = undefined;
        this.currentUpdateObj = undefined;
        this.getPageDataByNode(obj);
        this.currentAddChildPageBaseObj = obj;
    };

    handleDelete = async obj => {
        await this.props.ajax.post(`/api/auth/modules/remove`, {
            module_id: obj.id
        });
        message.success("删除成功");
        if (obj.type == "page") {
            this.setState({
                showPoint: false
            });
        }
        this.reloadUserInfo();
    };

    reloadUserInfo = () => {
        const {
            action: { menu, system },
            user
        } = this.props;
        PubSub.publish("refreshUserInfo");
        setTimeout(() => {
            menu.getMenus({
                params: user?.user_id
            });
        }, 500);
    };

    handleMenuActionCancle = () => {
        this.setState({ showMenu: false });
    };

    handleMenuAction = async data => {
        const { ajax } = this.props;
        try {
            if (this.currentActionType == "update") {
                await ajax.post("/api/auth/modules/modify", {
                    ...data,
                    type: "menu",
                    controller: this.currentUpdateObj.controller,
                    action: this.currentUpdateObj.action,
                    module_id: this.currentUpdateObj.id
                });
            } else {
                await ajax.post(`${"/api/auth/modules/create"}`, {
                    ...data,
                    type: "menu",
                    anchor_key: this.addDirection
                        ? this.currentAddBaseObj.key
                        : undefined,
                    direction: this.addDirection,
                    parent_key: this.addDirection
                        ? undefined
                        : this.currentAddBaseObj.key,
                    parent_module_id: this.addDirection
                        ? undefined
                        : this.currentAddBaseObj.key
                });
            }
            this.reloadUserInfo();
            this.setState({ showMenu: false });
        } catch (error) {
            console.log(error);
        }
    };

    handlePageActionCancle = () => {
        this.setState({ showPage: false });
    };

    handlePageAction = async data => {
        const { ajax } = this.props;
        try {
            if (this.currentActionType == "update") {
                console.log(this.currentUpdateObj);
                await ajax.post("/api/auth/modules/modify", {
                    ...data,
                    type: "page",
                    controller: this.currentUpdateObj.controller,
                    action: this.currentUpdateObj.action,
                    module_id: this.currentUpdateObj.id
                });
            } else {
                const extraParam = {};
                if (this.addPageType == "brother-up") {
                    extraParam["anchor_key"] = this.currentAddChildPageBaseObj.key;
                    extraParam["direction"] = "up";
                } else if (this.addPageType == "brother-down") {
                    extraParam["anchor_key"] = this.currentAddChildPageBaseObj.key;
                    extraParam["direction"] = "down";
                } else {
                    extraParam["parent_key"] = this.currentAddChildPageBaseObj.key;
                    extraParam["parent_module_id"] = this.currentAddChildPageBaseObj.key;
                }
                await ajax.post(`/api/auth/modules/create`, {
                    ...data,
                    type: "page",
                    ...extraParam
                });
            }
            this.setState({ showPage: false });
            this.reloadUserInfo();
        } catch (error) {
            console.log(error);
        }
    };

    handlePointActionCancle = () => {
        this.setState({ pointModal: false });
    };

    handlePointAction = async data => {
        const { ajax } = this.props;
        try {
            if (this.currentPointType == "update") {
                await ajax.post("/api/auth/modules/modify?type=point", {
                    ...data,
                    page_code: this.currentPageKey,
                    id: this.currentUpdatePointObj.id
                });
            } else {
                await ajax.post(`${"/api/menu/add?type=point"}`, {
                    ...data,
                    page_code: this.currentPageKey
                });
            }
            const { data: pointData } = await ajax.get(
                `/api/menu/index?type=point&code=${this.currentPageKey}`
            );
            this.setState({
                pointData,
                pointModal: false
            });
        } catch (error) {
            console.log(error);
        }
        // }
    };

    editPoint = obj => {
        this.currentPointType = "update";
        this.currentUpdatePointObj = obj;
        this.setState({
            pointModal: true
        });
    };

    deletePoint = async obj => {
        Modal.confirm({
            title: "提示",
            icon: <ExclamationCircleOutlined />,
            content: "确认删除吗？",
            okText: "确认",
            cancelText: "取消",
            onOk: async () => {
                const { ajax } = this.props;
                await ajax.post("/api/auth/modules/remove", { module_id: obj.module_id });
                const { data: pointData } = await ajax.get(
                    `/api/auth/modules/point?parent_module_id=${this.currentPageKey}`
                );
                this.setState({
                    pointData
                });
            }
        });
    };

    showPagePoint = async key => {
        this.currentPageKey = key;
        const { ajax } = this.props;
        try {
            this.currentPointType = "add";
            this.setState({
                pointLoading: true,
                showPoint: true
            });
            const { data: pointData } = await ajax.get(
                `/api/auth/modules/point?parent_module_id=${key}`
            );
            this.setState({
                pointLoading: false,
                pointData
            });
        } catch (error) {}
    };

    showPointModal = () => {
        this.currentUpdatePointObj = undefined;
        this.currentPointType = undefined;
        this.setState({
            pointModal: true
        });
    };

    render() {
        const columns = [
            { title: "ID", dataIndex: "module_id" },
            { title: "名称", dataIndex: "module_name" },
            { title: "控制器", dataIndex: "controller" },
            { title: "方法", dataIndex: "action" },
            { title: "描述", dataIndex: "description" },
            {
                title: "操作",
                dataIndex: "id",
                width: 100,
                render: (text, record) => (
                    <Space>
                        {/* <Iconfont
                            title="编辑"
                            type="icon-xiugai"
                            onClick={() => this.editPoint(record)}
                        /> */}
                        <Iconfont
                            title="删除"
                            type="icon-jinrongxianxingge-"
                            onClick={() => this.deletePoint(record)}
                        />
                    </Space>
                )
            }
        ];
        const {
            showMenu,
            showPoint,
            showPage,
            pointLoading,
            pointData,
            pointModal,
            allPageData,
            newMenus
        } = this.state;
        return (
            <PageContent>
                <div styleName="basicConfig-content">
                    <div styleName="basicConfig-navigation-container">
                        <Navigation
                            theme="light"
                            dataSource={this.props.menus}
                            // openKeys={this.props.openKeys}
                            handleRightMenuClick={this.handleRightMenuClick}
                            showPagePoint={this.showPagePoint}
                        />
                    </div>
                    <div styleName="basicConfig-line" />
                    <If data={showPoint}>
                        <div styleName="basicConfig-action">
                            <Table
                                rowKey={record => record.module_id}
                                columns={columns}
                                dataSource={pointData}
                                pagination={false}
                                loading={pointLoading}
                            />
                        </div>
                    </If>

                    <MenuModal
                        visible={showMenu}
                        initialValues={this.currentUpdateObj}
                        onOk={this.handleMenuAction}
                        onCancel={this.handleMenuActionCancle}
                    />
                    <PageModal
                        visible={showPage}
                        initialValues={this.currentUpdateObj}
                        onOk={this.handlePageAction}
                        onCancel={this.handlePageActionCancle}
                        type={this.currentActionType}
                        data={allPageData}
                    />
                    <PointModal
                        visible={pointModal}
                        initialValues={this.currentUpdatePointObj}
                        onOk={this.handlePointAction}
                        onCancel={this.handlePointActionCancle}
                    />
                </div>
            </PageContent>
        );
    }
}
