import React, { Component, useMemo } from "react";
import config from "src/commons/configHoc";
import PageContent from "src/components/page-content";
import { Button } from "antd";
import {
    Designer, //设计器根组件，主要用于下发上下文
    DesignerToolsWidget, //画板工具挂件
    ViewToolsWidget, //视图切换工具挂件
    Workspace, //工作区组件，核心组件，用于管理工作区内的拖拽行为，树节点数据等等...
    OutlineTreeWidget, //大纲树组件，它会自动识别当前工作区，展示出工作区内树节点
    ResourceWidget, //拖拽源挂件
    HistoryWidget, //历史记录挂件
    StudioPanel, //主布局面板
    CompositePanel, //左侧组合布局面板
    WorkspacePanel, //工作区布局面板
    ToolbarPanel, //工具栏布局面板
    ViewportPanel, //视口布局面板
    ViewPanel, //视图布局面板
    SettingsPanel, //右侧配置表单布局面板
    ComponentTreeWidget //组件树渲染器
} from "@designable/react";
import { SettingsForm } from "@designable/react-settings-form";
import { createDesigner, GlobalRegistry, Shortcut, KeyCode } from "@designable/core";
import { LogoWidget, ActionsWidget, PreviewWidget, SchemaEditorWidget, MarkupSchemaWidget } from "./widgets";
import { saveSchema } from "./service";
import {
    Form,
    Field,
    Input,
    Select,
    TreeSelect,
    Cascader,
    Radio,
    Checkbox,
    Slider,
    Rate,
    NumberPicker,
    Transfer,
    Password,
    DatePicker,
    TimePicker,
    Upload,
    Switch,
    Text,
    Card,
    ArrayCards,
    ObjectContainer,
    ArrayTable,
    Space,
    FormTab,
    FormCollapse,
    FormLayout,
    FormGrid
} from "@designable/formily-antd/lib";
import ModalForm from "./components/ModalForm";
import { connect } from "src/models";
import { getUrlParams } from "./utils";
import { api_prefix } from "./Common";
import { http } from "src/library/ajax";
import { noNeedDisplayLabel } from "./help/constant";
import "./LowCode.css";
import "./Empty.css";
import "./Setting.css";
import "antd/dist/antd.less";

GlobalRegistry.registerDesignerLocales({
    "zh-CN": {
        sources: {
            Inputs: "输入控件",
            Layouts: "布局组件",
            Arrays: "自增组件",
            Displays: "展示组件"
        }
    },
    "en-US": {
        sources: {
            Inputs: "Inputs",
            Layouts: "Layouts",
            Arrays: "Arrays",
            Displays: "Displays"
        }
    }
});

const App = ({ action, schema }) => {
    const engine = useMemo(
        () =>
            createDesigner({
                shortcuts: [
                    new Shortcut({
                        codes: [
                            [KeyCode.Meta, KeyCode.S],
                            [KeyCode.Control, KeyCode.S]
                        ],
                        handler(ctx) {
                            saveSchema(ctx.engine);
                        }
                    })
                ],
                rootComponentName: "Form"
            }),
        []
    );
    return (
        <Designer engine={engine}>
            <StudioPanel actions={<ActionsWidget action={action} schema={schema} />}>
                <CompositePanel>
                    <CompositePanel.Item title="panels.Component" icon="Component">
                        <ResourceWidget
                            title="sources.Inputs"
                            sources={[
                                Input,
                                // Password,
                                NumberPicker,
                                // Rate,
                                // Slider,
                                Select,
                                // TreeSelect,
                                // Cascader,
                                // Transfer,
                                Checkbox,
                                Radio,
                                DatePicker,
                                // TimePicker,
                                Upload
                                // Switch,
                                // ObjectContainer
                            ]}
                        />
                        <ResourceWidget
                            title="sources.Layouts"
                            sources={[
                                Card
                                // FormGrid,
                                // FormTab,
                                // FormLayout,
                                // FormCollapse,
                                // Space
                            ]}
                        />
                        <ResourceWidget
                            title="sources.Arrays"
                            sources={
                                [
                                    // ArrayCards, ArrayTable
                                ]
                            }
                        />
                        <ResourceWidget
                            title="sources.Displays"
                            sources={
                                [
                                    // Text
                                ]
                            }
                        />
                    </CompositePanel.Item>
                    <CompositePanel.Item title="panels.OutlinedTree" icon="Outline">
                        <OutlineTreeWidget />
                    </CompositePanel.Item>
                    <CompositePanel.Item title="panels.History" icon="History">
                        <HistoryWidget />
                    </CompositePanel.Item>
                </CompositePanel>
                <Workspace id="form">
                    <WorkspacePanel>
                        <ToolbarPanel>
                            <DesignerToolsWidget />
                            <ViewToolsWidget use={["DESIGNABLE", "JSONTREE", "MARKUP", "PREVIEW"]} />
                        </ToolbarPanel>
                        <ViewportPanel>
                            <ViewPanel type="DESIGNABLE">
                                {() => (
                                    <ComponentTreeWidget
                                        components={{
                                            Form,
                                            Field,
                                            Input,
                                            Select,
                                            // TreeSelect,
                                            // Cascader,
                                            Radio,
                                            Checkbox,
                                            // Slider,
                                            // Rate,
                                            NumberPicker,
                                            // Transfer,
                                            // Password,
                                            DatePicker,
                                            // TimePicker,
                                            Upload,
                                            // Switch,
                                            Text,
                                            Card,
                                            ArrayCards,
                                            ArrayTable,
                                            Space,
                                            FormTab,
                                            FormCollapse,
                                            FormGrid,
                                            FormLayout
                                            // ObjectContainer
                                        }}
                                    />
                                )}
                            </ViewPanel>
                            <ViewPanel type="JSONTREE" scrollable={false}>
                                {(tree, onChange) => <SchemaEditorWidget tree={tree} onChange={onChange} />}
                            </ViewPanel>
                            <ViewPanel type="MARKUP" scrollable={false}>
                                {tree => <MarkupSchemaWidget tree={tree} />}
                            </ViewPanel>
                            <ViewPanel type="PREVIEW">{tree => <PreviewWidget tree={tree} />}</ViewPanel>
                        </ViewportPanel>
                    </WorkspacePanel>
                </Workspace>
                <SettingsPanel title="panels.PropertySettings">
                    <SettingsForm uploadAction="https://www.mocky.io/v2/5cc8019d300000980a055e76" />
                </SettingsPanel>
            </StudioPanel>
        </Designer>
    );
};
@config({ path: "/lesscode/form/main", title: "表单设计器" })
@connect(({ user }) => ({ user }))
export default class LowCode extends Component {
    state = {
        modal: !getUrlParams("guid"),
        // modal: false,
        schema: "",
        options: [
            { label: "模块", type: "select", name: "parent_id" },
            { label: "标识", type: "input", name: "guid" },
            {
                label: "Model",
                type: "input",
                name: "model",
                required: false,
                placeholder: "带命名空间模型：\\Imee\\Models\\Starifyapp\\SyShowMusic"
            }
            //{ label: "表名", type: "input", name: "table_name", required:false, placeholder:"表名称，例：cms.lesscode_test，不写数据库默认bms数据库"},
            // { label: "开启功能", type: "checkbox", name: "points", required:false, options:[{label:"是", value:1}, {label:"否", value:0}]},
        ]
    };

    componentDidMount() {
        const { menus = [] } = this.props.user;
        const { options } = this.state;
        const valueArr = [menus.map(item => ({ label: item.name, value: item.id }))];
        const newOptions = options.map((item, index) => ({
            ...item,
            options: valueArr[index]
        }));
        this.setState({ options: newOptions });
        if (getUrlParams("guid")) {
            console.log(getUrlParams("guid"));
            this.getSchemaConfig();
        } else {
            this.getDefaultSchemaConfig();
        }
        setTimeout(() => {
            this.handleStyle();
            const formDom = document.querySelector(".dn-settings-form-content");
            let observer = new MutationObserver(() => this.handleStyle());
            observer.observe(formDom, { childList: true });
        }, 0);
    }

    handleStyle = () => {
        const doms = document.getElementsByClassName("ant-formily-item");
        for (let index = 0; index < doms.length; index++) {
            const dom = doms[index];
            noNeedDisplayLabel.forEach(item => {
                if (dom.innerHTML.includes(item)) {
                    dom.style.display = "none";
                }
            });
        }
    };

    getSchemaConfig = async () => {
        const { data } = await http.post(`/${api_prefix}/lesscode/index/schemaConfig`, {
            guid: getUrlParams("guid")
        });
        this.setState({ schema: data });
    };

    getDefaultSchemaConfig = () => {
        const data = {
            "form": { "labelCol": 6, "wrapperCol": 12 },
            "schema": {
                "type": "object",
                "properties": {
                    "km3xhe7xwm1": {
                        "type": "void",
                        "x-component": "Card",
                        "x-component-props": { "title": "填写功能名称（拖动组件都要在此卡片内）" },
                        "x-designable-id": "km3xhe7xwm1",
                        "properties": {
                            "id|pk": {
                                "type": "number",
                                "title": "主键字段",
                                "x-decorator": "FormItem",
                                "x-component": "NumberPicker",
                                "x-validator": [],
                                "x-component-props": {},
                                "x-decorator-props": {},
                                "name": "id|pk",
                                "x-designable-id": "f9sfcragopq",
                                "x-index": 0
                            }
                        },
                        "x-index": 0
                    }
                },
                "x-designable-id": "2sp3prg702q"
            }
        };
        this.setState({ schema: data });
    };

    handleOnModalOk = async okData => {
        const { data } = await http.post(`/${api_prefix}/lesscode/form/check`, okData);
        localStorage.setItem("formDesignData", JSON.stringify(okData));

        if (data.schema) {
            this.setState({ modal: false, schema: JSON.parse(data.schema) });
        } else {
            this.setState({ modal: false });
        }
    };

    handleOnModalCancel = () => {
        this.props.action.system.closeCurrentTab();
    };

    render() {
        const { modal, options, schema } = this.state;
        return (
            <PageContent>
                <App action={this.props.action} schema={schema} />
                <ModalForm visible={modal} options={options} onOk={this.handleOnModalOk} onCancel={this.handleOnModalCancel} />
            </PageContent>
        );
    }
}
