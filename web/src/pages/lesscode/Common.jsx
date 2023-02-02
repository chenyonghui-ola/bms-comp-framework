import React from "react";
import config from "src/commons/configHoc";
import { Modal, Button, Space, message, Image as AImage } from "antd";
import PageContent from "src/components/page-content";
import PubSub from "pubsub-js";
import CompGaiaTable from "./components/Table";
import CompGaiaFilter from "./components/Filter";
import ModalForm from "./components/ModalForm";
import moment from "moment";
import Iconfont from "./components/Iconfont";
import { If } from "./components";
import _ from "lodash";
import { filterObj, getUrlParams } from "./utils";
import { http } from "src/library/ajax";
import cfg from "src/config";
import { modalConfig } from "../Modal";
import { patchConfig } from "../patch";
import { lesscodeModalConfig } from "./modal";
import { imgConfig } from "../img";
import {
    cacheFilter,
    clearFilter,
    formatFilterData,
    generateFilterData,
    clipboardCopy,
    getMomentFormatType,
    cacheInitColumns
} from "./help";
const defaultModalPlaceholder = () => <div />;
const ModalPlaceholder = defaultModalPlaceholder;
const finalModalConfig = [...modalConfig, ...lesscodeModalConfig];

export const api_prefix = process.env.APP_API_PREFIX || "api";
const common_upload_url = `/${api_prefix}/common/upload/file`;

@config({ path: "/lesscode/common" })
class Common extends React.Component {
    state = {
        hasFilter: false,
        fieldData: [],
        columns: [],
        current: 1,
        total: 0,
        modal: false,
        manMadeModal: false,
        extra_operate_modal: false,
        extra_operate_modal_footer_hidden: false,
        modalDom: ModalPlaceholder,
        patchDom: undefined,
        pageSize: 15
    };
    param = {
        page: 1,
        limit: 15
    };
    selectedKeys = []; // 选中的数据key
    action = []; // table 上方的按钮
    extra = {}; // 额外的信息
    own_point = []; // 功能点，控制操作按钮的显示与隐藏
    special_list_field_collect = []; //  列表特殊字段的收集器
    extra_operate = []; // 列表操作里面除了编辑和删除外的其他操作
    isFirstFetch = true; // 判断是否是首次请求getData
    guid = getUrlParams("guid");
    current_search = window.location.search;

    constructor(props) {
        super(props);
        this.ref_filter = React.createRef(undefined);
    }

    componentDidMount() {
        this.getConfig();
    }

    getConfig = async () => {
        const {
            data: { list, cache, selectColumns, patch, filter = [], point = [], operate = [], extra = {}, action = [] }
        } = await http.post(`/${api_prefix}/lesscode/index/listConfig${this.current_search}`, {
            guid: this.guid
        });
        this.cache = cache;
        this.selectColumns = selectColumns;
        this.fields_list = list;
        this.filter_list = filter;
        this.handleFilter(filter, cache);
        this.handleList(list);
        this.handlePatch(patch || {});
        this.own_point = point;
        this.extra_operate = operate;
        this.extra = extra;
        this.action = action;
        this.getData();
    };

    getData = async () => {
        const temValues = this.ref_filter.current ? _.cloneDeep(this.ref_filter.current.getCurrentValues()) : {};
        const initFieldsValues = formatFilterData(temValues ? filterObj(temValues) : {}, this.filter_list);
        const cacheObj = JSON.parse(localStorage.getItem(this.guid) || "{}");
        let cache_extra_param = {};
        if (this.cache && this.isFirstFetch) cache_extra_param = { page: cacheObj.page, limit: cacheObj.limit };
        const finalParam = {
            guid: this.guid,
            ...this.param,
            ...initFieldsValues,
            ...cache_extra_param
        };
        this.setState({ loading: true });
        const { data, total } = await http.post(`/${api_prefix}/lesscode/index/list${this.current_search}`, finalParam);
        cacheFilter(finalParam);
        this.setState({
            data,
            total,
            loading: false,
            current: finalParam.page,
            pageSize: finalParam.limit
        });
        this.isFirstFetch = false;
    };

    handlePatch = ({ id: patchId }) => {
        const targetDom = patchConfig?.find(item => item.id == patchId)?.patch;
        this.setState({ patchDom: targetDom });
    };

    formatKeyWord = (obj = {}) => {
        const final_obj = {};
        if (Array.isArray(this.keyWord)) {
            this.keyWord.forEach(item => {
                final_obj[item] = obj[item];
            });
        } else {
            final_obj[this.keyWord] = obj[this.keyWord];
        }
        return final_obj;
    };

    deleteItem = obj => {
        Modal.confirm({
            content: "确定删除此条记录吗?",
            onOk: async () => {
                await http.post(`/${api_prefix}/lesscode/index/delete${this.current_search}&${this.generateStrAccordKeyWord(obj)}`, {
                    ...this.formatKeyWord(obj),
                    guid: this.guid
                });
                this.getData();
            }
        });
    };

    handleAction = (obj, record) => {
        const { type, title, multiple, modal_id, url, fields = [], params = [], path, resourceType, upload_data_type } = obj;
        // const str = this.generateStrAccordKeyWord(record);
        if (type == "manMadeModal") {
            if (multiple && this.selectedKeys.length == 0) {
                message.error("请至少选中某一项");
                return;
            }
            const target_modal = finalModalConfig.find(item => item.id == modal_id).modal ?? defaultModalPlaceholder;
            this.setState({
                modalDom: target_modal,
                manMadeModal: true,
                specialFieldParams: { selectedKeys: this.selectedKeys, ...this.generateParamsAccordKeyWordAndParams(obj, record) }
            });
        } else if (type == "need_confirm") {
            if (multiple && this.selectedKeys.length == 0) {
                message.error("请至少选中某一项");
                return;
            }
            Modal.confirm({
                content: `确定${title}此条记录吗?`,
                onOk: async () => {
                    await http.post(`/${api_prefix}${url}`, {
                        [this.keyWord]: this.selectedKeys,
                        guid: this.guid
                    });
                    message.success("操作成功");
                    this.getData();
                }
            });
        } else if (type == "modal") {
            if (multiple && this.selectedKeys.length == 0) {
                message.error("请至少选中某一项");
                return;
            }
            this.extra_operate_modal_flag = upload_data_type || "common";
            this.setState({ extra_operate_modal: true });
            this.extra_operate_url = url;
            // 选中的数据
            let selectedData = [];
            this.extra_operate_current_edit_obj = {};
            if (!Array.isArray(this.keyWord)) {
                /**
                 * 多条数据需要传给后端除pk 字段外 的其他字段
                 */
                this.multipleDataExtraParams = params;
                selectedData = this.state.data.filter(item => this.selectedKeys.includes(item[this.keyWord]));
                this.extra_operate_current_edit_obj = {
                    [this.keyWord]: this.selectedKeys
                };
                params?.map(fieldItem => {
                    this.extra_operate_current_edit_obj[fieldItem] = selectedData.map(dataItem => dataItem[fieldItem]);
                });
            }
            this.currentExtraEditObj = {};
            fields.forEach(item => {
                if (item.default) {
                    this.currentExtraEditObj[item.name] = item.default;
                }
            });
            this.extra_operate_title = title;
            this.extra_operate_options = fields
                .filter(item => item.component)
                .map(item => ({
                    ...item,
                    label: item.comment,
                    type: item.component,
                    required: item.required || false,
                    options: item.enum,
                    content: item.component == "text" ? item.default : "",
                    uploadUrl:
                        item.component == "upload"
                            ? item.upload_url
                                ? item.upload_url.indexOf("?") != -1
                                    ? `/${api_prefix}/${item.upload_url}&${this.generateStrAccordKeyWord(record)}`
                                    : `/${api_prefix}/${item.upload_url}?${this.generateStrAccordKeyWord(record)}`
                                : common_upload_url
                            : undefined
                }));
        } else if (type == "url") {
            if (resourceType == "static") {
                if (process.env.KA_ENV == "prod") {
                    window.location.href = `${cfg.ajaxPrefix}/${api_prefix}${path}`;
                } else {
                    window.location.href = `${cfg.ajaxPrefix}${path}`;
                }
            } else if (resourceType == "outside") {
                window.location.href = path;
            } else {
                this.props.history.push(`${path}`);
            }
        } else if (type == "create") {
            this.showModal();
        } else if (type == "export") {
            this.exportTask();
        }
    };

    gotoRight = async (obj, record) => {
        const {
            guid,
            path,
            type,
            url,
            title,
            fields = [],
            modal_id,
            params,
            form_load_url,
            resourceType,
            upload_data_type,
            callback_type
        } = obj;
        const str = this.generateStrAccordKeyWord(record);
        const paramsStr = this.generateParamsStrAccordKeyWord(obj, record);
        if (type == "guid") {
            this.props.history.push(`/lesscode/common?guid=${guid}&${str}${paramsStr}`);
        } else if (type == "need_confirm") {
            Modal.confirm({
                content: `确定${title}此条记录吗?`,
                onOk: async () => {
                    await http.post(`/${api_prefix}${url}${paramsStr}`, {
                        ...this.formatKeyWord(record),
                        guid: this.guid
                    });
                    if (callback_type) PubSub.publish(callback_type);
                    this.getData();
                }
            });
        } else if (type == "url") {
            if (resourceType == "static") {
                if (process.env.KA_ENV == "prod") {
                    window.open(`${cfg.ajaxPrefix}/${api_prefix}${path}`, "_blank");
                } else {
                    window.open(`${cfg.ajaxPrefix}${path}`, "_blank");
                }
            } else if (resourceType == "outside") {
                window.location.href = path;
            } else {
                this.props.history.push(`${path}?${str}${paramsStr}`);
            }
        } else if (type == "modal") {
            this.extra_operate_modal_flag = upload_data_type || "common";
            if (form_load_url) {
                const { data } = await http.get(`/${api_prefix}${form_load_url}`, this.formatKeyWord(record));
                let newData = data;
                if (Array.isArray(this.keyWord)) {
                    this.keyWord.forEach(item => {
                        newData[item] = record[item];
                    });
                } else {
                    newData[this.keyWord] = record[this.keyWord];
                }
                this.handleOnSomeBeforeModal(obj, newData);
            } else {
                this.handleOnSomeBeforeModal(obj, record);
            }
        } else if (type == "manMadeModal") {
            const target_modal = finalModalConfig.find(item => item.id == modal_id).modal ?? defaultModalPlaceholder;
            this.setState({
                modalDom: target_modal,
                manMadeModal: true,
                specialFieldParams: this.generateParamsAccordKeyWordAndParams(obj, record)
            });
        } else if (type == "modify") {
            this.showModal(record);
        } else if (type == "delete") {
            this.deleteItem(record);
        }
    };

    handleOnSomeBeforeModal = (obj, newRecord) => {
        const { url, title, fields = [], form_load_url, hiddenOpButton } = obj;
        this.extra_operate_url = url;
        this.extra_operate_current_edit_config = obj;
        this.extra_operate_current_edit_obj = newRecord;
        this.extra_operate_title = title;
        const need_fill_field = fields
            .filter(item => item.writeback)
            .map(item => ({
                name: item.name,
                component: item.component,
                dataType: item.dataType
            }));
        const dateKeys = fields.filter(item => item.component == "datepicker").map(item => item.name);
        this.currentExtraEditObj = form_load_url ? newRecord : {};

        if (form_load_url) {
            dateKeys.forEach(item => {
                this.currentExtraEditObj[item] = moment(this.currentExtraEditObj[item]);
            });
        }
        fields.forEach(item => {
            if (item.default) {
                this.currentExtraEditObj[item.name] = item.default;
            }
        });
        need_fill_field &&
            need_fill_field.forEach(item => {
                this.currentExtraEditObj[item.name] =
                    item.component == "datepicker"
                        ? newRecord[item.name] && moment(newRecord[item.name])
                        : item.dataType == "object"
                        ? newRecord[item.name]["value"]
                        : newRecord[item.name];
            });
        this.extra_operate_options = fields
            .filter(item => item.component)
            .map(item => ({
                ...item,
                label: item.comment,
                type: item.component,
                required: item.required || false,
                options: item.enum,
                content: item.writeback
                    ? item.dataType == "object"
                        ? newRecord[item.name]["value"]
                        : newRecord[item.name]
                    : form_load_url
                    ? newRecord[item.name]
                    : "",
                uploadUrl:
                    item.component == "upload"
                        ? item.upload_url
                            ? item.upload_url.indexOf("?") != -1
                                ? `/${api_prefix}/${item.upload_url}&${this.generateStrAccordKeyWord(newRecord)}`
                                : `/${api_prefix}/${item.upload_url}?${this.generateStrAccordKeyWord(newRecord)}`
                            : common_upload_url
                        : undefined
            }));
        this.setState({
            extra_operate_modal: true,
            extra_operate_modal_footer_hidden: !!hiddenOpButton
        });
    };

    handleSpecialListField = async obj => {
        const { url, type, modal_id, params, resourceType, value } = obj;
        if (type == "url") {
            if (resourceType == "static") {
                window.open(url, "_blank");
            } else if (resourceType == "outside") {
                window.location.href = url;
            } else {
                this.props.history.push(url);
            }
        } else if (type == "manMadeModal") {
            const target_modal = finalModalConfig.find(item => item.id == modal_id).modal ?? defaultModalPlaceholder;
            this.setState({
                modalDom: target_modal,
                manMadeModal: true,
                specialFieldParams: params
            });
        } else if (type == "media_url") {
            const imgSuffix = [".png", ".jpeg", ".jpg", ".webp", ".svg", ".bmp", ".gif"];
            const target = imgSuffix.find(item => url.toLowerCase().includes(item));
            if (target) {
                const image = new Image();
                image.src = url;
                const imgWindow = window.open(url);
                imgWindow.document.write(image.outerHTML);
            } else {
                window.open(url, "_blank");
            }
        } else if (type == "copy") {
            await clipboardCopy(value);
            message.success("复制成功");
        }
    };

    handleTableColumns = fields => {
        const newColumns = fields
            .filter(item => !item.hidden)
            .map((item, index) => {
                if (item.enum) {
                    return {
                        ...item,
                        title: item.comment,
                        fixed: index == 0 ? "left" : "",
                        dataIndex: item.name,
                        render: text => {
                            if (Array.isArray(text)) {
                                const filterArr = item.enum.filter(obj => text.includes(obj.value));
                                return filterArr?.map(item => item.label).join(",");
                            } else {
                                return item.enum.find(obj => obj.value == text)?.label;
                            }
                        }
                    };
                } else {
                    return {
                        ...item,
                        fixed: index == 0 ? "left" : "",
                        title: item.comment,
                        dataIndex: item.name
                    };
                }
            })
            .map(item => {
                if (item.sort) {
                    return { ...item, sorter: (a, b) => b[item.name] - a[item.name] };
                } else {
                    return item;
                }
            })
            .map(item => {
                if (item.dataType == "object") {
                    this.special_list_field_collect.push(item.name);
                    return {
                        ...item,
                        render: (_, rec) => (
                            <span onClick={() => this.handleSpecialListField(rec[item.name])}>
                                <If data={rec[item.name]?.icon}>
                                    <img
                                        src={imgConfig.find(sitem => sitem.id == rec[item.name]?.icon)?.value || ""}
                                        style={{
                                            cursor: rec[item.name]?.type ? "pointer" : "",
                                            width: 20,
                                            height: 20
                                        }}
                                        alt=""
                                    />
                                </If>
                                <If data={!rec[item.name]?.icon}>
                                    <span style={{ color: "#0076ff", cursor: "pointer" }}>{rec[item.name]?.title}</span>
                                </If>
                            </span>
                        )
                    };
                } else {
                    return item;
                }
            })
            .map(item => {
                if (item.dataType == "array") {
                    // this.special_list_field_collect.push(item.name);
                    return {
                        ...item,
                        render: (text, rec) => (
                            <Space direction="vertical">
                                {text?.map((item, index) => (
                                    <span
                                        key={index}
                                        style={{ color: "#0076ff", cursor: "pointer" }}
                                        onClick={() => this.handleSpecialListField(item)}
                                    >
                                        {item.title}
                                    </span>
                                ))}
                            </Space>
                        )
                    };
                } else {
                    return item;
                }
            })
            .map(item => {
                if (item.dataType == "img") {
                    return {
                        ...item,
                        render: text => (text ? <AImage width={44} height={44} src={text} /> : null)
                    };
                } else {
                    return item;
                }
            });
        const finalColumns = [
            ...newColumns,
            {
                title: "操作",
                dataIndex: "action",
                fixed: "right",
                render: (_, record) => (
                    <Space>
                        <If data={this.own_point.includes("modify")}>
                            <Iconfont title="编辑" type="icon-xiugai" onClick={() => this.showModal(record)} />
                        </If>
                        <If data={this.own_point.includes("delete")}>
                            <Iconfont title="删除" type="icon-jinrongxianxingge-" onClick={() => this.deleteItem(record)} />
                        </If>
                        {this.extra_operate?.map(item => (
                            <If data={item.show ? eval(item.show) : true} key={item.guid || item.path || item.url || item.modal_id}>
                                <Iconfont
                                    title={item.title}
                                    type={item.icon || "icon-xiangqing2"}
                                    onClick={() => this.gotoRight(item, record)}
                                />
                            </If>
                        ))}
                    </Space>
                )
            }
        ];
        this.setState({
            columns: _.cloneDeep(finalColumns)
        });
        return finalColumns;
    };

    handleList = obj => {
        const {
            action: { system }
        } = this.props;
        const { title, pk, fields = [], multiple } = obj;
        this.momentKey = fields.filter(item => item.component == "datepicker").map(item => item.name);
        this.keyWord = pk;
        this.listMultiple = multiple;
        document.title = title;
        system.setCurrentTabTitle(title);
        const finalColumns = this.handleTableColumns(fields);
        cacheInitColumns(finalColumns.slice(0));
    };

    handleOnColumnsCallBack = data => {
        this.handleTableColumns(data || []);
    };

    handleFilter = (data = [], cache) => {
        const specialComponent = ["multipleselect"];
        const cacheParam = cache ? generateFilterData(JSON.parse(localStorage.getItem(this.guid) || "{}")) : {};
        const newFieldData = data
            ?.map(item => {
                if (Array.isArray(item.name)) {
                    return {
                        ...item,
                        label: item.label,
                        options: item.enum,
                        type: item.component,
                        name: item.name[0],
                        value: cacheParam[item.name[0]] || item.default,
                        valueType: cacheParam[item.name[0]] ? "cache" : "default",
                        name2: item.name[1]
                    };
                } else {
                    return {
                        ...item,
                        label: item.label,
                        options: item.enum,
                        value: specialComponent.includes(item.component)
                            ? cacheParam[item.name] || item.default || []
                            : cacheParam[item.name] || item.default,
                        valueType: cacheParam[item.name] ? "cache" : "default",
                        type: item.component,
                        name: item.name
                    };
                }
            })
            .map(item => {
                if (item.component == "rangeinput") {
                    return {
                        ...item,
                        [`${item.name}_start`]: cacheParam[`${item.name}_start`],
                        [`${item.name}_end`]: cacheParam[`${item.name}_end`]
                    };
                } else {
                    return item;
                }
            });
        this.setState({ hasFilter: data.length > 0, fieldData: newFieldData });
    };

    handleOnPageChange = (page, pageSize) => {
        this.setState({ current: page });
        this.param = { ...this.param, page, limit: pageSize };
        this.getData();
    };

    generateStrAccordKeyWord = obj => {
        const suffix = [];
        if (Array.isArray(this.keyWord)) {
            this.keyWord.forEach((item, index) => {
                suffix.push(`${item}=${obj[item]}`);
            });
        } else {
            suffix.push(`${this.keyWord}=${obj[this.keyWord]}`);
        }
        // 多条数据  拼接  额外的字段 给后端
        if (this.multipleDataExtraParams) {
            this.multipleDataExtraParams.forEach(item => {
                suffix.push(`${item}=${obj[item]}`);
            });
        }
        return suffix.join("&");
    };

    generateParamsAccordKeyWordAndParams = (data, record) => {
        const { params, guid, title, fields } = data;
        const obj = this.formatKeyWord(record);
        obj["guid"] = guid;
        obj["title"] = title;
        if (Array.isArray(params)) {
            params.forEach((item, index) => {
                if (item.length == 1) {
                    obj[item[0]] = record[item[0]];
                } else {
                    obj[item[0]] = item[1];
                }
            });
        }
        if (fields) obj["fields"] = fields;
        return obj;
    };

    generateParamsStrAccordKeyWord = (obj, record) => {
        const suffix = [];
        if (Array.isArray(obj.params)) {
            obj.params.forEach((item, index) => {
                if (item.length == 1) {
                    suffix.push(`${item[0]}=${record[item]}`);
                } else {
                    suffix.push(`${item[0]}=${item[1]}`);
                }
            });
        }

        return suffix.length > 0 ? "&" + suffix.join("&") : "";
    };

    showModal = param => {
        const { form: { create: { url, type, modal_id } = {} } = {} } = this.extra;
        const obj = param ? { ...param } : undefined;
        let suffix = [];
        if (type == "manMadeModal") {
            const target_modal = finalModalConfig.find(item => item.id == modal_id).modal ?? defaultModalPlaceholder;
            this.setState({ modalDom: target_modal, manMadeModal: true });
            return;
        } else if (type == "url") {
            if (obj) {
                const rest_str = this.generateStrAccordKeyWord(obj);
                this.props.history.push(`${url}?${rest_str}`);
                return;
            } else {
                this.props.history.push(`${url}`);
                return;
            }
        }

        const { fields } = this.fields_list;
        this.addOptions = fields
            .filter(item => item.component)
            .map(item => ({
                ...item,
                label: obj
                    ? item.form?.modify?.comment
                        ? item.form?.modify?.comment
                        : item.comment
                    : item.form?.create?.comment
                    ? item.form?.create?.comment
                    : item.comment,
                type: obj
                    ? item.form?.modify?.component
                        ? item.form?.modify?.component
                        : item.component
                    : item.form?.create?.component
                    ? item.form?.create?.component
                    : item.component,
                uploadUrl:
                    item.component == "upload"
                        ? item?.form?.create?.upload_url
                            ? `/${api_prefix}/${item?.form?.create?.upload_url}`
                            : common_upload_url
                        : undefined,
                required: obj
                    ? item.form?.modify?.required
                        ? item.form?.modify?.required
                        : item.required
                        ? item.required
                        : false
                    : item.form?.create?.required
                    ? item.form?.create?.required
                    : item.required
                    ? item.required
                    : false,
                hidden: obj ? item.form?.modify?.hidden : item.form?.create?.hidden,
                disabled: obj ? item.form?.modify?.disabled : item.form?.create?.disabled,
                options: obj
                    ? item.form?.modify?.enum
                        ? item.form?.modify?.enum
                        : item.enum
                    : item.form?.create?.enum
                    ? item.form?.create?.enum
                    : item.enum,
                placeholder: obj
                    ? item.form?.modify?.placeholder
                        ? item.form?.modify?.placeholder
                        : item.placeholder
                    : item.form?.create?.placeholder
                    ? item.form?.create?.placeholder
                    : item.placeholder
            }));
        if (obj) {
            Object.keys(obj).forEach(item => {
                if (this.momentKey.includes(item)) {
                    obj[item] = obj[item] && moment(obj[item]);
                }
            });
        }
        this.currentEditObj = {};
        this.currentEditType = false;
        if (obj) {
            Object.keys(obj).forEach(item => {
                if (this.special_list_field_collect.includes(item)) {
                    this.currentEditObj[item] = obj[item]["value"];
                } else {
                    this.currentEditObj[item] = obj[item];
                }
            });
            this.currentEditType = true;
        } else {
            // 处理默认值
            fields.forEach(item => {
                if (item.form?.create?.default) {
                    this.currentEditObj[item.name] = item.form?.create?.default;
                } else if (item.default) {
                    this.currentEditObj[item.name] = item.default;
                }
            });
        }

        this.setState({ modal: true });
    };

    handleOnModalOk = async data => {
        Object.keys(data).forEach(item => {
            if (moment.isMoment(data[item])) {
                data[item] = data[item].format(getMomentFormatType(item, this.fields_list?.fields));
            }
            // 多选数组等数据如果为空key被删除兼容
            if (Array.isArray(data[item]) && data[item].length == 0) {
                data[item] = "";
            }
        });
        if (this.currentEditType === true)
            await http.post(
                `/${api_prefix}/lesscode/index/modify${this.current_search}&${this.generateStrAccordKeyWord(this.currentEditObj)}`,
                {
                    guid: this.guid,
                    ...this.formatKeyWord(this.currentEditObj),
                    ...data
                }
            );
        else
            await http.post(`/${api_prefix}/lesscode/index/create${this.current_search}`, {
                guid: this.guid,
                ...data
            });
        this.setState({ modal: false });
        this.getData();
    };

    handleOnModalCancel = () => {
        this.setState({ modal: false });
    };

    handleOnManMadeModalCancel = () => {
        this.setState({ manMadeModal: false });
    };

    handleOnManMadeModalSuccess = () => {
        this.setState({ manMadeModal: false });
        this.getData();
    };

    handleOnExtraOperateModalOk = async data => {
        Object.keys(data).forEach(item => {
            if (moment.isMoment(data[item])) {
                data[item] = data[item].format(getMomentFormatType(item, this.fields_list?.fields));
            }
        });
        await http.post(
            `/${api_prefix}${this.extra_operate_url}${this.current_search}&${this.generateStrAccordKeyWord(
                this.extra_operate_current_edit_obj
            )}${
                this.extra_operate_current_edit_config
                    ? this.generateParamsStrAccordKeyWord(this.extra_operate_current_edit_config, this.extra_operate_current_edit_obj)
                    : ""
            }`,
            {
                guid: this.guid,
                ...data
            }
        );
        this.setState({ extra_operate_modal: false });
        this.getData();
    };

    handleOnExtraOperateCsvModalOk = async data => {
        const formData = new FormData();
        Object.keys(data).forEach(item => {
            formData.append(item, data[item]);
        });
        formData.append("guid", this.guid);
        await http.post(
            `/${api_prefix}${this.extra_operate_url}${this.current_search}&${this.generateStrAccordKeyWord(
                this.extra_operate_current_edit_obj
            )}${
                this.extra_operate_current_edit_config
                    ? this.generateParamsStrAccordKeyWord(this.extra_operate_current_edit_config, this.extra_operate_current_edit_obj)
                    : ""
            }`,
            formData,
            {
                headers: {
                    "Content-Type": "multipart/form-data "
                }
            }
        );
        this.setState({ extra_operate_modal: false });
        this.getData();
    };

    handleOnExtraOperateModalCancel = () => {
        this.setState({
            extra_operate_modal: false
        });
    };

    search = params => {
        const data = formatFilterData(params, this.filter_list);
        this.param.page = 1;
        this.param = filterObj({ ...this.param, ...data });
        this.setState({ current: 1 });
        this.getData();
    };

    gotoFormCreate = () => {
        this.props.history.push(`/lesscode/form/main?guid=${this.guid}`);
    };

    handleOnGTableChange = (pagination, filters, sorter, extra) => {
        const { order, field } = sorter;
        const { action } = extra;
        if (action != "sort") return;
        if (order == "ascend") {
            this.param.dir = "asc";
            this.param.sort = field;
        } else if (order == "descend") {
            this.param.dir = "desc";
            this.param.sort = field;
        } else {
            this.param.dir = "";
            this.param.sort = "";
        }
        this.param.page = 1;
        this.setState({ current: 1 });
        this.getData();
    };

    exportTask = async () => {
        const current_value = this.ref_filter.current?.getCurrentValues() || {};
        const realObj = formatFilterData(filterObj(current_value), this.filter_list);
        const host = window.location.host.startsWith("localhost") ? window.location.origin : cfg.ajaxPrefix;
        const url = `${host}/${api_prefix}/lesscode/index/export${this.current_search}`;
        let arr = [`guid=${this.guid}`],
            realUrl;
        Object.keys(realObj).forEach(item => {
            arr.push(`${item}=${realObj[item]}`);
        });
        if (url.indexOf("?") == -1) {
            realUrl = `${url}?${arr.join("&")}`;
        } else {
            realUrl = `${url}&${arr.join("&")}`;
        }
        const newWindow = window.open(realUrl);
    };

    handleRowSelect = selectedKeys => {
        this.selectedKeys = selectedKeys;
    };

    render() {
        const {
            hasFilter,
            fieldData,
            columns,
            total,
            loading,
            data,
            current,
            modal,
            extra_operate_modal,
            manMadeModal,
            modalDom: C,
            patchDom: P,
            specialFieldParams,
            extra_operate_modal_footer_hidden,
            pageSize
        } = this.state;
        return (
            <PageContent>
                <div>
                    <If data={manMadeModal}>
                        <C
                            params={specialFieldParams}
                            visible={manMadeModal}
                            guid={this.guid}
                            onSuccess={this.handleOnManMadeModalSuccess}
                            onCancel={this.handleOnManMadeModalCancel}
                        />
                    </If>
                    <If data={hasFilter}>
                        <CompGaiaFilter ref={this.ref_filter} fileds={fieldData} onSubmit={this.search} />
                        <div style={{ height: 10, backgroundColor: "#F2F3F6" }} />
                    </If>
                    <If data={P}>
                        <div style={{ padding: 12, backgroundColor: "white" }}>{P && <P />}</div>
                        <div style={{ height: 10, backgroundColor: "#F2F3F6" }} />
                    </If>
                    <If data={columns.length > 0}>
                        <CompGaiaTable
                            columns={columns}
                            selectColumns={this.selectColumns}
                            columnsCallBack={this.handleOnColumnsCallBack}
                            loading={loading}
                            dataSource={data}
                            pk={this.keyWord}
                            onChange={this.handleOnGTableChange}
                            rowSelection={this.listMultiple ? this.handleRowSelect : null}
                            pagination={{
                                total,
                                defaultPageSize: pageSize,
                                current,
                                onChange: this.handleOnPageChange
                            }}
                        >
                            <If data={this.own_point.includes("create")}>
                                <Button type="primary" onClick={() => this.showModal()}>
                                    增加
                                </Button>
                            </If>
                            {this.action
                                ?.sort((a, b) => a["weight"] - b["weight"])
                                .map((item, index) => (
                                    <Button type="primary" key={index} onClick={() => this.handleAction(item)}>
                                        {item.title}
                                    </Button>
                                ))}
                            <If data={this.own_point.includes("add_field")}>
                                <Button type="primary" onClick={this.gotoFormCreate}>
                                    增加字段
                                </Button>
                            </If>
                        </CompGaiaTable>
                    </If>
                    <ModalForm
                        title={this.currentEditType === true ? "编辑" : "创建"}
                        visible={modal}
                        initialValues={this.currentEditObj}
                        options={this.addOptions}
                        onOk={this.handleOnModalOk}
                        onCancel={this.handleOnModalCancel}
                    />
                    <ModalForm
                        footer={extra_operate_modal_footer_hidden ? { footer: null } : undefined}
                        flag={this.extra_operate_modal_flag}
                        title={this.extra_operate_title}
                        visible={extra_operate_modal}
                        initialValues={this.currentExtraEditObj}
                        options={this.extra_operate_options || []}
                        onOk={this.handleOnExtraOperateModalOk}
                        onCsvOk={this.handleOnExtraOperateCsvModalOk}
                        onCancel={this.handleOnExtraOperateModalCancel}
                    />
                </div>
            </PageContent>
        );
    }
}

const CommonPage = props => {
    return <Common {...props} key={window.location.href} />;
};

export default CommonPage;
