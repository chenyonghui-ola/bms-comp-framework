import React, { useState, useEffect, useRef } from "react";
import {
    Modal,
    Input,
    Select,
    AutoComplete,
    Form,
    Upload,
    Button,
    Checkbox,
    DatePicker,
    Radio,
    TreeSelect,
    Tree,
    Table,
    Image as AImage,
    Cascader,
    TimePicker,
    message,
    Switch
} from "antd";
import { If } from "src/components";
import { PlusOutlined } from "@ant-design/icons";
import CompGaiaTable from "src/components/gaia-table";
import { ajax } from "src/library/ajax/index";
import DynamicSelect from "./DynamicSelect";
import "./ModalForm.less";
import cfg from "src/config";
import moment from "moment";
import PubSub from "pubsub-js";

const { TextArea } = Input;
const CheckboxGroup = Checkbox.Group;
const RadioGroup = Radio.Group;
const { Option, OptGroup } = Select;

const layout = {
    labelCol: { span: 6 },
    wrapperCol: { span: 18 }
};

/**
 * title: 标题
 * visible: 显示与关闭
 * onCancel: 取消按钮回调
 * onOk: 确定按钮回调
 * initialValues: 初始值，常用于编辑按钮
 * options: 字段数据
 *       {
 *          label: 描述内容，
 *          type:类型，
 *          disabled: 是否禁止
 *          required:是否必填，默认必填，
 *          pattern：校验的正则,
 *          placeholder:placeholder
 *          uploadUrl: 上传的地址,
 *          options: select,autoComplete等下拉数据源,
 *          content:描述性文字
 *       }
 */
export default ({
    title,
    visible,
    onCancel,
    flag,
    onOk, // 这个是上传图片，视频，音频等的回调a
    onCsvOk, // 这个是类似批量导入用的确定回调
    initialValues,
    onFieldsChange,
    includeTable = false,
    tableColumns,
    tableData,
    changeValue,
    treeKeys,
    options = [],
    width = 640,
    initfileList
}) => {
    const [form] = Form.useForm();
    const treeRef = useRef(undefined);
    const mediaRef = useRef(undefined);
    const [treeCheckedKeys, setTreeCheckedKeys] = useState([]);
    const [fileList, setFileList] = useState([]);

    const [previewData, setPreViewData] = useState(undefined);
    const [confirmLoading, setConfirmLoading] = useState(false);
    useEffect(() => {
        PubSub.subscribe("network-end", () => {
            setConfirmLoading(false);
        });
    }, []);

    useEffect(() => {
        if (initfileList) setFileList(initfileList);
    }, [initfileList]);

    useEffect(() => {
        if (changeValue) form.setFieldsValue(changeValue);
    }, [changeValue]);

    useEffect(() => {
        if (visible) {
            form.setFieldsValue(initialValues);
            if (treeKeys && initialValues && initialValues[treeKeys]) {
                setTreeCheckedKeys(initialValues[treeKeys]);
            }
        }
    }, [initialValues, visible]);

    const handleOk = async () => {
        const result = await form.validateFields();
        let final_result = { ...result };
        if (previewData) {
            final_result = { ...final_result, csvData: previewData };
        }
        if (treeRef.current) {
            final_result = { ...final_result, ...treeRef.current };
        }

        Object.keys(final_result).forEach(item => {
            if (final_result[item]?.hasOwnProperty("fileList")) {
                final_result[item] = fileList.map(fitem => {
                    if (fitem.url) {
                        final_result[`${item}_url`] = fitem.url;
                        return fitem.name;
                    } else {
                        final_result[`${item}_url`] = fitem?.response?.data?.url;
                        return fitem?.response?.data?.name;
                    }
                });
            }
        });
        if (flag == "csv") {
            onCsvOk({ ...final_result, ...mediaRef.current });
        } else {
            onOk(final_result);
        }
        setConfirmLoading(true);
    };

    const handleBeforeUpload = async (file, name, url) => {
        const formData = new FormData();
        formData.append("file", file);
        try {
            const { data } = await ajax.post(`${cfg.ajaxPrefix}${url}`, formData, {
                headers: {
                    "Content-Type": "multipart/form-data "
                }
            });

            if (data.length > 0) {
                setPreViewData(data.map((item, index) => ({ ...item, id: index })));
            }
            form.setFieldsValue({
                [name]: data.name
            });
            return false;
        } catch (error) {
            const {
                data: { msg = "系统错误" }
            } = error;
            message.error(`${msg}`);
        }
    };

    const handleBeforeUploadCSV = (file, name) => {
        const { name: mediaName } = file;
        form.setFieldsValue({
            [name]: mediaName
        });
        mediaRef.current = { [name]: file };
    };

    const onTreeCheck = (keys, info, name) => {
        const { checked, node, halfCheckedKeys } = info;
        setTreeCheckedKeys(keys);
        treeRef.current = { [name]: [...keys, ...halfCheckedKeys] };
    };

    const disabledDate = (maxDate, minDate) => {
        return current => {
            if (maxDate && current > moment(maxDate).endOf("day")) {
                return true;
            }

            return minDate && current < moment(minDate).startOf("hour");
        };
    };

    const handleOnMultipleChange = ({ fileList }) => {
        setFileList(fileList);
    };

    const handleOnMultiplePreview = async file => {
        let src = file.url;
        if (!src) {
            src = await new Promise(resolve => {
                const reader = new FileReader();
                reader.readAsDataURL(file.originFileObj);
                reader.onload = () => resolve(reader.result);
            });
        }
        const image = new Image();
        image.src = src;
        const imgWindow = window.open(src);
        imgWindow.document.write(image.outerHTML);
    };

    const afterClose = () => {
        setPreViewData(undefined);
        setFileList([]);
        form.resetFields();
        form.setFieldsValue({});
        setConfirmLoading(false);
        setTreeCheckedKeys([]);
        treeRef.current = undefined;
    };

    const uploadButton = (
        <div>
            <PlusOutlined />
            <div style={{ marginTop: 8 }}>Upload</div>
        </div>
    );

    const handleUploadClear = name => {
        form.setFieldsValue({ [name]: undefined });
    };

    return (
        <Modal
            title={title || "新增"}
            visible={visible}
            width={width}
            onOk={handleOk}
            onCancel={onCancel}
            afterClose={afterClose}
            destroyOnClose
            className="gaia-modal-form"
            confirmLoading={confirmLoading}
        >
            <If data={includeTable}>
                <CompGaiaTable columns={tableColumns} dataSource={tableData} />
            </If>
            <Form {...layout} form={form} preserve={false} onValuesChange={onFieldsChange}>
                {options.map((item, index) => {
                    const { required = true } = item;
                    if (item.type === "switch") {
                        return (
                            <Form.Item key={index} label={item.label} name={item.name} rules={[{ required: required, message: "请选择" }]}>
                                <Switch />
                            </Form.Item>
                        );
                    }
                    if (item.type === "input") {
                        let rules = [{ required: required, message: "请输入" }];
                        if (item.pattern) {
                            rules = [...rules, { pattern: item.pattern, message: "格式不正确" }];
                        }
                        return (
                            <Form.Item key={index} label={item.label} name={item.name} hidden={item.hidden} rules={rules}>
                                <Input disabled={item.disabled} maxLength={item.maxLength} placeholder={item.placeholder} />
                            </Form.Item>
                        );
                    }

                    if (item.type === "rangeinput") {
                        let rules = [{ required: required, message: "请输入" }];
                        if (item.pattern) {
                            rules = [...rules, { pattern: item.pattern, message: "格式不正确" }];
                        }
                        return (
                            <Form.Item key={index} label={item.label} hidden={item.hidden} required={required}>
                                <div style={{ display: "flex" }}>
                                    <div style={{ flex: 1 }}>
                                        <Form.Item name={`${item.name}_start`} rules={rules}>
                                            <Input />
                                        </Form.Item>
                                    </div>

                                    <span style={{ margin: "4px 10px 10px 10px" }}>-</span>
                                    <div style={{ flex: 1 }}>
                                        <Form.Item name={`${item.name}_end`} rules={rules}>
                                            <Input />
                                        </Form.Item>
                                    </div>
                                </div>
                            </Form.Item>
                        );
                    }

                    if (item.type === "tree") {
                        return (
                            <Form.Item
                                key={index}
                                label={item.label}
                                hidden={item.hidden}
                                rules={[{ required: required }]}
                                name={item.name}
                            >
                                <Tree
                                    {...item.options}
                                    checkedKeys={treeCheckedKeys}
                                    onCheck={(keys, info) => onTreeCheck(keys, info, item.name)}
                                />
                            </Form.Item>
                        );
                    }
                    if (item.type === "textarea") {
                        return (
                            <Form.Item
                                key={index}
                                label={item.label}
                                name={item.name}
                                hidden={item.hidden}
                                rules={[{ required: required, message: "请输入" }]}
                            >
                                <TextArea
                                    maxLength={item.maxLength}
                                    placeholder={item.placeholder}
                                    autoSize={{ minRows: 4 }}
                                    disabled={item.disabled}
                                />
                            </Form.Item>
                        );
                    }
                    if (item.type === "select") {
                        return (
                            <Form.Item
                                key={index}
                                label={item.label}
                                hidden={item.hidden}
                                name={item.name}
                                rules={[{ required: required, message: "请选择" }]}
                            >
                                <Select
                                    showSearch
                                    allowClear={true}
                                    disabled={item.disabled}
                                    placeholder="请选择"
                                    mode={item.mode || ""}
                                    options={item.options || []}
                                    optionFilterProp="children"
                                    filterOption={(inputValue, option) =>
                                        option.label.toLowerCase().indexOf(inputValue.toLowerCase()) !== -1
                                    }
                                />
                            </Form.Item>
                        );
                    }
                    if (item.type === "dynamicselect") {
                        return (
                            <Form.Item
                                key={index}
                                label={item.label}
                                hidden={item.hidden}
                                name={item.name}
                                rules={[{ required: required, message: "请选择" }]}
                            >
                                <DynamicSelect showSearch allowClear={true} url={item.url} disabled={item.disabled} placeholder="请选择" />
                            </Form.Item>
                        );
                    }
                    if (item.type === "multiplegroupselect") {
                        return (
                            <Form.Item
                                key={index}
                                label={item.label}
                                hidden={item.hidden}
                                name={item.name}
                                rules={[{ required: required, message: "请选择" }]}
                            >
                                <Select
                                    showSearch
                                    allowClear={true}
                                    disabled={item.disabled}
                                    placeholder="请选择"
                                    mode="multiple"
                                    // options={item.options || []}
                                    optionFilterProp="children"
                                    filterOption={(inputValue, option) =>
                                        option.label?.toLowerCase().indexOf(inputValue.toLowerCase()) !== -1
                                    }
                                >
                                    {item.options?.map((ditem, dindex) => (
                                        <OptGroup key={dindex} label={ditem.label}>
                                            {ditem.children?.map((citem, cindex) => (
                                                <Option value={citem.value} key={citem.value}>
                                                    {citem.label}
                                                </Option>
                                            ))}
                                        </OptGroup>
                                    ))}
                                </Select>
                            </Form.Item>
                        );
                    }
                    if (item.type === "multipleselect") {
                        return (
                            <Form.Item
                                key={index}
                                label={item.label}
                                hidden={item.hidden}
                                name={item.name}
                                rules={[{ required: required, message: "请选择" }]}
                            >
                                <Select
                                    showSearch
                                    allowClear={true}
                                    disabled={item.disabled}
                                    placeholder="请选择"
                                    mode="multiple"
                                    options={item.options || []}
                                    optionFilterProp="children"
                                    filterOption={(inputValue, option) =>
                                        option.label.toLowerCase().indexOf(inputValue.toLowerCase()) !== -1
                                    }
                                />
                            </Form.Item>
                        );
                    }
                    if (item.type === "autocomplete") {
                        return (
                            <Form.Item
                                key={index}
                                label={item.label}
                                hidden={item.hidden}
                                name={item.name}
                                rules={[{ required: required, message: "请选择" }]}
                            >
                                <AutoComplete
                                    placeholder="请选择"
                                    disabled={item.disabled}
                                    options={item.options || []}
                                    filterOption={(inputValue, option) =>
                                        option.value.toLowerCase().indexOf(inputValue.toLowerCase()) !== -1
                                    }
                                />
                            </Form.Item>
                        );
                    }

                    if (item.type === "upload") {
                        return (
                            <Form.Item label={item.label} required={required} key={index} hidden={item.hidden}>
                                <div
                                    style={{
                                        display: "flex",
                                        flexWrap: "nowrap",
                                        width: "100%"
                                    }}
                                >
                                    <div style={{ marginRight: 16, flex: 1 }}>
                                        <Form.Item
                                            name={item.name}
                                            rules={[
                                                {
                                                    required: required,
                                                    message: "请上传"
                                                }
                                            ]}
                                        >
                                            <Input disabled />
                                        </Form.Item>
                                    </div>
                                    <Upload fileList={[]} customRequest={({ file }) => handleBeforeUpload(file, item.name, item.uploadUrl)}>
                                        <Button type="primary">上传</Button>
                                    </Upload>
                                    <If data={item.includeClear}>
                                        <Button style={{ marginLeft: 16 }} onClick={() => handleUploadClear(item.name)}>
                                            清除
                                        </Button>
                                    </If>
                                    <If data={item.template_url}>
                                        <span
                                            style={{ marginLeft: 16, color: "#0076ff", cursor: "pointer" }}
                                            onClick={() => (window.location.href = `${cfg.ajaxPrefix}/${item.template_url}`)}
                                        >
                                            下载模板
                                        </span>
                                    </If>
                                </div>
                                <If data={previewData && previewData.length > 0}>
                                    <div style={{ width: "100%" }}>
                                        <Table
                                            rowKey="id"
                                            dataSource={previewData}
                                            columns={item.preColumns}
                                            scroll={{ x: "max-content" }}
                                            pagination={{
                                                defaultPageSize: 50,
                                                showSizeChanger: false
                                            }}
                                        />
                                    </div>
                                </If>
                            </Form.Item>
                        );
                    }

                    if (item.type === "uploadfile") {
                        return (
                            <Form.Item label={item.label} required={required} key={index} hidden={item.hidden}>
                                <div
                                    style={{
                                        display: "flex",
                                        flexWrap: "nowrap",
                                        width: "100%"
                                    }}
                                >
                                    <div style={{ marginRight: 16, flex: 1 }}>
                                        <Form.Item
                                            name={item.name}
                                            rules={[
                                                {
                                                    required: required,
                                                    message: "请上传"
                                                }
                                            ]}
                                        >
                                            <Input disabled />
                                        </Form.Item>
                                    </div>
                                    <Upload fileList={[]} customRequest={({ file }) => handleBeforeUploadCSV(file, item.name)}>
                                        <Button type="primary"> 上传</Button>
                                    </Upload>
                                    <If data={item.includeClear}>
                                        <Button style={{ marginLeft: 16 }} onClick={() => handleUploadClear(item.name)}>
                                            清除
                                        </Button>
                                    </If>
                                    <If data={item.template_url}>
                                        <span
                                            style={{ marginLeft: 16, color: "#0076ff", cursor: "pointer" }}
                                            onClick={() => (window.location.href = `${cfg.ajaxPrefix}/${item.template_url}`)}
                                        >
                                            下载模板
                                        </span>
                                    </If>
                                </div>
                            </Form.Item>
                        );
                    }

                    if (item.type == "multipleUpload") {
                        return (
                            <Form.Item
                                key={index}
                                label={item.label}
                                hidden={item.hidden}
                                name={item.name}
                                rules={[{ required: required, message: "请选择" }]}
                            >
                                <Upload
                                    action={`${cfg.ajaxPrefix}${item.uploadUrl}`}
                                    listType="picture-card"
                                    fileList={fileList}
                                    onChange={handleOnMultipleChange}
                                    onPreview={handleOnMultiplePreview}
                                >
                                    {fileList.length >= (item.length ?? 4) || item.disabled ? null : uploadButton}
                                </Upload>
                            </Form.Item>
                        );
                    }

                    if (item.type === "checkbox") {
                        return (
                            <Form.Item
                                key={index}
                                label={item.label}
                                hidden={item.hidden}
                                name={item.name}
                                rules={[{ required: required, message: "请选择" }]}
                            >
                                <CheckboxGroup disabled={item.disabled} options={item.options || []} />
                            </Form.Item>
                        );
                    }

                    if (item.type === "radio") {
                        return (
                            <Form.Item
                                key={index}
                                label={item.label}
                                hidden={item.hidden}
                                name={item.name}
                                rules={[{ required: required, message: "请选择" }]}
                            >
                                <RadioGroup disabled={item.disabled} options={item.options || []} />
                            </Form.Item>
                        );
                    }

                    if (item.type === "datepicker") {
                        return (
                            <Form.Item
                                key={index}
                                label={item.label}
                                name={item.name}
                                hidden={item.hidden}
                                rules={[{ required: required, message: "请选择" }]}
                            >
                                <DatePicker
                                    picker={item.flag == "year_time" ? "date" : item.flag || "date"}
                                    showTime={item.flag == "year_time"}
                                    disabled={item.disabled}
                                    disabledDate={disabledDate(item.maxDate, item.minDate)}
                                />
                            </Form.Item>
                        );
                    }

                    if (item.type === "text") {
                        return (
                            <Form.Item key={index} label={item.label} hidden={item.hidden} name={item.name} rules={[{ required: false }]}>
                                <span>{item.content}</span>
                            </Form.Item>
                        );
                    }

                    if (item.type === "img") {
                        return (
                            <Form.Item key={index} label={item.label} hidden={item.hidden} name={item.name} rules={[{ required: false }]}>
                                <AImage src={item.content} width={120} height={120} />
                            </Form.Item>
                        );
                    }

                    if (item.type === "cascader") {
                        return (
                            <Form.Item
                                key={index}
                                hidden={item.hidden}
                                label={item.label}
                                name={item.name}
                                rules={[{ required: required, message: "请选择" }]}
                            >
                                <Cascader disabled={item.disabled} fieldNames={item.fieldNames} options={item.options} />
                            </Form.Item>
                        );
                    }
                    if (item.type === "timepicker") {
                        return (
                            <Form.Item
                                key={index}
                                hidden={item.hidden}
                                label={item.label}
                                name={item.name}
                                rules={[{ required, message: "请选择" }]}
                            >
                                <TimePicker disabled={item.disabled} />
                            </Form.Item>
                        );
                    }

                    if (item.type == "onlyshow") {
                        return (
                            <Form.Item
                                key={index}
                                hidden={item.hidden}
                                label={item.label}
                                name={item.name || "only_show"}
                                rules={[{ required: false, message: "请选择" }]}
                            >
                                {item.content}
                            </Form.Item>
                        );
                    }
                    return null;
                })}
            </Form>
        </Modal>
    );
};
